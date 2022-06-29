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

    $id = saring($_POST['idData']);

    $q1 = "
            SELECT 
                COUNT(id) jml
            FROM 
                ujian_pelaksanaan_target_karyawan 
            WHERE 
                id_pelaksanaan = '$id'
            AND
                hapus = '0'
    ";
    $e1 = mysqli_query($conn, $q1);
    $r1 = mysqli_fetch_assoc($e1);

    $jmlPeserta = $r1['jml'];

    if($jmlPeserta=='0'){
?>
        <script type="text/javascript">
            tampilkanPesan('0','Peserta ujian tidak ada.');
        </script>
<?php        
        exit();
    }

    //ambil id_ujian sebagai pembeda
    $q = "
            SELECT
                id_ujian

            FROM
                ujian_pelaksanaan

            WHERE
                id = '$id'
    ";

    $e = mysqli_query($conn, $q);
    $r = mysqli_fetch_assoc($e);

    $id_ujian = $r['id_ujian'];

    if($id_ujian=='5483-8ABF1C'){
        //ujian nasional
        $jmlPkMin = 20;
        $jmlNon = 30;


        //ada setting grade pk?
        $q = "
                SELECT
                    id

                FROM
                    ujian_grade

                WHERE
                    hapus = '0'

                LIMIT
                    1
        ";
        $e = mysqli_query($conn, $q);
        $c = mysqli_num_rows($e);
        if($c=='0'){
?>
            <script type="text/javascript">
                tampilkanPesan('0','Grade penilaian PK tidak terdeteksi. Silahkan atur pada menu setting.');
            </script>
<?php        
            exit();
        }

    }
    else{
        $jmlPkMin = 6;
        $jmlNon = 12;
    }


    $q = "
            SELECT
                count(p.id) jmlP,
                count(j.id) jmlJ

            FROM
                pertanyaan p,
                jawaban j

            WHERE
                p.hapus = '0'
            AND
                j.hapus = '0'
            AND
                p.isi = ''
            AND
                j.isi = ''
    ";

    $e = mysqli_query($conn, $q);
    
    $r = mysqli_fetch_assoc($e);
    $jmlP = $r['jmlP'];
    $jmlJ = $r['jmlJ'];

    $ket = '';
    $adaKosong = 0;

    if($jmlP > 0){
        $ket .= 'Terdapat <strong>'.$jmlP.'</strong> pertanyaan dengan teks pertanyaan kosong. ';
        $adaKosong = '1';
    }

    if($jmlJ > 0){
        $ket .= 'Terdapat <strong>'.$jmlJ.'</strong> jawaban dengan teks jawaban kosong. ';
        $adaKosong = '1';
    }

    if($adaKosong == '1'){
        $ket .= 'Pastikan saat impor data, anda <strong>paste as text ke cell file excel</strong>.';
?>
        <script type="text/javascript">
            tampilkanPesan('0', '<?php echo $ket; ?>');
        </script>
<?php
    exit();        
    }




    //cek ketersediaan soal
    $q = "
            SELECT
                mk.id,
                mk.nama,
                mk.n_pk,
                
                count(p.id) jml

            FROM
                materi_kelompok mk

            LEFT JOIN
                pertanyaan p
            ON
                mk.id = p.id_kelompok
            AND
                p.hapus = '0'

            WHERE
                mk.hapus = '0'

            GROUP BY
                mk.id

            ORDER BY
                mk.nama ASC
    ";

    $e = mysqli_query($conn, $q);
    $jml_pk = 0;
    $jml_non = 0;

    while ($r = mysqli_fetch_assoc($e)) {
        $nama = $r['nama'];
        $n_pk = $r['n_pk'];
        $jml = $r['jml'];

        if($n_pk=='1'){
            $jml_pk = $jml_pk + $jml;
        }
        else{
            $jml_non = $jml_non + $jml;
        }
    }

    if($jml_pk < $jmlPkMin){
?>
        <script type="text/javascript">
            tampilkanPesan('0','Jumlah pertanyaan khusus PK tidak memadai (hanya <?php echo $jml_pk; ?> dari minimal <?php echo $jmlPkMin; ?>.');
        </script>
<?php        
        exit();
    }
    else if($jml_non < $jmlNon){
?>
        <script type="text/javascript">
            tampilkanPesan('0','Jumlah pertanyaan selain PK tidak memadai (hanya <?php echo $jml_non; ?> dari minimal <?php echo $jmlNon; ?>.');
        </script>
<?php        
        exit();
    }









    $tgl_aktif = date('Y-m-d');
    $waktu_aktif = date('H:i:s');

    $qP = "
                UPDATE
                    ujian_pelaksanaan

                SET
                    tgl_aktif = '$tgl_aktif',
                    waktu_aktif= '$waktu_aktif',
                    aktif = '1'
                    
                WHERE
                    id='$id'
        ";

    $eP = mysqli_query($conn, $qP);

    if($eP){
?>
        <script type="text/javascript">
            tampilkanPesan('1','Ujian berhasil diaktifkan.');
            $('#lastId').val('-');
            updateRowT();
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