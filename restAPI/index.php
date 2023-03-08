<?php

declare(strict_types=1);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

spl_autoload_register(function ($class) {
	require __DIR__ . "/src/$class.php";
});

set_exception_handler("ErrorHandler::handleException");

header("Content-type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Origin, Content-Type, Accept");

$parts = explode("/", $_SERVER["REQUEST_URI"]);

//print_r($parts);
// Exemplo sobre como as rotas são dispostas dentro de um array
// Lembrando que, por ter uma rota chamada restAPI antes de qualquer outra rota no site,
// o array sempre retornará, pelo menos, dois itens (considerando que esteja acessando
// o index) | Array ( [0] => [1] => restAPI [2] => )
// Array ( [0] => (index.php) [1] => restAPI [2] => (index.php) )
// |_ htdocs (0)
//  |_ restAPI (1)
//   |_ index.php (2)

if ($parts[2] != "pedido") {
	http_response_code(404);
	exit;
}

$id = $parts[3] ?? null;

$database = new Database("localhost", "restful", "root", "");

$gateway = new PedidoGateway($database);

//var_dump($id);

$controller = new PedidoController($gateway);
$controller->processRequest($_SERVER["REQUEST_METHOD"], $id);

?>