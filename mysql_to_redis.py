import mysql.connector
import redis
from datetime import datetime, date
from decimal import Decimal

# MySQL connection setup
mysql_config = {
    'host': 'localhost',          # MySQL container hostname (or 'localhost' if running locally)
    'port': 3307,                 # Port mapped to MySQL container
    'user': 'root',
    'password': 'rootpassword',   # MySQL root password
    'database': 'mydatabase'      # The database name
}

# Redis connection setup
redis_conn = redis.Redis(host='localhost', port=6379, decode_responses=True)

# Function to convert MySQL data types into Redis-compatible formats
def convert_mysql_to_redis(row):
    for key, value in row.items():
        if isinstance(value, (datetime, date)):  # Handle both datetime and date
            row[key] = str(value)  # Convert datetime/date to string
        elif isinstance(value, Decimal):  # Handle DECIMAL type (MySQL)
            row[key] = float(value)  # Convert DECIMAL to float
        elif isinstance(value, float):  # For DECIMAL type, store as float
            row[key] = float(value)
        elif isinstance(value, int):  # For INT type, store as int (already valid)
            row[key] = int(value)
        elif isinstance(value, str):  # For VARCHAR type, ensure it's a string (valid in Redis)
            row[key] = str(value)
        elif value is None:  # Handle NULL values (MySQL NULL values get converted to "NULL" string in Redis)
            row[key] = "NULL"
    return row

# Connect to MySQL
try:
    mysql_conn = mysql.connector.connect(**mysql_config)
    mysql_cursor = mysql_conn.cursor(dictionary=True)
    print("Connected to MySQL")

    # Check Redis connection
    try:
        redis_conn.ping()  # Ping Redis to check the connection
        print("Connected to Redis")
    except redis.ConnectionError:
        print("Failed to connect to Redis")

    # Fetch data from MySQL and load into Redis

    # Load EMPLOYEE table into Redis
    mysql_cursor.execute("SELECT * FROM EMPLOYEE")
    employees = mysql_cursor.fetchall()

    for employee in employees:
        employee = convert_mysql_to_redis(employee)  # Convert MySQL types to Redis-compatible types
        key = f"EMPLOYEE:{employee['Ssn']}"  # Use SSN as the Redis key
        redis_conn.hset(key, mapping=employee)
        print(f"Inserted EMPLOYEE:{employee['Ssn']} into Redis")

    # Load DEPARTMENT table into Redis
    mysql_cursor.execute("SELECT * FROM DEPARTMENT")
    departments = mysql_cursor.fetchall()
    for department in departments:
        department = convert_mysql_to_redis(department)  # Convert MySQL types
        key = f"DEPARTMENT:{department['Dnumber']}"  # Use department number as the Redis key
        redis_conn.hset(key, mapping=department)
        print(f"Inserted DEPARTMENT:{department['Dnumber']} into Redis")

    # Load DEPENDENT table into Redis
    mysql_cursor.execute("SELECT * FROM DEPENDENT")
    dependents = mysql_cursor.fetchall()
    for dependent in dependents:
        dependent = convert_mysql_to_redis(dependent)  # Convert MySQL types
        key = f"DEPENDENT:{dependent['Essn']}:{dependent['Dependent_name']}"  # Use SSN and dependent name as the Redis key
        redis_conn.hset(key, mapping=dependent)
        print(f"Inserted DEPENDENT:{dependent['Essn']}:{dependent['Dependent_name']} into Redis")

    # Load DEPT_LOCATION table into Redis
    mysql_cursor.execute("SELECT * FROM DEPT_LOCATION")
    dept_locations = mysql_cursor.fetchall()
    for dept_location in dept_locations:
        dept_location = convert_mysql_to_redis(dept_location)  # Convert MySQL types
        key = f"DEPT_LOCATION:{dept_location['Dnumber']}:{dept_location['Location']}"  # Use Dnumber and Location as the Redis key
        redis_conn.hset(key, mapping=dept_location)
        print(f"Inserted DEPT_LOCATION:{dept_location['Dnumber']}:{dept_location['Location']} into Redis")

    # Load PROJECT table into Redis
    mysql_cursor.execute("SELECT * FROM PROJECT")
    projects = mysql_cursor.fetchall()
    for project in projects:
        project = convert_mysql_to_redis(project)  # Convert MySQL types
        key = f"PROJECT:{project['Pnumber']}"  # Use project number as the Redis key
        redis_conn.hset(key, mapping=project)
        print(f"Inserted PROJECT:{project['Pnumber']} into Redis")

    # Load WORKS_ON table into Redis (Updated)
    mysql_cursor.execute("SELECT * FROM WORKS_ON")
    works_on = mysql_cursor.fetchall()
    for work in works_on:
        work = convert_mysql_to_redis(work)  # Convert MySQL types
        key = f"WORKS_ON:{work['Essn']}:{work['Pno']}"  # Use Essn and Pno as the Redis key
        redis_conn.hset(key, mapping=work)
        print(f"Inserted WORKS_ON:{work['Essn']}:{work['Pno']} into Redis")

    print("Data loaded into Redis successfully.")
    mysql_cursor.close()
    mysql_conn.close()

except Exception as e:
    print(f"Error: {e}")

