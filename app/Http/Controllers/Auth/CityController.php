<?php

namespace App\Http\Controllers\Auth;

use App\City;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class CityController extends Controller
{
    /**
     * Get the Cities
     *
     * @return [json] customer object
     */
    public function cities(Request $request)
    {
        $message = 'No Cities Found.';
        $status = 0;
        $cities = City::where('state_id',$request->stateId)->get();
        if(!empty($cities)){
            $status = 1;
            $message = 'City lists.';
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'citiesList' => $cities
        ], 200);
    }
    
}
