<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Devices</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      display: flex;
      margin: 0;
      flex-direction: column;
    }

    /* Top Navbar */
    .navbar {
      background-color: #3E6606;
      height: 50px;
      width: 100%;
      position: fixed;
      top: 0;
      left: 0;
      z-index: 1000; 
    }

    li a {
      text-decoration: none;
      color: #000000;
    }

    /* Side bar */
    .sidebar {
    width: 250px;
    background: #ffffff;
    height: 100vh; 
    padding: 0px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 2);
    position: fixed;
    top: 0; 
    left: 0;
    overflow-y: auto;
    }
    .sidebar ul li a {
      display: block;
      color: #000000;
      text-decoration: none;
      width: 100%;
      height: 100%;
    }
    .sidebar .logo img {
      width: 50px;
      margin-left: 10px;
      margin-top: 70px;
    }
    .sidebar h2 {
      margin-top: -40px;
      margin-left: 60px;
      margin-bottom: 50px;
    }
    .sidebar h4 {
      margin-top: -30px;
      margin-left: 80px;
      margin-bottom: 50px;
      color: #000000;
    }
    .sidebar ul {
      list-style: none;
      margin-left: 10px;
      padding: 0;
    }
    .sidebar ul li {
      padding: 15px;
      cursor: pointer;
      color: #000000;
      font-weight: bold;
      font-size: 15px;
      margin-top: 20px;
      display: flex;
      align-items: center;
    }
    .sidebar ul .active {
      background: #9FB383;
    }
    .sidebar ul li img {
      width: 20px;
      margin-right: 10px;
    }
    .sidebar ul li:hover {
      background: #c2d6a6;
      color: rgb(0, 0, 0);
      transition: 0.3s;
      border-radius: 5px;
    }

    /* Main Content */
    .main-content {
      flex: 1;
      padding: 20px;
      margin-left: calc(var(--sidebar-width, 280px));
    height: 100vh;
    overflow-y: auto; 
      margin-top: 60px;
    }

    /* Add Device Button */
    .add-device-btn {
      background-color: #3E6606;
      color: white;
      padding: 10px 15px;
      border: none;
      cursor: pointer;
      margin-left: 20px;
      border-radius: 3px;
      font-weight: bold;
      margin-top: 30px;
      margin-bottom: -20px;
    }
    .add-device-btn:hover {
      background-color: #2b4a04;
    }

    /* Modal Styling */
    .modal, .modal1 {
      display: none;
      position: fixed;
      z-index: 1;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
    }
    .modal-content, .modal-content1{
      background-color: #ffffff;
      margin: 10% auto;
      padding: 15px;
      border-radius: 5px;
      width: 30%;
      text-align: center;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }
    .modal-content {
      height: auto; 
    }
    .modal-content h4 {
      margin-bottom: 40px;
      text-align: left;
      margin-left: 20px;
      font-size: 18px;
    }
    .modal-content label{
      width: 30%;
      text-align: left;
      font-size: 15px;
      font-weight: bold;
    }
    .modal-content input, .modal-content select {
      width: 65%;
      padding: 10px;
      border: 1px solid #b6b4b4;
      border-radius: 3px;
      box-sizing: border-box;
    }

    /* General styles */
    .input-group {
      width: 90%;
      display: flex;
      align-items: center;
      margin-left: 20px;
      justify-content: space-between;
      margin-bottom: 10px;
      position: relative;
    }
    .input-group input {
      width: 65%;
      padding: 10px;
      border: 1px solid #b6b4b4;
      border-radius: 3px;
      box-sizing: border-box;
    }

    /* Password overlay toggle style */
    .pwd-overlay {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      color: #999;
      font-size: 14px;
      cursor: pointer;
      user-select: none;
    }

    /* Error message style */
    .error-message {
      color: red;
      font-size: 12px;
      margin-left: 60px;
      margin-bottom:5px;
    }
    
    /* Edit modal */
    .modal-content1 {
      height: auto;
    }
    #messageModal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 999;
    }
    .modal-content1 h3 {
      margin-bottom: 30px;
      text-align: left;
      font-size: 20px;
    }
    .input-group1 {
      width: 90%;
      display: flex;
      align-items: center;
      margin-left: 12px;
      justify-content: space-between;
      margin-bottom: 10px;
    }
    .modal-content1 label{
      width: 30%;
      text-align: left;
      font-weight: bold;
      font-size: 15px;
    }
    .modal-content1 input {
      width: 65%;
      padding: 10px;
      border: 1px solid #b6b4b4;
      border-radius: 3px;
      box-sizing: border-box;
    }
    .close-btn {
      background-color: red;
      color: white;
      padding: 10px;
      border: none;
      cursor: pointer;
      margin-top: 10px;
      margin-left: 80px;
      border-radius: 3px;
    }
    .save-btn {
      background-color: #3E6606;
      color: white;
      padding: 10px;
      border: none;
      cursor: pointer;
      margin-top: 10px;
      border-radius: 3px;
    }
    .close-btn:hover {
      background-color: darkred;
    }
    .save-btn:hover {
      background-color: #2b4a04;
    }
    .devices-container {
      margin-top: -10px;
    background: #ffffff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }
  
    /* Search Bar Styling */
    .search-bar {
      position: relative;
      width: 40%;
      max-width: 200px;
      margin: auto;
      margin-right: 19%;
      margin-top:-2px;
    }
    .search-bar input {
      width: 85%;
      padding: 12px 40px 12px 15px; 
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 15px;
      outline: none;
      transition: 0.3s;
    } 
    .search-bar input:focus {
      border-color: #3E6606;
      box-shadow: 0 0 5px rgba(62, 102, 6, 0.5);
    }
    .search-bar button {
      padding: 8px 12px;
      border: 1px solid #ccc;
      background: #ffffff;
      color: white;
      cursor: pointer;
      border-radius: 0 3px 3px 0;
    }
    .search-icon {
      position: absolute;
      right: -20px;
      top: 50%;
      transform: translateY(-50%);
      width: 20px;
      height: 20px;
      cursor: pointer;
      opacity: 0.6;
    }

    /* Filter */
    .filter-container {
      display: flex;
      justify-content: space-between;
      gap: 10px;
      margin-left:55%;
      margin-bottom: 10px;
      margin-top: -22px;
      border-radius: 15px;
    }
    #purchaseFilter {
      width: 150px;
      padding: 8px;
      border-radius: 10px;
      height: 38px;
    }
    #searchInput {
      width: 200px;
      padding: 8px;
      height: 22px;
      margin-right:20px;
    }

    /* Table */
    table {
    width: 100%;
    border-collapse: collapse;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
    overflow: hidden;
    border-radius: 10px;
    }
    th, td {
      padding: 15px;
      text-align: left;
    }
    th {
      background-color:#2E4A21;
      color: white;
      text-transform: uppercase;
    }
    tr:nth-child(even) {
      background-color: #f9f9f9;
    }
    tr:hover {
      background-color: #f1f1f1;
    }
    .edit-btn {
      background-color: #3E6606;
      color: white;
      border: none;
      padding:  10px;
      cursor: pointer;
      border-radius: 3px;
    }
    .default-btn {
      background-color: #004896;
      color: white;
      border: none;
      padding: 5px 10px;
      margin-right: -80px;
      cursor: pointer;
      border-radius: 3px;
    }
    .edit-btn:hover {
      background-color: #2b4a04;
    }
    .add-device-btn:hover {
      background-color: #2b4a04;
    }

    /* Toast Notification */
    .toast {
      visibility: hidden;
      min-width: 250px;
      margin-left: -125px;
      background-color: #3E6606;
      color: white;
      text-align: center;
      border-radius: 5px;
      padding: 16px;
      position: fixed;
      z-index: 1;
      left: 50%;
      bottom: 30px;
      font-size: 17px;
    }
    .toast.show {
      visibility: visible;
      animation: fadein 0.5s, fadeout 0.5s 2.5s;
    }
    @keyframes fadein {
      from {bottom: 0; opacity: 0;}
      to {bottom: 30px; opacity: 1;}
    }
    @keyframes fadeout {
      from {bottom: 30px; opacity: 1;}
      to {bottom: 0; opacity: 0;}
    }

    /* Pagination */
    .pagination {
      margin-top: 30px;
      text-align: right;
      display: flex;
      justify-content: flex-end;
      gap: 10px;
    }
    .pagination button {
      background-color:rgb(255, 255, 255);
      color: black;
      border: none;
      padding: 5px 10px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 2);
      margin: 0 3px;
      cursor: pointer;
      border-radius: 3px;
      transition: background-color 0.3s;
    }
    .pagination button.active {
      background: #3E6606;
      color: #ffffff; 
    }
    .pagination button:hover {
      background: #9FB383;
    }
  </style>

