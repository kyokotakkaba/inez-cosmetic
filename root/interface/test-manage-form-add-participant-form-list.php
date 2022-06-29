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

    $id_pelaksanaan   = saring($_POST['id_pelaksanaan']);

    $iniHari = date('Y-m-d');

	if(is_numeric($start) AND is_numeric($limit)){
		$q="
            SELECT 
                k.id,
                k.nik, 
                k.nama, 
                k.jk, 
                k.tgl_lahir, 
                k.tingkat,
                k.foto,
                k.tgl_masuk,

                a.jenis,

                w.nama nama_wil,

                wsw.nama nama_wil_sup

            FROM 
                karyawan k

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
                k.hapus = '0'
            AND
                k.id NOT IN 
                    (
                        SELECT 
                            id_karyawan
                        FROM 
                            ujian_pelaksanaan_target_karyawan 
                        WHERE
                            id_pelaksanaan = '$id_pelaksanaan'
                        AND
                            hapus = '0'
                    )
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
			<td colspan="3">
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
            $r['id']            = $d['id'];
            
            $r['nik']           = $d['nik'];
            $r['nama']          = $d['nama'];
            $r['jk']            = $d['jk'];
            $r['tgl_lahir']     = $d['tgl_lahir'];
            $r['foto']          = $d['foto'];
            $r['tgl_masuk']          = $d['tgl_masuk'];

            $r['nama_wil']      = $d['nama_wil'];

            $r['jenis']         = $d['jenis'];
            $r['nama_wil_sup']         = $d['nama_wil_sup'];

        
            $ar[]   = $r;
        }

        $cAr = $c-1;

        $nomor = $start+1;

        for ($i=0; $i <= $cAr; $i++) {
            $idK = $ar[$i]['id'];
            
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
                if($iniHari == $tgl_lahir){
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
                    <div class="ui toggle checkbox chkCalon">
                        <input type="checkbox" name="id_calon[]" value="<?php echo $idK; ?>">
                    </div>
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
			<td colspan="3">
				<i class="info circle icon teal"></i> <i> <?php echo $teksKosong; ?> </i>
			</td>
		</tr>
<?php    	
    }

?>





<script type="text/javascript">
<?php
    if($c>0){
?>
        $('#chkAllInPage').removeClass('disabled');
        $('.button, .popup').popup();
        $('.chkCalon').checkbox({
            onChecked: function() {
                var jml, baru, lama, all;
                lama = $('#calonTerpilih').val();
                jml = parseInt(lama);
                baru = 1+jml;
                all = <?php echo $c; ?>;
                $('#calonTerpilih').val(baru);
                if(baru>0&&baru<all){
                    $('#chkAllInPage').checkbox('set indeterminate');
                    $('#btnSetPeserta').removeClass('disabled');
                }
                else if(baru==all){
                    $('#chkAllInPage').checkbox('set checked');
                    $('#btnSetPeserta').removeClass('disabled');
                }
            },
            onUnchecked: function() {
                var jml, baru, lama, all;
                lama = $('#calonTerpilih').val();
                jml = parseInt(lama);
                baru = jml-1;
                all = <?php echo $c; ?>;
                $('#calonTerpilih').val(baru);
                if(baru>0&&baru<all){
                    $('#chkAllInPage').checkbox('set indeterminate');
                    $('#btnSetPeserta').removeClass('disabled');
                }
                else if(baru==0){
                    $('#chkAllInPage').checkbox('set unchecked');
                    $('#btnSetPeserta').addClass('disabled');
                }
            }
        });
<?php
    }
    else{
?>
        $('#chkAllInPage').addClass('disabled');
        $('#btnSetPeserta').addClass('disabled');
<?php        
    }
?>
</script>