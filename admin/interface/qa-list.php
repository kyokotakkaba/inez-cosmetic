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
	
	$start 	= saring($_POST['start']);
	$limit 	= saring($_POST['limit']);
	$cari 	= saring($_POST['cari']);

    $id_kelompok   = saring($_POST['id_kelompok']);
    $status   = saring($_POST['status']);

	if(is_numeric($start) AND is_numeric($limit)){
		$q="
			SELECT 
                tj.id,
                tj.id_karyawan,
                tj.pertanyaan, 
                tj.jawaban,
                tj.tanya_pada,
                tj.tanya_jam,
                tj.jawab_pada,
                tj.jawab_jam,
                tj.id_penjawab,
                kel.nama kelompok,

                kar.nama karyawan,
                kar.jk jkKaryawan,
                kar.foto fotoKaryawan,
                pen.nama penjawab,
                pen.jk jkPenjawab,
                pen.foto fotoPenjawab
                
            FROM 
                tanya_jawab tj

            LEFT JOIN
                materi_kelompok kel
            ON
                tj.id_kelompok = kel.id

            LEFT JOIN
                karyawan kar
            ON
                tj.id_karyawan = kar.id

            LEFT JOIN
                root pen
            ON
                tj.id_penjawab = pen.id
        
            WHERE
                tj.hapus = '0'
        ";

        if($cari!==''){
            $q.="
                    AND 
                    (
                        tj.pertanyaan LIKE '%$cari%'
                    OR
                        tj.jawaban LIKE '%$cari%'
                    OR
                        kar.nama LIKE '%$cari%'
                    OR
                        pen.nama LIKE '%$cari%'
                    )
            ";
        }

        if($status=='terjawab'){
            $q.="
                    AND
                        tj.id_penjawab != ''
            ";
        }
        else if($status=='belum'){
            $q.="
                    AND
                        tj.id_penjawab = ''
            ";
        }

        if($id_kelompok!=='semua'){
            $q.="
                    AND
                        tj.id_kelompok = '$id_kelompok'
            ";
        }

        $q.="   
                ORDER BY 
                    tj.tanya_pada DESC,
                    tj.tanya_jam DESC,
                    tj.jawab_pada DESC,
                    tj.jawab_jam DESC
                    
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
            $r['id_karyawan']            = $d['id_karyawan'];
            $r['pertanyaan']        = $d['pertanyaan'];
            $r['jawaban']          = $d['jawaban'];
            $r['tanya_pada']      	    = $d['tanya_pada'];
            $r['tanya_jam']            = $d['tanya_jam'];
            $r['jawab_pada']          = $d['jawab_pada'];
            $r['jawab_jam']          = $d['jawab_jam'];
            $r['id_penjawab']          = $d['id_penjawab'];
            $r['kelompok']            = $d['kelompok'];
            $r['karyawan']    = $d['karyawan'];
            $r['jkKaryawan']    = $d['jkKaryawan'];
            $r['fotoKaryawan']    = $d['fotoKaryawan'];
            $r['penjawab']     = $d['penjawab'];
            $r['jkPenjawab']    = $d['jkPenjawab'];
            $r['fotoPenjawab']    = $d['fotoPenjawab'];
            
            $ar[]   = $r;
        }

        $cAr = $c-1;

        $nomor = $start+1;
?>
        <h3 class="ui dividing header">Pertanyaan</h3>
