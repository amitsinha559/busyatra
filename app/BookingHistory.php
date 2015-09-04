<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class BookingHistory extends Model {

	public function bus() {
            return $this->belongsTo("App\Bus");
        }

}
