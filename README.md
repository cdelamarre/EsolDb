Cette librairie permet d'interroger des bases de données POSTGRESQL ou MYSQL: 
- de recuperer un array à partir d'une requete SELECT
- d'executer un INSERT, UPDATE, DELETE ou toute autre instruction SQL ne nécessitant pas de retour.

# Installation

## insallation via Composer
```
composer require esol/db
```

In Esol/Db composer.json, add script "Esol\\Db\\EsolDbConfigFile::initEsolDbConfigFile" in post-package-install & post-package-update event.

```
"scripts": {
    "post-package-install": [
         "Esol\\Db\\EsolDbConfigFile::initEsolDbConfigFile"
    ],
    "post-package-update": [
         "Esol\\Db\\EsolDbConfigFile::initEsolDbConfigFile"
    ]
}
```

## Extensions php à activer dans php.ini

Esol/Db utilise les extensions mysqli et/ou pgsql

### Pour Mysql
#### Linux
extension=mysqli
#### Windows
extension=php_mysqli.dll
### Pour Pgsql
#### Linux
extension=pgsql
#### Windows
extension=php_pgsql.dll

## Configuration

Dans le fichier {project_dir}/config/packages/prod/esolDb.yml on trouve les paramètres vers la base de donnée dans laquelle on veut agir

La configuration s'inscrit dans un format YAML sous la forme :

```
parameters:
    pgsqlTest:
        driver: pgsql
        host: db4free.net
        port: '5432'
        name: esoldb
        user: esoldb
        password: 4GwpEudP47s5qGD
```



##V3.4.30 Prise en compte du .env à la racine du projet sous la forme
Chaque nom de variable d'environnement doit être sous la forme
esolDb_*nomDeLaBase*_*nomDuParam*
ex :

```
esolDb_pgsqlTestDb_driver=pgsql
esolDb_pgsqlTestDb_host=db4free.net
esolDb_pgsqlTestDb_port='5432'
esolDb_pgsqlTestDb_name=esoldb
esolDb_pgsqlTestDb_user=esoldb
esolDb_pgsqlTestDb_password=4GwpEudP47s5qGD
```

## Fichiers de requetes
Dans le répertoire ./Resources/sql/ il faudra placer les fichiers sql que l'on souhaite executer.

## Basic Usage

### SELECT

```
$esolDb = new \Esol\Db\EsolDb("mysql_test", "./Resources/sql/select.sql");
$arrayData = $esolDb->getArrayData();
```
### INSERT, UPDATE, DELETE
```
$esolDb = new \Esol\Db\EsolDb("mysql_test", "./Resources/sql/insert.sql");
$esolDb->execute();
```

### Passage de paramètres dans les instructions SQL
ESOL/DB remplacera toutes les instructions présentent dans le SQL entre {}

On peut passer des paramètres  : 

- En utilisant Symfony\Component\HttpFoundation\Request
        
```
$request = new Request();
$request->query->set("ORDER_BY", "name");
$esolDb = new \Esol\Db\EsolDb("mysql_test", "./Resources/sql/select.sql");
```

```
$esolDb->setASqlrVars($request);
$arrayData = $esolDb->getArrayData();
```
OU en passant directement le Request à getArrayData
```
$arrayData = $esolDb->getArrayData($request);
```

- En utilisant une table de clé
```    
$array = array(
    "value1" => "BMOPQ", 
    "value2" => "JRYOM"
);
$esolDb = new \Esol\Db\EsolDb("mysql_test", "./Resources/sql/select.sql");
```
$esolDb->setASqlrVars($array);
$arrayData = $esolDb->getArrayData();
```
OU directement en passant le tableau en paramètre de getArrayData
```
$arrayData = $esolDb->getArrayData($array);
```


- En utilisant la fonction publique setASqlrVars passant paramètre par paramètre 
```
$esolDb = new \Esol\Db\EsolDb("mysql_test", "./Resources/sql/select.sql");
$esolDb->setASqlrVars('numPo', $rowData['id_po']);
$arrayData = $esolDb->getArrayData();
```

### récupérer la requète SQL au format String
$esolDb = new \Esol\Db\EsolDb("mysql_test", "./Resources/sql/select.sql");
$sqlr = $esolDb->getSqlr();


## Third Party Packages

Esol/SyTools

## About

### Submitting bugs and feature requests

Cedric DELAMARRE - <cdelamarre@e-solutions.tm.fr>

### Author

Cedric DELAMARRE - <cdelamarre@e-solutions.tm.fr>

### License

### Acknowledgements
