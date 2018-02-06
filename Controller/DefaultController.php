<?php

namespace Generic\EsolDbBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
		
        return $this->render('GenericEsolDbBundle:Default:index.html.twig');
    }
	
	public function test(){
$this->setDbConn('mysql_d2d_db');

	

	}
	
	    public function getSqlStrFromFilePath($sqlFilePath) {
        $sqlr = file_get_contents($this->getSqlDirPath() . $sqlFilePath);
        $aVars = $this->getSqlrVars();
        $sqlStr = $this->replace_in_sql($aVars, $sqlr);
        return $sqlStr;
    }

	    public function replace_in_sql($aVars, $sqlr) {
        foreach ($aVars as $key => $value) {
				$value = addslashes( $value );
				$value = utf8_encode( $value );			
            $sqlr = str_replace('[[' . $key . ']]', $value, $sqlr);
        }
        return $sqlr;
    }

	
	    public function getResultFromSql($sqlr) {
			$result = null;
		if($this->getDriver() == 'pdo_mysql'){
				$result = getResultFromSqlMysqli($sqlr);
			}
			if($this->getDriver() == 'pdo_pgsql'){
				$result = getResultFromSqlPgsql($sqlr);
			}
			return $result;
		}

	    public function getResultFromSqlMysqli($sqlr) {

        $dbconn = $this->getDbConn();
		$result = $dbconn->query($sqlr);
        if ($dbconn->error) {
            print "Error: " . $sqlr . "<br>" . $dbconn->error;
        }
        $this->closeDbConn();
        return $result;
    }

	    public function getResultFromSqlPgsql($sqlr) {
        $dbconn = $this->getDbConn();

		$result = pg_query($sqlr) or die('Echec de la requete : ' . pg_last_error());
		
        if ($dbconn->error) {
            print "Error: " . $sqlr . "<br>" . $dbconn->error;
        }
		
        $this->closeDbConn();
        return $result;
    }

    public function getArrayDataFromSqlResult($sqlResult) {

        $arrayData = array();
        while ($data = $sqlResult->fetch_object()) {
            $tmpData = array();
            $vars = get_object_vars($data);
            foreach ($vars as $key => $var) {
				$value = $data->$key;
				$value = stripslashes( $value );
				$value = utf8_decode ( $value );
                $tmpData[$key] = $value ;
//                $tmpData[$key] = $this->convert_from_latin1_to_utf8_recursively($data->$key);
            }
            array_push($arrayData, $tmpData);
        }
        return $arrayData;
    }

	public function getDbConnMysqli($whichDb) {
		return mysqli_connect($this->getServerHost(), $this->getUserName(), $this->getPassword(), $this->getDbName());
	}

	public function getDbConnPgsql($whichDb) {
		$dbConnectionString = " host=" . $this->getServerHost();
		$dbConnectionString .= " port=" . $this->getServerPort();
		$dbConnectionString .= " dbname=" . $this->getDbName();
		$dbConnectionString .= " user=" . $this->getUserName();
		$dbConnectionString .= " password=" . $this->getPassword();
		$dbConnectionString .= " options='--client_encoding=UTF8'";
		return pg_connect($dbConnectionString) or die('Connexion impossible : ' . pg_last_error());
	}

		
	
	public function setDbConn($whichDb) {

        $vars = new Vars($this->getParametersFilePath());

        $this->setDriver($vars['parameters.'.$whichDb.'.driver']);
        $this->setServerHost($vars['parameters.'.$whichDb.'.host']);
        $this->setServerPort($vars['parameters.'.$whichDb.'.port']);
        $this->setDbName($vars['parameters.'.$whichDb.'.name']);
        $this->setUserName($vars['parameters.'.$whichDb.'.user']);
        $this->setPassword($vars['parameters.'.$whichDb.'.password']);

        $dbConnection = '';
        try {
			if($this->getDriver() == 'pdo_mysql'){				
				$dbConnection = getDbConnMysqli();
			}
			if($this->getDriver() == 'pdo_pgsql'){
				$dbConnection = getDbConnPgsql();
			}
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }

        $this->dbConn = $dbConnection ;
    }

	    public function closeDbConn() {
			if($this->getDriver() == 'pdo_mysql'){
				$this->getDbconn()->close();
			}
			if($this->getDriver() == 'pdo_pgsql'){
				pg_close($this->getDbconn());
			}
		}

	
	private $driver;
	
	private $sqlDirPath = __DIR__ . '/../Resources/sql/';
	
//	private $parametersFilePath = get('kernel')->getRootDir() . '/config/parameters_local2.yml';
	private $parametersFilePath = '../../../'. '/config/parameters_local2.yml';
	
	private $dbconn;

	private $serverHost;
	private $serverPort;
	private $dbName;
	private $userName;
	private $password;
	private function setServerHost($str){
		$this->serverHost = $str;
	}
	private function setServerPort($str){
		$this->serverPort = $str;
	}
	private function setDbName($str){
		$this->dbName = $str;
	}
	private function setUserName($str){
		$this->userName = $str;
	}
	private function setPassword($str){
		$this->password = $str;
	}
	
	private function getServerHost(){
		return $this->serverHost;
	}
	private function getServerPort(){
		return $this->serverPort;
	}
	private function getDbName(){
		return $this->dbName;
	}
	private function getUserName(){
		return $this->userName;
	}
	private function getPassword(){
		return $this->password;
	}


	public function getDbconn(){
		return $this->dbconn;
	}
	
	public function setParametersFilePath($str){
		$this->parametersFilePath = $str;
	}
	
	public function getParametersFilePath(){
		return $this->parametersFilePath;
	}

	public function setSqlDirPath($str){
		$this->sqlDirPath = $str;
	}
	
	public function getSqlDirPath(){
		return $this->sqlDirPath;
	}
	
	private function setDriver($str){
		$this->driver = $str;
	}
	public function getDriver(){
		return $this->driver;
	}
	
}
