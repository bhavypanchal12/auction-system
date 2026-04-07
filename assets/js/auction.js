$(document).ready(function() {
    loadAuctions();
    
    // Auto-refresh every 3 seconds
    setInterval(loadAuctions, 3000);
    
    // Quick bid
    $(document).on('click', '.bid-btn', function() {
        let auctionId = $(this).data('auction-id');
        let currentPrice = parseFloat($('#current-price-' + auctionId).text().replace('$', ''));
        let bidAmount = prompt('Enter bid (min: $' + (currentPrice + 1).toFixed(2) + '):');
        
        if (bidAmount) {
            bidAmount = parseFloat(bidAmount);
            if (bidAmount > currentPrice) {
                placeBid(auctionId, bidAmount);
            } else {
                alert('Bid must be higher than current price!');
            }
        }
    });
});

function loadAuctions() {
    $.get('auctions/api_get_auctions.php', function(html) {
        $('#auctions-container').html(html);
    });
}

function placeBid(auctionId, amount) {
    $.post('auctions/bid.php', {auction_id: auctionId, amount: amount}, function(response) {
        if (response.success) {
            alert('✅ Bid placed successfully!');
            loadAuctions(); // Refresh
        } else {
            alert('❌ ' + response.message);
        }
    }, 'json');
}