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

class Conn
{
    private $esolDbParams = null;
    private $environment = 'dev';
    public function __construct()
    {
        $numargs = func_num_args();
        $arg_list = func_get_args();
        $this->esolDbParams = new Params();
        if ($numargs == 1) {
            $dbToRequest = $arg_list[0];
            $this->setDbToRequest($dbToRequest);
        }
    }

    public function setEnvironment($s)
    {
        $this->environment = $s;
        $this->esolDbParams->setEnvironment($s);
        $this->esolDbParams->initParams();
    }


    public function setDbToRequest($dbToRequest)
    {
        $this->esolDbParams->setDbToRequest($dbToRequest);
    }

    public function getDriver()
    {
        return $this->esolDbParams->getDriver();
    }

    public function setDbConn()
    {
        $dbConnection = '';
        try {

            if (strpos($this->esolDbParams->getDriver(), 'mysql')>-1) {
                if (in_array('mysqli', get_loaded_extensions())) {
                    $dbConnection = $this->getDbConnMysqli();
                } else if (in_array('pdo_mysql', get_loaded_extensions())) {
                    print PHP_EOL . "you need to activate php extension mysqli" . PHP_EOL;
                } else {
                    print PHP_EOL . "you need to activate php extension mysqli" . PHP_EOL;
                }
            }
            if (strpos($this->esolDbParams->getDriver(), 'pgsql')>-1) {
                if (in_array('pgsql', get_loaded_extensions())) {
                    $dbConnection = $this->getDbConnPgsql();
                } else if (in_array('pdo_pgsql', get_loaded_extensions())) {
                    print PHP_EOL . "you need to activate php extension pgsql" . PHP_EOL;
                } else {
                    print PHP_EOL . "you need to activate php extension pgsql" . PHP_EOL;
                }
            }
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }

        $this->dbConn = $dbConnection;
    }

    public function getDbConnMysqli()
    {
        $conn = mysqli_connect($this->esolDbParams->getServerHost(), $this->esolDbParams->getUserName(), $this->esolDbParams->getPassword(), $this->esolDbParams->getDbName());
        if (mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
            exit();
        }
        return $conn;
    }

    public function getDbConnPgsql()
    {
        $dbConnectionString = " host=" . $this->esolDbParams->getServerHost();
        $dbConnectionString .= " port=" . $this->esolDbParams->getServerPort();
        $dbConnectionString .= " dbname=" . $this->esolDbParams->getDbName();
        $dbConnectionString .= " user=" . $this->esolDbParams->getUserName();
        $dbConnectionString .= " password=" . $this->esolDbParams->getPassword();
        $dbConnectionString .= " options='--client_encoding=UTF8'";
        return pg_connect($dbConnectionString) or die('Connexion impossible : ' . pg_last_error());
    }

    public function unsetDbConn()
    {
        if (strpos($this->esolDbParams->getDriver(), 'mysql')>-1) {
            if (in_array('mysqli', get_loaded_extensions())) {
                $this->getDbconn()->close();
            }
        }
        if (strpos($this->esolDbParams->getDriver(), 'pgsql')>-1) {
            if (in_array('pgsql', get_loaded_extensions())) {
                pg_close();
            }
        }
    }

    private $dbConn;

    public function getDbConn()
    {
        return $this->dbConn;
    }

}
