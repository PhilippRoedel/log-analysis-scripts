<?php
//$logFilePath = './data/original.log';
//$logFilePath = './data/shortened.log';
$logFilePath = './data/fake.log';
$lines = file($logFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$lineArray = [];

$licenseArray = [];

foreach ($lines as $line) {
    $lineArray[] = $line;
    echo $line, PHP_EOL;
    $position1 = strpos($line, 'serial=');
    $position1 = $position1 + strlen('serial=');
    echo $position1, PHP_EOL;
    $position2 = strpos($line, 'version=');
    echo $position2, PHP_EOL;
    $strlen = $position2 -1 - $position1;
    echo "string length: $strlen", PHP_EOL;
    $extractedString = substr($line, $position1,$strlen);
    echo "extracted string: $extractedString", PHP_EOL;
    $licenseArray[] = $extractedString;
    $countedValues = array_count_values($licenseArray);
    arsort($countedValues);
    echo "extracted array: ", PHP_EOL;
    print_r($countedValues);
    echo PHP_EOL;
}
print_r($lineArray);