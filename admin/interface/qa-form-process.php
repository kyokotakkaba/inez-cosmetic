<?php
    session_start();
    $appSection = 'admin';

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

    $idPengguna = $_SESSION['idPengguna'];
    $q = "
            SELECT
                nama

            FROM
                karyawan

            WHERE
                id = '$idPengguna'
                
            LIMIT
                1
    ";

    $e = mysqli_query($conn, $q);
    $r = mysqli_fetch_assoc($e);
    $namaPengguna = $r['nama'];

    $id = saring($_POST['id']);
    $id_kelompok = saring($_POST['id_kelompok']);
    $pertanyaan = saring($_POST['pertanyaan']);

    if($id=='0'){
        $idBaru = UUIDBaru();
        $tanya_pada = date('Y-m-d');
        $tanya_jam = date('H:i:s');

        $q = "
                    INSERT INTO 
                        tanya_jawab
                            (
                                id, 
                                id_periode, 
                                id_kelompok, 
                                id_karyawan, 
                                pertanyaan, 
                                tanya_pada, 
                                tanya_jam
                            ) 
                    VALUES 
                            (
                                '$idBaru',
                                '$idPeriode',
                                '$id_kelompok',
                                '$idPengguna',
                                '$pertanyaan',
                                '$tanya_pada',
                                '$tanya_jam'
                            )
        ";

        $qG = "
                SELECT
                    nama

                FROM
                    materi_kelompok

                WHERE
                    id = '$id_kelompok'
        ";
        $eG = mysqli_query($conn, $qG);
        $rG = mysqli_fetch_assoc($eG);

        $judulNotif = 'Pengajuan pertanyaan';
        $isiNotif = $namaPengguna.' mengajukan pertanyaan baru pada kelompok '.$rG['nama'].'.';
        $untukNotif = 'root';
    }
    else{
        $q = "
                UPDATE 
                    tanya_jawab 

                SET 
                    id_kelompok='$id_kelompok',
                    pertanyaan='$pertanyaan'

                WHERE
                    id='$id'
        ";
    }

    //echo $qP;

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
        tampilkanPesan('1','Data berhasil disimpan.');
<?php
    if($id=='0'){
        //send system notif
        sendNotif($judulNotif, $isiNotif, $untukNotif);
?>
        $('#lastId').val('-');
<?php        
    }
?>        
        updateRowQa();
        backToMain();
    </script>