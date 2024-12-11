<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaundryOrder extends Model
{
    use HasFactory;

    //boot
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $m = self::do_prepare($model);
            return $m;
        });
        static::updating(function ($model) {
            $m = self::do_prepare($model);
            return $m;
        });
    }

    //static do prepare
    public static function do_prepare($data)
    {
        $customer = User::find($data->user_id);
        if ($customer == null) {
            throw new \Exception("Customer not found", 1);
        }
        //customer_name
        if ($data->customer_name == null || strlen($data->customer_name) < 2) {
            $data->customer_name = $customer->name;
        }
        //customer_phone
        if ($data->customer_phone == null || strlen($data->customer_phone) < 2) {
            $data->customer_phone = $customer->phone_number_1;
        }
        //customer_address
        if ($data->customer_address == null || strlen($data->customer_address) < 2) {
            $data->customer_address = $customer->home_address;
        }

        //pickup_address
        if ($data->pickup_address == null || strlen($data->pickup_address) < 2) {
            $data->pickup_address = $customer->home_address;
        }

        //delivery_address
        if ($data->delivery_address == null || strlen($data->delivery_address) < 2) {
            $data->delivery_address = $data->pickup_address;
        }
    }


    //belongs to customer
    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    /* 

id
created_at
updated_at
user_id
 

total_amount
payment_status
payment_method
payment_date
stripe_payment_link
payment_reference
payment_notes
customer_photos
scheduled_pickup_time
assigned_driver_id
driver_id
actual_pickup_time
pickup_notes
laundry_delivery_time
washer_assignment_time
assigned_washer_id
washer_id
washing_start_time
washing_end_time
drying_start_time
drying_end_time
scheduled_delivery_time
delivery_driver_id
actual_delivery_time
delivery_notes
final_payment_date
receipt_approved_date
rating
driver_amount
driving_distance
feedback
local_id
 
*/
    public function get_payment_link()
    {
        if ($this->stripe_payment_link != null && strlen($this->stripe_payment_link) > 5 && $this->payment_reference != null && strlen($this->payment_reference) > 2) {
            return $this->stripe_payment_link;
        }

        $stripe = env('STRIPE_KEY');
        $stripe = new \Stripe\StripeClient(
            env('STRIPE_KEY')
        );

        $name = "Order #" . $this->id . ", " . $this->customer->name . " - " . date('Y-m-d H:i:s');
        $resp = null;
        try {
            $resp = $stripe->products->create([
                'name' => $name,
                'default_price_data' => [
                    'currency' => 'cad',
                    'unit_amount' => $this->total_amount * 100,
                ],
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
        if ($resp == null) {
            throw new \Exception("Error Processing Request", 1);
        }
        if ($resp->default_price == null) {
            throw new \Exception("Error Processing Request", 1);
        }
        $linkResp = null;
        try {
            $linkResp = $stripe->paymentLinks->create([
                'currency' => 'cad',
                'line_items' => [
                    [
                        'price' => $resp->default_price,
                        'quantity' => 1,
                    ]
                ]
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
        if ($linkResp == null) {
            throw new \Exception("Error Processing Request", 1);
        }

        $this->stripe_payment_link = $linkResp->url;
        $this->payment_reference = $linkResp->id;
        $this->save();
        return $linkResp->url;
    }

    //is_order_paid check with stripe
    public function is_order_paid()
    {
        //check payment_status is yes
        if ($this->payment_status == 'Paid') {
            return 'Paid';
        }

        //check if stripe_payment_link is not empty
        if ($this->stripe_payment_link == null || strlen($this->stripe_payment_link) < 5) {
            return 'Not Paid';
        }

        $stripe = new \Stripe\StripeClient(
            env('STRIPE_KEY')
        );

        try {
            $paymentLink = $stripe->paymentLinks->retrieve(
                $this->payment_reference
            );

            // Check the status of the payment link
            if ($paymentLink->status == 'paid') {
                $sql = "UPDATE laundry_orders SET payment_status = 'Paid', payment_date = '" . date('Y-m-d H:i:s') . "' WHERE id = " . $this->id;
                $this->payment_status = 'Paid';
                $this->payment_date = date('Y-m-d H:i:s');
                $this->save();
                return 'Paid';
            } elseif ($paymentLink->status === 'requires_payment_method') {
                return 'Not Paid';
            } else {
                return 'Not Paid';
            }
        } catch (\Throwable $th) {
            throw $th;
        }
        return 'Not Paid';

        $payment = null;
        try {
            $payment = $stripe->paymentIntents->retrieve(
                $this->payment_reference,
                []
            );
        } catch (\Throwable $th) {
            throw $th;
        }
        if ($payment == null) {
            return 'Not Paid';
        }
        if ($payment->status == 'succeeded') {
            $this->payment_status = 'Paid';
            $this->payment_date = date('Y-m-d H:i:s');
            $this->save();
            return 'Paid';
        }
        return 'Not Paid';
    }

    //getter for driver_text attribute
    public function getDriverTextAttribute()
    {
        if ($this->driver_id == null) {
            return "Not Assigned";
        }
        $driver = User::find($this->driver_id);
        if ($driver == null) {
            return "Not Assigned";
        }
        return $driver->name;
    }
    //appends driver_text
    protected $appends = ['driver_text'];
}
