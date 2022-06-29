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
                p.id

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
