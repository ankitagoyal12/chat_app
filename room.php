<?php
session_start();
require_once "includes/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// Get all chat rooms
$sql = "SELECT * FROM chat_rooms";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Select Chat Room</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #00c6ff, #0072ff);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 20px;
            color: white;
        }

        h2, h3 {
            text-align: center;
            animation: fadeInDown 1s ease;
        }

        .room-list {
            width: 100%;
            max-width: 600px;
            margin-top: 30px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .room {
            background: white;
            color: #333;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            transition: transform 0.3s, box-shadow 0.3s;
            animation: floatUp 1s ease forwards;
        }

        .room:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }

        .room a {
            color: #0072ff;
            text-decoration: none;
            font-weight: bold;
            font-size: 18px;
        }

        .room p {
            margin: 8px 0 0;
            font-size: 14px;
            color: #555;
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes floatUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h2>
    <h3>Select a Chat Room:</h3>

    <div class="room-list">
        <?php while ($room = $result->fetch_assoc()): ?>
            <div class="room">
                <a href="chat.php?room_id=<?php echo $room['id']; ?>">
                    <?php echo htmlspecialchars($room["name"]); ?>
                </a>
                <p><?php echo htmlspecialchars($room["description"]); ?></p>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
