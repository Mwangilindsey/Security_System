<?php
include "DBO.php";

$recordsQuery = "SELECT * FROM gate_records ORDER BY record_id DESC";
$recordsResult = $conn->query($recordsQuery);

$totalEntries = $conn->query("SELECT COUNT(*) AS total FROM gate_records")->fetch_assoc()['total'];
$visitorCount = $conn->query("SELECT COUNT(*) AS total FROM gate_records WHERE category='Visitor' AND time_out IS NULL")->fetch_assoc()['total'];
$staffCount = $conn->query("SELECT COUNT(*) AS total FROM gate_records WHERE category='Staff' AND time_out IS NULL")->fetch_assoc()['total'];
$busDriversCount = $conn->query("SELECT COUNT(*) AS total FROM gate_records WHERE category='Bus Driver' AND time_out IS NULL")->fetch_assoc()['total'];
$pendingAlertsCount = $conn->query("SELECT COUNT(*) AS total FROM gate_records WHERE time_out IS NULL")->fetch_assoc()['total'];
?>
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Jonathan Gloag Academy Security System</title>

<link rel="stylesheet" href="css/style.css">
</head>

<body>

<!-- LOGIN PAGE -->
<div class="login-page" id="loginPage">
    <div class="container">
        <h2>Jonathan Gloag Academy</h2>
        <h3>Security System Login</h3>

        <input type="text" id="username" placeholder="Username">
        <input type="password" id="password" placeholder="Password">

        <button onclick="login()">Login</button>

        <p>
            Don't have an account?
            <a href="#" onclick="showSignup()">Sign Up</a>
        </p>

        <p>
            <a href="#" onclick="forgotPassword()">Forgot Password?</a>
        </p>

        <p id="loginMsg"></p>
    </div>
</div>

<!-- SIGN UP PAGE -->
<div class="login-page hidden" id="signupPage">
    <div class="container">
        <h2>Create Account</h2>

        <input type="text" id="newUsername" placeholder="Create Username">
        <input type="password" id="newPassword" placeholder="Create Password">

        <select id="newRole">
            <option value="security">Security Officer</option>
            <option value="admin">Admin</option>
        </select>

        <button onclick="signup()">Create Account</button>

        <p>
            Already have an account?
            <a href="#" onclick="backToLogin()">Login</a>
        </p>
    </div>
</div>

