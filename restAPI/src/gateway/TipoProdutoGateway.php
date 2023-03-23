<?php

class TipoProdutoGateway
{
    private PDO $conn;
    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();
    }

    public function getAll(): array
    {
        $sql = "SELECT * FROM tipo_produto WHERE ativado = 1";

        $stmt = $this->conn->query($sql);

        $data = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row["percentual_imposto"] = (float) $row["percentual_imposto"];
            $data[] = $row;
        }

        return $data;
    }

    public function create(array $data): string
    {
        $sql = "INSERT INTO tipo_produto (nome, percentual_imposto) VALUES (:nome, :percentual_imposto)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":nome", $data["nome"], PDO::PARAM_STR);
        $stmt->bindValue(":percentual_imposto", floatval($data["percentual_imposto"]), PDO::PARAM_STR);

        $stmt->execute();

        return $this->conn->lastInsertId();
    }

    public function get(string $id): array
    {
        $sql = "SELECT * FROM `tipo_produto` WHERE ativado = 1 AND codigo = :codigo";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":codigo", $id, PDO::PARAM_INT);

        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data === false) {
            return [];
        }

        return $data;
    }

    public function update(array $current, array $new): array
    {
        $sql = "UPDATE tipo_produto SET nome = :nome, percentual_imposto = :percentual_imposto, ativado = :ativado WHERE codigo = :codigo";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":nome", $new["nome"] ?? $current["nome"], PDO::PARAM_STR);
        $stmt->bindValue(":percentual_imposto", $new["percentual_imposto"] ?? floatval($current["percentual_imposto"]), PDO::PARAM_STR);
        $stmt->bindValue(":ativado", $new["ativado"] ?? intval($current["ativado"]), PDO::PARAM_INT);
        $stmt->bindValue(":codigo", $current["codigo"], PDO::PARAM_INT);

        $stmt->execute();

        echo $stmt->rowCount();

        // fetch the updated row
        $sql = "SELECT * FROM tipo_produto WHERE codigo = :codigo";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":codigo", $current["codigo"], PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function delete(string $id): int
    {
        $sql = "DELETE FROM tipo_produto WHERE codigo = :codigo";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":codigo", $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();
    }
}
