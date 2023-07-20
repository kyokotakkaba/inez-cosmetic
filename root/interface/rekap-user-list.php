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

    $idPengguna = $_SESSION['idPengguna'];
	
	$start 	= saring($_POST['start']);
	$limit 	= saring($_POST['limit']);
    $cari 	= saring($_POST['cari']);

    $iniHari = date('Y-m-d');
    
	if(is_numeric($start) AND is_numeric($limit)){
        
        $qTotalMateri ="
        SELECT 
            materi_kelompok_bahasan.no,
            materi.id_bahasan,
            COUNT(*) as jumlah_materi 
        FROM 
            materi
        LEFT JOIN
            materi_kelompok_bahasan
        ON 
            materi_kelompok_bahasan.id = materi.id_bahasan
        WHERE 
            materi.hapus = 0 
        GROUP BY 
            materi.id_bahasan
        ORDER BY
            materi_kelompok_bahasan.no ASC
        ";
        $eTotalMateri = mysqli_query($conn, $qTotalMateri);
        $cTotalMateri = mysqli_num_rows($eTotalMateri);
        $arrTotalMateri = array();
        if($cTotalMateri>0){
          while ($dTotalMateri = mysqli_fetch_assoc($eTotalMateri)) {
              $arrTotalMateri[$dTotalMateri['no']] = $dTotalMateri['jumlah_materi'];
          }
        }

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

        // new
        $qMateri="
        SELECT 
            id,
            no     
        FROM 
            `materi_kelompok_bahasan` 
        WHERE 
            `hapus` = 0
        ORDER BY 
            `no` ASC
            ";

       $eMateri = mysqli_query($conn, $qMateri);
       $cMateri = mysqli_num_rows($eMateri);

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
						<div class="content">
							<?php echo $nik.' - '.$nama; ?>
						</div>
					</h4>
				</td>
                <?php
                  $qBaca ="
                    SELECT 
                        karyawan_belajar_materi.id_bahasan,
                        karyawan_belajar_materi.id_materi,
                        materi_kelompok_bahasan.no
               
                    FROM 
                        karyawan_belajar_materi
                    LEFT JOIN
                        materi_kelompok_bahasan
                    ON 
                        materi_kelompok_bahasan.id = karyawan_belajar_materi.id_bahasan
                    WHERE
                        karyawan_belajar_materi.hapus = '0'
                    AND
                        karyawan_belajar_materi.id_karyawan = '$idData'
                    GROUP BY 
                        `id_materi`  
                    ORDER BY 
                        materi_kelompok_bahasan.no ASC
                  ";
                  $eBaca = mysqli_query($conn, $qBaca);
                  $cBaca = mysqli_num_rows($eBaca);
                  $arrMateri = array();
                  if($cMateri>0){
                    while ($dMateri = mysqli_fetch_assoc($eMateri)) {
                        $arrMateri[] = $dMateri['no'];
                    }
                    // Seek to row number 1
                    mysqli_data_seek($eMateri,0);
                  }

                  if($cBaca>0){
                    $arrBaca = array();
                    while ($dBaca = mysqli_fetch_assoc($eBaca)) {
                        if (array_key_exists($dBaca['no'],$arrBaca)){
                            $arrBaca[$dBaca['no']]= $arrBaca[$dBaca['no']] +1;
                        }
                        else{
                            $arrBaca[$dBaca['no']] = 1;
                        }
                        
                    }
                    foreach ($arrMateri as $vMateri) {
                        if (array_key_exists($vMateri,$arrBaca)){
                            if($arrBaca[$vMateri] < $arrTotalMateri[$vMateri]){
                                echo "<td style='color:red'>".$arrBaca[$vMateri]."/".$arrTotalMateri[$vMateri]."</td>";
                            }else{
                                echo "<td style='color:green'>".$arrBaca[$vMateri]."/".$arrTotalMateri[$vMateri]."</td>";
                            }
                            
                        }else{
                            echo "<td style='color:red'>0/".$arrTotalMateri[$vMateri]."</td>";
                            
                        }    
                    }
                  }
                ?>
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
				<i class="info circle icon teal"></i> <i> <?php  echo $teksKosong; ?> </i>
			</td>
		</tr>
<?php    	
    }

?>





<script type="text/javascript">
	$('.button, .popup').popup();
</script>