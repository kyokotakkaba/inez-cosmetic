<?php
	date_default_timezone_set("Asia/Jakarta");

	//for spmb connection
	$untuk = 'local'; //local or online

	if($untuk=='local'){
		$dbUser = 'root';       //database username
		$dbPass = '';           //database password
		$dbName = 'nzcmkcom_diklat'; 	//database name

		//local file manager helper
		$folder = 'inez';
	}
	else if($untuk=='online'){
		$dbUser = 'nzcmkcom_diklat';       //database username
		$dbPass = 'nzcmkcom_diklat';           //database password
		$dbName = 'nzcmkcom_diklat'; 	//database name

		//local file manager helper
		$folder = '';
	}

	$dbHost = 'localhost';  //database host name

	$conn = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);

	if(!$conn){
		die("Database connection failed: " . mysqli_connect_error());
		exit();
	}

	$baseLink = 'http://'.$_SERVER['HTTP_HOST'].'/'.$folder;
	$appCode = 'EB68DA-6CC63F63-46B0F';

	$q = "
			SELECT
				title,
				description,
				keywords,
				deploy_year,
				owner,
				owner_web

			FROM
				app_web_meta

			WHERE
				id = '$appCode'

			LIMIT
				1
	";


	$e = mysqli_query($conn, $q);
	$c = mysqli_num_rows($e);
	if($c=='1'){
		$r = mysqli_fetch_assoc($e);
		$title = $r['title'];
		$description = $r['description'];
		$keywords = $r['keywords'];

		$deploy_year = $r['deploy_year'];
		$owner = $r['owner'];
		$owner_web = $r['owner_web'];

		$q = "
				SELECT
					programmer,
					programmer_email,
					version,
					base_link,
					randomizer

				FROM
					app

				WHERE
					id = '$appCode'

				LIMIT
					1

		";
		$e = mysqli_query($conn, $q);
		$c = mysqli_num_rows($e);
		if($c==1){
			$r = mysqli_fetch_assoc($e);
			$author = $r['programmer'].' - '.$r['programmer_email'];
			//$baseLink = $r['base_link'];
			$acak = $r['randomizer'];
			$version = $r['version'];
		}
		else{
			echo "APP BROKEN";
			exit();
		}
	}
	else{
		echo 'ILLEGAL ACCESS!!';
		exit();
	}


	$qP = "
			SELECT
				id
			FROM
				periode
			WHERE
				aktif = '1'
			LIMIT
				1
	";
	$eP = mysqli_query($conn, $qP);
	$cP = mysqli_num_rows($eP);
	if($cP == '1'){
		$rP = mysqli_fetch_assoc($eP);
		$idPeriode = $rP['id'];
	}
	else{
		$idPeriode = '0';
	}




	//For Theme
	$bgColor = 'background-color: #F0F1F5';
	$bgHeader = 'background-image: linear-gradient(120deg, #a6c0fe 0%, #f68084 100%);';
	$bgMenu = 'background-image: linear-gradient(to right, #434343 0%, black 100%);';
	$bgFooter = 'background-color: #60646D;';
	$accentColor = 'background-image: linear-gradient(to top, #a18cd1 0%, #fbc2eb 100%);';
	$goTopColor = 'background-image: linear-gradient(45deg, #ff9a9e 0%, #fad0c4 99%, #fad0c4 100%);';

	$dev = 'Wannabe Kreasi';
	$dev_web = 'wannabe.id';




	//filter every input from user
	function saring($string){
		$pure = htmlspecialchars(trim($string));
		global $conn;

		$processed = mysqli_real_escape_string($conn, $pure);
		return 	$processed;
	}

	function acakNomor(){
		$numb = array();
		for ($i=3; $i <= 7; $i++) {
			array_push($numb, $i);
		}
        $acak = array_rand($numb);
        return $numb[$acak];
	}

	function acakPart(){
		$numb = array();
		for ($i=1; $i <= 4; $i++) {
			array_push($numb, $i);
		}
        $acak = array_rand($numb);
        return $numb[$acak];
	}

	function UUIDBaru() {
		$s = strtoupper(md5(uniqid(rand(),true)));
		$p = strlen($s);
		$part = acakPart();
		$guidText = '';
		$start = 0;
		for ($i=0; $i <= $part; $i++) {
			$start = $start + acakNomor();
			$many = acakNomor();
			$guidText .= substr($s, $start, $many);

			if($i<$part) {
				$guidText .= "-";
			}
		}
		return $guidText;
	}
	

	//convert to Indonesia rupiah
	function rupiahKan($uang){
		$ubah = 'Rp. '.number_format($uang,'0',',','.');
		return $ubah;
	}

	//convert to vamiliar indonesia date format
	function tanggalKan($lempar){
		if($lempar=="" || $lempar=="0000-00-00"){
			return "Tanggal belum di set";
		}
		else{
			$abc = explode("-", $lempar);
			$thn = $abc[0];
			$b = $abc[1];
			$tang = $abc[2];
			$bulan = array('January', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
			$cekB = $b-1;
			$bln = $bulan[$cekB];

			$bangun = $tang.' '.$bln.' '.$thn;
			return $bangun;
		}
	}

	//generate unique data
	function kodeBaru() {
		$s = strtoupper(md5(uniqid(rand(),true)));
		$guidText = substr($s,0,6);
		return $guidText;
	}

	function hitungUmur($awal){
		if($awal!==''&&$awal!=='0000-00-00'){
			$bday = new DateTime($awal);
		    $today = new Datetime(date('Y-m-d'));
		    $diff = $today->diff($bday);

		    $tahun = $diff->y;
		    $bulan = $diff->m;
		    $hari = $diff->d;
		}
		else{
			$tahun = '0';
		    $bulan = '0';
		    $hari = '0';
		}
		$kembali = '';
		if($tahun>0){
			$kembali .= $tahun.' tahun ';
		}
		if($bulan>0){
			$kembali .= $bulan.' bulan ';
		}
		if($tahun==0&&$bulan==0){
			$kembali .= $hari.' hari';
		}
	    return $kembali;
	}

	function adaData($data, $tabel, $kolom, $idData){
		global $conn;
		$q = "
					SELECT
						id

					FROM
						$tabel

					WHERE
						$kolom = '$data'
					AND
						hapus = '0'
					AND
						id != '$idData'

					LIMIT
						1
		";
		$e = mysqli_query($conn, $q);
		$r = mysqli_num_rows($e);
		return $r;
	}

	function buatLink($string, $ext=''){
		$replace = '-';
		$string = strtolower($string);
		//replace / and . with white space
		$string = preg_replace("/[\/\.]/", " ", $string);
		$string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
		//remove multiple dashes or whitespaces
		$string = preg_replace("/[\s-]+/", " ", $string);
		//convert whitespaces and underscore to $replace
		$string = preg_replace("/[\s_]/", $replace, $string);
		//limit the slug size
		$string = substr($string, 0, 100);
		//text is generated
		return ($ext) ? $string.$ext : $string;
	}



	function tglAwalSepekan($week, $year) {
	    $time = strtotime("1 January $year", time());
		$day = date('w', $time);
		$time += ((7*$week)+1-$day)*24*3600;
		$dates = date('Y-m-d', $time);
		return $dates;
	}

	function tglAkhirSepekan($week, $year) {
	    $time = strtotime("1 January $year", time());
		$day = date('w', $time);
		$time += ((7*$week)+1-$day)*24*3600;
		$time += 6*24*3600;
		$dates = date('Y-m-d', $time);
		return $dates;
	}

	function tulisLog($pesan){
		$idPengguna = $_SESSION['idPengguna'];
		$jenis = $_SESSION['jenisPengguna'];
		$target = 'files/log/'.$jenis.'.txt';
		if(file_exists('../../../'.$target)){
			$urlTo = '../../../';
		}
		else if(file_exists('../../'.$target)){
			$urlTo = '../../';
		}
		else if(file_exists('../'.$target)){
			$urlTo = '../';
		}
		$file = fopen($urlTo.''.$target, 'a');
	    $pjg = strlen($idPengguna);
	    for ($i=$pjg; $i <= 32; $i++) {
	        $idPengguna .= ' ';
	    }

	    if($jenis=='admin'){
	    	$untuk = $_SESSION['untuk'];
	    	$saatIni = $untuk.'  |  '.tanggalKan(date('Y-m-d')).'  |  '.date('H:i:s');
	    }
	    else{
	    	$saatIni = tanggalKan(date('Y-m-d')).'  |  '.date('H:i:s');
	    }

	    $aksi = $pesan." \r\n";
	    $logText = $idPengguna.'  |  '.$saatIni.'  |  '.$aksi;
	    echo fwrite($file, $logText);
	    fclose($file);
	}






	$arDays = array(
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu',
        'Sunday' => 'Minggu'
    );

    $arNumber = array(
    	'',
    	'one',
    	'two',
    	'three',
    	'four',
    	'five',
    	'six',
    	'seven',
    	'eight',
    	'nine',
    	'ten'
   	);

    $arAssocNumber = array(
		'0'=>'',
		'1'=>'one',
		'2'=>'two',
		'3'=>'three',
		'4'=>'four',
		'5'=>'five',
		'6'=>'six',
		'7'=>'seven',
		'8'=>'eight',
		'9'=>'nine',
		'10'=>'ten'
    );



    function cariIndexAssocArr($arData, $teks){
    	foreach ($arData as $key => $value) {
    		if($value==$teks){
    			return $key;
    			break;
    		}
    	}
    }
















	function cekNIK($data, $tabel, $idData){
		global $conn;
		$q = "
					SELECT
						id
					FROM
						$tabel
					WHERE
						nik = '$data'
					AND
						hapus = '0'
					AND
						id != '$idData'
					LIMIT
						1
		";
		$e = mysqli_query($conn, $q);
		$r = mysqli_num_rows($e);
		return $r;
	}

	function cekEmail($data, $tabel, $idData){
		global $conn;
		$q = "
					SELECT
						id
					FROM
						$tabel
					WHERE
						email = '$data'
					AND
						hapus = '0'
					AND
						id != '$idData'
					LIMIT
						1
		";
		$e = mysqli_query($conn, $q);
		$r = mysqli_num_rows($e);
		return $r;
	}


	function cekHp($data, $tabel, $idData){
		global $conn;
		$q = "
					SELECT
						id
					FROM
						$tabel
					WHERE
						hp = '$data'
					AND
						hapus = '0'
					AND
						id != '$idData'
					LIMIT
						1
		";
		$e = mysqli_query($conn, $q);
		$r = mysqli_num_rows($e);
		return $r;
	}




	function cekUname($data){
		global $conn;
		$q = "
					SELECT
						id
					FROM
						akun
					WHERE
						uname = '$data'
					LIMIT
						1
		";
		$e = mysqli_query($conn, $q);
		$r = mysqli_num_rows($e);
		return $r;
	}






















	function ambilPeriodeAktif(){
		global $conn;
		$q = "
					SELECT
						id

					FROM
						periode

					WHERE
						aktif = '1'

					LIMIT
						1
		";
		$e = mysqli_query($conn, $q);
		$c = mysqli_num_rows($e);
		if($c=='0'){
			return '';
		}
		else {
			$r = mysqli_fetch_assoc($e);
			return $r['id'];
		}
	}





	function sendNotif($judul, $isi, $untuk){
		$idNotif = UUIDBaru();
		date_default_timezone_set("Asia/Jakarta");
		$pada = date('Y-m-d');
    	$waktu = date('H:i:s');

		global $conn;
		$q = "
				INSERT INTO
					notifikasi
						(
							id,
							judul,
							isi,
							untuk,
							pada,
							waktu
						)
				VALUES
						(
							'$idNotif',
							'$judul',
							'$isi',
							'$untuk',
							'$pada',
							'$waktu'
						)
		";
		$e = mysqli_query($conn, $q);
	}

?>