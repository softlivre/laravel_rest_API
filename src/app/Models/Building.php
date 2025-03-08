<?php

namespace App\Models;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    use HasFactory;

    protected $guarded = [];

    // One building has many tasks
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
