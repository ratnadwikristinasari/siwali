<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advise extends Model
{
    protected $table = 'Advise';
    protected $fillable = [
        'student_id',
        'lecture_id',
        'status',
        'khs',
        'ipk',
        'keluhan',
        'masukan',
    ];
    public function lecture() {
        return $this->belongsTo(User::class, 'lecture_id');
    }
    public function student() {
        return $this->belongsTo(User::class, 'student_id');
    }
}
