<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sheet extends Model
{    
   /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'sheets';
    protected $fillable = ['sheet_id'];
    public $timestamps = false;
}
