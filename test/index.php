<?php
    session_start();

    $fromHome = '../';

    if(empty($_SESSION['idPengguna'])){
        header('location: '.$fromHome);
        exit();
    }

    $allowed = array(
        'admin',
        'user'
    );

    $jenisPengguna = $_SESSION['jenisPengguna'];
    if(!in_array($jenisPengguna, $allowed)){
        header('location: '.$fromHome.''.$jenisPengguna);
        exit();
    }

    $idPengguna = $_SESSION['idPengguna'];

    require_once $fromHome.'lib/core/head.php';
    $iniHari = date('Y-m-d');
   
    if(empty($_SESSION['test'])){
        if(empty($_GET['c'])){
            header('location: '.$fromHome);
            exit();
        }

        $kode = saring($_GET['c']);

        $q = "
            SELECT
                uptk.id idData,
                uptk.id_pelaksanaan,
                uptk.susulan,
                uptk.susulan_tgl,

                up.tanggal,
                up.kkm,
                up.waktu,
                up.id_ujian,


                u.nama namaUjian,

                ku.id id_pengerjaan,
                ku.mulai,
                ku.selesai

            FROM
                ujian_pelaksanaan_target_karyawan uptk

            LEFT JOIN
                ujian_pelaksanaan up
            ON
                uptk.id_pelaksanaan = up.id

            LEFT JOIN
                ujian u
            ON
                up.id_ujian = u.id


            LEFT JOIN
                karyawan_ujian ku
            ON
                ku.id_pelaksanaan = uptk.id_pelaksanaan
            AND
                ku.id_karyawan = '$idPengguna'
            AND
                ku.hapus = '0'

            WHERE
                uptk.id_karyawan = '$idPengguna'
            AND
                uptk.hapus = '0'
            AND 
                up.aktif = '1'
            AND
                up.kode = '$kode'
            
            LIMIT
                1
        ";
        $e = mysqli_query($conn, $q);
        $c = mysqli_num_rows($e);
        $sub = 'Online Test';
    }
    else {
        $id_pelaksanaan = $_SESSION['id_pelaksanaan'];
        $id_pengerjaan = $_SESSION['id_pengerjaan'];
        $akhir = $_SESSION['akhir'];
        $kode = $_SESSION['kode'];

        $id_ujian = $_SESSION['id_ujian'];

        if($id_ujian=='5483-8ABF1C'){
            //unas
            $jmlSoal = 50;
        }
        else{
            $jmlSoal = 20;
        }

        $sub = 'Online Test';
    }
