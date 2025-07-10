<?php
// register.php
session_start();
require_once "includes/db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = password_hash(trim($_POST["password"]), PASSWORD_DEFAULT);

    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $message = "Email already registered.";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password);
        if ($stmt->execute()) {
            header("Location: login.php?registered=1");
            exit;
        } else {
            $message = "Registration failed.";
        }
        $stmt->close();
    }

    $check->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #ff6ec4, #7873f5);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .card {
            background: white;
            padding: 40px 30px;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.4);
            width: 350px;
            animation: floatUp 1.2s ease forwards;
            transform: translateY(40px);
            opacity: 0;
        }

        h2 {
            text-align: center;
            color: #ff2e98;
            margin-bottom: 25px;
            font-weight: 700;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            transition: 0.3s;
        }

        input:focus {
            border-color: #7873f5;
            outline: none;
            box-shadow: 0 0 8px #7873f599;
        }

        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(to right, #ff2e98, #7873f5);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }

        .message {
            text-align: center;
            color: #d50000;
            margin-top: 15px;
        }

        @keyframes floatUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>Register</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="Enter your name" required>
            <input type="email" name="email" placeholder="Enter your email" required>
            <input type="password" name="password" placeholder="Enter a password" required>
            <button type="submit">Register</button>
        </form>
        <div class="message"><?php echo $message; ?></div>
    </div>
</body>
</html>
