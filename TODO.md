1° Ajout d'un script qui se lance à l'issue de composer install qui construira si necessaire 
- les fichiers config/packages/[dev|prod|test]/esolDb.yml 
- le repertoire Resources/sql
2° Rendre compatible avec pdo_mysql et pdo_pgsql
3° Faire le printSql
4° nettoyer les public et private
    Conn.php
    ~~Esol.db.php~~
    Params.php
    Sqlr.php
~~5° tester extension mysqli et pgsql~~
6° document les fonctions public
    Conn.php
    ~~Esol.db.php~~
    Params.php
    Sqlr.php
7° gérer les environnements dev, prod, test