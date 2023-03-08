<?php

class ProdutoGateway {
    private PDO $conn;
    public function __construct(Database $database) {
        $this->conn = $database->getConnection();
    }

    public function getAll(): array {
        $sql = "SELECT * FROM produto";

        $stmt = $this->conn->query($sql);

        $data = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row["valor"] = (float) $row["valor"];
            $row["tipo"] = (int) $row["tipo"];
            $data[] = $row;
        }

        return $data;
    }

    public function create(array $data): string {
        $sql = "INSERT INTO produto (nome, valor, tipo) VALUES (:nome, :valor, :tipo)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":nome", $data["nome"], PDO::PARAM_STR);
        $stmt->bindValue(":valor", floatval($data["valor"]), PDO::PARAM_STR);
        $stmt->bindValue(":tipo", intval($data["tipo"]), PDO::PARAM_STR);

        $stmt->execute();

        return $this->conn->lastInsertId();
    }

    public function get(string $id): array {
        $sql = "SELECT * FROM produto WHERE codigo = :codigo";

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
        $sql = "UPDATE produto SET nome = :nome, valor = :valor, tipo = :tipo WHERE codigo = :codigo";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":nome", $new["nome"] ?? $current["nome"], PDO::PARAM_STR);
        $stmt->bindValue(":valor", $new["valor"] ?? floatval($current["valor"]), PDO::PARAM_STR);
        $stmt->bindValue(":tipo", $new["tipo"] ?? intval($current["tipo"]), PDO::PARAM_STR);
        $stmt->bindValue(":codigo", $current["codigo"], PDO::PARAM_INT);

        $stmt->execute();

        echo $stmt->rowCount();

         // fetch the updated row
        $sql = "SELECT * FROM produto WHERE codigo = :codigo";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":codigo", $current["codigo"], PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);

        //return $stmt->rowCount();
    }

    public function delete(string $id): int {
        $sql = "DELETE FROM produto WHERE codigo = :codigo";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":codigo", $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();    
    }
}
