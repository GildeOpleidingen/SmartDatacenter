<?php
    $json = file_get_contents('php://input');
    include_once 'DBParser.php';
    //if (!empty($json)) {
        $parser = new DBParser();    
        $parser->parseDataToDB($json);
        http_response_code(200);
    //} else {
      //  http_response_code(400); // Bad Request
    //}    
?>


