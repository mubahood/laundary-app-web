<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\MainController;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Models\Company;
use App\Models\Consultation;
use App\Models\Gen;
use App\Models\LaundryOrder;
use App\Models\Meeting;
use App\Models\Project;
use App\Models\ReportModel;
use App\Models\Task;
use App\Models\User;
use App\Models\Utils;
use Encore\Admin\Facades\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::get('test-orders', function () {

    $lastOder = LaundryOrder::latest()->first();
    LaundryOrder::send_mails($lastOder); 
    die('done');
    
    dd($lastOder->total_amount);
    $lastOder = LaundryOrder::do_prepare($lastOder); 
    $lastOder->send_order_received_email();
    dd('done'); 
    LaundryOrder::send_mails($lastOder); 
    /* 
    send_mails
        "id" => 8
    "created_at" => "2025-02-27 07:16:46"
    "updated_at" => "2025-02-27 09:11:15"
    "user_id" => 68
    "customer_name" => "Muhind John"
    "customer_phone" => "+256783204611"
    "customer_address" => null
    "pickup_address" => "test address go here"
    "pickup_gps" => "null,null"
    "delivery_address" => "test address go here"
    "special_instructions" => "SOME INSTRUCTIONS"
    "total_amount" => "12.00"
    "payment_status" => "Not Paid"
    "payment_method" => null
    "payment_date" => null
    "stripe_payment_link" => "https://buy.stripe.com/28o9BB3bx1LGaE8eWd"
    "payment_reference" => "plink_1Qx2XTD6XvmPLQKHri47pI5G"
    "payment_notes" => null
    "customer_photos" => null
    "scheduled_pickup_time" => null
    "assigned_driver_id" => null
    "driver_id" => 2
    "actual_pickup_time" => "2025-02-27 00:00:00"
    "pickup_notes" => "3"
    "laundry_delivery_time" => null
    "washer_assignment_time" => null
    "assigned_washer_id" => null
    "washer_id" => 2
    "washing_start_time" => null
    "washing_end_time" => null
    "drying_start_time" => "11:00AM-11:30AM"
    "drying_end_time" => null
    "scheduled_delivery_time" => null
    "delivery_driver_id" => 2
    "actual_delivery_time" => null
    "delivery_notes" => "2"
    "final_payment_date" => null
    "receipt_approved_date" => null
    "rating" => null
    "driver_amount" => null
    "driving_distance" => null
    "feedback" => "CM1928"
    "local_id" => "137232-35967-250180-392563-803668-339719-373070-909306-858981-839344-3420"
    "status" => "DELIVERED"
    "washing_amount" => "11.00"
    "service_amount" => "1.00"
    "weight" => "12.00"
    "drying_start_time_weight" => null
    "order_received_email_sent" => "Yes"
    "order_picked_up_email_sent" => "Yes"
    "order_ready_for_payment_email_sent" => "Yes"
    "order_receipt_email_sent" => "No"
    "order_washed_email_sent" => "No"
    "order_delivered_email_sent" => "No"
    "order_feedback_email_sent" => "No"
    "driver_email_sent" => "No"
    */

    return "Cleared!";
});

Route::get('/clear', function () {

    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');

    return "Cleared!";
});
Route::get('migrate', function () {
    //run laravel migrate command in code
    $RESP = Artisan::call('migrate');
    echo "<pre>";
    print_r($RESP);
    echo "</pre>";
    die();
});

Route::get('mail-test', function () {

    /* 
    $orderStages = [
        "PICKED UP",
        "READY FOR PAYMENT",
        "AWAITING WASHING",
        "WASHING IN PROGRESS",
        "READY FOR DELIVERY",
        "OUT FOR DELIVERY",
        "DELIVERED",
        "CANCELLED",
        "COMPLETED"
    ];
    */

    $order = LaundryOrder::find(1);
    $order->customer_name .= '1';
    $order->status = 'READY FOR PAYMENT';
    $order->driver_amount =  '10';
    $order->driving_distance =  '10';
    $order->washing_amount =  '10';
    $order->service_amount =  '10';
    $order->weight =  '10';


    $order->save();
    die("done");

    $url = url('mail-test');
    $from = env('APP_NAME') . " Team.";
    $email_address = 'mubahood360@gmail.com';
    $name = 'Muhindo Mubaraka';

    $mail_body =
        <<<EOD
        <p>Dear <b>Muhindo Mubaraka</b>,</p>
        <p>Please use the code below to reset your password.</p><p style="font-size: 25px; font-weight: bold; text-align: center; color:rgb(7, 76, 194); "><b>$name</b></p>
        <p>Or clink on the link below to reset your password.</p>
        <p><a href="#">Reset Password</a></p>
        <p>Best regards,</p>
        <p>{$from}</p>
        EOD;
    try {
        $day = date('Y-m-d');
        $data['body'] = $mail_body;
        $data['data'] = $data['body'];
        $data['name'] = $name;
        $data['email'] = $email_address;
        $data['subject'] = 'Password Reset - ' . env('APP_NAME') . ' - ' . $day . ".";
        Utils::mail_sender($data);
    } catch (\Throwable $th) {
        throw $th;
    }
});
Route::get('test-order', function () {

    $latestOrder = LaundryOrder::latest()->first();
    //if 
    if ($latestOrder == null) {
        throw new \Exception('No order found');
    }
    if (strtolower($latestOrder->payment_status) == 'paid') {
        return 'Order already paid';
    }
    try {
        $orderUrl = $latestOrder->get_payment_link();
    } catch (\Exception $e) {
    }
    dd($d);
    $d = $latestOrder->get_payment_link();

    try {
        $orderUrl = $latestOrder->get_payment_link();
    } catch (\Exception $e) {
        throw $e;
    }
    echo '<a href="' . $orderUrl . '">Pay for order url # ' . $orderUrl . '</a>';
    die();
});
Route::get('migrate', function () {
    // Artisan::call('migrate');
    //do run laravel migration command
    Artisan::call('migrate', ['--force' => true]);
    //returning the output
    return Artisan::output();
});

