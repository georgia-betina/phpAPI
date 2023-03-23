<?php

require_once "./src/gateway/ProdutoGateway.php";
require_once "./src/Database.php";

class ProdutoController {
    private ProdutoGateway $gateway;
    public function __construct(private Database $database) {
        $this->gateway = new ProdutoGateway($database);
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
        $produto = $this->gateway->get($id);

        if (!$produto) {
            http_response_code(404);
            echo json_encode(["message" => "Produto não encontrado!"]);
            return;
        }

        switch ($method) {
            case "GET":
                echo json_encode($produto);
                break;
            case "PATCH":
                $data = (array) json_decode(file_get_contents("php://input"), true);
            
                $errors = $this->getValidationErrors($data, false);

                if (! empty($errors)) {
                    http_response_code(422);
                    echo json_encode(["errors" => $errors]);
                    break;
                }

                $rows = $this->gateway->update($produto, $data);

                echo json_encode([
                    "message" => "Produto $id atualizado",
                    "linhas" => $rows
                ]);

                break;
            case "DELETE":
                $rows = $this->gateway->delete($id);

                echo json_encode([
                    "message" => "Produto $id deletado",
                    "rows" => $rows
                ]);
                break;
            default:
                http_response_code(405);
                header("Allow: GET, PATCH, DELETE");
        }

        echo json_encode($produto);
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
                    "message" => "Produto criado",
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

        if ($is_new && empty($data["nome"])) {
            $errors[] = "NOME: Insira um nome válido";
        }

        if ($is_new && array_key_exists("valor", $data)) {
            if (filter_var($data["valor"], FILTER_VALIDATE_FLOAT) === false) {
                $errors[] = "VALOR: Insira um número positivo e diferente de 0.";
            }
        }

        if ($is_new && array_key_exists("tipo", $data)) {
            if (filter_var($data["tipo"], FILTER_VALIDATE_INT) === false) {
                $errors[] = "TIPO: Insira um número válido.";
            }
        }

        return $errors;
    }
}