<?php
@date_default_timezone_set('GMT'); // setup to allow code to run
ini_set('memory_limit', '512M');
ini_set('max_execution_time', '300');
ini_set('auto_detect_line_endings', true);

//Determine if variable is set and is not NULL
if (isset($_GET['date'])) {
    $getDate = $_GET['date'];
    $getStation = $_GET['station'];

    //set the date format
    $getDateTs = strtotime($getDate);
    $dateFormat = "d/m/Y H:i:s";
    $year = date($dateFormat, $getDateTs);
    $start = DateTime::createFromFormat($dateFormat, $year); // gets required dates for the time period selected
    $end = DateTime::createFromFormat($dateFormat, $year);
    $end = $end->add(new DateInterval('P1Y')); // adds one year to get end date

//load required xml.
    $xml = simplexml_load_file("../data-" . $getStation . ".xml");


    $result = $xml->xpath("//rec");

    usort($result, 'sortDates');

//https://stackoverflow.com/questions/18286735/building-a-google-chart-with-php-and-mysql
//create Multidimensional Array to hold column information
    $rows = array();
    $table = array();
    $table["cols"] = array(
        array("label" => "date/time", "type" => "date"),
        array("label" => "NO", "type" => "number")
    );

    $val = 0; // variables to be used in the loop
    $i = 0;
//  for each value in result from Xpath.
    foreach ($result as $single) {

        // Interprets a string of XML into an object
        //Returns an object of class SimpleXMLElement with properties containing the data held within the xml document
        $reading = simplexml_load_string($single->asXML());

        $date = (int)($reading->attributes()->ts); // gets the ts from record
        $date = date($dateFormat, $date);
        $date = DateTime::createFromFormat($dateFormat, $date); // formats the date to be used
//        echo print_r($date);
        if (($start < $date) && ($date < $end) && (str_contains(($date->format($dateFormat)), '08:00:00'))) { // checks the date and time are within the selected limits and time
            // I thought carbon monoxide was co but the spec says no so that is what is shown
            $day = (($date->format($dateFormat)[0]) . ($date->format($dateFormat)[1])); // only provides a result on the 28th days, as february is only 28 days long
            if ($day != 28) {
                $i++;
                $val = $val + $reading->attributes()->no;
            } else {
                $i++;
                $val = $val + $reading->attributes()->no;
                $val = ($val / $i);

                //Format Date correct for google charts.
                $dateFormatted = "Date("
                    . date("Y", $date->format("U")) . ", "
                    . (date("m", $date->format("U")) - 1) . ", "
                    . date("d", $date->format("U")) . ", "
                    . date("H", $date->format("U")) . ", "
                    . date("i", $date->format("U")) . ", "
                    . date("s", $date->format("U")) . ")";


                //Syntax for associative arrays:
                //array(key=>value,key=>value,key=>value,etc.);
                $temp = array();
                $temp[] = array("v" => $dateFormatted); //add value
                $temp[] = array("v" => (int)($val)); //add


                //https://stackoverflow.com/questions/31380320/change-point-colour-based-on-value-for-google-scatter-chart?rq=1
                //add colour to the points.
                $colour = colour($val);
                $temp[] = array("v" => $colour);


                $rows[] = array("c" => $temp); //add row to new column
                $val = 0;
                $i = 0;
            }
        }
    }

    $table["rows"] = $rows; // adds the rows to the table
    $finalTable = json_encode($table);
    echo $finalTable; // returns final table as json response
}

function colour($val) // function to determine colour of scatter point
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

function sortDates($a, $b) // function to sort dates
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

?>
