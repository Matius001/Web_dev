<?php
@date_default_timezone_set('GMT'); // setup to allow code to run
ini_set('memory_limit', '512M');
ini_set('max_execution_time', '300');
ini_set('auto_detect_line_endings', true);

// station names: For reference
// 188 => 'AURN Bristol Centre',
// 203 => 'Brislington Depot',
// 206 => 'Rupert Street',
// 209 => 'IKEA M32',
// 213 => 'Old Market',
// 215 => 'Parson Street School',
// 228 => 'Temple Meads Station',
// 270 => 'Wells Road',
// 271 => 'Trailer Portway P&R',
// 375 => 'Newfoundland Road Police Station',
// 395 => "Shiner's Garage",
// 452 => 'AURN St Pauls',
// 447 => 'Bath Road',
// 459 => 'Cheltenham Road \ Station Road',
// 463 => 'Fishponds Road',
// 481 => 'CREATE Centre Roof',
// 500 => 'Temple Way',
// 501 => 'Colston Avenue'

$st = microtime(true); // testing run time

$fileHeaders = ['siteID', 'ts', 'nox', 'no2', 'no', 'pm10', 'nvpm10', 'vpm10', 'nvpm2.5', 'pm2.5', 'vpm2.5', 'co', 'o3', 'so2', 'loc',
    'lat', 'long']; // first line for each output csv file
$fileNames = ['data-188.csv', 'data-203.csv', 'data-206.csv', 'data-209.csv', 'data-213.csv', 'data-215.csv',
    'data-228.csv', 'data-270.csv', 'data-271.csv', 'data-375.csv', 'data-395.csv', 'data-447.csv', 'data-452.csv',
    'data-459.csv', 'data-463.csv', 'data-481.csv', 'data-500.csv', 'data-501.csv']; // names for the output files

//$inputFile = 'fragments.csv'; // testing
$inputFile = 'air-quality-data-2004-2019.csv'; // getting input file for air pollution

$out188 = fopen($fileNames[0], 'a'); // opening files to be written to
$out203 = fopen($fileNames[1], 'a');
$out206 = fopen($fileNames[2], 'a');
$out209 = fopen($fileNames[3], 'a');
$out213 = fopen($fileNames[4], 'a');
$out215 = fopen($fileNames[5], 'a');
$out228 = fopen($fileNames[6], 'a'); // could be done with a loop, however I prefer to have useful
$out270 = fopen($fileNames[7], 'a'); // variable names
$out271 = fopen($fileNames[8], 'a');
$out375 = fopen($fileNames[9], 'a');
$out395 = fopen($fileNames[10], 'a');
$out447 = fopen($fileNames[11], 'a');
$out452 = fopen($fileNames[12], 'a');
$out459 = fopen($fileNames[13], 'a');
$out463 = fopen($fileNames[14], 'a');
$out481 = fopen($fileNames[15], 'a');
$out500 = fopen($fileNames[16], 'a');
$out501 = fopen($fileNames[17], 'a');

$row = 1; // row counting initation

if (($handle = fopen($inputFile, "r")) !== FALSE) { // opening the input file & checking the data exists

    while (($data = fgets($handle)) !== FALSE) { // checking the row exists
        $data = explode(";", $data); // turing each row into an array

        if ($row > 1) { // skipping the first row.
            if (($data[1] != '') || ($data[11] != '')) { // checking nox or co exist for this row

                $current = $data; // rearranging the array to suit the new requirements
                array_unshift($current, $data[4]);
                array_splice($current, 5, 1);
                array_splice($current, 14, 3);
                array_splice($current, 16, 3);
                $current[1] = strtotime($current[1]); // changing the string to a time stamp

                $site_id = $current[0]; // getting the site id of the row
                switch ($site_id) { // switch to write the new data to the relevant csv file
                    case 188:
                        fputcsv($out188, $current, ';');
                        break;
                    case 203:
                        fputcsv($out203, $current, ';');
                        break;
                    case 206:
                        fputcsv($out206, $current, ';');
                        break;
                    case 209:
                        fputcsv($out209, $current, ';');
                        break;
                    case 213:
                        fputcsv($out213, $current, ';');
                        break;
                    case 215:
                        fputcsv($out215, $current, ';');
                        break;
                    case 228:
                        fputcsv($out228, $current, ';');
                        break;
                    case 270:
                        fputcsv($out270, $current, ';');
                        break;
                    case 271:
                        fputcsv($out271, $current, ';');
                        break;
                    case 375:
                        fputcsv($out375, $current, ';');
                        break;
                    case 395:
                        fputcsv($out395, $current, ';');
                        break;
                    case 447:
                        fputcsv($out447, $current, ';');
                        break;
                    case 452:
                        fputcsv($out452, $current, ';');
                        break;
                    case 459:
                        fputcsv($out459, $current, ';');
                        break;
                    case 463:
                        fputcsv($out463, $current, ';');
                        break;
                    case 481:
                        fputcsv($out481, $current, ';');
                        break;
                    case 500:
                        fputcsv($out500, $current, ';');
                        break;
                    case 501:
                        fputcsv($out501, $current, ';');
                        break;
                }
            }

        }
        else { // on the first row, simply write the new file headers to the new file
            foreach ($fileNames as $file) {
                $out = fopen($file, 'w');
                fputcsv($out, $fileHeaders, ';');
                fclose($out);
            }
        }
        $row++;
    }
}
fclose($handle); // close out opened files
fclose($out188);
fclose($out203);
fclose($out206);
fclose($out209);
fclose($out213);
fclose($out215);
fclose($out228);
fclose($out270);
fclose($out271);
fclose($out375);
fclose($out395);
fclose($out447);
fclose($out452);
fclose($out459);
fclose($out463);
fclose($out481);
fclose($out500);
fclose($out501);


echo '<p>It took '; // displaying the run time of this operation
echo microtime(true) - $st;
echo ' seconds to write file to CSVs.</p>'; // in testing between it takes 15 and 20 seconds on my laptop
// Intel(R) Core(TM) i5-7300HQ CPU @ 2.50GHz   2.50 GHz
// 16GB RAM
// in power saving mode, worryingly, it takes 20 - 30 seconds
?>