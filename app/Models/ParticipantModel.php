<?php

namespace App\Models;

use CodeIgniter\Model;

class ParticipantModel extends Model
{
    protected $table = 'participant';
    protected $primaryKey = 'participant_id';
    protected $useAutoIncrement = true;
    protected $allowedFields = ['member_id', 'schedule_id'];
}