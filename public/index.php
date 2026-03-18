<?php
    require_once __DIR__ . '/../vendor/autoload.php';

    use App\Routing\Router;

    $router = new Router();

    require __DIR__ . '/../src/Routing/routes.php';

    $router->dispatch();

    // $json = file_get_contents('php://input');
    // include_once __DIR__ . '/includes/database/DBParser.php';
    // if (!empty($json)) {
    //     $parser = new DBParser();    
    //     $parser->parseDataToDB($json);
    //     http_response_code(200);
    // } else {
    //    http_response_code(400); // Bad Request
    // }
    // require_once('./website/Dashboard.php');
?>


