<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bank extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'branch', 'address', 'contact_person', 'bank_number', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
