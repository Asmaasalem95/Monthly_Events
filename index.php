<?php

/**
 * Interface fileCreator
 */
interface fileCreator
{
    public function create();
}

/**
 * Class csvCreator
 */
class csvCreator implements fileCreator
{

    /**
     * @var
     */
    private $data;
    /**
     * @var
     */
    private $headerRow;

    /**
     * @var
     *
     */
    private $fileName;

    /**
     * csvCreator constructor.
     * @param $data
     * @param $headerRow
     * @param $fileName
     */
    public function __construct($data, $headerRow, $fileName)
    {
        $this->data = $data;
        $this->headerRow = $headerRow;
        $this->fileName = $fileName;
    }

    /**
     * @return bool
     */
    public function create()
    {

        //Set the Content-Type and Content-Disposition headers.
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename="' . $this->fileName . '"');
        header("Content-Transfer-Encoding: UTF-8");

        try {
            //Open up a PHP output stream using the function fopen.
            $fp = fopen('php://output', 'w');


            fputcsv($fp, $this->headerRow);
            //Loop through the array containing our CSV data.
            foreach ($this->data as $row) {
                //fputcsv formats the array into a CSV format.
                fputcsv($fp, $row);
            }

            //Close the file handle.
            return fclose($fp);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }

    }
}

/**
 * Class App
 */
class App
{

    /**
     * @return string
     */
    public function index()
    {
        //Set header row
        $header = array("Month", "Brunch & Catchup", "Thirsty Thursday", "Friday Fry-up");
        // The name of the CSV file that will be downloaded by the user.
        $fileName = 'monthly_events.csv';
        try {
            $data = $this->getMonthlyEventsData();
            $csvFile = new csvCreator($data, $header, $fileName);
            $csvCreated = $csvFile->create();
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * @return array
     */
    public function getMonthlyEventsData()
    {
        //A multi-dimensional array containing our CSV data.
        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $month = date("Y-m", strtotime(date('Y-m-01') . " +$i months"));
            $thirdThursday = date("d-M-Y", strtotime("third Thursday of this month", strtotime($month)));
            $firstMonday = date("d-M-Y", strtotime("first monday of this month ", strtotime($month)));
            $lastFriday = date("d-M-Y", strtotime("last friday of this month", strtotime($month)));

            $monthData = [];
            array_push($monthData, $month, $firstMonday, $thirdThursday, $lastFriday);
            array_push($data, $monthData);
        }

        return $data;
    }

}
 $app = new App();
return $app->index();
