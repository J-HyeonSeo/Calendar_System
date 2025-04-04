<?php

namespace App\Models;

use CodeIgniter\Model;

class MemberModel extends Model
{
    protected $table = 'member';
    protected $primaryKey = 'member_id';
    protected $useAutoIncrement = true;
    protected $allowedFields = ['username', 'password', 'nickname'];

    public function findByUsername($username) {
        return $this->where('username', $username)->first();
    }
}