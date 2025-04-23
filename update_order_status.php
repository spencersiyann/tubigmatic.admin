
<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit();
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Get request data
$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['orderKey']) || !isset($data['status'])) {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
    exit();
}

$orderKey = $data['orderKey'];
$status = $data['status'];

// Fetch order details from Firebase
$firebase_orders_url = "https://tubigmatic-admin-default-rtdb.firebaseio.com/Orders/{$orderKey}.json";
$orderData = json_decode(file_get_contents($firebase_orders_url), true);

// Debugging: Log order data
file_put_contents("debug_log.txt", "Fetched Order: " . print_r($orderData, true) . PHP_EOL, FILE_APPEND);

if (!$orderData || !is_array($orderData)) {
    echo json_encode(["success" => false, "message" => "Order not found or invalid data"]);
    exit();
}

// Extract user email & name
$email = $orderData['email'] ?? null;
$name = $orderData['name'] ?? null;

if (empty($email) || empty($name)) {
    echo json_encode(["success" => false, "message" => "Missing email or name"]);
    exit();
}

// Set Firebase destination table
$destinationTable = ($status === "accepted") ? "AcceptedOrders" : "RejectedOrders";
$firebase_dest_url = "https://tubigmatic-admin-default-rtdb.firebaseio.com/{$destinationTable}/{$orderKey}.json";

// Move order to AcceptedOrders or RejectedOrders
$options = [
    "http" => [
        "header"  => "Content-type: application/json",
        "method"  => "PUT",
        "content" => json_encode($orderData),
    ],
];
$context  = stream_context_create($options);
$result = file_get_contents($firebase_dest_url, false, $context);

if (!$result) {
    echo json_encode(["success" => false, "message" => "Failed to update Firebase"]);
    exit();
}

// Remove order from Orders table
$delete_url = "https://tubigmatic-admin-default-rtdb.firebaseio.com/Orders/{$orderKey}.json";
file_get_contents($delete_url, false, stream_context_create(["http" => ["method" => "DELETE"]]));

// **Send email notification**
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'tubigmaticteam@gmail.com';
    $mail->Password = 'pcawzxvwgcrcasvj';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('tubigmaticteam@gmail.com', 'TubigMatic Admin');
    $mail->addAddress($email, $name);
    $mail->isHTML(true);

    if ($status === "accepted") {
        $mail->Subject = "Order Confirmation - TubigMatic";
        $mail->Body = "
        <p>Dear $name,</p>
    
        <p>We are pleased to inform you that your order has been <strong>accepted</strong>! Our team is now processing your request, and we will ensure that your delivery reaches you as soon as possible.</p>
    
        <p><strong>Order Details:</strong></p>
        <ul>
            <li><strong>Reference No.:</strong> {$orderData['referenceNumber']}</li>
            <li><strong>Delivery Address:</strong> {$orderData['address']}</li>
        </ul>
    
        <p>If you have any special requests or need further assistance, feel free to contact us at <a href='mailto:tubigmaticteam@gmail.com'>tubigmaticteam@gmail.com</a>.</p>
    
        <p>Thank you for choosing <strong>TubigMatic</strong>. We appreciate your trust in our service!</p>
    
        <p>Best regards,</p>
        <p><strong>TubigMatic Team</strong></p>
    ";

    } else {
        $mail->Subject = "Order Rejection - TubigMatic";
        $mail->Body = "
        <p>Dear $name,</p>
    
        <p>We regret to inform you that your order has been <strong>rejected</strong> due to an <strong>invalid reference number</strong>. Please double-check your reference number and try again.</p>
    
        <p><strong>Order Details:</strong></p>
        <ul>
            <li><strong>Reference No.:</strong> {$orderData['referenceNumber']}</li>
            <li><strong>Delivery Address:</strong> {$orderData['address']}</li>
        </ul>
    
        <p>If you believe this was a mistake or need any assistance, please contact our support team at <a href='mailto:tubigmaticteam@gmail.com'>tubigmaticteam@gmail.com</a>. We’ll be happy to help resolve the issue.</p>
    
        <p>We apologize for any inconvenience and appreciate your understanding.</p>
    
        <p>Best regards,</p>
        <p><strong>TubigMatic Team</strong></p>
    ";
    }

    $mail->send();
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Email could not be sent: {$mail->ErrorInfo}"]);
    exit();
}

echo json_encode(["success" => true]);
?>
