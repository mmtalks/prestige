<?php

require '../vendor/autoload.php';

use \PhpOffice\PhpSpreadsheet\Reader\Csv;
use \Exception;

/**
 * @author Mateus Mesquita (github.com/mmtalks).
 * @version 0.1.0
 * Use the method main to start the data extraction.
 */

class Prestige
{

    private $added_lines = 0;
    private $ignored_lines = 0;
    private $log = array();
    private $spreadsheet;
    private $labels_line;
    private $dictionary = array();

    /**
     * @param string $spreadsheet = absolute filepath of the .csv file.
     * @param array $dictionary = the relationship between columns on spreadsheet and columns on database's table.
     * @param int $labels_line is the line with the spreadsheet's headers/labels.
     */
    function __construct($spreadsheet, $labels_line, $dictionary){


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

    }

    
    public function main(){

        $reader = new Csv();
        $current_spreadsheet = $reader->load($this->spreadsheet);
        $current_spreadsheet = $current_spreadsheet->getActiveSheet();

        $current_line = $this->labels_line;
       
        while(true){

            if(!$current_spreadsheet->getCell($this->dictionary[0]['spreadsheet_column'].$current_line)->getValue()){
                break;
            }

            $current_row = array();

            foreach($this->dictionary as $spreadsheet_column){
                $current_row = $current_row + [$spreadsheet_column['db_table_column']=>$current_spreadsheet->getCell($spreadsheet_column['spreadsheet_column'].$current_line)->getValue()];
            }

            
            $current_line++;
        }
        

    }

    public function translate(){}

    public function preconditions(){}

    public function postconditions(){}

    public function transaction(){}

    public function log($line, $action_type, $action_description){
        array_push($this->log, ["spreadsheet_line"=>$line, "action_type"=>$action_type, "message"=>$action_description]);
    }

}