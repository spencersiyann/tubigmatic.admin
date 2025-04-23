<?php

    // Firebase Realtime Database URL
    define('FIREBASE_URL', 'https://tubigmatic-admin-default-rtdb.firebaseio.com/');

    // Function to get the last device ID and determine the next one
    function getNextDeviceInfo() {
    $url = FIREBASE_URL . "Devices.json"; // Get all devices

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $devices = json_decode($response, true);

    if (!$devices) {
        return ['deviceID' => 'DVC001', 'password' => 'TubigMaticDVC001']; // Start from DVC001
    }

    // Find the highest numbered device (DVC001, DVC002, ...)
    $maxNumber = 0;
    foreach ($devices as $key => $device) {
        if (isset($device['deviceID']) && preg_match('/DVC(\d+)/', $device['deviceID'], $matches)) {
            $num = (int)$matches[1];
            if ($num > $maxNumber) {
                $maxNumber = $num;
            }
        }
    }

    $nextNumber = str_pad($maxNumber + 1, 3, '0', STR_PAD_LEFT);
    $nextDeviceID = "DVC" . $nextNumber;
    $nextPassword = "TubigMatic" . $nextDeviceID;

    return ['deviceID' => $nextDeviceID, 'password' => $nextPassword];
    }

    // Handling request to fetch next available device ID & password
    if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['nextDevice'])) {
        echo json_encode(getNextDeviceInfo());
        exit;
    }

    // Function to add device to Firebase
    function addDeviceToFirebase($deviceID, $password, $email) {
    $deviceKey = "DVC" . substr($deviceID, 3); // Convert DVC001 -> device1
    $url = FIREBASE_URL . "Devices/$deviceKey.json"; // Save under key

    $data = [
        'deviceID' => $deviceID,
        'password' => $password,
        'email' => $email,
        'defaultPassword' => $password
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true) ? "Device added successfully!" : "Error adding device.";
    }

    // Handling "Add Device" form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $deviceID = $_POST['deviceID'];
    $password = $_POST['devicePassword'];
    $email = $_POST['userEmail'];

    if (strpos($deviceID, 'DVC') === false) {
        echo "Invalid device ID. It must start with 'DVC'.";
        exit;
    }

    if (empty($deviceID) || empty($password) || empty($email)) {
        echo "Please fill out all required fields.";
        exit;
    }

    // Add device to Firebase
    echo addDeviceToFirebase($deviceID, $password, $email);
    }

?>
