<?php
class Auction {
    private $conn;
    private $table_name = "auctions";

    // Properties
    public $id;
    public $title;
    public $description;
    public $image;
    public $starting_price;
    public $current_price;
    public $end_time;
    public $seller_id;
    public $winner_id;
    public $status;
    public $seller_name;
    public $total_bids;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create new auction
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET title=:title, description=:description, image=:image,
                      starting_price=:starting_price, current_price=:starting_price,
                      end_time=:end_time, seller_id=:seller_id, status='active'";
        
        $stmt = $this->conn->prepare($query);
        
        // Clean data
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->starting_price = floatval($this->starting_price);
        $this->end_time = date('Y-m-d H:i:s', strtotime($this->end_time));
        $this->seller_id = intval($this->seller_id);

        // Bind values
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":image", $this->image);
        $stmt->bindParam(":starting_price", $this->starting_price);
        $stmt->bindParam(":end_time", $this->end_time);
        $stmt->bindParam(":seller_id", $this->seller_id);

        return $stmt->execute();
    }

    // Read all active auctions
    public function readActive() {
        $query = "SELECT a.*, u.username as seller_name,
                         COALESCE(b.bid_count, 0) as bid_count,
                         COALESCE(b.highest_bid, a.current_price) as highest_bid
                  FROM " . $this->table_name . " a 
                  LEFT JOIN users u ON a.seller_id = u.id
                  LEFT JOIN (
                      SELECT auction_id, 
                             COUNT(*) as bid_count,
                             MAX(amount) as highest_bid
                      FROM bids 
                      GROUP BY auction_id
                  ) b ON a.id = b.auction_id
                  WHERE a.status = 'active' 
                  ORDER BY a.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Read single auction
    public function readOne() {
        $query = "SELECT a.*, u.username as seller_name,
                         w.username as winner_name,
                         COALESCE(b.total_bids, 0) as total_bids,
                         b.highest_bidder_id
                  FROM " . $this->table_name . " a 
                  LEFT JOIN users u ON a.seller_id = u.id
                  LEFT JOIN users w ON a.winner_id = w.id
                  LEFT JOIN (
                      SELECT auction_id, COUNT(*) as total_bids,
                             MAX(user_id) as highest_bidder_id
                      FROM bids GROUP BY auction_id
                  ) b ON a.id = b.auction_id
                  WHERE a.id = ? LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $this->id = $row['id'];
            $this->title = $row['title'];
            $this->description = $row['description'];
            $this->image = $row['image'];
            $this->starting_price = $row['starting_price'];
            $this->current_price = $row['current_price'];
            $this->end_time = $row['end_time'];
            $this->seller_id = $row['seller_id'];
            $this->winner_id = $row['winner_id'];
            $this->status = $row['status'];
            $this->seller_name = $row['seller_name'];
            $this->total_bids = $row['total_bids'];
            return true;
        }
        return false;
    }

    // Update current price
    public function updatePrice($new_price) {
        $query = "UPDATE " . $this->table_name . " 
                  SET current_price = :price 
                  WHERE id = :id AND status = 'active'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':price', $new_price);
        $stmt->bindParam(':id', $this->id);
        
        return $stmt->execute();
    }

    // Check if auction has ended
    public function isEnded() {
        $now = date('Y-m-d H:i:s');
        $query = "SELECT id FROM " . $this->table_name . " 
                  WHERE id = ? AND end_time <= ? AND status = 'active'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->bindParam(2, $now);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    // End auction and set winner
    public function endAuction() {
        $query = "UPDATE " . $this->table_name . " 
                  SET status = 'ended' 
                  WHERE id = ? AND status = 'active'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        
        if ($stmt->execute()) {
            // Set winner (highest bidder)
            $winner_query = "SELECT user_id FROM bids 
                            WHERE auction_id = ? 
                            ORDER BY amount DESC, created_at DESC 
                            LIMIT 1";
            $winner_stmt = $this->conn->prepare($winner_query);
            $winner_stmt->bindParam(1, $this->id);
            $winner_stmt->execute();
            
            if ($winner_row = $winner_stmt->fetch()) {
                $update_winner = "UPDATE " . $this->table_name . " 
                                 SET winner_id = ? WHERE id = ?";
                $winner_update = $this->conn->prepare($update_winner);
                $winner_update->bindParam(1, $winner_row['user_id']);
                $winner_update->bindParam(2, $this->id);
                $winner_update->execute();
            }
            return true;
        }
        return false;
    }

    // Get recent bids for auction
    public function getRecentBids($limit = 10) {
        $query = "SELECT b.*, u.username 
                  FROM bids b 
                  JOIN users u ON b.user_id = u.id 
                  WHERE b.auction_id = ? 
                  ORDER BY b.created_at DESC 
                  LIMIT " . intval($limit);
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        return $stmt;
    }

    // Search auctions
    public function search($keywords) {
        $query = "SELECT a.*, u.username as seller_name
                  FROM " . $this->table_name . " a 
                  LEFT JOIN users u ON a.seller_id = u.id 
                  WHERE a.status = 'active' 
                  AND (a.title LIKE :keywords OR a.description LIKE :keywords)
                  ORDER BY a.current_price DESC";
        
        $stmt = $this->conn->prepare($query);
        $keywords = "%$keywords%";
        $stmt->bindParam(':keywords', $keywords);
        $stmt->execute();
        return $stmt;
    }
}
// Add this method to your existing Auction.php
public function getRecentBids($limit = 10) {
    $query = "SELECT b.*, u.username FROM bids b 
              JOIN users u ON b.user_id = u.id 
              WHERE b.auction_id = ? ORDER BY b.created_at DESC LIMIT ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(1, $this->id);
    $stmt->bindParam(2, $limit);
    $stmt->execute();
    return $stmt;
}
?>