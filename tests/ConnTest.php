<?php

// ./vendor/bin/phpunit --bootstrap vendor/autoload.php tests/ConnTest
declare(strict_types=1);

use PHPUnit\Framework\TestCase;


class ConnTest extends TestCase
{

    public function testIsConfigEsolDbComplete()
    {
        $o = new \Esol\Db\Conn("mysqlTest");
        var_dump($o->getDbConn());
        $this->assertTrue(true);
    }

    public function testIsPgsqExtension()
    {
        $aPhpExtensions = get_loaded_extensions();
        var_dump($aPhpExtensions);
        $isExtensionEnabled = in_array('pdo_pgsql', $aPhpExtensions);
        $o = new \Esol\Db\Conn("mysqlTest");
        var_dump($o->getDbConn());
        $this->assertTrue($isExtensionEnabled);
    }




}
