<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'title',
        'description',
        'status',
        'building_id',
        'assigned_to',
        'created_by'
    ];

    // One task has many comments
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
