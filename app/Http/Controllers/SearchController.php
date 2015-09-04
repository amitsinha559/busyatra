<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Place;
use App\Http\Common\ValidateAndHandleError;
use App\Http\Common\ResponseCreator;
use DateTime;

class SearchController extends Controller {
    function __construct() {
        $this->response = new ResponseCreator;
        $this->validateAndHandleError = new ValidateAndHandleError();
    }
    
    public function getPlaces() 
    {
        $response = $this->response;
        $places = Place::all();
        return $response->successResponse(200, "All available places", $places);
    }
    
    public function getBusListByPlaceAndDate(Request $request) {
        $response = $this->response;
        $validateAndHandleError = $this->validateAndHandleError;
        
        if (!(NULL !== $request->route('doj')) || 
                !(NULL !== $request->route('dor')) || 
                !(NULL !== $request->route('departure_id')) ||                 
                !(NULL !== $request->route('departure_place')) ||                 
                !(NULL !== $request->route('destination_id')) || 
                !(NULL !== $request->route('destination'))){
            return $response->errorResponse('invalid_params');
        }
        $doj = $request->route('doj');
        $dor = $request->route('dor');
        $departure_id = $request->route('departure_id');
        $destination_id = $request->route('destination_id');
        $destination = $request->route('destination');
        $departure_place = $request->route('departure_place');
        
        /* validate number or not */
        $validateNumberOrNot = $validateAndHandleError->multiValidator([
            'from_place_mismatch' => $departure_id,
            'to_place_mismatch' => $destination_id,
                ], "number");
        if($validateNumberOrNot != "success") {
            return $validateNumberOrNot;
        }
        
        $dateValidator = $validateAndHandleError->multiValidator(['invalid_date'=>$doj], 'date');
        if ($dateValidator != "success"){
            return $dateValidator;
        }
        
        if ($dor !== '0') {
            $dateValidator = $validateAndHandleError->multiValidator(['invalid_date'=>$dor], 'date');
            if ($dateValidator != "success"){
                return $dateValidator;
            }
            $doj1 = new DateTime($doj);
            $dor1 = new DateTime($dor);
            if ($doj1 < $dor1){
                return $response->errorResponse('higher_dor');
            }
        }
        
        print_r(23);
    }
}
