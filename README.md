# 💬 Chat App

A real-time chat application using **PHP**, **MySQL**, **JavaScript**, and **WebSockets (Ratchet)**.

---

## ✨ Features

- ✅ User Registration & Login  
- 🏠 Chat Room Selection  
- 💬 Real-Time Messaging (via WebSockets)  
- 💾 Messages saved in MySQL  
- 📱 Responsive & Animated UI  
- 🔐 Logout functionality  

---

## ⚙️ How to Run This Project

### 1. Start XAMPP
- Start **Apache** and **MySQL**

### 2. Import Database
- Go to [http://localhost/phpmyadmin](http://localhost/phpmyadmin)  
- Create a new database: `chat_app`  
- Import `chat_app.sql`

### 3. Start WebSocket Server
```bash
cd C:\xampp\htdocs\chat_app
php server.php
```

### 4. Open the Chat App in Browser
```
http://localhost/chat_app/login.php
```

---

## 📁 Folder Structure

```
chat_app/
├── login.php
├── register.php
├── room.php
├── chat.php
├── logout.php
├── server.php
├── includes/
│   └── db.php
├── vendor/ (via Composer)
├── chat_app.sql
└── README.md
```

---

## 👩‍💻 Developer

**Ankita Goyal**  
_This project was created as part of an internship assignment._

---
