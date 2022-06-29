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

    $idB   = saring($_POST['lastId']);

	if(is_numeric($start) AND is_numeric($limit)){
		$q="
			SELECT 
                m.id, 
                m.no, 
                m.judul, 
                m.deskripsi, 
                m.baner, 
                m.kode,
                m.id_tingkat_belajar,

                tb.nama tingkat
            FROM
                materi m
                
            LEFT JOIN
                tingkat_belajar tb
            ON
                m.id_tingkat_belajar = tb.id

            WHERE
                m.id_bahasan = '$idB'
            AND
                m.hapus = '0'
		";

		if($cari!==''){
			$q.="
					AND 
					(
						m.judul LIKE '%$cari%'
					OR
						m.deskripsi LIKE '%$cari%'
					OR
						tb.nama LIKE '%$cari%'
					)
			";
		}

		$q.="	
				ORDER BY 
					m.no ASC

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
            $r['no']           = $d['no'];
            $r['judul']      	= $d['judul'];
            $r['deskripsi']           = $d['deskripsi'];
            $r['baner']           = $d['baner'];
            $r['kode']           = $d['kode'];

            $r['id_tingkat_belajar']           = $d['id_tingkat_belajar'];
            $r['tingkat']           = $d['tingkat'];
        
            $ar[]   = $r;
        }

        $cAr = $c-1;

        $nomor = $start+1;

        $qB = "
                SELECT
                    id

                FROM
                    materi

                WHERE
                    id_bahasan = '$idB'
        ";
        $eB = mysqli_query($conn, $qB);
        $cB = mysqli_num_rows($eB);

        $bebas = $cB+1;

        for ($i=0; $i <= $cAr; $i++) {
            $idData = $ar[$i]['id'];
            $no = $ar[$i]['no'];
            
            $judul = $ar[$i]['judul'];
            $deskripsi = $ar[$i]['deskripsi'];
            $banerUrl = $ar[$i]['baner'];
            $baner = '../files/photo/agenda.png';
            if(!empty($banerUrl) && $banerUrl!==''){
                // $buku1Url == str_replace('%20', ' ', $buku1Url);
                $headers = @get_headers($banerUrl);
                $checkFile = strpos($headers[0],'200');
                if($checkFile){
                    $baner = $banerUrl;
                }  
            }
            // if(!empty($banerUrl) && $banerUrl!==''){
            //     $banerUrl = str_replace('%20', ' ', $banerUrl);
            //     if(file_exists('../../'.$banerUrl)){
            //         $baner = '../'.$banerUrl;
            //     }
            // }

            $kode = $ar[$i]['kode'];

            $id_tingkat_belajar = $ar[$i]['id_tingkat_belajar'];
            if($id_tingkat_belajar=='semua'){
                $tingkat = 'Semua';
            }
            else{
                $tingkat = $ar[$i]['tingkat'];
            }
            

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
                        <img src="<?php echo $baner; ?>">
                        <div class="content">
                            <?php echo $judul; ?>
                            <div class="sub header">
                                <?php echo $deskripsi; ?>
                            </div>
                        </div>
					</h4>
					<div class="ui tiny blue label">
						Tingkat : <?php echo $tingkat; ?>
					</div>
				</td>
				<td>
<?php
    if($c>1){
?>
                        <div class="ui icon button <?php echo $classPrev; ?>" data-content="Majukan" onclick="reposisiData('<?php echo $idB; ?>[pisah]<?php echo $no; ?>','<?php echo $sasarPrev; ?>', '<?php echo $bebas; ?>', 'interface/kuri-bahasan-materi-reposition.php')">
                            <i class="up chevron icon"></i>
                        </div>
                        <div class="ui icon button <?php echo $classNext; ?>" data-content="Mundurkan" onclick="reposisiData('<?php echo $idB; ?>[pisah]<?php echo $no; ?>','<?php echo $sasarNext; ?>', '<?php echo $bebas; ?>', 'interface/kuri-bahasan-materi-reposition.php')">
                            <i class="down chevron icon"></i>
                        </div>
<?php        
    }
?>					
					<a class="ui icon teal button" data-content="preview" href="preview/materi/?kode=<?php echo $kode; ?>" target="_blank">
	                    <i class="external link icon"></i>
	                </a>
					<div class="ui icon button" data-content="Edit" onclick="loadFormSub('isi','<?php echo $idB; ?>[pisah]<?php echo $idData; ?>')">
	                    <i class="pencil alternate icon"></i>
	                </div>
	                <div class="ui icon button red" data-content="Hapus" onclick="tampilkanKonfirmasi('<?php echo $idData; ?>','Hapus data','Yakin ingin menghapus data materi ?<br><br><br>Data riwayat karyawan belajar materi terkait juga akan dihapus','interface/kuri-bahasan-materi-delete.php')">
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