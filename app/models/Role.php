<?php

namespace App\Models;

class Role {
    public $id;
    public $name;
    public $description;
    public $created_at;
    public $permissions = [];
}
