<?php

// ./vendor/bin/phpunit --bootstrap vendor/autoload.php tests/EsolDbTest
declare (strict_types = 1);

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Esol\Sy\Tools\Tools;
use Esol\Db\EsolDb;

class EsolDbTest extends TestCase
{


    public function testSqlSelectWithoutParameters()
    {
        print "----------------------testSqlSelectWithoutParameters----------------------";
        $esolDb = new \Esol\Db\EsolDb("mysql_test", "./Resources/sql/testSansParametre.sql");

        $arrayData = $esolDb->getArrayData();

        $this->assertNotCount(0, $arrayData);

    }

    public function testSqlSelectWithParameters()
    {
        print "----------------------testSqlSelectWithParameters----------------------";
        $esolDb = new \Esol\Db\EsolDb("mysql_test", "./Resources/sql/testAvecParametre.sql");

        $request = new Request();
        $request->query->set("value", "UKRZO");
//        $esolDb->setASqlrVars($request->query);

        $arrayData = $esolDb->getArrayData($request);

        var_dump($arrayData);

        $this->assertNotCount(0, $arrayData);
    }


    public function testSqlExecute()
    {
        $random_string = chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90)); // random(ish) 5 character string

        $esolDb = new \Esol\Db\EsolDb("mysql_test", "./Resources/sql/insert.sql");
        $esolDb->setASqlrVars('value', $random_string);
        $result = $esolDb->execute();

        $this->assertTrue(true);
    }


    public function testGetSqlr()
    {
        $random_string = chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90)); // random(ish) 5 character string

        $esolDb = new \Esol\Db\EsolDb("mysql_test", "./Resources/sql/insert.sql");
        $esolDb->setASqlrVars('value', $random_string);

//        $sqlr = $esolDb->getSqlr();

        $this->$this->assertNotEquals('', $esolDb->getSqlr());
    }



}
