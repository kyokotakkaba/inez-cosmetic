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

					//get specific data from previus form
					$id_kelompok = saring($_POST['id_kelompok']);
					$kosongkan = saring($_POST['kosongkan']);

					if($kosongkan=='1'){
						$qD = "
								UPDATE
									pertanyaan

								SET
									hapus = '1'

								WHERE
									id_kelompok = '$id_kelompok'
						";
						$eD = mysqli_query($conn, $qD);

						if(!$eD){
?>
							<script type="text/javascript">
								tampilkanPesan('0','Tidak dapat mengkosongkan pertanyaan lama.');
							</script>
<?php
							exit();
						}
					}

					//prepare for question query
					$qP = "
							INSERT INTO 
								pertanyaan
									(
										id, 
										id_kelompok, 
										isi
									)
							VALUES 
					";


					//prepare for answers query
					$qJ = "
							INSERT INTO 
								jawaban
									(
										id, 
										id_pertanyaan, 
										isi, 
										benar
									) 
							VALUES 
					";



					//read data from excel start here
					$data = new Spreadsheet_Excel_Reader($sourcePath);
					

					// baca jumlah baris dalam sheet 1
					$jmlBaris = $data->rowcount(0);



					//echo 'Jumlah pertanyaan : '.$jmlBaris;

					//check if the excel not empty
					if($jmlBaris=="1"){
?>
						<script type="text/javascript">
							tampilkanPesan('0','File kosong.');
						</script>
<?php						
						exit();
					}


					$qC = "
							SELECT 
								id

							FROM 
								pertanyaan 

							WHERE
								hapus = '0'
							AND
					";
					$adaSama = 0;
					$jmlSama = 0;
					$arPertanyaan = array();
					
					$qSama = "
								UPDATE
									pertanyaan 

								SET
									hapus = '1'

								WHERE
					";


					//loping creating variable from excel file
					for ($i=2; $i<=$jmlBaris; $i++){
						$nomor = $i-1;

						$adaBenar = '0';
						// baca data pada baris ke-i, kolom ke-1, pada sheet 1
						$idPBaru = UUIDBaru();
						$pertanyaan = saring(trim($data->val($i, 1, 0)));
						if($pertanyaan==''){
?>
							<script type="text/javascript">
								tampilkanPesan('0','Pertanyaan pada baris ke <?php echo $nomor; ?> tidak boleh kosong. Jika tidak kosong coba hapus data -> copy data dari sumber asli -> paste as text -> ke cell pertanyaan yang dituju. ');
							</script>
<?php			
							exit();
						}

						$qP .= "
								(
									'$idPBaru',
									'$id_kelompok',
									'$pertanyaan'
								)
						";

						if($jmlBaris>2){
							if($i<$jmlBaris){
								$qP .= ",";
							}
						}

						for ($a=2; $a <= 4; $a++) {
							$idJBaru = UUIDBaru();
							$jawaban = saring(trim($data->val($i, $a, 0)));

							$ke = $a-1;

							if($jawaban==''){
?>
								<script type="text/javascript">
									tampilkanPesan('0','Jawaban pada pertanyaan baris ke <?php echo $nomor; ?>, opsi ke <?php echo $ke; ?> tidak boleh kosong. Jika tidak kosong coba hapus data -> copy data dari sumber asli -> paste as text -> ke cell jawaban yang dituju. ');
								</script>
<?php			
								exit();
							}


							$status = trim(saring($data->val($i, 5, 0)));

							if($status==$ke){
								$benar = '1';
								$adaBenar = '1';
							}
							else{
								$benar = '0';
							}

							$qJ .= "
									(
										'$idJBaru',
										'$idPBaru',
										'$jawaban',
										'$benar'
									)
							";

							if($a<4){
								$qJ .= ",";
							}

							if($a==4){
								if($adaBenar=='0'){
?>
										<script type="text/javascript">
											tampilkanPesan('0','Set jawaban benar pada pertanyaan ke <?php echo $nomor; ?>.');
										</script>
<?php			
									exit();
								}
							}
						}

						array_push($arPertanyaan, $pertanyaan);

						if($jmlBaris>2){
							if($i<$jmlBaris){
								$qJ .= ",";
							}
						}
					}



					$jmlTanya = count($arPertanyaan);

					if($jmlTanya>0){
						if($jmlTanya==1){
							$isiCek = $arPertanyaan[0];
							$qC .= "
									isi = '$isiCek'
							";
						}
						else{
							$qC .= "(";
							$maxC = $jmlTanya-1;
							for ($s=0; $s < $jmlTanya; $s++) {
								$isiCek = $arPertanyaan[$s];
								$qC .= "
										isi = '$isiCek'
								";
								if($s<$maxC){
									$qC .= "OR";
								}
							}
							$qC .= ")";
						}

						$eC = mysqli_query($conn, $qC);
						$jmlC = mysqli_num_rows($eC);
						$jmlSama = $jmlC;
						if($jmlC>0){
							$adaSama = 1;
							$noSama = 0;
							while ($rC = mysqli_fetch_assoc($eC)) {
								$noSama = $noSama+1;
								$idSama = $rC['id'];
								$qSama .= "
											id = '$idSama'
								";
								if($jmlC>1){
									if($noSama<$jmlC){
										$qSama .= "OR";
									}
								}
							}
						}
					}

					//query test
					/*
					echo $qP;
					echo $qJ;
					exit();
					*/

					//execute the SQL here!!
					$eP = mysqli_query($conn, $qP);
					if($eP){
						$eJ = mysqli_query($conn, $qJ);
						if($eJ){
							$teksSama = '';
							if($adaSama==1){
								$eSama = mysqli_query($conn, $qSama);
								if($eSama){
									$teksSama .= 'Berhasil ';
								}
								else{
									$teksSama .= 'Gagal ';
								}
								$teksSama .= 'menghapus '.$jmlSama.' pertanyaan lama.';
							}
?>
							<script type="text/javascript">
								tampilkanPesan('1','Data berhasil diimpor. <?php echo $teksSama; ?>');
								$('#browseFile').val('');
								$('#textFile').val('');
								updateRow();
								backToMain();
							</script>
<?php
							exit();
						}
						else{
?>
							<script type="text/javascript">
								tampilkanPesan('0','Terjadi kesalahan saat impor jawaban.');
							</script>
<?php
							exit();
						}
					}
					else{
?>
						<script type="text/javascript">
							tampilkanPesan('0','Terjadi kesalahan saat memproses data.');
						</script>
<?php
						exit();
					}
				}
				else{
					//Ekstensi file yang diperbolehkan hanya *.xls
?>
					<script type="text/javascript">
						tampilkanPesan('0','Ekstensi file yang diperbolehkan hanya *.xls.');
					</script>
<?php
					exit();
				}

			}
			else{
				//Ukuran file tidak boleh melebihi 1 MB. Info -> 4 MB (4194304)
?>
				<script type="text/javascript">
					tampilkanPesan('0','Ukuran file tidak boleh melebihi 1 MB.');
				</script>
<?php			
				exit();
			}
			
		}
	}
?>