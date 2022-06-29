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

    $idMentah = saring($_POST['idData']);
    $pecah = explode('[pisah]', $idMentah);

    $idPelak = $pecah[0];
    $idKep = $pecah[1];
    $idKar = $pecah[2];

    $q = "
            UPDATE 
                ujian_pelaksanaan_target_karyawan 

            SET
                hapus = '1'

            WHERE
                id='$idKep'
    ";
    $eP = mysqli_query($conn, $q);
    if(!$e){
?>
        <script type="text/javascript">
            tampilkanPesan('0', 'Terjadi kesalahan saat memproses data.');
        </script>
<?php        
        exit();
    }




    $q = "
            SELECT
                id

            FROM 
                karyawan_ujian 

            WHERE
                id_pelaksanaan='$idPelak'
            AND
                id_karyawan='$idKar'
            AND
                hapus='0'

            LIMIT
                1
    ";
    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);
    if($c == '1'){
        $r = mysqli_fetch_assoc($e);
        $idPenger = $r['id'];

        $q = "
                UPDATE
                    karyawan_ujian

                SET
                    hapus = '1'

                WHERE
                    id = '$idPenger'
        ";
        $e = mysqli_query($conn, $q);

        $q = "
                UPDATE
                    karyawan_ujian_pengerjaan
                SET
                    hapus = '1'
                WHERE
                    id_pengerjaan = '$idPenger'
        ";
        $e = mysqli_query($conn, $q);

        $q = "
                UPDATE
                    karyawan_ujian_pengerjaan_daftar_jawaban
                SET
                    hapus = '1'
                WHERE
                    id_pengerjaan = '$idPenger'
        ";
        $e = mysqli_query($conn, $q);
    }
?>

<script type="text/javascript">
    tampilkanPesan('1','Data berhasil dihapus.');
    updateRowT();
    updateRowSub();
</script>         