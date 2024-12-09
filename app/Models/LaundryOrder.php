<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaundryOrder extends Model
{
    use HasFactory;

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
customer_name
customer_phone
customer_address
pickup_address
pickup_gps
delivery_address
special_instructions
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
        if ($this->stripe_url != null && strlen($this->stripe_url) > 5) {
            return $this->stripe_url;
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

        $this->stripe_url = $linkResp->url;
        $this->save();
        return $linkResp->url;
    }
}
