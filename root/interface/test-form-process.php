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

    $id_ujian = saring($_POST['id_ujian']);
    $kkm = saring($_POST['kkm']);
    $tanggal = saring($_POST['tanggal']);
    $tampilan = saring($_POST['tampilan']);
    $waktu = saring($_POST['waktu']);

    if($id=='0'){
        $idBaru = UUIDBaru();
        $kodeBaru = kodeBaru();
        $q = "
                    INSERT INTO 
                        ujian_pelaksanaan
                            (
                                id, 
                                id_periode, 
                                id_ujian, 
                                kkm,
                                tanggal, 
                                waktu, 
                                tampilan,
                                kode
                            ) 
                    VALUES 
                            (
                                '$idBaru',
                                '$idPeriode',
                                '$id_ujian',
                                '$kkm',
                                '$tanggal',
                                '$waktu',
                                '$tampilan',
                                '$kodeBaru'
                            )
        ";
    }
    else{
        $q = "
                UPDATE 
                    ujian_pelaksanaan 
                SET 
                    kkm='$kkm',
                    tanggal='$tanggal',
                    waktu='$waktu',
                    tampilan='$tampilan'
                WHERE
                    id='$id'
        ";
    }

    echo $q;

    $e = mysqli_query($conn, $q);

    if(!$e){
?>
        <script type="text/javascript">
            tampilkanPesan('0','Terjadi kesalahan saat memproses data.');
        </script>
<?php        
    }
?>



        <script type="text/javascript">
            tampilkanPesan('1','Data berhasil disimpan.');
<?php
    if($id == '0'){
?>
            $('#lastId').val('-');
<?php        
    }
?>            
            updateRowT();
            backToMain();
        </script>