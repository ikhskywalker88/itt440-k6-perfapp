<?php
// simulate heavy booking work (payment, DB, external API)
usleep(rand(400, 900) * 1000); // 0.4s–0.9s
?>
<!doctype html>
<html>
<head><title>PerfApp - Booking</title></head>
<body>
<h1>Booking Completed</h1>
<p>Your flight has been booked (simulated).</p>
<p><a href="index.php">Back to home</a></p>
</body>
</html>
