<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class BusDroppingPoint extends Model {

	public function bus() {
            return $this->belongsTo("App\Bus");
        }
        
        public function busDeparturePoint() {
            return $this->belongsTo("App\BusDeparturePoint");
        }

}
