<?php
@date_default_timezone_set('GMT'); // setup to allow code to run
ini_set('memory_limit', '512M');
ini_set('max_execution_time', '300');
ini_set('auto_detect_line_endings', true);

$st = microtime(true); // testing run time

$attributes = ['siteID', 'ts', 'nox', 'no2', 'no', 'pm10', 'nvpm10', 'vpm10', 'nvpm2.5', 'pm2.5', 'vpm2.5', 'co', 'o3', 'so2', 'loc',
    'lat', 'long'];

//$inputFiles = ['data-209.csv', 'data-271.csv']; // test files
$inputFiles = ['data-188.csv', 'data-203.csv', 'data-206.csv', 'data-209.csv', 'data-213.csv', 'data-215.csv',
    'data-228.csv', 'data-270.csv', 'data-271.csv', 'data-375.csv', 'data-395.csv', 'data-447.csv', 'data-452.csv',
    'data-459.csv', 'data-463.csv', 'data-481.csv', 'data-500.csv', 'data-501.csv']; // names of the input files
//$outputFileNames = ['data-209.xml', 'data-271.xml']; // test files
$outputFileNames = ['data-188.xml', 'data-203.xml', 'data-206.xml', 'data-209.xml', 'data-213.xml', 'data-215.xml',
    'data-228.xml', 'data-270.xml', 'data-271.xml', 'data-375.xml', 'data-395.xml', 'data-447.xml', 'data-452.xml',
    'data-459.xml', 'data-463.xml', 'data-481.xml', 'data-500.xml', 'data-501.xml']; // names for the output files

$writer = new XMLWriter(); // xml writer initation
$writer->openMemory();

for ($i = 0; $i < count($inputFiles); $i++) {
    $row = 1; // row counting initation

    $writer->startDocument("1.0", "UTF-8");
    $writer->setIndent(true);
    $writer->startElement("station"); // station information

    if (($handle = fopen($inputFiles[$i], "rt")) !== FALSE) { // opening the input file & checking the data exists

        while (($data = fgets($handle)) !== FALSE) {
            $data = explode(";", $data); // turing each row into an array

            if ($row > 2) { // checking values exist for required fields
                $writer->startElement("rec"); // new record
                $writer->writeAttribute("ts", $data[1]); // ts attribute
                for ($x = 2; $x < 14; $x++) { // check for other values and add attributes
                    if ($data[$x] != '') {
                        $writer->writeAttribute($attributes[$x], $data[$x]);
                    }
                }
                $writer->endElement();
            } else if ($row == 2) {
                $writer->writeAttribute("id", $data[0]); // attributes for the station tag
                $writer->writeAttribute("name", $data[14]);
                $writer->writeAttribute("geocode", $data[15]);

                $writer->startElement("rec"); // new record for ts, nox, no, and no2
                $writer->writeAttribute("ts", $data[1]);
                for ($x = 2; $x < 14; $x++) { // check for other values and add attributes
                    if ($data[$x] != '') {
                        $writer->writeAttribute($attributes[$x], $data[$x]);
                    }
                }
                $writer->endElement();
            }
            $row++;
        }
        if ($i == 15) { // as station 481 does not have any records, this must be added in manually, however

            $writer->writeAttribute("id", '481');
            $writer->writeAttribute("name", 'CREATE Centre Roof');
            $writer->writeAttribute("geocode", '51.447213417,-2.62247405516');
        }

    }
    fclose($handle);
    $writer->endElement(); //</station>
    $writer->endDocument();
    file_put_contents($outputFileNames[$i], $writer->flush(true));
}

echo '<p>It took '; // displaying the run time of this operation
echo microtime(true) - $st;
echo ' seconds to write files to XMLs.</p>';

?>