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
                p.id, 
                p.isi,

                k.nama kelompok,
                k.n_pk,

                (
                    SELECT
                        id

                    FROM
                        jawaban 
                    WHERE
                        id_pertanyaan = p.id
                    AND
                        isi = ''

                    LIMIT
                        1
                ) adaJKosong

            FROM 
                pertanyaan p

            LEFT JOIN
                materi_kelompok k
            ON
                p.id_kelompok = k.id

            LEFT JOIN
                materi_kelompok_bahasan b
            ON
                p.id_bahasan = b.id

            LEFT JOIN
                materi m
            ON
                p.id_materi = m.id

            WHERE
                p.hapus = '0'
		";

		if($cari!==''){
			$q.="
					AND 
					(
						p.isi LIKE '%$cari%'
					OR
						k.nama LIKE '%$cari%'
                    OR
                        b.nama LIKE '%$cari%'
                    OR
                        m.judul LIKE '%$cari%'
					)
			";
		}

		$q.="	
				ORDER BY 
					p.isi ASC
                    
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
            $r['id']           = $d['id'];
            $r['isi']      	    = $d['isi'];
            $r['kelompok']           = $d['kelompok'];
            $r['n_pk']           = $d['n_pk'];

            $r['adaJKosong']           = $d['adaJKosong'];
            
            $ar[]   = $r;
        }

        $cAr = $c-1;

        $nomor = $start+1;

        for ($i=0; $i <= $cAr; $i++) {
            $idData = $ar[$i]['id'];
            $isi = trim($ar[$i]['isi']);
            $kelompok = $ar[$i]['kelompok'];
            $n_pk = $ar[$i]['n_pk'];
            $label = $kelompok;
            $warna = '';
            if($n_pk=='1'){
                $label .= ' - PK';
                $warna = 'blue';
            }

            $classTr = '';

            if($isi=='' || empty($isi)){
                $classTr = 'negative';
            }

            $adaJKosong = $ar[$i]['adaJKosong'];

            if($adaJKosong=='1'){
                $classTr = 'negative';
            }
?>
			<tr class="<?php echo $classTr; ?>">
				<td><?php echo $nomor; ?></td>
				<td>
					<?php echo html_entity_decode($isi) ?>
                    <span class="ui tiny label <?php echo $warna; ?>"><?php echo $label; ?></span>
				</td>
				<td>
                    <div class="ui icon button" data-content="Edit" onclick="loadForm('bank','<?php echo $idData; ?>')">
                        <i class="pencil alternate icon"></i>
                    </div>
                    <div class="ui icon button red" data-content="Hapus" onclick="tampilkanKonfirmasi('<?php echo $idData; ?>','Hapus data','Yakin ingin menghapus data pertanyaan ?','interface/bank-delete.php')">
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