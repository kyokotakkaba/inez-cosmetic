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

    $q = "
            UPDATE 
                pelatihan_catatan_topik
            SET 
                hapus='1'
            WHERE
                id='$id'
    ";

    $e = mysqli_query($conn, $q);

    if($e){
        $qU = "
                UPDATE
                    pelatihan_catatan
                SET
                    hapus = '1'
                WHERE
                    id_topik = '$id'
        ";
        $eU = mysqli_query($conn, $qU);
?>
        <script type="text/javascript">
            tampilkanPesan('1','Data berhasil dihapus.');
            $('#lastId').val('-');
            $('#lastIdSub').val('-');
            loadTopic();
            updateRow();
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