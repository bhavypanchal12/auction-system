<footer class="bg-dark text-light py-5 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4">
                <h5><i class="fas fa-gavel text-warning me-2"></i>AuctionPHP</h5>
                <p class="text-muted">The ultimate real-time auction platform built with PHP, MySQL & Bootstrap.</p>
                <div class="mt-3">
                    <a href="#" class="text-light me-3"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-light me-3"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-light me-3"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            <div class="col-md-2 col-sm-6 mb-4">
                <h6>Quick Links</h6>
                <ul class="list-unstyled">
                    <li><a href="index.php" class="text-muted text-decoration-none">Home</a></li>
                    <li><a href="auctions/create.php" class="text-muted text-decoration-none">Sell</a></li>
                    <?php if (isLoggedIn()): ?>
                    <li><a href="admin/dashboard.php" class="text-muted text-decoration-none">Admin</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <h6>Demo Accounts</h6>
                <ul class="list-unstyled">
                    <li><strong>seller1</strong><br><small class="text-muted">password</small></li>
                    <li><strong>buyer1</strong><br><small class="text-muted">password</small></li>
                </ul>
            </div>
            <div class="col-md-3 mb-4">
                <h6>Status</h6>
                <div class="mb-2">
                    <span class="badge bg-success">🟢 Live</span>
                    <span class="badge bg-primary ms-1">Real-time</span>
                </div>
                <small class="text-muted">
                    PHP 8+ • MySQL • Bootstrap 5<br>
                    AJAX Real-time Bidding
                </small>
            </div>
        </div>
        <hr>
        <div class="text-center">
            <p class="mb-0">&copy; 2024 AuctionPHP. Built with ❤️ for learning.</p>
        </div>
    </div>
</footer>

<!-- Chat Bubble (Optional) -->
<div class="chat-bubble" onclick="toggleChat()">
    <i class="fas fa-comments"></i>
</div>

<script>
function toggleChat() {
    alert('💬 Full chat system can be added with WebSocket/Pusher!');
}
</script>

<style>
.chat-bubble {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    cursor: pointer;
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    transition: transform 0.3s;
    z-index: 9999;
}
.chat-bubble:hover {
    transform: scale(1.1);
}
</style>