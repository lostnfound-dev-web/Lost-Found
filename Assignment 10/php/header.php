<?php
// Shared layout header for all pages
// Set $TITLE in each page before including this file.
$TITLE = $TITLE ?? 'Lost & Found';

// helper to render a nav item and mark it active
function nav_item($href, $label, $icon, $is_active) {
  $active = $is_active ? 'background:#e9f0ff;border-radius:12px;font-weight:600;' : '';
  echo '<a class="nav-item" href="'.$href.'" style="text-decoration:none;color:#333;padding:8px;display:flex;align-items:center;gap:10px;'.$active.'">'
      .'<span class="icon">'.$icon.'</span> '.$label.'</a>';
}

// figure out which page we're on to highlight the correct item
$script = basename($_SERVER['SCRIPT_NAME'] ?? '');
$is_search = in_array($script, ['search_form.php','search_results.php','detail.php']);
?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="/~anmata/lostandfound/autocomplete.js"></script>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title><?= htmlspecialchars($TITLE) ?></title>
  <link rel="stylesheet" href="/~anmata/lostandfound/style.css" />
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar" style="width:220px; float:left; padding:16px; background:#fff; min-height:100vh;">
  <div class="brand" style="display:flex; align-items:center; gap:10px; margin-bottom:24px;">
    <div class="logo">
      <img src="/~anmata/lostandfound/img/LOGO.png" alt="Lost & Found Logo" style="height:48px; width:auto; display:block;">
    </div>
    <div class="brand-text">
      <strong>Lost&nbsp;&amp;&nbsp;Found</strong><br>
      <span class="muted">Project</span>
    </div>
  </div>

  <nav class="nav" style="display:flex; flex-direction:column; gap:10px;">
    <?php nav_item('/~anmata/lostandfound/index.html', 'Dashboard', '🏠', $script === 'index.html'); ?>
    <?php nav_item('/~anmata/lostandfound/categories.html', 'Categories', '📁', $script === 'categories.html'); ?>
    <?php nav_item('/~anmata/lostandfound/items.html', 'Items', '🧳', $script === 'items.html'); ?>

    <!-- NEW: Search in sidebar (auto-highlight on search pages) -->
    <?php nav_item('/~anmata/lostandfound/search_form.php', 'Search Items', '🔎', $is_search); ?>

    <div style="margin:12px 8px 0;color:#666;font-weight:700;letter-spacing:.02em;">MAINTENANCE</div>
    <?php nav_item('/~anmata/lostandfound/maintenance.html', 'Data Input', '🧩', $script === 'maintenance.html'); ?>
    <?php nav_item('/~anmata/lostandfound/users.html', 'Users', '👥', $script === 'users.html'); ?>
    <?php nav_item('/~anmata/lostandfound/login.html', 'Log in / Log out', '🔐', $script === 'login.html'); ?>

    <div class="nav-footer" style="margin-top:auto; padding-top:20px;">
      <a href="/~anmata/lostandfound/imprint.html" class="imprint-link" style="text-decoration:none; color:#777;">Imprint / Disclaimer</a>
    </div>
  </nav>
</aside>

<!-- Main -->
<main class="main">
  <header class="topbar">
    <button class="hamburger" aria-label="Menu">☰</button>
    <div class="spacer"></div>
  </header>
