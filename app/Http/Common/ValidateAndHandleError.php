<?php namespace App\Http\Common;

use Illuminate\Support\Facades\Validator;
use App\User;
use App\Place;
use App\Http\Common\ResponseCreator;

class ValidateAndHandleError {
    
    function __construct() {
        $this->response = new ResponseCreator();
    }
    
    public function errorIdentifier($name){
        $errorDetails = array(
            "data_not_saved" => array (
                "code" => -998,
                "message" => "Data not saved"
            ),
            "unknown_error" => array (
                "code" => -999,
                "message" => "Unknown error"
            ),
            "bus_owners_id" => array (
                "code" => -1000,
                "message" => "bus_owners_id field is empty"
            ),
            "bus_name" => array (
                "code" => -1001,
                "message" => "bus_name field is empty"
            ),
            "bus_from" => array (
                "code" => -1002,
                "message" => "bus_form field is empty"
            ),
            "bus_to" => array (
                "code" => -1003,
                "message" => "bus_to field is empty"
            ),
            "bus_type" => array (
                "code" => -1004,
                "message" => "bus_type field is empty"
            ),
            "bus_comfort" => array (
                "code" => -1005,
                "message" => "bus_comfort field is empty"
            ),
            "bus_seat_image_location" => array (
                "code" => -1006,
                "message" => "bus_seat_image_location field is empty"
            ),
            "bus_total_seats" => array (
                "code" => -1007,
                "message" => "bus_total_seats field is empty"
            ),
            "invalid_user" => array (
                "code" => -1009,
                "message" => "Invalid user"
            ),
            "online_booking_seats" => array (
                "code" => -1010,
                "message" => "Please enter number of online booking seats"
            ),
            "bus_total_seats" => array (
                "code" => -1011,
                "message" => "Invalid total seat number"
            ),
            "user_id" => array (
                "code" => -1012,
                "message" => "Empty user id"
            ),
            "name_of_the_travels" => array (
                "code" => -1013,
                "message" => "Empty name of the travels"
            ),
            "owner_name" => array (
                "code" => -1014,
                "message" => "Empty owner name"
            ),
            "phone_number_one" => array (
                "code" => -1015,
                "message" => "Invalid phone number"
            ),
            "street" => array (
                "code" => -1016,
                "message" => "Empty Street"
            ),
            "city" => array (
                "code" => -1017,
                "message" => "Empty city"
            ),
            "pincode" => array (
                "code" => -1018,
                "message" => "Empty pincode"
            ),
            "state" => array (
                "code" => -1019,
                "message" => "Empty state"
            ),
            "country" => array (
                "code" => -1020,
                "message" => "Empty country"
            ),
            "bus_owner_exist" => array (
                "code" => -1021,
                "message" => "Bus owner already exist"
            ),            
            "buses_id" => array (
                "code" => -1022,
                "message" => "Invalid bus id"
            ),
            "departure_from" => array (
                "code" => -1023,
                "message" => "Empty departure point"
            ),
            "departure_time" => array (
                "code" => -1024,
                "message" => "Empty departure time"
            ),
            "bus_does_not_exist" => array (
                "code" => -1025,
                "message" => "Bus details does't exist"
            ),
            "departure_from_exist" => array (
                "code" => -1026,
                "message" => "Departure from already exist"
            ),
            "departure_time_exist" => array (
                "code" => -1027,
                "message" => "Departure time is already exist for same departure point"
            ),
            "bus_departure_points_id" => array (
                "code" => -1028,
                "message" => "Invalid departure point id"
            ),
            "dropping_point" => array (
                "code" => -1029,
                "message" => "Invalid dropping point id"
            ),
            "dropping_time" => array (
                "code" => -1030,
                "message" => "Empty dropping point time"
            ),
            "price" => array (
                "code" => -1031,
                "message" => "Invalid price"
            ),
            "bus_departure_details_does_not_exist" => array (
                "code" => -1032,
                "message" => "Bus departure details for the given id does't exist"
            ),
            "bus_does_not_exist_for_departure_details" => array (
                "code" => -1033,
                "message" => "Bus does't exist for departure details"
            ),
            "same_dropping_point" => array (
                "code" => -1034,
                "message" => "Dropping place is already exist for same bus"
            ),
            "same_dropping_time" => array (
                "code" => -1035,
                "message" => "Dropping time is already exist for same bus"
            ),
            "id_not_exist" => array (
                "code" => -1036,
                "message" => "User id not available in URL"
            ),
            "user_not_accessible" => array (
                "code" => -1037,
                "message" => "You are not authorized to access this user details"
            ),
            "user_not_exist" => array (
                "code" => -1038,
                "message" => "User not exist"
            ),
            "bus_details_not_available" => array (
                "code" => -1039,
                "message" => "Bus details is not available"
            ),
            "details_not_accessible" => array (
                "code" => -1040,
                "message" => "Details are not accessible by you"
            ),
            "should_be_numeric_value" => array (
                "code" => -1041,
                "message" => "Value should be numeric"
            ),
            "bus_owner_details_not_available" => array (
                "code" => -1042,
                "message" => "Bus owners details not available"
            ),
            "departure_details_not_available" => array (
                "code" => -1043,
                "message" => "Departure details not available. Please add the details"
            ),
            "dropping_details_not_available" => array (
                "code" => -1044,
                "message" => "Dropping details not available. Please add the details"
            ),
            "invalid_params" => array (
                "code" => -1045,
                "message" => "Invalid params"
            ),
            "update_unsuccessfull" => array (
                "code" => -1046,
                "message" => "Data update unsuccessfull"
            ),
            "bus_id_not_exist" => array (
                "code" => -1047,
                "message" => "Bus id not exist"
            ),
            "departure_id_not_exist" => array (
                "code" => -1048,
                "message" => "Departure id not exist"
            ),
            "departure_id_not_number" => array (
                "code" => -1049,
                "message" => "Departure id is not a number"
            ),
            "invalid_departure_details" => array (
                "code" => -1050,
                "message" => "Departure details not matching with bus id"
            ),
            "dropping_id_not_exist" => array (
                "code" => -1051,
                "message" => "Dropping id does't exist"
            ),
            "dropping_id_not_number" => array (
                "code" => -1052,
                "message" => "Dropping id is not a number"
            ),
            "dropping_details_not_available" => array (
                "code" => -1053,
                "message" => "Dropping details not available"
            ),
            "bus_id_and_departure_id_mismatch" => array (
                "code" => -1054,
                "message" => "Bus id or depararture id is not exist for bus dropping id"
            ),
            "travelling_days" => array (
                "code" => -1055,
                "message" => "Travelling days cannot be empty"
            ),
            "from_place_id" => array (
                "code" => -1056,
                "message" => "Place id should be numeric"
            ),
            "to_place_id" => array (
                "code" => -1057,
                "message" => "Place id should be numeric"
            ),
            "from_and_to_id_same" => array (
                "code" => -1058,
                "message" => "Id of departure and arrival place is same"
            ),
            "from_place_mismatch" => array (
                "code" => -1059,
                "message" => "Given departure id is not matching with location"
            ),
            "to_place_mismatch" => array (
                "code" => -1060,
                "message" => "Given destination id is not matching with location"
            ),
            "place_id" => array (
                "code" => -1061,
                "message" => "Place id should be number"
            ),
            "invalid_date" => array (
                "code" => -1062,
                "message" => "Please enter a valid date"
            ),
            "higher_dor" => array (
                "code" => -1063,
                "message" => "Date of return is higher than date of journey"
            )
        );
        
        foreach ($errorDetails as $key => $value) {
            if($key == $name) {
                return $errorDetails[$key];
            }
        }
        return $errorDetails["unknown_error"];
    }    
    
