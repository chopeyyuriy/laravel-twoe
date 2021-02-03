<?php

namespace App\Http\Controllers\Admin;

use App\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;

class OrdersController extends Controller
{
    public function index()
    {
        $all_orders = Order::leftJoin('users', 'orders.user_id', 'users.id')
            ->select('orders.*', 'users.login', 'users.avatar')
            ->get();

        return view('admin.orders.index', ['all_orders' => $all_orders]);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        $all_orders = Order::leftJoin('users', 'orders.user_id', 'users.id')
            ->where('orders.id', $id)
            ->select('orders.*', 'users.login')
            ->get();

        return response()->json([
            'orders' => $all_orders
        ]);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
        ]);
        $edit_order = Order::findOrFail($id);
        $edit_order->title = $request->title;
        $edit_order->description = $request->description;
        if (isset($request->status)) {
            $edit_order->status = $request->status;
        }
        $edit_order->save();
        return redirect('/admin/orders')->with('success', 'Заявка изменена !');
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return redirect('/admin/orders')->with('success', 'Заявка удалена !');
    }


    public function change_status(Request $request)
    {
        $old_status = DB::table('orders')->where('id', $request->id)->select('status')->first();

        if ($request->status == 'active' && $old_status->status != 'active') {
            // order sendTo
            $order = Order::where('id', $request->id)->first();
            if ($order){
                $order->sendToTelegram();
            }
        }

        DB::table('orders')->where('id', $request->id)->update(['status' => $request->status]);
        return response()->json([
            'status' => 'changed'
        ]);
    }

}
