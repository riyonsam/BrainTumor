<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Data</title>
    <!-- Link to the CSS file -->
    <link rel="stylesheet" href="database.css">
</head>

<body>
    <h2>Patient Data</h2>
    <!-- HTML Table -->
    <table id="patient-table">
        <!-- Table headers -->
        <tr>
            <th>ID</th>
            <th>Patient Name</th>
            <th>Age</th>
            <th>Sex</th>
            <th>Phone Number</th>
            <th>Result</th>
        </tr>
        <!-- PHP code to generate table rows -->
        <?php
        // Enable error reporting for development
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        // Database connection
        $host = 'localhost';
        $username = 'root';
        $password = '';
        $dbname = 'hospital_db';

        $conn = new mysqli($host, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die('Connection failed: ' . $conn->connect_error);
        }

        // Retrieve data from the 'patient' table
        $sql = 'SELECT id, pname, age, sex, phone, result FROM patient';
        $result = $conn->query($sql);

        // Check if there is any data
        if ($result->num_rows > 0) {
            // Output each row as a table row
            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['id']) . '</td>';
                echo '<td>' . htmlspecialchars($row['pname']) . '</td>';
                echo '<td>' . htmlspecialchars($row['age']) . '</td>';
                echo '<td>' . htmlspecialchars($row['sex']) . '</td>';
                echo '<td>' . htmlspecialchars($row['phone']) . '</td>';
                echo '<td>' . htmlspecialchars($row['result']) . '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="6">No patient data found.</td></tr>';
        }

        // Close the database connection
        $conn->close();
        ?>
    </table>
</body>

</html>
