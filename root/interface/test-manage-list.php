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
	
	$start 	= saring($_POST['start']);
	$limit 	= saring($_POST['limit']);
	$cari 	= saring($_POST['cari']);

    $id_pelaksanaan   = saring($_POST['lastId']);

    $sekarang = date('Y-m-d');

	if(is_numeric($start) AND is_numeric($limit)){
		$q = "
                SELECT 
                    up.id idP,
                    up.susulan,
                    up.susulan_tgl,

                    k.id idKar,
                    k.nik, 
                    k.nama, 
                    k.jk, 
                    k.tgl_lahir, 
                    k.tingkat,
                    k.foto,
                    k.tgl_masuk,

                    a.jenis,

                    w.nama nama_wil,

                    wsw.nama nama_wil_sup,
                    
                    t.nama tingkat,

                    ku.id idPu,
                    ku.tanggal, 
                    ku.mulai,
                    ku.selesai,
                    ku.nilai_akhir,
                    ku.n,
                    ku.n_pk,
                    ku.nilai_grade,
                    ku.remidi,

                    p.id_ujian,
                    p.kkm,
                    p.tanggal tglUjian

                FROM 
                    ujian_pelaksanaan_target_karyawan up
                
                LEFT JOIN
                    karyawan k
                ON
                    up.id_karyawan = k.id

                LEFT JOIN
                    tingkat_belajar t
                ON
                    k.tingkat = t.id

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

                LEFT JOIN
                    karyawan_ujian ku
                ON
                    up.id_karyawan = ku.id_karyawan
                AND
                    up.id_pelaksanaan = ku.id_pelaksanaan

                LEFT JOIN
                    ujian_pelaksanaan p
                ON
                    up.id_pelaksanaan = p.id

                WHERE
                    up.id_pelaksanaan = '$id_pelaksanaan'
                AND
                    up.hapus = '0'
        ";

		if($cari!==''){
            $q.="
                    AND 
                    (
                        k.nik = '$cari'
                    OR
                        k.nama LIKE '%$cari%'
                    OR
                        wsw.nama LIKE '%$cari%'
                    OR
                        w.nama LIKE '%$cari%'
                    OR
                        a.jenis LIKE '%$cari%'
                    )
            ";
        }

		$q.="	
				ORDER BY 
					k.nik ASC
                    
				LIMIT 
					$limit 
				OFFSET 
					$start
		";
	}
	else{
?>
		<tr>
			<td colspan="4">
				<i class="circle info icon teal"></i> <i>Parameter limit dan offset harus angka.</i>
			</td>
		</tr>
<?php		
		exit();
	}
    
	$e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);

    if($c>0){
        $ar = array();
        $r = array();

        while ($d = mysqli_fetch_assoc($e)) {
            $r['idP']           = $d['idP'];
            $r['id_ujian']           = $d['id_ujian'];

            $r['idKar']           = $d['idKar'];
            $r['nik']              = $d['nik'];
            $r['nama']          = $d['nama'];
            $r['jk']            = $d['jk'];
            $r['tgl_lahir']     = $d['tgl_lahir'];
            $r['foto']          = $d['foto'];
            $r['tgl_masuk']          = $d['tgl_masuk'];

            $r['nama_wil']      = $d['nama_wil'];

            $r['jenis']         = $d['jenis'];
            $r['nama_wil_sup']         = $d['nama_wil_sup'];

            $r['tingkat']     = $d['tingkat'];

            $r['susulan']     = $d['susulan'];
            $r['susulan_tgl']     = $d['susulan_tgl'];

            $r['idPu']        = $d['idPu'];
            $r['tanggal']        = $d['tanggal'];
            $r['mulai']         = $d['mulai'];
            $r['selesai']            = $d['selesai'];
            $r['nilai_akhir']          = $d['nilai_akhir'];
            $r['n']          = $d['n'];
            $r['n_pk']          = $d['n_pk'];
            $r['nilai_grade']          = $d['nilai_grade'];
            $r['remidi']          = $d['remidi'];


            $r['kkm']          = $d['kkm'];
            $r['tglUjian']          = $d['tglUjian'];
        
            $ar[]   = $r;
        }

        $cAr = $c-1;

        $nomor = $start+1;

        for ($i=0; $i <= $cAr; $i++) {
            $id = $ar[$i]['idP'];
            $id_ujian = $ar[$i]['id_ujian'];

            $idKar = $ar[$i]['idKar'];
            $nik = $ar[$i]['nik'];
            $nama = $ar[$i]['nama'];
            
            $jk = strtolower($ar[$i]['jk']);
            if($jk!=='l'&&$jk!=='p'){
                $jk = 'n';
            }

            $tgl_lahir = $ar[$i]['tgl_lahir'];
            $infoUmur = '-';
            if(!empty($tgl_lahir) && $tgl_lahir !== '' && $tgl_lahir !== '0000-00-00'){
                $infoUmur = hitungUmur($tgl_lahir);
                if($sekarang == $tgl_lahir){
                    $infoUmur .= '<span class="ui pink label">ULTAH!</span>';
                }
            }
            
            $jenis = $ar[$i]['jenis'];

            $nama_wil = $ar[$i]['nama_wil'];
            $nama_wil_sup = $ar[$i]['nama_wil_sup'];
            $infoTingkat = $jenis;
            if(!empty($nama_wil_sup)){
                if($nama_wil_sup!==$nama_wil){
                    $infoTingkat .= ' - '.$nama_wil_sup;
                }
            }

            if($nama_wil==''){
                $nama_wil = '-';
            }
            
            $foto = $ar[$i]['foto'];
            $avatar = '../files/photo/'.$jk.'.png';
            if(!empty($foto) && $foto !== ''){
                $foto = str_replace('%20', ' ', $foto);
                if(file_exists('../../'.$foto)){
                    $avatar = '../'.$foto;
                }
            }          

            $tgl_masuk = $ar[$i]['tgl_masuk'];
            $lamaKerja = '-';
            if(!empty($tgl_masuk) && $tgl_masuk !== '' && $tgl_masuk !== '0000-00-00'){
                $lamaKerja = hitungUmur($tgl_masuk);
            }

            $tingkat = $ar[$i]['tingkat'];
            
            $susulan = $ar[$i]['susulan'];
            $susulan_tgl = $ar[$i]['susulan_tgl'];

            $idPengerjaan = $ar[$i]['idPu'];
            $tanggal = $ar[$i]['tanggal'];
            $mulai = $ar[$i]['mulai'];
            $selesai = $ar[$i]['selesai'];
            

            $nilai_akhir = $ar[$i]['nilai_akhir'];
            $n = $ar[$i]['n'];
            $n_pk = $ar[$i]['n_pk'];
            $nilai_grade = $ar[$i]['nilai_grade'];
            $remidi = $ar[$i]['remidi'];

            $score = $nilai_akhir.' (PK = '.$n_pk.') -> '.$nilai_grade;

            $kkm = $ar[$i]['kkm'];

            if($nilai_akhir < $kkm){
                $warnaNilai = 'orange';
            }
            else{
                $warnaNilai = 'teal';
            }

            $classTr = '';

            if($id_ujian=='5483-8ABF1C'){
                if($nilai_akhir < $kkm){
                    $classTr = 'negative';
                }
            }


            
            $tglUjian = $ar[$i]['tglUjian'];

            if($foto==''){
            	$avatar = '../files/photo/'.$jk.'.png';
            }
            else{
                $foto = str_replace('%20', ' ', $foto);
            	if(file_exists('../../'.$foto)){
	            	$avatar = '../'.$foto;
	            }
	            else{
	            	$avatar = '../files/photo/'.$jk.'.png';
	            }
            }


            $idHapus = $id_pelaksanaan.'[pisah]'.$id.'[pisah]'.$idKar;
            
?>
			<tr>
				<td><?php echo $nomor; ?></td>
				<td>
					<h4 class="ui image header">
                        <img src="<?php echo $avatar; ?>" class="ui mini rounded image">
                        <div class="content">
                            <?php echo $nik.' - '.$nama; ?>
                            <div class="sub header">
                                <p>Wilayah : <?php echo $nama_wil .'<br>Jenis : '.$infoTingkat.'<br>Umur : '.$infoUmur.'<br>Lama Bekerja : '.$lamaKerja; ?></p>
                            </div>
                        </div>
                    </h4>
				</td>
                <td>
<?php
    if($susulan=='1'){
?>
                    <span class="ui orange label">
                        Susulan
                    </span><br>
<?php        
        if($susulan_tgl<$sekarang){
            if($tanggal==''){
                //susulan waktu terlewat belum mengerjakan
?>
                <span class="ui red label">Belum</span>
<?php                                     
            }
            else{
                //susulan waktu terlewat sudah mengerjakan
                echo "Pada ".tanggalKan($tanggal).' ('.$mulai.' - '.$selesai.')<br><br>';
?>
                    <span class="ui label <?php echo $warnaNilai; ?>"><?php echo $score; ?></span>
<?php           
            }
        }
        else{
            if($tanggal==''){
                //belum mengerjakan waktu masih ada.
                echo "Pada ".tanggalKan($susulan_tgl);
            }
            else{
                //sudah mengerjakan
                echo "pada ".tanggalKan($tanggal).' ('.$mulai.' - '.$selesai.')<br><br>';
?>
                    <span class="ui label <?php echo $warnaNilai; ?>"><?php echo $score; ?></span>
<?php           
            }
        }
    }
    else{
        //tidak susulan
        if($sekarang>=$tglUjian){
            //tisak susulan belum mengerjakan
            if($tanggal==''){
?>
                <span class="ui red label">Belum</span>
<?php                                     
            }
            else{
                //tidak susulan sudah mengerjakan
                echo "pada ".tanggalKan($tanggal).' ('.$mulai.' - '.$selesai.')<br><br>';
?>
                <span class="ui label <?php echo $warnaNilai; ?>"><?php echo $score; ?></span>
<?php           
            }
        }
    }
?>                    
                </td>
				<td>
<?php
    if($susulan=='1'){
        if($susulan_tgl<$sekarang){
            if($tanggal==''){
                //susulan dan masih belum mengerjakan
?>
                <div id="mainBtnBox<?php echo $id; ?>" >
                    <div class="ui icon button" data-content="Atur ulang tanggal" onclick="reSusulan('<?php echo $id; ?>')">
                        <i class="calendar outline alternate icon"></i>
                    </div>
                    <div class="ui icon button red" data-content="Hapus" onclick="tampilkanKonfirmasi('<?php echo $idHapus; ?>','Hapus data','Yakin ingin menghapus data peserta ujian ?<br><br><br>Jika peserta telah mengikuti ujian, data pengerjaan ujian juga akan dihapus','interface/test-manage-form-participant-delete.php')">
                        <i class="trash alternate icon"></i>
                    </div>
                </div>
                
                <div id="opsiReSusulan<?php echo $id; ?>" style="display: none;" class="ui vertical basc segment clearing">
                    <div class="ui icon button" data-content="Batal" onclick="batalReSusulan('<?php echo $id; ?>')">
                        <i class="left chevron icon"></i>
                    </div>
                    <div class="ui calendar reSusulan">
                        <div class="ui input left icon">
                            <i class="calendar outline icon"></i>
                            <input type="text" id="tglReSusulan<?php echo $id; ?>" placeholder="YYYY-MM-DD" name="tgl_susulan" id="tgl_susulan" value="" />
                        </div>
                    </div>
                    <div class="ui icon teal button" data-content="Simpan" onclick="simpanReSusulan('<?php echo $id; ?>')">
                        <i class="save icon"></i>
                    </div>
                </div>
<?php
            }
            else{
                //susulan sudah mengerjakan
?>
                <div class="ui icon button orange" data-content="Reset" onclick="tampilkanKonfirmasi('<?php echo $idPengerjaan; ?>','Reset pengerjaan','Yakin ingin mereset hasil pengerjaan dari peserta dengan NIK <strong><?php echo $nik; ?></strong> ?<br><br><br>Peserta harus mengikuti ujian lagi untuk mendapatkan nilai.','interface/test-manage-form-participant-reset.php')">
                    <i class="retweet icon"></i>
                </div>
<?php                
            }
        }
        else if ($susulan_tgl>$sekarang){
            if($tanggal==''){
                //susulan belum mengerjakan masih ada waktu
            }
            else{
                //susulan sudah mengerjakan
?>
                <div class="ui icon button orange" data-content="Reset" onclick="tampilkanKonfirmasi('<?php echo $idPengerjaan; ?>','Reset pengerjaan','Yakin ingin mereset hasil pengerjaan dari peserta dengan NIK <strong><?php echo $nik; ?></strong> ?<br><br><br>Peserta harus mengikuti ujian lagi untuk mendapatkan nilai.','interface/test-manage-form-participant-reset.php')">
                    <i class="retweet icon"></i>
                </div>
<?php                
            }
        }
    }
    else{
        if($sekarang>=$tglUjian){
            if($tanggal==''){
                //normal belum mengerjakan
?>
                <div id="mainBtnBox<?php echo $id; ?>" >
                    <div class="ui icon button" data-content="Atur ulang tanggal (menjadi susulan)" onclick="reSusulan('<?php echo $id; ?>')">
                        <i class="calendar outline alternate icon"></i>
                    </div>
                    <div class="ui icon button red" data-content="Hapus" onclick="tampilkanKonfirmasi('<?php echo $idHapus; ?>','Hapus data','Yakin ingin menghapus data peserta ujian ?<br><br><br>Jika peserta telah mengikuti ujian, data pengerjaan ujian juga akan dihapus','interface/test-manage-form-participant-delete.php')">
                        <i class="trash alternate icon"></i>
                    </div>
                </div>
                
                <div id="opsiReSusulan<?php echo $id; ?>" style="display: none;" class="ui vertical basc segment clearing">
                    <div class="ui icon button" data-content="Batal" onclick="batalReSusulan('<?php echo $id; ?>')">
                        <i class="left chevron icon"></i>
                    </div>
                    <div class="ui calendar reSusulan">
                        <div class="ui input left icon">
                            <i class="calendar outline icon"></i>
                            <input type="text" id="tglReSusulan<?php echo $id; ?>" placeholder="YYYY-MM-DD" name="tgl_susulan" id="tgl_susulan" value="" />
                        </div>
                    </div>
                    <div class="ui icon teal button" data-content="Simpan" onclick="simpanReSusulan('<?php echo $id; ?>')">
                        <i class="save icon"></i>
                    </div>
                </div>
<?php
            }
            else{
                //normal sudah mengerjakan
?>
                <div class="ui icon button orange" data-content="Reset" onclick="tampilkanKonfirmasi('<?php echo $idPengerjaan; ?>','Reset pengerjaan','Yakin ingin mereset hasil pengerjaan dari peserta dengan NIK <strong><?php echo $nik; ?></strong> ?<br><br><br>Peserta harus mengikuti ujian lagi untuk mendapatkan nilai.','interface/test-manage-form-participant-reset.php')">
                    <i class="retweet icon"></i>
                </div>
<?php                
            }
        }
        else{
?>
            <div class="ui icon button red" data-content="Hapus" onclick="tampilkanKonfirmasi('<?php echo $idHapus; ?>','Hapus data','Yakin ingin menghapus data peserta ujian ?','interface/test-manage-form-participant-delete.php')">
                <i class="trash alternate icon"></i>
            </div>
<?php            
        }
    }
?>                    
				</td>
			</tr>
<?php    
            $nomor = $nomor+1;            
        }
    }
    else{
    	if($cari==''){
    		$teksKosong = 'Belum ada data.';
    	}
    	else{
    		$teksKosong = "Data dengan kata kunci <strong>".$cari."</strong> tidak ditemukan.";
    	}
?>
		<tr>
			<td colspan="4">
				<i class="info circle icon teal"></i> <i> <?php echo $teksKosong; ?> </i>
			</td>
		</tr>
<?php    	
    }

?>





<script type="text/javascript">
	$('.button, .popup').popup();

    $('.reSusulan').calendar({
        type: 'date',
        formatter:{
            date: function(date, setting){
                if (!date) return '';
                var day = ("0"+date.getDate()).slice(-2);
                var month = ("0"+(date.getMonth() + 1)).slice(-2);
                var year = date.getFullYear();
                return year + '-' + month + '-' + day;
            }
        }
    });
</script>