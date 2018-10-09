<?php

namespace Esol\Db;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Esol\Db\Conn;
use \Esol\Sy\Tools\Tools as SyTools;

class EsolDb
{

    private $esolDbParams;
    private $esolDbConn;
    private $esolDbResult;
    private $sqlr;
    private $environment = 'dev';

    function __construct()
    {
        $this->esolDbConn = new Conn();

        $numargs = func_num_args();
        $arg_list = func_get_args();
        if ($numargs == 0) {
            $syTools = new SyTools();
            $sqlDir = $syTools->getProjectDir() . "/Resources/sql/";
            $this->setSqlDirPath($sqlDir);
        }
        if ($numargs == 1) {
            $dbToRequest = $arg_list[0];
            $this->setDbToRequest($dbToRequest);
        }
        if ($numargs == 2) {
            $dbToRequest = $arg_list[0];
            $sqlFilePath = $arg_list[1];
            $this->setDbToRequest($dbToRequest);
            $this->setSqlFilePath($sqlFilePath);
        }
        
        if ($numargs == 3) {
            $dbToRequest = $arg_list[0];
            $sqlFileDir = $arg_list[1];
            $sqlFileName = $arg_list[2];
            $this->setDbToRequest($dbToRequest);
            $this->setSqlDirPath($sqlFilePath);
            $this->setSqlFileName($sqlFileName);
            $this->setSqlFilePath();
        }
    }

    public function setEnvironment($s)
    {
        $this->environment = $s;
        $this->esolDbConn->setEnvironment($s);

    }

    public function setDbToRequest($s)
    {
        $this->esolDbConn->setDbToRequest($s);
    }

    public function getDriver()
    {
        $this->esolDbConn->getDriver();
    }

    public function getArrayDataFromFileName($sqlFileName)
    {
        $this->setSqlFileName($sqlFileName);
        return $this->getArrayDataFromResult();
    }

    public function getArrayData()
    {
        $sqlResult = $this->getResultFromSqlr($this->getSqlr());
        return $this->getArrayDataFromSqlResult($sqlResult);
    }

    public function execute()
    {
        $numargs = func_num_args();
        $arg_list = func_get_args();
        $msg = null;
        if ($numargs == 0) {
            $msg = $this->getResultFromSqlr($this->getSqlr());
        }
        if ($numargs == 1) {
            $msg = $this->getResultFromSqlr($arg_list[0]);
        }

        return $msg;
    }

    public function getPrintSqlr()
    {

        $sqlr = $this->getSqlr();

        // Génère : <body text='black'>
        $sqlr = str_replace("\n", "\\\n", $sqlr);
        $sqlr = str_replace("\r", "\\\r", $sqlr);
        $sqlr = str_replace("\t", "\\\t", $sqlr);
        return $sqlr;
    }

    public function getArray()
    {
        $arrayData = $this->getArrayData();
        $arrayRoot['draw'] = 1;
        $arrayRoot['sqlr'] = $this->getPrintSqlr();
        $arrayRoot['recordsTotal'] = count($arrayData);
        $arrayRoot['recordsFiltered'] = $arrayRoot['recordsTotal'];
        $arrayRoot['data'] = $arrayData;
        $arrayRoot['containers'] = $arrayData;
        return $arrayRoot;
    }

    public function getSqlr()
    {
        $valueToReturn = $this->getSqlrWithEsolDb();

        return $valueToReturn;
    }

    public function getSqlrWithEsolDb()
    {
        $esolDbSqlr = new Sqlr();
        $esolDbSqlr->setASqlrVars($this->getASqlrVars());
        if (strlen($this->getSqlFilePath()) > 0) {
            $esolDbSqlr->setSqlFilePath($this->getSqlFilePath());
            $this->setSqlr($esolDbSqlr->getRawSqlrFromFilePath());
        }
        $esolDbSqlr->setSqlrWithParams($this->sqlr);
        return $esolDbSqlr->getSqlr();
    }

    public function setSqlr($s)
    {
        $this->sqlr = $s;
    }

    private $aSqlrVars = array();

    public function getASqlrVars()
    {
        return $this->aSqlrVars;
    }

    public function setASqlrVars()
    {
        $numargs = func_num_args();
        $arg_list = func_get_args();
        if ($numargs == 1) {
            $this->setASqlrVarsFromRequestQuery($arg_list[0]);
        }
        if ($numargs == 2) {
            $this->setASqlrVarsKeyValue($arg_list[0], $arg_list[1]);
        }
    }

    public function setASqlrVarsKeyValue($key, $value)
    {
        $this->aSqlrVars[$key] = $value;
    }

