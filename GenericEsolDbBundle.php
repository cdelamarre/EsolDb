<?php

namespace Generic\EsolDbBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use M1\Vars\Vars;
use Symfony\Component\Config\ConfigCache;

class GenericEsolDbBundle extends Bundle {

    public function __construct() {
        $this->setSqlDirPath(__DIR__ . '/../Resources/sql/');
        $this->setParametersFilePath(__DIR__ . '/../../../' . 'app/config/parameters_local2.yml');
    }

    public function setSqlrFromFileName() {
        $sqlr = file_get_contents($this->getSqlDirPath() . '/' . $this->getSqlFileName());
        $this->setSqlr($this->replace_in_sql($sqlr));
    }

    public function replace_in_sql($sqlr) {
        foreach ($this->getASqlrVars() as $key => $value) {
            $value = addslashes($value);
            $value = utf8_encode($value);
            $sqlr = str_replace('[[' . $key . ']]', $value, $sqlr);
        }
        return $sqlr;
    }

    public function getResultFromSql($sqlr) {
        $this->setDbConn();
        $result = null;
        if ($this->getDriver() == 'pdo_mysql') {
            $result = $this->getResultFromSqlMysqli($sqlr);
        }
        if ($this->getDriver() == 'pdo_pgsql') {
            $result = $this->getResultFromSqlPgsql($sqlr);
        }
        $this->unsetDbConn();
        return $result;
    }

    public function getResultFromSqlMysqli($sqlr) {
        $result = $this->getDbConn()->query($sqlr);
        if ($this->getDbConn()->error) {
            print "Error: " . $sqlr . "<br>" . $dbconn->error;
        }
        return $result;
    }

    public function getResultFromSqlPgsql($sqlr) {
        $result = pg_query($sqlr) or die('Echec de la requete : ' . $sqlr . ' ' . pg_last_error());
        return $result;
    }

    public function getArrayDataFromFileName($sqlFileName) {
        $resultData = array();
        $this->setSqlFileName($sqlFileName);
        $this->setSqlrFromFileName();
        $sqlResult = $this->getResultFromSql($this->getSqlr());
        return $this->getArrayDataFromSqlResult($sqlResult);
    }

    public function getArrayDataFromSqlResult($sqlResult) {

        $resultData = array();
        if ($this->getDriver() == 'pdo_mysql') {
            $resultData = $this->getArrayDataFromMysqlResult($sqlResult);
        }
        if ($this->getDriver() == 'pdo_pgsql') {
            $resultData = $this->getArrayDataFromPgsqlResult($sqlResult);
        }
        return $resultData;
    }

    public function getArrayDataFromMysqlResult($sqlResult) {

        $arrayData = array();
        while ($resultData = $sqlResult->fetch_object()) {
            $tmpData = array();
            $vars = get_object_vars($resultData);
            foreach ($vars as $key => $var) {
                $value = $resultData->$key;
                $value = stripslashes($value);
                $value = utf8_decode($value);
                $tmpData[$key] = $value;
            }
            array_push($arrayData, $tmpData);
        }
        return $arrayData;
    }

    public function getArrayDataFromPgsqlResult($sqlResult) {

        $arrayData = array();
        while ($resultData = pg_fetch_object($sqlResult)) {
            $tmpData = array();
            $vars = get_object_vars($resultData);
            foreach ($vars as $key => $var) {
                $value = $resultData->$key;
                $value = stripslashes($value);
                $value = utf8_decode($value);
                $tmpData[$key] = $value;
            }
            array_push($arrayData, $tmpData);
        }
        return $arrayData;
    }

    public function setDbConn() {

        $vars = new Vars($this->getParametersFilePath());

        $this->setDriver($vars['parameters.' . $this->getDbToRequest() . '.driver']);
        $this->setServerHost($vars['parameters.' . $this->getDbToRequest() . '.host']);
        $this->setServerPort($vars['parameters.' . $this->getDbToRequest() . '.port']);
        $this->setDbName($vars['parameters.' . $this->getDbToRequest() . '.name']);
        $this->setUserName($vars['parameters.' . $this->getDbToRequest() . '.user']);
        $this->setPassword($vars['parameters.' . $this->getDbToRequest() . '.password']);

        $dbConnection = '';
        try {
            if ($this->getDriver() == 'pdo_mysql') {
                $dbConnection = $this->getDbConnMysqli();
            }
            if ($this->getDriver() == 'pdo_pgsql') {
                $dbConnection = $this->getDbConnPgsql();
            }
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }

        $this->dbConn = $dbConnection;
    }

    public function getDbConnMysqli() {
        return mysqli_connect($this->getServerHost(), $this->getUserName(), $this->getPassword(), $this->getDbName());
    }

    public function getDbConnPgsql() {
        $dbConnectionString = " host=" . $this->getServerHost();
        $dbConnectionString .= " port=" . $this->getServerPort();
        $dbConnectionString .= " dbname=" . $this->getDbName();
        $dbConnectionString .= " user=" . $this->getUserName();
        $dbConnectionString .= " password=" . $this->getPassword();
        $dbConnectionString .= " options='--client_encoding=UTF8'";
        return pg_connect($dbConnectionString) or die('Connexion impossible : ' . pg_last_error());
    }

    public function unsetDbConn() {
        if ($this->getDriver() == 'pdo_mysql') {
            $this->getDbconn()->close();
        }
        if ($this->getDriver() == 'pdo_pgsql') {
            pg_close();
        }
    }

    private $dbConn;

    public function getDbConn() {
        return $this->dbConn;
    }

    private $aSqlrVars = array();
    private $dbName;
    private $dbToRequest;
    private $driver;
    private $parametersFilePath;
    private $password;
    private $serverHost;
    private $serverPort;
    private $sqlDirPath;
    private $sqlFileName;
    private $sqlr;
    private $userName;

    public function getASqlrVars() {
        return $this->aSqlrVars;
    }

    public function setASqlrVars($key, $value) {
        $this->aSqlrVars[$key] = $value;
    }

    public function getDbName() {
        return $this->dbName;
    }

    public function setDbName($dbName) {
        $this->dbName = $dbName;
    }

    public function getDbToRequest() {
        return $this->dbToRequest;
    }

    public function setDbToRequest($dbToRequest) {
        $this->dbToRequest = $dbToRequest;
    }

    public function getDriver() {
        return $this->driver;
    }

    public function setDriver($driver) {
        $this->driver = $driver;
    }

    public function getParametersFilePath() {
        return $this->parametersFilePath;
    }

    public function setParametersFilePath($parametersFilePath) {
        $this->parametersFilePath = $parametersFilePath;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function getServerHost() {
        return $this->serverHost;
    }

    public function setServerHost($serverHost) {
        $this->serverHost = $serverHost;
    }

    public function getServerPort() {
        return $this->serverPort;
    }

    public function setServerPort($serverPort) {
        $this->serverPort = $serverPort;
    }

    public function getSqlDirPath() {
        return $this->sqlDirPath;
    }

    public function setSqlDirPath($sqlDirPath) {
        $this->sqlDirPath = $sqlDirPath;
    }

    public function getSqlFileName() {
        return $this->sqlFileName;
    }

    public function setSqlFileName($sqlFileName) {
        $this->sqlFileName = $sqlFileName;
    }

    public function getSqlr() {
        return $this->sqlr;
    }

    public function setSqlr($sqlr) {
        $this->sqlr = $sqlr;
    }

    public function getUserName() {
        return $this->userName;
    }

    public function setUserName($userName) {
        $this->userName = $userName;
    }

}
