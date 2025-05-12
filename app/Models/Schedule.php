<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'cetap',
        'semester',
        'subject',
        'teacher_id',
        'date',
        'mode',
        'working_day',
        'commission'
    ];

    // Definir la relación con Teacher
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
