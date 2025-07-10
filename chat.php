<?php
session_start();
require_once "includes/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET["room_id"])) {
    echo "No chat room selected.";
    exit;
}

$room_id = intval($_GET["room_id"]);

$sql = "SELECT * FROM chat_rooms WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $room_id);
$stmt->execute();
$room = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($room["name"]); ?> - Chat</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #d9afd9, #97d9e1);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .chat-container {
            background: white;
            width: 90%;
            max-width: 600px;
            height: 85vh;
            border-radius: 15px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            animation: floatUp 1s ease;
        }

        .chat-header {
            padding: 20px;
            background: #6a11cb;
            background: linear-gradient(to right, #6a11cb, #2575fc);
            color: white;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
        }

        .chat-messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background: #f9f9f9;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .chat-messages p {
            margin: 0;
            padding: 10px 15px;
            background: #e1eaff;
            border-radius: 20px;
            max-width: 70%;
            animation: fadeIn 0.5s ease;
        }

        .chat-messages p.you {
            align-self: flex-end;
            background: #c2ffd8;
        }

        .chat-input {
            display: flex;
            padding: 15px;
            background: #ffffff;
            border-top: 1px solid #ddd;
        }

        .chat-input input {
            flex: 1;
            padding: 12px;
            border: 2px solid #ccc;
            border-radius: 25px 0 0 25px;
            outline: none;
            font-size: 16px;
        }

        .chat-input button {
            padding: 12px 20px;
            border: none;
            background: #6a11cb;
            color: white;
            font-weight: bold;
            font-size: 16px;
            border-radius: 0 25px 25px 0;
            cursor: pointer;
            transition: background 0.3s;
        }

        .chat-input button:hover {
            background: #5010a1;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        @keyframes floatUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">
            Room: <?php echo htmlspecialchars($room["name"]); ?>
        </div>

        <div class="chat-messages" id="messages">
            <?php
            $sql = "SELECT users.username, messages.message_text FROM messages 
                    JOIN users ON messages.user_id = users.id 
                    WHERE room_id = ? 
                    ORDER BY messages.timestamp ASC";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $room_id);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $username = htmlspecialchars($row["username"]);
                $text = htmlspecialchars($row["message_text"]);
                $class = ($username == $_SESSION["username"]) ? "you" : "";
                echo "<p class='$class'><strong>$username:</strong> $text</p>";
            }

            $stmt->close();
            ?>
        </div>

        <form class="chat-input" onsubmit="sendMessage(event)">
            <input type="text" id="messageInput" placeholder="Type your message..." required>
            <button type="submit">Send</button>
        </form>
    </div>

    <script>
        const conn = new WebSocket('ws://localhost:8080');
        const messagesDiv = document.getElementById("messages");
        const input = document.getElementById("messageInput");

        conn.onopen = () => console.log("WebSocket Connected ✅");

        conn.onmessage = function (e) {
            const p = document.createElement("p");
            p.textContent = e.data;
            messagesDiv.appendChild(p);
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        };

        function sendMessage(e) {
            e.preventDefault();
            const message = input.value;
            const room_id = <?php echo $room_id; ?>;
            const user_id = <?php echo $_SESSION["user_id"]; ?>;
            const username = "<?php echo $_SESSION["username"]; ?>";

            const messageData = {
                room_id: room_id,
                user_id: user_id,
                message: message
            };

            conn.send(JSON.stringify(messageData));

            const p = document.createElement("p");
            p.classList.add("you");
            p.textContent = `You: ${message}`;
            messagesDiv.appendChild(p);
            messagesDiv.scrollTop = messagesDiv.scrollHeight;

            input.value = "";
        }
    </script>
</body>
</html>
