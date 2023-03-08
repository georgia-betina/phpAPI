<?php

class ProdutoPedidoGateway {
    private PDO $conn;
    public function __construct(Database $database) {
        $this->conn = $database->getConnection();
    }

    public function getAll(): array {
        $sql = "SELECT * FROM produto_pedido";

        $stmt = $this->conn->query($sql);

        $data = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row["pedido"] = (int) $row["pedido"];
            $row["produto"] = (int) $row["produto"];
            $row["quantidade"] = (int) $row["quantidade"];
            $row["total"] = (float) $row["total"];
            $data[] = $row;
        }

        return $data;
    }

    public function create(array $data): string {
        $sql = "INSERT INTO produto_pedido (pedido, produto, quantidade, total) VALUES (:pedido, :produto, :quantidade, :total)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":pedido", intval($data["pedido"]), PDO::PARAM_STR);
        $stmt->bindValue(":produto", intval($data["produto"]), PDO::PARAM_STR);
        $stmt->bindValue(":quantidade", intval($data["quantidade"]), PDO::PARAM_STR);
        $stmt->bindValue(":total", floatval($data["total"]), PDO::PARAM_STR);

        $stmt->execute();

        return $this->conn->lastInsertId();
    }

    public function get(string $id): array {
        $sql = "SELECT * FROM produto_pedido WHERE codigo = :codigo";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":codigo", $id, PDO::PARAM_INT);

        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data === false) {
            return [];
        }

        return $data;
    }

    public function update(array $current, array $new): array {
        $sql = "UPDATE produto_pedido SET pedido = :pedido, produto = :produto, quantidade = :quantidade, total = :total WHERE codigo = :codigo";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":pedido", $new["pedido"] ?? intval($current["pedido"]), PDO::PARAM_STR);
        $stmt->bindValue(":produto", $new["produto"] ?? intval($current["produto"]), PDO::PARAM_STR);
        $stmt->bindValue(":quantidade", $new["quantidade"] ?? intval($current["quantidade"]), PDO::PARAM_STR);
        $stmt->bindValue(":total", $new["total"] ?? floatval($current["total"]), PDO::PARAM_STR);
        $stmt->bindValue(":codigo", $current["codigo"], PDO::PARAM_INT);

        $stmt->execute();

        echo $stmt->rowCount();

         // fetch the updated row
        $sql = "SELECT * FROM produto_pedido WHERE codigo = :codigo";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":codigo", $current["codigo"], PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function delete(string $id): int {
        $sql = "DELETE FROM produto_pedido WHERE codigo = :codigo";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":codigo", $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();    
    }
}
