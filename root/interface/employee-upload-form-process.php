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
	require_once $fromHome."lib/excel-reader.php";

	if(!empty($_FILES)) {
		if(is_uploaded_file($_FILES['browseFile']['tmp_name'])) {
			$sourcePath = $_FILES['browseFile']['tmp_name'];

			$fileDiUnggah = $_FILES['browseFile']['name'];

			$allowedExts = array("xls");
			$extension = trim(strtolower(end(explode(".", $fileDiUnggah))));

			if ($_FILES["browseFile"]["size"] < "1048576"){

				if(in_array($extension, $allowedExts)){


					//prepare for creating existance data
					$q = "
							SELECT
								nik,
								email,
								hp
							FROM
								karyawan
							WHERE
								hapus = '0'
					";
					$e = mysqli_query($conn, $q);
					$c = mysqli_num_rows($e);
					if($c=="0"){
						$adaData = '0';
					}
					else{
						$adaData = '1';
					}

					//temporary array for existance checing
					$arrayNik = array();
					$arrayEmail = array();
					$arrayHp = array();

					if($adaData=='1'){
						//store data for already used data (uname, email, hp)
						while ($r=mysqli_fetch_array($e)) {
							$aNik = $r['nik'];
							$aEmail = $r['email'];
							$aHp = $r['hp'];

							array_push($arrayNik, $aNik);
							array_push($arrayEmail, $aEmail);
							array_push($arrayHp, $aHp);
						}
					}


					//get specific data from previus form
					$id_wil = saring($_POST['id_wil']);
					$jenis = saring($_POST['jenis']);
					$tingkat = saring($_POST['tingkat']);



					//prepare insert query to db
					$qI = "
							INSERT INTO 
								karyawan
									(
										id, 
										id_wil, 
										kode, 
										nik, 
										nama, 
										jk, 
										tmpt_lahir, 
										tgl_lahir, 
										email, 
										hp, 
										alamat,
										tingkat,
										tgl_masuk
									) 
							VALUES
					";


					$qA = "
							INSERT INTO 
								akun
									(
										id, 
										uname, 
										pass, 
										id_pengguna, 
										jenis
									) 
							VALUES
					";


					//read data from excel start here
					$data = new Spreadsheet_Excel_Reader($sourcePath);
					

					// baca jumlah baris dalam sheet 1
					$jmlBaris = $data->rowcount(0);

					//check if the excel not empty
					if($jmlBaris=="1"){
?>
						<script type="text/javascript">
							tampilkanPesan('0','File kosong.');
						</script>
<?php						
						exit();
					}




					//loping creating variable from excel file
					for ($i=2; $i<=$jmlBaris; $i++){
						
						//karyawan variabel
						// baca data pada baris ke-i, kolom ke-1, pada sheet 1
						$idKaryawanBaru = UUIDBaru();
						$kode = kodeBaru();
						$nik = saring($data->val($i, 1, 0));
						$nama = saring($data->val($i, 2, 0));
						$jk = saring($data->val($i, 3, 0));
						$tmpt_lahir = saring($data->val($i, 4, 0));
						$tgl_lahir = saring($data->val($i, 5, 0));
						$email = saring($data->val($i, 6, 0));
						$hp = saring($data->val($i, 7, 0));
						$alamat = saring($data->val($i, 8, 0));
						$tgl_masuk = saring($data->val($i, 0, 0));


						//chek if th iinfo not empty in some field
						if($nik==''){
?>
							<script type="text/javascript">
								tampilkanPesan('0','Data NIK pada baris data ke-<?php echo $i-1; ?> kosong.');
							</script>
<?php							
							exit();									
						}
						else if($nama==''){
?>
							<script type="text/javascript">
								tampilkanPesan('0','Data Nama pada baris data ke-<?php echo $i-1; ?> kosong.');
							</script>
<?php							
							exit();
						}
						else if($email==''){
?>
							<script type="text/javascript">
								tampilkanPesan('0','Data Email pada baris data ke-<?php echo $i-1; ?> kosong.');
							</script>
<?php							
							exit();
						}
						else if($hp==''){
?>
							<script type="text/javascript">
								tampilkanPesan('0','Data No HP pada baris data ke-<?php echo $i-1; ?> kosong.');
							</script>
<?php							
							exit();
						}





						//check if the data already exist in temp array
						if(in_array($nik, $arrayNik)){
?>
							<script type="text/javascript">
								tampilkanPesan('0',"NIK <strong><i><?php echo $nik; ?></i></strong> sudah digunakan.");
							</script>
<?php							
							exit();
						}

						if(in_array($email, $arrayEmail)){
?>
							<script type="text/javascript">
								tampilkanPesan('0',"Email <strong><i><?php echo $email; ?></i></strong> sudah digunakan.");
							</script>
<?php							
							exit();
						}

						if(in_array($hp, $arrayHp)){
?>
							<script type="text/javascript">
								tampilkanPesan('0',"Nomor HP <strong><i><?php echo $hp; ?></i></strong> sudah digunakan.");
							</script>
<?php							
							exit();
						}







						//adding current data to temporary array for next cecking existence
						array_push($arrayNik, $nik);
						array_push($arrayEmail, $email);
						array_push($arrayHp, $hp);



						//akun variabel
						$idAkunBaru = UUIDBaru();
						$uname = $nik;
						$pass = md5($acak.md5($nik));






						//updating the SQL
						$qI .= "
									(
										'$idKaryawanBaru', 
										'$id_wil', 
										'$kode', 
										'$nik', 
										'$nama', 
										'$jk', 
										'$tmpt_lahir', 
										'$tgl_lahir', 
										'$email', 
										'$hp', 
										'$alamat',
										'$tingkat',
										'$tgl_masuk'
									)
						";

						$qA .= "
									(
										'$idAkunBaru', 
										'$uname', 
										'$pass', 
										'$idKaryawanBaru', 
										'$jenis'
									) 
						";

						if($i<$jmlBaris){
							$qI .= ",";
							$qA .= ",";
						}

					}



					//echo $qI.'<br><br><br>'.$qA;
					//execute the SQL here!!
					$eI = mysqli_query($conn,$qI);

					if($eI){
						$eA = mysqli_query($conn, $qA);
						if($eA){
?>
							<script type="text/javascript">
								tampilkanPesan('1','Data berhasil diimpor.');
								$('#browseFile').val('');
								$('#textFile').val('');
								updateRow();
								backToMain();
							</script>
<?php
						}
						else{
?>
							<script type="text/javascript">
								tampilkanPesan('0','Terjadi kesalahan saat pembuatan akun.');
							</script>
<?php
						}
					}
					else{
?>
						<script type="text/javascript">
							tampilkanPesan('0','Terjadi kesalahan saat memproses data.');
						</script>
<?php
					}

					
				}
				else{
					//Ekstensi file yang diperbolehkan hanya *.xls
?>
					<script type="text/javascript">
						tampilkanPesan('0','Ekstensi file yang diperbolehkan hanya *.xls.');
					</script>
<?php
				}

			}
			else{
				//Ukuran file tidak boleh melebihi 1 MB. Info -> 4 MB (4194304)
?>
				<script type="text/javascript">
					tampilkanPesan('0','Ukuran file tidak boleh melebihi 1 MB.');
				</script>
<?php			
			}
			
		}
	}
?>