?>
    

    <style type="text/css">
        #desktopMenu {
            margin: 0px;
        }

        #desktopMenu, #mobileMenuTrigger, #goTop {
            box-shadow: 0px 0px 9px 3px rgba(41, 41, 41, .25);
        }

        .table .button, .table .label {
            margin-bottom: 8px;
        }

        #mainContainer {
            padding: 0px;
            margin-top: 58px;
        }

        @media only screen and (max-width: 480px){

            #sideMenu {
                display: none;
            }

            #loaderSide {
                display: block;
                width: 100%;
                margin: 8px 14px 0px 14px;
                padding: 0px;
            }

            #desktopMenu {
                display: none;
            }
        }

        #footer {
            margin-top: 50px;
        }

        @media only screen and (min-width: 481px){
            #mainContainer {
                margin-top: 80px;
            }
        }

        #deskProfilePicture {
            background-color: #FFFFF7;
            background-image: url(<?php echo $profilePicture; ?>);
            background-size: cover;
            background-position: center;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            margin: 0px auto;
            border: #D4D4D4 solid 1px;
        }

        .opsiNumb {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            justify-content: center;
            align-items: flex-start;
            align-content: flex-start;
            padding-bottom: 6px;
        }

        .opsiNumb .button {
            width: 34px;
            height: 34px;
            margin: 4px;
        }




        .bgPushMenu{
            display: none;
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0px;
            left: 0px;
            background-color: rgba(0,0,0,0.8);
            z-index: 102;
        }

        .pushMenu {
            display: none;
            <?php echo $bgMenu; ?>;
            padding: 14px 0px 14px 45px;
            top: 0px;
            left: 0px;
            width: 71%;
            height: 100%;
            margin-top: 0px;
            margin-left: -34px;
            max-width: 340px;
            min-width: 290px;
            position: fixed;
            z-index: 103;
            color: white;
        }

        #pushMenu, #pushMenu .item {
            color: white;
        }

        #mobileProfilePicture {
            background-image: url(<?php echo $profilePicture; ?>); 
            background-size: cover; 
            background-position: center; 
            width: 56px; 
            height: 56px; 
            border-radius: 50%; 
            margin: -10px auto;
        }

        @media only screen and (min-width: 481px){
            #mobileMenuTrigger {
                display: none;
            }
        }
    </style>

    <div id="desktopMenu" class="ui fixed inverted menu" style="<?php echo $bgHeader; ?>">
        <div class="ui container">
            <div class="item">
                <img class="logo" src="<?php echo $icon; ?>"> &nbsp; <?php echo ucfirst($jenisPengguna); ?> Online Test
            </div>
            
            <div class="right menu">
                <div class="item">
                    <img class="ui mini circular image" src="<?php echo $profilePicture; ?>">
                </div>
                <div class="ui dropdown item">
                    <?php echo $namaPengguna; ?>
                    <i class="dropdown icon"></i>
                    <div class="menu">
                        <a class="item" href="<?php echo $fromHome.''.$jenisPengguna; ?>">
                            <i class="user circle icon"></i> Panel <?php echo ucwords($jenisPengguna) ?>
                        </a>
                        <div class="link item" onclick="tampilkanKonfirmasi('1','Logout','Yakin ingin keluar ?','<?php echo $fromHome; ?>interface/logout.php')">
                            <i class="logout icon"></i> Logout
                        </div>
                    </div>                    
                </div>
            </div>
        </div>
    </div>

        <div id="mainContainer" class="ui basic segment container">
