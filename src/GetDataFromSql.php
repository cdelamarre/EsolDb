<?php

namespace Esol\Db;

use Esol\Db\Main as EsolDb;

class GetDataFromSql
{
    public function __construct()
    {
        $numargs = func_num_args();
        $arg_list = func_get_args();
        if ($numargs == 0) {
            print("Veuillez renseignez un paramètres !");
        }
        if ($numargs == 1) {
            $sqlFilePath = $arg_list[0];
            $this->setSqlFilePath($sqlFilePath);
        }
    }

    public function getArrayData($request)
    {
        $db = new EsolDb('pgsql_yr_db', $this->getSqlFilePath());
        $db->setASqlrVars($request->query);
        $arrayData = $db->getArray();
        return $arrayData;
    }
    
    private $sqlFilePath = "";

    public function setSqlFilePath($s)
    {
        $this->sqlFilePath = $s;
    }

    public function getSqlFilePath()
    {
        return $this->sqlFilePath;
    }
}
