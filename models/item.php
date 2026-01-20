<?php
/**
 * Item Model
 * Handles all database operations for items table
 */
class Item {
    private $conn;
    private $table = 'items';

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Get public items with filters, search, and pagination
     * Only returns OPEN, CLAIM_PENDING, and APPROVED items (excludes HIDDEN)
     */
    public function getPublicItems($filters = [], $limit = 12, $offset = 0) {
        $query = "SELECT 
                    i.item_id,
                    i.title,
                    i.description,
                    i.item_type,
                    i.current_status,
                    i.event_date,
                    i.image_path,
                    i.created_at,
                    c.category_name,
                    l.location_name,
                    u.full_name as poster_name
                FROM {$this->table} i
                LEFT JOIN categories c ON i.category_id = c.category_id
                LEFT JOIN locations l ON i.location_id = l.location_id
                LEFT JOIN users u ON i.posted_by = u.user_id
                WHERE i.current_status IN ('OPEN', 'CLAIM_PENDING', 'APPROVED')";

        $params = [];

        // Apply filters
        if (!empty($filters['keyword'])) {
            $query .= " AND (i.title LIKE ? OR i.description LIKE ?)";
            $searchTerm = '%' . $filters['keyword'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if (!empty($filters['type']) && in_array($filters['type'], ['LOST', 'FOUND'])) {
            $query .= " AND i.item_type = ?";
            $params[] = $filters['type'];
        }

        if (!empty($filters['category'])) {
            $query .= " AND i.category_id = ?";
            $params[] = $filters['category'];
        }

        if (!empty($filters['location'])) {
            $query .= " AND i.location_id = ?";
            $params[] = $filters['location'];
        }

        if (!empty($filters['status']) && in_array($filters['status'], ['OPEN', 'CLAIM_PENDING', 'APPROVED'])) {
            $query .= " AND i.current_status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['date_from'])) {
            $query .= " AND i.event_date >= ?";
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $query .= " AND i.event_date <= ?";
            $params[] = $filters['date_to'];
        }

        // If no filters applied, show random posts, otherwise show newest first
        $hasFilters = !empty($filters['keyword']) || !empty($filters['type']) || 
                     !empty($filters['category']) || !empty($filters['location']) || 
                     !empty($filters['status']) || !empty($filters['date_from']) || 
                     !empty($filters['date_to']);
        
        if ($hasFilters) {
            $query .= " ORDER BY i.created_at DESC";
        } else {
            $query .= " ORDER BY RAND()";
        }
        
        $query .= " LIMIT ? OFFSET ?";

        $stmt = $this->conn->prepare($query);
        
        // Bind all parameters
        $paramIndex = 1;
        foreach ($params as $param) {
            $stmt->bindValue($paramIndex++, $param);
        }
        
        // Bind LIMIT and OFFSET as integers
        $stmt->bindValue($paramIndex++, (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue($paramIndex, (int)$offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Count total public items matching filters
     */
    public function countPublicItems($filters = []) {
        $query = "SELECT COUNT(*) as total
                FROM {$this->table} i
                WHERE i.current_status IN ('OPEN', 'CLAIM_PENDING', 'APPROVED')";

        $params = [];

        // Apply same filters as getPublicItems
        if (!empty($filters['keyword'])) {
            $query .= " AND (i.title LIKE ? OR i.description LIKE ?)";
            $searchTerm = '%' . $filters['keyword'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if (!empty($filters['type']) && in_array($filters['type'], ['LOST', 'FOUND'])) {
            $query .= " AND i.item_type = ?";
            $params[] = $filters['type'];
        }

        if (!empty($filters['category'])) {
            $query .= " AND i.category_id = ?";
            $params[] = $filters['category'];
        }

        if (!empty($filters['location'])) {
            $query .= " AND i.location_id = ?";
            $params[] = $filters['location'];
        }

        if (!empty($filters['status']) && in_array($filters['status'], ['OPEN', 'CLAIM_PENDING', 'APPROVED'])) {
            $query .= " AND i.current_status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['date_from'])) {
            $query .= " AND i.event_date >= ?";
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $query .= " AND i.event_date <= ?";
            $params[] = $filters['date_to'];
        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['total'];
    }

    /**
     * Get single item details by ID
     */
    public function getItemDetails($itemId) {
        $query = "SELECT 
                    i.*,
                    c.category_name,
                    l.location_name,
                    u.full_name as poster_name,
                    u.email as poster_email
                FROM {$this->table} i
                LEFT JOIN categories c ON i.category_id = c.category_id
                LEFT JOIN locations l ON i.location_id = l.location_id
                LEFT JOIN users u ON i.posted_by = u.user_id
                WHERE i.item_id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$itemId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get statistics for home page
     */
    public function getStatistics() {
        $query = "SELECT 
                    COUNT(*) as total_items,
                    SUM(CASE WHEN item_type = 'LOST' THEN 1 ELSE 0 END) as total_lost,
                    SUM(CASE WHEN item_type = 'FOUND' THEN 1 ELSE 0 END) as total_found,
                    SUM(CASE WHEN current_status IN ('RETURNED', 'CLOSED') THEN 1 ELSE 0 END) as total_returned
                FROM {$this->table}
                WHERE current_status != 'HIDDEN'";

        $stmt = $this->conn->query($query);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get recent found items for home page
     */
    public function getRecentFoundItems($limit = 6) {
        $limit = (int)$limit; // Ensure it's an integer
        $query = "SELECT 
                    i.item_id,
                    i.title,
                    i.item_type,
                    i.current_status,
                    i.event_date,
                    i.image_path,
                    i.created_at,
                    c.category_name,
                    l.location_name,
                    u.full_name
                FROM {$this->table} i
                LEFT JOIN categories c ON i.category_id = c.category_id
                LEFT JOIN locations l ON i.location_id = l.location_id
                LEFT JOIN users u ON i.posted_by = u.user_id
                WHERE i.item_type = 'FOUND' 
                AND i.current_status IN ('OPEN', 'CLAIM_PENDING', 'APPROVED')
                ORDER BY i.created_at DESC
                LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Create new item
     */
    public function createItem($data) {
        $query = "INSERT INTO {$this->table} 
                  (title, description, item_type, category_id, location_id, event_date, 
                   image_path, posted_by, current_status) 
                  VALUES 
                  (:title, :description, :item_type, :category_id, :location_id, :event_date, 
                   :image_path, :posted_by, :current_status)";

        $stmt = $this->conn->prepare($query);

        // Bind values
        $stmt->bindValue(':title', $data['title']);
        $stmt->bindValue(':description', $data['description']);
        $stmt->bindValue(':item_type', $data['item_type']);
        $stmt->bindValue(':category_id', $data['category_id'], PDO::PARAM_INT);
        $stmt->bindValue(':location_id', $data['location_id'], PDO::PARAM_INT);
        $stmt->bindValue(':event_date', $data['event_date']);
        $stmt->bindValue(':image_path', $data['image_path']);
        $stmt->bindValue(':posted_by', $data['posted_by'], PDO::PARAM_INT);
        $stmt->bindValue(':current_status', $data['current_status']);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }

        return false;
    }

    /**
     * Get items posted by a specific user with optional filters
     */
    public function getMyItems($userId, $filters = []) {
        $query = "SELECT 
                    i.item_id,
                    i.title,
                    i.description,
                    i.item_type,
                    i.current_status,
                    i.event_date,
                    i.image_path,
                    i.created_at,
                    c.category_name,
                    l.location_name
                FROM {$this->table} i
                LEFT JOIN categories c ON i.category_id = c.category_id
                LEFT JOIN locations l ON i.location_id = l.location_id
                WHERE i.posted_by = ?";

        $params = [$userId];

        // Apply filters
        if (!empty($filters['type']) && in_array($filters['type'], ['LOST', 'FOUND'])) {
            $query .= " AND i.item_type = ?";
            $params[] = $filters['type'];
        }

        if (!empty($filters['status']) && in_array($filters['status'], ['OPEN', 'CLAIM_PENDING', 'APPROVED', 'RETURNED', 'CLOSED', 'HIDDEN'])) {
            $query .= " AND i.current_status = ?";
            $params[] = $filters['status'];
        }

        $query .= " ORDER BY i.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Delete an item by ID (only if posted by specified user)
     */
    public function deleteItem($itemId, $userId) {
        // First verify the item belongs to the user
        $query = "SELECT image_path FROM {$this->table} WHERE item_id = ? AND posted_by = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$itemId, $userId]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$item) {
            return false; // Item not found or doesn't belong to user
        }

        // Delete the item
        $query = "DELETE FROM {$this->table} WHERE item_id = ? AND posted_by = ?";
        $stmt = $this->conn->prepare($query);
        
        if ($stmt->execute([$itemId, $userId])) {
            return $item['image_path']; // Return image path for deletion
        }

        return false;
    }

    /**
     * Update item status (only if posted by specified user)
     */
    public function updateItemStatus($itemId, $userId, $status) {
        $validStatuses = ['PENDING', 'CLAIMED', 'RETURNED', 'OPEN', 'CLAIM_PENDING', 'APPROVED', 'REJECTED', 'HIDDEN'];
        
        if (!in_array($status, $validStatuses)) {
            return false;
        }

        $query = "UPDATE {$this->table} 
                  SET current_status = ? 
                  WHERE item_id = ? AND posted_by = ?";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$status, $itemId, $userId]);
    }
}
?>
