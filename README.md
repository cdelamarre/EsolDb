Cette librairie permet d'intérroge des bases de données POSTGRESQL ou MYSQL: 
- de recuperer un array à partir d'une requete SELECT
- d'executer un INSERT, UPDATE, DELETE ou toute autre instruction SQL ne nécessitant pas de retour.

# Installation

## insallation via Composer
```
composer require esol/db
```

## Extensions php à activer dans php.ini

Esol/Db utilise les extensions php_mysqli et php_pgsql

### Pour Mysql
extension=php_mysqli.dll
### Pour Pgsql
extension=php_pgsql.dll

## Configuration

Dans le fichier {project_dir}/config/packages/prod/esolDb.yml on trouve les paramètres vers la base de donnée dans laquelle on veut agir

## Fichiers de requetes
Dans le répertoire ./Resources/sql/ il faudra placer les fichiers sql que l'on souhaite executer.

## Basic Usage

### SELECT

````
$esolDb = new \Esol\Db\EsolDb("mysql_test", "./Resources/sql/select.sql");
$arrayData = $esolDb->getArrayData();
````
### INSERT, UPDATE, DELETE
````
$esolDb = new \Esol\Db\EsolDb("mysql_test", "./Resources/sql/insert.sql");
$esolDb->execute();
````

### Passage de paramètres dans les instructions SQL
ESOL/DB remplacera toutes les instructions présentent dans le SQL entre {}

On peut passer des paramètres  : 

- En utilisant Symfony\Component\HttpFoundation\Request
````    
$request = new Request();
$request->query->set("ORDER_BY", "name");

$esolDb = new \Esol\Db\EsolDb("mysql_test", "./Resources/sql/select.sql");
$esolDb->setASqlrVars($request);
````

- En utilisant la fonction publique setASqlrVars passant paramètre par paramètre 
````
$esolDb = new \Esol\Db\EsolDb("mysql_test", "./Resources/sql/select.sql");
$esolDb->setASqlrVars('numPo', $rowData['id_po']);
````

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
