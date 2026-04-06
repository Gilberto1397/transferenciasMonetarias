<?php

namespace App\Models;

use Database\Factories\JuristicAccountFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JuristicAccount extends Model
{
    use HasFactory;

    protected $table = 'juristicaccount';
    protected $primaryKey = 'juristicaccount_id';
    public $timestamps = false;

    protected static function newFactory()
    {
        return JuristicAccountFactory::new();
    }
}
