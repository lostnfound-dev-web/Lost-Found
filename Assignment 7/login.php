<?php
session_start();
require_once __DIR__ . '/db/db_connect.php'; // adjust if your path is different

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please fill in both fields.';
    } else {
        // Users(username, password) — plain text for this coursework
        $sql = "SELECT username, password FROM Users WHERE username = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user && $user['password'] === $password) {
            session_regenerate_id(true);
            $_SESSION['username'] = $user['username'];
            header('Location: index.html'); 
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Admin Login • Lost & Found</title>

<!-- Font & Icons -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
    :root{
        --primary:#5B7CFF;          /* button start */
        --secondary:#FF4FD8;        /* button end */
        --card:#ffffff;
        --text:#1d1f2a;
        --muted:#6b7280;
        --input:#e6e9f2;
        --focus:rgba(91,124,255,.35);
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{
        margin:0;
        font-family:"Poppins",system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial;
        color:var(--text);
        /* soft diagonal gradient like your screenshot */
        background: radial-gradient(1200px 1200px at 85% 20%, rgba(255,255,255,.07) 0, rgba(255,255,255,0) 50%),
                    linear-gradient(135deg, #6a88ff 0%, #b15cff 55%, #ff5bbb 100%);
        display:flex; align-items:center; justify-content:center;
        padding:24px;
    }
    .card{
        width:min(92vw, 430px);
        background:var(--card);
        border-radius:16px;
        box-shadow:0 20px 60px rgba(0,0,0,.15);
        padding:34px 34px 28px;
    }
    .title{
        margin:0 0 22px;
        font-size:30px;
        font-weight:700;
        text-align:center;
        letter-spacing:.2px;
    }
    .group{margin:14px 0}
    label{
        display:block;
        font-size:13px;
        color:var(--muted);
        margin:0 0 6px 2px;
    }
    .input-wrap{
        display:flex; align-items:center; gap:10px;
        background:var(--card);
        border:1.5px solid var(--input);
        border-radius:10px;
        padding:12px 14px;
        transition:border-color .2s, box-shadow .2s, background .2s;
    }
    .input-wrap:focus-within{
        border-color:var(--primary);
        box-shadow:0 0 0 6px var(--focus);
    }
    .input-wrap i{color:var(--muted)}
    input{
        border:none; outline:none; background:transparent;
        width:100%; font-size:15px; color:var(--text);
    }
    .row{
        display:flex; justify-content:space-between; align-items:center;
        margin-top:8px; margin-bottom:6px;
    }
    .hint{
        font-size:12px; color:var(--muted);
        text-decoration:none;
    }
    .btn{
        width:100%;
        border:none; cursor:pointer;
        margin-top:12px;
        padding:13px 16px;
        font-weight:700; letter-spacing:.4px;
        color:white;
        border-radius:999px;
        background-image:linear-gradient(90deg, var(--primary), var(--secondary));
        box-shadow:0 10px 24px rgba(91,124,255,.35);
        transition:transform .06s ease-out, box-shadow .15s ease-out, filter .15s;
    }
    .btn:hover{filter:saturate(1.1)}
    .btn:active{transform:translateY(1px); box-shadow:0 6px 16px rgba(91,124,255,.35)}
    .error{
        background:#ffe9e9; color:#b60d0d;
        border:1px solid #ffbcbc;
        padding:10px 12px; border-radius:10px;
        font-size:14px; margin-bottom:14px;
    }
    .footer-note{
        text-align:center; margin-top:16px; font-size:12px; color:#ffffffcc;
    }
</style>
</head>
<body>

<main class="card" aria-label="Login Card">
    <h1 class="title">Login</h1>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" autocomplete="off" novalidate>
        <div class="group">
            <label for="username">Username</label>
            <div class="input-wrap">
                <i class="fa-regular fa-user"></i>
                <input type="text" id="username" name="username" placeholder="Type your username" required>
            </div>
        </div>

        <div class="group">
            <label for="password">Password</label>
            <div class="input-wrap">
                <i class="fa-solid fa-lock"></i>
                <input type="password" id="password" name="password" placeholder="Type your password" required>
            </div>
            <div class="row">
                <span></span>
                <a class="hint" href="#" tabindex="-1">Forgot password?</a>
            </div>
        </div>

        <button class="btn" type="submit">LOGIN</button>
    </form>
</main>

</body>
</html>
