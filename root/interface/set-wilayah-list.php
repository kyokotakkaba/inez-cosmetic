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

	if(is_numeric($start) AND is_numeric($limit)){
		$q="
            SELECT 
                w.id idWil, 
                w.nama nmWil, 
                w.kode,

                ws.id idSup,
                ws.id_karyawan idKar,

                k.nik,
                k.nama,
                k.jk,
                k.tmpt_lahir,
                k.tgl_lahir,
                k.foto,

                wk.nama namaKelompok,
                wk.standar
                
            FROM 
                wilayah w
            
            LEFT JOIN
                wilayah_supervisi ws
            ON
                w.id = ws.id_wilayah
            AND
                ws.hapus = '0'
            
            LEFT JOIN 
                karyawan k
            ON
                ws.id_karyawan = k.id
            AND
                k.hapus = '0'

            LEFT JOIN
                wilayah_kelompok wk
            ON
                w.id_kelompok = wk.id
            AND
                wk.hapus = '0'

            WHERE
            	w.hapus = '0'
		";

		if($cari!==''){
			$q.="
					AND 
					(
						w.nama LIKE '%$cari%'
					OR
						w.kode LIKE '%$cari%'
                    OR
                        k.nik LIKE '%$cari%'
					OR
						k.nama LIKE '%$cari%'
                    OR
                        wk.nama LIKE '%$cari%'
                    OR
                        wk.standar LIKE '%$cari%'
					)
			";
		}

		$q.="	
				ORDER BY 
					w.nama ASC
                    
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
            $r['idWil']            = $d['idWil'];
            $r['nmWil']        = $d['nmWil'];
            $r['kode']          = $d['kode'];

            $r['idSup']          = $d['idSup'];

            $r['idKar']           = $d['idKar'];
            $r['nik']      	    = $d['nik'];
            $r['nama']          = $d['nama'];
            $r['jk']            = $d['jk'];
            $r['tmpt_lahir']    = $d['tmpt_lahir'];
            $r['tgl_lahir']     = $d['tgl_lahir'];
            $r['foto']          = $d['foto'];

            $r['namaKelompok']          = $d['namaKelompok'];
            $r['standar']          = $d['standar'];

        
            $ar[]   = $r;
        }

        $cAr = $c-1;

        $nomor = $start+1;

        for ($i=0; $i <= $cAr; $i++) {
            $idWil = $ar[$i]['idWil'];
            $nmWil = $ar[$i]['nmWil'];
            $kode = $ar[$i]['kode'];

            $idSup = $ar[$i]['idSup'];

            $idKar = $ar[$i]['idKar'];
            $nik = $ar[$i]['nik'];
            $nama = $ar[$i]['nama'];
            
            $jk = strtolower($ar[$i]['jk']);
            if($jk!=='l'&&$jk!=='p'){
                $jk = 'n';
            }
            
            $tmpt_lahir = $ar[$i]['tmpt_lahir'];
            $tgl_lahir = $ar[$i]['tgl_lahir'];
            
            $foto = $ar[$i]['foto'];

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

            $namaKelompok = $ar[$i]['namaKelompok'];
            $standar = $ar[$i]['standar'];
            
?>
			<tr>
				<td><?php echo $nomor; ?></td>
				<td>
                    <h4 class="ui header">
                        <?php echo $kode; ?> 
                        <div class="sub header">
                            <?php echo $nmWil; ?><br>
                            Supervisor : 
<?php
    //echo '->'.$idSup.'<-';
    if(!empty($idSup)){
                            echo '<strong>'.$nik.' - '.$nama.'</strong>';
    }
    else{
?>
                    Belum ada
<?php        
    }
?>            
                            <br>
                            <?php if($namaKelompok!==''){ ?> <span class="ui label"><?php echo $namaKelompok.' ('.$standar.'%)'; ?></span> <?php } ?>
                        </div>
                    </h4>        
				</td>
				<td>
					<div class="ui icon button" data-content="Edit" onclick="loadForm('set-wilayah','<?php echo $idWil; ?>')">
	                    <i class="pencil alternate icon"></i>
	                </div>
	                <div class="ui icon button red" data-content="Hapus" onclick="tampilkanKonfirmasi('<?php echo $idWil; ?>','Hapus data','Yakin ingin menghapus data wilayah ?','interface/set-wilayah-delete.php')">
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