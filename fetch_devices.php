<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Firebase Realtime Database URL
$firebase_url = "https://tubigmatic-admin-default-rtdb.firebaseio.com/Devices.json";

// Fetch data from Firebase
$response = file_get_contents($firebase_url);

// Check if response is valid
if ($response === FALSE) {
    echo json_encode(["error" => "Failed to fetch data from Firebase"]);
    exit;
}

// Decode JSON response
$devices = json_decode($response, true);

// Check if JSON decoding was successful
if ($devices === null) {
    echo json_encode(["error" => "Invalid JSON response from Firebase"]);
    exit;
}

// Process devices
$processedDevices = [];

foreach ($devices as $key => $device) {
    $processedDevices[$key] = [
        "id" => $key,  // Store Firebase key as Device ID
        "password" => isset($device['password']) ? str_repeat('*', strlen($device['password'])) : "*********",
        "email" => isset($device['email']) && !empty($device['email']) ? $device['email'] : "No email available"
    ];
}

// Return JSON response
echo json_encode($processedDevices);
?>
