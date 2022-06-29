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

    $nama = saring($_POST['nama']);

    if($id=='0'){
        $idBaru = UUIDBaru();
        $q = "
                INSERT INTO 
                    pelatihan_catatan_topik
                        (
                            id, 
                            nama
                        ) 
                VALUES 
                        (
                            '$idBaru',
                            '$nama'
                        )
        ";    
    }
    else{
        $q = "
                UPDATE 
                    pelatihan_catatan_topik
                SET 
                    nama='$nama'
                WHERE
                    id='$id'
        ";
    }

    $e = mysqli_query($conn, $q);

    if($e){
?>
        <script type="text/javascript">
            tampilkanPesan('1','Topik berhasil disimpan.');
            $('#lastId').val('-');
            $('#lastIdSub').val('-');
            loadTopic();
            backToMain();
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