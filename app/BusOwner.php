<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class BusOwner extends Model {

	public function user() {
            return $this->belongsTo("App\User");
        }
        
        public function bus() {
            return $this->hasMany('App\Bus', 'bus_owners_id');
        }

}