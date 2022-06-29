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
                    angket_label

                SET 
                    hapus = '1'
                    
                WHERE
                    id='$id'
        ";

    $eP = mysqli_query($conn, $qP);

    if($eP){
?>
        <script type="text/javascript">
            tampilkanPesan('1','Data berhasil dihapus.');
            $('#lastIdSub').val('-');
            reloadSub();
            berhasilMenghapusPnK('Label');
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