Route::get('medical-report', function () {
    $id = $_GET['id'];
    $item = Consultation::find($id);
    if ($item == null) {
        die('item not found');
    }
    $item->process_invoice();

    if (isset($_GET['html'])) {
        return $item->process_report();
    }
    $item->process_report();
    $url = url('storage/' . $item->report_link);
    return redirect($url);
    die($url);;
});


Route::get('regenerate-invoice', function () {
    $id = $_GET['id'];
    $item = Consultation::find($id);
    if ($item == null) {
        die('item not found');
    }
    $item->process_invoice();
    $url = url('storage/' . $item->invoice_pdf);

    return redirect($url);
    die($url);
    $company = Company::find(1);
    $pdf = App::make('dompdf.wrapper');
    $pdf->set_option('enable_html5_parser', TRUE);
    $pdf->loadHTML(view('invoice', [
        'item' => $item,
        'company' => $company,
    ])->render());
    return $pdf->stream('test.pdf');
});



Route::get('app', function () {
    //return url('taskease-v1.apk');
    return redirect(url('taskease-v1.apk'));
});
Route::get('report', function () {

    $id = $_GET['id'];
    $item = ReportModel::find($id);
    $pdf = App::make('dompdf.wrapper');
    $pdf->set_option('enable_html5_parser', TRUE);
    $pdf->loadHTML(view('report', [
        'item' => $item,
    ])->render());
    return $pdf->stream('test.pdf');
});



Route::get('project-report', function () {

    $id = $_GET['id'];
    $project = Project::find($id);

    $pdf = App::make('dompdf.wrapper');
    //'isHtml5ParserEnabled', true
    $pdf->set_option('enable_html5_parser', TRUE);


    $pdf->loadHTML(view('project-report', [
        'title' => 'project',
        'item' => $project,
    ])->render());

    return $pdf->stream('file_name.pdf');
});

//return view('mail-1');

Route::get('reset-mail', function () {
    $u = User::find($_GET['id']);
    try {
        $u->send_password_reset();
        die('Email sent');
    } catch (\Throwable $th) {
        die($th->getMessage());
    }
});

Route::get('reset-password', function () {
    $u = User::where([
        'stream_id' => $_GET['token']
    ])->first();
    if ($u == null) {
        die('Invalid token');
    }
    return view('auth.reset-password', ['u' => $u]);
});

Route::post('reset-password', function () {
    $u = User::where([
        'stream_id' => $_GET['token']
    ])->first();
    if ($u == null) {
        die('Invalid token');
    }
    $p1 = $_POST['password'];
    $p2 = $_POST['password_1'];
    if ($p1 != $p2) {
        return back()
            ->withErrors(['password' => 'Passwords do not match.'])
            ->withInput();
    }
    $u->password = bcrypt($p1);
    $u->save();

    return redirect(admin_url('auth/login') . '?message=Password reset successful. Login to continue.');
    if (Auth::attempt([
        'email' => $u->email,
        'password' => $p1,
    ], true)) {
        die();
    }
    return back()
        ->withErrors(['password' => 'Failed to login. Try again.'])
        ->withInput();
});

Route::get('request-password-reset', function () {
    return view('auth.request-password-reset');
});

Route::post('request-password-reset', function (Request $r) {
    $u = User::where('email', $r->username)->first();
    if ($u == null) {
        return back()
            ->withErrors(['username' => 'Email address not found.'])
            ->withInput();
    }
    try {
        $u->send_password_reset();
        $msg = 'Password reset link has been sent to your email ' . $u->email . ".";
        return redirect(admin_url('auth/login') . '?message=' . $msg);
    } catch (\Throwable $th) {
        $msg = $th->getMessage();
        return back()
            ->withErrors(['username' => $msg])
            ->withInput();
    }
});

Route::get('auth/login', function () {
    $u = Admin::user();
    if ($u != null) {
        return redirect(url('/'));
    }

    return view('auth.login');
})->name('login');

Route::get('mobile', function () {
    return url('');
});
Route::get('test', function () {
    $m = Meeting::find(1);
});


Route::get('policy', function () {
    return view('policy');
});

Route::get('/gen-form', function () {
    die(Gen::find($_GET['id'])->make_forms());
})->name("gen-form");


Route::get('generate-class', [MainController::class, 'generate_class']);
Route::get('/gen', function () {
    $m = Gen::find($_GET['id']);
    if ($m == null) {
        return "Not found";
    }
    die($m->do_get());
})->name("register");
