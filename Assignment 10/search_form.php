<?php
$TITLE = 'Search Lost Items';
include __DIR__ . '/php/header.php';
?>
<section class="about-card" style="max-width:900px; margin:0 auto;">
  <nav class="mini-nav" style="margin-bottom:8px;">
    <a href="/~anmata/lostandfound/index.html">Home</a> · <span>Search</span>
  </nav>

  <h1>Search Lost Items</h1>
  <p class="lead">Filter by category and/or a starting date. Leave empty to see all.</p>

  <form action="search_results.php" method="get" class="form" style="display:flex; gap:12px; flex-wrap:wrap; margin-top:12px;">
    <label>Category</label>
    <input class="auto" type="text" name="category" value="<?= htmlspecialchars($_GET['category'] ?? '') ?>">

    <label>Date Lost (from)</label>
    <input type="date" name="date_lost" value="<?= htmlspecialchars($_GET['date_lost'] ?? '') ?>">

    <button type="submit" class="btn primary">Search</button>
  </form>
</section>
<?php include __DIR__ . '/php/footer.php'; ?>
