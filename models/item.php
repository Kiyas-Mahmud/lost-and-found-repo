<?php
class Item {
    private $conn;
    private $table = 'items';

    public $item_id;
    public $title;
    public $description;
    public $item_type;
    public $category_id;
    public $location_id;
    public $event_date;
    public $image_path;
    public $current_status;
    public $posted_by;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create new item
    public function create() {
        $query = "INSERT INTO {$this->table} 
                  (title, description, item_type, category_id, location_id, event_date, image_path, current_status, posted_by) 
                  VALUES (:title, :description, :item_type, :category_id, :location_id, :event_date, :image_path, :current_status, :posted_by)";

        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));

        // Bind parameters
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':item_type', $this->item_type);
        $stmt->bindParam(':category_id', $this->category_id);
        $stmt->bindParam(':location_id', $this->location_id);
        $stmt->bindParam(':event_date', $this->event_date);
        $stmt->bindParam(':image_path', $this->image_path);
        $stmt->bindParam(':current_status', $this->current_status);
        $stmt->bindParam(':posted_by', $this->posted_by);

        if ($stmt->execute()) {
            $this->item_id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Get item by ID with related data
    public function getById($id) {
        $query = "SELECT i.*, 
                         c.category_name, 
                         l.location_name, 
                         u.full_name as posted_by_name, 
                         u.email as posted_by_email
                  FROM {$this->table} i
                  LEFT JOIN categories c ON i.category_id = c.category_id
                  LEFT JOIN locations l ON i.location_id = l.location_id
                  LEFT JOIN users u ON i.posted_by = u.user_id
                  WHERE i.item_id = :item_id 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':item_id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // Get all items with filters and pagination
    public function getAll($filters = [], $limit = 12, $offset = 0) {
        $query = "SELECT i.*, 
                         c.category_name, 
                         l.location_name, 
                         u.full_name as posted_by_name
                  FROM {$this->table} i
                  LEFT JOIN categories c ON i.category_id = c.category_id
                  LEFT JOIN locations l ON i.location_id = l.location_id
                  LEFT JOIN users u ON i.posted_by = u.user_id
                  WHERE 1=1";

        // Apply filters
        if (isset($filters['item_type']) && $filters['item_type'] != '') {
            $query .= " AND i.item_type = :item_type";
        }
        if (isset($filters['category_id']) && $filters['category_id'] != '') {
            $query .= " AND i.category_id = :category_id";
        }
        if (isset($filters['location_id']) && $filters['location_id'] != '') {
            $query .= " AND i.location_id = :location_id";
        }
        if (isset($filters['current_status']) && $filters['current_status'] != '') {
            $query .= " AND i.current_status = :current_status";
        }
        if (isset($filters['posted_by']) && $filters['posted_by'] != '') {
            $query .= " AND i.posted_by = :posted_by";
        }
        if (isset($filters['search']) && $filters['search'] != '') {
            $query .= " AND (i.title LIKE :search OR i.description LIKE :search)";
        }
        // Exclude HIDDEN items for public browsing
        if (isset($filters['exclude_hidden']) && $filters['exclude_hidden'] === true) {
            $query .= " AND i.current_status != 'HIDDEN'";
        }

        $query .= " ORDER BY i.created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);

        // Bind filter parameters
        if (isset($filters['item_type']) && $filters['item_type'] != '') {
            $stmt->bindParam(':item_type', $filters['item_type']);
        }
        if (isset($filters['category_id']) && $filters['category_id'] != '') {
            $stmt->bindParam(':category_id', $filters['category_id']);
        }
        if (isset($filters['location_id']) && $filters['location_id'] != '') {
            $stmt->bindParam(':location_id', $filters['location_id']);
        }
        if (isset($filters['current_status']) && $filters['current_status'] != '') {
            $stmt->bindParam(':current_status', $filters['current_status']);
        }
        if (isset($filters['posted_by']) && $filters['posted_by'] != '') {
            $stmt->bindParam(':posted_by', $filters['posted_by']);
        }
        if (isset($filters['search']) && $filters['search'] != '') {
            $search_term = '%' . $filters['search'] . '%';
            $stmt->bindParam(':search', $search_term);
        }

        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Get total count with filters
    public function getCount($filters = []) {
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE 1=1";

        if (isset($filters['item_type']) && $filters['item_type'] != '') {
            $query .= " AND item_type = :item_type";
        }
        if (isset($filters['category_id']) && $filters['category_id'] != '') {
            $query .= " AND category_id = :category_id";
        }
        if (isset($filters['location_id']) && $filters['location_id'] != '') {
            $query .= " AND location_id = :location_id";
        }
        if (isset($filters['current_status']) && $filters['current_status'] != '') {
            $query .= " AND current_status = :current_status";
        }
        if (isset($filters['posted_by']) && $filters['posted_by'] != '') {
            $query .= " AND posted_by = :posted_by";
        }
        if (isset($filters['search']) && $filters['search'] != '') {
            $query .= " AND (title LIKE :search OR description LIKE :search)";
        }
        if (isset($filters['exclude_hidden']) && $filters['exclude_hidden'] === true) {
            $query .= " AND current_status != 'HIDDEN'";
        }

        $stmt = $this->conn->prepare($query);

        if (isset($filters['item_type']) && $filters['item_type'] != '') {
            $stmt->bindParam(':item_type', $filters['item_type']);
        }
        if (isset($filters['category_id']) && $filters['category_id'] != '') {
            $stmt->bindParam(':category_id', $filters['category_id']);
        }
        if (isset($filters['location_id']) && $filters['location_id'] != '') {
            $stmt->bindParam(':location_id', $filters['location_id']);
        }
        if (isset($filters['current_status']) && $filters['current_status'] != '') {
            $stmt->bindParam(':current_status', $filters['current_status']);
        }
        if (isset($filters['posted_by']) && $filters['posted_by'] != '') {
            $stmt->bindParam(':posted_by', $filters['posted_by']);
        }
        if (isset($filters['search']) && $filters['search'] != '') {
            $search_term = '%' . $filters['search'] . '%';
            $stmt->bindParam(':search', $search_term);
        }

        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Update item
    public function update() {
        $query = "UPDATE {$this->table} 
                  SET title = :title, 
                      description = :description, 
                      category_id = :category_id, 
                      location_id = :location_id, 
                      event_date = :event_date";

        if ($this->image_path) {
            $query .= ", image_path = :image_path";
        }

        $query .= " WHERE item_id = :item_id";

        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));

        // Bind parameters
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':category_id', $this->category_id);
        $stmt->bindParam(':location_id', $this->location_id);
        $stmt->bindParam(':event_date', $this->event_date);
        $stmt->bindParam(':item_id', $this->item_id);

        if ($this->image_path) {
            $stmt->bindParam(':image_path', $this->image_path);
        }

        return $stmt->execute();
    }

    // Update item status
    public function updateStatus($status) {
        $query = "UPDATE {$this->table} SET current_status = :status WHERE item_id = :item_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':item_id', $this->item_id);

        return $stmt->execute();
    }

    // Delete item
    public function delete() {
        $query = "DELETE FROM {$this->table} WHERE item_id = :item_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':item_id', $this->item_id);

        return $stmt->execute();
    }

    // Get statistics
    public function getStats() {
        $query = "SELECT 
                    COUNT(*) as total_items,
                    SUM(CASE WHEN item_type = 'LOST' THEN 1 ELSE 0 END) as total_lost,
                    SUM(CASE WHEN item_type = 'FOUND' THEN 1 ELSE 0 END) as total_found,
                    SUM(CASE WHEN current_status = 'RETURNED' OR current_status = 'CLOSED' THEN 1 ELSE 0 END) as total_returned,
                    SUM(CASE WHEN current_status = 'OPEN' THEN 1 ELSE 0 END) as total_open
                  FROM {$this->table}";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
