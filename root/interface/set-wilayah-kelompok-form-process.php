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
    $standar = saring($_POST['standar']);
    
    if($id=='0'){
        $idBaru = UUIDBaru();

        $q = "
                INSERT INTO 
                    wilayah_kelompok
                        (
                            id, 
                            nama, 
                            deskripsi,
                            standar
                        ) 
                VALUES (
                            '$idBaru',
                            '$nama',
                            '$deskripsi',
                            '$standar'
                        )
        ";
    }
    else{
        $q = "
                UPDATE 
                    wilayah_kelompok 
                SET 
                    nama='$nama',
                    deskripsi='$deskripsi',
                    standar='$standar'
                WHERE
                    id='$id'
        ";
    }
    
    $e = mysqli_query($conn, $q);

    if($e){
?>
        <script type="text/javascript">
            tampilkanPesan('1','Data berhasil disimpan.');
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