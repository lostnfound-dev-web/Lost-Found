<?php require_once __DIR__ . '/includes/auth.php'; ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Lost & Found — Maintenance Page</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
<!-- Sidebar -->
<aside class="sidebar" style="width:220px; float:left; padding:16px; background:#fff; min-height:100vh;">
  <div class="brand" style="display:flex; align-items:center; gap:10px; margin-bottom:24px;">
    <div class="logo">
      <img src="img/LOGO.png" alt="Lost & Found Logo" style="height:48px; width:auto; display:block;">
    </div>
    <div class="brand-text">
      <strong>Lost&nbsp;&amp;&nbsp;Found</strong><br>
      <span class="muted">Project</span>
    </div>
  </div>

  <nav class="nav" style="display:flex; flex-direction:column; gap:10px;">
    <a class="nav-item" href="index.html" style="text-decoration:none; color:#333; padding:8px;">
      <span class="icon">🏠</span> Dashboard
    </a>
    <a class="nav-item" href="#" style="text-decoration:none; color:#333; padding:8px;">
      <span class="icon">🗂️</span> Categories
    </a>
    <a class="nav-item" href="#" style="text-decoration:none; color:#333; padding:8px;">
      <span class="icon">🎒</span> Items
    </a>

    <div class="nav-section" style="margin-top:20px; font-weight:bold; color:#555;">Maintenance</div>
    <a class="nav-item active" href="maintenance.php" style="text-decoration:none; color:#333; padding:8px;">
      Maintenance
    </a>

    <div class="nav-footer" style="margin-top:auto; padding-top:20px;">
      <a href="imprint.html" class="imprint-link" style="text-decoration:none; color:#777;">Imprint / Disclaimer</a><br>
      <?php if (session_status()===PHP_SESSION_NONE) session_start(); ?>
      <?php if (!empty($_SESSION['logged_in'])): ?>
        <a href="logout.php?next=index.html" style="text-decoration:none; color:#a00;">🚪 Logout</a>
      <?php else: ?>
        <a href="login.php?next=maintenance.php" style="text-decoration:none; color:#0d6efd;">🔒 Log in</a>
      <?php endif; ?>
    </div>
  </nav>
</aside>

<!-- Main -->
<main class="main" style="margin-left:240px; padding:24px;">
  <header class="topbar" style="display:flex; align-items:center; gap:12px; margin-bottom:14px;">
    <h1 style="margin:0;">Maintenance Page</h1>
  </header>

  <section class="card">
    <ul class="list-links list-compact">
      <!-- entity pages -->
      <li><a href="forms/input_user.html">👤 Add User</a></li>
      <li><a href="forms/input_student.html">🎓 Add Student</a></li>
      <li><a href="forms/input_admin.html">🛡️ Add Admin</a></li>
      <li><a href="forms/input_item.html">🎒 Add Item</a></li>
      <li><a href="forms/input_status.html">📌 Add Status</a></li>

      <!-- relationship pages -->
      <li><a href="forms/input_report.html">📝 Add Report</a></li>
      <li><a href="forms/input_reportitem.html">🔗 Link Report ↔ Item</a></li>
      <li><a href="forms/input_itemstatus.html">📊 Set Item Status</a></li>
      <li><a href="forms/input_adminverifiesitemstatus.html">✅ Admin Verifies Item Status</a></li>
    </ul>
  </section>

  <footer class="footer">
    <span>© 2025 Lost &amp; Found (Student Project)</span>
    <a href="imprint.html">Imprint / Disclaimer</a>
  </footer>
</main>
</body>
</html>
