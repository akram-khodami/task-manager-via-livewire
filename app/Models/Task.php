<?php

namespace App\Models;

use App\Enums\TaskStatus;
use App\Enums\TaskPriority;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'due_date',
        'priority',
        'folder_id',
        'project_id',
        'created_by',
        'assigned_to',
        'estimated_hours',
        'spent_hours',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'estimated_hours' => 'decimal:2',
        'spent_hours' => 'decimal:2',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function attachments()
    {
        return $this->hasMany(TaskAttachment::class);
    }

    //use Accessor for access to $task->status_title
    public function getStatusTitleAttribute()
    {
//        return TaskStatus::from($this->status)->label();
    }

    public function getPriorityTitleAttribute()
    {
//        return TaskPriority::from($this->priority)->label();
    }


    public function getStatusColorAttribute()
    {
        return match($this->status){
        'todo' => 'warning',
        'in_progress' => 'info',
        'done' => 'success',
        'cancelled' => 'danger',
        default => 'secondary'
    };
}
}
