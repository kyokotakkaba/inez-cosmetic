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

    $main = 'Data BA/ BC';
    $sub = 'Unggah Data BA/ BC';

    if($_SESSION['menu']!=='employee'){
        $_SESSION['menu'] = 'employee';
    }
?>
<div class="ui message">
    <div class="ui breadcrumb">
        <div class="section"><?php echo $main; ?></div>
        <i class="right angle icon divider"></i>
        <div class="active section"><?php echo $sub; ?></div>
    </div>
</div>

<form method="post" id="frmUnggahKaryawan" enctype="multipart/form-data">
	<input type="hidden" name="view" value="1">

	<div class="field">
        <div class="ui icon button" onclick="backToMain()">
            <i class="left chevron icon"></i> Kembali
        </div>    
    </div>

    <div class="ui orange segment">
        <div class="field">
            <label>Area</label>
            <select id="id_wil" name="id_wil" class="ui search fluid dropdown">
<?php
	$q = "
			SELECT
				id,
				kode,
				nama

			FROM
				wilayah

			WHERE
				hapus = '0'

			ORDER BY
				nama ASC
	";
	$e = mysqli_query($conn, $q);
	$c = mysqli_num_rows($e);
	if($c=='0'){
?>
				<option value="">Data belum ada.</option>
<?php		
	}
	else{
?>
				<option value="">Pilih</option>
<?php		
		while ($r = mysqli_fetch_assoc($e)) {
			$idWil = $r['id'];
			$dis = $r['kode'].' - '.$r['nama'];
?>
				<option value="<?php echo $idWil; ?>"><?php echo $dis; ?></option>
<?php			
		}
	}
?>            	  
            </select>
        </div>
        <div class="two fields">
            <div class="field">
                <label>Hak akses</label>
                <div class="ui input">
                    <input type="text" id="jenis" name="jenis" readonly="readonly" placeholder="Admin (Supervisor)/ User" maxlength="8" required="required" value="user">
                </div>
            </div>
            <div class="field">
                <label>Tingkat belajar</label>
                <select id="tingkat" name="tingkat" class="ui dropdown">
<?php
    $qT = "
            SELECT
                id,
                nama
            FROM
                tingkat_belajar
            WHERE
                hapus = '0'
            ORDER BY
                no ASC
    ";
    $eT = mysqli_query($conn, $qT);
    $cT = mysqli_num_rows($eT);

    if($cT=='0'){
?>
                    <option value="">Belum ada data tingkat belajar</option>
<?php        
    }
    else{
?>
                    <option value="">Pilih</option>
<?php        
        while ($rT = mysqli_fetch_assoc($eT)) {
            $idTingkat = $rT['id'];
            $namaTingkat = $rT['nama'];
?>
                    <option value="<?php echo $idTingkat; ?>" >
                        <?php echo $namaTingkat; ?>
                    </option>
<?php            
        }
    }
?>                    
                </select>
            </div>
        </div>

        <div class="field">
			<label>Pilih File</label>
			<div class="ui left action input">
				<div class="ui teal labeled icon button" onclick="pilihFile()">
					<i class="pin icon"></i> Pilih
				</div>
				<input type="text" id="textFile" placeholder="Pilih File" onchange="cekFile()" readonly="readonly" onclick="pilihFile()">
				<input type="file" accept=".xls" name="browseFile" id="browseFile" style="display: none;" onchange="terpilih()">
			</div>
		</div>
		<div class="field eksekusi" style="display: none;">
			<button type="submit" Class="ui labeled icon primary button">
				<i class="upload icon"></i> Upload
			</button>
		</div>
    </div>

	<div class="field">
		<div class="ui message">
			<div class="content">
				<div class="header">
					Istruksi :
				</div>
				<ul>
					<li>Pilih File Excel 97-2003 Workbook (*.xls).</li>
					<li>Pastikan tidak ada data yang sama.</li>
					<li>Jika terdapat kesamaan data dengan sebelumnya (nik, email, no hp), proses unggah akan dibatalkan</li>
					<li>NIK, Nama, Email, No. HP wajid di isi. Pengisian data secara lengkap sangat disarankan.</li>
				</ul>
				<a class="ui icon basic teal button" href="../files/inez_upload_karyawan_template.xls"><i class="cloud download icon"></i> Unduh template dasar</a>
			</div>
		</div>
	</div>
</form>    

<script type="text/javascript">
	$('.dropdown').dropdown({ fullTextSearch: "exact" });

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


	$("#frmUnggahKaryawan").submit(function(e){
		var formData, id_wil, jenis, tingkat, filenya;
		formData = new FormData(this);
		id_wil = $('#id_wil').val();
		jenis = $('#jenis').val();
		tingkat = $('#tingkat').val();
		filenya = $('#textFile').val();
		e.preventDefault();
		loadingMulai();

		if(id_wil==''||jenis==''||tingkat==''||filenya==''){
			tampilkanPesan('0','Lengkapi form.');
			loadingSelesai();
		}
		else{
			$.ajax({
				type:'post',
				url:'interface/employee-upload-form-process.php',
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