<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountType extends Model
{
    protected $table = 'accounttypes';
    protected $primaryKey = 'accounttypes_id';
    public $timestamps = false;
}