<?php
    if(empty($_SESSION['test'])){
        if($c=='0'){
?>
            <div class="ui icon floating message">
                <i class="inbox icon"></i>
                <div class="content">
                    <div class="header">
                        KOSONG
                    </div>
                    <p>Tes tidak ditemukan.</p>
                </div>
            </div>
            <div class="ui vertical basic segment center aligned">
                <p style="font-size: 9pt; color: #60646D;;">
                    Kembali ke panel <?php echo $jenisPengguna; ?> ? klik <a href="<?php echo $fromHome.''.$jenisPengguna; ?>">di sini</a>
                </p>
            </div>
<?php        
        }
        else{
            $r = mysqli_fetch_assoc($e);

            $idKepesertaan = $r['idData'];
            $id_pelaksanaan = $r['id_pelaksanaan'];
            $susulan = $r['susulan'];
            if($susulan == '1'){
                $susulan_tgl = $r['susulan_tgl'];
            }

            $tanggal = $r['tanggal'];
            $namaUjian = $r['namaUjian'];
            $id_ujian = $r['id_ujian'];

            if($id_ujian=='5483-8ABF1C'){
                //unas
                $jmlSoal = 50;
            }
            else{
                $jmlSoal = 20;
            }

            $kkm = $r['kkm'];
            $waktu = $r['waktu'];

            $id_pengerjaan = $r['id_pengerjaan'];
            if(empty($id_pengerjaan)){
                $id_pengerjaan = '-';
            }

            $mulai = $r['mulai'];
            if(empty($mulai)){
                $mulai = '-';
            }
            $selesai = $r['selesai'];
            if(empty($selesai)){
                $selesai = '-';
            }

            $q = "
                SELECT 
                    k.nik, 
                    k.nama, 
                    k.jk, 
                    k.tmpt_lahir,
                    k.tgl_lahir, 
                    k.email,
                    k.hp,
                    k.alamat,
                    k.foto,
                    k.tgl_masuk,

                    tb.nama tingkatBelajar,

                    a.jenis,

                    w.nama nama_wil,

                    wsw.nama nama_wil_sup

                FROM 
                    karyawan k

                LEFT JOIN
                    tingkat_belajar tb
                ON
                    tb.id = k.tingkat

                LEFT JOIN
                    akun a
                ON
                    a.id_pengguna = k.id

                LEFT JOIN
                    wilayah w
                ON
                    k.id_wil = w.id
                AND
                    w.hapus = '0'

                LEFT JOIN
                    wilayah_supervisi ws
                ON
                    ws.id_karyawan = k.id
                AND
                    ws.hapus = '0'

                LEFT JOIN
                    wilayah wsw
                ON
                    ws.id_wilayah = wsw.id
                AND
                    wsw.hapus = '0'

                WHERE
                    k.id = '$idPengguna'
                AND
                    k.hapus = '0'

                LIMIT
                    1
            ";
            $e = mysqli_query($conn, $q);
            $c = mysqli_num_rows($e);
            if($c=='0'){
?>
                <div class="ui icon floating message">
                    <i class="exclamation triangle icon"></i>
                    <div class="content">
                        <div class="header">
                            NULL
                        </div>
                        <p>Data karyawan tidak ditemukan.</p>
                    </div>
                </div>
                <div class="ui vertical basic segment center aligned">
                    <p style="font-size: 9pt; color: #60646D;;">
                        Kembali ke panel <?php echo $jenisPengguna; ?> ? klik <a href="<?php echo $fromHome.''.$jenisPengguna; ?>">di sini</a>
                    </p>
                </div>
<?php                
            }
            else{
                $r = mysqli_fetch_assoc($e);
                        
                $nik = $r['nik'];
                $nama = $r['nama'];
                
                $jk = strtolower($r['jk']);
                if($jk!=='l'&&$jk!=='p'){
                    $jk = 'n';
                }
                $infoJk = '-';
                if($jk == 'l'){
                    $infoJk == 'Laki-laki';
                }
                else if($jk == 'p'){
                    $infoJk = 'Perempuan';
                }

                $tgl_lahir = $r['tgl_lahir'];
                $infoUmur = '-';
                if(!empty($tgl_lahir) && $tgl_lahir !== '' && $tgl_lahir !== '0000-00-00'){
                    $infoUmur = hitungUmur($tgl_lahir);
                    if($iniHari == $tgl_lahir){
                        $infoUmur .= '<span class="ui pink label">ULTAH!</span>';
                    }
                }
                else{
                    $tgl_lahir = '';
                }
                
                $jenis = $r['jenis'];

                $nama_wil = $r['nama_wil'];
                $nama_wil_sup = $r['nama_wil_sup'];
                $infoTingkat = $jenis;
                if(!empty($nama_wil_sup)){
                    if($nama_wil_sup!==$nama_wil){
                        $infoTingkat .= ' - '.$nama_wil_sup;
                    }
                }

                if($nama_wil==''){
                    $nama_wil = '-';
                }
                
                $foto = $r['foto'];
                $avatar = '../files/photo/'.$jk.'.png';
                if(!empty($foto) && $foto !== ''){
                    $foto = str_replace('%20', ' ', $foto);
                    if(file_exists('../../'.$foto)){
                        $avatar = '../'.$foto;
                    }
                }          

                $tgl_masuk = $r['tgl_masuk'];
                $lamaKerja = '-';
                if(!empty($tgl_masuk) && $tgl_masuk !== '' && $tgl_masuk !== '0000-00-00'){
                    $lamaKerja = hitungUmur($tgl_masuk);
                }

                $tingkatBelajar = $r['tingkatBelajar'];
                $tmpt_lahir = $r['tmpt_lahir'];
                $alamat = $r['alamat'];
                $email = $r['email'];
                $hp = $r['hp'];
            }
?>
            <div id="mainLoader" class="ui vertical basic segment loaderArea">
                <table class="ui table">
                    <thead>
                        <tr>
                            <th colspan="2">Deskripsi Ujian</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td width="26%">Kode</td>
                            <td>
                                <strong><?php echo $kode; ?></strong>
                            </td>
                        </tr>
                        <tr>
                            <td>Judul</td>
                            <td><?php echo $namaUjian; ?></td>
                        </tr>
<?php
            if($susulan == '1'){
?>
                        <tr>
                            <td></td>
                            <td><div class="ui black label">SUSULAN</div></td>
                        </tr>
<?php                
            }
?>                        
                        <tr>
                            <td>Nilai Minimal Lulus</td>
                            <td><?php echo $kkm; ?></td>
                        </tr>
                        <tr>
                            <td>Waktu</td>
                            <td><?php echo $waktu.' (menit)'; ?></td>
                        </tr>
                        <tr>
                            <td>Jumlah Soal</td>
                            <td><?php echo $jmlSoal; ?></td>
                        </tr>
<?php
            if($susulan == '0' && $iniHari !== $tanggal){
?>
                        <tr>
                            <td>
                                Ujian dilaksanakan pada <strong><?php echo tanggalKan($tanggal); ?></strong>
                            </td>
                        </tr>
<?php                
            }

            if($susulan == '1' && $iniHari !== $susulan_tgl){
?>
                        <tr>
                            <td>
                                Ujian susulan dilaksanakan pada <strong><?php echo tanggalKan($tanggal); ?></strong>
                            </td>
                        </tr>
<?php                
            }
?>                        
                    </tbody>
                </table>
<?php
            if($c=='1'){
?>
                <div class="ui hidden divider"></div>
                <table class="ui table">
                    <thead>
                        <tr>
                            <th colspan="2">Info Karyawan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="2">
                                <img id="prevGambar" src="<?php echo $avatar; ?>" class="ui centered image small rounded">
                            </td>
                        </tr>
                        <tr>
                            <td width="26%">NIK</td>
                            <td>
                                <strong><?php echo $nik; ?></strong>
                            </td>
                        </tr>
                        <tr>
                            <td>Nama</td>
                            <td><?php echo $nama; ?></td>
                        </tr>
                        <tr>
                            <td>Jenis Kelamin</td>
                            <td><?php echo $infoJk; ?></td>
                        </tr>
                        <tr>
                            <td>Tempat dan tanggal lahir</td>
                            <td><?php echo $tmpt_lahir.', '.tanggalKan($tgl_lahir); ?></td>
                        </tr>
                    </tbody>
                </table>

                <div class="ui vertical basic segment center aligned">
<?php
                if($mulai=='-'){
?>
                            <div class="ui primary button" onclick="tampilkanKonfirmasi('<?php echo $idKepesertaan; ?>[pisah]<?php echo $id_pelaksanaan; ?>', 'Mulai Ujian', 'Yakin ingin memulai ujian ?', 'interface/start.php')">
                                <i class="stopwatch icon"></i> Mulai Ujian
                            </div>
<?php        
                }
                else{
                    if($selesai=='-'){
                        if($id_pengerjaan=='-'){
?>
                            <i class="exclamation triangle icon"></i> Terjadi kesalahan. Mohon reset pengerjaan tes ke pihak admin akademik
<?php                
                        }
                        else{
?>
                            <div class="ui primary button" onclick="tampilkanKonfirmasi('<?php echo $id_pengerjaan; ?>', 'Lanjutkan Ujian', 'Yakin ingin melanjutkan ujian ?', 'interface/continue.php')">
                                <i class="stopwatch icon"></i> Lanjutkan Ujian
                            </div>
<?php            
                        }
                    }
                    else{
?>
                            <i class="smile icon"></i> Tes selesai
<?php            
                    }
                }
?>                                                
                    
                    <p style="font-size: 9pt; color: #60646D; margin-top: 30px;">
                        Kembali ke panel <?php echo $jenisPengguna; ?> ? klik <a href="<?php echo $fromHome.''.$jenisPengguna; ?>">di sini</a>
                    </p>
                </div>
<?php                
            }
?>
            </div>
<?php            
        }
    }
    else{
        //get data from DB
        $jmlTerjawab = 0;
        $q = "
                SELECT 
                    kup.id, 
                    kup.id_pertanyaan, 
                    kup.id_jawaban idTerjawab,
                    kup.last,

                    p.isi pertanyaan,

                    kdj.id_jawaban idJawaban,

                    j.isi jawaban

                FROM 
                    karyawan_ujian_pengerjaan kup

                LEFT JOIN
                    pertanyaan p
                ON
                    kup.id_pertanyaan = p.id

                RIGHT JOIN
                    karyawan_ujian_pengerjaan_daftar_jawaban kdj
                ON
                    kdj.id_pertanyaan = kup.id_pertanyaan

                LEFT JOIN 
                    jawaban j
                ON
                    kdj.id_jawaban = j.id

                WHERE
                    kup.id_pengerjaan = '$id_pengerjaan'
                AND
                    kdj.id_pengerjaan = '$id_pengerjaan'

                ORDER BY
                    kup.no ASC,
                    kdj.no ASC
        ";

        $e = mysqli_query($conn, $q);
        $c = mysqli_num_rows($e);

        if($c > 0){
            //creat global array
            $r = array();
            $ar = array();

            while ($d = mysqli_fetch_assoc($e)) {
                $idPengerjaanSub = $d['id'];
                $idPertanyaan = $d['id_pertanyaan'];
                $idTerjawab = $d['idTerjawab'];
                $pertanyaan = $d['pertanyaan'];
                $last = $d['last'];

                $idJawaban = $d['idJawaban'];
                $jawaban = $d['jawaban'];


                //input value  for gllobal array
                $r['idPengerjaanSub'] = $idPengerjaanSub;
                $r['idPertanyaan'] = $idPertanyaan;
                $r['last'] = $last;
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
            $arLast = array();

            //the numbers of the questions (at the final)
            $numb = 0;

            for ($i=0; $i <= $car; $i++) {
                $idPengerjaanSub = $ar[$i]['idPengerjaanSub'];
                $idPertanyaan = $ar[$i]['idPertanyaan'];
                $pertanyaan = $ar[$i]['pertanyaan'];
                $last = $ar[$i]['last'];

                $idTerjawab = $ar[$i]['idTerjawab'];
                if($idTerjawab =='' || empty($idTerjawab)){
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
                    array_push($arLast, $last);

                    //create array for string id question asc
                    $idAns = 'arIdJawaban'.$numb;
                    $$idAns = array();
                    array_push($$idAns, $idJawaban);

                    //create array for storing question based on id question asc
                    $ans = 'arJawaban'.$numb;
                    $$ans = array();
                    array_push($$ans, $jawaban);

                    if($idTerjawab !== '0'){
                        $jmlTerjawab = $jmlTerjawab + 1;
                    }
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
                        array_push($arLast, $last);

                        //increae the number indicate how many the questions
                        $numb = $numb+1;
                        //initialize the new (increase number) id answers array
                        $idAns = 'arIdJawaban'.$numb;
                        $$idAns = array();
                        //insert id of the answers to array
                        array_push($$idAns, $idJawaban);

                        //initialize the new (increase number) answers array
                        $ans = 'arJawaban'.$numb;
                        $$ans = array();
                        array_push($$ans, $jawaban);

                        if($idTerjawab !== '0'){
                            $jmlTerjawab = $jmlTerjawab + 1;
                        }
                    }
                    else{
                        //insert id and answers to array
                        array_push($$idAns, $idJawaban);
                        array_push($$ans, $jawaban);
                    }
                }
            }
        }
?>

        <input type="hidden" id="akhir" name="akhir" value="<?php echo $akhir; ?>">
        <input type="hidden" id="jmlTerjawab" name="jmlTerjawab" value="<?php echo $jmlTerjawab; ?>">
        <input type="hidden" id="jmlSoal" name="jmlSoal" value="<?php echo $jmlSoal; ?>">


        <div class="ui grid">
            <div id="mainRow" class="two column row">
                <div id="menuSide" class="four wide column">
                    <div id="sideMenu" class="ui vertical fluid inverted menu mainMenu" >
                        <div class="item">
                            <div id="deskProfilePicture"></div>
                            <div class="ui horizontal inverted divider">
                                <?php echo $namaPengguna; ?>
                            </div>
                        </div>
                        <div class="item" style="margin-top: -16px;">
                            <h2 class="ui horizontal divider inverted header infoWaktu">
                                [ WAKTU ]
                            </h2>
                        </div>
                        <div class="item">
                            Terjawab <span class="jmlTerjawab"><?php echo $jmlTerjawab; ?></span>
                        </div>
                        <div class="item">
                            Jumlah Soal <?php echo $jmlSoal; ?>
                        </div>
                        <div class="link item btnSelesai" style="display: none;" onclick="sudahi()">
                            SELESAI <i class="ban icon"></i>
                        </div>
                    </div>
                </div>

                <div id="loaderSide" class="twelve wide column">
                    <div class="ui form clearing">
                        <div class="ui segment loaderArea">
                            <div id="mainLoader" style="max-width: 100%">
                                
                                <div class="ui styled fluid accordion">
<?php
    //populate the answer and question display
    for ($i=0; $i <= $numb; $i++) {
        $idPengerjaanSub = $arIdPengerjaanSub[$i];
        $idPertanyaan = $arIdPertanyaan[$i];
        $pertanyaan = $arPertanyaan[$i];
        $last = $arLast[$i];

        $idTerjawab = $arIdTerjawab[$i];
        if($idTerjawab!=='0'&&$idTerjawab!==''){
            $classLabel = 'blue';
        }
        else{
            $classLabel = '';
        }

        $idJwb = 'arIdJawaban'.$i;
        $jwb = 'arJawaban'.$i;

        $no = $i+1;
        $classAct = '';
        if($last == '1'){
            $classAct = 'active';
        }
?>        
        
        <div class="<?php echo $classAct; ?> title">
            <i class="dropdown icon"></i> Pertanyaan No. <?php echo $no; ?>
            <div id="lbl<?php echo $idPertanyaan; ?>" class="ui lblStatus empty circular label <?php echo $classLabel; ?>" style="position: absolute; margin: 4px auto; right: 30px;"></div>
        </div>
        <div class="<?php echo $classAct; ?> content">
            
            <input type="hidden" id="idJawabSub<?php echo $idPertanyaan; ?>" value="<?php echo $idTerjawab; ?>">

            <?php echo html_entity_decode($pertanyaan); ?>

            <table class="ui very basic collapsing celled table unstackable">
                <tbody>
<?php
                $aSudah = 0;
                $bSudah = 0;
                $cSudah = 0;

                for ($z=0; $z <= 2; $z++) {
                    $idJ = ${$idJwb}[$z];
                    $jawaban = ${$jwb}[$z];
                    if($aSudah=='0'){
                        $teks = 'A';    
                        $aSudah = '1';
                    }
                    else if($bSudah=='0'){
                        $teks = 'B';
                        $bSudah = '1';
                    }
                    else if($cSudah=='0'){
                        $teks = 'C';
                        $cSudah = '1';
                    }

                    if($idJ == $idTerjawab){
                        $classBtnOpsi = 'ative primary';
                    }
                    else{
                        $classBtnOpsi = '';
                    }
?>
                    <tr>
                        <td width="4%">
                            <div id="btn<?php echo $idJ; ?>" class="ui icon button btnOpsi<?php echo $idPertanyaan; ?> <?php echo $classBtnOpsi; ?>" onclick="jawabSoal('<?php echo $idPengerjaanSub; ?>', '<?php echo $idPertanyaan; ?>', '<?php echo $idJ; ?>')">
                                <?php echo $teks; ?>
                            </div>
                        </td>
                        <td>
                            <?php echo $jawaban; ?>
                        </td>
                    </tr>
<?php           
                }
?>
                </tbody>
            </table>
        </div>
<?php
    }
?>        
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<?php        
    }
