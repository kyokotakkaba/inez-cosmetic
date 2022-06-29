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

    $id = saring($_POST['id']);

    $id_karyawan = saring($_POST['id_karyawan']);
    
    $rekomendasi = saring($_POST['rekomendasi']);
    
    $tgl = saring($_POST['tanggal']);
    $id_root = $_SESSION['idPengguna'];

    if($id=='0'){
        $idBaru = UUIDBaru();
        $q = "
                INSERT INTO 
                    pelatihan_catatan_rekomendasi
                        (
                            id, 
                            id_periode, 
                            id_karyawan, 
                            tanggal, 
                            id_root, 
                            rekomendasi
                        ) 
                VALUES 
                        (
                            '$idBaru',
                            '$idPeriode',
                            '$id_karyawan',
                            '$tgl',
                            '$id_root',
                            '$rekomendasi'
                        )
        ";    
    }
    else{
        $q = "
                UPDATE 
                    pelatihan_catatan_rekomendasi
                SET 
                    rekomendasi='$rekomendasi',
                    id_root='$id_root'
                WHERE
                    id='$id'
        ";
    }

    $e = mysqli_query($conn, $q);

    if($e){
?>
        <script type="text/javascript">
            tampilkanPesan('1','Catatan berhasil disimpan.');
            $('#lastIdSub').val('-');
            reloadSub();
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