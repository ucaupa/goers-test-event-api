<?php /** @noinspection PhpUndefinedFieldInspection */

namespace App\Repositories;

use App\Models\BaseModel;
use App\Repositories\Contracts\IGenericRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

abstract class GenericRepository implements IGenericRepository
{
    /**
     * @var  BaseModel
     */
    protected $model;

    protected $callback = false;

    public function __construct($model, $callback = false)
    {
        $this->model = $model;
        $this->callback = $callback;
    }

    public function get($page, $limit, $order = null, $sort = null, $filter = null)
    {
        $orderBy = $order ? $order : $this->model->getKeyName();
        $sortBy = $sort ? $sort : $this->model->getSortDirection();
        $table = $this->model->getTable();

        $data = $this->model->query();

        if (Schema::hasColumn($table, 'organization_id'))
            $data->where('organization_id', Auth::user()->organization_id);

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
        $table = $this->model->getTable();

        $data = $this->model->query();

        if (Schema::hasColumn($table, 'organization_id'))
            $data->where('organization_id', Auth::user()->organization_id);

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
        $data = $this->model->query();
        $table = $this->model->getTable();

        if (Schema::hasColumn($table, 'organization_id'))
            $data->where('organization_id', Auth::user()->organization_id);

        return $data->findOrFail($id);
    }

    public function create($model)
    {
        $data = $this->model->query()->create($model);

        return $data->onCreated();
    }

    public function update($id, $model)
    {
        $data = $this->model->query();
        $table = $this->model->getTable();

        if (Schema::hasColumn($table, 'organization_id'))
            $data->where('organization_id', Auth::user()->organization_id);

        $data->findOrFail($id);

        $data->update($model);

        return null;
    }

    public function delete($id)
    {
        $data = $this->model->query();
        $table = $this->model->getTable();

        if (Schema::hasColumn($table, 'organization_id'))
            $data->where('organization_id', Auth::user()->organization_id);

        $data->findOrFail($id);

        $data->delete();

        return null;
    }
}
