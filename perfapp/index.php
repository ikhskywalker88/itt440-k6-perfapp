<?php
$flights = [
    ['from' => 'Boston', 'to' => 'New York',  'price' => 120],
    ['from' => 'Boston', 'to' => 'Rome',      'price' => 620],
    ['from' => 'London', 'to' => 'Berlin',    'price' => 150],
    ['from' => 'Tokyo',  'to' => 'Seoul',     'price' => 200],
    ['from' => 'Sydney', 'to' => 'Melbourne', 'price' => 90],
];

usleep(rand(50, 200) * 1000); // small random delay
?>
<!doctype html>
<html>
<head>
    <title>PerfApp - Flights</title>
</head>
<body>
<h1>PerfApp - Demo Flight List</h1>
<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>From</th><th>To</th><th>Price (USD)</th>
    </tr>
    <?php foreach ($flights as $f): ?>
        <tr>
            <td><?= htmlspecialchars($f['from']) ?></td>
            <td><?= htmlspecialchars($f['to']) ?></td>
            <td><?= htmlspecialchars($f['price']) ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<p><a href="search.php?from=Boston&to=Rome">Search sample flights</a></p>
<p><a href="booking.php">Simulate booking (heavier)</a></p>
</body>
</html>