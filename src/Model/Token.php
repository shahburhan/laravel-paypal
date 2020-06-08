<?php

namespace ShahBurhan\LaravelPayPal\Model;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    protected $table = "__pp_token";
    protected $guarded = ['id'];
}
