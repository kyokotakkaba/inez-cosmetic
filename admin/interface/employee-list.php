<?php
    session_start();
    $appSection = 'admin';

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

    $idPengguna = $_SESSION['idPengguna'];

    $q = "
            SELECT 
                ws.id, 
                ws.id_wilayah, 
                
                w.kode, 
                w.nama

            FROM 
                wilayah_supervisi ws

            LEFT JOIN
                wilayah w
            ON
                ws.id_wilayah = w.id

            WHERE
                ws.hapus  = '0'
            AND
                ws.id_karyawan = '$idPengguna'
            AND
                w.hapus = '0'

            LIMIT
                1
    ";
    $e = mysqli_query($conn, $q);
    $r = mysqli_fetch_assoc($e);
    $id_wil = $r['id_wilayah'];
	
	$start 	= saring($_POST['start']);
	$limit 	= saring($_POST['limit']);
	$cari 	= saring($_POST['cari']);

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
                k.id_wil = '$id_wil'
            AND
                k.id != '$idPengguna'
            AND
                k.hapus = '0'
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
            
            $r['nik']      	    = $d['nik'];
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
            $idData = $ar[$i]['id'];
            
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
                                <p><?php echo 'Umur : '.$infoUmur.'<br>Lama Bekerja : '.$lamaKerja; ?></p>
							</div>
						</div>
					</h4>
				</td>
				<td>
                    <div class="ui icon button teal" data-content="Reset" onclick="tampilkanKonfirmasi('<?php echo $idData; ?>','Reset Akun','Yakin ingin mereset akun karyawan ? <br><br>*Password menjadi sama seperti <strong>Nomor Induk Karyawan</strong>','interface/employee-reset-pass.php')">
                        <i class="retweet icon"></i>
                    </div>
					<div class="ui icon button" data-content="Edit" onclick="loadForm('employee','<?php echo $idData; ?>')">
	                    <i class="pencil alternate icon"></i>
	                </div>
                    <div class="ui icon button primary" data-content="Detail" onclick="loadForm('employee-history','<?php echo $idData; ?>')">
                        <i class="server icon"></i>
                    </div>
	                <div class="ui icon button red" data-content="Hapus" onclick="tampilkanKonfirmasi('<?php echo $idData; ?>','Hapus data','Yakin ingin menghapus data karyawan ?','interface/employee-delete.php')">
	                    <i class="trash alternate icon"></i>
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
	$('.button, .popup').popup();
</script>