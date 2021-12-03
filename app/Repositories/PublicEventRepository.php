<?php

namespace App\Repositories;

use App\Models\BaseModel;
use App\Models\Event;
use App\Models\EventImage;
use App\Repositories\Contracts\IPublicEventRepository;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PublicEventRepository extends GenericRepository implements IPublicEventRepository
{
    /**
     * @var  BaseModel
     */
    protected $modelImage;

    public function __construct(EventImage $modelImage)
    {
        parent::__construct(app(Event::class));
        $this->modelImage = $modelImage;
    }

    public function get($page, $limit, $order = null, $sort = null, $filter = null)
    {
        $orderBy = $order ? $order : $this->model->getKeyName();
        $sortBy = $sort ? $sort : $this->model->getSortDirection();

        $data = $this->model->query()->whereHas('tickets')->where('is_draft', false);

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

    public function getAll($order = null, $sort = null, $filter = null)
    {
        $orderBy = $order ? $order : $this->model->getKeyName();
        $sortBy = $sort ? $sort : $this->model->getSortDirection();

        $data = $this->model->query()->whereHas('tickets')->where('is_draft', false);

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

        return $data
            ->orderBy($orderBy, $sortBy)->get();
    }

    public function find($id)
    {
        $data = $this->model->query()->where('is_draft', false);

        return $data->findOrFail($id);
    }

    public function findBySlug($slugId)
    {
        $slug = explode('--', $slugId);

        if (count($slug) < 2)
            throw new HttpException(Response::HTTP_NOT_FOUND, 'Not Found');

        $name_slug = $slug[0];
        $id = (int)$slug[1];

        $data = $this->model->query()
            ->where('is_draft', false)
            ->findOrFail($id);

        if (Str::slug($data->name) != $name_slug)
            throw new HttpException(Response::HTTP_NOT_FOUND, 'Not Found');

        return $data;
    }
}
