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
            tampilkanPesan('0','Grade penilaian ujian tidak terdeteksi.');
        </script>
<?php        
        exit();
    }

    






    $mentah = saring($_POST['idData']);
    $pecah = explode('[pisah]', $mentah);

    $id = saring($pecah[0]);
    $id_pelaksanaan = saring($pecah[1]);
    $id_karyawan = $_SESSION['idPengguna'];
    $pada = date('Y-m-d');

    $q = "
            SELECT
                id_pelaksanaan,
                id_karyawan,
                susulan,
                susulan_tgl

            FROM
                ujian_pelaksanaan_target_karyawan

            WHERE
                id = '$id'
            AND
                hapus = '0'

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

    $id_pelaksanaan_db = $r['id_pelaksanaan'];
    $id_karyawan_db = $r['id_karyawan'];

    if($id_pelaksanaan!==$id_pelaksanaan_db || $id_karyawan!==$id_karyawan_db){
?>
        <script type="text/javascript">
            tampilkanPesan('0', 'Data tidak valid.');
        </script>
<?php        
        exit();        
    }

    $susulan = $r['susulan'];
    if($susulan=='1'){
        $susulan_tgl = $r['susulan_tgl'];
        if($susulan_tgl !== $pada){
?>
        <script type="text/javascript">
            tampilkanPesan('0', 'Bukan saatnya ujian susulan.');
        </script>
<?php        
        exit();               
        }
    }





    $q="
        SELECT 
            up.id_ujian,
            up.kkm,
            up.waktu,
            up.tanggal,
            up.aktif,
            up.kode

        FROM 
            ujian_pelaksanaan up

        WHERE
            up.id = '$id_pelaksanaan'
            
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
    $tanggal = $r['tanggal'];
    $aktif = $r['aktif'];
    if($susulan=='0'){
        if($pada !== $tanggal || $aktif == '0'){
?>
        <script type="text/javascript">
            tampilkanPesan('0', 'Bukan saatnya ujian.');
        </script>
<?php        
            exit();            
        }
    }

    $id_ujian = $r['id_ujian'];
    $kode = $r['kode'];
    $kkm = $r['kkm'];
    $waktu = $r['waktu'];
    $mulai = date('H:i:s');
    $selesai = '';






    $arQAwal = array();

    //using pick random from bank
    if($id_ujian=='5483-8ABF1C'){
        //ujian nasional
        $jmlPkMin = 20;
        $jmlNon = 30;
    }
    else{
        $jmlPkMin = 8;
        $jmlNon = 12;
    }

    $cP = $jmlPkMin + $jmlNon;

    $qPK = "
            SELECT 
                p.id
                
            FROM 
                pertanyaan p

            LEFT JOIN
                materi_kelompok mk
            ON
                mk.id = p.id_kelompok

            WHERE
                p.hapus = '0'
            AND
                p.isi != ''
            AND
                mk.hapus = '0'
            AND
                mk.n_pk = '1'

            ORDER BY
                RAND()

            LIMIT
                $jmlPkMin
    ";
    $ePK = mysqli_query($conn, $qPK);
    $cPK = mysqli_num_rows($ePK);

    if($cPK !== $jmlPkMin){
?>
        <script type="text/javascript">
            tampilkanPesan('0','Ketersediaan soal Product Knowledge tidak sesuai.');
        </script>
<?php        
        exit();            
    }

    while ($rPK = mysqli_fetch_assoc($ePK)){
        $id_p = $rPK['id'];
        array_push($arQAwal, $id_p);
    }





    $qNon = "
            SELECT 
                p.id
                
            FROM 
                pertanyaan p

            LEFT JOIN
                materi_kelompok mk
            ON
                mk.id = p.id_kelompok

            WHERE
                p.hapus = '0'
            AND
                p.isi != ''
            AND
                mk.hapus = '0'
            AND
                mk.n_pk = '0'

            ORDER BY
                RAND()

            LIMIT
                $jmlNon
    ";

    $eNon = mysqli_query($conn, $qNon);
    $cNOn = mysqli_num_rows($eNon);

    if($cNOn!==$jmlNon){
?>
        <script type="text/javascript">
            tampilkanPesan('0','Ketersediaan soal Non Product Knowledge tidak sesuai.');
        </script>
<?php        
        exit();            
    }

    while ($rNon = mysqli_fetch_assoc($eNon)){
        $id_p = $rNon['id'];
        array_push($arQAwal, $id_p);
    }

    





    $q = "
            INSERT INTO 
                karyawan_ujian
                    (
                        id, 
                        id_pelaksanaan, 
                        id_karyawan, 
                        tanggal, 
                        mulai
                    ) 
            VALUES 
                    (
                        '$id',
                        '$id_pelaksanaan',
                        '$id_karyawan',
                        '$pada',
                        '$mulai'
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









    $arQRandom = array();
    for ($i=0; $i < $cP; $i++) {
        $rand = array_rand($arQAwal);
        $idCalon = $arQAwal[$rand];
        if(!in_array($idCalon, $arQRandom)){
            array_push($arQRandom, $idCalon);
        }
        else{
            $i = $i-1;
        }
    }





    $q = "
            INSERT INTO 
                karyawan_ujian_pengerjaan
                    (
                        id, 
                        id_karyawan, 
                        id_pengerjaan, 
                        id_pertanyaan,
                        no,
                        last
                    ) 
            VALUES 
    ";

    $no = 0;
    foreach ($arQRandom as $idPertanyaan) {
        $no = $no + 1;
        if($no=='1'){
            $last = '1';
        }
        else{
            $last = '0';
        }
        $idB = UUIDBaru();
        $q .= "
                    (
                        '$idB',
                        '$id_karyawan',
                        '$id',
                        '$idPertanyaan',
                        '$no',
                        '$last'
                    )
        ";
        if($cP > 1){
            if($no < $cP){
                $q .= ",";
            }
        }
    }

    $e = mysqli_query($conn, $q);
    if(!$e){
?>
        <script type="text/javascript">
            tampilkanPesan('0', 'Terjadi kesalahan saat memproses random pertanyaan.');
        </script>
<?php        
        exit();
    }





    $q = "
            SELECT 
                kup.id, 
                kup.id_pertanyaan, 
                kup.id_jawaban idTerjawab,
                kup.no,

                p.isi pertanyaan,

                j.id idJawaban,
                j.isi jawaban

            FROM 
                karyawan_ujian_pengerjaan kup
                
            LEFT JOIN
                pertanyaan p
            ON
                kup.id_pertanyaan = p.id

            RIGHT JOIN
                jawaban j
            ON
                j.id_pertanyaan = kup.id_pertanyaan

            WHERE
                kup.id_pengerjaan = '$id'

            ORDER BY
                kup.no ASC,
                j.id ASC
    ";
    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);

    //creat global array
    $r = array();
    $ar = array();

    while ($d = mysqli_fetch_assoc($e)) {
        $idPengerjaanSub = $d['id'];
        $idPertanyaan = $d['id_pertanyaan'];
        $idTerjawab = $d['idTerjawab'];
        $pertanyaan = $d['pertanyaan'];

        $idJawaban = $d['idJawaban'];
        $jawaban = $d['jawaban'];


        //input value  for gllobal array
        $r['idPengerjaanSub'] = $idPengerjaanSub;
        $r['idPertanyaan'] = $idPertanyaan;
        $r['idTerjawab'] = $idTerjawab;
        $r['pertanyaan'] = $pertanyaan;
        $r['idJawaban'] = $idJawaban;
        $r['jawaban'] = $jawaban;

        $ar[] = $r;
    }

    //max count item for global array
    $car = $c-1;

    //create indepeendent array
    $arIdPengerjaanSub = array();
    $arIdPertanyaan = array();
    $arPertanyaan = array();
    $arIdTerjawab = array();

    //the numbers of the questions (at the final)
    $numb = 0;

    for ($i=0; $i <= $car; $i++) {
        $idPengerjaanSub = $ar[$i]['idPengerjaanSub'];
        $idPertanyaan = $ar[$i]['idPertanyaan'];
        $pertanyaan = $ar[$i]['pertanyaan'];

        $idTerjawab = $ar[$i]['idTerjawab'];
        if($idTerjawab==''){
            $idTerjawab = '0';
        }

        $idJawaban = $ar[$i]['idJawaban'];
        $jawaban = $ar[$i]['jawaban'];

        if($i=='0'){
            //first data of all important should be import to array
            array_push($arIdPengerjaanSub, $idPengerjaanSub);
            array_push($arIdPertanyaan, $idPertanyaan);
            array_push($arPertanyaan, $pertanyaan);
            array_push($arIdTerjawab, $idTerjawab);

            //create array for string id question asc
            $idAns = 'idQuest'.$numb;
            $$idAns = array();
            array_push($$idAns, $idJawaban);

            //create array for storing question based on id question asc
            $ans = 'answe'.$numb;
            $$ans = array();
            array_push($$ans, $jawaban);
        }
        else{
            $x = $i-1;
            $idPengerjaanSubPrev = $ar[$x]['idPengerjaanSub'];
            if($idPengerjaanSub!==$idPengerjaanSubPrev){
                //next data if not the same with previoue should be import to array
                array_push($arIdPengerjaanSub, $idPengerjaanSub);
                array_push($arIdPertanyaan, $idPertanyaan);
                array_push($arPertanyaan, $pertanyaan);
                array_push($arIdTerjawab, $idTerjawab);

                //increae the number indicate how many the questions
                $numb = $numb+1;
                //initialize the new (increase number) id answers array
                $idAns = 'idQuest'.$numb;
                $$idAns = array();
                //insert id of the answers to array
                array_push($$idAns, $idJawaban);

                //initialize the new (increase number) answers array
                $ans = 'answe'.$numb;
                $$ans = array();
                array_push($$ans, $jawaban);
            }
            else{
                //insert id and answers to array
                array_push($$idAns, $idJawaban);
                array_push($$ans, $jawaban);
            }
        }
    }


    //chec how many the id of the question collected
    //echo 'ID pertanyaan terkumpul :'.$numb.'<br><br>';

    for ($i=0; $i <= $numb; $i++) {
        //helper
        $idQue = 'idQuest'.$i;
        $answ = 'answe'.$i;

        //create array for random id answers
        $idQRand = 'arIdJawaban'.$i;
        $$idQRand = array();

        //create array for answers based on random id answers array
        $aRand = 'arJawaban'.$i;
        $$aRand = array();

        for ($x=0; $x <= 2; $x++) {
            //random from already exist array of id answers
            $rand = array_rand($$idQue);
            $calon = ${$idQue}[$rand];
            if(!in_array($calon, $$idQRand)){
                //insert selected id answers and answers to array random id answers and answers
                array_push($$idQRand, $calon);
                $ansForAr = ${$answ}[$rand];
                array_push($$aRand, $ansForAr);
            }
            else{
                $x = $x-1;
            }
        }
    }

    //insert fix data to DB
    $q = "
            INSERT INTO 
                karyawan_ujian_pengerjaan_daftar_jawaban
                    (
                        id, 
                        id_pengerjaan, 
                        id_pertanyaan, 
                        id_jawaban, 
                        no
                    ) 
            VALUES 
    ";

    $inputed = 0;
    for ($z=0; $z <= $numb; $z++) {
        $idPertanyaan = $arIdPertanyaan[$z];
        $aRand = 'arIdJawaban'.$z;
        for ($y=0; $y <= 2; $y++) {
            $idBaru = UUIDBaru();
            $idJawaban = ${$aRand}[$y];
            $no = $y + 1;
            $q .= "
                    (
                        '$idBaru',
                        '$id',
                        '$idPertanyaan',
                        '$idJawaban',
                        '$no'
                    )
            ";
            if($inputed < $car){
                $q .= ",";
            }
            $inputed = $inputed+1;
        }
    }

    $e = mysqli_query($conn, $q);
    if(!$e){
?>
        <script type="text/javascript">
            tampilkanPesan('0', 'Terjadi kesalahan saat memproses random jawaban.');
        </script>
<?php        
        exit();
    }












    $akhir = date('H:i:s', strtotime("+".$waktu." minutes", strtotime($mulai)));

    $_SESSION['test'] = '1';
    $_SESSION['id_pengerjaan'] = $id;
    $_SESSION['id_pelaksanaan'] = $id_pelaksanaan;
    $_SESSION['id_ujian'] = $id_ujian;
    $_SESSION['kkm'] = $kkm;
    $_SESSION['akhir'] = $akhir;
    $_SESSION['kode'] = $kode;
?>

<script type="text/javascript">
    tampilkanPesan('1', 'Sesi ujian berhasil dibuat. Laman akan dimuat ulang.');
    setTimeout(function () {
        window.location.reload(true);
    }, 1000);
</script>