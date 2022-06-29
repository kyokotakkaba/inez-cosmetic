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

    $mentah = $_POST['idData'];
    $pecah = explode('[pisah]', $mentah);

    if(empty($pecah[0]) || empty($pecah[1]) || empty($pecah[2])){
?>
        <script type="text/javascript">
            tampilkanPesan('0', 'Data tidak valid.');
        </script>
<?php        
        exit();
    }
	
    $idK    = saring($pecah[0]);
	$ket 	= saring($pecah[1]);
    $tgl    = saring($pecah[2]);
    if($ket=='masuk'){
        $stat = '1';
    }
    else{
        $stat = '0';
    }

    $idRoot = $_SESSION['idPengguna'];

    $q = "
            SELECT 
                id, 
                id_root, 
                masuk, 
                keterangan

            FROM 
                pelatihan_presensi 

            WHERE
                id_periode = '$idPeriode'
            AND
                id_karyawan = '$idK'
            AND
                tanggal = '$tgl'
            AND
                hapus = '0'

            LIMIT
                1
    ";
    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);

    if($c == '1'){
?>
        <script type="text/javascript">
            updateTrain();
            tampilkanPesan('2', 'Presensi sudah diset sebelumnya. Server membutuhkan waktu untuk reload data tampilan menu presensi.');
        </script>
<?php        
        exit();
    }



    $idBaru = UUIDBaru();
    $q = "
            INSERT INTO 
                pelatihan_presensi
                    (
                        id, 
                        id_periode, 
                        id_karyawan, 
                        tanggal, 
                        id_root, 
                        masuk, 
                        keterangan
                    ) 
            VALUES 
                    (
                        '$idBaru',
                        '$idPeriode',
                        '$idK',
                        '$tgl',
                        '$idRoot',
                        '$stat',
                        '$ket'
                    )
    ";
    $e = mysqli_query($conn, $q);
    if(!$e){
?>
        <script type="text/javascript">
            tampilkanPesan('0','Terjadi kesalahan saat memproses data.');
        </script>
<?php        
        exit();
    }


?>
<script type="text/javascript">
    tampilkanPesan('1','Presensi disimpan.');
    updateTrain();
</script>