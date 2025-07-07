<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    protected $fillable = [
        'job_id',
        'user_id',
        'employer_id',
        'applied_date',
    ];

    use HasFactory;

    public function job(){
        return $this->belongsTo(Fremaa_job::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
