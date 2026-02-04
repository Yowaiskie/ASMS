<?php

namespace App\Models;

class Schedule {
    public $id;
    public $mass_type; // e.g., Sunday Mass, Wedding, Funeral
    public $mass_date;
    public $mass_time;
    public $assigned_servers; // Array of server objects
    public $assigned_ids; // Array of server IDs
    public $status; // Confirmed, Pending, Cancelled
    public $color;
}