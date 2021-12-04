<?php

namespace App\Repositories;

use App\Models\BaseModel;
use App\Models\EventTicket;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderHistory;
use App\Models\PaymentMethod;
use App\Repositories\Contracts\ITransactionRepository;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\CoreApi;
use Midtrans\Transaction;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TransactionRepository implements ITransactionRepository
{
    /**
     * @var  BaseModel
     */
    protected $model;

    /**
     * @var  BaseModel
     */
    protected $modelDetail;

    public function __construct(Order $model, OrderDetail $modelDetail)
    {
        // Set your Merchant Server Key
        Config::$serverKey = config('midtrans.server_key');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        Config::$isProduction = false;

        $this->model = $model;
        $this->modelDetail = $modelDetail;
    }

    public function get($transactionId)
    {
        return $this->model->query()->where('order_id', $transactionId)->firstOrFail();
    }

    public function checkout($model)
    {
        return DB::transaction(function () use ($model) {
            $eventId = $model['event_id'];
            $tickets = $model['tickets'];

            unset($model['event_id']);
            unset($model['tickets']);

            $data = $this->model->query()->create($model);

            foreach ($tickets as $item) {
                $ticket = EventTicket::query()->find($item['ticketId']);
                /* TODO: failure when quota minus */
                $ticket->update([
                    'quota' => $ticket->quota - $item['qty']
                ]);

                $this->modelDetail->query()->create([
                    'order_id' => $data->id,
                    'event_ticket_id' => $ticket->id,
                    'qty' => $item['qty'],
                    'price' => $ticket->price,
                ]);
            }

            return $model['order_id'];
        });
    }

    public function pay($model)
    {
        return DB::transaction(function () use ($model) {
            $orderId = $model['order_id'];
            $paymentMethod = $model['payment_method'];

            $data = $this->model->query()
                ->with('detail.ticket')
                ->whereIn('status', ['WAITING', 'PENDING'])
                ->where('order_id', $orderId)
                ->firstOrFail();

            $created_at_1 = Carbon::parse($data->created_at)->addHours(3);
            $now = Carbon::now();

            if ($created_at_1 < $now && in_array($data->status, ['WAITING', 'PENDING'])) {
                if ($data->transaction_id)
                    Transaction::cancel($orderId);

                $data->status = 'CANCEL';
                $data->save();

                foreach ($data->detail as $item) {
                    $ticket = EventTicket::query()->find($item->event_ticket_id);
                    $ticket->quota = $ticket->quota + $item->qty;
                    $ticket->save();
                }

                return 'Transaction canceled';
            }

            if ($data->transaction_id)
                return 'Complete the previous payment';

            $order_amount = 0;
            $details = [];
            foreach ($data->detail as $item) {
                $order_amount += ($item->qty * $item->price);
                $details[] = [
                    "id" => $item->id,
                    "price" => $item->price,
                    "quantity" => $item->qty,
                    "name" => $item->ticket->name
                ];
            }

            $params = [
                'order_id' => $orderId,
                'order_amount' => $order_amount,
                'item_details' => $details,
                'email' => $data->email,
                'full_name' => $data->first_name . ' ' . $data->last_name,
                'first_name' => $data->first_name,
                'last_name' => $data->last_name,
                'phone' => $data->phone_number,
            ];

            $request = $this->buildRequest($paymentMethod, $params);

            $result = CoreApi::charge($request);

            if (!$result) {
                Log::error($result);
                throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'Internal server error');
            }

            $data->payment_method_id = $paymentMethod;
            $data->transaction_id = $result->transaction_id;
            $data->status = 'PENDING';
            $data->data = json_encode($result);
            $data->save();

            return 'Success';
        });
    }

    public function buildRequest($paymentMethod, $params)
    {
        $method = PaymentMethod::query()->find($paymentMethod);

        if (empty($method))
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Payment method not supported');

        if (!file_exists(database_path($method->payload)))
            throw new \Exception("Unable to load json file");

        $payload = file_get_contents(database_path($method->payload));

        foreach ($params as $key => $param) {
            if (gettype($param) == 'array') {
                $param = json_encode($param);
                $payload = Str::replace('"${{' . $key . '}}"', $param, $payload);
            } elseif ($key == 'order_amount')
                $payload = Str::replace('"${{' . $key . '}}"', $param, $payload);
            else
                $payload = Str::replace('${{' . $key . '}}', $param, $payload);
        }

        return json_decode($payload, true);
    }

    public function callback($model)
    {
        $orderId = $model['order_id'];
        $transactionId = $model['transaction_id'];
        $statusCode = (int)$model['status_code'];

        $order = $this->model->query()
            ->with('detail')
            ->where('order_id', $orderId)
            ->where('transaction_id', $transactionId)
            ->first();

        if (empty($order))
            throw new HttpException(Response::HTTP_NOT_FOUND, 'Order not found');

        switch ($statusCode) {
            case 200;
                $order->status = 'SUCCESS';
                /* TODO: send ticket to user */
                break;
            case 201;
                $order->status = 'PENDING';
                /* TODO: send info transaction to user */
                break;
            case 202;
                if ($order->status != 'CANCEL')
                    foreach ($order->detail as $item) {
                        $ticket = EventTicket::query()->find($item->event_ticket_id);
                        $ticket->quota = $ticket->quota + $item->qty;
                        $ticket->save();
                    }

                $order->status = 'CANCEL';
                break;
        }

        $history = new OrderHistory();
        $history->order_id = $orderId;
        $history->transaction_id = $transactionId;
        $history->payment_method_id = $order->payment_method_id;
        $history->status = $order->status;
        $history->data = $order->data;
        $history->save();

        $order->data = json_encode($model);
        $order->save();

        return [
            'message' => 'Success',
            'code' => Response::HTTP_OK,
            'data' => $model
        ];
    }
}
