<?php

namespace App\Models;

use App\Config\Database;

class Contact
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    public function saveContact($name, $email, $subject, $message)
    {
        $stmt = $this->db->prepare("INSERT INTO contacts (name, email, subject, message) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$name, $email, $subject, $message]);
    }
    public function getAll()
    {
        $stmt = $this->db->query("SELECT * FROM contacts ORDER BY created_at DESC");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
