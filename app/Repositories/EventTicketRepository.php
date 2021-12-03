<?php /** @noinspection ALL */

namespace App\Repositories;

use App\Models\BaseModel;
use App\Models\EventTicket;
use App\Models\EventTicketSession;
use App\Repositories\Contracts\IEventTicketRepository;
use Illuminate\Support\Facades\DB;

class EventTicketRepository implements IEventTicketRepository
{
    /**
     * @var  BaseModel
     */
    protected $model;

    public function __construct(EventTicket $model)
    {
        $this->model = $model;
    }

    public function create($eventId, $model)
    {
        return DB::transaction(function () use ($eventId, $model) {
            $sessions = [];
            if (isset($model['session']))
                $sessions = $model['session'];

            unset($model['session']);

            $model['event_id'] = $eventId;
            $data = $this->model->query()->create($model);

            foreach ($sessions as $session) {
                EventTicketSession::query()->create([
                    'event_ticket_id' => $data->id,
                    'event_schedule_id' => $session,
                ]);
            }

            return $data->onCreated();
        });
    }

    public function get($eventId, $page, $limit, $order = null, $sort = null, $filter = null)
    {
        $orderBy = $order ? $order : $this->model->getKeyName();
        $sortBy = $sort ? $sort : $this->model->getSortDirection();

        $data = $this->model->query()->where('event_id', $eventId);

        if (is_array($filter)) {
            $data->where(function ($q) use ($filter) {
                foreach ($filter as $key => $value) {
                    if (!is_string($value) || (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $value) !== 1)) {
                        $q->whereRaw('LOWER(' . $key . ') LIKE ? ', ['%' . trim(strtolower($value)) . '%']);
                    } else {
                        if ($key === 'id') {
                            $q->where($key, '!=', $value);
                        } else {
                            $q->where($key, $value);
                        }
                    }
                }
            });
        }

        return $data
            ->orderBy($orderBy, $sortBy)
            ->offset(($page - 1) * $page)
            ->limit($limit)
            ->paginate($limit);
    }

    public function getAll($eventId, $order = null, $sort = null, $filter = null)
    {
        $orderBy = $order ? $order : $this->model->getKeyName();
        $sortBy = $sort ? $sort : $this->model->getSortDirection();

        $data = $this->model->query()->where('event_id', $eventId);

        if (is_array($filter)) {
            $data->where(function ($q) use ($filter) {
                foreach ($filter as $key => $value) {
                    if (!is_string($value) || (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $value) !== 1)) {
                        $q->whereRaw('LOWER(' . $key . ') LIKE ? ', ['%' . trim(strtolower($value)) . '%']);
                    } else {
                        $q->where($key, $value);
                    }
                }
            });
        }

        return $data->orderBy($orderBy, $sortBy)->get();
    }

    public function find($eventId, $id)
    {
        $data = $this->model->query()->where('event_id', $eventId);

        return $data->findOrFail($id);
    }

    public function update($eventId, $id, $model)
    {
        return DB::transaction(function () use ($eventId, $id, $model) {
            $sessions = [];
            if (isset($model['session'])) {
                $sessions = $model['session'];
                unset($model['session']);

                EventTicketSession::query()->where('event_ticket_id', id)->delete();
            }

            $data = $this->model->query()->where('event_id', $eventId);

            $data->findOrFail($id);

            $data->update($model);
            $data = $this->model->query()->create($model);
            foreach ($sessions as $session) {
                EventTicketSession::query()->create([
                    'event_ticket_id' => $data->id,
                    'event_schedule_id' => $session,
                ]);
            }

            return null;
        });
    }

    public function delete($eventId, $id)
    {
        $data = $this->model->query()->where('event_id', $eventId);

        $data->findOrFail($id);

        $data->delete();

        return null;
    }
}
