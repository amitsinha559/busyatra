<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Bus extends Model {

	public function busOwner() {
            return $this->belongsTo("App\BusOwner");
        }
        
        public function busDeparturePoint() {
            return $this->hasMany('App\BusDeparturePoint', 'buses_id');
        }
        
        public function busDroppingPoint() {
            return $this->hasMany('App\BusDroppingPoint', 'buses_id');
        }

        public function bookingHistory() {
            return $this->hasMany("App\BookingHistory", 'buses_id');
        }
}
