<?php  

function logAction($pdo, $user_id, $action_type, $action_details) { // for audit logs
    $sql = "INSERT INTO audit_logs (user_id, action_type, action_details) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $executeQuery = $stmt->execute([$user_id, $action_type, $action_details]);

    return $executeQuery; // Returns true on success, false otherwise
}
function insertToyReseller($pdo, $username, $first_name, $last_name, $gender, $age, $date_of_birth, $location, $added_by, $user_id) {

	$sql = "INSERT INTO toy_resellers (username, first_name, last_name, gender, age, date_of_birth, location, added_by) VALUES(?,?,?,?,?,?,?,?)";

	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$username, $first_name, $last_name, $gender, $age, $date_of_birth, $location, $added_by]);

	if ($executeQuery) {
        logAction($pdo, $user_id, 'CREATE', "Created toy reseller: $username");
		return true;
	}
}

function updateToyReseller($pdo, $username, $first_name, $last_name, $gender, $age, $date_of_birth, $location, $toy_reseller_id, $user_id) {

	$sql = "UPDATE toy_resellers
				SET username = ?,
					first_name = ?,
					last_name = ?,
                    gender = ?,
                    age = ?,
					date_of_birth = ?, 
					location = ?,
                    last_updated = CURRENT_TIMESTAMP
				WHERE toy_reseller_id = ?";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$username, $first_name, $last_name, $gender, $age, $date_of_birth, $location, $toy_reseller_id]);
	
	if ($executeQuery) {
        logAction($pdo, $user_id, 'UPDATE', "Updated toy reseller: $username");
		return true;
	}

}

function deleteToyReseller($pdo, $toy_reseller_id) {
	$deleteToys = "DELETE FROM toys WHERE toy_reseller_id = ?";
	$deleteStmt = $pdo->prepare($deleteToys);
	$executeDeleteQuery = $deleteStmt->execute([$toy_reseller_id]);

	if ($executeDeleteQuery) {
		$sql = "DELETE FROM toy_resellers WHERE toy_reseller_id = ?";
		$stmt = $pdo->prepare($sql);
		$executeQuery = $stmt->execute([$toy_reseller_id]);

		if ($executeQuery) {
			return true;
		}

	}
	
}

function getAllToyResellers($pdo) {
	$sql = "SELECT * FROM toy_resellers";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute();

	if ($executeQuery) {
		return $stmt->fetchAll();
	}
}

function getToyResellerByID($pdo, $toy_reseller_id) {
	$sql = "SELECT * FROM toy_resellers WHERE toy_reseller_id = ?"; // updated selected method to only include toys that aren't deleted
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$toy_reseller_id]);

	if ($executeQuery) {
        // Fetch the result
        $result = $stmt->fetch();
        
        // Return the result if found, otherwise return null
        return $result !== false ? $result : null; // Explicitly return null if no record is found
    }

    return null; // Return null if the query fails
}

function getToysByToyReseller($pdo, $toy_reseller_id) {
    $sql = "SELECT 
                toys.toy_id AS toy_id,
                toys.toy_name AS toy_name,
                toys.toy_type AS toy_type,
                toys.date_added AS date_added,
                CONCAT(toy_resellers.first_name, ' ', toy_resellers.last_name) AS toy_owner
            FROM toys
            JOIN toy_resellers ON toys.toy_reseller_id = toy_resellers.toy_reseller_id
            WHERE toys.toy_reseller_id = ?
            ORDER BY toys.toy_id ASC";  

    $stmt = $pdo->prepare($sql);
    
    try {
        $executeQuery = $stmt->execute([$toy_reseller_id]);
        if ($executeQuery) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch as associative array
        }
    } catch (PDOException $e) {
        // Handle error
        error_log($e->getMessage());
        return []; // Return an empty array on error
    }

    return []; // Return an empty array if nothing is fetched
}


