<?php

// ./vendor/bin/phpunit --bootstrap vendor/autoload.php tests/ParamsTest
declare(strict_types=1);

use PHPUnit\Framework\TestCase;


class ConnTest extends TestCase
{

    public function testIsConfigEsolDbComplete()
    {
        $o = new \Esol\Db\Conn("mysql_test");
        var_dump($o->getDbConn());
        $this->assertTrue(true);
    }


}
