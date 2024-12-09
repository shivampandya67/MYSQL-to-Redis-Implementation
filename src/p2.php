<!DOCTYPE html>
<html>
<head>
    <title>Department Employee Details</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
        }
        .container {
            text-align: center;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }
        h3, h4 {
            color: #333;
            margin-bottom: 20px;
        }
        form {
            margin-bottom: 15px;
        }
        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        input[type="number"] {
            padding: 8px;
            width: 100%;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        input[type="submit"] {
            padding: 10px 15px;
            font-size: 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        td {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>

<div class="container">
    <h3>Enter Department Number</h3>
    <form method="GET" action="p2.php">
        <label for="dno">Department Number:</label>
        <input type="number" id="dno" name="dno" required>
        <input type="submit" value="Get Employee Details">
    </form>

    <?php
    try {
        // Connect to Redis
        $redis = new Redis();
        $redis->connect('redis', 6379);  // Use Redis container hostname

        if (isset($_GET['dno'])) {
            $dno = $_GET['dno'];

            // Debugging: Show the department number
            echo "<h4>Employees in Department $dno</h4>";

            // Redis keys are in the format EMPLOYEE:<ssn>
            $keys = $redis->keys('EMPLOYEE:*');  // Get all employee keys

            $found = false;  // Flag to check if employees are found

            echo "<table>";
            echo "<tr><th>Last Name</th><th>Salary</th></tr>";

            foreach ($keys as $key) {
                // Get the employee data
                $employee = $redis->hGetAll($key);

                // Check if the employee belongs to the requested department
                if (isset($employee['Dno']) && $employee['Dno'] == $dno) {
                    $found = true;
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($employee['Lname']) . "</td>";
                    echo "<td>$" . htmlspecialchars($employee['Salary']) . "</td>";
                    echo "</tr>";
                }
            }

            if (!$found) {
                echo "<p>No employees found in department $dno.</p>";
            }

            echo "</table>";
        } else {
            echo "<p>Please enter a department number above.</p>";
        }
    } catch (Exception $e) {
        echo "Could not connect to Redis: " . htmlspecialchars($e->getMessage());
    }
    ?>
</div>

</body>
</html>

