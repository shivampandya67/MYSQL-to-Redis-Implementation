<!DOCTYPE html>
<html>
<head>
    <title>Employee Information</title>
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
        h4 {
            color: #333;
            margin-bottom: 20px;
        }
        .employee-info {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-top: 15px;
        }
    </style>
</head>
<body>

<div class="container">
    <h4>Employee Information</h4>
    <div class="employee-info">
        <?php
        try {
            // Connect to Redis
            $redis = new Redis();
            $redis->connect('redis', 6379);  // Use Redis container hostname

            // Check if 'ssn' is passed via GET
            if (isset($_GET['ssn'])) {
                $ssn = $_GET['ssn'];

                // Build the Redis key for the employee
                $key = "EMPLOYEE:" . $ssn;

                // Check if the key exists in Redis
                if ($redis->exists($key)) {
                    // Fetch employee details from Redis
                    $employee = $redis->hGetAll($key);

                    // Display employee information
                    echo "<div style='text-align:center; padding: 20px; border: 1px solid #ddd; border-radius: 5px; width: 300px; margin: auto;'>";
                    echo "<h2>Employee Information</h2>";
                    echo "Name: " . htmlspecialchars($employee['Fname']) . " " . 
                         htmlspecialchars($employee['Minit']) . " " . 
                         htmlspecialchars($employee['Lname']) . "<br>";
                    echo "SSN: " . htmlspecialchars($employee['Ssn']) . "<br>";
                    echo "Date of Birth: " . htmlspecialchars($employee['Bdate']) . "<br>";
                    echo "Address: " . htmlspecialchars($employee['Address']) . "<br>";
                    echo "Sex: " . htmlspecialchars($employee['Sex']) . "<br>";
                    echo "Salary: $" . htmlspecialchars($employee['Salary']) . "<br>";
                    echo "</div>";
                } else {
                    echo "No employee found with SSN: $ssn in Redis.";
                }
            } else {
                echo "SSN parameter is missing in the request.";
            }
        } catch (Exception $e) {
            // Handle Redis connection error
            echo "Could not connect to Redis: " . htmlspecialchars($e->getMessage());
        }
        ?>
    </div>
</div>

</body>
</html>

