<?php
// login.php
session_start();
require_once "includes/db.php";

$message = "";
$registered = isset($_GET["registered"]) && $_GET["registered"] == "1";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user["password"])) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            header("Location: room.php");
            exit;
        } else {
            $message = "Invalid password.";
        }
    } else {
        $message = "No user found with that email.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
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
            color: red;
            margin-top: 15px;
        }

        .success {
            text-align: center;
            color: green;
            margin-bottom: 10px;
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
        <h2>Login</h2>
        <?php if ($registered): ?>
            <div class="success">Registered successfully! Please login.</div>
        <?php endif; ?>
        <form method="POST">
            <input type="email" name="email" placeholder="Enter your email" required>
            <input type="password" name="password" placeholder="Enter your password" required>
            <button type="submit">Login</button>
        </form>
        <div class="message"><?php echo $message; ?></div>
    </div>
</body>
</html>
