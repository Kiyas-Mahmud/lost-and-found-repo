<?php
// Report Model
class Report {
    private $conn;
    private $table = 'reports';

    public function __construct($db) {
        $this->conn = $db;
    }
}
?>
