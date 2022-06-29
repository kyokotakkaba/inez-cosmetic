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

    if($id=='0'){
        $idBaru = UUIDBaru();
        $qP = "
                    INSERT INTO 
                        angket_kategori
                            (
                                id, 
                                nama, 
                                deskripsi
                            ) 
                    VALUES 
                            (
                                '$idBaru',
                                '$nama',
                                '$deskripsi'
                            )
        ";
    }
    else{
        $qP = "
                UPDATE 
                    angket_kategori 

                SET 
                    nama='$nama',
                    deskripsi='$deskripsi'
                    
                WHERE
                    id='$id'
        ";
    }

    $eP = mysqli_query($conn, $qP);

    if($eP){
?>
        <script type="text/javascript">
            tampilkanPesan('1','Data berhasil disimpan.');
            $('#lastIdSub').val('-');
            reloadSub();
<?php
    if($id=='0'){
?>
            berhasilMenambahkanPnK('JenisA');
<?php        
    }
?>
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