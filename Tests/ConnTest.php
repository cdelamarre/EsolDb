<?php

// ./vendor/bin/phpunit --bootstrap vendor/autoload.php tests/ParamsTest
declare(strict_types=1);

use PHPUnit\Framework\TestCase;


class ConnTest extends TestCase
{

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
