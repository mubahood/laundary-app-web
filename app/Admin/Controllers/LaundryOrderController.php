<?php

namespace App\Admin\Controllers;

use App\Models\LaundryOrder;
use App\Models\User;
use App\Models\Utils;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class LaundryOrderController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'LaundryOrder';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new LaundryOrder());
        $grid->model()->orderBy('id', 'desc');
        $grid->column('id', __('ORDER #ID'))
            ->sortable()
            ->display(function ($id) {
                return "#" . $id;
            });
        $grid->column('created_at', __('Date'))
            ->sortable()
            ->display(function ($created_at) {
                return date('d M Y', strtotime($created_at));
            });
        $grid->column('customer_name', __('Customer name'))
            ->sortable()
            ->display(function ($customer_name) {
                $u = User::where('id', $this->user_id)->first();
                if ($u) {
                    return $u->name;
                } else {
                    return $customer_name;
                }
            });
        $grid->column('customer_phone', __('Customer Phone'))
            ->sortable();

        $grid->column('pickup_address', __('Pickup Address'))
            ->sortable()
            ->display(function ($pickup_address) {
                return $pickup_address;
            });
        $grid->column('pickup_gps', __('Pickup GPS'))
            ->sortable()
            ->display(function ($pickup_gps) {
                return $pickup_gps;
            })->editable();
        $grid->column('delivery_address', __('Delivery address'));
        $grid->column('special_instructions', __('Special instructions'))
            ->display(function ($special_instructions) {
                return $special_instructions;
            });
        $grid->column('total_amount', __('Total Billing Amount (CAD)'))
            ->sortable()
            ->display(function ($total_amount) {
                return number_format($total_amount, 2);
            });
        $grid->column('payment_status', __('Payment Status'))
            ->sortable()
            ->label([
                'Paid' => 'success',
                'Not Paid' => 'danger',
            ])->filter([
                'Paid' => 'Paid',
                'Not Paid' => 'Not Paid',
            ]);
        $grid->column('payment_method', __('Payment Method'))->hide();
        $grid->column('payment_date', __('Payment date'))->hide();
        $grid->column('stripe_payment_link', __('Stripe payment link'))->hide();
        $grid->column('payment_reference', __('Payment reference'))->hide();
        $grid->column('payment_notes', __('Payment notes'))->hide();
        $grid->column('customer_photos', __('Customer photos'))->hide();
        $grid->column('scheduled_pickup_time', __('Scheduled pickup time'))->hide();
        $grid->column('assigned_driver_id', __('Assigned driver id'))->hide();
        $grid->column('driver_id', __('Driver id'))->hide();
        $grid->column('actual_pickup_time', __('Actual pickup time'))->hide();
        $grid->column('pickup_notes', __('Pickup notes'))->hide();
        $grid->column('laundry_delivery_time', __('Laundry delivery time'))->hide();
        $grid->column('washer_assignment_time', __('Washer assignment time'))->hide();
        $grid->column('assigned_washer_id', __('Assigned washer id'))->hide();
        $grid->column('washer_id', __('Washer id'))->hide();
        $grid->column('washing_start_time', __('Washing start time'))->hide();
        $grid->column('washing_end_time', __('Washing end time'))->hide();
        $grid->column('drying_start_time', __('Drying start time'))->hide();
        $grid->column('drying_end_time', __('Drying end time'))->hide();
        $grid->column('scheduled_delivery_time', __('Scheduled delivery time'))->hide();
        $grid->column('delivery_driver_id', __('Delivery driver id'))->hide();
        $grid->column('actual_delivery_time', __('Actual delivery time'))->hide();
        $grid->column('delivery_notes', __('Delivery notes'))->hide();
        $grid->column('final_payment_date', __('Final payment date'))->hide();
        $grid->column('receipt_approved_date', __('Receipt approved date'))->hide();
        $grid->column('rating', __('Rating'))->hide();
        $grid->column('driver_amount', __('Driver amount'))->hide();
        $grid->column('driving_distance', __('Driving distance'))->hide();
        $grid->column('feedback', __('Feedback'))->hide();
        $grid->column('status', __('Status'))
            ->label([
                'PENDING' => 'default',
                'AWAITING PICKUP' => 'primary',
                'Picked Up' => 'success',
                'BILLING' => 'warning',
                'READY FOR PAYMENT' => 'info',
                'ASSIGN WASHER' => 'success',
                'Washing in Progress' => 'success',
                'READY FOR DELIVERY' => 'info',
                'OUT FOR DELIVERY' => 'success',
                'DELIVERED' => 'success',
                'COMPLETED' => 'success',
            ])->filter([
                'PENDING' => 'PENDING',
                'AWAITING PICKUP' => 'AWAITING PICKUP',
                strtoupper('Picked Up') => strtoupper('Picked Up'),
                'BILLING' => 'BILLING',
                strtoupper('Washing in Progress') => strtoupper('Washing in Progress'),
                'READY FOR PAYMENT' => 'READY FOR PAYMENT',
                strtoupper('Ready for Delivery') => strtoupper('Ready for Delivery'),
                strtoupper('Out for Delivery') => strtoupper('Out for Delivery'),
                strtoupper('Delivered') => strtoupper('Delivered'),
            ]);
        $grid->column('washing_amount', __('Washing Amount'))->sortable();
        $grid->column('service_amount', __('Service Amount'))->sortable();
        $grid->column('weight', __('Weight'))->sortable();
        $grid->disableBatchActions();

        // $grid->column('order_received_email_sent', __('Order received email sent'));
        // $grid->column('order_picked_up_email_sent', __('Order picked up email sent'));
        // $grid->column('order_ready_for_payment_email_sent', __('Order ready for payment email sent'));
        // $grid->column('order_receipt_email_sent', __('Order receipt email sent'));
        // $grid->column('order_washed_email_sent', __('Order washed email sent'));
        // $grid->column('order_delivered_email_sent', __('Order delivered email sent'));
        // $grid->column('order_feedback_email_sent', __('Order feedback email sent'));
        // $grid->column('driver_email_sent', __('Driver email sent'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(LaundryOrder::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('user_id', __('User id'));
        $show->field('customer_name', __('Customer name'));
        $show->field('customer_phone', __('Customer phone'));
        $show->field('customer_address', __('Customer address'));
        $show->field('pickup_address', __('Pickup address'));
        $show->field('pickup_gps', __('Pickup gps'));
        $show->field('delivery_address', __('Delivery address'));
        $show->field('special_instructions', __('Special instructions'));
        $show->field('total_amount', __('Total amount'));
        $show->field('payment_status', __('Payment status'));
        $show->field('payment_method', __('Payment method'));
        $show->field('payment_date', __('Payment date'));
        $show->field('stripe_payment_link', __('Stripe payment link'));
        $show->field('payment_reference', __('Payment reference'));
        $show->field('payment_notes', __('Payment notes'));
        $show->field('customer_photos', __('Customer photos'));
        $show->field('scheduled_pickup_time', __('Scheduled pickup time'));
        $show->field('assigned_driver_id', __('Assigned driver id'));
        $show->field('driver_id', __('Driver id'));
        $show->field('actual_pickup_time', __('Actual pickup time'));
        $show->field('pickup_notes', __('Pickup notes'));
        $show->field('laundry_delivery_time', __('Laundry delivery time'));
        $show->field('washer_assignment_time', __('Washer assignment time'));
        $show->field('assigned_washer_id', __('Assigned washer id'));
        $show->field('washer_id', __('Washer id'));
        $show->field('washing_start_time', __('Washing start time'));
        $show->field('washing_end_time', __('Washing end time'));
        $show->field('drying_start_time', __('Drying start time'));
        $show->field('drying_end_time', __('Drying end time'));
        $show->field('scheduled_delivery_time', __('Scheduled delivery time'));
        $show->field('delivery_driver_id', __('Delivery driver id'));
        $show->field('actual_delivery_time', __('Actual delivery time'));
        $show->field('delivery_notes', __('Delivery notes'));
        $show->field('final_payment_date', __('Final payment date'));
        $show->field('receipt_approved_date', __('Receipt approved date'));
        $show->field('rating', __('Rating'));
        $show->field('driver_amount', __('Driver amount'));
        $show->field('driving_distance', __('Driving distance'));
        $show->field('feedback', __('Feedback'));
        $show->field('local_id', __('Local id'));
        $show->field('status', __('Status'));
        $show->field('washing_amount', __('Washing amount'));
        $show->field('service_amount', __('Service amount'));
        $show->field('weight', __('Weight'));
        $show->field('drying_start_time_weight', __('Drying start time weight'));
        $show->field('order_received_email_sent', __('Order received email sent'));
        $show->field('order_picked_up_email_sent', __('Order picked up email sent'));
        $show->field('order_ready_for_payment_email_sent', __('Order ready for payment email sent'));
        $show->field('order_receipt_email_sent', __('Order receipt email sent'));
        $show->field('order_washed_email_sent', __('Order washed email sent'));
        $show->field('order_delivered_email_sent', __('Order delivered email sent'));
        $show->field('order_feedback_email_sent', __('Order feedback email sent'));
        $show->field('driver_email_sent', __('Driver email sent'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new LaundryOrder());

        $id = request()->segments()[1];
        $order = LaundryOrder::find($id);
        if ($order == null) {
            throw new \Exception("Order not found");
        }
        if ($order->customer == null) {
            throw new \Exception("Customer not found");
        }
        $form->display('applicant_name', __('Customer name'))->default($order->customer->name);
        $form->display('id', __('Order ID'))->default($order->id);
        $form->display('customer_name', __('Customer Name'))->default($order->customer_name);
        $form->display('customer_phone', __('Customer Phone'))->default($order->customer_phone);
        $form->display('delivery_address', __('Delivery Address'))->default($order->delivery_address);
        $form->display('total_amount', __('Total Amount'))->default($order->total_amount);
        $form->display('payment_status', __('Payment Status'))->default($order->payment_status);
        $form->display('status', __('Order Status'))->default($order->status);
        $form->display('special_instructions', __('Special Instructions'))->default($order->special_instructions);
        $form->display('scheduled_pickup_time', __('Scheduled Pickup Time'))->default($order->scheduled_pickup_time);
        $form->display('actual_pickup_time', __('Actual Pickup Time'))->default($order->actual_pickup_time);
        $form->display('user_id', __('User ID'))->default($order->user_id);
        $form->display('customer_address', __('Customer Address'))->default($order->customer_address);
        $form->display('pickup_address', __('Pickup Address'))->default($order->pickup_address);
        $form->display('pickup_gps', __('Pickup GPS'))->default($order->pickup_gps);
        $form->divider('UPDATE ORDER STATUS');
        
        $form->textarea('special_instructions', __('Special instructions'));

        $form->radio('status', 'Order Status')->options([
            'PENDING' => 'PENDING',
            'AWAITING PICKUP' => 'AWAITING PICKUP',
            strtoupper('Picked Up') => strtoupper('Picked Up'),
            'BILLING' => 'BILLING',
            'READY FOR PAYMENT' => 'READY FOR PAYMENT',
            'ASSIGN WASHER' => 'ASSIGN WASHER',
            strtoupper('Washing in Progress') => strtoupper('Washing in Progress'),
            'READY FOR DELIVERY' => 'READY FOR DELIVERY',
            'OUT FOR DELIVERY' => 'OUT FOR DELIVERY',
            'DELIVERED' => 'DELIVERED',
            'COMPLETED' => 'COMPLETED',
        ])->when('AWAITING PICKUP', function ($form) {
            $drivers = Utils::get_drivers();
            //assigned_driver_id
            $form->select('driver_id', __('Assigned Driver '))->options($drivers)->rules('required');
        })->when('BILLING', function ($form) {
            //ugnewz24@gmail.com	
            $form->decimal('weight', __('Total weight (in LB)'))->rules('required|numeric|min:0.1');
            $form->decimal('service_amount', __('Service Amount'))->rules('required|numeric|min:0.1');
            $form->decimal('washing_amount', __('Washing Amount'))->rules('required|numeric|min:0.1');
            //display total_amount
            $form->display('total_amount', __('Total Amount'))->default(number_format($form->total_amount, 2));

            //payment_status
            $form->radio('payment_status', __('Payment Status'))->options([
                'Paid' => 'Paid',
                'Not Paid' => 'Not Paid',
            ])->default('Unpaid')->rules('required')
                ->when('Paid', function ($form) {
                    $form->text('payment_method', __('Payment Method'))->rules('required');
                    $form->text('payment_date', __('Payment Date'))->default(date('Y-m-d H:i:s'));
                    $form->text('stripe_payment_link', __('Stripe Payment Link'));
                    $form->text('payment_reference', __('Payment Reference'));
                    $form->text('payment_notes', __('Payment Notes'));
                });
            //payment_status
        })->when('ASSIGN WASHER', function ($form) {
            $warshers = Utils::get_warshers();
            $form->select('assigned_washer_id', __('Assigned Washer'))->options($warshers)->rules('required');
        })->when(strtoupper('Washing in Progress'), function ($form) {
            $form->datetime('washing_start_time', __('Washing Start Time'))->default(date('Y-m-d H:i:s'));
        })->when('READY FOR DELIVERY', function ($form) {
            $drivers = Utils::get_drivers(); 
            $form->select('delivery_driver_id', __('Delivery Driver'))->options($drivers)->rules('required');
        });



        return $form;
        $form->number('user_id', __('User id'));
        $form->textarea('customer_name', __('Customer name'));
        $form->textarea('customer_phone', __('Customer phone'));
        $form->textarea('customer_address', __('Customer address'));
        $form->textarea('pickup_address', __('Pickup address'));
        $form->textarea('pickup_gps', __('Pickup gps'));
        $form->textarea('delivery_address', __('Delivery address'));
        $form->decimal('total_amount', __('Total amount'));
        $form->text('payment_status', __('Payment status'))->default('Unpaid');
        $form->textarea('payment_method', __('Payment method'));
        $form->textarea('payment_date', __('Payment date'));
        $form->textarea('stripe_payment_link', __('Stripe payment link'));
        $form->textarea('payment_reference', __('Payment reference'));
        $form->textarea('payment_notes', __('Payment notes'));
        $form->textarea('customer_photos', __('Customer photos'));
        $form->datetime('scheduled_pickup_time', __('Scheduled pickup time'))->default(date('Y-m-d H:i:s'));
        $form->number('assigned_driver_id', __('Assigned driver id'));
        $form->number('driver_id', __('Driver id'));
        $form->datetime('actual_pickup_time', __('Actual pickup time'))->default(date('Y-m-d H:i:s'));
        $form->textarea('pickup_notes', __('Pickup notes'));
        $form->datetime('laundry_delivery_time', __('Laundry delivery time'))->default(date('Y-m-d H:i:s'));
        $form->datetime('washer_assignment_time', __('Washer assignment time'))->default(date('Y-m-d H:i:s'));
        $form->number('assigned_washer_id', __('Assigned washer id'));
        $form->number('washer_id', __('Washer id'));
        $form->datetime('washing_start_time', __('Washing start time'))->default(date('Y-m-d H:i:s'));
        $form->datetime('washing_end_time', __('Washing end time'))->default(date('Y-m-d H:i:s'));
        $form->textarea('drying_start_time', __('Drying start time'));
        $form->datetime('drying_end_time', __('Drying end time'))->default(date('Y-m-d H:i:s'));
        $form->datetime('scheduled_delivery_time', __('Scheduled delivery time'))->default(date('Y-m-d H:i:s'));
        $form->number('delivery_driver_id', __('Delivery driver id'));
        $form->datetime('actual_delivery_time', __('Actual delivery time'))->default(date('Y-m-d H:i:s'));
        $form->textarea('delivery_notes', __('Delivery notes'));
        $form->datetime('final_payment_date', __('Final payment date'))->default(date('Y-m-d H:i:s'));
        $form->datetime('receipt_approved_date', __('Receipt approved date'))->default(date('Y-m-d H:i:s'));
        $form->number('rating', __('Rating'));
        $form->number('driver_amount', __('Driver amount'));
        $form->number('driving_distance', __('Driving distance'));
        $form->textarea('feedback', __('Feedback'));
        $form->textarea('local_id', __('Local id'));
        $form->text('status', __('Status'))->default('Pending');
        $form->decimal('washing_amount', __('Washing amount'));
        $form->decimal('service_amount', __('Service amount'));
        $form->decimal('weight', __('Weight'));
        $form->textarea('drying_start_time_weight', __('Drying start time weight'));
        $form->text('order_received_email_sent', __('Order received email sent'))->default('No');
        $form->text('order_picked_up_email_sent', __('Order picked up email sent'))->default('No');
        $form->text('order_ready_for_payment_email_sent', __('Order ready for payment email sent'))->default('No');
        $form->text('order_receipt_email_sent', __('Order receipt email sent'))->default('No');
        $form->text('order_washed_email_sent', __('Order washed email sent'))->default('No');
        $form->text('order_delivered_email_sent', __('Order delivered email sent'))->default('No');
        $form->text('order_feedback_email_sent', __('Order feedback email sent'))->default('No');
        $form->text('driver_email_sent', __('Driver email sent'))->default('No');

        return $form;
    }
}
