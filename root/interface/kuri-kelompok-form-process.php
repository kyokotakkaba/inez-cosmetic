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
    $deskripsi = saring($_POST['deskripsi']);
    if(empty($_POST['n_pk'])){
        $n_pk = '0';
    }
    else{
        $n_pk = saring($_POST['n_pk']);
    }
    
    
    if($id=='0'){
        $idBaru = UUIDBaru();
        $q = "
                INSERT INTO 
                    materi_kelompok
                        (
                            id, 
                            nama, 
                            deskripsi,
                            n_pk
                        ) 
                VALUES 
                        (
                            '$idBaru',
                            '$nama',
                            '$deskripsi',
                            '$n_pk'
                        )
        ";    
    }
    else{
        $q = "
                UPDATE 
                    materi_kelompok 
                SET 
                    nama='$nama',
                    deskripsi='$deskripsi',
                    n_pk='$n_pk'
                WHERE
                    id='$id'
        ";
    }

    $e = mysqli_query($conn, $q);

    if($e){
?>
        <script type="text/javascript">
            tampilkanPesan('1','Data berhasil disimpan.');
            $('#lastId').val('-');
            $('#lastSubId').val('-');
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