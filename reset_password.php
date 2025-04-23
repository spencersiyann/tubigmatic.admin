<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Load PHPMailer from Composer

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Firebase Realtime Database URLs
$devices_url = "https://tubigmatic-admin-default-rtdb.firebaseio.com/Devices";
$requests_url = "https://tubigmatic-admin-default-rtdb.firebaseio.com/Requests";

// Get input data from JavaScript
$data = json_decode(file_get_contents('php://input'), true);
$deviceID = $data['deviceID'] ?? '';

if (!$deviceID) {
    echo json_encode(["success" => false, "message" => "Invalid device ID"]);
    exit;
}

// Fetch request details from Requests table
$request_response = file_get_contents("$requests_url/$deviceID.json");
$request = json_decode($request_response, true);

if (!$request || !isset($request['email'])) {
    echo json_encode(["success" => false, "message" => "User email not found for device $deviceID"]);
    exit;
}

$userEmail = $request['email']; // Get email from Requests table

// Fetch device details from Devices table
$device_response = file_get_contents("$devices_url/$deviceID.json");
$device = json_decode($device_response, true);

if (!$device || !isset($device['defaultPassword'])) {
    echo json_encode(["success" => false, "message" => "Default password not found for device $deviceID"]);
    exit;
}

$defaultPassword = $device['defaultPassword']; // Get default password
$deviceEmail = $device['email'] ?? null; // Get email from Devices table (if exists)

// Update password in Firebase Devices table
$update_password_url = "$devices_url/$deviceID/password.json";
$options = [
    "http" => [
        "header" => "Content-Type: application/json",
        "method" => "PUT",
        "content" => json_encode($defaultPassword)
    ]
];

$context = stream_context_create($options);
$password_update_response = file_get_contents($update_password_url, false, $context);

if ($password_update_response === FALSE) {
    echo json_encode(["success" => false, "message" => "Failed to update password"]);
    exit;
}

// Remove request from Requests table
$delete_request_url = "$requests_url/$deviceID.json";
$options_delete = [
    "http" => [
        "header" => "Content-Type: application/json",
        "method" => "DELETE"
    ]
];

$context_delete = stream_context_create($options_delete);
file_get_contents($delete_request_url, false, $context_delete);

// Send email using PHPMailer with SMTP
$mail = new PHPMailer(true);

try {
    // SMTP configuration
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP server
    $mail->SMTPAuth = true;
    $mail->Username = 'tubigmaticteam@gmail.com'; // Replace with your email
    $mail->Password = 'pcawzxvwgcrcasvj'; // Replace with your email password or App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Email setup
    $mail->setFrom('support@tubigmatic.com', 'TubigMatic Support');
    
    // Add user email
    $mail->addAddress($userEmail);

    // If device email exists and is different, add it as well
    if ($deviceEmail && $deviceEmail !== $userEmail) {
        $mail->addAddress($deviceEmail);
    }

    $mail->isHTML(true);
    $mail->Subject = "Your TubigMatic Password Has Been Reset";
    $mail->Body = "
        <html>
        <head>
            <title>Your TubigMatic Password Has Been Reset</title>
        </head>
        <body>
            <p>Good day,</p>
            <p>Your Device password reset request has been successfully processed!</p>
            <p>We've set your password back to its default.</p>
            <p>Thank you for using TubigMatic!</p>
            <p>                   - TubigMatic Team</p>
        </body>
        </html>
    ";

    // Send the email
    if ($mail->send()) {
        echo json_encode(["success" => true, "message" => "Password reset successfully! Email notification sent."]);
    } else {
        echo json_encode(["success" => false, "message" => "Password reset but failed to send email notification."]);
    }
    } catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Mail error: " . $mail->ErrorInfo]);
    }
?>
