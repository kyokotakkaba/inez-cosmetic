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
                karyawan
            SET
                hapus = '1'
            WHERE
                id = '$id'
    ";
    $e = mysqli_query($conn, $q);

    if($e){
        $q = "
                UPDATE
                    akun
                SET
                    hapus = '1'
                WHERE
                    id_pengguna = '$id'
        ";
        $e = mysqli_query($conn, $q);
        if($e){
?>
            <script type="text/javascript">
                tampilkanPesan('1','Data berhasil di hapus.');
                updateRow();
                $('#lastId').val('-');
            </script>
<?php
            exit();        
        }
        else{
?>
            <script type="text/javascript">
                tampilkanPesan('2','Data karyawan berhasil di hapus, akun gagal dinonaktifkan.');
                updateRow();
                $('#lastId').val('-');
            </script>
<?php
            exit();                    
        }
    }
    else{
?>
        <script type="text/javascript">
            tampilkanPesan('0','Terjadi kesalahan saat memproses data.');
        </script>
<?php        
    }
?>