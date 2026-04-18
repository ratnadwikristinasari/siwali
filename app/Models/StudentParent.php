<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class StudentParent extends Model
{
    use HasUuids;
    protected $table = 'student_parents';
    protected $fillable = [
        'parent_id',
        'parent_external_id',
        'student_id',
    ];

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
