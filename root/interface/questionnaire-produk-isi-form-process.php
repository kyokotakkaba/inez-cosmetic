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

    $gambarMentah = saring($_POST['gambar']);
    $gambarPros = str_replace($baseLink, '', $gambarMentah);
    $gambar = str_replace("../", "", $gambarPros);

    if($id=='0'){
        $idBaru = UUIDBaru();
        $qP = "
                    INSERT INTO
                        produk
                            (
                                id, 
                                nama, 
                                deskripsi, 
                                gambar
                            ) 
                    VALUES 
                            (
                                '$idBaru',
                                '$nama',
                                '$deskripsi',
                                '$gambar'
                            )
        ";
    }
    else{
        $qP = "
                UPDATE 
                    produk 
                SET 
                    nama='$nama',
                    deskripsi='$deskripsi',
                    gambar='$gambar'
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
            berhasilMenambahkanPnK('Produk');
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