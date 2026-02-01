<?php

namespace App\Models;

class Attendance {
    public $id;
    public $server_name;
    public $date;
    public $mass_time;
    public $status; // Present, Late, Absent
}