<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    public const STATUS_TODO = 0;
    public const STATUS_DONE = 1;
    public const STATUS_NAME_TODO = 'todo';
    public const STATUS_NAME_DONE = 'done';

    public const STATUSES = [
        self::STATUS_NAME_TODO => self::STATUS_TODO,
        self::STATUS_NAME_DONE => self::STATUS_DONE,
    ];

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'status',
        'priority',
        'title',
        'description',
        'createdAt',
        'completedAt'
    ];
}
