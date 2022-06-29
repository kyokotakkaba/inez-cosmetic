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
    $id_kelompok = saring($_POST['id_kelompok']);
    $nama = saring($_POST['nama']);
    $deskripsi = saring($_POST['deskripsi']);
    
    if($id=='0'){
        $idBaru = UUIDBaru();

        $qN = "
                SELECT
                    COUNT(id) jml
                FROM
                    materi_kelompok_bahasan
        ";
        $eN = mysqli_query($conn, $qN);
        $rA = mysqli_fetch_assoc($eN);
        $no_a = $rA['jml'];

        $no = $no_a + 1;

        $q = "
                INSERT INTO 
                    materi_kelompok_bahasan
                        (
                            id, 
                            id_kelompok,
                            no,
                            nama, 
                            deskripsi
                        ) 
                VALUES 
                        (
                            '$idBaru',
                            '$id_kelompok',
                            '$no',
                            '$nama',
                            '$deskripsi'
                        )
        ";    
    }
    else{
        $q = "
                UPDATE 
                    materi_kelompok_bahasan
                SET 
                    nama='$nama',
                    id_kelompok = '$id_kelompok',
                    deskripsi='$deskripsi'
                WHERE
                    id='$id'
        ";
    }

    $e = mysqli_query($conn, $q);

    if($e){
?>
        <script type="text/javascript">
            tampilkanPesan('1','Bahasan materi berhasil disimpan.');
            $('#lastId').val('-');
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