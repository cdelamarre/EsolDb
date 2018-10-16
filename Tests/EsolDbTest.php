<?php

// ./vendor/bin/phpunit --bootstrap vendor/autoload.php tests/EsolDbTest
declare (strict_types = 1);

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Esol\Sy\Tools\Tools;
use Esol\Db\EsolDb;

class EsolDbTest extends TestCase
{


    /**
     * testSqlSelectWithoutParameters
     *
     * @return void
     */
    public function atestSqlSelectWithoutParameters()
    {
        $esolDb = new \Esol\Db\EsolDb("mysql_test", "./Resources/sql/testSansParametre.sql");
        $arrayData = $esolDb->getArrayData();

        var_dump($arrayData);

        $this->assertNotCount(0, $arrayData);

    }

    public function atestSqlSelectWithParameters()
    {
        $esolDb = new \Esol\Db\EsolDb("mysql_test", "./Resources/sql/testAvecParametre.sql");

        $request = new Request();
        $request->query->set("value", "UKRZO");
//        $esolDb->setASqlrVars($request->query);

        $arrayData = $esolDb->getArrayData($request);

        var_dump($arrayData);

        $this->assertNotCount(0, $arrayData);
    }

    public function testSqlSelectWithParameterArray()
    {
        $esolDb = new \Esol\Db\EsolDb("mysql_test", "./Resources/sql/testAvecParametre.sql");

        $array = array(
            "value1" => "BMOPQ", 
            "value2" => "JRYOM"

        );

        $esolDb->setASqlrVars($array);
        $arrayData = $esolDb->getArrayData();

        print $esolDb->getSqlr();

        var_dump($arrayData);

        $this->assertNotCount(0, $arrayData);
    }


    public function atestSqlExecute()
    {
        print "------------------testSqlExecute------------------";

        $random_string = chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90)); // random(ish) 5 character string

        $esolDb = new \Esol\Db\EsolDb("mysql_test", "./Resources/sql/insert.sql");
        $esolDb->setASqlrVars('value', $random_string);
        print $esolDb->getsqlr();
        $result = $esolDb->execute();
print $result;
        $this->assertTrue(true);
    }



    public function atestGetSqlr()
    {
        $random_string = chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90)); // random(ish) 5 character string

        $esolDb = new \Esol\Db\EsolDb("mysql_test", "./Resources/sql/insert.sql");
        $esolDb->setASqlrVars('value', $random_string);

//        $sqlr = $esolDb->getSqlr();

        $this->assertNotEquals('', $esolDb->getSqlr());
    }



}
