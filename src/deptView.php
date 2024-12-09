<!DOCTYPE html>
<html>
<head>
    <title>Department View</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f9;
            margin: 0;
        }
        .container {
            width: 80%;
            max-width: 600px;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h3, h4 {
            color: #333;
            margin-bottom: 15px;
        }
        form {
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
            margin-right: 10px;
        }
        input[type="number"], input[type="submit"] {
            padding: 8px;
            font-size: 16px;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        td a {
            color: #4CAF50;
            text-decoration: none;
        }
        td a:hover {
            text-decoration: underline;
        }
        .section-title {
            font-weight: bold;
            color: #555;
            margin-top: 20px;
        }
        .no-data {
            color: #777;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h3>Enter Department Number</h3>
    <form method="GET" action="deptView.php">
        <label for="dno">Department Number:</label>
        <input type="number" id="dno" name="dno" required>
        <input type="submit" value="Submit">
    </form>

    <?php
    try {
        // Connect to Redis
        $redis = new Redis();
        $redis->connect('redis', 6379); // Connect to Redis container

        if (isset($_GET['dno'])) {
            $dno = $_GET['dno'];

            // Build the Redis key for the department
            $dept_key = "DEPARTMENT:$dno";

            // Check if the department exists in Redis
            if ($redis->exists($dept_key)) {
                $department = $redis->hGetAll($dept_key);

                // Display department information
                $dname = htmlspecialchars($department["Dname"]);
                $mssn = htmlspecialchars($department["Mgr_ssn"]);
                $mstart = htmlspecialchars($department["Mgr_start_date"]);

                echo "<h4>Department: $dname</h4>";
                echo "<p>Manager SSN: <a href=\"p1.php?ssn=$mssn\">$mssn</a></p>";
                echo "<p>Manager Start Date: $mstart</p>";

                // Query department locations
                echo "<h4 class='section-title'>Department Locations:</h4>";
                $locations = $redis->keys("DEPT_LOCATION:$dno:*");

                if (!empty($locations)) {
                    foreach ($locations as $location_key) {
                        $location_data = $redis->hGetAll($location_key);
                        echo htmlspecialchars($location_data['Location']) . "<br>";
                    }
                } else {
                    echo "<p class='no-data'>No locations found.</p>";
                }

                // Query employees in the department
                echo "<h4 class='section-title'>Employees:</h4>";
                $employee_keys = $redis->keys("EMPLOYEE:*");
                $found_employees = false;

                echo "<table>";
                echo "<tr><th>Employee SSN</th><th>Last Name</th><th>First Name</th></tr>";
                foreach ($employee_keys as $emp_key) {
                    $employee = $redis->hGetAll($emp_key);
                    if ($employee['Dno'] == $dno) {
                        $found_employees = true;
                        echo "<tr>";
                        echo "<td><a href=\"p1.php?ssn=" . htmlspecialchars($employee['Ssn']) . "\">" . htmlspecialchars($employee['Ssn']) . "</a></td>";
                        echo "<td>" . htmlspecialchars($employee['Lname']) . "</td>";
                        echo "<td>" . htmlspecialchars($employee['Fname']) . "</td>";
                        echo "</tr>";
                    }
                }
                echo "</table>";
                if (!$found_employees) {
                    echo "<p class='no-data'>No employees found.</p>";
                }

                // Query projects in the department
                echo "<h4 class='section-title'>Projects:</h4>";
                $project_keys = $redis->keys("PROJECT:*");
                $found_projects = false;

                echo "<table>";
                echo "<tr><th>Project Number</th><th>Project Name</th><th>Location</th></tr>";
                foreach ($project_keys as $proj_key) {
                    $project = $redis->hGetAll($proj_key);
                    if ($project['Dnum'] == $dno) {
                        $found_projects = true;
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($project['Pnumber']) . "</td>";
                        echo "<td>" . htmlspecialchars($project['Pname']) . "</td>";
                        echo "<td>" . htmlspecialchars($project['Plocation']) . "</td>";
                        echo "</tr>";
                    }
                }
                echo "</table>";
                if (!$found_projects) {
                    echo "<p class='no-data'>No projects found.</p>";
                }
            } else {
                echo "<p class='no-data'>No department found with number $dno.</p>";
            }
        } else {
            echo "<p class='no-data'>Please enter a department number above.</p>";
        }
    } catch (Exception $e) {
        echo "Could not connect to Redis: " . htmlspecialchars($e->getMessage());
    }
    ?>
</div>

</body>
</html>

