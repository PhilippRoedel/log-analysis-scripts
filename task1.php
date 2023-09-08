<?php
$logFilePath = './data/original.log';
//$logFilePath = './data/shortened.log';
//$logFilePath = './data/fake.log';
$handle = fopen($logFilePath, 'r');
$licenseArray = [];
$countedValues = [];

while (($line = fgets($handle)) !== false) {
    $position1 = strpos($line, 'serial=');
    $position1 = $position1 + strlen('serial=');
    $position2 = strpos($line, 'version=');
    $strlen = $position2 -1 - $position1;
    $extractedString = substr($line, $position1,$strlen);
    $licenseArray[] = $extractedString;
}
fclose($handle);

$countedValues = array_count_values($licenseArray);
arsort($countedValues);

$firstTen = array_slice($countedValues, 0, 10);
foreach ($firstTen as $value => $count) {
    echo "License '$value' is accessing the server $count times\n";
}