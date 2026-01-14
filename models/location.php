<?php
// Location Model
class Location {
    private $conn;
    private $table = 'locations';

    public function __construct($db) {
        $this->conn = $db;
    }
}
?>
