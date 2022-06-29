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

    $id = saring($_POST['idData']);

    $qP = "
                UPDATE 
                    wilayah_kelompok
                SET 
                    hapus = '1'
                WHERE
                    id='$id'
        ";

    $eP = mysqli_query($conn, $qP);

    if($eP){
        $qS = "
                UPDATE 
                    wilayah
                SET 
                    id_kelompok='' 
                WHERE
                    id_kelompok='$id'
        ";
        $eS = mysqli_query($conn, $qS);
?>
        <script type="text/javascript">
            tampilkanPesan('1','Data berhasil dihapus.');
            reloadFrame();
        </script>
<?php           
        exit();
    }         
    else{
?>
        <script type="text/javascript">
            tampilkanPesan('0','Terjadi kesalahan saat memproses data.');
        </script>
<?php           
        exit();                     
    }
?>