<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\BusOwner;
use App\User;
use App\Bus;
use App\Place;
use App\BusDeparturePoint;
use App\BusDroppingPoint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Http\Common\ResponseCreator;
use App\Http\Common\ValidateAndHandleError;

class BusController extends Controller {
    
        function __construct() {
            $this->response = new ResponseCreator;
            $this->validateAndHandleError = new ValidateAndHandleError();
        }

    
        /**
         * Method is used to add bus owner details
         * One user/owner have ony one details in db
         * URL: api/v1.0/add/bus/owner
         * payload:
        {
                "params": {
                        "name_of_the_travels": "Swapna",
                        "owner_name": "Trivedi",
                        "phone_number_one": 9987899879,
                        "phone_number_two": 838383833,
                        "street": "BOC Para",
                        "city": "Dharmanagar",
                        "pincode": "799250",
                        "state": "Tripura",
                        "country": "India"
                }
        }
         */
        public function addBusOwner(){
                $response = $this->response;
                $data = Input::json()->all();
                $validateAndHandleError = new ValidateAndHandleError;
                
                if(!Auth::user()->id){
                    return $validateAndHandleError->invalidUser();
                }
                $user_id = Auth::user()->id;
                $is_unique_bus_owner = BusOwner::where('user_id', $user_id)->get();
                if (count($is_unique_bus_owner) > 0) {
                    return $response->errorResponse('bus_owner_exist');
//                    return $validateAndHandleError->errorIdentifier("bus_owner_exist");
                }
                
                if(!isset($data) || !isset($data['params']) ||
                        !isset($data['params']['name_of_the_travels']) || 
                        !isset($data['params']['owner_name']) ||
                        !isset($data['params']['phone_number_one']) ||
                        !isset($data['params']['street']) ||
                        !isset($data['params']['city']) ||
                        !isset($data['params']['pincode']) ||
                        !isset($data['params']['state']) ||
                        !isset($data['params']['country'])) {
                    return $response->errorResponse('invalid_params');
//                   return $validateAndHandleError->errorIdentifier("invalid_params");
                }
                
                $name_of_the_travels = $data['params']['name_of_the_travels'];
                $owner_name = $data['params']['owner_name'];
                $phone_number_one = $data['params']['phone_number_one'];
                $street = $data['params']['street'];
                $city = $data['params']['city'];
                $pincode = $data['params']['pincode'];
                $state = $data['params']['state'];
                $country = $data['params']['country'];
                
                $phone_number_two = "";
                if (isset($data['params']['phone_number_two'])) {
                    $phone_number_two = $data['params']['phone_number_two'];
                }
                
                /* Null validation */
                $validateNullData = array(
                    'user_id' => $user_id,
                    'name_of_the_travels' => $name_of_the_travels,
                    'owner_name' => $owner_name,
                    'phone_number_one' => $phone_number_one,
                    'street' => $street,
                    'city' => $city,
                    'pincode' => $pincode,
                    'state' => $state,
                    'country' => $country
                );   
                
                
                $nullValidator = $validateAndHandleError->multiValidator($validateNullData, "string");
                if($nullValidator != "success") {
                    return $nullValidator;
                }
                
                /* Phone number validation */
                $phoneNumberValidator = $validateAndHandleError->multiValidator(['phone_number_one' => $phone_number_one], "phone_number");
                if ($phoneNumberValidator != "success") {
                    return ($phoneNumberValidator);
                }
                
                /* If phone_number_two is exist then only validate it */
                if ($phone_number_two) {
                    $phoneNumberTwoValidator = $validateAndHandleError->multiValidator(['phone_number_one' => $phone_number_two], "phone_number");
                    if ($phoneNumberTwoValidator != "success") {
                        return ($phoneNumberTwoValidator);
                    }
                }
                
                /* Validating pincode */
                $pincodeValidator = $validateAndHandleError->multiValidator(['pincode' => $pincode], "pincode");
                if ($pincodeValidator != "success") {
                    return ($pincodeValidator);
                }
                
                $bus_owner = new BusOwner;
                $bus_owner->user_id = $user_id;
                $bus_owner->name_of_the_travels = $name_of_the_travels;
                $bus_owner->owners_name = $owner_name;
                $bus_owner->phone_number_one = $phone_number_one;
                $bus_owner->phone_number_two = $phone_number_two;
                $bus_owner->street = $street;
                $bus_owner->city = $city;
                $bus_owner->pincode = $pincode;
                $bus_owner->state = $state;
                $bus_owner->country = $country;
                
                
                if ($bus_owner->save()) {
                    return $response->successResponse(200, "Bus owner details has been saved successfully");
                }
               
                return $response->errorResponse("data_not_saved");
        }
        /**
         * Returns
         * 
         */
        
        
        /**
         * Used to add bus details
         * URL: api/v1.0/add/bus/details
        {
                "params": {
                        "bus_name": "Alqueda",
                        "bus_from": "Guwahati",
                        "from_place_id": "33",
                        "bus_to": "Dharmanagar",
                        "to_place_id": "11",
                        "bus_type": "Hollow",
                        "bus_comfort": "Sleeepr",
                        "bus_seat_image_location": "/here/asd.jpg",
                        "bus_total_seats": 40,
                        "travelling_days": "Sunday, Monday, Saturday",
                        "online_booking_seats": "1,2,3",
                        "is_full_bus": "false"
                }
        }
         */
        public function addBusDetails(){
                $response = $this->response;
                $data = Input::json()->all();
                $validateAndHandleError = new ValidateAndHandleError;
                if(!Auth::user()->id){
                    return $validateAndHandleError->invalidUser();
                }
                $user_id = Auth::user()->id;
                $user = User::with('busOwner')->find($user_id);
                $bus_owner_id = $user->toArray()['bus_owner']['id'];
                $bus = new Bus;
                
                if(!isset($data) || !isset($data['params']) ||
                        !isset($data['params']['bus_name']) || 
                        !isset($data['params']['bus_from']) ||
                        !isset($data['params']['from_place_id']) ||
                        !isset($data['params']['bus_to']) ||
                        !isset($data['params']['to_place_id']) ||
                        !isset($data['params']['bus_type']) ||
                        !isset($data['params']['bus_comfort']) ||
                        !isset($data['params']['bus_seat_image_location']) ||
                        !isset($data['params']['bus_total_seats']) ||
                        !isset($data['params']['travelling_days']) ||
                        !isset($data['params']['is_full_bus'])) {
                    return $response->errorResponse('invalid_params');
//                    return $validateAndHandleError->errorIdentifier("invalid_params");
                }
                
                $online_booking_seats = "";
                if (isset($data['params']['online_booking_seats'])) {
                    $online_booking_seats = $data['params']['online_booking_seats'];
                }
                
                $bus_name = $data['params']['bus_name'];
                $bus_from = $data['params']['bus_from'];
                $from_place_id = $data['params']['from_place_id'];
                $bus_to = $data['params']['bus_to'];
                $to_place_id = $data['params']['to_place_id'];
                $bus_type = $data['params']['bus_type'];
                $bus_comfort = $data['params']['bus_comfort'];
                $bus_seat_image_location = $data['params']['bus_seat_image_location'];
                $bus_total_seats = $data['params']['bus_total_seats'];
                $is_full_bus = $data['params']['is_full_bus'];
                $travelling_days = $data['params']['travelling_days'];
                
                
                /* Bus null validation */
                $validateNullData = array(
                    'bus_owner_id' => $bus_owner_id,
                    'bus_name' => $bus_name,
                    'bus_from' => $bus_from,
                    'from_place_id' => $from_place_id,
                    'bus_to' => $bus_to,
                    'to_place_id' => $to_place_id,
                    'bus_type' => $bus_type,
                    'bus_comfort' => $bus_comfort,
                    'bus_seat_image_location' => $bus_seat_image_location,
                    'bus_total_seats' => $bus_total_seats,
                    'is_full_bus' => $is_full_bus,
                    'travelling_days' => $travelling_days
                );                
                $nullValidator = $validateAndHandleError->multiValidator($validateNullData, "string");
                if($nullValidator != "success") {
                    return $nullValidator;
                }                
                
                /* if is_full_bus is false then only online_booking_seats will exist */
                if (!$is_full_bus && !$online_booking_seats) {
                    return $response->errorResponse('online_booking_seats');
//                    return $validateAndHandleError->errorIdentifier("online_booking_seats");
                }
                
                /* Validating seat number is numeric or not */
                $validateSeats = $validateAndHandleError->multiValidator(['bus_total_seats' => $bus_total_seats], "seat_number");
                
                if($validateSeats != "success") {
                    return $validateSeats;
                }
                
                $validatePlaceId = $validateAndHandleError->multiValidator([
                    'from_place_id' => $from_place_id,
                    'to_place_id' => $to_place_id], "number");
                
                if($validatePlaceId != "success") {
                    return $validatePlaceId;
                }
                
                $validateDestinationAndArrival = $validateAndHandleError->arrivalAndDestinationVerifier($to_place_id, $from_place_id, $bus_from, $bus_to);
                if ($validateDestinationAndArrival !== TRUE){
                    return $validateDestinationAndArrival;
                }

                $bus->bus_owners_id = $bus_owner_id;
                $bus->bus_name = $bus_name;
                $bus->bus_from = $bus_from;
                $bus->from_place_id = $from_place_id;
                $bus->bus_to = $bus_to;
                $bus->to_place_id = $to_place_id;
                $bus->bus_type = $bus_type;
                $bus->bus_comfort = $bus_comfort;
                $bus->bus_seat_image_location = $bus_seat_image_location;
                $bus->bus_total_seats = $bus_total_seats;
                $bus->online_booking_seats = $online_booking_seats;
                $bus->is_full_bus = $is_full_bus;
                $bus->travelling_days = $travelling_days;
                

//                $response = new ResponseCreator;
                if ($bus->save()) {
                    $bus_id = Bus::where('bus_owners_id', $bus_owner_id)
                        ->where('bus_name', $bus_name)
                        ->where('bus_from', $bus_from)
                        ->where('from_place_id', $from_place_id)
                        ->where('bus_to', $bus_to)
                        ->where('to_place_id', $to_place_id)
                        ->where('bus_type', $bus_type)
                        ->where('bus_comfort', $bus_comfort)
                        ->where('bus_seat_image_location', $bus_seat_image_location)
                        ->where('bus_total_seats', $bus_total_seats)
                        ->where('travelling_days', $travelling_days)
                        ->where('is_full_bus', $is_full_bus)
                        ->get();
                    $info = array (
                        "bus_id" => $bus_id[0]->id,
                        "bus_owners_id" => $bus_id[0]->bus_owners_id
                    );
                    return $response->successResponse(200, "Bus details has been saved successfully", $info);
                }
                return $response->errorResponse("data_not_saved");
        }
        /**
         * returns:
         */
        
        
        /**
         * Used to add dropping details
         * URL: api/v1.0/add/bus/destination
         * payload:
        {
                "params": {
                        "buses_id": "44",
                        "bus_departure_points_id": "9",
                        "dropping_point": "Dewmalis",
                        "dropping_time": "93pm",
                        "price": "123"
                }
        }
         */
        public function addDroppingPointDetails() {
            $response = $this->response;
            $data = Input::json()->all();
            $validateAndHandleError = new ValidateAndHandleError;
            if(!Auth::user()->id){
                return $validateAndHandleError->invalidUser();
            }
            
            if(!isset($data) || !isset($data['params']) ||
                        !isset($data['params']['bus_departure_points_id']) || 
                        !isset($data['params']['dropping_point']) ||
                        !isset($data['params']['place_id']) ||
                        !isset($data['params']['dropping_time']) ||
                        !isset($data['params']['price'])) {
                    return $response->errorResponse('invalid_params');
//                    return $validateAndHandleError->errorIdentifier("invalid_params");
                }
                
//            if(!isset($data) || !isset($data['params']) ||
//                        !isset($data['params']['bus_departure_points_id']) || 
//                        !isset($data['params']['dropping_point']) ||
//                        !isset($data['params']['dropping_time']) ||
//                        !isset($data['params']['price']) ||
//                        !isset($data['params']['extra_price_one']) ||
//                        !isset($data['params']['extra_price_two']) ||
//                        !isset($data['params']['reduce_price']) ||
//                        !isset($data['params']['percentage_increament_price']) ||
//                        !isset($data['params']['percentage_reduction_price'])) {
//                    return $validateAndHandleError->errorIdentifier("invalid_params");
//                }
                
            
            
            $buses_id = $data['params']['buses_id'];
            $bus_departure_points_id = $data['params']['bus_departure_points_id'];
            $dropping_point = $data['params']['dropping_point'];
            $place_id = $data['params']['place_id'];
            $dropping_time = $data['params']['dropping_time'];
            $price = $data['params']['price'];
            /* Only Super admin can update the bellow details (offer related details) */
//            $extra_price_one = $data['params']['extra_price_one'] = 0;
//            $extra_price_two = $data['params']['extra_price_two'] = 0;
//            $reduce_price = $data['params']['reduce_price'] = 0;
//            $percentage_increament_price = $data['params']['percentage_increament_price'] = 0;
//            $percentage_reduction_price = $data['params']['percentage_reduction_price'] = 0;
            
            /* For null validation */
            $stringValidator = array(
                    'dropping_point' => $dropping_point,
                    'place_id' => $place_id,
                    'dropping_time' => $dropping_time,
                    'buses_id' => $buses_id,
                    'bus_departure_points_id' => $bus_departure_points_id,
                    'price' => $price
                );
            $nullValidator = $validateAndHandleError->multiValidator($stringValidator, "string");
            if($nullValidator != "success") {
                return $nullValidator;
            }
            
            /* For Numeric validation */
            $numericValidator = array(
                    'buses_id' => $buses_id,
                    'bus_departure_points_id' => $bus_departure_points_id,
                    'price' => $price,
                    'place_id' => $place_id
                );
            $is_number = $validateAndHandleError->multiValidator($numericValidator, "number");
            if($is_number != "success") {
                return $is_number;
            }
            
            $place = Place::where('id', $place_id)->get()[0]->place;

            if ($place != $dropping_point) {
                return $response->errorResponse('to_place_mismatch');
//                return $validateAndHandleError->errorIdentifier("to_place_mismatch");
            }
            
            /* Checking whether the bus id is really available or not */
            $buses = Bus::where('id', $buses_id)->get();
            if (count($buses) <= 0) {
                return $response->errorResponse('bus_does_not_exist');
//                return $validateAndHandleError->errorIdentifier("bus_does_not_exist");
            }            
            
            /* Checking the departure details are really available or not */
            $departure_id = BusDeparturePoint::where('id', $bus_departure_points_id)->get();
            if (count($departure_id) <= 0) {
                return $response->errorResponse('bus_departure_details_does_not_exist');
//                return $validateAndHandleError->errorIdentifier("bus_departure_details_does_not_exist");
            }
            
            /* For these departure details bus is exist or not */
            foreach ($departure_id as $value) {
                if($value->buses_id != $buses_id){
                    return $response->errorResponse('bus_does_not_exist_for_departure_details');
//                    return $validateAndHandleError->errorIdentifier("bus_does_not_exist_for_departure_details");
                }
            }
            
            /* validating whether dropping point or dropping time is exist for same bus or not. Should not be same */
            $dropping_details = BusDroppingPoint::where('buses_id', $buses_id)->get();
            foreach ($dropping_details as $value) {
                if($value->dropping_point == $dropping_point) {
                    return $response->errorResponse('same_dropping_point');
//                    return $validateAndHandleError->errorIdentifier("same_dropping_point");
                }                
                if($value->dropping_time == $dropping_time) {
                    return $response->errorResponse('same_dropping_time');
//                    return $validateAndHandleError->errorIdentifier("same_dropping_time");
                }                
            }
            
            $bus_dropping_point_details = new BusDroppingPoint;
            $bus_dropping_point_details->buses_id = $buses_id;
            $bus_dropping_point_details->bus_departure_points_id = $bus_departure_points_id;
            $bus_dropping_point_details->dropping_point = $dropping_point;
            $bus_dropping_point_details->place_id = $place_id;
            $bus_dropping_point_details->dropping_time = $dropping_time;
            $bus_dropping_point_details->price = $price;
//            $bus_dropping_point_details->extra_price_one = $extra_price_one;
//            $bus_dropping_point_details->extra_price_two = $extra_price_two;
//            $bus_dropping_point_details->reduce_price = $reduce_price;
//            $bus_dropping_point_details->percentage_increament_price = $percentage_increament_price;
//            $bus_dropping_point_details->percentage_reduction_price = $percentage_reduction_price;
            
            
//            $response = new ResponseCreator;
            if ($bus_dropping_point_details->save()) {
                $info = array(
                    "buses_id" => $buses_id,
                    "bus_departure_points_id" => $bus_departure_points_id,
                    "dropping_point" => $dropping_point,
                    "place_id" => $place_id,
                    "dropping_time" => $dropping_time,
                    "price" => $price
                );
                return $response->successResponse(200, "Dropping details has been added successfully", $info);
            }
            return $response->errorResponse("data_not_saved");
        }
        /**
         * Returns:
        {
            "error": false,
            "code": 200,
            "message": "Dropping details has been added successfully",
            "info": {
                "buses_id": "44",
                "bus_departure_points_id": "9",
                "dropping_point": "Dewmalis",
                "dropping_time": "93pm",
                "price": "123"
            }
        }
         */
        
        
        /**
         * Used to add departure details
         * URL: /api/v1.0/add/bus/departure
         * payloads
        {
               "params": {
                       "buses_id": 27,
                       "departure_from": "Paltan Bazar",
                       "departure_time": "9:20pm"
               }
        }
         */
        public function addDepartureDetails() {
            $response = $this->response;
            $data = Input::json()->all();
            $validateAndHandleError = new ValidateAndHandleError;
            if(!Auth::user()->id){
                return $validateAndHandleError->invalidUser();
            }
            
            if(!isset($data) || !isset($data['params']) ||
                        !isset($data['params']['departure_from']) || 
                        !isset($data['params']['departure_time'])) {
                    return $response->errorResponse('invalid_params');
//                    return $validateAndHandleError->errorIdentifier("invalid_params");
                }
            $buses_id = $data['params']['buses_id'];
            $departure_from = $data['params']['departure_from'];
            $departure_time = $data['params']['departure_time'];
            
            /* Null validation */
            $validateNullData = array(
                "buses_id" => $buses_id,
                "departure_from" => $departure_from,
                "departure_time" => $departure_time
            );
            
            $nullValidator = $validateAndHandleError->multiValidator($validateNullData, "string");
            if($nullValidator != "success") {
                return $nullValidator;
            }
            
            /* Bus id is number or not */
            $validateBusId = $validateAndHandleError->multiValidator(['buses_id' => $buses_id], "number");
            if($validateBusId != "success") {
                return $validateBusId;
            }
            
            /* Bus id exist or not */
            $buses = Bus::where('id', $buses_id)->get();
            if (count($buses) <= 0) {
                return $response->errorResponse('bus_does_not_exist');
//                return $validateAndHandleError->errorIdentifier("bus_does_not_exist");
            }
            
            /* vaidate departure from and time is exist for same bus or not */
            $bus_departure_details = BusDeparturePoint::where('buses_id', $buses_id)->get();
            foreach ($bus_departure_details as $value) {
                if ($value->departure_from == $departure_from && $value->buses_id == $buses_id) {
                    return $response->errorResponse('departure_from_exist');
//                    return $validateAndHandleError->errorIdentifier("departure_from_exist");
                }                
                if ($value->departure_time == $departure_time && $value->buses_id == $buses_id) {
                    return $response->errorResponse('departure_time_exist');
//                    return $validateAndHandleError->errorIdentifier("departure_time_exist");
                }
            }
            
            $bus_departure_point = new BusDeparturePoint();
            $bus_departure_point->buses_id = $buses_id;
            $bus_departure_point->departure_from = $departure_from;
            $bus_departure_point->departure_time = $departure_time;
            
           
//            $response = new ResponseCreator;
            if ($bus_departure_point->save()) {
                $bus_departure_point_id = BusDeparturePoint::where('buses_id', $buses_id)
                    ->where('departure_from', $departure_from)
                    ->where('departure_time', $departure_time)
                    ->get();
                $info = array(
                    "buses_id" => $buses_id,
                    "bus_departure_point_id" => $bus_departure_point_id[0]->id
                );
                return $response->successResponse(200, "Departure details has been added successfully", $info);
            }
            return $response->errorResponse("data_not_saved");
        }
        /**
         * Returns:
        {
            "error": false,
            "code": 200,
            "message": "Departure details has been added successfully",
            "info": {
                "buses_id": 44,
                "bus_departure_point_id": 9
            }
        }
         */
        
        
        /**
         * Used to get bus owner details + bus details
         * Only for admin and bus owners
         */
        public function getBusOwnerDetails(Request $request) {
            $response = $this->response;
            $validateAndHandleError = new ValidateAndHandleError();
            if (!$request->route('id')){
                return $response->errorResponse("id_not_exist");
            }
            
            $id_from_url = $request->route('id'); 
            $check_user_id_numeric = $validateAndHandleError->multiValidator(['should_be_numeric_value' => $id_from_url], "number");
            if ($check_user_id_numeric != "success"){
                return $check_user_id_numeric;
            }            
                       
            if(!Auth::user()->id){
                return $validateAndHandleError->invalidUser();
            }
            $user_id = Auth::user()->id;            
            $user_type = $validateAndHandleError->userIdentifier($id_from_url);
            
            /* Checking whether id is exist or not in users table */
            $user_id_check = User::where('id', $id_from_url)->get();
            if (count($user_id_check) <= 0 ){
                return $response->errorResponse("user_not_exist");
            }
            
            /* Only bus owner and admin can access get this api. */
            if (($user_type == "bus_owner" && $user_id == $id_from_url) || $user_type == "admin") {
                $user = User::with('busOwner')->find($id_from_url);
                $busOwner = $user->busOwner->bus;
                return $response->successResponse(200, "Bus owner details with bus details", $user);
            }
            return $response->errorResponse("user_not_accessible");
        }
        
        
        /**
         * This method returns all the bus owner details with their bus details
         */
        public function getBusOwnersDetails() {
            $response = $this->response;
            $validateAndHandleError = new ValidateAndHandleError();
            if(!Auth::user()->id){
                return $validateAndHandleError->invalidUser();
            }
            $user_id = Auth::user()->id;
            $user_type = $validateAndHandleError->userIdentifier($user_id);
            $user_type = "admin"; // delete it later
            if ($user_type != "admin") {
                return $response->errorResponse("user_not_accessible");
            }
            $user = User::all();
            if (count($user) <=0 ) {
                return $response->errorResponse("user_not_exist");
            }            
            
            /* Bus details for the buss owners */
            foreach ($user as $key => $value) {
                if ($value->user_type == "b") {
                    $value->busOwner->bus;
                    $bus_owner_details[] = array (
                        "details" => $value
                    );
                }
            }
            if (!isset($bus_owner_details)) {
                return $response->errorResponse("bus_details_not_available");
            }
            return $response->successResponse(200, "All the bus owners details with their bus details", $bus_owner_details);
        }

        
        /**
         * This method is used to get departure details of buses
         */
        public function getDepartureDetails(Request $request) {
            $response = $this->response;
            $validateAndHandleError = new ValidateAndHandleError();
            if(!Auth::user()->id){
                return $validateAndHandleError->invalidUser();
            }
            $user_id = Auth::user()->id;
            $user_type = $validateAndHandleError->userIdentifier($user_id);
//            $user_type = "customer"; // delete it later
            /* details are only accessible by admin and bus owner */
            if ($user_type == "customer" || $user_type == "dealer" || $user_type == "unknown") {
                return $response->errorResponse("details_not_accessible");
            }
            
            if (!$request->route('id')){
                return $response->errorResponse("id_not_exist");
            }
            /* url id should be a numeric value */
            $bus_id = $request->route('id');
            $check_bus_id_numeric = $validateAndHandleError->multiValidator(['should_be_numeric_value' => $bus_id], "number");
            if ($check_bus_id_numeric != "success"){
                return $check_bus_id_numeric;
            }

            /* getting bus owner id by bus id */
            $bus_owner_details = Bus::where('id', $bus_id)->get();
            if (count($bus_owner_details) <= 0){
                return $response->errorResponse("bus_details_not_available");
            }
            $bus_owners_id = $bus_owner_details[0]->bus_owners_id;
            
            /* gettting bus owners user id by bus owner id */
            $user_profile = BusOwner::where('id', $bus_owners_id)->get();
            if (count($user_profile) <= 0){
                return $response->errorResponse("bus_owner_details_not_available");
            }
            $user_id_temp = $user_profile[0]->user_id;
            
            /* if buw owner then bus_id given via url should match with him */
            if ( $user_type == "bus_owner" && ($user_id_temp != $user_id)) {
                return $response->errorResponse("details_not_accessible");
            }
            
            $departure_details = BusDeparturePoint::where('buses_id', $bus_id)->get();
            if (count($departure_details) <= 0){
                return $response->errorResponse("departure_details_not_available");
            }
            return $response->successResponse(200, "Departure details of Bus id: " . $bus_id, $departure_details);
        }
        
        
        /**
         * This method is used to get dropping details of buses
         */
        public function getDroppingDetails (Request $request) {
            $response = $this->response;
            $validateAndHandleError = new ValidateAndHandleError();
            if(!Auth::user()->id){
                return $validateAndHandleError->invalidUser();
            }
            $user_id = Auth::user()->id;
            $user_type = $validateAndHandleError->userIdentifier($user_id);
            /* details are only accessible by admin and bus owner */
            if ($user_type == "customer" || $user_type == "dealer" || $user_type == "unknown") {
                return $response->errorResponse("details_not_accessible");
            }
            
            if (!$request->route('id')){
                return $response->errorResponse("id_not_exist");
            }
            /* url id should be a numeric value */
            $bus_id = $request->route('id');
            $check_bus_id_numeric = $validateAndHandleError->multiValidator(['should_be_numeric_value' => $bus_id], "number");
            if ($check_bus_id_numeric != "success"){
                return $check_bus_id_numeric;
            }

            /* getting bus owner id by bus id */
            $bus_owner_details = Bus::where('id', $bus_id)->get();
            if (count($bus_owner_details) <= 0){
                return $response->errorResponse("bus_details_not_available");
            }
            $bus_owners_id = $bus_owner_details[0]->bus_owners_id;
            
            /* gettting bus owners user id by bus owner id */
            $user_profile = BusOwner::where('id', $bus_owners_id)->get();
            if (count($user_profile) <= 0){
                return $response->errorResponse("bus_owner_details_not_available");
            }
            $user_id_temp = $user_profile[0]->user_id;
            
            /* if bus owner then bus_id given via url should match with him */
            if ( $user_type == "bus_owner" && ($user_id_temp != $user_id)) {
                return $response->errorResponse("details_not_accessible");
            }
            
            $dropping_details = BusDroppingPoint::where('buses_id', $bus_id)->get();
            if (count($dropping_details) <= 0){
                return $response->errorResponse("dropping_details_not_available");
            }
            return $response->successResponse(200, "Dropping details of Bus id: " . $bus_id, $dropping_details);
        }
        
