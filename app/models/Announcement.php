<?php

namespace App\Models;

class Announcement {
    public $id;
    public $title;
    public $category;
    public $message;
    public $created_at;
    public $author; // Optional
}