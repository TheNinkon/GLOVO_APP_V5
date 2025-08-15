<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Rider extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The guard associated with the model.
     *
     * @var string
     */
    protected $guard = 'rider';

    protected $fillable = [
        'full_name',
        'dni',
        'city',
        'phone',
        'email',
        'password',
        'start_date',
        'status',
        'notes',
        'weekly_contract_hours', // <-- NUEVO CAMPO AÑADIDO
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'start_date' => 'date',
        ];
    }
}