        /**
         * This method is used to update the bu owner details
         * URL: /public/api/v1.0/update/bus/owner/{user_id}
         * payloads:
            {
                    "params": {
                            "name_of_the_travels": "Dilwale",
                            "owner_name": "Sharru",
                            "phone_number_one": 99938355,
                            "phone_number_two": 838383833,
                            "street": "BOC",
                            "city": "Dharmagar",
                            "pincode": "797250",
                            "state": "Tripra",
                            "country": "India"
                    }
            }
         * @param Request $request
         */
        public function updateBusOwner(Request $request) {
            $response = $this->response;
            $validateAndHandleError = new ValidateAndHandleError();
            if(!Auth::user()->id){
                return $validateAndHandleError->invalidUser();
            }
            $user_id = Auth::user()->id;
            $user_type = $validateAndHandleError->userIdentifier($user_id);            
            //$user_type = 'admin'; //delete it later
            
            if (!$request->route('owner_id')){
                return $response->errorResponse("id_not_exist");
            }
            /* url owner_id should be a numeric value */
            $owner_id = $request->route('owner_id');
            $check_bus_id_numeric = $validateAndHandleError->multiValidator(['should_be_numeric_value' => $owner_id], "number");
            if ($check_bus_id_numeric != "success"){
                return $check_bus_id_numeric;
            }
            
            $check_owners_availability = BusOwner::where('user_id', $owner_id)->get();
            if (count($check_owners_availability) <= 0) {
                return $response->errorResponse("bus_owner_details_not_available");
            }
            
            /* details are only accessible by admin and bus owner */
            if ($user_type == "customer" || $user_type == "dealer" || $user_type == "unknown" || 
                    ($user_type == "bus_owner" && $owner_id != $user_id)) {
                return $response->errorResponse("details_not_accessible");
            }
            
            $data = Input::json()->all();
            if(!isset($data) || !isset($data['params']) ||
                        !isset($data['params']['name_of_the_travels']) || 
                        !isset($data['params']['owner_name']) ||
                        !isset($data['params']['phone_number_one']) ||
                        !isset($data['params']['street']) ||
                        !isset($data['params']['city']) ||
                        !isset($data['params']['pincode']) ||
                        !isset($data['params']['state']) ||
                        !isset($data['params']['country'])) {
                    return $response->errorResponse('invalid_params');
//                    return $validateAndHandleError->errorIdentifier("invalid_params");
                }
                
                $name_of_the_travels = $data['params']['name_of_the_travels'];
                $owner_name = $data['params']['owner_name'];
                $phone_number_one = $data['params']['phone_number_one'];
                $street = $data['params']['street'];
                $city = $data['params']['city'];
                $pincode = $data['params']['pincode'];
                $state = $data['params']['state'];
                $country = $data['params']['country'];
                
                $phone_number_two = "";
                if (isset($data['params']['phone_number_two'])) {
                    $phone_number_two = $data['params']['phone_number_two'];
                }
                
                /* Null validation */
                $validateNullData = array(
                    'user_id' => $user_id,
                    'name_of_the_travels' => $name_of_the_travels,
                    'owner_name' => $owner_name,
                    'phone_number_one' => $phone_number_one,
                    'street' => $street,
                    'city' => $city,
                    'pincode' => $pincode,
                    'state' => $state,
                    'country' => $country
                );   
                
                
                $nullValidator = $validateAndHandleError->multiValidator($validateNullData, "string");
                if($nullValidator != "success") {
                    return $nullValidator;
                }
                
                /* Phone number validation */
                $phoneNumberValidator = $validateAndHandleError->multiValidator(['phone_number_one' => $phone_number_one], "phone_number");
                if ($phoneNumberValidator != "success") {
                    return ($phoneNumberValidator);
                }
                
                /* If phone_number_two is exist then only validate it */
                if ($phone_number_two) {
                    $phoneNumberTwoValidator = $validateAndHandleError->multiValidator(['phone_number_one' => $phone_number_two], "phone_number");
                    if ($phoneNumberTwoValidator != "success") {
                        return ($phoneNumberTwoValidator);
                    }
                }
                
                /* Validating pincode */
                $pincodeValidator = $validateAndHandleError->multiValidator(['pincode' => $pincode], "pincode");
                if ($pincodeValidator != "success") {
                    return ($pincodeValidator);
                }
                
                $is_bus_owner_updated = BusOwner::where('user_id', $owner_id)
                        ->update(['name_of_the_travels' => $name_of_the_travels,
                            'owners_name' => $owner_name,
                            'phone_number_one' => $phone_number_one,
                            'phone_number_two' => $phone_number_two,
                            'street' => $street,
                            'city' => $city,
                            'pincode' => $pincode,
                            'state' => $state,
                            'country' => $country
                        ]);
                
                $info = array (
                  "user_id" => $user_id
                );
                
                if ($is_bus_owner_updated == 1) {
                    return $response->successResponse(200, "Bus owner details updated successully", $info);
                }
                return $response->errorResponse("update_unsuccessfull");
        }
        /**
         * Returns
            {
                "error": false,
                "code": 200,
                "message": "Bus owner details updated successully",
                "info": {
                    "user_id": 1
                }
            }
         */
        
