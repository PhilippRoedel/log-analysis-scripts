<?php
/***
 * One license serial number should only be active on one physical device.
 * Describe how you identify a single device as such.
 * Provide a way to identify licenses that are installed on more than one device.
 * What are the 10 license serials that violate this rule the most?
 * On how many distinct devices are these licenses installed?
**/

$logFilePath = './data/original.log';
$handle = fopen($logFilePath, 'r');

$licenseArray = [];
$macArray = [];
$counter = 0;
$macLicenseArray = [];


while (($line = fgets($handle)) !== false) {
    $counter++;

    $positionSerial1 = strpos($line, 'serial=');
    $positionSerial1 = $positionSerial1 + strlen('serial=');
    $positionSerial2 = strpos($line, 'version=');
    $strlenSerial = $positionSerial2 -1 - $positionSerial1;
    $extractedSerial = substr($line, $positionSerial1,$strlenSerial);

    // identify a single device by its mac address
    $positionSpec1 = strpos($line, 'specs=');
    $positionSpec1 = $positionSpec1 + strlen('specs=');
    $positionSpec2 = strpos($line, 'not_after=');
    $strlenSpec = $positionSpec2 - $positionSpec1;
    $extractedSpecString = substr($line, $positionSpec1,$strlenSpec);

    $gzipEncodedString = base64_decode($extractedSpecString);
    $originalString = gzdecode($gzipEncodedString);
    $decodedData = json_decode($originalString, true);

    $mac = $decodedData['mac'];
    $macLicenseArray[$mac] = $extractedSerial;
}
fclose($handle);

// identify licenses that are installed on more than one device
$macCounts = [];
foreach ($macLicenseArray as $mac => $license) {
    if (isset($macCounts[$license])) {
        $macCounts[$license]++;
    } else {
        $macCounts[$license] = 1;
    }
}
arsort($macCounts);
//print_r(array_reverse($valueCounts));

// 10 licenses with the highest installation count
$firstTen = array_slice($macCounts, 0, 10);
foreach ($firstTen as $license => $macCount) {
    echo "License '$license' is used for $macCount mac addresses", PHP_EOL;
}