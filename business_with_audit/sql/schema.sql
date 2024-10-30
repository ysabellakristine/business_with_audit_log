CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    gender VARCHAR(50) NOT NULL,
    password VARCHAR(250) NOT NULL,
    age INT,
    date_of_birth DATE,
    email VARCHAR(100) NOT NULL UNIQUE,
    address TEXT NULL,
    contact_no VARCHAR(20) NOT NULL,
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS toy_resellers (
    toy_reseller_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    gender VARCHAR(50),
    age INT,
    date_of_birth DATE,
    location TEXT NULL,
    added_by INT,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (added_by) REFERENCES users(user_id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS toys (
    toy_id INT AUTO_INCREMENT PRIMARY KEY,
    toy_name VARCHAR(50),
    toy_type TEXT,
    toy_reseller_id INT,
    added_by INT,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (toy_reseller_id) REFERENCES toy_resellers(toy_reseller_id),
    FOREIGN KEY (added_by) REFERENCES users(user_id) ON DELETE SET NULL  -- Ensure added_by has a valid reference
);

CREATE TABLE IF NOT EXISTS audit_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action_type VARCHAR(50) NOT NULL,                             -- Possible values: CREATE, UPDATE, OR DELETE
    action_details TEXT,                                          -- Comments from the database
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) 
);
