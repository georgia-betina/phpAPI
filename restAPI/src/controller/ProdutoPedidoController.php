<?php

require_once "./src/gateway/ProdutoPedidoGateway.php";
require_once "./src/Database.php";

class ProdutoPedidoController {
    private ProdutoPedidoGateway $gateway;
    public function __construct(private Database $database) {
        $this->gateway = new ProdutoPedidoGateway($database);
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
        $produtoPedido = $this->gateway->get($id);

        if (!$produtoPedido) {
            http_response_code(404);
            echo json_encode(["message" => "Produto pedido não encontrado!"]);
            return;
        }

        switch ($method) {
            case "GET":
                echo json_encode($produtoPedido);
                break;
            case "PATCH":
                $data = (array) json_decode(file_get_contents("php://input"), true);
            
                $errors = $this->getValidationErrors($data, false);

                if (! empty($errors)) {
                    http_response_code(422);
                    echo json_encode(["errors" => $errors]);
                    break;
                }

                $rows = $this->gateway->update($produtoPedido, $data);

                echo json_encode([
                    "message" => "Produto pedido $id atualizado",
                    "linhas" => $rows
                ]);

                break;
            case "DELETE":
                $rows = $this->gateway->delete($id);

                echo json_encode([
                    "message" => "Produto pedido $id deletado",
                    "rows" => $rows
                ]);
                break;
            default:
                http_response_code(405);
                header("Allow: GET, PATCH, DELETE");
        }

        echo json_encode($produtoPedido);
    }

    private function processCollectionRequest(string $method): void {
        switch ($method) {
            case "GET":
                echo json_encode($this->gateway->getAll());
                break;

            case "POST":
                $data = (array) json_decode(file_get_contents("php://input"), true);
            
                $errors = $this->getValidationErrors($data);

                if (! empty($errors)) {
                    http_response_code(422);
                    echo json_encode(["errors" => $errors]);
                    break;
                }

                $id = $this->gateway->create($data);

                $response = [
                    "message" => "Produto pedido criado",
                    "codigo" => $id
                ];

                http_response_code(201);
                header('Content-Type: application/json');
                echo json_encode($response);
                break;
            default:
                http_response_code(405);
                header("Allow: GET, POST");
        }
    }

    private function getValidationErrors(array $data, bool $is_new = true): array {
        $errors = [];

        if ($is_new && empty($data["pedido"])) {
            if (filter_var($data["pedido"], FILTER_VALIDATE_INT) === false) {
                $errors[] = "PEDIDO: Insira um pedido válido/existente";
            }
        }

        if ($is_new && empty($data["produto"])) {
            if (filter_var($data["produto"], FILTER_VALIDATE_INT) === false) {
                $errors[] = "PRODUTO: Insira um produto válido/existente";
            }
        }

        if ($is_new && empty($data["quantidade"])) {
            if (filter_var($data["quantidade"], FILTER_VALIDATE_INT) === false) {
                $errors[] = "QUANTIDADE: Insira uma quantidade válida";
            }
        }

        if ($is_new && array_key_exists("total", $data)) {
            if (filter_var($data["total"], FILTER_VALIDATE_FLOAT) === false) {
                $errors[] = "TOTAL: Insira um número positivo e diferente de 0";
            }
        }

        return $errors;
    }
}