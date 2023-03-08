<?php

class PedidoGateway {
    private PDO $conn;
    public function __construct(Database $database) {
        $this->conn = $database->getConnection();
    }

    public function getAll(): array {
        $sql = "SELECT * FROM pedido";

        $stmt = $this->conn->query($sql);

        $data = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row["total"] = (float) $row["total"];
            $data[] = $row;
        }

        return $data;
    }

    public function create(array $data): string {
        $sql = "INSERT INTO pedido (data, total) VALUES (:data, :total)";

        $stmt = $this->conn->prepare($sql);
        $date = new DateTime($data["data"]);

        $stmt->bindValue(":data", $date->format('Y-m-d H:i:s'), PDO::PARAM_STR);
        $stmt->bindValue(":total", floatval($data["total"]), PDO::PARAM_STR);

        $stmt->execute();
        return $this->conn->lastInsertId();
        //http_response_code(201);
        /* http_response_code(500);
        return json_encode(array("message" => "Unable to add data.")); */
    }
}
