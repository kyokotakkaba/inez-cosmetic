<?php 
session_start();
$appSection = 'root';

if(empty($_SESSION['idPengguna'])){
    echo "SESSION EXPIRED";
    exit();
}

$jenisPengguna = $_SESSION['jenisPengguna'];
if($jenisPengguna !== $appSection){
    echo "INVALID USER";
    exit();
}

$fromHome = '../../';
require_once $fromHome."conf/function.php";

$limit = $_POST['value'];
 $q= "
 UPDATE 
     setting 
 SET 
     value = $limit
 WHERE
     id= 0
";

$e = mysqli_query($conn, $q);
?>