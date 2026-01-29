<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'project_identifier', 'name', 'description', 'discipline_id', 'client_name', 'project_manager', 'start_date', 'end_date', 'status',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function discipline()
    {
        return $this->belongsTo(Discipline::class);
    }
}
