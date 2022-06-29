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

    $idB   = saring($_POST['lastId']);

	if(is_numeric($limit)){
		$q="
			SELECT 
                m.id, 
                m.no, 
                m.judul, 
                m.deskripsi, 
                m.baner, 
                m.isi, 
                m.buku1, 
                m.buku2, 
                m.lampiran,
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
	<a id="numberSub<?php echo $i; ?>" class="item <?php if($i==1){ echo "active"; }?>" onclick="updateListSub('<?php echo $startFrom; ?>','<?php echo $i; ?>')">
		<?php echo $i ?>
	</a>
<?php
		$startFrom = $startFrom+$limit;
	}
?>