</head>
<body>
  <!-- Navigation Bar -->
  <div class="navbar"></div>
  
  <!-- Sidebar for navigation links -->
  <div class="sidebar">

    <div class="logo">
      <img src="logo.png" alt="TubigMatic Logo">
      <h2><span style="color:#006F9E">Tubig</span><span style="color:#3E6606">Matic</span></h2>
      <h4>Admin</h4>
    </div>
    <ul>
      <!-- Navigation links -->
      <li><a href="dashboard.php"><img src="icons/dashboard.png" alt="Dashboard Icon"> Dashboard</a></li>
      <li><a href="account.php"><img src="icons/account.png" alt="Accounts Icon"> Accounts</a></li>
      <li class="active"><a href="device.php"><img src="icons/phone.png" alt="Device Icon"> Devices</a></li>
      <li><a href="order.php"><img src="icons/order.png" alt="Order Icon"> Orders</a></li>
      <!-- Logout link with confirmation -->
      <li><a href="logout.php" id="logout" onclick="return confirmLogout();">
        <img src="icons/logout.png" alt="Logout Icon"> Logout
      </a></li>
    </ul>
  </div>

  <!-- Main content section -->
  <div class="main-content">
    <h2>Devices</h2>
    
    <!-- Button to open the "Add Device" modal -->
    <button class="add-device-btn" onclick="openModal()">+ ADD DEVICE</button>

    <!-- Filter and search functionality -->
    <div class="filter-container">
      <!-- Device purchase filter -->
      <select id="purchaseFilter" onchange="filterDevices()" style="display: none;">
        <option value="all">All</option>
        <option value="purchased">Purchased</option>
        <option value="not_purchased">Not yet purchased</option>
      </select>
      
      <!-- Search bar to filter devices by ID -->
      <div class="search-bar">
        <input type="text" id="searchInput" placeholder="Search Device ID..." onkeyup="searchDevices()">
        <img src="icons/search.png" alt="Search" class="search-icon">
      </div>
    </div>

    <!-- Modal for adding a new device -->
    <div id="addDeviceModal" class="modal">
      <div class="modal-content">
        <h4>Add New Device</h4>

        <!-- Device ID input field -->
        <div class="input-group">
          <label for="deviceID">Device ID:</label>
          <input type="text" id="deviceID" placeholder="Enter Device ID" oninput="handleDeviceIDInput()" disabled required>
        </div>
   
        <!-- Error message for invalid device ID -->
        <div id="deviceError" class="error-message" style="display:none;">Please add a number after "DVC".</div>
        
        <!-- Password input field with show/hide functionality -->
        <div class="input-group" style="position: relative;">
          <label for="devicePassword">Password:</label>
          <input type="password" id="devicePassword" placeholder="Password" required>
          <div id="pwdOverlay" class="pwd-overlay" onclick="togglePassword()">Show</div>
        </div>

        <!-- Email input field -->
        <div class="input-group">
          <label for="userEmail">Email:</label>
          <input type="email" id="userEmail" placeholder="Enter Email" required>
        </div>
        
        <br>
        <!-- Add button to save the new device -->
        <button class="save-btn" onclick="saveDevice()">ADD</button>
        <!-- Button to close the modal -->
        <button class="close-btn" onclick="closeModal()">CLOSE</button>
      </div>
    </div>

    <!-- Toast notification for successful password reset -->
    <div id="toast" class="toast">Password reset successfully!</div>

    <!-- Devices Table: Displays list of devices -->
    <div class="devices-container">
      <table id="devicesTable">
        <thead>
          <tr>
            <th>Device ID</th>
            <th>Password</th>
            <th>Email</th>
          </tr>
        </thead>
        <tbody id="devicesBody"></tbody>
      </table>
      <!-- Pagination controls for devices -->
      <div class="pagination" id="pagination"></div>
    </div>

    <!-- Reset Password Requests Section -->
    <h3>Reset Password Requests</h3>
    <div class="devices-container">
      <table id="resetRequestsTable">
        <thead>
          <tr>
            <th>Device ID</th>
            <th>Email</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody id="resetRequestsBody"></tbody>
      </table>
    </div>

    <!-- Edit Device Modal: Allows for editing device information -->
    <div id="editDeviceModal" class="modal1">
      <div class="modal-content1">
        <h3>Update User ID</h3>
        
        <!-- Displaying the device details for editing -->
        <div class="input-group1">
          <label>Device ID:</label>
          <input type="text" id="editDeviceID" disabled>
        </div>
        <div class="input-group1">
          <label>Password:</label>
          <input type="text" id="editDevicePassword" disabled>
        </div>
        <div class="input-group1">
          <label>Email:</label>
          <input type="text" id="editUserEmail">
        </div>
        
        <br>
        <!-- Buttons for saving or closing the edit -->
        <button class="save-btn" onclick="saveEdit()">EDIT</button>
        <button class="close-btn" onclick="closeEditModal()">CLOSE</button>
      </div>
    </div>

  </div>

  <!-- General Message Modal (e.g., success or error messages) -->
  <div id="messageModal" style="display:none;">
    <div class="modal-content">
      <p id="modalMessage"></p>
      <!-- Button to close the message modal -->
      <button onclick="closeMessageModal()">Close</button>
    </div>
  </div>
