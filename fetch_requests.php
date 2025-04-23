<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Firebase Realtime Database URL
$firebase_url = "https://tubigmatic-admin-default-rtdb.firebaseio.com/Requests.json";

// Fetch data from Firebase
$response = file_get_contents($firebase_url);

// Check if response is valid
if ($response === FALSE) {
    echo json_encode(["error" => "Failed to fetch data from Firebase"]);
    exit;
}

// Decode JSON response
$requests = json_decode($response, true);

// Check if JSON decoding was successful
if ($requests === null) {
    echo json_encode(["error" => "Invalid JSON response from Firebase"]);
    exit;
}

// Process requests
$processedRequests = [];

foreach ($requests as $deviceID => $data) {
    $processedRequests[] = [
        "id" => $deviceID,
        "email" => $data['email'] ?? "No email available"
    ];
}

// Return JSON response
echo json_encode($processedRequests);
?>
