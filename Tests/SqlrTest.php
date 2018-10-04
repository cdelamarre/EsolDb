<?php

// ./vendor/bin/phpunit --bootstrap vendor/autoload.php tests/SqlrTest
declare(strict_types=1);

use PHPUnit\Framework\TestCase;


class SqlrTest extends TestCase
{

    public function testRemoveUnknownKey()
    {
        $o = new \Esol\Db\Sqlr();
        $s = "chaine de test avec des [[crochets]] et des {{accolades}} . ";
        print PHP_EOL.$s.PHP_EOL;
        $s = $o->removeUnknownKey($s);
        print PHP_EOL.$s.PHP_EOL;
        $this->assertNotContains('[[', $s);
        $this->assertNotContains('{{', $s);
        $this->assertNotContains(']]', $s);
        $this->assertNotContains('}}', $s);
    }


}
