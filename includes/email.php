<?php
function sendBidEmail($user_id, $auction_title, $amount) {
    $user = new User($pdo);
    $user->getById($user_id);
    
    $to = $user->email;
    $subject = "New bid on $auction_title!";
    $message = "Your bid of $$amount was placed successfully!";
    
    mail($to, $subject, $message);
}
?>