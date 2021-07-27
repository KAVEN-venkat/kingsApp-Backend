<?php

namespace App\Http\Controllers\Auth;

use App\Winner;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class WinnerController extends Controller{

	public function index(Request $request){
        $user = auth()->user();
        $message = 'No Winners Found.';
        $status = 0;
        $winners = Winner::orderBy('created_at', 'DESC')->get();
        if(!empty($winners)){
            $status = 1;
            $message = 'Winner lists.';
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'winner' => $winners
        ], 200);
    }

	public function store(Request $request){
		$user = auth()->user();
        $message = 'No Winner.';
        $status = 0;
        $result_time = Carbon::parse($request->result_time);
        $winner=Winner::whereDate('winner_date', date("Y-m-d",strtotime($request->winner_date)))->where('result_time',$result_time->format('H:i'))->first();
        if($winner==null){
		$winner = new Winner;
        }
        $winner->winner_date = date("Y-m-d",strtotime($request->winner_date));
		$winner->result_time = $result_time->format('H:i');
		$winner->winner_a = $request->winner_a;
        $winner->winner_b = $request->winner_b;
        $winner->winner_c = $request->winner_c;
        $winner->winner_d = $request->winner_d;
        $winner->winner_e = $request->winner_e;
		$winner->save();
		if(!empty($winner)){
		$status = 1;
		$message = 'Winner Created.';
		}
		return response()->json([
            'status' => $status,
            'message' => $message,
            'winner' => $winner
        ], 200);
	}
}