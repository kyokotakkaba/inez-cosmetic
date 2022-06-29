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
        $qN = "
                SELECT
                    no
                FROM
                    tingkat_belajar
                WHERE
                    hapus = '0'
                ORDER BY
                    no DESC
                LIMIT
                    1
        ";
        $eN = mysqli_query($conn, $qN);
        $cN = mysqli_num_rows($eN);
        if($cN=='0'){
            $no = '1';
        }
        else{
            $rN = mysqli_fetch_assoc($eN);
            $last = $rN['no'];
            $no = $last+1;
        }

        $q = "
                INSERT INTO 
                    tingkat_belajar
                        (
                            id, 
                            no, 
                            nama, 
                            deskripsi
                        ) 
                VALUES (
                            '$idBaru',
                            '$no',
                            '$nama',
                            '$deskripsi'
                        )
        ";
    }
    else{
        $q = "
                UPDATE 
                    tingkat_belajar 
                SET 
                    nama='$nama',
                    deskripsi='$deskripsi'
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