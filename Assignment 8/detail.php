<?php
$TITLE = 'Item Detail — Lost & Found';
include __DIR__ . '/php/header.php';
include __DIR__ . '/db/db_connect.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
function h($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

if (!$id) {
  echo '<section class="about-card"><h3>Invalid request</h3><p>Missing item id.</p><p><a class="btn primary" href="search_form.php">← Back</a></p></section>';
  include __DIR__ . '/php/footer.php'; exit;
}

$q = "SELECT i.ItemID, i.Name, i.Category, i.Description, i.DateLost, i.LocationLost
      FROM Item i WHERE i.ItemID = ?";
$st = $conn->prepare($q);
$st->bind_param("i", $id);
$st->execute();
$item = $st->get_result()->fetch_assoc();
?>
<section class="about-card" style="max-width:900px; margin:0 auto;">
  <?php if (!$item): ?>
    <h3>Item not found</h3>
    <p><a class="btn primary" href="search_form.php">← Back to search</a></p>
  <?php else: ?>
    <h2><?= h($item['Name']) ?> <span class="muted" style="font-size:0.7em">#<?= h($item['ItemID']) ?></span></h2>
    <p><b>Category:</b> <?= h($item['Category']) ?></p>
    <p><b>Location:</b> <?= h($item['LocationLost']) ?></p>
    <p><b>Description:</b> <?= h($item['Description']) ?></p>
    <p><b>Date Lost:</b> <?= h($item['DateLost']) ?></p>

    <p style="margin-top:16px;">
      <a class="btn primary" href="search_form.php">← Back to Search</a>
    </p>
  <?php endif; ?>
</section>
<?php include __DIR__ . '/php/footer.php'; ?>