?>
            


        </div>
    
        <!-- Push Menu -->
        <div class="bgPushMenu" onclick="triggerPushMenu()">
            <!-- only for focus on menu -->
        </div>

        <!-- Push Menu -->
        <div class="pushMenu">
            <div id="pushMenu" class="ui vertical fluid secondary menu mainMenu">
                <div class="ui icon basic text right floated" onclick="triggerPushMenu()">
                    <i class="left arrow large icon"></i>
                </div>
                <div class="item">
                    <div id="mobileProfilePicture"></div>
                </div>
                <div class="ui horizontal divider" style="color: white;">
                    <?php echo $namaPengguna; ?>
                </div>
<?php
    if(empty($_SESSION['test'])){
?>
                <a class="item" href="<?php echo $fromHome.''.$jenisPengguna; ?>">
                    Panel <?php echo ucwords($jenisPengguna) ?> <i class="user circle icon"></i>
                </a>
                <div class="link item" onclick="tampilkanKonfirmasi('1','Logout','Yakin ingin keluar ?','<?php echo $fromHome; ?>interface/logout.php')">
                    <i class="logout icon"></i> Logout
                </div>
<?php        
    }
    else{
?>
                <div class="item">
                    <h2 class="ui horizontal divider inverted header infoWaktu">
                        [ WAKTU ]
                    </h2>
                </div>
                <div class="item">
                        Terjawab <span class="jmlTerjawab"><?php echo $jmlTerjawab; ?></span>
                    </div>
                    <div class="item">
                        Jumlah Soal <?php echo $jmlSoal; ?> 
                    </div>
                <div class="link item btnSelesai" style="display: none;" onclick="sudahi()">
                    SELESAI <i class="ban icon"></i>
                </div>
<?php        
    }
