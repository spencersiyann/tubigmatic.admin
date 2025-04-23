// Firebase Configuration
const firebaseConfig = {
    apiKey: "AIzaSyBbMev-m-CRCOV4TpJ2if8RwvlCKkqik-w",
    authDomain: "tubigmatic-admin.firebaseapp.com",
    databaseURL: "https://tubigmatic-admin-default-rtdb.firebaseio.com",
    projectId: "tubigmatic-admin",
    storageBucket: "tubigmatic-admin.firebasestorage.app",
    messagingSenderId: "802346145214",
    appId: "1:802346145214:web:6163bd3abad6ae832415ef",
    measurementId: "G-VR0SC0NYVQ"
};

// Initialize Firebase
const app = firebase.initializeApp(firebaseConfig);
const database = firebase.database(app);

// Fetch Users from Firebase Realtime Database
function fetchUsers() {
    const usersRef = database.ref('Users');
    usersRef.once('value', (snapshot) => {
        const usersData = snapshot.val();
        if (usersData) {
            let usersArray = [];
            for (const userKey in usersData) {
                const user = usersData[userKey];
                // Push user data into the array
                usersArray.push({
                    id: userKey, // Firebase unique key as user ID
                    name: `${user.firstName} ${user.lastName}`,
                    email: user.email,
                    contact: user.contactNumber,
                    lastLogin: user.LastLogin || "N/A" // Fallback value if LastLogin is not available
                });
            }
            displayUsers(usersArray); // Display users in table
        }
    });
}

// Display Users in the Table
function displayUsers(users) {
    const userTable = document.getElementById('userTable');
    userTable.innerHTML = `
        <tr>
            <th>User ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Contact No.</th>
            <th>Last Login</th>
            <th>Action</th>
        </tr>
    `;

    // Populate the table with user data
    users.forEach(user => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${user.id}</td>
            <td>${user.name}</td>
            <td>${user.email}</td>
            <td>${user.contact}</td>
            <td>${user.lastLogin}</td>
            <td>
                <button class="delete-btn" onclick="confirmDelete('${user.id}')">DELETE</button>
            </td>
        `;
        userTable.appendChild(row);
    });
}

// Function to Show Delete Confirmation Modal
function confirmDelete(userID) {
    console.log("Delete requested for User ID:", userID);
    const modal = document.getElementById("deleteModal");
    modal.style.display = "block";

    // Store userID in the confirm button's dataset for use later
    document.getElementById("confirmDeleteBtn").setAttribute("data-userid", userID);
}

// Close Modal Function
function closeModal() {
    const modal = document.getElementById("deleteModal");
    modal.style.display = "none";
}

// Attach Event Listener to Confirm Delete Button in Modal
document.getElementById("confirmDeleteBtn").addEventListener("click", function () {
    const userID = this.getAttribute("data-userid");
    if (!userID) {
        alert("Invalid user ID.");
        return;
    }

    // Firebase DELETE request to remove user
    const deleteURL = `https://tubigmatic-admin-default-rtdb.firebaseio.com/Users/${userID}.json`;

    fetch(deleteURL, { method: "DELETE" })
        .then(response => {
            if (response.ok) {
                alert("User deleted successfully.");
                location.reload(); // Reload the page to reflect changes
            } else {
                alert("Failed to delete user.");
            }
        })
        .catch(error => {
            console.error("Error deleting user:", error);
            alert("Error deleting user.");
        });

    closeModal(); // Close the modal after deletion
});

// Search Users Function
function searchUsers() {
    let input = document.getElementById("searchInput").value.toLowerCase();
    let rows = document.getElementById("userTable").getElementsByTagName("tr");

    // Filter rows based on the search input (name or email)
    for (let i = 1; i < rows.length; i++) {
        let name = rows[i].getElementsByTagName("td")[1].innerText.toLowerCase();
        let email = rows[i].getElementsByTagName("td")[2].innerText.toLowerCase();
        rows[i].style.display = (name.includes(input) || email.includes(input)) ? "" : "none";
    }
}

// Confirm Logout Function
function confirmLogout() {
    return confirm("Are you sure you want to logout?");
}

// Load Users on Page Load
document.addEventListener('DOMContentLoaded', fetchUsers);
