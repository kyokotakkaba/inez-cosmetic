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

    $id_kategori = saring($_POST['id_kategori']);
    $id_produk = saring($_POST['id_produk']);
    $judul = saring($_POST['judul']);
    $deskripsi = saring($_POST['deskripsi']);
    $responden = saring($_POST['responden']);

    if($id=='0'){
        $idBaru = UUIDBaru();
        $kode = kodeBaru();
        $qP = "
                    INSERT INTO 
                        angket
                            (
                                id, 
                                id_kategori, 
                                id_produk, 
                                judul, 
                                deskripsi, 
                                responden,
                                kode
                            ) 
                    VALUES 
                            (
                                '$idBaru',
                                '$id_kategori',
                                '$id_produk',
                                '$judul',
                                '$deskripsi',
                                '$responden',
                                '$kode'
                            )
        ";
    }
    else{
        $kode = saring($_POST['kode']);

        $qP = "
                UPDATE 
                    angket 
                SET 
                    id_kategori='$id_kategori',
                    id_produk='$id_produk',
                    judul='$judul',
                    deskripsi='$deskripsi',
                    responden='$responden'
        ";

        if($kode==''){
            $kode = kodeBaru();
            $qP .= "
                    ,
                    kode = '$kode'
            ";
        }

        $qP .="
                WHERE
                    id='$id'
        ";

        
    }

    $eP = mysqli_query($conn, $qP);

    if($eP){
?>
        <script type="text/javascript">
            tampilkanPesan('1','Data berhasil disimpan.');
            $('#lastId').val('-');
            updateRowQ();
            backToMain();
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