<?php

namespace App\Product\Model;

use Base\Model\Db;

class Product
{

    protected $conn;

    public function __construct()
    {
        $this->conn = Db::getInstance()->getConn();
    }

    public function create($data)
    {
        $query = $this->conn->prepare("INSERT INTO product (user_id, name, category, quantity, price) VALUES (:user_id, :name, :category, :quantity, :price)");
        $query->execute($data);

        return $this->conn->lastInsertId();
    }

    public function findById($id)
    {
        $query = $this->conn->prepare("SELECT * FROM product WHERE id = :id");
        $query->execute(['id' => $id]);

        return $query->fetch();
    }

    public function findAll($options)
    {
        $search = $options['search'];
        $page = $options['page'];
        $limit = $options['limit'];
        $userId = $options['userId'];

        $sql = "SELECT * FROM product";
        $patternValues = [];
        $pattern = [];

        if ($search) {
            $columns = ['name', 'category', 'quantity', 'price'];

            foreach ($columns as $column) {
                $pattern[] = "$column like ?";
                $patternValues[] = "%$search%";
            }

            $sql .= " WHERE (" . implode(" OR ", $pattern) . ")";
        }

        if ($userId && $search) {
            $sql .= " AND user_id = ?";
            $patternValues[] = $userId;
        } else {
            $sql .= " WHERE user_id = ?";
            $patternValues[] = $userId;
        }

        if ($page) {
            $offset = ($page - 1) * $limit;
            $sql .= " LIMIT $limit OFFSET $offset";
        }

        $query = $this->conn->prepare($sql);
        $query->execute($patternValues);

        return $query->fetchAll();
    }

    public function totalCount($options)
    {
        $search = $options['search'];
        $userId = $options['userId'];

        $sql = "SELECT COUNT(*) AS TOTAL_COUNT FROM product";
        $patternValues = [];

        if ($search) {
            $columns = ['name', 'category', 'quantity', 'price'];
            $pattern = [];

            foreach ($columns as $column) {
                $pattern[] = "$column like ?";
                $patternValues[] = "%$search%";
            }

            $sql .= " WHERE " . implode(" OR ", $pattern);
        }

        if ($userId && $search) {
            $sql .= " AND user_id = ?";
            $patternValues[] = $userId;
        } else {
            $sql .= " WHERE user_id = ?";
            $patternValues[] = $userId;
        }

        $query = $this->conn->prepare($sql);
        $query->execute($patternValues);
        $result = $query->fetch();

        return $result['TOTAL_COUNT'];
    }


    public function update($id, $data)
    {
        $query = $this->conn->prepare("UPDATE product SET name = :name, category = :category, quantity = :quantity, price = :price WHERE id = :id");
        $query->execute([...$data, 'id' => $id]);

        return $query->rowCount();
    }

    public function delete($id)
    {
        $query = $this->conn->prepare("DELETE FROM product WHERE id = :id");
        $query->execute(['id' => $id]);

        return $query->rowCount();
    }
}
