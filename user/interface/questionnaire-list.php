<?php
    session_start();
    $appSection = 'user';

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

    $idPengguna = $_SESSION['idPengguna'];

	if(is_numeric($start) AND is_numeric($limit)){
		$q="
			SELECT 
                a.id, 
                a.judul, 
                a.deskripsi, 
                a.responden,

                ak.nama angket_kategori,

                p.nama produk,
                p.gambar,

                (
                    SELECT COUNT(id) jml
                    FROM 
                        angket_item 
                    WHERE
                        id_angket = a.id
                    AND
                        hapus = '0'
                ) jml,

                (
                    SELECT COUNT(id) jmlRes
                    FROM 
                        angket_respon 
                    WHERE
                        id_karyawan = '$idPengguna'
                    AND
                        id_angket = a.id
                ) jmlRes

            FROM 
                angket a

            LEFT JOIN
                angket_kategori ak
            ON
                a.id_kategori = ak.id

            LEFT JOIN
                produk p
            ON
                a.id_produk = p.id

            WHERE
                a.hapus = '0'
            AND
                a.aktif = '1'
            AND
                (
                    a.responden = '$jenisPengguna'
            OR
                    a.responden = 'semua'
                )
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
                    OR
                        ak.nama LIKE '%$cari%'
					)
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
            $r['judul']      	= $d['judul'];
            $r['deskripsi']           = $d['deskripsi'];
            $r['responden']           = $d['responden'];

            $r['angket_kategori']           = $d['angket_kategori'];

            $r['produk']           = $d['produk'];
            $r['gambar']           = $d['gambar'];

            $r['jml']           = $d['jml'];
            $r['jmlRes']           = $d['jmlRes'];
        
            $ar[]   = $r;
        }

        $cAr = $c-1;

        $nomor = $start+1;

        for ($i=0; $i <= $cAr; $i++) {
            $id = $ar[$i]['id'];
            $judul = $ar[$i]['judul'];
            $deskripsi = $ar[$i]['deskripsi'];
            $responden = $ar[$i]['responden'];

            $angket_kategori = $ar[$i]['angket_kategori'];

            if($responden=='user'){
                $warnaLabel = 'blue';
            }
            else{
                $warnaLabel = 'grey';
            }

            $produk = $ar[$i]['produk'];
            $gambarUrl = $ar[$i]['gambar'];
            $gambar = '../files/photo/pictures.png';
            if(!empty($gambarUrl) && $gambarUrl !== ''){
                $gambarUrl = str_replace('%20', ' ', $gambarUrl);
                if(file_exists('../../'.$gambarUrl)){
                    $gambar = '../'.$gambarUrl;
                }
            }

            $jml = $ar[$i]['jml'];
            $jmlRes = $ar[$i]['jmlRes'];

            if($jmlRes<$jml){
                $classBtn = '';
            }
            else{
                $classBtn = 'disabled';
            }
?>  
            <div class="item">
                <div class="image">
                    <img src="<?php echo $gambar; ?>">
                </div>
                <div class="content">
                    <a class="header"><?php echo $judul; ?></a>
                    <div class="meta">
                        <span>Target: <?php echo $responden; ?></span>
                    </div>
                    <div class="description">
                        <?php echo $deskripsi; ?><br>
                        Produk: <?php echo $produk; ?><br>
                        Jumlah item: <strong><i><?php echo $jml; ?></i></strong>
                    </div>
                    <div class="extra">
                        <span class="ui label">
                            <?php echo $angket_kategori; ?>
                        </span>
                        <div class="ui icon button primary right floated <?php echo $classBtn; ?>" data-content="Isi angket" onclick="loadForm('questionnaire','<?php echo $id; ?>')">
                            <i class="edit alternate icon"></i>
                        </div>
                    </div>
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