    public function multiValidator($dataArray, $validationType = 'string'){
        $response = $this->response;
        $type = "";
        if($validationType == "string") {
            $type = "required";
        } else if ($validationType == "seat_number") {
            $type = "numeric|between:1,50";
        } else if ($validationType == "number") {
            $type = "numeric";
        } else if ($validationType == "phone_number") {
            $type = "min:7";
        } else if ($validationType == "pincode") {
            $type = "size:6";
        } else if ($validationType == "date") {
            $type = "date";
        }
        if ($type == ""){
            return $response->errorResponse('unknown_error');
        }
        foreach ($dataArray as $key => $value) {
            $validator = Validator::make(
                [
                    $key => $value,
                ],
                [
                    $key => $type,
                ]
            );
            if ($validator->fails())
            {
                $errorDetails = $this->errorIdentifier($key);
                $response = array(
                    'error' => true,
                    'info' => $errorDetails,
                    'info2' => $validator->messages()
                );
                return ($response);
            }
        }
        return 'success';
    }
    
    public function invalidUser(){
        $response = $this->response;
        return $response->errorResponse('invalid_user');
//        return $errorDetails = $this->errorIdentifier("invalid_user");
    }
    
    public function userIdentifier($user_id) {
        $user = User::where('id', $user_id)->get();
        if (sizeof($user) == 0) {
            return "invalid_user_id";
        }
        if ($user[0]->user_type == "a") {
            return "admin";
        } 
        if ($user[0]->user_type == "b") {
            return "bus_owner";
        } 
        if ($user[0]->user_type == "c") {
            return "customer";
        } 
        if ($user[0]->user_type == "d") {
            return "dealer";
        }
        return "unknown";
    }
    
    public function arrivalAndDestinationVerifier($to_place_id, $from_place_id, $bus_from, $bus_to){
        $response = $this->response;
        if ($to_place_id == $from_place_id) {
            return $response->errorResponse('from_and_to_id_same');
//            return $this->errorIdentifier("from_and_to_id_same");
        }

        $from_place = Place::where('id', $from_place_id)->get()[0]->place;
        $to_place = Place::where('id', $to_place_id)->get()[0]->place;

        if ($from_place != $bus_from) {
            return $response->errorResponse('from_place_mismatch');
//            return $this->errorIdentifier("from_place_mismatch");
        }
        if ($to_place != $bus_to) {
            return $response->errorResponse('to_place_mismatch');
//            return $this->errorIdentifier("to_place_mismatch");
        }
        return TRUE;
    }
}