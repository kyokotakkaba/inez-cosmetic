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

    if(empty($_POST['idData'])){
?>
        <script type="text/javascript">
            tampilkanPesan('0','Tidak ada data untuk di impor.');
        </script>
<?php        
        exit();
    }

    $idData = saring($_POST['idData']);


    $sekarang = date('Y-m-d');

    $q = "
            SELECT
                id_ujian,
                tanggal

            FROM
                ujian_pelaksanaan

            WHERE
                id = '$idData'
            LIMIT
                1
    ";
    $e = mysqli_query($conn, $q);
    $r = mysqli_fetch_assoc($e);


    $id_ujian = $r['id_ujian'];
    $tanggal = $r['tanggal'];
    $susulan = '0';

    if($sekarang>$tanggal){
        $tanggal = $sekarang;
        $susulan = '1';
    }










    //getting latest id pelaksanaan to check remidial
    $q = "
            SELECT 
                id, 
                kkm, 
                waktu, 
                tampilan, 
                kode
            FROM 
                ujian_pelaksanaan 

            WHERE
                id_periode = '$idPeriode'
            AND
                id_ujian = '$id_ujian'
            AND
                hapus = '0'
            AND
                tanggal <= '$sekarang'
            AND
                aktif = '1'
            AND
                id != '$idData'

            ORDER BY
                tanggal DESC

            LIMIT
                1
    ";

    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);

    if($c==1){
        $r = mysqli_fetch_assoc($e);
        $idPrev = $r['id'];
        $kkm = $r['kkm'];

        //get the id karyawan that remidi
        $q = "
                SELECT 
                    id, 
                    id_karyawan, 
                    tanggal, 
                    mulai, 
                    selesai
                    
                FROM 
                    karyawan_ujian 

                WHERE
                    hapus = '0' 
                AND
                    id_pelaksanaan = '$idPrev'
                AND
                    remidi = '1'
                AND
                    id_karyawan

                    NOT IN (
                        SELECT 
                            id_karyawan
                            
                        FROM 
                            ujian_pelaksanaan_target_karyawan 

                        WHERE
                            id_pelaksanaan = '$idData'
                        AND
                            hapus = '0'
                    )
        ";

        $e = mysqli_query($conn, $q);
        $jmlRemidial = mysqli_num_rows($e);
    }


    if($jmlRemidial==0){
?>
        <script type="text/javascript">
            tampilkanPesan('0','Tidak ada data untuk di impor.');
        </script>
<?php        
        exit();
    }


    $arIdCalon = array();

    while ($r = mysqli_fetch_assoc($e)) {
        $idCalon = $r['id_karyawan'];
        array_push($arIdCalon, $idCalon);
    }


    $q = "
            INSERT INTO 
                ujian_pelaksanaan_target_karyawan
                    (
                        id, 
                        id_pelaksanaan, 
                        id_karyawan,
                        susulan,
                        susulan_tgl
                    ) 
            VALUES
    ";

    $jml = $jmlRemidial-1;

    for ($i=0; $i <= $jml; $i++) { 
        $idCalon = $arIdCalon[$i];
        $idBaru = UUIDBaru();
        $q.="
                (
                    '$idBaru',
                    '$idData',
                    '$idCalon',
                    '$susulan',
                    '$tanggal'
                )
            ";

        if($jmlRemidial>1){
            if($i<$jml){
                $q.="
                        ,
                ";
            }
        }
    }


    $e = mysqli_query($conn, $q);


    if($e){
?>
        <script type="text/javascript">
            tampilkanPesan('1','Data karyawan remidial berhasil dijadikan peserta ujian pada <?php echo tanggalKan($tanggal); ?>.');
            $('#imporMsg<?php echo $idData; ?>').transition('drop');
            updateRowPart();
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