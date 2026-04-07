<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $accounttypes_id
 * @property string $accounttypes_description
 */
class AccountType extends Model
{
    protected $table = 'accounttypes';
    protected $primaryKey = 'accounttypes_id';
    public $timestamps = false;
}
