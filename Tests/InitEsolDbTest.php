<?php

declare (strict_types = 1);

use PHPUnit\Framework\TestCase;

class InitEsolDbTest extends TestCase
{

    public function testRootDir()
    {
        $o = new \Esol\Sy\Tools\Tools;
        $d = $o->getRootDir();
        $this->assertDirectoryExists($d);

    }
    public function testProjectDir()
    {
        $o = new \Esol\Sy\Tools\Tools;
        $d = $o->getProjectDir();
        $this->assertDirectoryExists($d);

    }
    public function testRessourceDir()
    {
        $o = new \Esol\Sy\Tools\Tools;
        $d = $o->getRootDir();
        $this->assertDirectoryExists($d);

    }

    public function testIsApp()
    {
        $o = new \Esol\Sy\Tools\Tools;
        $isAppTreeStructure = 0;
        if($o->isAppTreeStructure()){
            $isAppTreeStructure = 1;
        };
        print PHP_EOL . "AppTreeStructure : " . $isAppTreeStructure . PHP_EOL;
        $this->assertNotNull($isAppTreeStructure);
    }

    public function testBuildRelativePathDir(){
        $relativePathToBuild = 'toto/sql2';
        $o = new \Esol\Sy\Tools\Tools;

        $o->buildRelativePathDir($relativePathToBuild);
        $absolutePath = $o->getProjectDir().'/'.$relativePathToBuild;
        $this->assertDirectoryExists($absolutePath);
        $absolutePath = $o->getProjectDir().'/toto/sql2';
        rmdir($absolutePath);
        $absolutePath = $o->getProjectDir().'/toto';
        rmdir($absolutePath);
        $this->assertDirectoryNotExists($absolutePath);
    }
}