<?php
        for ($i=0; $i <= $cAr; $i++) {
            $idData = $ar[$i]['id'];
            $id_karyawan = $ar[$i]['id_karyawan'];
            $pertanyaan = $ar[$i]['pertanyaan'];
            $jawaban = $ar[$i]['jawaban'];
            $tanya_pada = $ar[$i]['tanya_pada'];
            $tanya_jam = $ar[$i]['tanya_jam'];
            $jawab_pada = $ar[$i]['jawab_pada'];
            $jawab_jam = $ar[$i]['jawab_jam'];
            $id_penjawab = $ar[$i]['id_penjawab'];
            $kelompok = $ar[$i]['kelompok'];
            

            if($id_karyawan == $idPengguna){
                $karyawan = 'Anda';
            }
            else{
                $karyawan = $ar[$i]['karyawan'];
                if(empty($karyawan)){
                    $karyawan = 'Penanya';
                }
            }
            $jkKaryawan = $ar[$i]['jkKaryawan'];
            if($jkKaryawan == ''){
                $jkKaryawan = 'n';
            }
            $urlFotoKaryawan = $ar[$i]['fotoKaryawan'];

            $fotoKaryawan = '../files/photo/'.$jkKaryawan.'.png';
            if(!empty($urlFotoKaryawan) && $urlFotoKaryawan !==''){
                $urlFotoKaryawan = str_replace('%20', ' ', $urlFotoKaryawan);
                if(file_exists('../../'.$urlFotoKaryawan)){
                    $fotoKaryawan = '../'.$urlFotoKaryawan;
                }
            }

            
            $id_penjawab = $ar[$i]['id_penjawab'];
            $penjawab = $ar[$i]['penjawab'];
            $jkPenjawab = $ar[$i]['jkPenjawab'];
            if($jkPenjawab==''){
                $jkPenjawab = 'n';
            }
            $urlFotoPenjawab = $ar[$i]['fotoPenjawab'];
            $fotoPenjawab = '../files/photo/'.$jkPenjawab.'.png';
            if(!empty($urlFotoPenjawab) && $urlFotoPenjawab !==''){
                $urlFotoPenjawab = str_replace('%20', ' ', $urlFotoPenjawab);
                if(file_exists('../../'.$urlFotoPenjawab)){
                    $fotoPenjawab = '../'.$urlFotoPenjawab;
                }
            }
?>
            <div class="comment">
                <a class="avatar">
                    <img src="<?php echo $fotoKaryawan; ?>">
                </a>
                <div class="content">
                    <a class="author"><?php echo $karyawan; ?></a>
                    <div class="metadata">
                        <span class="date"><?php echo tanggalKan($tanya_pada).' &nbsp; '.$tanya_jam; ?></span>
                    </div>
                    <div class="text">
                        <?php echo html_entity_decode($pertanyaan); ?>
                    </div>
                    
<?php
    if($id_kelompok=='semua'){
?>
                    <div class="ui tiny label">
                        <?php echo $kelompok; ?>
                    </div>
<?php        
    }
            if($penjawab ==''){
                if($id_karyawan==$idPengguna){
?>
                    <div class="actions" id="action<?php echo $idData; ?>" style="margin-top:6px;">
                        <a onclick="loadForm('qa','<?php echo $idData; ?>')">
                            <i class="pencil alternate icon"></i> Edit
                        </a>
                        <a onclick="tampilkanKonfirmasi('<?php echo $idData; ?>','Hapus data','Yakin ingin menghapus data pertanyaan yang telah diajukan ?','interface/qa-delete.php')">
                            <i class="trash alternate icon"></i> Hapus
                        </a>
                    </div>
<?php                
                }
            }
            else{
?>
                    <div class="comments">
                        <div class="comment">
                            <a class="avatar">
                                <img src="<?php echo $fotoPenjawab; ?>">
                            </a>
                            <div class="content">
                                <a class="author"><?php echo $penjawab ?></a>
                                <div class="metadata">
                                    <span class="date"><?php echo tanggalKan($jawab_pada).' &nbsp; '.$jawab_jam; ?></span>
                                </div>
                                <div class="text">
                                    <?php echo html_entity_decode($jawaban); ?>
                                </div>
                            </div>
                        </div>
                    </div>
<?php                
            }
?>                    
                </div>
            </div>
<?php    
            $nomor = $nomor+1;            
        }
    }
    else{
    	if($cari==''){
    		$teksKosong = 'Belum ada data.';
    	}
    	else{
    		$teksKosong = "Data dengan kata kunci <strong>".$cari."</strong> tidak ditemukan. ";
    	}
?>
		<i class="info circle icon teal"></i> <i> <?php echo $teksKosong; ?> </i>
<?php    	
    }

?>





<script type="text/javascript">
	$('.button, .popup').popup();
</script>