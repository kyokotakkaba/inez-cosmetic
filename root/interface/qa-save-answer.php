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

    $id = saring($_POST['idPertanyaan']);

    $idPenjawab = $_SESSION['idPengguna'];
    $jawaban = saring($_POST['jawaban']);
    $tglSekarang = date('Y-m-d');
    $jamSekarang = date('H:i:s');

    $qP = "
                UPDATE 
                    tanya_jawab 

                SET 
                    id_penjawab = '$idPenjawab',
                    jawaban = '$jawaban',
                    jawab_pada = '$tglSekarang',
                    jawab_jam = '$jamSekarang'

                WHERE
                    id='$id'
        ";

    $eP = mysqli_query($conn, $qP);

    $qG = "
            SELECT 
                
                tj.id_karyawan, 
                tj.tanya_pada,
                tj.tanya_jam,
                
                mk.nama kelompok
                
            FROM 
                tanya_jawab tj

            LEFT JOIN
                materi_kelompok mk
            ON
                tj.id_kelompok = mk.id

            WHERE
                tj.id = '$id'
    ";
    $eG = mysqli_query($conn, $qG);
    $rG = mysqli_fetch_assoc($eG);

    $untukNotif = $rG['id_karyawan'];
    $tanyaPada = tanggalKan($rG['tanya_pada']).' '.$rG['tanya_jam'];

    $judulNotif = 'Pertanyaan anda dijawab';
    $isiNotif = 'Pertanyaan yang diajukan pada kelompok '.$rG['kelompok'].' (ditanyakan pada '.$tanyaPada.') telah ditanggapi.';

    if($eP){
        //send system notif
        sendNotif($judulNotif, $isiNotif, $untukNotif);
?>
        <script type="text/javascript">
            tampilkanPesan('1','Data berhasil disimpan.');
            updateQa();
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