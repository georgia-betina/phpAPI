<?php

class PedidoController {
    public function __construct(private PedidoGateway $gateway) {

    }

    public function processRequest(string $method, ?string $id): void {
        //var_dump($method, $id);
        if ($id) {
            $this->processResourceRequest($method, $id);
        } else {
            $this->processCollectionRequest($method);
        }
    }

    private function processResourceRequest(string $method, string $id): void {

    }

    private function processCollectionRequest(string $method): void {
        switch ($method) {
            case "GET":
                echo json_encode($this->gateway->getAll());
                break;

            case "POST":
                $data = (array) json_decode(file_get_contents("php://input"), true);
            
                $id = $this->gateway->create($data);

                $response = [
                    "message" => "Pedido criado",
                    "codigo" => $id
                ];

                http_response_code(201);
                header('Content-Type: application/json');
                echo json_encode($response);
                break;
        }
    }
}