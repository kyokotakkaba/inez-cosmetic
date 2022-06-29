<?php
    session_start();
    $appSection = 'admin';

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
	
	$id_notif = saring($_POST['idData']);
    $id_pengguna = $_SESSION['idPengguna'];

    $idBaru = UUIDBaru();

    $q = "
            INSERT INTO 
                notifikasi_readed
                    (
                        id, 
                        id_notif, 
                        id_pengguna
                    ) 
            VALUES 
                    (
                        '$idBaru',
                        '$id_notif',
                        '$id_pengguna'
                    )
    ";
    $e = mysqli_query($conn, $q);
    if($e){
?>
        <script type="text/javascript">
            tampilkanPesan('1','Nofikasi berhasil dihapus.');
            updateRow();
            kurangiJmlmNotif();
        </script>
<?php
    }
    else{
?>
        <script type="text/javascript">
            tampilkanPesan('0','Terjadi kesalahan saat memproses data.');
        </script>
<?php
    }
?>