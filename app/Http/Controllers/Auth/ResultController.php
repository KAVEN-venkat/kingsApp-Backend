<?php

namespace App\Http\Controllers\Auth;

use App\Order;
use App\Luckyprice;
use App\Winner;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class ResultController extends Controller
{
    /**
     * Get the Results
     *
     * @return [json] customer object
     */
    public function index(Request $request){
        $startDate = date('Y-m-d', strtotime($request->result_from));
        $endDate = date('Y-m-d', strtotime($request->result_to));
        $resultTime = $request->item_time;
        $message = 'No Results Found.';
        $status = 0;
        $result = array();
        if(isset($request->item_time) && $request->item_time == 'All'){
            /*$orders = Order::join('items', function ($item) use($resultTime) {
                $item->on('orders.item_id', '=', 'items.id');
            })->with('customer')->where('user_id',$request->customer_id)->where('sale_date','>=',$startDate)->where('sale_date','<=',$endDate)->orderBy('orders.id', 'ASC')
            ->select('orders.*','items.item_name','items.item_digits as itemDigits','items.result_time', 'items.result_code', 'items.item_price', 'items.bonus', 'items.st')->get();*/
            //$orders = Order::with('item')->where('user_id',$request->customer_id)->where('sale_date','>=',$startDate)->where('sale_date','<=',$endDate)->get();
            if($request->user_type == 'Sub-Dealer'){
                $user = User::with(['order.item'])->where('created_by',$request->customer_id)->get();
            }else{
                $orders = Order::with('item')->where('user_id',$request->customer_id)->whereDate('sale_date','>=',$startDate)->whereDate('sale_date','<=',$endDate)->get();
            }
            return $user;
            //$winners = Winner::where('winner_date','>=',$startDate)->where('winner_date','<=',$endDate)->get();
        }else{
            /*$orders = Order::join('items', function ($item) use($resultTime) {
                $item->on('orders.item_id', '=', 'items.id')
                     ->where('items.result_time', $resultTime);
            })->with('customer')->where('user_id',$request->customer_id)->where('sale_date','>=',$startDate)->where('sale_date','<=',$endDate)->orderBy('orders.id', 'ASC')
            ->select('orders.*','items.item_name','items.item_digits as itemDigits','items.result_time', 'items.result_code', 'items.item_price', 'items.bonus', 'items.st')->get();*/
            $orders = Order::with(['customer','item' => function ($q) use($resultTime) {
                $q->where('result_time', $resultTime);
            }])->where('user_id',$request->customer_id)->whereDate('sale_date','>=',$startDate)->whereDate('sale_date','<=',$endDate)->get();
            //$winners = Winner::where('winner_date','>=',$startDate)->where('winner_date','<=',$endDate)->where('result_time', $resultTime)->get();
        }
        if(count($orders) > 0){
            $result['customer'] = $orders[0]->customer;
            $total_qty = 0;
            $total_amount = 0;
            $total_bonus = 0;
            $winning_total = 0;
            $winners = array();
            foreach($orders as $key=>$order){
                /*$result['items'][$key]['bonus'] = $order->bonus;
                $result['items'][$key]['created_at'] = date('Y-m-d',strtotime($order->created_at));
                $result['items'][$key]['created_by'] = $order->created_by;
                $result['items'][$key]['extra_price'] = $order->extra_price;
                $result['items'][$key]['id'] = $order->id;
                $result['items'][$key]['itemDigits'] = $order->itemDigits;
                $result['items'][$key]['item_digits'] = $order->item_digits;
                $result['items'][$key]['item_from'] = $order->item_from;
                $result['items'][$key]['item_id'] = $order->item_id;
                $result['items'][$key]['item_name'] = $order->item_name;
                $result['items'][$key]['item_price'] = $order->item_price;
                $result['items'][$key]['item_to'] = $order->item_to;
                $result['items'][$key]['result_code'] = $order->result_code;
                $result['items'][$key]['result_time'] = $order->result_time;
                $result['items'][$key]['sale_date'] = $order->sale_date;
                $result['items'][$key]['st'] = $order->st;
                $result['items'][$key]['total_items'] = $order->total_items;
                $result['items'][$key]['total_price'] = $order->total_price;
                $result['items'][$key]['updated_at'] = $order->updated_at;
                $result['items'][$key]['user_id'] = $order->user_id;*/
                $itemQty = 0;
                $totalBonus = 0;
                $winningAmount = 0;
                $n=$order->item->item_digits;
                $itemName = str_replace('M', '', $order->item->item_name);
                $itemName = str_replace('E', '', $order->item->item_name);
                $orderDate = date('Y-m-d', strtotime($order->sale_date));
                $resultTime = $order->item->result_time;
                for ($i = 0; $i < $n; $i++) {
                        $itemNum = substr($order->item_number, $i);
                        if(strlen($itemNum) == 5){
                            $winner = Winner::whereRaw("CONCAT(winner_d,winner_e,winner_a,winner_b,winner_c) like ('".$itemNum."')")->where('winner_date',$orderDate)->where('result_time',$resultTime)->first();
                            if($winner){
                                $bonusWinner = self::luckPrice($order->item_id,strlen($itemNum));
                                if($bonusWinner){
                                    $winningAmount = $winningAmount + $bonusWinner->price;
                                    $totalBonus = $totalBonus + ($bonusWinner->bonus + $bonusWinner->stbonus);
                                }
                                break;
                            }
                        }else if(strlen($itemNum) == 4){
                            $winner = Winner::whereRaw("CONCAT(winner_e,winner_a,winner_b,winner_c) like ('".$itemNum."')")->where('winner_date',$orderDate)->where('result_time',$resultTime)->first();
                            if($winner){
                                $bonusWinner = self::luckPrice($order->item_id,strlen($itemNum));
                                if($bonusWinner){
                                    $winningAmount = $winningAmount + $bonusWinner->price;
                                    $totalBonus = $totalBonus + ($bonusWinner->bonus + $bonusWinner->stbonus);
                                }
                                break;
                            }
                        }else if(strlen($itemNum) == 3){
                            $winner = Winner::whereRaw("CONCAT(winner_a,winner_b,winner_c) like ('".$itemNum."')")->where('winner_date',$orderDate)->where('result_time',$resultTime)->first();
                            if($winner){
                                $bonusWinner = self::luckPrice($order->item_id,strlen($itemNum));
                                if($bonusWinner){
                                    $winningAmount = $winningAmount + $bonusWinner->price;
                                    $totalBonus = $totalBonus + ($bonusWinner->bonus + $bonusWinner->stbonus);
                                }
                                break;
                            }
                        }else if(strlen($itemNum) == 2){
                            $winner = Winner::whereRaw("CONCAT(winner_b,winner_c) like ('".$itemNum."')")->where('winner_date',$orderDate)->where('result_time',$resultTime)->first();
                            if($winner){
                                $bonusWinner = self::luckPrice($order->item_id,strlen($itemNum));
                                if($bonusWinner){
                                    $winningAmount = $winningAmount + $bonusWinner->price;
                                    $totalBonus = $totalBonus + ($bonusWinner->bonus + $bonusWinner->stbonus);
                                }
                                break;
                            }
                        }else{
                            if($n == 1){
                                $winner = Winner::whereRaw("CONCAT(winner_".strtolower($itemName).") like ('".$itemNum."')")->where('winner_date',$orderDate)->where('result_time',$resultTime)->first();
                            }else{
                                $winner = Winner::whereRaw("CONCAT(winner_c) like ('".$itemNum."')")->where('winner_date',$orderDate)->where('result_time',$order->item->result_time)->first();
                            }
                            if($winner){
                                $bonusWinner = self::luckPrice($order->item_id,strlen($itemNum));
                                if($bonusWinner){
                                    $winningAmount = $winningAmount + $bonusWinner->price;
                                    $totalBonus = $totalBonus + ($bonusWinner->bonus + $bonusWinner->stbonus);
                                }
                                //break;
                            }
                        }
                    }
            
                //$winner = Winner::whereRaw("CONCAT('winner_a','winner_b','winner_c') = ?", [$order->item->item_number])->get();
                //$winner = Winner::whereRaw("CONCAT('winner_b','winner_c') = ?", [substr($order->item_number, 1)])->get();
                //$winner = Winner::where('winner_c', substr($order->item_number, 2))->get();
                
                /*$winners = self::getWinnerprice($order->result_time,$startDate,$endDate,$winners);
                //print_r($winners);
                if($order->item_digits == 'C'){
                    for ($i = $order->item_from; $i <= $order->item_to; $i++) {
                        $itemQty = $itemQty + 1;
                        if(strlen($i) <= $order->itemDigits){
                            $a = "";
                            $compVal = $order->itemDigits - strlen($i);
                            for($j=0; $j < $compVal; $j++){
                                $a = $a.'0';
                            }
                            $i = $a."".$i;
                            if(in_array($i,$winners,true)){
                                $itemlLength = strlen($winners[array_search($i,$winners,true)]);
                                $bonusWinner = self::luckPrice($order->item_id,$itemlLength);
                                $winningAmount = $winningAmount + $bonusWinner->price;
                                $totalBonus = $totalBonus + ($bonusWinner->bonus + $bonusWinner->stbonus);
                            }
                        }
                    }
                }else if($order->item_digits == 'S'){
                    $sDigit = substr($order->item_digits,$order->itemDigits-1);
                    for($i=$order->item_from;$i<=$order->item_to;$i=$i+10){
                        $itemQty = $itemQty + 1;
                        if(substr($i,$order->itemDigits-1) == $sDigit){
                            if(strlen($i) <= $order->itemDigits){
                                $a = "";
                                $compVal = $order->itemDigits - strlen($i);
                                for($j=0; $j < $compVal; $j++){
                                    $a = $a.'0';
                                }
                                $i = $a."".$i;
                                if(in_array(substr($i,$order->itemDigits-1),$winners,true)){
                                    $itemlLength = strlen($winners[array_search(substr($i,$order->itemDigits-1),$winners,true)]);
                                    $bonusWinner = self::luckPrice($order->item_id,$itemlLength);
                                    if(!empty($bonusWinner)){
                                        $winningAmount = $winningAmount + $bonusWinner->price;
                                        $totalBonus = $totalBonus + ($bonusWinner->bonus + $bonusWinner->stbonus);
                                    }
                                }
                            }
                        }
                    }
                }else if($order->item_digits == 'D'){
                    $sDigit = substr($order->item_from,$order->itemDigits-2);
                    for($i=$order->item_from;$i<=$order->item_to;$i=$i+100){
                        $itemQty = $itemQty + 1;
                        if(substr($i,$order->itemDigits-2) == $sDigit){
                            if(strlen($i) <= $order->itemDigits){
                                if(in_array(substr($i,$order->itemDigits-2),$winners,true)){
                                    $itemlLength = strlen($winners[array_search(substr($i,$order->itemDigits-2),$winners,true)]);
                                    $bonusWinner = self::luckPrice($order->item_id,$itemlLength);
                                    $winningAmount = $winningAmount + $bonusWinner->price;
                                    $totalBonus = $totalBonus + ($bonusWinner->bonus + $bonusWinner->stbonus);
                                }
                            }
                        }
                    }
                }else{
                    for($c=0;$c<10;$c++){
                        $compareValue[$c] = str_repeat($c,3);
                    }
                    for($i= $order->item_from;$i<=$order->item_to;$i=$i+111){
                        $itemQty = $itemQty + 1;
                        if(in_array(substr($i,$order->itemDigits-3),$compareValue)){
                            $itemQty = $itemQty + 1;
                            if(in_array(substr($i,$order->itemDigits-3),$winners,true)){
                                $itemlLength = strlen($winners[array_search(substr($i,$order->itemDigits-3),$winners,true)]);
                                $bonusWinner = self::luckPrice($order->item_id,$itemlLength);
                                $winningAmount = $winningAmount + $bonusWinner->price;
                                $totalBonus = $totalBonus + ($bonusWinner->bonus + $bonusWinner->stbonus);
                            }
                        }			
                    }
                }*/
                /*$total_qty = $total_qty + $itemQty;
                $total_amount = $total_amount + $itemQty * ($order->item_price + $order->extra_price);
                $total_bonus = $total_bonus + $totalBonus;
                $winning_total = $winning_total + $winningAmount;
                $result['items'][$key]['item_qty'] = $itemQty;
                $result['items'][$key]['bonus'] = $totalBonus;
                $result['items'][$key]['winning_amount'] = $winningAmount;*/
                $total_qty = $total_qty + $order->item_qty;
                $total_amount = $total_amount + $order->item_qty * ($order->item->item_price + $order->item->extra_price);
                $total_bonus = $total_bonus + $totalBonus*$order->item_qty;
                $winning_total = $winning_total + ($winningAmount*$order->item_qty);
                $itemName = $order->item->item_name;
                $result['items'][$key]['bonus'] = $totalBonus;
                $result['items'][$key]['winning_amount'] = $winningAmount*$order->item_qty;
                $result['items'][$key]['item_name'] = $order->item->item_name;
                $result['items'][$key]['item_qty'] = $order->item_qty;
                //$result['items'][$key]['item_number']=$order->item_number;
                $result['items'][$key]['extra_price'] = ($order->item->extra_price) ? $order->item->extra_price : 0;
                $result['items'][$key]['item_price'] = $order->item->item_price;
                $result['items'][$key]['item_total'] = ($order->item->item_price + $result['items'][$key]['extra_price']) * $order->item_qty;
                
            }
            $res  = array();
            foreach($result['items'] as $vals){
                if(array_key_exists($vals['item_name'],$res)){
                    $res[$vals['item_name']]['bonus'] += $vals['bonus'];
                    $res[$vals['item_name']]['winning_amount'] += $vals['winning_amount'];
                    $res[$vals['item_name']]['item_qty'] += $vals['item_qty'];
                    $res[$vals['item_name']]['extra_price'] += $vals['extra_price'];
                    $res[$vals['item_name']]['item_price'] += $vals['item_price'];
                    $res[$vals['item_name']]['item_total'] += $vals['item_total'];
                }
                else{
                    $res[$vals['item_name']]  = $vals;
                }
            }
            $result['items']=array_values($res);
            $result['total_qty'] = $total_qty;
            $result['total_amount'] = $total_amount;
            $result['total_bonus'] = $total_bonus;
            $result['winning_total'] = $winning_total;
        }
        
        if(!empty($orders)){
            $status = 1;
            $message = 'Result lists.';
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'resultsList' => $result
        ], 200);
    }

    public function getWinnerprice($resultTime,$startDate,$endDate,$oldWinners){
        $winningItems = $oldWinners;
        $winners = Winner::where('result_time', $resultTime)->whereBetween('created_at', [$startDate." 00:00:00",$endDate." 23:59:59"])->get();
        if(!empty($winners)){
            foreach($winners as $winner){
                foreach($winner->winning_item as $key=>$win){
                    if(!in_array($win['winning_item'],$winningItems,true)){
                    array_push($winningItems,strval($win['winning_item']));
                    }
                }
            }
        }
        return $winningItems;
    }

    public function luckPrice($itemId,$itemlLength){
        $luckPrice = Luckyprice::select('price','bonus','stbonus')->where('digit',$itemlLength)->where('item_id',$itemId)->first();
        return $luckPrice;
    }
}
?>