</body>



  <script>

  const devicesPerPage = 5;
  let currentPage = 1;
  let devices = []; // Store all devices here
  let filteredDevices = []; // Store filtered devices

  async function fetchDevices() {
    try {
      const response = await fetch("fetch_devices.php"); // Fetch from PHP API
      const devicesData = await response.json();

      devices = Object.entries(devicesData).map(([key, value]) => ({
        id: key, // Firebase key as Device ID
        password: value.password || "*********", // Masked password
        userId: value.userId || "Not yet purchased",
        email: value.email || "No email available", // Fetch email from Firebase
      }));

      filteredDevices = [...devices]; // Reset filtered devices
      currentPage = 1; // Reset to first page
      renderDevices();
    } catch (error) {
      console.error("Error fetching devices:", error);
    }
  }

  // Call fetchDevices when the page loads
  fetchDevices();

  // Devices Table
  function renderDevices() {
  const tbody = document.getElementById("devicesBody");
  tbody.innerHTML = "";

  const startIndex = (currentPage - 1) * devicesPerPage;
  const endIndex = startIndex + devicesPerPage;
  const devicesToShow = filteredDevices.slice(startIndex, endIndex);

  devicesToShow.forEach((device) => {
    const tr = document.createElement("tr");
    tr.innerHTML = `
      <td>${device.id}</td>
      <td>${device.password}</td>
      <td>${device.email}</td>
     
    `;
    tbody.appendChild(tr);
  });

  renderPagination();
  }

  // Pagination 
  function renderPagination() {
  const paginationDiv = document.getElementById("pagination");
  paginationDiv.innerHTML = "";
  const totalPages = Math.ceil(filteredDevices.length / devicesPerPage);

  // Previous Button
  const prevBtn = document.createElement("button");
  prevBtn.textContent = "Prev";
  prevBtn.disabled = currentPage === 1;
  prevBtn.onclick = () => {
    if (currentPage > 1) {
      currentPage--;
      renderDevices();
    }
  };
  paginationDiv.appendChild(prevBtn);

  // Page Number Buttons
  for (let i = 1; i <= totalPages; i++) {
    const btn = document.createElement("button");
    btn.textContent = i;
    btn.className = i === currentPage ? "active" : "";
    btn.onclick = () => {
      currentPage = i;
      renderDevices();
    };
    paginationDiv.appendChild(btn);
  }

  // Next Button
  const nextBtn = document.createElement("button");
  nextBtn.textContent = "Next";
  nextBtn.disabled = currentPage === totalPages;
  nextBtn.onclick = () => {
    if (currentPage < totalPages) {
      currentPage++;
      renderDevices();
    }
  };
  paginationDiv.appendChild(nextBtn);
  }
 
  // Filter Devices by Device ID
  function filterDevices() {
  const filterValue = document.getElementById("purchaseFilter").value;
  const searchQuery = document.getElementById("searchInput").value.toLowerCase();

  filteredDevices = devices.filter((device) => {
    const matchesSearch =
      device.id.toLowerCase().includes(searchQuery) ||
      device.email.toLowerCase().includes(searchQuery);
    const isPurchased = device.email !== "Not yet purchased";

    if (filterValue === "purchased") return isPurchased && matchesSearch;
    if (filterValue === "not_purchased") return !isPurchased && matchesSearch;
    return matchesSearch;
  });

  currentPage = 1;
  renderDevices();
  }

  // Search Devices
  function searchDevices() {
    filterDevices();
  }

  // Increment Device ID
  function fetchNextDeviceInfo() {
  fetch("add_device.php?nextDevice=true")
    .then((response) => response.json())
    .then((data) => {
      document.getElementById("deviceID").value = data.deviceID;
      document.getElementById("devicePassword").value = data.password;
    })
    .catch((error) => console.error("Error fetching next device info:", error));
  }

  // Modal for Add Device
  function openModal() {
    fetchNextDeviceInfo();
    document.getElementById("addDeviceModal").style.display = "block";
  }

  function closeModal() {
    document.getElementById("addDeviceModal").style.display = "none";
    clearFields();
  }

  // 'DVC' Format of Device ID
  function handleDeviceIDInput() {
    let input = document.getElementById("deviceID");
    let numericPart = input.value.replace(/^DVC/, "").replace(/\D/g, "");
    input.value = "DVC" + numericPart;
    let errorMsg = document.getElementById("deviceError");
    errorMsg.style.display = numericPart === "" ? "block" : "none";
  }

  // Toggle Password Show/Hide
  function togglePassword() {
    let passwordField = document.getElementById("devicePassword");
    let overlay = document.getElementById("pwdOverlay");
    passwordField.type = passwordField.type === "password" ? "text" : "password";
    overlay.textContent = passwordField.type === "password" ? "Show" : "Hide";
  }

  // Clear After Transaction
  function clearFields() {
    document.getElementById("deviceID").value = "";
    document.getElementById("devicePassword").value = "";
    document.getElementById("userID").value = "";
  }

  // Show the custom message modal
  function showMessageModal(message) {
    document.getElementById("modalMessage").textContent = message;
    document.getElementById("messageModal").style.display = "flex";
  }

  // Close the message modal
  function closeMessageModal() {
    document.getElementById("messageModal").style.display = "none";
  }

  // Save Device Functions
  function saveDevice() {
    let deviceID = document.getElementById("deviceID").value.trim();
    let password = document.getElementById("devicePassword").value.trim();
    let email = document.getElementById("userEmail").value.trim();
    let modal = document.getElementById("addDeviceModal");

    if (deviceID === "DVC" || !deviceID.includes("DVC")) {
      showMessageModal("Device ID must start with 'DVC' and include a valid number.");
      return;
    }

    if (!deviceID || !password || !email) {
      showMessageModal("Please fill out all required fields.");
      return;
    }

    // Validate email must contain '@gmail.com'
    if (!email.endsWith("@gmail.com")) {
      showMessageModal("Invalid email. Please use a @gmail.com address.");
      return; // Stop execution, modal remains open
    }

    fetch("add_device.php", {
      method: "POST",
      body: new URLSearchParams({
        deviceID,
        devicePassword: password,
        userEmail: email,
      }),
    })
      .then((response) => response.text())
      .then((data) => {
        showMessageModal(data);
        closeModal(); // Close modal only if request succeeds
      })
      .catch((error) => console.error("Error:", error));
  }

  window.onclick = function (event) {
    let modal = document.getElementById("addDeviceModal");
    if (event.target === modal) {
      closeModal();
    }
  };

  // Reset Password of Device Account
  function resetPassword(deviceID) {
    if (confirm("Are you sure you want to reset the password for " + deviceID + "?")) {
      document.getElementById("toast").classList.add("show");
      setTimeout(() => document.getElementById("toast").classList.remove("show"), 3000);
    }
  }

  // Edit Modal
  function openEditModal(deviceID, password, email) {
    document.getElementById("editDeviceID").value = deviceID;
    document.getElementById("editDevicePassword").value = password;
    document.getElementById("editUserEmail").value = email;
    document.getElementById("editDeviceModal").style.display = "block";
  }

  function closeEditModal() {
    document.getElementById("editDeviceModal").style.display = "none";
    document.getElementById("editDeviceID").value = "";
    document.getElementById("editDevicePassword").value = "";
    document.getElementById("editUserEmail").value = "";
  }

  function saveEdit() {
    let deviceID = document.getElementById("editDeviceID").value;
    let newEmail = document.getElementById("editUserEmail").value.trim();

    if (!newEmail) {
      showMessageModal("Email cannot be empty!");
      return;
    }

    // AJAX request to update the email in Firebase via PHP
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "edit_device.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
      if (xhr.readyState === 4) {
        showMessageModal(xhr.responseText);
        closeEditModal();
      }
    };
    
    xhr.send(`deviceID=${encodeURIComponent(deviceID)}&newEmail=${encodeURIComponent(newEmail)}`);
  }

  function confirmLogout() {
    return confirm("Are you sure you want to logout?");
  }

  filterDevices();


  //RequestDefaultPassword
  document.addEventListener("DOMContentLoaded", function () {
    fetchResetRequests();
  });

  // Fetch Reset Requests from Firebase
  function fetchResetRequests() {
    fetch("fetch_requests.php")
      .then((response) => response.json())
      .then((data) => {
        renderResetRequests(data);
      })
      .catch((error) => console.error("Error fetching requests:", error));
  }

  // Reset Requests Table 
  function renderResetRequests(resetRequests) {
    const tbody = document.getElementById("resetRequestsBody");
    tbody.innerHTML = "";

    resetRequests.forEach((request) => {
      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td>${request.id}</td>
        <td>${request.email}</td>
        <td>
          <button class="edit-btn" onclick="confirmReset('${request.id}')">Default</button>
        </td>
      `;
      tbody.appendChild(tr);
    });
  }

  // Reset Password of Device Accounts
  function confirmReset(deviceID) {
    if (confirm(`Are you sure you want to reset the password for ${deviceID}?`)) {
      fetch("reset_password.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ deviceID }),
      })
        .then((response) => response.json())
        .then((data) => {
          showMessageModal(data.message);
          fetchResetRequests();
        })
        .catch((error) => console.error("Error resetting password:", error));
    }
  }

  </script>

  </body>
  </html>
