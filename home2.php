<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Home Page</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 0; line-height: 1.6; }
        header { background: #333; color: #fff; padding: 1rem; text-align: center; }
        nav { display: flex; justify-content: center; background: #444; padding: 0.5rem; position: relative; }
        nav a { color: white; margin: 0 15px; text-decoration: none; }
        .hero { padding: 50px; text-align: center; background: #f4f4f4; }
        .container { padding: 20px; max-width: 800px; margin: auto; }
        footer { background: #333; color: #fff; text-align: center; padding: 10px; position: fixed; bottom: 0; width: 100%; }
        h1 { margin: 0; }
    </style>
</head>
<body>

<header>
    <h1>RiftBound</h1>
</header>

<nav>
    <a href="#">TBD</a>
    <a href="logout.php">Logout</a>
    <a href="#">TBD</a>
    <a href="#">TBD</a>
</nav>

<section class="hero">
    <h2>Discover Amazing Content</h2>
    <p>This is a simple landing page built with pure HTML and CSS.</p>
    <button onclick="alert('Hello!')">Get Started</button>
</section>

<section class="hero">
    <video autoplay muted loop playsinline class="hero-video">
        <source src="https://preview.redd.it/q0dd3k02unqb1.gif?width=1152&format=mp4&s=5fa5cac1069d4f256d69efc1654621c5aa9520a5" type="video/mp4">
        Your browser does not support the video tag.
    </video>
</section>

<div class="container">
    <!-- Add your content here -->
</div>

<footer></footer>
</body>
</html>