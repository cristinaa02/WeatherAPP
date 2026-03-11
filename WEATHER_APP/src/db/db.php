<?php

//Conexión con la base de datos

class Database {

    private $db=null;
    
    public static function connect ()
    {
        $host='db'; 
        $dbname=getenv ('DB_NAME');

        try {
            $dsn = 'mysql:host='.$host.";dbname=".$dbname.";charset=UTF8";
            $dbh = new PDO($dsn, 'admin', 'admin123');
            $dbh->setAttribute (PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $dbh;

        } catch (PDOException $e){
            echo $e->getMessage();
        }
    }

}
?>