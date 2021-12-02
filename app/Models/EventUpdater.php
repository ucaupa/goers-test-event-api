<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;

trait EventUpdater
{
    protected static function boot()
    {
        parent::boot();

        /*
        * During a model create Eloquent will also update the updated_at field so * need to have the updated_by field here as well * */
        static::creating(function ($model) {
            $model->created_by = Auth::user() ? Auth::user()->id : 'Anonymous';
            $model->updated_by = Auth::user() ? Auth::user()->id : 'Anonymous';
        });

        static::updating(function ($model) {
            $model->updated_by = Auth::user() ? Auth::user()->id : 'Anonymous';
        });

        /*
         * Deleting a model is slightly different than creating or deleting. For
         * deletes we need to save the model first with the deleted_by field
         * */
        /*static::deleting(function ($model) {
            $model->deleted_by = Auth::user() ? Auth::user()->id : 'Anonymous';
            $model->save();
        });*/
    }
}
