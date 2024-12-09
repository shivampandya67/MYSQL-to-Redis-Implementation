<!DOCTYPE html>
<html>
<head>
    <title>Select Employee SSN</title>
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
        h3 {
            color: #333;
            margin-bottom: 20px;
        }
        select {
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
    </style>
</head>
<body>

<div class="container">
    <h3>Employee Details for:</h3>
    <form method="GET" action="p1.php">
        <label for="ssn">Select SSN:</label>
        <select name="ssn" id="ssn" required>
            <?php
                // Connect to Redis
                $redis = new Redis();
                $redis->connect('redis', 6379);  // Redis container hostname

                // Fetch all employee keys
                $keys = $redis->keys('EMPLOYEE:*');  // Get all employee SSNs

                if (empty($keys)) {
                    echo "<option disabled>No employees found</option>";
                } else {
                    foreach ($keys as $key) {
                        // Extract SSN from the key (EMPLOYEE:<ssn>)
                        $ssn = str_replace('EMPLOYEE:', '', $key);
                        echo "<option value=\"$ssn\">$ssn</option>";
                    }
                }
            ?>
        </select>
        <input type="submit" value="Get Employee Details">
    </form>
</div>

</body>
</html>

