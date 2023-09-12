<?php
/***
 * Based on the information given in the specs metadata, try to identify the
 * different classes of hardware that are in use and provide the number of licenses
 * that are active on these types of hardware.
 *
 **/

$logFilePath = './data/original.log';
$handle = fopen($logFilePath, 'r');

$licenseArray = [];
$macArray = [];
$counter = 0;
$errorCounter = 0;
$licenseCpuArray = [];


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

    // create array of license-cpu pairs
    if (isset($decodedData['cpu'])) {
        $cpuData = $decodedData['cpu'];
        $licenseCpuArray[$extractedSerial] = $cpuData;
    } else {
        echo "data error in line no. $counter", PHP_EOL;
        echo "line: $line", PHP_EOL;
        $errorCounter++;
    }
}
fclose($handle);
echo "", PHP_EOL;

// count licenses attached to one cpu model
$cpuCounts = [];
foreach ($licenseCpuArray as $license => $cpu) {
    if (isset($cpuCounts[$cpu])) {
        $cpuCounts[$cpu]++;
    } else {
        $cpuCounts[$cpu] = 1;
    }
}
arsort($cpuCounts);
//print_r(array_reverse($cpuCounts));

// total count of licenses in license-cpu-array
$totalOfLicensesInArray = 0;
foreach ($cpuCounts as $cpu => $licenses) {
    $totalOfLicensesInArray = $totalOfLicensesInArray + $licenses;
}

// 20 cpus with the highest installation count
echo "license-cpu-array contains $totalOfLicensesInArray licenses", PHP_EOL;
echo "", PHP_EOL;
$firstTen = array_slice($cpuCounts, 0, 20);
foreach ($firstTen as $cpu => $licenses) {
    echo "$licenses \t licenses on \t $cpu", PHP_EOL;
}
echo "", PHP_EOL;
echo "due to data errors $errorCounter of $counter lines were not used", PHP_EOL;