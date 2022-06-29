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


    $idData = saring($_POST['idData']);
    $tanggal = saring($_POST['tanggal']);

    $q = "
            UPDATE 
                ujian_pelaksanaan_target_karyawan 
            SET 
                susulan='1',
                susulan_tgl='$tanggal'
            WHERE
                id='$idData'
    ";
    
    $e = mysqli_query($conn, $q);

    if($e){
?>
        <script type="text/javascript">
            tampilkanPesan('1','Data tanggal ujian susulan berhasil diset.');
            updateRowSub();
            $('#lastIdSub').val('-');
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