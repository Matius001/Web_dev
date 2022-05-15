<?php
@date_default_timezone_set('GMT'); // setup to allow code to run
ini_set('memory_limit', '512M');
ini_set('max_execution_time', '300');
ini_set('auto_detect_line_endings', true);

//Determine if variables are set and is not NULL
if (isset($_GET['date'])) {
    $getDate = $_GET['date'];
    $getStation = $_GET['station'];
    $getPollutant = $_GET['pollutant'];

    $getDateTs = strtotime($getDate); // gets dates required for time period selected and formats
    $dateFormat = "d/m/Y H:i:s";
    $day = date($dateFormat, $getDateTs);
    $start = DateTime::createFromFormat($dateFormat, $day);
    $end = DateTime::createFromFormat($dateFormat, $day);
    $end = $end->add(new DateInterval('P1D')); // adds one day to get end date

//load required xml.
    $xml = simplexml_load_file("../data-" . $getStation . ".xml");

    $result = $xml->xpath("//rec");


//need to sort array
//The comparison function must return an integer less than, equal to, or greater than zero if the first argument
//is considered to be respectively less than, equal to, or greater than the second.
    usort($result, 'sortDates');


//https://stackoverflow.com/questions/18286735/building-a-google-chart-with-php-and-mysql
//create Multidimensional Array to hold column information
    $rows = array();
    $table = array();
    $table["cols"] = array(
        array("label" => "date/time", "type" => "date"),
        array("label" => $getPollutant, "type" => "number")
    );


//set the date format
    $dateFormat = "d/m/Y H:i:s";


//for each value in result from Xpath.
    foreach ($result as $single) {

        // Interprets a string of XML into an object
        //Returns an object of class SimpleXMLElement with properties containing the data held within the xml document
        $reading = simplexml_load_string($single->asXML());

        $date = (int)($reading->attributes()->ts);
        $date = date($dateFormat, $date);
        $date = DateTime::createFromFormat($dateFormat, $date); // formats ts to date
        if (($start < $date) && ($date < $end)) { // checks the date is within specified range
            $val = $reading->attributes()->$getPollutant; // get the correct attribute

//Format Date correct for google charts.


            $dateFormatted = "Date("
                . date("Y", $date->format("U")) . ", "
                . (date("m", $date->format("U")) - 1) . ", "
                . date("d", $date->format("U")) . ", "
                . date("H", $date->format("U")) . ", "
                . date("i", $date->format("U")) . ", "
                . date("s", $date->format("U")) . ")";

            $temp = array();
            $temp[] = array("v" => $dateFormatted); //add value
            $temp[] = array("v" => (int)($val)); //add value
            $rows[] = array("c" => $temp); //add row to new column
        }
    }
    $table["rows"] = $rows; // adds rows to table
    $finalTable = json_encode($table);
    echo $finalTable; // returns table as json response

}

function sortDates($a, $b) //function to sort dates asc
{
    $dateFormat = "d/m/Y H:i:s";
    // Interprets a string of XML into an object
    $reading1 = simplexml_load_string($a->asXML());
    $reading2 = simplexml_load_string($b->asXML());

    //get dates and compare
    $date1 = (int)($reading1->attributes()->ts);
    $date2 = (int)($reading2->attributes()->ts);
    $date1 = date($dateFormat, $date1);
    $date2 = date($dateFormat, $date2);
    $date1 = DateTime::createFromFormat($dateFormat, ($date1));
    $date2 = DateTime::createFromFormat($dateFormat, ($date2));

    //return comparison
    if ($date1 == $date2) {
        return 0;
    }
    if ($date1 < $date2) {
        return -1;
    } else {
        return 1;
    }

}
