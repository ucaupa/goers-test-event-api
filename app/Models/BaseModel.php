<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaseModel extends Model
{
    use SoftDeletes, EventUpdater;

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * Default sort direction
     * @var string
     */
    protected $defaultSort = 'desc';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'deleted_by',
        'deleted_at'
    ];

    public function getSortDirection()
    {
        return $this->defaultSort;
    }

    public function getCreatedAtAttribute($attr)
    {
        return $this->formatDate($attr);
    }

    private function formatDate($attr)
    {
        return $attr ? Carbon::parse($attr)->format('c') : null;
    }

    public function getUpdatedAtAttribute($attr)
    {
        return $this->formatDate($attr);
    }

    public function getDeletedAtAttribute($attr)
    {
        return $this->formatDate($attr);
    }

    public function onCreated()
    {
        return [
            'id' => $this->attributes['id'],
            'createdAt' => $this->formatDate($this->attributes['created_at'])
        ];
    }

    public function onUpdated()
    {
        return [
            'id' => $this->attributes['id'],
            'updatedAt' => $this->formatDate($this->attributes['updated_at']),
        ];
    }

    public function onDeleted()
    {
        return [
            'id' => $this->attributes['id'],
            'deletedAt' => $this->formatDate($this->attributes['deleted_at']),
        ];
    }

    public function pagination($page, $limit, $sort = null, $order = null, $filter = null)
    {
        $sortBy = $sort ? $sort : $this->getKeyName();
        $orderBy = $order ? $order : $this->getSortDirection();

        $data = $this->query();

        if (is_array($filter)) {
            $data->orWhere(function ($query) use ($filter) {
                foreach ($filter as $key => $value) {
                    $query->where($key, 'like', '%' . $value . '%');
                }
            });
        }

        return $data->orderBy($sortBy, $orderBy)
            ->offset(($page - 1) * $page)
            ->limit($limit)
            ->paginate($limit);
    }

    public function getAll($sort = null, $order = null, $filter = null)
    {
        $sortBy = $sort ? $sort : $this->getKeyName();
        $orderBy = $order ? $order : $this->getSortDirection();

        $data = $this->query();

        if (is_array($filter)) {
            $data->orWhere(function ($query) use ($filter) {
                foreach ($filter as $key => $value) {
                    $query->where($key, 'like', '%' . $value . '%');
                }
            });
        }

        return $data->orderBy($sortBy, $orderBy)->get();
    }

    public function scopeWhereIsActive($query)
    {
        return $query->where('is_active', true);
    }
}
