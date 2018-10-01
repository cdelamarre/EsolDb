<?php

// ./vendor/bin/phpunit --bootstrap vendor/autoload.php tests/ParamsTest
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;


class EsolDbTest extends TestCase
{

    public function test1()
    {
        $o = new \Esol\Db\EsolDb("mysql_test", "./Ressources/sql/test.sql");
         
        $request = new Request();
        $request->query->set("ORDER_BY", "name");
        $o->setASqlrVars($request->query);

        $arrayData = $o->getArrayData($request);
        var_dump($arrayData);



        $this->assertTrue(true);
    }


}
