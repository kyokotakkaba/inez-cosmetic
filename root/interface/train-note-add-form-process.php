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
    
    $id_topik = saring($_POST['id_topik']);
    $nilai_before = saring($_POST['nilai_before']);
    $nilai_after = saring($_POST['nilai_after']);
    
    $tgl = saring($_POST['tanggal']);
    $id_root = $_SESSION['idPengguna'];

    if($id=='0'){
        $idBaru = UUIDBaru();
        $q = "
                INSERT INTO 
                    pelatihan_catatan
                        (
                            id, 
                            id_periode, 
                            id_karyawan, 
                            tanggal, 
                            id_topik, 
                            id_root, 
                            nilai_before, 
                            nilai_after
                        ) 
                VALUES 
                        (
                            '$idBaru',
                            '$idPeriode',
                            '$id_karyawan',
                            '$tgl',
                            '$id_topik',
                            '$id_root',
                            '$nilai_before',
                            '$nilai_after'
                        )
        ";    
    }
    else{
        $qC = "
                SELECT
                    id

                FROM
                    pelatihan_catatan

                WHERE
                    id_karyawan = '$id_karyawan'
                AND
                    tanggal = '$tgl'
                AND
                    hapus = '0'
                AND
                    id != '$id'
                AND
                    id_topik = '$id_topik'

                LIMIT
                    1
        ";
        $eC = mysqli_query($conn, $qC);
        $cC = mysqli_num_rows($eC);

        if($cC=='1'){
?>
            <script type="text/javascript">
                tampilkanPesan('0','Topik yang terpilih sudah digunakan.');
            </script>
<?php           
            exit();         
        }

        $q = "
                UPDATE 
                    pelatihan_catatan 
                SET 
                    id_topik='$id_topik',
                    id_root='$id_root',
                    nilai_before='$nilai_before',
                    nilai_after='$nilai_after'

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
            updateTrain();
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