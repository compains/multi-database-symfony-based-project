<?php

namespace App\Connection;


use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver;

class DoctrineMultidatabaseConnection extends Connection {
    public function changeDatabase(string $dbName): bool {
        $params = $this->getParams();
        if ($params['dbname'] != $dbName) {
            if ($this->isConnected()) {
                $this->close();
            }
            $params['url'] = "mysql://" . $params['user'] . ":" . $params['password'] . "@" . $params['host'] . ":" . $params['port'] . "/" . $dbName;
            $params['dbname'] = $dbName;
            parent::__construct(
                $params,
                $this->_driver,
                $this->_config,
                $this->_eventManager
            );
            return true;
        }
        return false;
    }

    public function getDatabases(string $prefix = 'app_') {
        $dbs = $this->fetchAllAssociative('show databases;');
        $res = [];
        foreach ($dbs as $key => $dbName) {
            if (strpos($dbName['Database'], $prefix) === 0) {
                $res[] = $dbName['Database'];
            }
        }
        return $res;
    }
}
