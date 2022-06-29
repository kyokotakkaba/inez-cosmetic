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

    if(empty($_SESSION['test'])){
?>
        <script type="text/javascript">
            tampilkanPesan('0', 'Tidak ada tes yang berlangsung.');
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



    $idData = saring($_POST['idData']);
    $id_pengerjaan = $_SESSION['id_pengerjaan'];

    if($idData !== $id_pengerjaan){
?>
        <script type="text/javascript">
            tampilkanPesan('0', 'Data tidak valid.');
        </script>
<?php        
        exit();        
    }



    $id_karyawan = $_SESSION['idPengguna'];
    $id_ujian = $_SESSION['id_ujian'];
    $kkm = $_SESSION['kkm'];
    $selesai = date('H:i:s');
    $n_poin = 0;


    $q = "
            SELECT
                nilai_akhir

            FROM
                karyawan_ujian

            WHERE
                id = '$id_pengerjaan'

            LIMIT
                1
    ";
    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);

    if($c == '0'){
?>
        <script type="text/javascript">
            tampilkanPesan('0', 'Terjadi kesalahan saat memproses kalkulasi nilai akhir.');
        </script>
<?php
        exit();            
    }

    $r = mysqli_fetch_assoc($e);
    $nilai_akhir = $r['nilai_akhir'];


    //cek if its UN -> 5483-8ABF1C
    if($id_ujian == '5483-8ABF1C'){
        $iniHari = date('Y-m-d');

        //Rekap perhitungan poin ujian bulanan sebelumnya max (3) -> 60BF-E0E0
        // $q = "
        //         SELECT 
        //             ku.id, 
        //             ku.tanggal, 
        //             ku.mulai, 
        //             ku.nilai_akhir

        //         FROM 
        //             karyawan_ujian ku

        //         RIGHT JOIN
        //             ujian_pelaksanaan up
        //         ON
        //             ku.id_pelaksanaan = up.id
        //         AND
        //             up.id_periode = '$idPeriode'
        //         AND
        //             up.id_ujian = '60BF-E0E0'

        //         WHERE
        //             ku.hapus = '0'
        //         AND
        //             ku.id_karyawan = '$id_karyawan'
        //         AND
        //             ku.tanggal <= '$iniHari'

        //         ORDER BY
        //             ku.tanggal DESC,
        //             ku.mulai DESC

        //         LIMIT
        //             3
        // ";

        $q_setting = "
        SELECT 
            value 
        FROM 
            setting
        WHERE
            id = 0
        ";
        $e_setting = mysqli_query($conn, $q_setting);
        $d_setting = mysqli_fetch_assoc($e_setting);
        $limit_ub = $d_setting['value'];

        $q = "
        SELECT 
            ku.id, 
            ku.tanggal, 
            ku.mulai, 
            ku.nilai_akhir

        FROM 
            karyawan_ujian ku

        RIGHT JOIN
            ujian_pelaksanaan up
        ON
            ku.id_pelaksanaan = up.id
        AND
            up.id_ujian = '60BF-E0E0'

        WHERE
            ku.hapus = '0'
        AND
            ku.id_karyawan = '$id_karyawan'
        AND
            ku.tanggal <= '$iniHari'

        ORDER BY
            ku.tanggal DESC,
            ku.mulai DESC

        LIMIT
            $limit_ub
        ";
        
        $e = mysqli_query($conn, $q);
        $c = mysqli_num_rows($e);

        $rerata = 0;
        $total = 0;

        if($c > 0){
            while ($d = mysqli_fetch_assoc($e)) {
                $score = $d['nilai_akhir'];
                $total = $total + $score;
            }

            $rerata = $total / $c;
        }

        $poin = 0;

        if($rerata > 0){
            $rerata = round($rerata);

            //set point based on fixed rules
            if($rerata >= 70 && $rerata < 80){
                $poin = 2;
            }
            else if($rerata >= 80 && $rerata < 90){
                $poin = 4;
            }
            else if($rerata >= 90 && $rerata <= 99){
                $poin = 6;
            }
            else if($rerata > 99){
                $poin = 8;
            }
        }

        if($poin > 0){
            if($nilai_akhir < 100){
                $newNilai = $nilai_akhir + $poin;
                $newNilai = round($newNilai);
                if($newNilai > 100){
                    $newNilai = 100;
                }

                $nilai_akhir = $newNilai;
            }
        }
    }



    $q = "
            UPDATE 
                karyawan_ujian 

            SET 
                last_time = '$selesai',
                selesai = '$selesai',
                nilai_akhir = '$nilai_akhir',
                n_poin = '$n_poin'

            WHERE
                id = '$id_pengerjaan'
    ";
    $e = mysqli_query($conn, $q);
    if(!$e){
?>
        <script type="text/javascript">
            tampilkanPesan('0', 'Terjadi kesalahan saat memproses data.');
        </script>
<?php        
        exit();
    }






    unset($_SESSION['test']);
    unset($_SESSION['id_pelaksanaan']);
    unset($_SESSION['id_ujian']);
    unset($_SESSION['kkm']);
    unset($_SESSION['akhir']);
    unset($_SESSION['kode']);
?>

<script type="text/javascript">
    tampilkanPesan('1', 'Ujian selesai.');
    setTimeout(function () {
        window.location.href = 'finish/';
    }, 1000);
</script>