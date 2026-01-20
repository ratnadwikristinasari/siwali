<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;


class Advise extends Model
{
    use HasUuids;
    protected $table = 'advise';
    protected $fillable = [
        'student_user_id',
        'lecture_user_id',
        'student_id',
        'lecture_id',
        'status',
        'khs',
        'semester',
        'ipk',
        'keluhan',
        'masukan',
        'semester_id',
        'session_id',
    ];
    public function lecture() {
        return $this->belongsTo(User::class, 'lecture_user_id');
    }
    public function student() {
        return $this->belongsTo(User::class, 'student_user_id');
    }
    public function setMasukanAttribute($value) {
        $this->attributes['masukan'] = $value;
        $this->attributes['status'] = empty($value) ? 'Pending' : 'Done';
    }
}
