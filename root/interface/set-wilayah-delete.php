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
                    wilayah 
                SET 
                    hapus = '1'
                WHERE
                    id='$id'
        ";

    $eP = mysqli_query($conn, $qP);

    if($eP){
        $qS = "
                UPDATE 
                    wilayah_supervisi 
                SET 
                    hapus='1' 
                WHERE
                    id_wilayah='$id'
        ";
        $eS = mysqli_query($conn, $qS);

        $qK = "
                UPDATE 
                    karyawan 
                SET 
                    id_wil = '',
                    
                    hapus='1' 
                WHERE
                    id_wilayah='$id'
        ";
        $eS = mysqli_query($conn, $qS);
?>
        <script type="text/javascript">
            tampilkanPesan('1','Data berhasil dihapus.');
            $('#lastId').val('-');
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