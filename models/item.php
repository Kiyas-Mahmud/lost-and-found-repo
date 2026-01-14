<?php
// Item Model
class Item {
    private $conn;
    private $table = 'items';

    public function __construct($db) {
        $this->conn = $db;
    }
}
?>
