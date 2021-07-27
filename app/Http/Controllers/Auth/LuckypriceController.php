<?php

namespace App\Http\Controllers\Auth;

use App\Luckyprice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class LuckypriceController extends Controller
{
    /**
     * Get the Items
     *
     * @return [json] customer object
     */
    public function index(Request $request){
        $user = auth()->user();
        $message = 'No Lucky Price Found.';
        $status = 0;
        $luckPrices = Luckyprice::with('item.category')->get();
        if(!empty($luckPrices)){
            $status = 1;
            $message = 'Lucky Price lists.';
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'luckyPriceList' => $luckPrices
        ], 200);
    }
    public function store(Request $request)
    {
        $message = 'Lucky price creation failed.';
        $status = 0;
        $request->validate([
            'prices.*.price' => 'required',
            'prices.*.bonus' => 'required',
            'prices.*.stbonus' => 'required'
        ]);
        //dd($request->prices);
        
        if(count($request->prices) > 0){
            foreach($request->prices as $key=>$price){
                $luckyPrice = new Luckyprice;
                $luckyPrice->digit = count($request->prices) - $key;
                $luckyPrice->price = $price['price']; 
                $luckyPrice->bonus = $price['bonus']; 
                $luckyPrice->stbonus = $price['stbonus']; 
                $luckyPrice->item_id = $request->itemId; 
                $luckyPrice->created_by = auth()->user()->id;
                //dd($luckyPrice);
                $luckyPrice->save();
                //dd($luckyPrice);
            }
        }
        
        if(!empty($luckyPrice)){
            $status = 1;
            $message = 'Lucky price created.';
        }
        return response()->json([
            'status' => $status,
            'message' => $message
        ], 200);
    }
    public function show(Request $request){
        $message = 'Lucky not available.';
        $status = 0;
        $luckPrice = Luckyprice::with('item.category')->find($request->id);
        if(!empty($luckPrice)){
            $status = 1;
            $message = 'Lucky Price lists.';
        }
        
        return response()->json([
            'status' => $status,
            'message' => $message,
            'luckPrice' => $luckPrice
        ], 200);
    } 
    public function update(Request $request)
    {
        $message = 'Lucky price update failed.';
        $status = 0;
        $request->validate([
            'price' => 'required',
            'bonus' => 'required',
            'stbonus' => 'required'
        ]);
        $luckyPrice = Luckyprice::find($request->id);
        $luckyPrice->price = $request->price; 
        $luckyPrice->bonus = $request->bonus; 
        $luckyPrice->stbonus = $request->stbonus; 
        $luckyPrice->save();
        
        if(!empty($luckyPrice)){
            $status = 1;
            $message = 'Lucky price updated.';
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'luckPrice'=>$luckyPrice
        ], 200);
    }

    public function delete(Request $request, $id){
        $message = 'Failed delete Lucky.';
        $status = 0;
        $item = Luckyprice::find($id);
        if($item){
            $item->delete();
            $message = 'Lucky deleted successfully.';
            $status = 1;
        }else{
            $message = 'Lucky does not available.';
            $status = 0;
        }
        return response()->json([
            'status' => $status,
            'message' => $message
        ], 200);
    }
    /*public function getPrice(Request $request){
        $message = 'No extra price.';
        $status = 0;
        if(isset($request->itemId)){
            $customerPrice = Customerprice::with('item')->where('user_id',$request->userId)->where('item_id',$request->itemId)->first();
        }else{
            $customerPrice = Customerprice::with('item')->where('user_id',$request->userId)->get();
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

    public function deleteItem(Request $request, $id){
        $message = 'Failed delete Item.';
        $status = 0;
        $item = Item::find($id);
        if($item){
            $item->delete();
            $message = 'Item deleted successfully.';
            $status = 1;
        }else{
            $message = 'Item does not available.';
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
        $orders = Order::with('customer')->with('item')->where('id',$request->orderId)->where('created_by',$userId)->orderBy('id', 'DESC')->first();
        if(!empty($orders)){
            $status = 1;
            $message = 'Order lists.';
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'ordersList' => $orders
        ], 200);
    }*/
}  
