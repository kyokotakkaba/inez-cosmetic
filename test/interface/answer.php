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
            tampilkanPesan('0', 'Sudah ada tes yang berlangsung.');
        </script>
<?php        
        exit();
    }



    if(empty($_POST['idJ']) || empty($_POST['idP']) || empty($_POST['idS']) || empty($_POST['view'])){
        echo "NOT PERMITED!!";
        exit();
    }

    $id_pengerjaan = $_SESSION['id_pengerjaan'];
    $id_karyawan = $_SESSION['idPengguna'];
    $id_ujian = $_SESSION['id_ujian'];
    $kkm = $_SESSION['kkm'];

    if($id_ujian=='5483-8ABF1C'){
        //unas
        $jmlSoal = 50;
    }
    else{
        $jmlSoal = 20;
    }



    $id_pengerjaan_sub = saring($_POST['idS']);
    $id_pertanyaan = saring($_POST['idP']);
    $id_jawaban = saring($_POST['idJ']);
    $jam = date('H:i:s');

    
    $q = "
            SELECT 
                id

            FROM 
                jawaban 

            WHERE
                id_pertanyaan = '$id_pertanyaan'
            AND
                benar = '1'

            LIMIT
                1
    ";
    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);
    if($c == '0'){
?>
        <script type="text/javascript">
            tampilkanPesan('0', 'Kunci jawaban tidak ditemukan. Silahkan hubungi superadmin.');
        </script>
<?php        
        exit();
    }

    $r = mysqli_fetch_assoc($e);
    $id_jawaban_db = $r['id'];

    $benar = '0';
    $nilai = '0';
    
    if($id_jawaban_db == $id_jawaban){
        $benar = '1';
        $nilai = 100 / $jmlSoal;
    }



    $q = "
            SELECT 
                p.id,

                mk.n_pk

            FROM 
                pertanyaan p

            LEFT JOIN
                materi_kelompok mk
            ON
                mk.id = p.id_kelompok

            WHERE
                p.id = '$id_pertanyaan'
    ";
    $e = mysqli_query($conn, $q);
    $r = mysqli_fetch_assoc($e);

    $n_pk = $r['n_pk'];
    $nilai_pk = '0';

    if($n_pk == '1'){
        if($benar == '1'){
            $nilai_pk = $nilai;
            $nilai = '0';
        }
    }


    //reset last
    $q = "
            UPDATE 
                karyawan_ujian_pengerjaan

            SET
                last = '0'

            WHERE
                id_pengerjaan = '$id_pengerjaan'
    ";
    $e = mysqli_query($conn, $q);



    $q = "
            UPDATE 
                karyawan_ujian_pengerjaan 

            SET 
                id_jawaban = '$id_jawaban',
                jam = '$jam',
                benar = '$benar',
                nilai = '$nilai',
                nilai_pk = '$nilai_pk',
                last = '1'

            WHERE
                id = '$id_pengerjaan_sub'
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





    $konversi = array();
    $q = "
            SELECT 
                id, 
                huruf, 
                min, 
                max 

            FROM 
                ujian_grade 

            WHERE
                hapus = '0'

            ORDER BY
                min ASC
    ";
    $e = mysqli_query($conn, $q);
    while($d = mysqli_fetch_assoc($e)){
        $konversi[] = array(
            'min' => $d['min'],
            'max' => $d['max'],
            'huruf' => $d['huruf']
        );
    }


    //get total nilai and nilai_pk
    $q = "
            SELECT 
                SUM(nilai) nilai,
                SUM(nilai_pk) nilai_pk

            FROM 
                karyawan_ujian_pengerjaan 

            WHERE
                id_pengerjaan = '$id_pengerjaan'
    ";
    
    $e = mysqli_query($conn, $q);
    $r = mysqli_fetch_assoc($e);

    $n = $r['nilai'];
    $n_pk = $r['nilai_pk'];
    $nilai_akhir = $n + $n_pk;

    $remidi = '1';
    $remidi_karena = 'Nilai akhir kurang dari nilai minimal lulus ('.$kkm.')';

    if($nilai_akhir >= $kkm){
        $remidi = '0';
        $remidi_karena = '';
    }

    $terdeteksi = 0;
    $nilai_grade = 'C';

    //get the grade
    foreach($konversi as $value){
        $min = $value['min'];
        $max = $value['max'];
        $huruf = $value['huruf'];
        $nilai_final = round($nilai_akhir);
        if($nilai_final >= $min && $nilai_final <= $max){
            $terdeteksi = '1';
            $nilai_grade = $huruf;
            break;
        }
    }




    $q = "
            UPDATE 
                karyawan_ujian 

            SET 
                last_time = '$jam',
                nilai_akhir = '$nilai_akhir',
                n = '$n',
                n_pk = '$n_pk',
                nilai_grade = '$nilai_grade',
                remidi = '$remidi',
                remidi_karena = '$remidi_karena'

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



?>




<script type="text/javascript">
    tampilkanPesan('1', 'Jawaban tersimpan.');
    $('.btnOpsi<?php echo $id_pertanyaan; ?>').removeClass('blue');
    $('.btnOpsi<?php echo $id_pertanyaan; ?>').removeClass('active');
    $('#btn<?php echo $id_jawaban; ?>').addClass('blue active');

    jawabanTersimpan('<?php echo $id_pertanyaan; ?>', '<?php echo $id_jawaban; ?>');
</script>