<?php
global $conn;
include('config.php'); // Sigurohu që ky skedar është i saktë dhe përmban lidhjen me databazën
header("Location: /front_End/login.html");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $birthdate = $_POST['birthdate'];
    $gender = $_POST['gender'];
    $password = $_POST['password'];

    if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
        die("Password does not meet security requirements.");
    }

    if (strpos($email, '@companydomain.com') !== false) {
        $role = 'admin'; // Vetëm ata që kanë email institucional mund të jenë admin
    } else {
        $role = 'user';
    }

    $admin_code = $_POST['admin_code'] ?? ''; // Merr kodin e admin (opsional)

// Kodi i paracaktuar për admin
    $valid_admin_code = "ADMIN123";

// Kontrollo rolin bazuar në kodin e admin
    $role = ($admin_code === $valid_admin_code) ? 'admin' : 'user';

// Futja e të dhënave në databazë me rolin përkatës
    $insert_query = "INSERT INTO users (first_name, last_name, email, birthdate, gender, password, role) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("sssssss", $first_name, $last_name, $email, $birthdate, $gender, $hashed_password, $role);

    if ($stmt->execute()) {
        echo "Registration successful!";
    } else {
        echo "Error: " . $stmt->error;
    }


    // Kontrollo nëse emaili është i vlefshëm
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Emaili është i pavlefshëm.");
    }

    // Enkriptimi i fjalëkalimit
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Kontrollo nëse emaili ekziston
    $check_email_query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($check_email_query); // Inicializimi i variablës $stmt
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        die("Ky email ekziston tashmë!");
    }

    // Futja e të dhënave të reja
    $insert_query = "INSERT INTO users (first_name, last_name, email, birthdate, gender, password) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_query); // Inicializimi i saktë i $stmt për futjen e të dhënave
    $stmt->bind_param("ssssss", $first_name, $last_name, $email, $birthdate, $gender, $hashed_password);

    if ($stmt->execute()) {
        echo "Përdoruesi u regjistrua me sukses!";
        // Opsionale: Ridrejto te faqja e login-it
        header("Location: login.html");
        exit();
    } else {
        echo "Gabim gjatë regjistrimit: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
