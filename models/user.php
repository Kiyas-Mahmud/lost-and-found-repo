<?php
class User {
    private $conn;
    private $table = 'users';

    public $user_id;
    public $full_name;
    public $email;
    public $student_id;
    public $phone;
    public $password_hash;
    public $role;
    public $account_status;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create new user
    public function create() {
        $query = "INSERT INTO {$this->table} 
                  (full_name, email, student_id, phone, password_hash, role, account_status) 
                  VALUES (:full_name, :email, :student_id, :phone, :password_hash, :role, :account_status)";

        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->full_name = htmlspecialchars(strip_tags($this->full_name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->student_id = htmlspecialchars(strip_tags($this->student_id));
        $this->phone = htmlspecialchars(strip_tags($this->phone));

        // Bind parameters
        $stmt->bindParam(':full_name', $this->full_name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':student_id', $this->student_id);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':password_hash', $this->password_hash);
        $stmt->bindParam(':role', $this->role);
        $stmt->bindParam(':account_status', $this->account_status);

        if ($stmt->execute()) {
            $this->user_id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Get user by ID
    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE user_id = :user_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // Get user by email
    public function getByEmail($email) {
        $query = "SELECT * FROM {$this->table} WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // Check if email exists
    public function emailExists($email) {
        $query = "SELECT user_id FROM {$this->table} WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    // Update user profile
    public function update() {
        $query = "UPDATE {$this->table} 
                  SET full_name = :full_name, 
                      student_id = :student_id, 
                      phone = :phone 
                  WHERE user_id = :user_id";

        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->full_name = htmlspecialchars(strip_tags($this->full_name));
        $this->student_id = htmlspecialchars(strip_tags($this->student_id));
        $this->phone = htmlspecialchars(strip_tags($this->phone));

        // Bind parameters
        $stmt->bindParam(':full_name', $this->full_name);
        $stmt->bindParam(':student_id', $this->student_id);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':user_id', $this->user_id);

        return $stmt->execute();
    }

    // Update password
    public function updatePassword($new_password_hash) {
        $query = "UPDATE {$this->table} SET password_hash = :password_hash WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':password_hash', $new_password_hash);
        $stmt->bindParam(':user_id', $this->user_id);

        return $stmt->execute();
    }

    // Update account status (ACTIVE, INACTIVE, SUSPENDED)
    public function updateStatus($status) {
        $query = "UPDATE {$this->table} SET account_status = :status WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':user_id', $this->user_id);

        return $stmt->execute();
    }

    // Get all users with optional filters
    public function getAll($role = null, $status = null) {
        $query = "SELECT * FROM {$this->table} WHERE 1=1";
        
        if ($role) {
            $query .= " AND role = :role";
        }
        if ($status) {
            $query .= " AND account_status = :status";
        }
        
        $query .= " ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        
        if ($role) {
            $stmt->bindParam(':role', $role);
        }
        if ($status) {
            $stmt->bindParam(':status', $status);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Delete user
    public function delete() {
        $query = "DELETE FROM {$this->table} WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);

        return $stmt->execute();
    }

    // Get total users count
    public function getCount($role = null) {
        $query = "SELECT COUNT(*) as total FROM {$this->table}";
        if ($role) {
            $query .= " WHERE role = :role";
        }

        $stmt = $this->conn->prepare($query);
        if ($role) {
            $stmt->bindParam(':role', $role);
        }

        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
}
?>
