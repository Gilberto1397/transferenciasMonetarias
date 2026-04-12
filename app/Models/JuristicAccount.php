<?php

namespace App\Models;

use Database\Factories\JuristicAccountFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $juristicaccount_id
 * @property int $juristicaccount_accounttype
 * @property int $juristicaccount_user
 * @property string $juristicaccount_cnpj
 */
class JuristicAccount extends Model
{
    use HasFactory;

    protected $table = 'juristicaccounts';
    protected $primaryKey = 'juristicaccount_id';
    public $timestamps = false;

    protected static function newFactory(): Factory
    {
        return JuristicAccountFactory::new();
    }
}