?>                    
            </div>
        </div>
        <!-- End Push Menu -->


        <div id="mobileMenuTrigger" class="ui fixed inverted secondary menu" style="<?php echo $bgHeader; ?> opacity: 0.96; padding: 3px 0px 3px 0px; font-size: 14pt;"> 
            <div class="ui container">
                <div class="item" style="padding: 0px 0px 0px 6px;">
                    <div class="ui icon basic inverted tiny button" style="padding: 8px;" onclick="triggerPushMenu()">
                        <i class="bars icon"></i>
                    </div>
                </div>
                <div class="item" style="padding-left: 0px;">
                    <?php echo $sub; ?>
                </div>
                <div class="right menu">
                    <div class="item active infoWaktu">
                        [ WAKTU ]
                    </div>
                </div>
            </div>
        </div>


<?php
        require_once $fromHome.'lib/core/snippet.php';
        require_once $fromHome.'lib/core/footer.php';
?>
        <script type="text/javascript" src="<?php echo $fromHome; ?>lib/jquery.backDetect.min.js"></script>
        <script type="text/javascript">

            //disable back button for redirect
            (function (global) {
                if (typeof (global) === "undefined") {
                    throw new Error("window is undefined");
                }

                var _hash = "!";
                var noBackPlease = function () {
                    global.location.href += "#";

                    global.setTimeout(function () {
                        global.location.href += "!";
                    }, 50);
                };

                global.onhashchange = function () {
                    if (global.location.hash !== _hash) {
                        global.location.hash = _hash;
                    }
                };

                global.onload = function () {
                    noBackPlease();
                    
                    document.body.onkeydown = function (e) {
                        var elm = e.target.nodeName.toLowerCase();
                        if (e.which === 8 && (elm !== 'input' && elm  !== 'textarea')) {
                            e.preventDefault();
                            trapBack();
                        }
                        e.stopPropagation();
                    };
                };
            })(window);


            respon();

            $(window).resize(
                function(){
                    respon();
                }
            );   


            function respon() {
                var panjang, mainRow, loaderSide, stateWide, stateTrigger, pushState;
                panjang = $("body").width();
                mainRow = $('#mainRow').attr('class');
                loaderSide = $('#loaderSide').attr('class');

                stateWide = $('#stateWide').val();

                stateTrigger = $('#stateTrigger').val();
                pushState = $('#pushState').val();

                if(panjang<481){
                    if(mainRow=='two column row'){
                        $('#mainRow').attr('class','row');
                    }

                    if(loaderSide=='twelve wide column'){
                        $('#loaderSide').attr('class','column');
                    }

                    if(stateTrigger=='0'){
                        $('#mobileMenuTrigger').transition('fade');
                        $('#stateTrigger').val('1');
                    }
                }
                else{
                    if(mainRow=='row'){
                        $('#mainRow').attr('class','two column row');
                    }

                    if(loaderSide=='column'){
                        $('#loaderSide').attr('class','twelve wide column');
                    }   

                    if(stateTrigger=='1'){
                        $('#mobileMenuTrigger').transition('fade');
                        $('#stateTrigger').val('0');
                    }
                    else if(stateTrigger=='-'){
                        if($('#mobileMenuTrigger').is(':visible')==true){
                            $('#mobileMenuTrigger').transition('fade');
                            $('#stateTrigger').val('0');
                        }
                    }

                    if(pushState=='1'){
                        triggerPushMenu();
                    }
                }
            }


            function triggerPushMenu(){
                var pushState;
                pushState = $('#pushState').val();

                if(pushState=="1"){
                    $('#pushState').val('0');
                }
                else if(pushState=="0"){
                    $('#pushState').val('1');
                }
                $('.bgPushMenu').transition('fade');
                $('.pushMenu').transition('fly right');
            }


            $('.dropdown').dropdown();



