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
class InitEsolDbConfigFile
{
    /**
     * initEsolDbConfigFilePath
     * créé le fichier esolDb.yml dans les 3 environnements dev, prod, test s'il n'existe pas 
     * @return void
     */
    public static function initEsolDbConfigFile()
    {
        self::initAllConfDir();
        self::initAllEsolDbYml();
    }

    private static function initAllConfDir()
    {
        $syTools = new SyTools();
        $projectDir = $syTools->getProjectDir();
        $configDir = $projectDir . '/config/packages/';

        self::mkConfDir($configDir . 'dev');
        self::mkConfDir($configDir . 'prod');
        self::mkConfDir($configDir . 'test');
    }
    private static function initAllEsolDbYml()
    {
        $syTools = new SyTools();
        $projectDir = $syTools->getProjectDir();
        $configDir = $projectDir . '/config/packages/';

        self::initEsolDbYml('dev');
        self::initEsolDbYml('prod');
        self::initEsolDbYml('test');

    }


    /**
     * mkConfDir
     * fabrique le répertoire de configuration
     * @param  string $confDirPath
     *
     * @return void
     */
    private static function mkConfDir($confDirPath)
    {
        try {
            if (!file_exists($confDirPath) && !is_dir($confDirPath)) {
                print "construction du répertoire " . $confDirPath . PHP_EOL;
                mkdir($confDirPath, 0777, true);
            }
        } catch (\Exception $e) {
            print $e . PHP_EOL;
        }
    }


    private static function initEsolDbYml($whichConfDir)
    {

        $syTools = new SyTools();
        $projectDir = $syTools->getProjectDir();
        $configDir = $projectDir . '/config/packages/';

        $aDb = array(
            'pgsql_test' => self::getAPgsqlDemo(),
            'mysql_test' => self::getAMysqlDemo()
        );
        $aRoot = array(
            'parameters' => $aDb
        );
        $yaml = Yaml::dump($aRoot, 10);
        try {
            $configFilePath = $configDir . $whichConfDir . '/esolDb.yml';
            if (!file_exists($configFilePath)) {
                print "création du fichier de configuration " . $configFilePath . PHP_EOL;
                file_put_contents($configFilePath, $yaml);
            }
        } catch (\Exception $e) {
            print $e . PHP_EOL;
        }
    }
    /**
     * getAMysqlDemo
     * retourne un tableau contenant les paramètres de connexion à la base de test mysql
     *
     * @return array
     */
    public static function getAMysqlDemo()
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
    public static function getAPgsqlDemo()
    {
        $aParams = array(
            'driver' => 'pgsql',
            'host' => 'db4free.net',
            'port' => '5432',
            'name' => 'esoldb',
            'user' => 'esoldb',
            'password' => '4GwpEudP47s5qGD'
        );
        return $aParams;
    }

}