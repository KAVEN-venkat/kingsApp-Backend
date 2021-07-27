<?php

namespace App\Http\Controllers\Auth;

use App\Customerprice;
use App\Item;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class PriceController extends Controller
{
    /**
     * Get the Items
     *
     * @return [json] customer object
     */
    public function index(Request $request){
        /*$message = 'No Items Found.';
        $status = 0;
        $items = Customerprice::get();
        if(!empty($items)){
            $status = 1;
            $message = 'Item lists.';
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'itemsList' => $items
        ], 200);*/
    }
    public function store(Request $request)
    {
        $message = 'Customer price creation failed.';
        $status = 0;
        $request->validate([
            'user_id' => 'required',
            'item_id' => 'required',
            'extra_price' => 'required'
        ]);
        $customerPrice = new Customerprice;
        if(User::where('id',$request->user_id)->exists()){
            if(Item::where('id',$request->item_id)->exists()){
                if(!Customerprice::where('user_id',$request->user_id)->where('item_id',$request->item_id)->exists()) {        
                    $customerPrice->user_id = $request->user_id;
                    $customerPrice->item_id = $request->item_id;        
                    $customerPrice->extra_price = $request->extra_price;
                    $customerPrice->save();
                    if(!empty($customerPrice)){
                        $status = 1;
                        $message = 'Customer price created.';
                    }
                }else{
                    $status = 1;
                    $message = 'Customer price already added.';
                }
            }else{
                $status = 1;
                $message = 'Ticket was not available.';
            }
        }else{
            $status = 1;
            $message = 'Customer was not available.';
        }        
        return response()->json([
            'status' => $status,
            'message' => $message,
            'customerPriceDetails' => $customerPrice
        ], 200);
    }
    public function getPrice(Request $request){
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
}  
