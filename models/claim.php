<?php
// Claim Model
class Claim {
    private $conn;
    private $table = 'claims';

    public function __construct($db) {
        $this->conn = $db;
    }
}
?>