function insertToys($pdo, $toy_name, $toy_type, $toy_reseller_id, $added_by,$user_id, $username) {
    try {
        // Check if toy_reseller_id exists first
        $checkSql = "SELECT COUNT(*) FROM toy_resellers WHERE toy_reseller_id = ?";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([$toy_reseller_id]);

        if ($checkStmt->fetchColumn() == 0) {
            // Toy reseller does not exist
            error_log("Toy reseller ID $toy_reseller_id does not exist.");
            return false;
        }

        // Proceed with toy insertion
        $sql = "INSERT INTO toys (toy_name, toy_type, toy_reseller_id, added_by) VALUES (?,?,?,?)";
        $stmt = $pdo->prepare($sql);
        $executeQuery = $stmt->execute([$toy_name, $toy_type, $toy_reseller_id,$added_by]);

        if ($executeQuery) {
            logAction($pdo, $user_id, 'CREATE', "Created toy: $toy_name");
            return true;
        }

    } catch (PDOException $e) {
        // Log any PDO exceptions
        error_log("Error inserting toy: " . $e->getMessage());
        return false;
    }
}


function getToyByID($pdo, $toy_id) {
	$sql = "SELECT 
				toys.toy_id AS toy_id,
				toys.toy_name AS toy_name,
				toys.toy_type AS toy_type,
				toys.date_added AS date_added,
				CONCAT(toy_resellers.first_name,' ',toy_resellers.last_name) AS toy_owner
			FROM toys
			JOIN toy_resellers ON toys.toy_reseller_id = toy_resellers.toy_reseller_id
			WHERE toys.toy_id  = ? 
			GROUP BY toys.toy_name";

	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$toy_id]);
	if ($executeQuery) {
		return $stmt->fetch();
	}
}

function updatetoy($pdo, $toy_name, $toy_type, $toy_id,$user_id, $username) {
	$sql = "UPDATE toys
			SET toy_name = ?,
				toy_type = ?,
                last_updated = CURRENT_TIMESTAMP -- added timestamp for update
			WHERE toy_id = ?
			";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$toy_name, $toy_type, $toy_id]);

	if ($executeQuery) {
        logAction($pdo, $user_id, 'UPDATE', "Updated toy: $toy_name");
		return true;
	}
}

function deletetoy($pdo, $toy_id) {
	$sql = "DELETE FROM toys WHERE toy_id = ?";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$toy_id]);
	if ($executeQuery) {
		return true;
	}
}


function getAllToysWithOwner($pdo) {
    $sql = "SELECT 
                toys.toy_id AS toy_id,
                toys.toy_name AS toy_name,
                toys.toy_type AS toy_type,
                toys.date_added AS date_added,
                toys.last_updated AS last_updated,
                CONCAT(toy_resellers.first_name, ' ', toy_resellers.last_name) AS toy_owner
            FROM toys
            LEFT JOIN toy_resellers ON toys.toy_reseller_id = toy_resellers.toy_reseller_id
            ORDER BY toys.toy_id ASC";

    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch associative array for better readability
    } catch (PDOException $e) {
        // Log the error message or handle it as needed
        error_log($e->getMessage());
        return []; // Return an empty array on error
    }
}



function getAllToys($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT toy_id, toy_name AS name, toy_type AS type FROM toys");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error fetching toys: " . $e->getMessage();
        return [];
    }
}



function addUser($conn, $username, $password) {
    // Check if username already exists
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username]);

    if ($stmt->rowCount() == 0) {
        // Hash the password before storing it
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert the new user into the database
        $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$username, $hashedPassword]);
    } else {
        // Handle the case where the username already exists
        return false; // Optionally, you can throw an exception or return a specific error code
    }
}


function login($pdo, $login_input, $password) {
    // Prepare the query to check if the user exists using username or email
    $query = "SELECT * FROM users WHERE username = ? OR email = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$login_input, $login_input]); // Using the same input for both placeholders

    if ($stmt->rowCount() == 1) {
        // Returns associative array
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Get values from the fetched row
        $uid = $row['user_id'];
        $passHash = $row['password'];

        // Validate password 
        if (password_verify($password, $passHash)) {
            // Store user info as session variables
            $_SESSION['user_id'] = $uid;
            $_SESSION['username'] = $row['username']; // Access username from the fetched row
            $_SESSION['email'] = $row['email']; // Access email from the fetched row
            $_SESSION['userLoginStatus'] = 1; // Set login status to true
            return true; // Successful login
        } else {
            // Incorrect password
            return false;
        }
    } else {
        // User not found
        return false;
    }
}

