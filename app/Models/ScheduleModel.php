<?php

namespace App\Models;

use CodeIgniter\Model;

class ScheduleModel extends Model
{
    protected $table = 'schedule';
    protected $primaryKey = 'schedule_id';
    protected $useAutoIncrement = true;
    protected $allowedFields = ['type', 'title', 'place', 'start_dt', 'end_dt', 'member_id'];
}