<!DOCTYPE html>
<html>
<head>
    <title>All Departments</title>
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
            text-align: center;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h4 {
            color: #333;
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
    </style>
</head>
<body>

<div class="container">
    <h4>Departments of the Company</h4>
    <table>
        <tr>
            <th>Department Number</th> 
            <th>Department Name</th>
        </tr>
        
        <?php
        try {
            // Connect to Redis
            $redis = new Redis();
            $redis->connect('redis', 6379); // Use the Redis container hostname

            // Fetch all department keys
            $dept_keys = $redis->keys("DEPARTMENT:*");

            if (!empty($dept_keys)) {
                foreach ($dept_keys as $dept_key) {
                    // Get department details
                    $department = $redis->hGetAll($dept_key);

                    // Extract department number and name
                    $dno = htmlspecialchars($department['Dnumber']);
                    $dname = htmlspecialchars($department['Dname']);

                    // Display department details in a table row
                    echo "<tr>
                            <td><a href=\"deptView.php?dno=$dno\">$dno</a></td>
                            <td>$dname</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='2'>No departments found.</td></tr>";
            }
        } catch (Exception $e) {
            echo "<tr><td colspan='2'>Error: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
        }
        ?>
    </table>
</div>

</body>
</html>

