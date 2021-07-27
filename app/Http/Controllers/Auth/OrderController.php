<?php

namespace App\Http\Controllers\Auth;

use App\Order;
use App\Item;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class OrderController extends Controller
{
    public function orderCount(Request $request){
        $user = auth()->user();
        if($request->user_id){
            $userId = $request->user_id;
        }else{
            $userId = $user->id;
        }
        $message = 'No Orders Found.';
        $status = 0;
        $orders = Order::with('customer')->with('item.category')->where('created_by',$userId)->orderBy('sale_date', 'DESC')->get()->count();
        if(!empty($orders)){
            $status = 1;
            $message = 'Order Count.';
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'ordersCount' => $orders
        ], 200);
    }
    /**
     * Get the Items
     *
     * @return [json] customer object
     */
    public function index(Request $request){
        $user = auth()->user();
        if($request->user_id){
            $userId = $request->user_id;
        }else{
            $userId = $user->id;
        }
        $message = 'No Orders Found.';
        $status = 0;
        $offSet = $request->offset;
        $limit = 100;
        /*if($request->offset == 0){
            $orders = Order::with('customer')->with('item.category')->where('created_by',$userId)->orderBy('sale_date', 'DESC')->skip($offSet*$limit)->take($limit)->get();
        }else{
            $orders = Order::with('customer')->with('item.category')->where('created_by',$userId)->orderBy('sale_date', 'DESC')->get();
        }*/
        $orders = Order::with('customer')->with('item.category')->where('created_by',$userId)->orderBy('sale_date', 'DESC')->skip($offSet*$limit)->take($limit)->get();
        if(!empty($orders)){
            $status = 1;
            $message = 'Order lists.';
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'ordersList' => $orders
        ], 200);
    }
    public function store(Request $request)
    {
        $message = 'Customer order creation failed.';
        $status = 0;
        /*$request->validate([
            'user_id' => 'required',
            'item_id' => 'required',
            'extra_price' => 'required'
        ]);*/
        $orders = array();
        $maxDigit = $request->maxLength;
        if($request->item_digits == 'C'){
            if($request->board != '' && $request->board == 'allboard'){
                $items = Item::where('category_id',$request->category_id)->where('item_digits',$maxDigit)->get();
                foreach ($items as $key => $item) {
                    for($i=$request->item_from;$i<=$request->item_to;$i++){
                        $currentNumber = $i; 
                        $count = strlen($currentNumber);
                        $numZero = "";
                        for($count;$count<$maxDigit;$count++)
                            $numZero = $numZero."0";
                        $order = new Order;
                        $order->user_id = $request->customer_id;
                        $order->category_id = $request->category_id;
                        $order->bill_no = $request->billNo;
                        $order->item_id = $item->id;        
                        $order->extra_price = ($item->bonus != null && $item->st != null) ? ($item->bonus + $item->st) : 0;
                        $order->sale_date = date('Y-m-d',strtotime($request->sale_date));
                        $order->item_price = $item->item_price;
                        $order->item_digits = $request->item_digits;
                        $order->item_number = $numZero.$i;
                        $order->item_qty = $request->item_qty;
                        $order->total_items = $request->item_qty;
                        $order->total_price = $order->total_items * ($order->extra_price + $order->item_price);
                        $order->created_by = auth()->user()->id;
                        $order->save();
                        $orders[] = Order::with('item.category','customer')->find($order->id);
                    }
                }
            }else{
                for($i=$request->item_from;$i<=$request->item_to;$i++){
                    $currentNumber = $i; 
                    $count = strlen($currentNumber);
                    $numZero = "";
                    for($count;$count<$maxDigit;$count++)
                        $numZero = $numZero."0";
                    $order = new Order;
                    $order->user_id = $request->customer_id;
                    $order->category_id = $request->category_id;
                    $order->bill_no = $request->billNo;
                    $order->item_id = $request->item_id;        
                    $order->extra_price = $request->extra_price ? $request->extra_price : 0;
                    $order->sale_date = date('Y-m-d',strtotime($request->sale_date));
                    $order->item_price = $request->item_price;
                    $order->item_digits = $request->item_digits;
                    $order->item_number = $numZero.$i;
                    $order->item_qty = $request->item_qty;
                    $order->total_items = $request->item_qty;
                    $order->total_price = $order->total_items * ($order->extra_price + $order->item_price);
                    $order->created_by = auth()->user()->id;
                    $order->save();
                    $orders[] = Order::with('item.category','customer')->find($order->id);
                }
            }
            $status = 1;
            $message = 'Customer order created.';
        }else if($request->item_digits == 'S'){ 
            //echo $request->item_from."==".$request->item_to;
            for($i=(int) $request->item_from;$i<=(int) $request->item_to;$i+=10){
                $currentNumber = $i; 
                $count = strlen($currentNumber);
                $numZero = "";
                for($count;$count<$maxDigit;$count++)
                    $numZero = $numZero."0";
                //echo $numZero.$i."<br>";
                $order = new Order;
                $order->user_id = $request->customer_id;
                $order->category_id = $request->category_id;
                $order->bill_no = $request->billNo;
                $order->item_id = $request->item_id;        
                $order->extra_price = $request->extra_price ? $request->extra_price : 0;
                $order->sale_date = date('Y-m-d',strtotime($request->sale_date));
                $order->item_price = $request->item_price;
                $order->item_digits = $request->item_digits;
                $order->item_number = $numZero.$i;
                $order->item_qty = $request->item_qty;
                $order->total_items = 1 * $request->item_qty;
                $order->total_price = $order->total_items * ($order->extra_price + $order->item_price);
                $order->created_by = auth()->user()->id;
                $order->save();
                $orders[] = Order::with('item.category','customer')->find($order->id);
            }
            $status = 1;
            $message = 'Customer order created.'; 
        }else if($request->item_digits == 'D'){ 
            for($i=(int) $request->item_from;$i<=(int) $request->item_to;$i+=100){
                $currentNumber = $i; 
                $count = strlen($currentNumber);
                $numZero = "";
                for($count;$count<$maxDigit;$count++)
                    $numZero = $numZero."0";
                //echo $numZero.$i."<br>";
                $order = new Order;
                $order->user_id = $request->customer_id;
                $order->category_id = $request->category_id;
                $order->bill_no = $request->billNo;
                $order->item_id = $request->item_id;        
                $order->extra_price = $request->extra_price ? $request->extra_price : 0;
                $order->sale_date = date('Y-m-d H:m:s',strtotime($request->sale_date));
                $order->item_price = $request->item_price;
                $order->item_digits = $request->item_digits;
                $order->item_number = $numZero.$i;
                $order->item_qty = $request->item_qty;
                $order->total_items = 1 * $request->item_qty;
                $order->total_price = $order->total_items * ($order->extra_price + $order->item_price);
                $order->created_by = auth()->user()->id;
                $order->save();
                $orders[] = Order::with('item.category','customer')->find($order->id);
            }
            $status = 1;
            $message = 'Customer order created.'; 
        }else if($request->item_digits == 'T'){
            for($c=0;$c<10;$c++){
                $compareValue[$c] = str_repeat($c,3);
            }
            for($i=(int) $request->item_from;$i<=(int) $request->item_to;$i++){
                $currentNumber = $i; 
                $count = strlen($currentNumber);
                $numZero = "";
                for($count;$count<$maxDigit;$count++)
                    $numZero = $numZero."0";
                if(in_array($numZero.$i,$compareValue)){
                    $order = new Order;
                    $order->user_id = $request->customer_id;
                    $order->category_id = $request->category_id;
                    $order->bill_no = $request->billNo;
                    $order->item_id = $request->item_id;        
                    $order->extra_price = $request->extra_price ? $request->extra_price : 0;
                    $order->sale_date = date('Y-m-d H:m:s',strtotime($request->sale_date));
                    $order->item_price = $request->item_price;
                    $order->item_digits = $request->item_digits;
                    $order->item_number = $numZero.$i;
                    $order->item_qty = $request->item_qty;
                    $order->total_items = 1 * $request->item_qty;
                    $order->total_price = $order->total_items * ($order->extra_price + $order->item_price);
                    $order->created_by = auth()->user()->id;
                    $order->save();
                    $orders[] = Order::with('item.category','customer')->find($order->id);
                }
            }
            $status = 1;
            $message = 'Customer order created.';
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'orderDetails' => $orders
        ], 200);
    }
    public function getPrice(Request $request){
        $message = 'No extra price.';
        $status = 0;
        if(isset($request->itemId)){
            $customerPrice = Customerprice::with('item.category')->where('user_id',$request->userId)->where('item_id',$request->itemId)->first();
        }else{
            $customerPrice = Customerprice::with('item.category')->where('user_id',$request->userId)->get();
        }
        
        if(!empty($customerPrice)){
            $status = 1;
            $message = 'Extra price.';
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'customerPriceDetails' => $customerPrice
        ], 200);
    } 
    public function updateItem(Request $request, $id){
        $message = 'Item update failed.';
        $status = 0;
        $item = Item::find($id);
        $item->item_name = $request->item_name;
        $item->item_digits = $request->item_digits;
        $item->result_time = date('H:m:s',strtotime($request->result_time));
        $item->result_code = $request->result_code;
        $item->item_price = $request->item_price;       
        if($item->save()){
            $status = 1;
            $message = 'Item details updated.';
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'itemDetails' => $item
        ], 200);
    }

    public function deleteOrder(Request $request, $id){
        $message = 'Failed delete Order.';
        $status = 0;
        $order = Order::find($id);
        if($order){
            $order->delete();
            $message = 'Order deleted successfully.';
            $status = 1;
        }else{
            $message = 'Order does not available.';
            $status = 0;
        }
        return response()->json([
            'status' => $status,
            'message' => $message
        ], 200);
    }
    public function orderByUser(Request $request){
        $user = auth()->user();
        if($request->user_id){
            $userId = $request->user_id;
        }else{
            $userId = $user->id;
        }
        $message = 'No Orders Found.';
        $status = 0;
        $orders = Order::with('customer')->with('item.category')->where('id',$request->orderId)->where('created_by',$userId)->orderBy('id', 'DESC')->first();
       /* $items = array();
        foreach (range($orders['item_from'], $orders['item_to']) as $number) {
            $items[] = $number;
        }
        $orders['items'] = $items;*/
        if(!empty($orders)){
            $status = 1;
            $message = 'Order lists.';
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'ordersList' => $orders
        ], 200);
    }

    public function totalOrderPrice(Request $request){
        $user = auth()->user();
        if($request->user_id){
            $userId = $request->user_id;
        }else{
            $userId = $user->id;
        }
        $orders = Order::where('created_by',$userId)->whereDate('created_at', Carbon::today())->sum('total_price');
        $status = 1;
            $message = 'Order Total Amount.';
        return response()->json([
            'status' => $status,
            'message' => $message,
            'ordersTotal' => array('total'=>$orders)
        ], 200);
    }
}  
