<?php
require_once __DIR__ . '/../../config/database.php';

class mDatabase
{
    protected $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }
}
