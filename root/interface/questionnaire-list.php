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

    $responden   = saring($_POST['responden']);

	if(is_numeric($start) AND is_numeric($limit)){
		$q="
			SELECT 
                a.id, 
                a.judul, 
                a.deskripsi, 
                a.responden,
                a.kode,
                a.aktif,

                p.nama produk,

                (
                    SELECT
                        COUNT(id)

                    FROM
                        angket_item

                    WHERE
                        id_angket = a.id
                    AND
                        hapus = '0'
                ) jmlItem

            FROM 
                angket a

            LEFT JOIN
                produk p
            ON
                a.id_produk = p.id

            WHERE
                a.hapus = '0'
		";

		if($cari!==''){
			$q.="
					AND 
					(
						a.judul LIKE '%$cari%'
					OR
						a.deskripsi LIKE '%$cari%'
                    OR
                        p.nama LIKE '%$cari%'
					)
			";
		}

        if($responden!=='semua'){
            $q.="
                    AND 
                        a.responden = '$responden'
            ";
        }

		$q.="	
				ORDER BY 
					a.judul ASC

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
            $r['id']            = $d['id'];
            $r['judul']      	= $d['judul'];
            $r['deskripsi']           = $d['deskripsi'];
            $r['responden']           = $d['responden'];
            $r['kode']           = $d['kode'];

            $r['produk']           = $d['produk'];

            $r['aktif']           = $d['aktif'];

            $r['jmlItem']           = $d['jmlItem'];
        
            $ar[]   = $r;
        }

        $cAr = $c-1;

        $nomor = $start+1;

        for ($i=0; $i <= $cAr; $i++) {
            $id = $ar[$i]['id'];
            $judul = $ar[$i]['judul'];
            $deskripsi = $ar[$i]['deskripsi'];
            $responden = $ar[$i]['responden'];

            if($responden=='admin'){
                $warnaLabel = 'blue';
            }
            else{
                $warnaLabel = 'grey';
            }

            $produk = $ar[$i]['produk'];

            $kode = $ar[$i]['kode'];
            $aktif = $ar[$i]['aktif'];

            $jmlItem = $ar[$i]['jmlItem'];

            $classActive = '';
            if($aktif == '0'){
                if($jmlItem == '0'){
                    $classActive = 'disabled';
                }
            }

            $ikon = 'ban';

            if($aktif=='1'){
                $ikon = 'check teal';
            }
?>
			<tr>
				<td><?php echo $nomor; ?></td>
				<td>
					<h4 class="ui header">
						<?php echo $judul; ?>
						<div class="sub header">
							<?php echo $deskripsi; ?><br>
                            Produk: <?php echo $produk; ?><br>
                            Responden: <?php echo $responden; ?>
						</div>
					</h4>
                    <div class="ui blue label">Item: <?php echo $jmlItem; ?></div>
				</td>
                <td>
                    <i class="<?php echo $ikon; ?> icon"></i>
                </td>
				<td>
<?php
    if($aktif=='1'){
?>
                    <a class="ui icon button" data-content="Laporan" href="report/survey/?kode=<?php echo $kode; ?>" target="_BLANK" >
                        <i class="print icon"></i>
                    </a>
<?php        
    }

    if($aktif=='0'){
?>
                    <div class="ui icon button <?php echo $classActive; ?>" data-content="Aktifkan" onclick="tampilkanKonfirmasi('<?php echo $id; ?>','Aktifkan survey','Yakin ingin mengaktifkan survey ?<br><br><br>Jika diaktifkan, angket tidak dapat dikelola (tambah, edit, hapus - item pertanyaan atau peryataan) lagi.','interface/questionnaire-activate.php')">
                        <i class="check teal icon"></i>
                    </div>
                    <div class="ui icon blue button" data-content="Kelola" onclick="loadForm('questionnaire-manage','<?php echo $id; ?>')">
                        <i class="server icon"></i>
                    </div>
<?php        
    }
?>   

                    <a class="ui icon teal button" data-content="preview" href="preview/survey/?kode=<?php echo $kode; ?>" target="_blank">
                        <i class="external link icon"></i>
                    </a>
					<div class="ui icon button" data-content="Edit" onclick="loadForm('questionnaire','<?php echo $id; ?>')">
	                    <i class="pencil alternate icon"></i>
	                </div>
	                <div class="ui icon button red" data-content="Hapus" onclick="tampilkanKonfirmasi('<?php echo $id; ?>','Hapus data','Yakin ingin menghapus data angket ?','interface/questionnaire-delete.php')">
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