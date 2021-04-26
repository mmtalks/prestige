# Prestige

## Requirements to use

- PHP
- composer
- Phpspreadsheet installed via composer

## How to use

- Clone this repository or copy the source code in Prestige/Prestige.php
- Install dependencies (Phpspreadsheet)
- Start to use like as another class.


## Example of use

```

$csv = new Prestige('/var/www/html/example.csv', 1, 1000, array(   
                               ["spreadsheet_column"=>"A", "db_table_column"=>"id"]));
echo var_dump($csv->get());
echo var_dump($csv->inspect());
echo $csv->table();

```
## API

|methotd|description|
|-|-|
|get|Return the data extracted from the csv file.|
|inspect|Return the log about the data's extraction.|
|table|Return one HTML code to mount a table with the data from the csv file.|


## License

Copyright (c) 2021 Mateus Mesquita

Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
"Software"), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.