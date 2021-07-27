<?php

namespace App\Http\Controllers\Auth;

use App\Item;
use App\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class ItemController extends Controller
{
    /**
     * Get the Items
     *
     * @return [json] customer object
     */
    public function index(Request $request){
        $message = 'No Items Found.';
        $status = 0;
        $items = Item::with('category')->orderBy('id','desc')->get();
        if(!empty($items)){
            $status = 1;
            $message = 'Item lists.';
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'itemsList' => $items
        ], 200);
    }
    public function store(Request $request)
    {
        $message = 'Item creation failed.';
        $status = 0;
        $request->validate([
            'category_id' => 'required',
            'item_name' => 'required',
            'item_digits' => 'required',
            'result_time' => 'required',
            'result_code' => 'required',
            'item_price' => 'required'
        ]);
        $date = Carbon::parse($request->result_time);
        $item = new Item;
        $item->category_id = $request->category_id;
        $item->item_name = $request->item_name;
        $item->item_digits = $request->item_digits;
        $item->result_time = $date->format('H:i');
        $item->result_code = $request->result_code;
        $item->item_price = $request->item_price;
        if($request->bonus){
        $item->bonus = $request->bonus;
        }
        if($request->st){
        $item->st = $request->st;
        }
        $item->save();
        
        if(!empty($item)){
            $status = 1;
            $message = 'Item Created.';
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'itemDetails' => $item
        ], 200);
    }
    public function item(Request $request){
        $message = 'Item not available.';
        $status = 0;
        if($request->columnName){
            $item = Item::select($request->columnName)->where('id',$request->itemId)->first();
        }else{
            $item = Item::where('id',$request->itemId)->first();
        }
        
        if(!empty($item)){
            $status = 1;
            $message = 'Item details.';
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'itemDetails' => $item
        ], 200);
    } 
    public function updateItem(Request $request, $id){
        $message = 'Item update failed.';
        $status = 0;
        $item = Item::find($id);
        $item->category_id = $request->category_id;
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
     public function itemsTime(Request $request){
        $message = 'No Items Found.';
        $status = 0;
        $items = Item::select(DB::raw('DATE_FORMAT(result_time, "%H:%i") as result_time'))->distinct()->get();
        if(!empty($items)){
            $status = 1;
            $message = 'Item lists.';
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'itemsList' => $items
        ], 200);
    }
    public function itemByCategory(Request $request){
        $message = 'Item not available.';
        $status = 0;
        $item = Item::where('category_id',$request->id)->get();
        if(!empty($item)){
            $status = 1;
            $message = 'Item details.';
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'itemDetails' => $item
        ], 200);
    }
}  
