<?php

/**
 * @author Mateus Mesquita (github.com/mmtalks).
 * @version 0.1.0
 * Please read the tutorial on Github: https://github.com/mmtalks/prestige/
 */


/**
 * Uncomment to see all potentials errors and bugs.
 * ini_set('display_errors', 1);
 * ini_set('display_startup_errors', 1);
 * error_reporting(E_ALL);
 *  */

/**
 * Requires the autoload from the composer, and loads the PhpSpreadsheet library.
 *  */
require '../vendor/autoload.php';

use \PhpOffice\PhpSpreadsheet\Reader\Csv;
use \Exception;

class MaxOfLines extends Exception{}


class Prestige
{

    private $spreadsheet;
    private $labels_line;
    private $max_of_lines;
    private $dictionary = array();
    private $data = array();
    private $log = array();
    private $added_lines = array();
    private $ignored_lines = array();

    /**
     * @param string $spreadsheet = absolute filepath of the .csv file.
     * @param array $dictionary = the relationship between columns on spreadsheet and columns on database's table.
     * @param int $max_of_lines = the max of lines that the Prestige can read in the CSV file.
     * @param int $labels_line = the line with the spreadsheet's headers/labels.
     * 
     * Example:
     * $csv = new Prestige('/var/www/html/example.csv', 1, 1000, array(["spreadsheet_column"=>"A", "db_table_column"=>"id"]));
     * echo var_dump($csv->get());
     * echo var_dump($csv->inspect());
     * echo $csv->table();
     */
    function __construct($spreadsheet, $labels_line, $max_of_lines, $dictionary){


        if(!is_string($spreadsheet)){
            throw new Exception('The parameter $spreadsheet is not a string. Use this parameter to set the absolute path of the csv file.');
        }

        if(!is_array($dictionary)){
            throw new Exception('The parameter $dictionary is not an array. Use this parameter to set the relationship between spreadsheet\'s columns and the table\'s columns.');
        }

        if(!file_exists($spreadsheet)){
            throw new Exception('The spreadsheet doest no exist in the absolute filepath.');
        }

        if(!mime_content_type($spreadsheet) == "application/csv"){
            throw new Exception('This file is not a spreadsheet.');
        }

        if(!is_int($labels_line)){
            throw new Exception('The parameter $labels_line is not an integer.');
        }


        foreach($dictionary as $column){
            if(!is_string($column['spreadsheet_column'])){
                throw new Exception("The dictionary's elements MUST be string.");
            }
        }

        
        $this->spreadsheet = $spreadsheet;
        $this->dictionary = $dictionary;
        $this->labels_line = $labels_line;
        $this->max_of_lines = $this->labels_line+$max_of_lines;

    }

    /**
     * Extract data from the csv file.
     *  */    
    public function main(){

        $reader = new Csv();
        $current_spreadsheet = $reader->load($this->spreadsheet);
        $current_spreadsheet = $current_spreadsheet->getActiveSheet();

        $current_line = $this->labels_line+1;
       
        while(true){

            try{
                if($current_line>$this->max_of_lines){
                    throw new MaxOfLines('Beyond the max of lines.');
                }

            }catch(MaxOfLines $e){
                $this->log("max_of_lines", $current_line, $e->getMessage());
                break;
            }

            try{

                if(!$current_spreadsheet->getCell($this->dictionary[0]['spreadsheet_column'].$current_line)->getValue()){
                    break;
                }
    
                $current_row = array();
    
                foreach($this->dictionary as $spreadsheet_column){
                    $current_row = $current_row + [$spreadsheet_column['db_table_column']=>$current_spreadsheet->getCell($spreadsheet_column['spreadsheet_column'].$current_line)->getValue()];
                }
    
                array_push($this->data, $current_row);

                $this->log("added_line", $current_line, "");

            }catch(Exception $e){
                $this->log("ignored_line", $current_line, $e->getMessage());
            }
            
            
            $current_line++;
        }
        

    }

    /**
     * @param string $action_type with the type of action to record a new element on log.
     * @param int $line with the line that events happened.
     * @param string $description with a short description about the log's event.
     */
    private function log($action_type, $line, $description=""){
        switch($action_type){
            case "added_line":
                array_push($this->added_lines, $line);
                array_push($this->log, ["action_type"=>$action_type, "line"=>$line, "description"=>$description]);
                break;
            case "ignored_line":
                array_push($this->ignored_lines, $line);
                array_push($this->log, ["action_type"=>$action_type, "line"=>$line, "description"=>$description]);
                break;
            case "max_of_lines":
                array_push($this->log, ["action_type"=>$action_type, "line"=>$line, "description"=>$description]);
            default:
                break;
        }
    }

    /**
     * @return array with the log (added_lines, ignored_lines, and others actions).
     */
    public function inspect(){
        return ["log"=>$this->log, "added_lines"=>$this->added_lines, "ignored_lines"=>$this->ignored_lines];
    }

    /**
     * @return array with data from the csv file.
     */
    public function get(){
        return $this->data;
    }

    /**
     * @return HTML structure to mount a table with data from the csv file.
     */
    public function table(){
        $html = "";

        $html .= '<table id="prestige">';
        $html .= "<thead><tr>";

        foreach($this->dictionary as $th){
            $html .= "<td>";
            $html .= $th["db_table_column"];
            $html .= "</td>";   
        }


        $html .= "</tr></thead>";
        $html .= "<tbody>";

        foreach($this->data as $row){
            $html .= "<tr>";
            
            foreach($row as $td){
                $html .= "<td>";
                $html .= $td;
                $html .= "</td>";
            }

            $html .= "</tr>";
        }

        $html .= "</tbody>";
        $html .= "</table>";

        return $html;

    }

}