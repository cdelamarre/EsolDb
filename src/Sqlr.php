<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Esol\Db;

/**
 * Description of EsolDbSqlr
 *
 * @author cdelamarre
 */
class Sqlr {

    function __construct() {
        $numargs = func_num_args();
        $arg_list = func_get_args();
        if ($numargs == 1) {
            $sqlFilePath = $arg_list[0];
            $this->setSqlFilePath($sqlFilePath);
        }
    }

    private $sqlr;
    private $aSqlrVars = array();

    public function getASqlrVars() {
        return $this->aSqlrVars;
    }

    public function setASqlrVars() {
        $numargs = func_num_args();
        $arg_list = func_get_args();
        if ($numargs == 1) {
            $this->aSqlrVars = $arg_list[0];
        }
        if ($numargs == 2) {
            $key = $arg_list[0];
            $value = $arg_list[1];
            $this->aSqlrVars[$key] = $value;
        }
    }

    public function getSqlr() {
        return $this->sqlr;
    }

    public function setSqlr($sqlr) {
        $this->sqlr = $sqlr;
    }

    public function setSqlrFromFileName() {
        $this->setRawSqlrFromFilePath();
        $this->setSqlrWithParams($this->getRawSqlrFromFilePath());
    }

    private $rawSqlr;

    public function setRawSqlrFromFilePath() {
        $this->rawSqlr = file_get_contents($this->getSqlFilePath());
    }

    public function setSqlrWithParams($sqlr) {
        foreach ($this->getASqlrVars() as $key => $value) {
            $value = addslashes($value);
            $value = utf8_encode($value);
            $value = str_replace("\\", "", $value);  // CD20180908 on enleve les \ car si on passe des critères avec des ' comme on peut en avoir dans les critère IN ca plante
            $sqlr = str_replace('[[' . $key . ']]', $value, $sqlr);
            $sqlr = str_replace('{{' . $key . '}}', $value, $sqlr);
        }
        $sqlr = $this->removeUnknownKey($sqlr);
        $this->setSqlr($sqlr);
    }

    public function removeUnknownKey($s){
        $pattern = '/\[\[[^\[^\]]+\]\]/i';
        $replacement = '';
        $s = preg_replace($pattern, $replacement, $s);
        return $s;
    }

    public function getRawSqlrFromFilePath() {
        return file_get_contents($this->getSqlFilePath());
    }

    public function replace_in_sql($sqlr) {
        foreach ($this->getASqlrVars() as $key => $value) {
            $value = addslashes($value);
            $value = utf8_encode($value);
            $sqlr = str_replace('[[' . $key . ']]', $value, $sqlr);
        }
        $sqlr = $this->removeUnknownKey($sqlr);
        return $sqlr;
    }

    public function getSqlFilePath() {
        return $this->sqlFilePath;
    }

    public function setSqlFilePath($s) {
        $this->sqlFilePath = $s;
    }

}
