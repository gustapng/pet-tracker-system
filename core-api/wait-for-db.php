<?php
$host = 'mysql';
$port = 3306;

while (!$conn = @fsockopen($host, $port)) {
    echo "..." . PHP_EOL;
    sleep(2);
}
fclose($conn);
exit(0);