    public function getASqlrVarsKeyValue($key)
    {
        $v = null;
        if (is_object($this->aSqlrVars)) {
            $v = $this->aSqlrVars->get($key);
        }
        if (is_array($this->aSqlrVars) && in_array($key, $this->aSqlrVars)) {
            $v = $this->aSqlrVars->get($key);
        }
        return $v;
    }

    public function setASqlrVarsFromRequestQuery($requestQuery)
    {
        $this->aSqlrVars = $requestQuery;
    }

    public function getArrayDataFromSqlResult($sqlResult)
    {
        if ($this->getASqlrVarsKeyValue("showSqlr")) {
            $sqlr = $this->getPrintSqlr();
            $sqlr = str_replace("\\", "", $sqlr);
        }
        $resultData = array();
        if ($this->esolDbConn->getDriver() == 'pdo_mysql') {

            $resultData = $this->getArrayDataFromMysqlResult($sqlResult);
        }
        if ($this->esolDbConn->getDriver() == 'pdo_pgsql') {
            if ($this->getASqlrVarsKeyValue("showResultDetail")) {
                print pg_affected_rows($sqlResult) . " lignes ont été affectées.\n";
                print pg_num_rows($sqlResult) . " .\n";
            }
            $resultData = $this->getArrayDataFromPgsqlResult($sqlResult);
        }
        return $resultData;
    }

    public function getArrayDataFromMysqlResult($sqlResult)
    {

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

    public function getArrayDataFromPgsqlResult($sqlResult)
    {



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
        if (!pg_num_fields($sqlResult)) {
            $tmpData = array();
            $tmpData["affected_rows"] = pg_affected_rows($sqlResult);
            array_push($arrayData, $tmpData);
        }

        return $arrayData;
    }

    private $sqlDirPath;
    private $sqlFileName;
    private $sqlFilePath;

    public function getSqlDirPath()
    {
        return $this->sqlDirPath;
    }

    public function setSqlDirPath($sqlDirPath)
    {
        $this->sqlDirPath = $sqlDirPath;
    }

    public function getSqlFileName()
    {
        return $this->sqlFileName;
    }

    public function setSqlFileName($sqlFileName)
    {
        $this->sqlFileName = $sqlFileName;
        $this->setSqlFilePath();
    }

    public function setSqlFilePath()
    {
        $numargs = func_num_args();
        $arg_list = func_get_args();
        if ($numargs == 0) {
            if ($this->getSqlDirPath() != null && $this->getSqlFileName() != null) {
                $this->sqlFilePath = $this->getSqlDirPath() . '/' . $this->getSqlFileName();
            }
        }
        if ($numargs == 1) {
            $sqlFilePath = $arg_list[0];
            if (strpos($sqlFilePath, './') !== false) {
                $syTools = new SyTools();
                $sqlFilePath = $syTools->getProjectDir() . '/' . $sqlFilePath;
            }
            $this->sqlFilePath = $sqlFilePath;
        }
    }

    public function getSqlFilePath()
    {
        $sqlFilePath = $this->sqlFilePath;
        if ($sqlFilePath == null) {
            if ($this->getSqlDirPath() != null && $this->getSqlFileName() != null) {
                $sqlFilePath = $this->getSqlDirPath() . '/' . $this->getSqlFileName();
            }
        }
        return $sqlFilePath;
    }

    public function getResultFromSqlr($sqlr)
    {
        $this->esolDbConn->setDbConn();
        $result = null;
        if ($this->esolDbConn->getDriver() == 'pdo_mysql') {
            $result = $this->getResultFromSqlrMysqli($sqlr);
        }
        if ($this->esolDbConn->getDriver() == 'pdo_pgsql') {
            $result = $this->getResultFromSqlrPgsql($sqlr);
        }
        $this->esolDbConn->unsetDbConn();
        return $result;
    }

    public function getResultFromSqlrMysqli($sqlr)
    {
        $result = mysqli_query($this->esolDbConn->getDbConn(), $sqlr);
        if ($this->esolDbConn->getDbConn()->error) {
            print "Error: " . $sqlr . "<br>" . $this->esolDbConn->getDbConn()->error;
        }
        return $result;
    }

    public function getResultFromSqlrPgsql($sqlr)
    {
        $result = null;
        try {
            $result = pg_query($sqlr) or die('Echec de la requete : ' . $sqlr . ' ' . pg_last_error());
        } catch (\Exception $e) {
            print "<pre>" . pg_last_error() . "</pre>";
            print "<pre>" . $sqlr . "</pre>";
        }
        return $result;
    }

}
