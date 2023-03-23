<?php

class PedidoGateway
{
    private PDO $conn;

    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();
    }

    public function getAll(string $id): array
    {
        /* $sql = "SELECT * FROM pedido"; */
        $sql = "SELECT pedido.codigo AS codigo_pedido,
        pedido.data AS data_pedido,
        produto_pedido.quantidade AS quantidade,
        produto_pedido.total AS total,
        produto.nome AS nome_produto,
        tipo_produto.nome AS nome_tipo_produto,
        tipo_produto.percentual_imposto AS percentual_imposto
 FROM pedido
 INNER JOIN produto_pedido ON pedido.codigo = produto_pedido.pedido
 INNER JOIN produto ON produto_pedido.produto = produto.codigo
 INNER JOIN tipo_produto ON produto.tipo = tipo_produto.codigo";


        $stmt = $this->conn->prepare($sql);
        /* $stmt->bindValue(":codigo", $id); */

        $stmt->execute();

        $data = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row["total"] = (float) $row["total"];
            $data[] = $row;
        }

        return $data;
    }

    public function create(array $data): string
    {
        // begin a transaction
        $this->conn->beginTransaction();

        // first query
        $sql = "INSERT INTO pedido (data, total) VALUES (:data, :total)";
        $stmt = $this->conn->prepare($sql);
        $date = new DateTime($data["data"]);
        $stmt->bindValue(":data", $date->format('Y-m-d H:i:s'), PDO::PARAM_STR);
        $stmt->bindValue(":total", floatval($data["total"]), PDO::PARAM_STR);
        $stmt->execute();

        $pedido_id = $this->conn->lastInsertId();

        // second query
        $sql = "INSERT INTO produto_pedido (pedido, produto, quantidade, total) VALUES (:pedido, :produto, :quantidade, :total)";
        $stmt = $this->conn->prepare($sql);
        foreach ($data["produtos"] as $produto) {
            $stmt->bindValue(":pedido", $pedido_id, PDO::PARAM_INT);
            $stmt->bindValue(":produto", intval($produto["produto"]), PDO::PARAM_INT);
            $stmt->bindValue(":quantidade", intval($produto["quantidade"]), PDO::PARAM_INT);
            $stmt->bindValue(":total", floatval($produto["total"]), PDO::PARAM_STR);
            $stmt->execute();
        }
        $this->conn->commit();
        return $this->conn->lastInsertId();
    }

    public function get(string $id): array
    {
        $sql = "SELECT pedido.codigo AS codigo_pedido,
        pedido.data AS data_pedido,
        produto_pedido.quantidade AS quantidade,
        produto_pedido.total AS total,
        produto.nome AS nome_produto,
        tipo_produto.nome AS nome_tipo_produto,
        tipo_produto.percentual_imposto AS percentual_imposto
        FROM pedido
        INNER JOIN produto_pedido ON pedido.codigo = produto_pedido.pedido
        INNER JOIN produto ON produto_pedido.produto = produto.codigo
        INNER JOIN tipo_produto ON produto.tipo = tipo_produto.codigo";

        $stmt = $this->conn->prepare($sql);
        /* $stmt->bindValue(":codigo", $id); */
        $stmt->execute();

        $data = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row["total"] = (float) $row["total"];
            $data[] = $row;
        }

        return $data;
    }

    public function update(array $current, array $new): int
    {
        $sql = "UPDATE pedido SET data = :data, total = :total WHERE codigo = :codigo";

        $stmt = $this->conn->prepare($sql);
        $date = new DateTime($current["data"]);

        $stmt->bindValue(":data", $new["data"] ?? $date->format('Y-m-d H:i:s'), PDO::PARAM_STR);
        $stmt->bindValue(":total", $new["total"] ?? floatval($current["total"]), PDO::PARAM_STR);
        $stmt->bindValue(":codigo", $current["codigo"], PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();
    }

    public function delete(string $id): int
    {
        $sql = "DELETE FROM pedido WHERE codigo = :codigo";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":codigo", $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();
    }

    public function validateData(array $data): bool
    {
        // check if required fields are present
        if (!isset($data["data"]) || !isset($data["total"]) || !isset($data["produtos"])) {
            return false;
        }

        // check if data is in the correct format
        $date = DateTime::createFromFormat('d-m-Y H:i:s', $data["data"]);
        if (!$date) {
            return false;
        }

        // check if total is a valid number
        if (!is_numeric($data["total"])) {
            return false;
        }

        // check if produtos is an array
        if (!is_array($data["produtos"])) {
            return false;
        }

        // check each produto
        foreach ($data["produtos"] as $produto) {
            // check if required fields are present
            if (!isset($produto["produto"]) || !isset($produto["quantidade"]) || !isset($produto["totalQuantidadePedida"])) {
                return false;
            }

            // check if values are valid numbers
            if (!is_numeric($produto["produto"]) || !is_numeric($produto["quantidade"]) || !is_numeric($produto["totalQuantidadePedida"])) {
                return false;
            }
        }

        return true;
    }
}
