<?php

// ./vendor/bin/phpunit --bootstrap vendor/autoload.php tests/ParamsTest
declare(strict_types=1);

use PHPUnit\Framework\TestCase;


class ParamsTest extends TestCase
{


    public function testInitEsolDbYml()
    {

        $kernelNameClass = $this->getKernelClass(); // Récupération du nom de la classe Kernel
$kernel = new $kernelNameClass('test', true); // Instanciation de la classe dans un environnement de test avec débogage
$kernel->boot(); // On boote le kernel (comme un pc)
$this->em = $kernel->getContainer()->get('MonSvc');

//        print $this->environment.self::CONFIG_EXTS."]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]";

        $o = new \Esol\Db\Params("mysql_test");
    
        $configDir = __DIR__.'/../app/config/packages/tests/';
        $fileToTest = $configDir."esolDb.yml";
        $o->initEsolDbYml();

        $this->assertTrue(file_exists($fileToTest), "File ".$fileToTest." does not exist");
    }

    public function testIsConfigEsolDbExist()
    {
        $configDirectories = __DIR__.'/../config/packages/prod/';
        $fileToTest = $configDirectories."esolDb.yml";

        $this->assertTrue(file_exists($fileToTest), "File ".$fileToTest." does not exist");
    }

    public function testIsConfigEsolDbComplete()
    {
        $o = new \Esol\Db\Params("mysql_test");
        $o->initParams();
        $this->assertNotEmpty($o->getDriver(), "Driver is Empty");
        $this->assertNotEmpty($o->getServerHost(), "Host is Empty");
        $this->assertNotEmpty($o->getServerPort(), "Port is Empty");
        $this->assertNotEmpty($o->getDbName(), "DbName is Empty");
        $this->assertNotEmpty($o->getUserName(), "UserName is Empty");
        $this->assertNotNull($o->getPassword(), "Password is Null");

    }


}
