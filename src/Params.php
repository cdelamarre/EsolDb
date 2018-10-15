<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Esol\Db;

use Symfony\Component\Yaml\Yaml;
use \Esol\Sy\Tools\Tools as SyTools;
/**
 * Description of EsolObject
 *
 * @author cdelamarre
 */
class Params
{

    private $environment = 'prod';

    function __construct()
    {
        $numargs = func_num_args();
        $arg_list = func_get_args();
        if ($numargs > 1) {
            $dbToRequest = $arg_list[0];
            $this->setDbToRequest($dbToRequest);
        }
        if ($numargs == 2) {
            $this->$environment = $arg_list[1];
        }

    }

    public function setEnvironment($s)
    {
        $this->environment = $s;
    }

    public function IsConfigEsolDbExist()
    {
        return true;
    }

    /**
     * mkConfDir
     * fabrique le répertoire de configuration
     * @param  string $confDirPath
     *
     * @return void
     */
    public function mkConfDir($confDirPath)
    {
        mkdir($confDirPath, 0777, true);
    }


    /**
     * getAMysqlDemo
     * retourne un tableau contenant les paramètres de connexion à la base de test mysql
     *
     * @return array
     */
    public function getAMysqlDemo()
    {
        $aParams = array(
            'driver' => 'mysql',
            'host' => 'db4free.net',
            'port' => '3306',
            'name' => 'esoldb',
            'user' => 'esoldb',
            'password' => '4GwpEudP47s5qGD'
        );
        return $aParams;
    }

    /**
     * getAPgsqlDemo
     * retourne un tableau contenant les paramètres de connexion à la base de test postgresql
     * @return array
     */
    public function getAPgsqlDemo()
    {
        $aParams = array(
            'driver' => 'pgsql',
            'host' => 'db4free.net',
            'port' => '3306',
            'name' => 'esoldb',
            'user' => 'esoldb',
            'password' => '4GwpEudP47s5qGD'
        );
        return $aParams;
    }

    public function initEsolDbYml($whichConfDir)
    {

        $syTools = new SyTools();
        $projectDir = $syTools->getProjectDir();
        $configDir = $projectDir . '/config/packages/';

        $aDb = array(
            'pgsql_test' => $this->getAPgsqlDemo(),
            'mysql_test' => $this->getAMysqlDemo()
        );
        $aRoot = array(
            'parameters' => $aDb
        );
        $yaml = Yaml::dump($aRoot, 10);
        file_put_contents($configDir . $whichConfDir . '/esolDb.yml', $yaml);
    }


    /**
     * renameConfDirTestsToTestIfNecessary
     * Dans une précédente version le répertoire config/packages/test a été nommé avec erreur config/packages/tests
     * Cette fonction vient corriger cette erreur.
     * 
     * @return void
     */
    public function renameConfDirTestsToTestIfNecessary()
    {
        $syTools = new SyTools();
        $projectDir = $syTools->getProjectDir();
        $configDir = $projectDir . '/config/packages/';
        if (file_exists($configDir . 'tests/') && is_dir($configDir . 'tests/')) {
            rename($configDir . 'tests/', $configDir . 'test/');
        }
    }

    /**
     * getConfDir
     * Cette fonction récupère le chemin du répertoire de configuration
     * @return string
     */
    public function getConfDir()
    {
        $syTools = new SyTools();
        $projectDir = $syTools->getProjectDir();

        $configDir = $projectDir . '/config/packages/';

        $this->renameConfDirTestsToTestIfNecessary();


        $configDir .= $this->environment . '/';

        if (!file_exists($configDir) && !is_dir($configDir)) {
            $this->mkConfDir($configDir);
        }
        return $configDir;
    }

    /**
     * getEsolDbConfigFilePath
     *  retourne le chemin du fichier de config
     * @return string
     */
    public function getEsolDbConfigFilePath()
    {
        $syTools = new SyTools();
        $projectDir = $syTools->getProjectDir();
        $configDir = $projectDir . '/config/packages/';
        
        if (!file_exists($configDir . 'prod' . '/esolDb.yml')) {
            $this->initEsolDbYml('prod');
        }
        if (!file_exists($configDir . 'dev' . '/esolDb.yml')) {
            $this->initEsolDbYml('dev');
        }
        if (!file_exists($configDir . 'test' . '/esolDb.yml')) {
            $this->initEsolDbYml('test');
        }
        return $this->getConfDir() . 'esolDb.yml';
    }

    public function initParams()
    {

        $esolDbConfigFilePath = $this->getEsolDbConfigFilePath();

        $a = Yaml::parseFile($this->getEsolDbConfigFilePath());
        $o = (object)$a['parameters'][$this->getDbToRequest()];

        $this->setDriver($o->driver);
        $this->setServerHost($o->host);
        $this->setServerPort($o->port);
        $this->setDbName($o->name);
        $this->setUserName($o->user);
        $this->setPassword($o->password);
    }


    private function initParamsOld()
    {
        $syTools = new SyTools();
        $projectDir = $syTools->getProjectDir();
        $this->setParametersFilePath($projectDir . '/config/packages/dev/esolDb.yml');
        $vars = new Vars($this->getParametersFilePath());

        $this->setDriver($vars['parameters.' . $this->getDbToRequest() . '.driver']);
        $this->setServerHost($vars['parameters.' . $this->getDbToRequest() . '.host']);
        $this->setServerPort($vars['parameters.' . $this->getDbToRequest() . '.port']);
        $this->setDbName($vars['parameters.' . $this->getDbToRequest() . '.name']);
        $this->setUserName($vars['parameters.' . $this->getDbToRequest() . '.user']);
        $this->setPassword($vars['parameters.' . $this->getDbToRequest() . '.password']);

    }

    private $dbToRequest;

    private $parametersFilePath;

    private $driver;
    private $serverHost;
    private $serverPort;
    private $dbName;
    private $userName;
    private $password;


    public function getDbName()
    {
        return $this->dbName;
    }

    public function setDbName($dbName)
    {
        $this->dbName = $dbName;
    }

    public function getDbToRequest()
    {
        return $this->dbToRequest;
    }

    public function setDbToRequest($dbToRequest)
    {
        $this->dbToRequest = $dbToRequest;
        $this->initParams();
    }

    public function getDriver()
    {
        return $this->driver;
    }

    public function setDriver($driver)
    {
        $this->driver = $driver;
    }

    public function getParametersFilePath()
    {
        return $this->parametersFilePath;
    }

    public function setParametersFilePath($parametersFilePath)
    {
        $this->parametersFilePath = $parametersFilePath;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getServerHost()
    {
        return $this->serverHost;
    }

    public function setServerHost($serverHost)
    {
        $this->serverHost = $serverHost;
    }

    public function getServerPort()
    {
        return $this->serverPort;
    }

    public function setServerPort($serverPort)
    {
        $this->serverPort = $serverPort;
    }

    public function getUserName()
    {
        return $this->userName;
    }

    public function setUserName($userName)
    {
        $this->userName = $userName;
    }



}
