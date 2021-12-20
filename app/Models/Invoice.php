<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'currency_id',
        'status_id',
        'total',
        'tax',
        'invoice_date',
        'expired_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function sender()
    {
        return $this->hasMany(User::class, 'id', 'sender_id');
    }

    public function receiver()
    {
        return $this->hasMany(User::class, 'id', 'receiver_id');
    }

    public function status()
    {
        return $this->hasMany(Status::class, 'id', 'status_id');
    }

    public function currency()
    {
        return $this->hasMany(Currency::class, 'id', 'currency_id');
    }
}
