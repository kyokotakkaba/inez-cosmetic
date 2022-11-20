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
        if(substr($_SESSION['idPengguna'],0,3)!="X-0"){
		$q="
			SELECT 
				b.id idB,
				b.id_kelompok idK, 
				b.no, 
				b.nama namaB, 
				b.deskripsi,

				k.nama namaK,

                (
                    SELECT
                        COUNT(id)

                    FROM
                        materi

                    WHERE
                        id_bahasan = b.id
                    AND
                        hapus = '0'
                ) jmlMateri

            FROM
            	materi_kelompok_bahasan b

            LEFT JOIN
            	materi_kelompok k
            ON
            	b.id_kelompok = k.id

            WHERE
                b.hapus = '0'
		";
    }else{
        $q="
			SELECT 
				b.id idB,
				b.id_kelompok idK, 
				b.no, 
				b.nama namaB, 
				b.deskripsi,

				k.nama namaK,

                (
                    SELECT
                        COUNT(id)

                    FROM
                        materi

                    WHERE
                        id_bahasan = b.id
                    AND
                        hapus = '0'
                ) jmlMateri

            FROM
            	materi_kelompok_bahasan b

            LEFT JOIN
            	materi_kelompok k
            ON
            	b.id_kelompok = k.id

            WHERE
                b.hapus = '0'
            AND
                b.id = '069F6B-9768A'
		";
    }

		if($cari!==''){
			$q.="
					AND 
					(
						b.nama LIKE '%$cari%'
					OR
						b.deskripsi LIKE '%$cari%'
					OR
						b.k.nama LIKE '%$cari%'
					)
			";
		}

		$q.="	
				ORDER BY 
                    b.no ASC

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
            $r['idK']            = $d['idK'];
            $r['namaK']      	= $d['namaK'];
            
            $r['idB']           = $d['idB'];
            $r['no']           = $d['no'];
            $r['namaB']           = $d['namaB'];
            $r['deskripsi']           = $d['deskripsi'];

            $r['jmlMateri']           = $d['jmlMateri'];
        
            $ar[]   = $r;
        }

        $cAr = $c-1;

        $nomor = $start+1;

        $qB = "
                SELECT
                    id
                    
                FROM
                    materi_kelompok_bahasan
        ";
        $eB = mysqli_query($conn, $qB);
        $cB = mysqli_num_rows($eB);

        $bebas = $cB+1;

        for ($i=0; $i <= $cAr; $i++) {
            $idK = $ar[$i]['idK'];
            $namaK = $ar[$i]['namaK'];
            
            $idB = $ar[$i]['idB'];
            $no = $ar[$i]['no'];
            $namaB = $ar[$i]['namaB'];
            $deskripsi = $ar[$i]['deskripsi'];

            $jmlMateri = $ar[$i]['jmlMateri'];

            if($c>1){
                if($i==0){
                    $classPrev = 'disabled';
                    $classNext = '';

                    $sasarPrev = $no;
                    $n = $i+1;
                    $sasarNext = $ar[$n]['no'];
                }
                else if($i==$cAr){
                    $classPrev = '';
                    $classNext = 'disabled';

                    $sasarNext = $no;
                    $p = $i-1;
                    $sasarPrev = $ar[$p]['no'];
                }
                else if($i>0&&$i<$cAr){
                    $classPrev = '';
                    $classNext = '';

                    $sasarNext = $no;
                    $p = $i-1;
                    $n = $i+1;
                    $sasarPrev = $ar[$p]['no'];
                    $sasarNext = $ar[$n]['no'];
                }
            }
?>
			<tr>
				<td><?php echo $nomor; ?></td>
				<td>
					<h4 class="ui header">
						<?php echo $namaB; ?>
						<div class="sub header">
							<?php echo $deskripsi; ?>
						</div>
					</h4>
					<div class="ui tiny label">
						<?php echo $namaK; ?>
					</div>
                    <div class="ui tiny blue label">
                        Materi : <?php echo $jmlMateri; ?>
                    </div>
				</td>
				<td>
<?php
    if($c>1){
?>
                        <div class="ui icon button <?php echo $classPrev; ?>" data-content="Majukan" onclick="reposisiData('<?php echo $no; ?>', '<?php echo $sasarPrev; ?>', '<?php echo $bebas; ?>', 'interface/kuri-bahasan-reposition.php')">
                            <i class="up chevron icon"></i>
                        </div>
                        <div class="ui icon button <?php echo $classNext; ?>" data-content="Mundurkan" onclick="reposisiData('<?php echo $no; ?>', '<?php echo $sasarNext; ?>', '<?php echo $bebas; ?>', 'interface/kuri-bahasan-reposition.php')">
                            <i class="down chevron icon"></i>
                        </div>
<?php        
    }
?>					
					<div class="ui icon blue button" data-content="Kelola" onclick="loadForm('kuri-bahasan-materi','<?php echo $idB; ?>')">
	                    <i class="server icon"></i>
	                </div>
					<div class="ui icon button" data-content="Edit" onclick="loadForm('kuri-bahasan','<?php echo $idB; ?>')">
	                    <i class="pencil alternate icon"></i>
	                </div>
                    <?php if(substr($_SESSION['idPengguna'],0,3)!="X-0"){?>
	                <div class="ui icon button red" data-content="Hapus" onclick="tampilkanKonfirmasi('<?php echo $idB; ?>','Hapus data','Yakin ingin menghapus data bahasan ? <br><br><br>Data materi dan riwayat belajar karyawan terkait akan dihapus','interface/kuri-bahasan-delete.php')">
	                    <i class="trash alternate icon"></i>
	                </div>
                    <?php }?>
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