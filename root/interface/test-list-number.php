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

	$id_periode   = saring($_POST['id_periode']);
    $id_ujian   = saring($_POST['jenis_ujian']);

	if(is_numeric($limit)){
		$q="
			SELECT 
                up.id idP, 
                up.tanggal, 
                up.waktu, 
                up.kkm,
                up.tampilan, 
                up.aktif,
                up.kode,
                up.tgl_aktif,
                up.waktu_aktif,

                u.nama namaUjian, 
                u.deskripsi deskUjian,
                u.tipe

            FROM 
                ujian_pelaksanaan up

            LEFT JOIN
                ujian u
            ON
                up.id_ujian = u.id

            WHERE
                up.hapus = '0'
		";

        if($id_periode!=='semua'){
            $q.="
                AND
                    up.id_periode = '$id_periode'
            ";
        }

        if($id_ujian!=='semua'){
            $q.="
                AND
                    up.id_ujian = '$id_ujian'
            ";
        }

		if($cari!==''){
			$q.="
					AND 
					(
                    OR
                        up.tanggal LIKE '%$cari%'
						s.nama LIKE '%$cari%'
					OR
						s.deskripsi LIKE '%$cari%'
					OR
						u.nama LIKE '%$cari%'
					OR
						u.deskripsi LIKE '%$cari%'
					)
			";
		}

		$q.="	
				ORDER BY 
					up.tanggal DESC,
                    up.aktif ASC,
                    up.tgl_aktif DESC,
                    up.waktu_aktif DESC
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
	<a id="number<?php echo $i; ?>" class="item <?php if($i==1){ echo "active"; }?>" onclick="updateListT('<?php echo $startFrom; ?>','<?php echo $i; ?>')">
		<?php echo $i ?>
	</a>
<?php
		$startFrom = $startFrom+$limit;
	}
?>
