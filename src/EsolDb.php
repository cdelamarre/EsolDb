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

    /**
     * __construct
     * (@param null )
     * (@param string dbToRequest)
     * (@param string dbToRequest, @param string sqlFilePath )
     * (@param string dbToRequest, @param string sqlFileDir, @param string sqlFileName )
     * @return void
     */
    public function __construct()
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
        }
    }

    /**
     * setEnvironment
     * permet de forcer l'environnement dev, prod ou test ce qui permettra d'aller chercjher le fichier de config esolDb.yml spécifique
     * @param string 
     *
     * @return void
     */
    public function setEnvironment($s)
    {
        $this->environment = $s;
        $this->esolDbConn->setEnvironment($s);
    }

    /**
     * setDbToRequest
     * permet de spécifier la BDD configurée dans le fichier esolDb.yml que l'on souhaite interroger
     * @param  string
     *
     * @return void
     */
    public function setDbToRequest($s)
    {
        $this->esolDbConn->setDbToRequest($s);
    }

    /**
     * getDriver
     *  retourne le parametre driver de la db interrogée
     * @return string
     */
    private function getDriver()
    {
        $this->esolDbConn->getDriver();
    }

    private function getArrayDataFromFileName($sqlFileName)
    {
        $this->setSqlFileName($sqlFileName);
        return $this->getArrayDataFromResult();
    }

    /**
     * getArrayData
     * Retourne le result d'une requete sous forme d'un Array
     * (@param null)
     * (@param Symfony\Component\HttpFoundation\Request)
     * (@param array)
     * 
     * @return array
     */
    public function getArrayData()
    {

        if (func_num_args() == 1) {
            $arg1 = func_get_args()[0];
            $this->setASqlrVars($arg1);
        }
        $sqlResult = $this->getResultFromSqlr($this->getSqlr());
        return $this->getArrayDataFromSqlResult($sqlResult);
    }

    /**
     * execute
     * execute une requete sql
     * @param null execute la requete présent dans l'objet
     * @param sqlr execute la requete transmise en paramètre
     * 
     * @return void
     */
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

    /**
     * getPrintSqlr
     * retourne la requete sqlr ( avec les key éventuellement transmises ) dans une version lisible 
     *  
     * @return string
     */
    public function getPrintSqlr()
    {

        $sqlr = $this->getSqlr();

        // Génère : <body text='black'>
        $sqlr = str_replace("\n", "\\\n", $sqlr);
        $sqlr = str_replace("\r", "\\\r", $sqlr);
        $sqlr = str_replace("\t", "\\\t", $sqlr);
        return $sqlr;
    }

    /**
     * getArray
     * retourne un tableau de resultat contenant des informations complémentaire
     * sqlr : la requete interrogée
     * recordsTotal : le nombre de lignes dans le résultat
     * recorsFiltered : recordsTotal
     * data : le tableau de résultat
     * containers : data
     * @return void
     */
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

    /**
     * getSqlr
     * retourne la requete sqlr ( avec les key éventuellement transmises ) dans une version lisible 
     *
     * @return string
     */
    public function getSqlr()
    {
        $valueToReturn = $this->getSqlrWithEsolDb();

        return $valueToReturn;
    }

    private function getSqlrWithEsolDb()
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

    /**
     * setSqlr
     * permet de paramétrer une requete à interroger
     * @param  string
     *
     * @return void
     */
    public function setSqlr($s)
    {
        $this->sqlr = $s;
    }

    private $aSqlrVars = array();

    /**
     * getASqlrVars
     * Retourne l'objet des variables transmises à la requête
     * 
     * @return object
     */
    public function getASqlrVars()
    {
        return $this->aSqlrVars;
    }

    /**
     * setASqlrVars
     * permet de transmettre des variables à une requete contenant des {{key}} 
     * ( @param array )
     * ex : $esolDb->setASqlrVars($array);
     * ( @param key, @param value )
     * ex : $esolDb->setASqlrVars('key', 'value');
     * 
     * @return void
     */
    public function setASqlrVars()
    {
        $numargs = func_num_args();
        $arg_list = func_get_args();
        if ($numargs == 1) {
            $arg = $arg_list[0];
            try {
                if (gettype($arg) == 'object') {
                    if (get_class($arg) == 'Symfony\Component\HttpFoundation\Request') {
                        $this->setASqlrVarsFromRequestQuery($arg->query);
                    } else {
                        $this->setASqlrVarsFromObject($arg);
                    }
                }
            } catch (\Exception $e) {
                //                print $e . PHP_EOL;
            }

            try {
                if (gettype($arg) == 'array') {
                    $this->setASqlrVarsFromArray($arg);
                }
            } catch (\Exception $e) {
                //                print $e . PHP_EOL;

            }
        }
        if ($numargs == 2) {
            $this->setASqlrVarsKeyValue($arg_list[0], $arg_list[1]);
        }
    }

    private function setASqlrVarsKeyValue($key, $value)
    {
        $this->aSqlrVars[$key] = $value;
    }
    private function setASqlrVarsFromArray($array)
    {
        foreach ($array as $key => $value) {
            $this->setASqlrVarsKeyValue($key, $value);
        }
    }

    private function setASqlrVarsFromObject($object)
    {
        foreach ((array)$object as $key => $value) {
            $key = preg_replace('/\x00.*\x00/', '', $key);
            $this->setASqlrVarsKeyValue($key, $value);
        }
    }

    private function getASqlrVarsKeyValue($key)
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

    private function setASqlrVarsFromRequestQuery($requestQuery)
    {
        $this->aSqlrVars = $requestQuery;
    }

    private function getArrayDataFromSqlResult($sqlResult)
    {
        if ($this->getASqlrVarsKeyValue("showSqlr")) {
            $sqlr = $this->getPrintSqlr();
            $sqlr = str_replace("\\", "", $sqlr);
        }
        $resultData = array();
        if (strpos($this->esolDbConn->getDriver(), 'mysql ' )>-1) {
            if (in_array('mysqli', get_loaded_extensions())) {
                $resultData = $this->getArrayDataFromMysqliResult($sqlResult);
            }
        }
        if (strpos($this->esolDbConn->getDriver(), 'pgsql ' )>-1) {
            if (in_array('pgsql', get_loaded_extensions())) {
                if ($this->getASqlrVarsKeyValue("showResultDetail")) {
                    print pg_affected_rows($sqlResult) . "  lignes ont été affectées.\n";
                    print pg_num_rows($sqlResult) . " .\n";
                }
                $resultData = $this->getArrayDataFromPgsqlResult($sqlResult);
            }
        }
        return $resultData;
    }

    private function getArrayDataFromMysqliResult($sqlResult)
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

    private function getArrayDataFromPgsqlResult($sqlResult)
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

    private function getSqlDirPath()
    {
        return $this->sqlDirPath;
    }

    private function setSqlDirPath($sqlDirPath)
    {
        $this->sqlDirPath = $sqlDirPath;
    }

    private function getSqlFileName()
    {
        return $this->sqlFileName;
    }

    /**
     * setSqlFileName
     * permet de transmettre le nom d'un fichier sql à executer
     * 
     * @param string
     *
     * @return void
     */
    public function setSqlFileName($sqlFileName)
    {
        $this->sqlFileName = $sqlFileName;
        $this->setSqlFilePath();
    }

    private function setSqlFilePath()
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

    private function getSqlFilePath()
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

        if (strpos($this->esolDbConn->getDriver(), 'mysql ' )>-1) {
            if (in_array('mysqli', get_loaded_extensions())) {
                $result = $this->getResultFromSqlrMysqli($sqlr);
            }
        }
        if (strpos($this->esolDbConn->getDriver(), 'pgsql ' )>-1) {
            if (in_array('pgsql', get_loaded_extensions())) {
                $result = $this->getResultFromSqlrPgsql($sqlr);
            }
        }
        $this->esolDbConn->unsetDbConn();
        return $result;
    }

    private function getResultFromSqlrMysqli($sqlr)
    {
        $result = mysqli_query($this->esolDbConn->getDbConn(), $sqlr);
        if ($this->esolDbConn->getDbConn()->error) {
            print "Error: " . $sqlr . "<br>" . $this->esolDbConn->getDbConn()->error;
        }
        return $result;
    }

    private function getResultFromSqlrPgsql($sqlr)
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
