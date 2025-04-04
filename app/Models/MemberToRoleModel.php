<?php

namespace App\Models;

use CodeIgniter\Model;

class MemberToRoleModel extends Model
{
    protected $table = 'member_to_role';
    protected $primaryKey = 'member_to_role_id';
    protected $useAutoIncrement = true;
    protected $allowedFields = ['member_id', 'role_id'];
}