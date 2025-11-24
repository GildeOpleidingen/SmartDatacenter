<?php
    $json = file_get_contents('php://input');
    include_once 'DBConnection.php';
    include_once 'dbParser.php';

    //if (!empty($json)) {
        $parser = new DBParser();    
        $parser->storeActivity($json);
        http_response_code(200);
    //} else {
      //  http_response_code(400); // Bad Request
    //}    
?>


