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

    $id_wil = '';
    if(!empty($_POST['id_wil'])){
        $id_wil = saring($_POST['id_wil']);
    }

    $sekarang = date('Y-m-d');
    $tgl    = saring($_POST['tgl']);

	if(is_numeric($start) AND is_numeric($limit)){
		$q="
            SELECT 
                k.id idK,
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

                p.masuk,
                p.keterangan,
                
                (   
                    SELECT 
                        COUNT(id) jml
                    FROM 
                        pelatihan_catatan
                    WHERE
                        id_periode = '$idPeriode'
                    AND
                        id_karyawan = idK
                    AND
                        tanggal = '$tgl'
                    AND
                        hapus = '0'
                ) jml

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

            LEFT JOIN
                pelatihan_presensi p
            ON
                k.id = p.id_karyawan
            AND
                p.id_periode = '$idPeriode'
            AND
                p.tanggal = '$tgl'

            WHERE
                k.hapus = '0'
		";

        if($id_wil!==''){
            $q.="
                AND
                    k.id_wil = '$id_wil'
            ";
        }

		if($cari!==''){
			$q.="
					AND 
					(
						k.nama LIKE '%$cari%'
					OR
						k.nik LIKE '%$cari%'
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
			<td colspan="6">
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

        $no = 0;

        $teksInfo = 'Menampilkan data';

        while ($d = mysqli_fetch_assoc($e)) {
            $r['idK']           = $d['idK'];
            
            $r['nik']           = $d['nik'];
            $r['nama']          = $d['nama'];
            $r['jk']            = $d['jk'];
            $r['tgl_lahir']     = $d['tgl_lahir'];
            $r['foto']          = $d['foto'];
            $r['tgl_masuk']          = $d['tgl_masuk'];

            $r['nama_wil']      = $d['nama_wil'];

            $r['jenis']         = $d['jenis'];
            $r['nama_wil_sup']         = $d['nama_wil_sup'];

            $r['jml']    = $d['jml'];

            $r['masuk']    = $d['masuk'];
            $r['keterangan']    = $d['keterangan'];

        
            $ar[]   = $r;

            if($no==0){
                if($id_wil!==''){
                    $teksInfo .= ', pada lingkup wilayah '.$r['nama_wil'];
                }
                else{
                    $teksInfo .= ' di semua wilayah';
                }

                if($cari!==''){
                    $teksInfo .= " dengan kata kunci pencarian ".$cari;
                }

                if($tgl!==$sekarang){
                    $teksInfo .= ', pada tanggal '.tanggalkan($tgl);
                }
                else{
                    $teksInfo .= ', pada hari ini';
                }


                $no = 1;
            }
        }

        $cAr = $c-1;

        $nomor = $start+1;
?>
        <tr>
            <td colspan="3" class="positive">
                <?php echo $teksInfo; ?>
            </td>
        </tr>
<?php        

        for ($i=0; $i <= $cAr; $i++) {
            $jml = $ar[$i]['jml'];
            if($jml=='0'){
                $warna = '';
            }
            else if($jml>0&&$jml<=3){
                $warna = 'teal';
            }
            else if($jml>3&&$jml<=6){
                $warna = 'orange';
            }
            else{
                $warna = 'pink';
            }


            $masuk = $ar[$i]['masuk'];
            $keterangan = $ar[$i]['keterangan'];
            
            $idKaryawan = $ar[$i]['idK'];
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
    if($masuk==''){
?>
                    <div id="trigBtn<?php echo $idKaryawan; ?>">
                        <div class="ui positive icon button" onclick="tampilkanKonfirmasi('<?php echo $idKaryawan; ?>[pisah]masuk[pisah]<?php echo $tgl; ?>', 'Set presensi', 'Yakin ingin set presensi pelatihan karyawan dengan NIK <strong><?php echo $nik; ?></strong> Masuk ?', 'interface/train-set-presensi.php')">
                            Masuk
                        </div>
                        <div class="ui negative icon button" onclick="triggerNeg('<?php echo $idKaryawan; ?>')">
                            Tidak
                        </div>
                    </div>
                    <div id="negBtn<?php echo $idKaryawan; ?>" style="display: none;">
                        <div class="ui icon button" onclick="triggerNeg('<?php echo $idKaryawan; ?>')">
                            <i class="left chevron icon"></i>
                        </div>
                        <div class="ui icon button black" onclick="tampilkanKonfirmasi('<?php echo $idKaryawan; ?>[pisah]sakit[pisah]<?php echo $tgl; ?>', 'Set presensi', 'Yakin ingin set presensi pelatihan karyawan dengan NIK <strong><?php echo $nik; ?></strong> Sakit ?', 'interface/train-set-presensi.php')">
                            Sakit
                        </div>
                        <div class="ui icon button yellow" onclick="tampilkanKonfirmasi('<?php echo $idKaryawan; ?>[pisah]Ijin[pisah]<?php echo $tgl; ?>', 'Set presensi', 'Yakin ingin set presensi pelatihan karyawan dengan NIK <strong><?php echo $nik; ?></strong> Ijin ?', 'interface/train-set-presensi.php')">
                            Ijin
                        </div>
                        <div class="ui icon button red" onclick="tampilkanKonfirmasi('<?php echo $idKaryawan; ?>[pisah]alpha[pisah]<?php echo $tgl; ?>', 'Set presensi', 'Yakin ingin set presensi pelatihan karyawan dengan NIK <strong><?php echo $nik; ?></strong> Alpha ?', 'interface/train-set-presensi.php')">
                            Alpha
                        </div>
                    </div>
<?php        
    }
    else{
        if($masuk=='1'){
?>
                    <div class="ui compact menu">
                        <div class="link item" onclick="loadForm('train-note','<?php echo $idKaryawan; ?>[pisah]<?php echo $tgl; ?>')">
                            <i class="icon edit"></i> Catatan
                            <div class="ui circular floating <?php echo $warna; ?> label">
                               <?php echo $jml; ?>
                           </div>
                        </div>
                    </div>                   
<?php
        }
        else{
            echo ucfirst($keterangan);
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