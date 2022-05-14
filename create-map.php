<?php
@date_default_timezone_set('GMT'); // setup to allow code to run
ini_set('memory_limit', '512M');
ini_set('max_execution_time', '300');
ini_set('auto_detect_line_endings', true);

//Determine if variable is set and is not NULL
if (isset($_GET['pollutant'])) {
    $getPollutant = $_GET['pollutant'];

    $inputFileNames = ['data-188.xml', 'data-203.xml', 'data-206.xml', 'data-209.xml', 'data-213.xml', 'data-215.xml',
        'data-228.xml', 'data-270.xml', 'data-271.xml', 'data-375.xml', 'data-395.xml', 'data-447.xml', 'data-452.xml',
        'data-459.xml', 'data-463.xml', 'data-481.xml', 'data-500.xml', 'data-501.xml']; // names of the input files

    $stations = [];
    foreach ($inputFileNames as $inputFileName) {
        $xml = simplexml_load_file("../" . $inputFileName);
        $result = $xml->xpath("//station");

        foreach ($result as $single) {

            // Interprets a string of XML into an object
            //Returns an object of class SimpleXMLElement with properties containing the data held within the xml document
            $reading = simplexml_load_string($single->asXML());
            $stationId = (int)$reading->attributes()->id;
            $station = (string)$reading->attributes()->name;
            $geocode = (string)$reading->attributes()->geocode;
            $geocode = explode(',', $geocode);
            $geocode[0] = (float)$geocode[0];
            $geocode[1] = (float)$geocode[1];

            $station = ['id' => $stationId, 'station' => $station, 'geocode' => $geocode];

            $newResult = $xml->xpath("//rec");

            $i = 0;
            $totalPollutant = (float)0.0;
            foreach ($newResult as $newSingle) {
                $newReading = simplexml_load_string($newSingle->asXML());
                if ($newReading->attributes()->$getPollutant) {
                    $i++;
                    $newPollutant = $newReading->attributes()->$getPollutant;
                    $totalPollutant = $totalPollutant + $newPollutant;
                }
            }
            if ($totalPollutant > 0.0 && $i > 0) {
                $totalPollutant = ($totalPollutant / $i);
            }
            $station['value'] = $totalPollutant;
            $station['colour'] = colour($totalPollutant);
            array_push($stations, $station);
        }
    }
    $stations = json_encode($stations);
    echo $stations;
}

function colour($val)
{
    if ($val >= 0 && $val <= 67) {
        return "'#9CFF9C'";
    }
    if ($val >= 68 && $val <= 134) {
        return "#31FF00";
    }
    if ($val >= 135 && $val <= 200) {
        return "#31CF00";
    }
    if ($val >= 201 && $val <= 267) {
        return "#FFFF00";
    }
    if ($val >= 268 && $val <= 334) {
        return "#FFCF00";
    }
    if ($val >= 335 && $val <= 400) {
        return "#FF9A00";
    }
    if ($val >= 401 && $val <= 467) {
        return "#FF6464";
    }
    if ($val >= 468 && $val <= 534) {
        return "FF0000";
    }
    if ($val >= 535 && $val <= 600) {
        return "#990000";
    }
    if ($val >= 601) {
        return "#CE30FF";
    } else {
        return "#DAF7A6";
    }

}