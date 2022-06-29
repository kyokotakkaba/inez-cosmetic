<?php
    session_start();

    if(empty($_SESSION['idPengguna'])){
        echo "SESSION EXPIRED";
        exit();
    }

    $allowed = array(
        'admin',
        'user'
    );

    $fromHome = '../../';

    $jenisPengguna = $_SESSION['jenisPengguna'];
    if(!in_array($jenisPengguna, $allowed)){
        header('location: '.$fromHome.''.$jenisPengguna);
        exit();
    }

    require_once $fromHome.'conf/function.php';

    if(!empty($_SESSION['test'])){
?>
        <script type="text/javascript">
            tampilkanPesan('0', 'Sudah ada tes yang berlangsung.');
        </script>
<?php        
        exit();
    }

    if(empty($_POST['idData'])){
        echo 'NOT ALLOWED!!';
?>
        <script type="text/javascript">
            tampilkanPesan('0', 'NOT ALLOWED!!.');
        </script>
<?php        
        exit();
    }

    $id = saring($_POST['idData']);
    $id_karyawan = $_SESSION['idPengguna'];
    $iniHari = date('Y-m-d');

    $q="
        SELECT 
            ku.id_karyawan, 
            ku.id_pelaksanaan,
            ku.tanggal, 
            ku.mulai, 
            ku.selesai, 
            ku.last_time, 

            up.id_ujian,
            up.kkm,
            up.waktu,
            up.kode

        FROM 
            karyawan_ujian ku

        LEFT JOIN
            ujian_pelaksanaan up
        ON
            ku.id_pelaksanaan = up.id

        WHERE
            ku.hapus = '0'
        AND
            ku.id = '$id'
            
        LIMIT
            1
    ";
    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);
    if($c=='0'){
?>
        <script type="text/javascript">
            tampilkanPesan('0', 'Data tidak valid.');
        </script>
<?php        
        exit();        
    }

    $r = mysqli_fetch_assoc($e);

    $id_karyawan_db = $r['id_karyawan'];
    if($id_karyawan !== $id_karyawan_db){
?>
        <script type="text/javascript">
            tampilkanPesan('0', 'Data tidak valid.');
        </script>
<?php        
        exit();        
    }

    $tanggal = $r['tanggal'];
    if($tanggal !== $iniHari){
?>
        <script type="text/javascript">
            tampilkanPesan('0', 'Hari berbeda. Tidak diperkenankan untuk melanjutkan ujian. Silahkan minta superadmin untuk reset ujian anda.');
        </script>
<?php        
        exit();        
    }


    $id_pelaksanaan = $r['id_pelaksanaan'];
    $id_ujian = $r['id_ujian'];
    $kode = $r['kode'];
    $kkm = $r['kkm'];
    $waktu = $r['waktu'];
    $mulai = $r['mulai'];
    $last_time = $r['last_time'];

    //echo 'mulai : '.$mulai.'<br><br>';
    //echo 'last : '.$last_time.'<br><br>';
    
    $kepake = strtotime($last_time) - strtotime($mulai);
    //echo 'kepake : '.$kepake.' detik<br><br>';

    $saatIni = date('H:i:s');
    //echo 'saat ini : '.$saatIni.'<br><br>';
    
    $akhir = date('H:i:s', strtotime("+".$waktu." minutes", strtotime($saatIni)));
    //echo 'akhir : '.$akhir.'<br><br>';

    $new_akhir = date('H:i:s', strtotime("-".$kepake." seconds", strtotime($akhir)));
    //echo 'new akhir '.date('H:i:s', strtotime($new_akhir)).'<br><br>';

    $_SESSION['test'] = '1';
    $_SESSION['id_pengerjaan'] = $id;
    $_SESSION['id_pelaksanaan'] = $id_pelaksanaan;
    $_SESSION['id_ujian'] = $id_ujian;
    $_SESSION['kkm'] = $kkm;
    $_SESSION['akhir'] = $new_akhir;
    $_SESSION['kode'] = $kode;
?>

<script type="text/javascript">
    tampilkanPesan('1', 'Sesi ujian berhasil dibuat. Laman akan dimuat ulang.');
    setTimeout(function () {
        window.location.reload(true);
    }, 1000);
</script>