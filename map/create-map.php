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

    $stations = []; // variable used within the loop
    foreach ($inputFileNames as $inputFileName) { //loops through all files
        $xml = simplexml_load_file("../" . $inputFileName); // opens xml as simplexml
        $result = $xml->xpath("//station"); // gets all stations

        foreach ($result as $single) { // for each xml result

            // Interprets a string of XML into an object
            //Returns an object of class SimpleXMLElement with properties containing the data held within the xml document
            $reading = simplexml_load_string($single->asXML());
            $stationId = (int)$reading->attributes()->id; // sets variables accordingly
            $station = (string)$reading->attributes()->name;
            $geocode = (string)$reading->attributes()->geocode;
            $geocode = explode(',', $geocode);
            $geocode[0] = (float)$geocode[0]; // formats geocode correctly
            $geocode[1] = (float)$geocode[1];

            $station = ['id' => $stationId, 'station' => $station, 'geocode' => $geocode]; // format station object correctly

            $newResult = $xml->xpath("//rec"); // gets all xml records for this file

            $i = 0; // variables used in loop
            $totalPollutant = (float)0.0;
            foreach ($newResult as $newSingle) { // for each xml result
                $newReading = simplexml_load_string($newSingle->asXML());
                if ($newReading->attributes()->$getPollutant) { // if reading has pollutant attribute get it
                    $i++; // add one to number of records
                    $newPollutant = $newReading->attributes()->$getPollutant;
                    $totalPollutant = $totalPollutant + $newPollutant; // add to total pollutant for that station
                }
            }
            if ($totalPollutant > 0.0 && $i > 0) { // after loop divide total by number of records if any exist
                $totalPollutant = ($totalPollutant / $i);
            }
            $station['value'] = $totalPollutant; // add value to the station object
            $station['colour'] = colour($totalPollutant); // add colour
            array_push($stations, $station); // add station to station array
        }
    }
    $stations = json_encode($stations);
    echo $stations; // return station as json
}

function colour($val) // function to get colours based on value of pollutant
{
    if ($val >= 0 && $val <= 67) {
        return "#9CFF9C";
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
        return "#9CFF9C";
    }

}