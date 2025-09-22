<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Integration extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'platform',
        'api_key',
        'api_secret',
        'access_token',
        'settings'
    ];

    protected $casts = [
        'settings' => 'array',
    ];
}
