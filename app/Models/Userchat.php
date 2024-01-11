<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Userchat extends Model
{
    use HasFactory, HasUuids;

    /**
     * keytype
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * incrementing
     *
     * @var boolean
     */
    public $incrementing = false;

    /**
     * fillable properties
     *
     * @var array
     */
    public $fillable = [
        'user_id', 'ip_address', 'message', 'response', 'is_failed'
    ];
}
