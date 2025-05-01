<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = ['name', 'description', 'status_id'];

    public function status()
    {
        return $this->belongsTo(Status::class);
    }
}
