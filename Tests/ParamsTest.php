<?php

// ./vendor/bin/phpunit --bootstrap vendor/autoload.php tests/ParamsTest
declare (strict_types = 1);

use PHPUnit\Framework\TestCase;
use \Esol\Sy\Tools\Tools as SyTools;



class ParamsTest extends TestCase
{


    public function atestEnvironnement()
    {
        $kernelNameClass = $this->getKernelClass(); // Récupération du nom de la classe Kernel
        $kernel = new $kernelNameClass('test', true); // Instanciation de la classe dans un environnement de test avec débogage
        $kernel->boot(); // On boote le kernel (comme un pc)
        $this->em = $kernel->getContainer()->get('MonSvc');
//        print $this->environment.self::CONFIG_EXTS."]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]";
    }

    public function atestInitEsolDbYml()
    {

        $o = new \Esol\Db\Params("mysql_test");
        $o->initEsolDbYml();

        $configDir = __DIR__ . '/../app/config/packages/tests/';
        $fileToTest = $configDir . "esolDb.yml";

        $this->assertTrue(file_exists($fileToTest), "File " . $fileToTest . " does not exist");
    }

    public function atestIsConfigEsolDbExist()
    {
        $configDirectories = __DIR__ . '/../config/packages/prod/';
        $fileToTest = $configDirectories . "esolDb.yml";

        $this->assertTrue(file_exists($fileToTest), "File " . $fileToTest . " does not exist");
    }


    public function atestIsConfigEsolDbComplete()
    {
        $o = new \Esol\Db\Params("mysql_test");
        $this->assertNotEmpty($o->getDriver(), "Driver is Empty");
        $this->assertNotEmpty($o->getServerHost(), "Host is Empty");
        $this->assertNotEmpty($o->getServerPort(), "Port is Empty");
        $this->assertNotEmpty($o->getDbName(), "DbName is Empty");
        $this->assertNotEmpty($o->getUserName(), "UserName is Empty");
        $this->assertNotNull($o->getPassword(), "Password is Null");

    }

    public function testAutoBuildEsolDbYml()
    {

        $syTools = new SyTools();
        $projectDir = $syTools->getProjectDir();
        $configDir = $projectDir . '/config/packages/';

        try {
            unlink($configDir . "dev/" . "/esolDb.yml");
            unlink($configDir . "prod/" . "/esolDb.yml");
            unlink($configDir . "test/" . "/esolDb.yml");
        } catch (\Exception $e) {
            print $e;
        }

        \Esol\Db\EsolDbConfigFile::unlinkEsolDbConfigFile();
        \Esol\Db\EsolDbConfigFile::initEsolDbConfigFile();

        $fileToTest = $configDir . "dev" . "/esolDb.yml";

        $this->assertTrue(file_exists($fileToTest), "File " . $fileToTest . " does not exist");

    }

}
