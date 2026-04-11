<?php

namespace App\Models;

use Database\Factories\FisicAccountFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $fisicaccount_id
 * @property int $fisicaccount_accounttype
 * @property int $fisicaccount_user
 * @property string $fisicaccount_cpf
 */
class FisicAccount extends Model
{
    use HasFactory;

    protected $table = 'fisicaccount';
    protected $primaryKey = 'fisicaccount_id';
    public $timestamps = false;

    protected static function newFactory(): Factory
    {
        return FisicAccountFactory::new();
    }
}
