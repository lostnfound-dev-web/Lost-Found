<?php
$TITLE = 'Results — Lost & Found';
include __DIR__ . '/php/header.php';
include __DIR__ . '/db/db_connect.php';

$category = trim($_GET['category'] ?? '');
$date     = trim($_GET['date_lost'] ?? '');

$sql = "SELECT i.ItemID, i.Name, i.Category, i.DateLost, i.LocationLost
        FROM Item i
        JOIN LostItem li ON li.ItemID = i.ItemID
        WHERE 1=1";
$params = []; $types = '';
if ($category !== '') { $sql .= " AND i.Category LIKE ?"; $params[] = "%{$category}%"; $types .= 's'; }
if ($date !== '')     { $sql .= " AND i.DateLost  >= ?";  $params[] = $date;            $types .= 's'; }
$sql .= " ORDER BY i.DateLost DESC, i.ItemID DESC LIMIT 100";

$stmt = $conn->prepare($sql);
if ($params) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
$res = $stmt->get_result();

function h($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
?>
<section class="about-card" style="max-width:900px; margin:0 auto;">
  <nav class="mini-nav" style="margin-bottom:8px;">
    <a href="/~anmata/lostandfound/index.html">Home</a> · <a href="search_form.php">Search</a> · <span>Results</span>
  </nav>

  <h1>Results</h1>

  <?php if ($res->num_rows): ?>
    <div class="list">
      <?php while ($r = $res->fetch_assoc()): ?>
        <a class="list-item" href="detail.php?id=<?= urlencode($r['ItemID']) ?>" style="display:block; padding:12px 0; border-bottom:1px solid #eee; text-decoration:none; color:inherit;">
          <strong><?= h($r['Name']) ?></strong>
          — <?= h($r['LocationLost']) ?>
          <span class="muted">(<?= h($r['DateLost']) ?>)</span>
        </a>
      <?php endwhile; ?>
    </div>
  <?php else: ?>
    <p class="muted">No results found. Try clearing filters.</p>
  <?php endif; ?>

  <div style="margin-top:16px;">
    <a class="btn primary" href="search_form.php">← Back to Search</a>
  </div>
</section>
<?php include __DIR__ . '/php/footer.php'; ?>
