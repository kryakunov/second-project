<?php

namespace app;
use Aura\SqlQuery\QueryFactory;
use PDO;

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

    public function getUserInfo($id)
    {
        $select = $this->queryFactory->newSelect();
        $select
            ->cols(['*'])
            ->from('users_info')
            ->where('user_id = :id')
            ->bindValue('id', $id); 

        $sth = $this->pdo->prepare($select->getStatement());
        $sth->execute($select->getBindValues());
        $result = $sth->fetch(PDO::FETCH_ASSOC);
        
        return $result;
    }

    public function getUsersInfo()
    {
        $select = $this->queryFactory->newSelect();
        $select
            ->cols(['*'])
            ->from('users')
            ->join(
                'LEFT',             // the join-type
                'users_info',        // join to this table ...
                'users.id = users_info.user_id' // ... ON these conditions
            );
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
            ->table($table)
            ->cols($data)
            ->where('id = :id')
            ->bindValue('id', $id);
        
        $sth = $this->pdo->prepare($update->getStatement());
        $sth->execute($update->getBindValues());
    }

    public function updateUserInfo($id, $data) 
    {
        $update = $this->queryFactory->newUpdate();

        $update
            ->table('users_info')
            ->cols($data)
            ->where('user_id = :id')
            ->bindValue('id', $id);
        
        $sth = $this->pdo->prepare($update->getStatement());
        $sth->execute($update->getBindValues());
    }

    public function delete($table, $id) 
    {
        $delete = $this->queryFactory->newDelete();

        $delete
            ->from($table)
            ->where('id = :id')
            ->bindValue('id', $id);
            
        $sth = $this->pdo->prepare($delete->getStatement());
        $sth->execute($delete->getBindValues());
    }

}