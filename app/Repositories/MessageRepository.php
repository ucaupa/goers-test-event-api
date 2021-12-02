<?php

namespace App\Repositories;

use App\Models\Message;
use App\Repositories\Contracts\IMessageRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MessageRepository extends GenericRepository implements IMessageRepository
{
    public function __construct()
    {
        parent::__construct(app(Message::class));
    }

    public function getAllMessage($order, $sort, $filter = null, $status = null)
    {
        $orderBy = $order ? $order : $this->model->getKeyName();
        $sortBy = $sort ? $sort : $this->model->getSortDirection();

        $data = $this->model->query()->where('username_tujuan', Auth::user()->username);

        if ($status) {
            $data->where('dibaca', $status == 'read' ? 'Y' : 'N');
        }

        if (is_array($filter)) {
            $data->where(function ($q) use ($filter) {
                foreach ($filter as $key => $value) {
                    $q->orWhere($key, 'like', '%' . $value . '%');
                }
            });
        }

        return $data
            ->orderBy($orderBy, $sortBy)->get();
    }

    public function read($key)
    {
        $data = $this->model->query()->where('tag_pesan', $key)->firstOrFail();

        $data->update(['dibaca' => 'Y', 'tanggal_baca' => Carbon::now()]);

        return null;
    }
}
