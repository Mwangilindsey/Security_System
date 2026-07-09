<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != "security") {
    header("Location: index.php");
    exit();
}

$insidePeopleQuery = "SELECT * FROM gate_records WHERE time_out IS NULL ORDER BY time_in DESC";
$insidePeopleResult = $conn->query($insidePeopleQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Security Officer Dashboard</title>

<link rel="stylesheet" href="css/style.css">
</head>

<body>

<div class="dashboard" id="securityDashboard">
    <h1>Security Officer Dashboard</h1>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></p>

    <div class="card">
        <h2>Record Entry</h2>

        <form action="save_record.php" method="POST">

            <input type="text"
       id="full_name"
       name="full_name"
       placeholder="Full Name"
       required>

<small id="fullNameError" style="color:red;"></small>

            <select name="category" required>
                <option value="Visitor">Visitor</option>
                <option value="Staff">Staff</option>
                <option value="Bus Driver">Bus Driver</option>
            </select>
            

<input
    type="email"
    name="email"
    placeholder="Visitor Email"
    required>

<br><br>


<input
    type="tel"
    name="phone"
    placeholder="Phone Number (0712345678)"
    required
    pattern="^(07|01)[0-9]{8}$"
    title="Enter a valid Kenyan phone number starting with 07 or 01.">


    <label>Purpose of Entry</label>
            <select name="purpose" required>
                <option value="">-- Select Purpose --</option>
                <option value="School Event">School Event</option>
                <option value="Admission">Admission</option>
                <option value="Staff Meeting">Staff Meeting</option>
                <option value="Service/Delivery">Service/Delivery</option>
                <option value="Staff Member">Staff Member</option>
            </select>

            <button type="submit"
                    name="action"
                    value="entry">
                Record Entry
            </button>

        </form>
    </div>

    <div class="card">
        <h2>People Currently Inside</h2>

        <table>
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Category</th>
                    <th>Purpose of Entry</th>
                    <th>ID Number</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Time In</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($person = $insidePeopleResult->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($person['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($person['category']); ?></td>
                        <td><?php echo htmlspecialchars($person['purpose']); ?></td>
                        <td><?php echo htmlspecialchars($person['id_number']); ?></td>
                        <td><?php echo htmlspecialchars($person['email']); ?></td>
                        <td><?php echo htmlspecialchars($person['phone']); ?></td>
                        <td><?php echo htmlspecialchars($person['time_in']); ?></td>
                        <td>
                            <form action="save_record.php" method="POST">
                                <input type="hidden" name="action" value="exit">
                                <input type="hidden" name="record_id" value="<?php echo $person['record_id']; ?>">

                                <button type="submit">Record Exit</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    
    <a href="generate_report.php">
    <button type="button">Generate Report</button>
    </a>
    <a href="logout.php">
        <button class="logout-btn">Logout</button>
    </a>
</div>

<script>
const fullName = document.getElementById("full_name");
const fullNameError = document.getElementById("fullNameError");

fullName.addEventListener("input", function () {

    const value = fullName.value;

    if (/[^A-Za-z ]/.test(value)) {
        fullNameError.textContent = "Wrong input! Numbers and special characters are not allowed.";
    } else {
        fullNameError.textContent = "";
    }

});
</script>

</body>
</html>