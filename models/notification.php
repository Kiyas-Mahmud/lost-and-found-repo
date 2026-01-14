<?php
class Notification {
    private $conn;
    private $table = 'notifications';

    public $notification_id;
    public $user_id;
    public $title;
    public $message;
    public $item_id;
    public $claim_id;
    public $is_read;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create new notification
    public function create($user_id, $title, $message, $item_id = null, $claim_id = null) {
        $query = "INSERT INTO {$this->table} 
                  (user_id, title, message, item_id, claim_id, is_read) 
                  VALUES (:user_id, :title, :message, :item_id, :claim_id, 0)";

        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $title = htmlspecialchars(strip_tags($title));
        $message = htmlspecialchars(strip_tags($message));

        // Bind parameters
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':message', $message);
        $stmt->bindParam(':item_id', $item_id);
        $stmt->bindParam(':claim_id', $claim_id);

        return $stmt->execute();
    }

    // Get all notifications for a user
    public function getByUserId($user_id, $filter = 'all', $limit = 20, $offset = 0) {
        $query = "SELECT * FROM {$this->table} WHERE user_id = :user_id";

        if ($filter == 'unread') {
            $query .= " AND is_read = 0";
        } elseif ($filter == 'read') {
            $query .= " AND is_read = 1";
        }

        $query .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Get notification by ID
    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE notification_id = :notification_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':notification_id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // Mark notification as read
    public function markAsRead($notification_id) {
        $query = "UPDATE {$this->table} SET is_read = 1 WHERE notification_id = :notification_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':notification_id', $notification_id);

        return $stmt->execute();
    }

    // Mark all notifications as read for a user
    public function markAllAsRead($user_id) {
        $query = "UPDATE {$this->table} SET is_read = 1 WHERE user_id = :user_id AND is_read = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);

        return $stmt->execute();
    }

    // Get unread count for user
    public function getUnreadCount($user_id) {
        $query = "SELECT COUNT(*) as total FROM {$this->table} 
                  WHERE user_id = :user_id AND is_read = 0";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Get total count for user
    public function getCount($user_id, $filter = 'all') {
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE user_id = :user_id";

        if ($filter == 'unread') {
            $query .= " AND is_read = 0";
        } elseif ($filter == 'read') {
            $query .= " AND is_read = 1";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Delete notification
    public function delete($notification_id) {
        $query = "DELETE FROM {$this->table} WHERE notification_id = :notification_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':notification_id', $notification_id);

        return $stmt->execute();
    }

    // Delete all notifications for a user
    public function deleteAllByUserId($user_id) {
        $query = "DELETE FROM {$this->table} WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);

        return $stmt->execute();
    }
}
?>
