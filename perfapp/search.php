<?php
$from = $_GET['from'] ?? 'Boston';
$to   = $_GET['to'] ?? 'Rome';

// simulate DB/search work
usleep(rand(150, 400) * 1000);

$results = [
    ['flight' => 'BR123', 'from' => $from, 'to' => $to, 'price' => 550],
    ['flight' => 'BR456', 'from' => $from, 'to' => $to, 'price' => 580],
];
?>
<!doctype html>
<html>
<head><title>PerfApp - Search</title></head>
<body>
<h1>Search result: <?= htmlspecialchars($from) ?> → <?= htmlspecialchars($to) ?></h1>
<ul>
    <?php foreach ($results as $r): ?>
        <li><?= htmlspecialchars($r['flight']) ?> - <?= htmlspecialchars($r['price']) ?> USD</li>
    <?php endforeach; ?>
</ul>
<p><a href="index.php">Back to home</a></p>
</body>
</html>