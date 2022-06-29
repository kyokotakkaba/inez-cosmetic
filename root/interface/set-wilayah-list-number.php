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
	
	$limit 	= saring($_POST['limit']);
	$cari 	= saring($_POST['cari']);

	if(is_numeric($limit)){
		$q="
            SELECT 
                w.id
                
            FROM 
                wilayah w
            
            LEFT JOIN
                wilayah_supervisi ws
            ON
                w.id = ws.id_wilayah
            
            LEFT JOIN 
                karyawan k
            ON
                ws.id_karyawan = k.id

            LEFT JOIN
                tingkat_belajar t
            ON
                k.tingkat = t.id

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
						k.nama LIKE '%$cari%'
					)
			";
		}

		$q.="	
				ORDER BY 
					w.nama ASC
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
	<a id="number<?php echo $i; ?>" class="item <?php if($i==1){ echo "active"; }?>" onclick="updateListW('<?php echo $startFrom; ?>','<?php echo $i; ?>')">
		<?php echo $i ?>
	</a>
<?php
		$startFrom = $startFrom+$limit;
	}
?>
