<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class BusDeparturePoint extends Model {

	public function bus() {
            return $this->belongsTo("App\Bus");
        }
        
        public function busDroppingPoint() {
            return $this->hasMany('App\BusDroppingPoint', 'bus_departure_points_id');
        }

}
