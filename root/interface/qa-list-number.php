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

    $idPengguna = $_SESSION['idPengguna'];
	
	$limit 	= saring($_POST['limit']);
	$cari 	= saring($_POST['cari']);

	$id_kelompok   = saring($_POST['id_kelompok']);
    $status   = saring($_POST['status']);

	if(is_numeric($limit)){
		$q="
			SELECT 
                tj.id
                
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
                    tj.tanya_pada ASC
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
