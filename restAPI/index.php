<?php

	declare(strict_types=1);

	/* ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL); */

	require "./src/controller/PedidoController.php";
	require "./src/controller/ProdutoController.php";
	require "./src/controller/ProdutoPedidoController.php";
	require "./src/controller/TipoProdutoController.php";
	require "./src/Database.php";
	require "./src/ErrorHandler.php";
	require "./cors.php";
	
	set_error_handler("ErrorHandler::handleError");
	set_exception_handler("ErrorHandler::handleException");
	
	header("Content-type: application/json; charset=UTF-8");
	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Methods: GET, POST, PATCH, OPTIONS");
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

	$database = new Database("localhost", "restful", "root", "");

	switch($parts[3]) {
		case "pedidos":
			$controller = new PedidoController($database);
			
			break;
		case "produtos":
			$controller = new ProdutoController($database);

			break;
		case "produtosPedidos":
			$controller = new ProdutoPedidoController($database);

			break;
		case "tipoProdutos":
			$controller = new TipoProdutoController($database);

			break;
		default:
			http_response_code(404);
			exit;
	}

	if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Methods: GET, POST");
		header("Access-Control-Allow-Headers: Content-Type");
		exit;
	}

	$id = $parts[4] ?? null;
	$controller->processRequest($_SERVER["REQUEST_METHOD"], $id);

	//var_dump($id);

?>