<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EsolDbConn
 *
 * @author cdelamarre
 */

namespace Esol\Db;

use Esol\Db\Params;

class Conn {

    private $esolDbParams = null;

    function __construct() {
        $numargs = func_num_args();
        $arg_list = func_get_args();
        $this->esolDbParams = new Params();
        if ($numargs == 1) {
            $dbToRequest = $arg_list[0];
            $this->setDbToRequest($dbToRequest);
        }
    }

    public function setDbToRequest($dbToRequest) {
        $this->esolDbParams->setDbToRequest($dbToRequest);
    }

    public function getDriver() {
        return $this->esolDbParams->getDriver();
    }

    public function setDbConn() {

        $dbConnection = '';
        try {
            if ($this->esolDbParams->getDriver() == 'pdo_mysql') {
                $dbConnection = $this->getDbConnMysqli();
            }
            if ($this->esolDbParams->getDriver() == 'pdo_pgsql') {
                $dbConnection = $this->getDbConnPgsql();
            }
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }

        $this->dbConn = $dbConnection;
    }

    public function getDbConnMysqli() {
        $conn = mysqli_connect($this->esolDbParams->getServerHost(), $this->esolDbParams->getUserName(), $this->esolDbParams->getPassword(), $this->esolDbParams->getDbName());
        if (mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
            exit();
        }
        return $conn;
    }

    public function getDbConnPgsql() {
        $dbConnectionString = " host=" . $this->esolDbParams->getServerHost();
        $dbConnectionString .= " port=" . $this->esolDbParams->getServerPort();
        $dbConnectionString .= " dbname=" . $this->esolDbParams->getDbName();
        $dbConnectionString .= " user=" . $this->esolDbParams->getUserName();
        $dbConnectionString .= " password=" . $this->esolDbParams->getPassword();
        $dbConnectionString .= " options='--client_encoding=UTF8'";
        return pg_connect($dbConnectionString) or die('Connexion impossible : ' . pg_last_error());
    }

    public function unsetDbConn() {
        if ($this->esolDbParams->getDriver() == 'pdo_mysql') {
            $this->getDbconn()->close();
        }
        if ($this->esolDbParams->getDriver() == 'pdo_pgsql') {
            pg_close();
        }
    }

    private $dbConn;

    public function getDbConn() {
        return $this->dbConn;
    }

}
