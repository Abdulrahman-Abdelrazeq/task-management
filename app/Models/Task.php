<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'description', 'status_id'];

    public function status()
    {
        return $this->belongsTo(Status::class);
    }
}
