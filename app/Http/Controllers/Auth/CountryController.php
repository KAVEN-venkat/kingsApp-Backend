<?php

namespace App\Http\Controllers\Auth;

use App\Country;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class CountryController extends Controller
{
    /**
     * Get the Countries
     *
     * @return [json] customer object
     */
    public function countries(Request $request)
    {
        $message = 'No Countries Found.';
        $status = 0;
        $countires = Country::get();
        if(!empty($countires)){
            $status = 1;
            $message = 'Country lists.';
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'countiresList' => $countires
        ], 200);
    }
    
}
