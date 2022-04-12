<?php

namespace app;
use Aura\SqlQuery\QueryFactory;
use PDO;
use App\myclass;


class QueryBuilder 
{

    private $pdo;
    private $queryFactory;

    public function __construct() 
    {
        $this->pdo = new PDO('mysql:host=localhost;dbname=host1380688_marlindev', 'host1380688_marlindev', 'marlindev');
        $this->queryFactory = new QueryFactory('mysql');
    }

    public function getAll($table) {
        $select = $this->queryFactory->newSelect();
        $select->cols(['*'])->from($table); 
        $sth = $this->pdo->prepare($select->getStatement());
        $sth->execute($select->getBindValues());
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }


    public function getOne($table, $id)
    {
        $select = $this->queryFactory->newSelect();
        $select
            ->cols(['*'])
            ->from($table)
            ->where('id = :id')
          ->bindValue('id', $id); 
        $sth = $this->pdo->prepare($select->getStatement());
        $sth->execute($select->getBindValues());
        $result = $sth->fetch(PDO::FETCH_ASSOC);
        return $result;
    }


    public function get($table, $offset, $limit)
    {
        $select = $this->queryFactory->newSelect();
        $select
            ->cols(['*'])
            ->from($table)
            ->offset($offset)
            ->limit($limit); 
        $sth = $this->pdo->prepare($select->getStatement());
        $sth->execute($select->getBindValues());
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function insert($table, $data) 
    {
        $insert = $this->queryFactory->newInsert();

        $insert->into($table)->cols($data);

        $sth = $this->pdo->prepare($insert->getStatement());
        $sth->execute($insert->getBindValues());
    }


    public function update($table, $id, $data) 
    {
        $update = $this->queryFactory->newUpdate();

        $update
        ->table($table)                  // update this table
        ->cols($data)
        ->where('id = :id')      // bind this value to the condition
          // OR WHERE these conditions
        ->bindValue('id', $id);   // bind one value to a placeholder

        $sth = $this->pdo->prepare($update->getStatement());
        $sth->execute($update->getBindValues());
    }

    public function delete($table, $id) 
    {
        $delete = $this->queryFactory->newDelete();

        $delete
            ->from($table)                   // FROM this table
            ->where('id = :id')           // AND WHERE these conditions
            ->bindValue('id', $id);   // bind one value to a placeholder
            
            $sth = $this->pdo->prepare($delete->getStatement());
            $sth->execute($delete->getBindValues());
    }

}