<!-- ADMIN DASHBOARD -->
<div class="dashboard hidden" id="adminDashboard">
    <h1>Admin Dashboard</h1>
    <p>Welcome, Admin</p>

    <div class="stats">

        <div class="stat-box">
            <h3>Visitors On-Site</h3>
            <p id="visitorCount"><?php echo $visitorCount; ?></p>
        </div>

        <div class="stat-box">
            <h3>Staff Present</h3>
            <p id="staffCount"><?php echo $staffCount; ?></p>
        </div>

        <div class="stat-box">
            <h3>Bus Drivers Present</h3>
            <p id="busDriversCount"><?php echo $busDriversCount; ?></p>
        </div>

        <div class="stat-box">
            <h3>Total Records</h3>
            <p id="totalEntries"><?php echo $totalEntries; ?></p>
        </div>

        <div class="stat-box">
            <h3>Pending Alerts</h3>
            <p id="pendingAlertsCount"><?php echo $pendingAlertsCount; ?></p>
        </div>

    </div>

    <div class="card">
        <h2>Records</h2>

        <table>
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Category</th>
                    <th>ID Number</th>
                    <th>Time In</th>
                    <th>Time Out</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody id="recordsTable">
                <?php while ($record = $recordsResult->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($record['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($record['category']); ?></td>
                        <td><?php echo htmlspecialchars($record['id_number']); ?></td>
                        <td><?php echo htmlspecialchars($record['time_in']); ?></td>
                        <td>
                            <?php 
                            if ($record['time_out']) {
                                echo htmlspecialchars($record['time_out']);
                            } else {
                                echo "Still Inside";
                            }
                            ?>
                        </td>
                        <td>
                            <?php 
                            if ($record['time_out']) {
                                echo "OUT";
                            } else {
                                echo "IN";
                            }
                            ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <button class="logout-btn" onclick="logout()">Logout</button>
</div>

<!-- SECURITY OFFICER DASHBOARD -->
<div class="dashboard hidden" id="securityDashboard">
    <h1>Security Officer Dashboard</h1>
    <p>Welcome, Security Officer</p>

    <div class="card">
        <h2>Record Entry / Exit</h2>

        <form action="save_record.php" method="POST">

            <input type="text"
                   name="full_name"
                   id="fullName"
                   placeholder="Full Name"
                   required>

            <select name="category"
                    id="category"
                    required>
                <option value="Visitor">Visitor</option>
                <option value="Staff">Staff</option>
                <option value="Bus Driver">Bus Driver</option>
            </select>

            <input type="text"
                   name="id_number"
                   id="idNumber"
                   placeholder="ID / Registration Number"
                   required>

            <button type="submit"
                    name="action"
                    value="entry">
                Record Entry
            </button>

            <button type="submit"
                    name="action"
                    value="exit">
                Record Exit
            </button>

        </form>
    </div>

    <div class="card">
        <h2>Security Activity Log</h2>

        <div class="log" id="activityLog">
            <strong>Activity Log:</strong><br>
            Records are now saved in the database.
        </div>
    </div>

    <button class="logout-btn" onclick="logout()">Logout</button>
</div>

<script>
let users = [
    { username: "security", password: "admin123", role: "admin" },
    { username: "security", password: "security123", role: "security" }
];

function showSignup() {
    document.getElementById("loginPage").classList.add("hidden");
    document.getElementById("signupPage").classList.remove("hidden");
}

function backToLogin() {
    document.getElementById("signupPage").classList.add("hidden");
    document.getElementById("loginPage").classList.remove("hidden");
}

function signup() {
    const username = document.getElementById("newUsername").value;
    const password = document.getElementById("newPassword").value;
    const role = document.getElementById("newRole").value;

    if (!username || !password) {
        alert("Please fill all fields.");
        return;
    }

    const existingUser = users.find(user => user.username === username);

    if (existingUser) {
        alert("Username already exists.");
        return;
    }

    users.push({
        username: username,
        password: password,
        role: role
    });

    alert("Account created successfully.");

    document.getElementById("newUsername").value = "";
    document.getElementById("newPassword").value = "";

    backToLogin();
}

function forgotPassword() {
    const username = prompt("Enter your username:");

    if (!username) {
        return;
    }

    const user = users.find(user => user.username === username);

    if (!user) {
        alert("Account not found.");
        return;
    }

    alert("Your password is: " + user.password);
}

function login() {
    const username = document.getElementById("username").value;
    const password = document.getElementById("password").value;

    const user = users.find(user =>
        user.username === username &&
        user.password === password
    );

    if (!user) {
        document.getElementById("loginMsg").innerText =
            "Invalid username or password";
        return;
    }

    localStorage.setItem("loggedInUser", username);
    localStorage.setItem("loggedInRole", user.role);

    document.getElementById("loginPage").classList.add("hidden");

    if (user.role === "admin") {
        document.getElementById("adminDashboard").classList.remove("hidden");
    } 
    else if (user.role === "security") {
        document.getElementById("securityDashboard").classList.remove("hidden");
    }
}

function logout() {
    localStorage.removeItem("loggedInUser");
    localStorage.removeItem("loggedInRole");

    document.getElementById("loginPage").classList.remove("hidden");
    document.getElementById("signupPage").classList.add("hidden");
    document.getElementById("adminDashboard").classList.add("hidden");
    document.getElementById("securityDashboard").classList.add("hidden");

    document.getElementById("username").value = "";
    document.getElementById("password").value = "";
    document.getElementById("loginMsg").innerText = "";
}

window.onload = function() {
    const role = localStorage.getItem("loggedInRole");

    if (role === "security") {
        document.getElementById("loginPage").classList.add("hidden");
        document.getElementById("securityDashboard").classList.remove("hidden");
    }

    if (role === "admin") {
        document.getElementById("loginPage").classList.add("hidden");
        document.getElementById("adminDashboard").classList.remove("hidden");
    }
};

</script>

</body>
</html>
