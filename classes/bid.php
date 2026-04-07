<?php
class Auction {
    private $conn;
    private $table_name = "auctions";

    public $id;
    public $title;
    public $description;
    public $image;
    public $starting_price;
    public $current_price;
    public $end_time;
    public $seller_id;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET title=:title, description=:description, image=:image,
                      starting_price=:starting_price, current_price=:starting_price,
                      end_time=:end_time, seller_id=:seller_id, status='active'";
        
        $stmt = $this->conn->prepare($query);
        
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->starting_price = floatval($this->starting_price);
        $this->end_time = date('Y-m-d H:i:s', strtotime($this->end_time));
        $this->seller_id = intval($this->seller_id);

        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":image", $this->image);
        $stmt->bindParam(":starting_price", $this->starting_price);
        $stmt->bindParam(":end_time", $this->end_time);
        $stmt->bindParam(":seller_id", $this->seller_id);

        return $stmt->execute();
    }

    public function readActive() {
        $query = "SELECT a.*, u.username as seller_name,
                         (SELECT COUNT(*) FROM bids b WHERE b.auction_id = a.id) as bid_count,
                         (SELECT MAX(amount) FROM bids b WHERE b.auction_id = a.id) as highest_bid
                  FROM " . $this->table_name . " a 
                  LEFT JOIN users u ON a.seller_id = u.id 
                  WHERE a.status = 'active' 
                  ORDER BY a.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readOne() {
        $query = "SELECT a.*, u.username as seller_name,
                         (SELECT username FROM users WHERE id = a.winner_id) as winner_name,
                         (SELECT COUNT(*) FROM bids WHERE auction_id = a.id) as total_bids
                  FROM " . $this->table_name . " a 
                  LEFT JOIN users u ON a.seller_id = u.id 
                  WHERE a.id = ? LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $this->title = $row['title'];
            $this->description = $row['description'];
            $this->current_price = $row['current_price'];
            $this->end_time = $row['end_time'];
            // ... other properties
            return true;
        }
        return false;
    }
}
?>