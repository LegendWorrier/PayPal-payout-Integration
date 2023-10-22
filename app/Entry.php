<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Entry extends Model
{    
   /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'entries';
    protected $fillable = ['email', 'amount'];
    public $timestamps = false;
}