        /**
         * Used to update bus details
         * URL: /public/api/v1.0/update/bus/details/{bus_id}
        {
            "params": {
              "bus_name": "Tarapop",
              "bus_from": "Aachara",
              "from_place_id" : 2,
              "bus_to": "Aahor",
              "to_place_id": 5,
              "bus_type": "Hollow",
              "bus_comfort": "Sleeepr",
              "bus_seat_image_location": "/here/asd.jpg",
              "bus_total_seats": "44",
              "online_booking_seats": "",
              "is_full_bus": true,
                  "travelling_days": "sunday, monday"
            }
          }
         */
        public function updateBusDetails (Request $request) {
            $response = $this->response;
            $validateAndHandleError = new ValidateAndHandleError();
            if(!Auth::user()->id){
                return $validateAndHandleError->invalidUser();
            }
            $user_id = Auth::user()->id;
            $user_type = $validateAndHandleError->userIdentifier($user_id);            
            //$user_type = 'admin'; //delete it later
            
            if (!$request->route('bus_id')){
                return $response->errorResponse("id_not_exist");
            }
            /* url bus_id should be a numeric value */
            $bus_id = $request->route('bus_id');
            $check_bus_id_numeric = $validateAndHandleError->multiValidator(['should_be_numeric_value' => $bus_id], "number");
            if ($check_bus_id_numeric != "success"){
                return $check_bus_id_numeric;
            }
            
            $check_bus_availability = Bus::where('id', $bus_id)->get();
            if (count($check_bus_availability) <= 0) {
                return $response->errorResponse("bus_details_not_available");
            }
            
            $bus_owners_id = $check_bus_availability[0]->bus_owners_id;
            /* $user_id_temp is used to check whether the details are accessible or not */
            $user_id_temp = BusOwner::where('id', $bus_owners_id)->get()[0]->user_id;            
            
            /* details are only accessible by admin and bus owner */
            if ($user_type == "customer" || $user_type == "dealer" || $user_type == "unknown" || 
                    ($user_type == "bus_owner" && $user_id_temp != $user_id)) {
                return $response->errorResponse("details_not_accessible");
            }
            
            $data = Input::json()->all();
            if(!isset($data) || !isset($data['params']) ||
                    !isset($data['params']['bus_name']) || 
                    !isset($data['params']['bus_from']) ||
                    !isset($data['params']['from_place_id']) ||
                    !isset($data['params']['bus_to']) ||
                    !isset($data['params']['to_place_id']) ||
                    !isset($data['params']['bus_type']) ||
                    !isset($data['params']['bus_comfort']) ||
                    !isset($data['params']['bus_seat_image_location']) ||
                    !isset($data['params']['bus_total_seats']) ||
                    !isset($data['params']['travelling_days']) ||
                    !isset($data['params']['is_full_bus'])) {
                return $response->errorResponse('invalid_params');
//                return $validateAndHandleError->errorIdentifier("invalid_params");
            }

            $online_booking_seats = "";
            if (!$data['params']['is_full_bus'] && isset($data['params']['online_booking_seats'])) {
                $online_booking_seats = $data['params']['online_booking_seats'];
            }

            $bus_name = $data['params']['bus_name'];
            $bus_from = $data['params']['bus_from'];
            $from_place_id = $data['params']['from_place_id'];
            $bus_to = $data['params']['bus_to'];
            $to_place_id = $data['params']['to_place_id'];
            $bus_type = $data['params']['bus_type'];
            $bus_comfort = $data['params']['bus_comfort'];
            $bus_seat_image_location = $data['params']['bus_seat_image_location'];
            $bus_total_seats = $data['params']['bus_total_seats'];
            $is_full_bus = $data['params']['is_full_bus'];
            $travelling_days = $data['params']['travelling_days'];


            /* Bus null validation */
            $validateNullData = array(
                'bus_owner_id' => $bus_owners_id,
                'bus_name' => $bus_name,
                'bus_from' => $bus_from,
                'from_place_id' => $from_place_id,
                'bus_to' => $bus_to,
                'to_place_id' => $to_place_id,
                'bus_type' => $bus_type,
                'bus_comfort' => $bus_comfort,
                'bus_seat_image_location' => $bus_seat_image_location,
                'bus_total_seats' => $bus_total_seats,
                'is_full_bus' => $is_full_bus,
                'travelling_days' => $travelling_days
            );             
            
            
            $nullValidator = $validateAndHandleError->multiValidator($validateNullData, "string");
            if($nullValidator != "success") {
                return $nullValidator;
            }                

            /* if is_full_bus is false then only online_booking_seats will exist */
            if (!$is_full_bus && !$online_booking_seats) {
                return $response->errorResponse('online_booking_seats');
//                return $validateAndHandleError->errorIdentifier("online_booking_seats");
            }

            /* Validating seat number is numeric or not */
            $validateSeats = $validateAndHandleError->multiValidator(['bus_total_seats' => $bus_total_seats], "seat_number");
            if($validateSeats != "success") {
                return $validateSeats;
            }
            
            /* Validating arrival an destination unique code */
            $validateLocationId = $validateAndHandleError->multiValidator([
                'from_place_id' => $from_place_id,
                'to_place_id' => $to_place_id,
                ], "number");
            if($validateLocationId != "success") {
                return $validateLocationId;
            }
            
            $validateDestinationAndArrival = $validateAndHandleError->arrivalAndDestinationVerifier($to_place_id, $from_place_id, $bus_from, $bus_to);
            if ($validateDestinationAndArrival !== TRUE){
                return $validateDestinationAndArrival;
            }
            
            $is_bus_owner_updated = Bus::where('id', $bus_id)
                    ->where('bus_owners_id', $bus_owners_id)
                    ->update(['bus_name' => $bus_name,
                        'bus_from' => $bus_from,
                        'bus_to' => $bus_to,
                        'bus_type' => $bus_type,
                        'bus_comfort' => $bus_comfort,
                        'bus_seat_image_location' => $bus_seat_image_location,
                        'bus_total_seats' => $bus_total_seats,
                        'online_booking_seats' => $online_booking_seats,
                        'is_full_bus' => $is_full_bus,
                        'travelling_days' => $travelling_days
                    ]);
            
            $info = array(
                'bus_id' => $bus_id,
                'bus_owners_id' => $bus_owners_id
            );
            
            if ($is_bus_owner_updated == 1) {
                return $response->successResponse(200, "Bus details has been updated successully", $info);
            }
            return $response->errorResponse("update_unsuccessfull");
        }
        /**
         * Returns:
        {
            "error": false,
            "code": 200,
            "message": "Bus details has been updated successully",
            "info": {
                "bus_id": "27",
                "bus_owners_id": 4
            }
        }
         */
        
        
        /**
         * Used to update departure details
        {
                "params": {
                        "buses_id":44,
                        "departure_from": "Paltan Bazar",
                        "departure_time": "9:20pm"
                }
        }
         * URL: /public/api/v1.0/update/bus/departure/{departure_id}
         */
        public function updateDepartureDetails(Request $request){
            $data = Input::json()->all();
            $response = $this->response;
            $validateAndHandleError = new ValidateAndHandleError;
            if(!Auth::user()->id){
                return $validateAndHandleError->invalidUser();
            }
            
            if (!$request->route('departure_id')){
                return $response->errorResponse("departure_id_not_exist");
            }
            $departure_id = $request->route('departure_id');
            
            if(!isset($data) || !isset($data['params']) ||
                    !isset($data['params']['departure_from']) ||
                    !isset($data['params']['departure_time'])) {
                return $response->errorResponse('invalid_params');
//                return $validateAndHandleError->errorIdentifier("invalid_params");
            }
            $buses_id = $data['params']['buses_id'];
            $departure_from = $data['params']['departure_from'];
            $departure_time = $data['params']['departure_time'];
            
            /* Null validation */
            $validateNullData = array(
                "buses_id" => $buses_id,
                "departure_from" => $departure_from,
                "departure_time" => $departure_time
            );
            
            $nullValidator = $validateAndHandleError->multiValidator($validateNullData, "string");
            if($nullValidator != "success") {
                return $nullValidator;
            }
            
            /* Bus id is number or not */
            $validateBusId = $validateAndHandleError->multiValidator(['buses_id' => $buses_id, 'departure_id_not_number' => $departure_id], "number");
            if($validateBusId != "success") {
                return $validateBusId;
            }
            
            /* Bus id exist or not */
            $check_bus_availability = Bus::where('id', $buses_id)->get();
            if (count($check_bus_availability) <= 0) {
                return $response->errorResponse("bus_details_not_available");
            }
            
            /* Departure id exist or not */
            $prev_departure_details = BusDeparturePoint::where('id', $departure_id)->get();
            if (count($prev_departure_details) <= 0) {
                return $response->errorResponse('bus_departure_details_does_not_exist');
//                return $validateAndHandleError->errorIdentifier("bus_departure_details_does_not_exist");
            }
            
            /* bus id passed through param should be equal with bus id of db */
            if ($prev_departure_details[0]->buses_id != $buses_id) {
                return $response->errorResponse('invalid_departure_details');
//                return $validateAndHandleError->errorIdentifier("invalid_departure_details");
            }
            
            $user_id = Auth::user()->id;
            $bus_owners_id = $check_bus_availability[0]->bus_owners_id;
            /* $user_id_temp is used to check whether the details are accessible or not */
            $user_id_temp = BusOwner::where('id', $bus_owners_id)->get()[0]->user_id;
            
            $user_type = $validateAndHandleError->userIdentifier($user_id);
            /* details are only accessible by admin and bus owner */
            if ($user_type == "customer" || $user_type == "dealer" || $user_type == "unknown" || 
                    ($user_type == "bus_owner" && $user_id_temp != $user_id)) {
                return $response->errorResponse("details_not_accessible");
            }
            
            $is_departure_details_updated = BusDeparturePoint::where('id', $departure_id)
                    ->where('buses_id', $buses_id)
                    ->update(['departure_from' => $departure_from,
                        'departure_time' => $departure_time
                    ]);
            
            $info = array(
                'bus_id' => $buses_id,
                'bus_owners_id' => $bus_owners_id,
                'departure_id' => $departure_id,
                'departure_from' => $departure_from,
                'departure_time' => $departure_time
            );
            
            if ($is_departure_details_updated == 1) {
                return $response->successResponse(200, "Bus departure details has been updated successully", $info);
            }
            return $response->errorResponse("update_unsuccessfull");
            
        }
        /**
         * Returns:
        {
            "error": false,
            "code": 200,
            "message": "Bus departure details has been updated successully",
            "info": {
                "bus_id": 27,
                "bus_owners_id": 4,
                "departure_id": "6",
                "departure_from": "Paltan Bazar",
                "departure_time": "9:20pm"
            }
        }
         */
        
        
        /**
         * Url:
         * /public/api/v1.0/update/bus/destination/{dropping_id}
         * 
         * Payload:
            {
                "params": {
                    "buses_id": 27,
                    "bus_departure_points_id": 5,
                    "dropping_point": "Tada",
                    "dropping_time": "9am",
                    "price": "1003"
                }
            }
         */
        public function updateDroppingDetails(Request $request){
            $data = Input::json()->all();
            $response = $this->response;
            $validateAndHandleError = new ValidateAndHandleError;
            if(!Auth::user()->id){
                return $validateAndHandleError->invalidUser();
            }
            
            if (!$request->route('dropping_id')){
                return $response->errorResponse("dropping_id_not_exist");
            }
            $dropping_id = $request->route('dropping_id');
            
            if(!isset($data) || !isset($data['params']) ||
                    !isset($data['params']['buses_id']) ||
                    !isset($data['params']['bus_departure_points_id']) ||
                    !isset($data['params']['dropping_point']) ||
                    !isset($data['params']['place_id']) ||
                    !isset($data['params']['dropping_time']) ||
                    !isset($data['params']['price'])) {
                return $response->errorResponse('invalid_params');
//                return $validateAndHandleError->errorIdentifier("invalid_params");
            }
            $buses_id = $data['params']['buses_id'];
            $bus_departure_points_id = $data['params']['bus_departure_points_id'];
            $dropping_point = $data['params']['dropping_point'];
            $dropping_time = $data['params']['dropping_time'];
            $place_id = $data['params']['place_id'];
            $price = $data['params']['price'];
            
            /* Null validation */
            $validateNullData = array(
                "buses_id" => $buses_id,
                "bus_departure_points_id" => $bus_departure_points_id,
                "dropping_point" => $dropping_point,
                "dropping_time" => $dropping_time,
                "price" => $price,
                "place_id" => $place_id
            );
            
            $nullValidator = $validateAndHandleError->multiValidator($validateNullData, "string");
            if($nullValidator != "success") {
                return $nullValidator;
            }
            
            /* Bus id is number or not */
            $validateBusId = $validateAndHandleError->multiValidator([
                'buses_id' => $buses_id, 
                'dropping_id_not_number'=> $dropping_id, 
                'place_id'=> $place_id, 
                'departure_id_not_number' => $bus_departure_points_id], "number");
            if($validateBusId != "success") {
                return $validateBusId;
            }
            
            $place = Place::where('id', $place_id)->get()[0]->place;

            if ($place != $dropping_point) {
                return $response->errorResponse('to_place_mismatch');
//                return $validateAndHandleError->errorIdentifier("to_place_mismatch");
            }
          
            /* Validate dropping id present in db or not */
            $check_dropping_id = BusDroppingPoint::where('id', $dropping_id)->get();
            if (count($check_dropping_id) <= 0) {
                return $response->errorResponse("dropping_details_not_available");
            }
            
            /* for this dropping id check bus_id and departure id is matching or not */
            if ($check_dropping_id[0]->buses_id != $buses_id || $check_dropping_id[0]->bus_departure_points_id != $bus_departure_points_id) {
                return $response->errorResponse("bus_id_and_departure_id_mismatch");
            }
            
            /* Bus id exist or not */
            $check_bus_availability = Bus::where('id', $buses_id)->get();
            if (count($check_bus_availability) <= 0) {
                return $response->errorResponse("bus_details_not_available");
            }
            
            /* Departure id exist or not */
            $prev_departure_details = BusDeparturePoint::where('id', $bus_departure_points_id)->get();
            if (count($prev_departure_details) <= 0) {
                return $response->errorResponse('bus_departure_details_does_not_exist');
//                return $validateAndHandleError->errorIdentifier("bus_departure_details_does_not_exist");
            }
            
            /* bus id passed through param should be equal with bus id of db */
            if ($prev_departure_details[0]->buses_id != $buses_id) {
                return $response->errorResponse('invalid_departure_details');
//                return $validateAndHandleError->errorIdentifier("invalid_departure_details");
            }
            
            $user_id = Auth::user()->id;
            $bus_owners_id = $check_bus_availability[0]->bus_owners_id;
            /* $user_id_temp is used to check whether the details are accessible or not */
            $user_id_temp = BusOwner::where('id', $bus_owners_id)->get()[0]->user_id;
            
            $user_type = $validateAndHandleError->userIdentifier($user_id);
            /* details are only accessible by admin and bus owner */
            if ($user_type == "customer" || $user_type == "dealer" || $user_type == "unknown" || 
                    ($user_type == "bus_owner" && $user_id_temp != $user_id)) {
                return $response->errorResponse("details_not_accessible");
            }
            
            $is_dropping_details_updated = BusDroppingPoint::where('id', $dropping_id)
                    ->where('buses_id', $buses_id)
                    ->where('bus_departure_points_id', $bus_departure_points_id)
                    ->update(['dropping_point' => $dropping_point,
                        'dropping_time' => $dropping_time,
                        'price' => $price
                    ]);
            
            $info = array(
                'bus_dropping_id' => $dropping_id,
                'bus_id' => $buses_id,
                'bus_owners_id' => $bus_owners_id,
                'departure_id' => $bus_departure_points_id,
                'dropping_point' => $dropping_point,
                'dropping_time' => $dropping_time,
                'price' => $price
            );
            
            if ($is_dropping_details_updated == 1) {
                return $response->successResponse(200, "Bus dropping details has been updated successully", $info);
            }
            return $response->errorResponse("update_unsuccessfull");
            
        }
        
        /**
         * Returns: 
        {
            "error": false,
            "code": 200,
            "message": "Bus dropping details has been updated successully",
            "info": {
                "bus_dropping_id": "15",
                "bus_id": 27,
                "bus_owners_id": 4,
                "departure_id": 5,
                "dropping_point": "Tada",
                "dropping_time": "9am",
                "price": "1003"
            }
        }
         */
}
