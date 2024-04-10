<?php

namespace App\User\Model;

use Base\Model\Db;

class User
{
    protected $conn;

    public function __construct()
    {
        $this->conn = Db::getInstance()->getConn();
    }

    public function create($data)
    {
        $query = $this->conn->prepare("INSERT INTO user (name, email, password) VALUES (:name, :email, :password)");
        $query->execute($data);

        return $this->conn->lastInsertId();
    }


    public function findById($id)
    {
        $query = $this->conn->prepare("SELECT * FROM user WHERE id = :id");
        $query->execute(['id' => $id]);

        return $query->fetch();
    }

    public function findByEmail($email)
    {
        $query = $this->conn->prepare("SELECT * FROM user WHERE email = :email");
        $query->execute(['email' => $email]);

        return $query->fetch();
    }

    public function findAll($search = null, $currentPage = null, $pageLimit = 10)
    {
        $offset = 0;
        $parameters = [];

        $sql = "SELECT * FROM user";

        if ($search) {
            $columns = ['name', 'email'];
            $patterns = [];

            foreach ($columns as $column) {
                $patterns[] = "$column like ?";
                $parameters[] = "%$search%";
            }

            $sql .= " WHERE " . implode("OR ", $patterns);
        }

        if ($currentPage) {
            $offset = ($currentPage - 1) * $pageLimit;
            $sql .= " LIMIT $pageLimit OFFSET $offset";
        }

        $query = $this->conn->prepare($sql);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getTotalRowCount()
    {
        $query = $this->conn->query("SELECT COUNT(*) as TOTAL_ROWS FROM user");
        $result = $query->fetch();

        return $result['TOTAL_ROWS'] ?? null;
    }

    public function update($id, $data)
    {
        $query = $this->conn->prepare("UPDATE user SET name = :name, email = :email, password = :password WHERE id = :id");
        $query->execute([...$data, 'id' => $id]);

        return $query->rowCount();
    }

    public function delete($id)
    {
        $query = $this->conn->prepare("DELETE FROM user WHERE id = :id");
        $query->execute(['id' => $id]);

        return $query->rowCount();
    }
}
