<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: index.php");
    exit();
}

$recordsQuery = "SELECT * FROM gate_records ORDER BY record_id DESC";
$recordsResult = $conn->query($recordsQuery);

$totalEntries = $conn->query("SELECT COUNT(*) AS total FROM gate_records")->fetch_assoc()['total'];
$visitorCount = $conn->query("SELECT COUNT(*) AS total FROM gate_records WHERE category='Visitor' AND time_out IS NULL")->fetch_assoc()['total'];
$staffCount = $conn->query("SELECT COUNT(*) AS total FROM gate_records WHERE category='Staff' AND time_out IS NULL")->fetch_assoc()['total'];
$busDriversCount = $conn->query("SELECT COUNT(*) AS total FROM gate_records WHERE category='Bus Driver' AND time_out IS NULL")->fetch_assoc()['total'];
$pendingAlertsCount = $conn->query("SELECT COUNT(*) AS total FROM gate_records WHERE time_out IS NULL")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Admin Dashboard</title>

<link rel="stylesheet" href="css/style.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

<div class="dashboard" id="adminDashboard">
    <h1>Admin Dashboard</h1>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></p>

    <div class="card">
        <h2>Create Security Officer Account</h2>

        <?php if (isset($_GET['success'])) { ?>
            <p style="color:lightgreen;"><?php echo htmlspecialchars($_GET['success']); ?></p>
        <?php } ?>

        <?php if (isset($_GET['error'])) { ?>
            <p style="color:red;"><?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php } ?>

        <form action="create_security.php" method="POST">

    <input type="text"
           name="username"
           placeholder="Security Officer Username"
           required
           pattern="[A-Za-z0-9]{4,20}"
           title="Username must be 4-20 letters and numbers only.">

    <input type="password"
           name="password"
           placeholder="Security Officer Password"
           required
           minlength="6"
           title="Password must be at least 6 characters.">

    <input type="email"
           name="email"
           placeholder="Security Officer Email"
           required>

    <input type="tel"
           name="phone"
           placeholder="Phone Number (e.g. 0712345678)"
           required
           pattern="^(07|01)[0-9]{8}$"
           title="Enter a valid Kenyan phone number starting with 07 or 01.">

    <button type="submit">Create Security Officer</button>

</form>
    </div>

    <div class="card">
        <h2>Security Overview</h2>

        <div style="width:420px; margin:auto;">
            <canvas id="securityChart"></canvas>
        </div>
    </div>

    <div class="card">
        <h2>Records</h2>

        <table>
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Category</th>
                    <th>Purpose</th>
                    <th>ID Number</th>
                    <th>Time In</th>
                    <th>Time Out</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($record = $recordsResult->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($record['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($record['category']); ?></td>
                        <td><?php echo htmlspecialchars($record['purpose']); ?></td>
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
    <a href="generate_report.php">
    <button type="button">Generate Report</button>
</a>
    <a href="logout.php">
        <button class="logout-btn">Logout</button>
    </a>
    
</div>

<script>
const chartData = [
    <?php echo $visitorCount; ?>,
    <?php echo $staffCount; ?>,
    <?php echo $busDriversCount; ?>,
    <?php echo $totalEntries; ?>,
    <?php echo $pendingAlertsCount; ?>
];

const chartTotal = chartData.reduce((sum, value) => sum + value, 0);

new Chart(document.getElementById("securityChart"), {
    type: "doughnut",
    data: {
        labels: [
            "Visitors On-Site",
            "Staff Present",
            "Bus Drivers Present",
            "Total Entries",
            "Pending Alerts"
        ],
        datasets: [{
            data: chartData,
            backgroundColor: [
                "#3b82f6",
                "#10b981",
                "#f59e0b",
                "#8b5cf6",
                "#ef4444"
            ],
            borderColor: "#3f495b",
            borderWidth: 3
        }]
    },
    options: {
        responsive: true,
        cutout: "65%",
        plugins: {
            legend: {
                position: "bottom",
                labels: {
                    color: "white",
                    font: {
                        size: 14
                    },
                    generateLabels: function(chart) {
                        const data = chart.data.datasets[0].data;

                        return chart.data.labels.map((label, index) => {
                            const value = data[index];
                            const percentage = chartTotal > 0
                                ? ((value / chartTotal) * 100).toFixed(1)
                                : 0;

                            return {
                                text: `${label}: ${value} (${percentage}%)`,
                                fillStyle: chart.data.datasets[0].backgroundColor[index],
                                strokeStyle: chart.data.datasets[0].backgroundColor[index],
                                index: index
                            };
                        });
                    }
                }
            }
        }
    }
});
</script>

<script>
const username = document.getElementById("username");
const error = document.getElementById("usernameError");

username.addEventListener("input", function () {

    const value = username.value;

    if (/[^A-Za-z ]/.test(value)) {
        error.textContent = "Wrong input! Numbers and special characters are not allowed.";
    } else {
        error.textContent = "";
    }

});
</script>

</body>
</html>