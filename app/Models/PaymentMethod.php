<?php

namespace App\Models;

class PaymentMethod extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'payment_method';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'name',
        'type',
        'bank_transfer',
        'image_url',
        'group',
        'is_active',
        'payload',
    ];
}
