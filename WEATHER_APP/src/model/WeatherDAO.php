<?php
include_once ('db/db.php');

class WeatherDAO {

    public $db_con;

    //conexión BD
    public function __construct (){
        $this->db_con=Database::connect();
    }

    //devuelve todas las consultas de la BD
    public function getAllSearches(){
        $stmt= $this->db_con->prepare("SELECT * FROM consultas ORDER BY fecha_consulta DESC");
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function insertSearch($ciudad, $lat, $lon, $temp, $desc, $icon){
        $stmt= $this->db_con->prepare ("INSERT INTO consultas (ciudad, latitud, longitud, temperatura, descripcion, icon) VALUES (:ciudad, :latitud, :longitud, :temperatura, :descripcion, :icon)");      
        $stmt->bindParam(':ciudad', $ciudad);
        $stmt->bindParam(':latitud', $lat);
        $stmt->bindParam(':longitud', $lon);
        $stmt->bindParam(':temperatura', $temp);
        $stmt->bindParam(':descripcion', $desc);
        $stmt->bindParam(':icon', $icon);

        try{
            return $stmt->execute();
        } catch (PDOException $e){
            echo $e->getMessage();
        }
        
    }
}
?>