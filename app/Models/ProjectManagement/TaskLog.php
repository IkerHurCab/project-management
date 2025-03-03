<?php
namespace App\Models\ProjectManagement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TaskLog extends Model
{
    use HasFactory;


    protected $fillable = [
        'task_id',
        'user_id',
        'log_time',
        'log_date',
        'description',
    ];

    protected $appends = ['day_of_week'];
    
    public function task()
    {
        return $this->belongsTo(Task::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getDayOfWeekAttribute() {
        return Carbon::parse($this->log_date)->dayOfWeek;
    }
}
