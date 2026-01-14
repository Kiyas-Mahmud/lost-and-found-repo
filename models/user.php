<?php
// User Model
class User {
    private $conn;
    private $table = 'users';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register($full_name, $email, $student_id, $phone, $password) {
        if ($this->emailExists($email)) {
            return ['success' => false, 'message' => 'Email already registered'];
        }

        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (full_name, email, student_id, phone, password_hash, role) 
                VALUES (:full_name, :email, :student_id, :phone, :password_hash, 'STUDENT')";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':full_name', $full_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':student_id', $student_id);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':password_hash', $password_hash);
        
        try {
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Registration successful'];
            }
            return ['success' => false, 'message' => 'Registration failed'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function emailExists($email) {
        $sql = "SELECT user_id FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
    
    public function getByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
    
    public function getByStudentId($student_id) {
        $sql = "SELECT * FROM users WHERE student_id = :student_id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':student_id', $student_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
}
?>
