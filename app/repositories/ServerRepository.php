<?php

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Core\Database;
use App\Models\Server;

class ServerRepository implements RepositoryInterface {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll($limit = 1000, $offset = 0) {
        $this->db->query("SELECT *, CONCAT_WS(' ', first_name, middle_name, last_name) as name FROM servers ORDER BY last_name ASC, first_name ASC LIMIT :limit OFFSET :offset");
        $this->db->bind(':limit', $limit);
        $this->db->bind(':offset', $offset);
        return $this->db->resultSet();
    }

    public function countAll() {
        $this->db->query("SELECT COUNT(*) as count FROM servers");
        $row = $this->db->single();
        return $row ? $row->count : 0;
    }

    public function getById($id) {
        $this->db->query("SELECT *, CONCAT_WS(' ', first_name, middle_name, last_name) as name FROM servers WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function create(array $data) {
        $this->db->query("INSERT INTO servers (first_name, middle_name, last_name, nickname, dob, age, address, phone, email, rank, team, status, month_joined, investiture_date, order_name, position) 
                          VALUES (:fname, :mname, :lname, :nickname, :dob, :age, :address, :phone, :email, :rank, :team, :status, :month_joined, :investiture_date, :order_name, :position)");
        $this->db->bind(':fname', $data['first_name']);
        $this->db->bind(':mname', $data['middle_name'] ?? '');
        $this->db->bind(':lname', $data['last_name']);
        $this->db->bind(':nickname', $data['nickname'] ?? null);
        $this->db->bind(':dob', $data['dob'] ?? null);
        $this->db->bind(':age', $data['age'] ?? null);
        $this->db->bind(':address', $data['address'] ?? null);
        $this->db->bind(':phone', $data['phone'] ?? null);
        $this->db->bind(':email', $data['email'] ?? '');
        $this->db->bind(':rank', $data['rank'] ?? 'Server');
        $this->db->bind(':team', $data['team'] ?? 'Unassigned');
        $this->db->bind(':status', $data['status'] ?? 'Active');
        $this->db->bind(':month_joined', $data['month_joined'] ?? null);
        $this->db->bind(':investiture_date', $data['investiture_date'] ?? null);
        $this->db->bind(':order_name', $data['order_name'] ?? null);
        $this->db->bind(':position', $data['position'] ?? null);
        return $this->db->execute();
    }

    public function update($id, array $data) {
        $this->db->query("UPDATE servers SET first_name = :fname, middle_name = :mname, last_name = :lname, nickname = :nickname, dob = :dob, age = :age, address = :address, phone = :phone, email = :email, rank = :rank, team = :team, status = :status, month_joined = :month_joined, investiture_date = :investiture_date, order_name = :order_name, position = :position WHERE id = :id");
        $this->db->bind(':id', $id);
        $this->db->bind(':fname', $data['first_name']);
        $this->db->bind(':mname', $data['middle_name'] ?? '');
        $this->db->bind(':lname', $data['last_name']);
        $this->db->bind(':nickname', $data['nickname'] ?? null);
        $this->db->bind(':dob', $data['dob'] ?? null);
        $this->db->bind(':age', $data['age'] ?? null);
        $this->db->bind(':address', $data['address'] ?? null);
        $this->db->bind(':phone', $data['phone'] ?? null);
        $this->db->bind(':email', $data['email'] ?? '');
        $this->db->bind(':rank', $data['rank'] ?? '');
        $this->db->bind(':team', $data['team'] ?? '');
        $this->db->bind(':status', $data['status'] ?? '');
        $this->db->bind(':month_joined', $data['month_joined'] ?? null);
        $this->db->bind(':investiture_date', $data['investiture_date'] ?? null);
        $this->db->bind(':order_name', $data['order_name'] ?? null);
        $this->db->bind(':position', $data['position'] ?? null);
        return $this->db->execute();
    }

    public function delete($id) {
        $this->db->query("DELETE FROM servers WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function suspendServer($id, $untilDate) {
        $this->db->query("UPDATE servers SET status = 'Suspended', suspension_until = :until WHERE id = :id");
        $this->db->bind(':until', $untilDate);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}