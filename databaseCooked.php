<?php
class Database {
  private static $mysqli = null;
  public function __construct() {
    die('Init function error');
  }

  public static function dbConnect() {
  $mysqli = null;
  try {
     require_once("/home/group12-sp25/DBgroup12.php");
      $mysqli = new PDO('mysql:host='.DBHOST.';dbname='.DBNAME, USERNAME, PASSWORD);
      //echo "Successful Connection";
   }  catch (PDOException $e){
    echo "Could not Connect to Database";
  }
 
 
    return $mysqli;
  }

  public static function dbDisconnect() {
    $mysqli = null;
  }
}
?>
