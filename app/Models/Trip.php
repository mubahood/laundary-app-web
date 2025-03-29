<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;

    //oncreating boot

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {

            //IF TYPE IS AWAITING FOR PICKUP, CHANGE IT TO PICKUP
            if ($model->type == 'AWAITING FOR PICKUP') {
                $model->type = 'PICKUP';
            } 

            $hasExisingPickup = Trip::where('driver_id', $model->driver_id)
                ->where('type',  $model->type)
                ->where('status', 'ONGOING')
                ->count();
            if ($hasExisingPickup > 0) {
                throw new \Exception("Driver has an ongoing pickup trip", 1);
            }

            $delivery = Trip::where('driver_id', $model->driver_id)
                ->where('type',  $model->type)
                ->where('status', 'ONGOING')
                ->count();
            if ($delivery > 0) {
                throw new \Exception("Driver has an ongoing delivery trip", 1);
            }
            return $model;
        });

        //UPDATING
        static::updating(function ($model) {
            if ($model->type == 'AWAITING FOR PICKUP') {
                $model->type = 'PICKUP';
            }
            return $model;
        }); 
    }

    //GETTER FRO driver_text
    public function getDriverTextAttribute()
    {
        $driver = User::find($this->driver_id);
        if ($driver == null) {
            return "Driver not found";
        }
        return $driver->name . " - " . $driver->phone_number_1;
    }

    //items  getter
    public function getItemsAttribute()
    {
        if ($this->type == 'PICKUP') {
            return LaundryOrder::where('pickup_notes', $this->id)->count();
        } else {
            return LaundryOrder::where('delivery_notes', $this->id)->count();
        }
    }

    //appends driver_text
    protected $appends = ['driver_text'];
}
