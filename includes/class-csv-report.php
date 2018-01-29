<?php

class CV_Export_CSV {

    private $type;
    private $separator;
    private $values_csv;
    private $headers_csv;

    function __construct($type, $val_csv, $head_csv=[], $filename='report', $sep=';') {

        $this->type = $type;
        $this->separator = $sep;
        $this->headers_csv = $head_csv;
        $this->values_csv = $val_csv;

        $generatedDate = date('d-m-y');

        $csvFile = $this->generate_csv();
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private", false);
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"" . $filename . " " .'_' . $generatedDate . ".csv\";");
        header("Content-Transfer-Encoding: binary");

        echo $csvFile;
        exit;
    }
        
    function generate_csv() {

        $csv_output = '';
        
        if(is_array($this->headers_csv) && $this->headers_csv != []){
            foreach ($this->headers_csv as $row) {
                $csv_output = $csv_output . $row . $this->separator;
            }
            $csv_output = substr($csv_output, 0, -1);
            $csv_output .= "\n";
        }else if(is_array($this->values_csv) && $this->values_csv != []){
            foreach ($this->values_csv[0] as $key=>$row) {
                $csv_output = $csv_output . $key . $this->separator;
            }
            $csv_output = substr($csv_output, 0, -1);
            $csv_output .= "\n";            
        }
        
        if(is_array($this->values_csv) && $this->values_csv != []){
            foreach ($this->values_csv as $rowr) {
                $csv_output .= implode($this->separator, $rowr);
                $csv_output .= "\n";
            }
        }

        return $csv_output;
    }

}
