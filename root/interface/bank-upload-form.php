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
    $main = 'Bank Soal';
    $sub = 'Upoad Pertanyaan';
?>
<div class="ui message">
    <div class="ui breadcrumb">
        <div class="section"><?php echo $main; ?></div>
        <i class="right angle icon divider"></i>
        <div class="active section"><?php echo $sub; ?></div>
    </div>
</div>
<form method="post" id="frmUnggahPertanyaan" enctype="multipart/form-data">
	<input type="hidden" name="view" value="1">
	

	<div class="field">
        <div class="ui icon button" onclick="backToMain()">
            <i class="left chevron icon"></i> Kembali
        </div>    
    </div>

    <div class="ui orange segment">
        <div class="field">
            <label>Kelompok materi</label>
            <select id="id_kelompok" name="id_kelompok" class="ui dropdown">
<?php
	$q = "
			SELECT
				id,
				nama,
				n_pk

			FROM
				materi_kelompok

			WHERE
				hapus = '0'

			ORDER BY
				nama ASC
	";
	$e = mysqli_query($conn, $q);
	$c = mysqli_num_rows($e);
	if($e=='0'){
?>
				<option value="">Belum ada data</option>
<?php
	}
	else{
?>
				<option value="">Pilih</option>
<?php		
		while ($r = mysqli_fetch_assoc($e)) {
			$idK = $r['id'];
			$kelompok = $r['nama'];
			$n_pk = $r['n_pk'];
			$dis = $kelompok;
			if($n_pk=='1'){
				$dis .= " (PK)";
			}
?>
				<option value="<?php echo $idK; ?>"><?php echo $dis; ?></option>
<?php			
		}
	}
?>                        	  
            </select>
        </div>
        <div class="field">
			<label>Kosongkan data pertanyaan lama pada kelompok materi terpilih ?</label>
            <div id="chkKosongkan" class="ui toggle checkbox">
                <input type="checkbox" name="kosongkan" id="kosongkan" value="0">
                <label id="lblTeksInfoKosong">Tidak</label>
            </div>
        </div>
    </div>
        
	<div class="field">
		<label>Pilih File</label>
		<div class="ui left action input">
			<div class="ui teal labeled icon button" onclick="pilihFile()">
				<i class="pin icon"></i> Pilih
			</div>
			<input type="text" placeholder="Pilih File" id="textFile" onchange="cekFile()" readonly="readonly" onclick="pilihFile()">
			<input type="file" accept=".xls" name="browseFile" id="browseFile" style="display: none;" onchange="terpilih()">
		</div>
	</div>
	<div class="field eksekusi" style="display: none;">
		<button type="submit" Class="ui labeled icon primary button">
			<i class="upload icon"></i> Upload
		</button>
	</div>
	<div class="field">
		<div class="ui message">
			<div class="content">
				<div class="header">
					Informasi :
				</div>
				<ul>
					<li>Pilih File Excel 97-2003 Workbook (*.xls).</li>
					<li>Silahkan <strong>copy pertanyaan atau jawaban</strong> ke cell pada file excel dengan cara -> <strong>paste as text</strong>.</li>
					<li>Data akan disimpan dengan kelompok materi yang terpilih di atas.</li>
					<li>Data akan dicek terlebih dahulu apakah pertanyaan sudah ada atau belum. Jika sudah ada, maka data lama akan dihapus.</li>
					<li>Proses cek adalah dengan membandingkan data pertanyaan yang sudah ada dengan data baru secara detail. Perbedaan titik (.), koma (,) dan karakter khusus lain akan dianggap berbeda dan data lama tidak akan dihapus.</li>
					<li>Jika anda memilih untuk mengkosongkan semua pertanyaan pada kelompok materi terpilih, maka semua pertanyaan lama (yang sudah ada) pada kelompok materi terpilih akan dihapus dan pertanyaan yang baru (yang diupload) akan disimpan.</li>
					<li>Semakin banyak pertanyaan yang diunggah semakin lama server mengeksekusi perintah (bergantung juga dengan spesifikasi server dan jaringan)</li>
				</ul>
				<a class="ui icon basic teal button" href="../files/inez_upload_pertanyaan.xls"><i class="cloud download icon"></i> Unduh template dasar</a>
			</div>
		</div>
	</div>
</form>    

<script type="text/javascript">
	$('.dropdown').dropdown();

	$('#chkKosongkan').checkbox({
        onChecked: function() {
            $('#lblTeksInfoKosong').text('Ya');
            $('#kosongkan').val('1');
        },
        onUnchecked: function() {
            $('#lblTeksInfoKosong').text('Tidak');
            $('#kosongkan').val('0');
        }    
    })


	function terpilih(){
		var terpilih = $('#browseFile').val();
		$('#textFile').val(terpilih);
		cekFile();
	}

	function cekFile(){
		var file = $('#textFile').val();
		if (file == ""){
			if($('.eksekusi').is(':visible')==true){
				$('.eksekusi').transition('drop');
			}
		}
		else{
			if($('.eksekusi').is(':visible')==false){
				$('.eksekusi').transition('drop');
			}
		}
	}

	function pilihFile(){
		$('#browseFile').click();
	}


	$("#frmUnggahPertanyaan").submit(function(e){
		
		var formData, id_kelompok, filenya;
		formData = new FormData(this);
		id_kelompok = $('#id_kelompok').val();
		filenya = $('#textFile').val();

		e.preventDefault();

		loadingMulai();

		if(id_kelompok==''){
			tampilkanPesan('0','Pilih kelompok materi.');
			loadingSelesai();
		}
		else if(filenya==''){
			tampilkanPesan('0','Pilih file.');
			loadingSelesai();
		}
		else{
			console.log('ajax ready');
			$.ajax({
				type:'post',
				url:'interface/bank-upload-form-process.php',
				async:true,
				data: formData,
				cache:false,
	            contentType: false,
	            processData: false,
	            success:function(data){
	                $('#feedBack').html(data);
	                loadingSelesai();
	            },
	            error: function(data){
	                $('#feedBack').html(data);
	                loadingSelesai();
	            }
			})
		}
	})

</script>