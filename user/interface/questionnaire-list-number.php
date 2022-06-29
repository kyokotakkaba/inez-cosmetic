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
	
	$limit 	= saring($_POST['limit']);
	$cari 	= saring($_POST['cari']);

	if(is_numeric($limit)){
		$q="
			SELECT 
                a.id
                
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
		";

	}
	else{
?>
		<div class="item active">
			!NUM
		</div>
<?php
		exit();
	}

	$e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);

    if($c=='0'){
?>
		<div class="item active">
			0
		</div>
<?php
		exit();
    }
	
	$jumlahPage = ceil($c/$limit);
	$startFrom = 0;
	
	for($i=1; $i<=$jumlahPage; $i++){
?>
	<a id="number<?php echo $i; ?>" class="item <?php if($i==1){ echo "active"; }?>" onclick="updateList('<?php echo $startFrom; ?>','<?php echo $i; ?>')">
		<?php echo $i ?>
	</a>
<?php
		$startFrom = $startFrom+$limit;
	}
?>