<?php
    if(!empty($_SESSION['test'])){
        $iniHari = str_replace('-', '/', $iniHari);
?>
            $('.accordion').accordion();

            runTimer();

            var akhir, countDownDate, x, vStop;

            function runTimer(){
                akhir = $('#akhir').val();
                countDownDate = new Date("<?php echo $iniHari; ?> "+akhir).getTime();

                vStop = $('.btnSelesai').is(':visible');

                x = setInterval(function() {
                    var asiaTime = new Date().toLocaleString("en-US", {timeZone: "Asia/Jakarta"}),
                    now = new Date(asiaTime).getTime(),
                    distance = countDownDate - now;
                    hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)),
                    minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60)),
                    seconds = Math.floor((distance % (1000 * 60)) / 1000);
                    isian = hours + " : " + minutes + " : " +seconds;

                    $(".infoWaktu").html(isian);

                    if(distance < 900000){
                        if(vStop==false){
                            $('.btnSelesai').show();
                        }
                    }
                
                    if (distance < 0) {
                        clearInterval(x);
                        stopTest();
                        $(".infoWaktu").html("SELESAI!!");
                    }

                }, 1000);
            }


            function sudahi(){
                var jmlSoal = parseInt($('#jmlSoal').val()),
                    jmlTerjawab = parseInt($('#jmlTerjawab').val());

                if(jmlTerjawab < jmlSoal){
                    tampilkanPesan('0', 'Anda belum menjawab semua pertanyaan.');
                }
                else {
                    tampilkanKonfirmasi('<?php echo $id_pengerjaan; ?>','Selesai','Yakin ingin mengakhiri tes?','interface/stop.php');
                }
            }



            function jawabSoal(idS, idP, idJ){
                $('.btnOpsi'+idP).addClass('loading');
                $.ajax({
                    type:"post",
                    async:true,
                    url:"interface/answer.php",
                    data:{
                        'view': '1',
                        'idS': idS,
                        'idP': idP,
                        'idJ': idJ
                    },
                    success:function(data){
                        $("#feedBack").html(data);
                        setTimeout(function () {
                            $('.btnOpsi'+idP).removeClass('loading');
                        }, 600);
                    }
                })
            }

            function stopTest(){
                loadingMulai();
                $.ajax({
                    type:"post",
                    async:true,
                    url:"interface/stop.php",
                    data:{
                        'view': '1',
                        'idData': '<?php echo $id_pengerjaan; ?>'
                    },
                    success:function(data){
                        $("#feedBack").html(data);
                    }
                })
            }

            function jawabanTersimpan(idP, idJ){
                var already = $('#idJawabSub'+idP).val(),
                    terjawab = $('#jmlTerjawab').val(),
                    newTerjawab = parseInt(terjawab) + 1;

                if(already == '0'){
                    $('#idJawabSub'+idP).val(idJ);
                    $('#jmlTerjawab').val(newTerjawab);
                    perbaruiJmlTerjawab();
                }

                $('#lbl'+idP).removeClass('blue');
                $('#lbl'+idP).addClass('blue');
            }

            function perbaruiJmlTerjawab(){
                var terjawab = $('#jmlTerjawab').val();
                $('.jmlTerjawab').html(terjawab);
            }
<?php        
    }
?>
            

        </script>
    </body>
</html>