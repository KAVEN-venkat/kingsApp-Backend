<?php

namespace App\Http\Controllers\Auth;

use App\State;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class StateController extends Controller
{
    /**
     * Get the States
     *
     * @return [json] customer object
     */
    public function states(Request $request)
    {
        $message = 'No States Found.';
        $status = 0;
        $states = State::where('country_id',$request->countryId)->get();
        if(!empty($states)){
            $status = 1;
            $message = 'State lists.';
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'statesList' => $states
        ], 200);
    }
    
}
