<?php
    session_start();

    $fromHome = '../../';

    if(empty($_SESSION['idPengguna'])){
        header('location: '.$fromHome);
        exit();
    }

    if(!empty($_SESSION['test'])){
        header('location: ../');
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

    $ketemu = '0';
    if(!empty($_SESSION['id_pengerjaan'])){
    	//unset($_SESSION['id_pengerjaan']);
    	$id_pengerjaan = $_SESSION['id_pengerjaan'];
		$q = "
				SELECT 
					ku.tanggal tgl_megnerjakan, 
					ku.mulai, 
					ku.selesai, 
					ku.last_time, 
					ku.nilai_akhir, 
					ku.n, 
					ku.n_pk, 
					ku.nilai_grade, 
					ku.remidi, 
					ku.remidi_karena, 
					ku.n_poin,

					up.id_ujian, 
					up.kkm, 
					up.tanggal tgl_jadwal, 
					up.waktu,
					up.kode,

                    u.nama namaUjian

				FROM 
					karyawan_ujian ku

				LEFT JOIN
					ujian_pelaksanaan up
				ON
					ku.id_pelaksanaan = up.id

                LEFT JOIN
                    ujian u
                ON
                    up.id_ujian = u.id

				WHERE
					ku.id = '$id_pengerjaan'
				AND
					ku.hapus = '0'

				LIMIT
					1
		";
		$e = mysqli_query($conn, $q);
		$c = mysqli_num_rows($e);

		if($c == '1'){
			$ketemu = '1';

			$r = mysqli_fetch_assoc($e);

            $tgl_megnerjakan = $r['tgl_megnerjakan'];
            $mulai = $r['mulai'];
            $selesai = $r['selesai'];
            $nilai_akhir = $r['nilai_akhir'];
            $n = $r['n'];
            $n_pk = $r['n_pk'];
            $nilai_grade = $r['nilai_grade'];
            $remidi = $r['remidi'];
            $remidi_karena = $r['remidi_karena'];
            $infoLulus = 'Lulus';
            if($remidi == '1'){
                $infoLulus = 'Belum Lulus. '.$remidi_karena;
            }
            $n_poin = $r['n_poin'];

            $id_ujian = $r['id_ujian'];
            $kkm = $r['kkm'];
            $tgl_jadwal = $r['tgl_jadwal'];
            $waktu = $r['waktu'];
            $kode = $r['kode'];

            $namaUjian = $r['namaUjian'];
		}
    }

    $sub = 'Test Finish';
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
            top: 60px;
            padding: 0px;
        }


        @media only screen and (max-width: 480px){
            #mainContainer {
                top: 44px;
            }

            #sideMenu {
                display: none;
            }

            #desktopMenu {
                display: none;
            }
        }

        #footer {
            margin-top: 80px;
        }

        @media only screen and (min-width: 481px){
            
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
	if($ketemu == '0'){
?>
		<div class="ui icon floating message">
            <i class="inbox icon"></i>
            <div class="content">
                <div class="header">
                    KOSONG
                </div>
                <p>Data hasil ujian tidak ditemukan.</p>
            </div>
        </div>
        <div class="ui vertical basic segment center aligned">
            <p style="font-size: 9pt; color: #60646D;">
                Kembali ke panel <?php echo $jenisPengguna; ?> ? klik <a href="<?php echo $fromHome.''.$jenisPengguna; ?>">di sini</a>
            </p>
        </div>
<?php		
	}
	else{

        $tgl_megnerjakan = $r['tgl_megnerjakan'];
        $mulai = $r['mulai'];
        $selesai = $r['selesai'];
        $nilai_akhir = $r['nilai_akhir'];
        $n = $r['n'];
        $n_pk = $r['n_pk'];
        $nilai_grade = $r['nilai_grade'];
        $remidi = $r['remidi'];
        $remidi_karena = $r['remidi_karena'];
        $n_poin = $r['n_poin'];

        $id_ujian = $r['id_ujian'];
        if($id_ujian=='5483-8ABF1C'){
            //unas
            $jmlSoal = 50;
        }
        else{
            $jmlSoal = 20;
        }

        $kkm = $r['kkm'];
        $tgl_jadwal = $r['tgl_jadwal'];
        $waktu = $r['waktu'];
        $kode = $r['kode'];

        $namaUjian = $r['namaUjian'];

        $susulan = 0;
        if($tgl_jadwal !== $tgl_megnerjakan){
            $susulan = 1;
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
                <p style="font-size: 9pt; color: #60646D;">
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
            $avatar = '../../files/photo/'.$jk.'.png';
            if(!empty($foto) && $foto !== ''){
                $foto = str_replace('%20', ' ', $foto);
                if(file_exists('../../../'.$foto)){
                    $avatar = '../../'.$foto;
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
                    </tbody>
                </table>

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

                <div class="ui hidden divider"></div>
                <table class="ui table">
                    <thead>
                        <tr>
                            <th colspan="2">Hasil</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td width="26%">Nilai Akhir</td>
                            <td>
                                <strong><?php echo $nilai_akhir; ?></strong>
                            </td>
                        </tr>
<?php
    //if UN
    if($id_ujian == '5483-8ABF1C'){
?>
                        <tr>
                            <td>Poin Tambahan</td>
                            <td>
                                <strong><?php echo $n_poin; ?></strong>
                                <p style="font-size: 9pt; color: #60646D;">
                                    Poin diambil dari rerata maksimal 3 ujian bulanan sebelumnya. Pembulatan <strong>Nilai akhir</strong> maksimal setelah ditambah poin tetap 100.
                                </p>
                            </td>
                        </tr>
<?php        
    }
?>                        
                        <tr>
                            <td>Product Knowledge (PK)</td>
                            <td><?php echo $n_pk; ?></td>
                        </tr>
                        <tr>
                            <td>CH</td>
                            <td><?php echo $n; ?></td>
                        </tr>
                        <tr>
                            <td>Grade</td>
                            <td><?php echo $nilai_grade; ?></td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td><strong><?php echo $infoLulus; ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="ui vertical basic segment center aligned">
                <p style="font-size: 9pt; color: #60646D;">
                    Kembali ke panel <?php echo $jenisPengguna; ?> ? klik <a href="<?php echo $fromHome.''.$jenisPengguna; ?>">di sini</a>
                </p>
            </div>
<?php
        }
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
            <a class="item" href="<?php echo $fromHome.''.$jenisPengguna; ?>">
                Panel <?php echo ucwords($jenisPengguna) ?> <i class="user circle icon"></i>
            </a>
            <div class="link item" onclick="tampilkanKonfirmasi('1','Logout','Yakin ingin keluar ?','<?php echo $fromHome; ?>interface/logout.php')">
                <i class="logout icon"></i> Logout
            </div>
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
                var panjang, stateWide, stateTrigger, pushState;
                panjang = $("body").width();

                stateWide = $('#stateWide').val();

                stateTrigger = $('#stateTrigger').val();
                pushState = $('#pushState').val();

                if(panjang<481){
                    if(stateTrigger=='0'){
                        $('#mobileMenuTrigger').transition('fade');
                        $('#stateTrigger').val('1');
                    }
                }
                else{
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
        </script>
    </body>
</html>