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
    }

    public function get(string $id): array {
        $sql = "SELECT * FROM pedido WHERE codigo = :codigo";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":codigo", $id, PDO::PARAM_INT);

        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data === false) {
            return [];
        }

        return $data;
    }

    public function update(array $current, array $new): int {
        $sql = "UPDATE pedido SET data = :data, total = :total WHERE codigo = :codigo";

        $stmt = $this->conn->prepare($sql);
        $date = new DateTime($current["data"]);

        $stmt->bindValue(":data", $new["data"] ?? $date->format('Y-m-d H:i:s'), PDO::PARAM_STR);
        $stmt->bindValue(":total", $new["total"] ?? floatval($current["total"]), PDO::PARAM_STR);
        $stmt->bindValue(":codigo", $current["codigo"], PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();
    }

    public function delete(string $id): int {
        $sql = "DELETE FROM pedido WHERE codigo = :codigo";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":codigo", $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();    
    }
}
