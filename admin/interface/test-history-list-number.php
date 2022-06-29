<?php
    session_start();
    $appSection = 'admin';

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

	$idPengguna = $_SESSION['idPengguna'];

	if(is_numeric($limit)){
		$q = "
            SELECT 
                ku.id, 
                ku.tanggal mengerjakan, 
                ku.mulai, 
                ku.selesai, 
                ku.nilai_akhir, 
                ku.n, 
                ku.n_pk, 
                ku.nilai_grade,
                ku.n_poin, 
                ku.remidi,
                ku.remidi_karena,

                up.id_ujian,
                up.kkm, 
                up.tanggal jadwal, 
                up.waktu, 
                up.tampilan, 
                up.aktif, 
                up.kode,

                u.nama namaUjian

            FROM 
                karyawan_ujian ku

            LEFT JOIN
                ujian_pelaksanaan up
            ON
                ku.id_pelaksanaan = up.id

            LEFT JOIN 
                ujian u
            ON
                up.id_ujian = u.id
            
            WHERE
                ku.id_karyawan = '$idPengguna'
            AND
                ku.selesai != ''
            AND
                ku.hapus = '0'
            AND
                up.hapus = '0'
        ";

        if($id_periode !== 'semua'){
            $q .= "
                    AND
                        up.id_periode = '$id_periode'
            ";
        }

        if($id_ujian !== 'semua'){
            $q .= "
                    AND
                        up.id_ujian = '$id_ujian'
            ";
        }

        if($cari!==''){
            $q.="
                    AND 
                    (
                        up.tanggal LIKE '%$cari%'
                    OR
                        ku.tanggal LIKE '%$cari%'
                    )
            ";
        }

        $q.="   
                ORDER BY
                    up.id_periode DESC,
                    ku.tanggal DESC,
                    ku.mulai DESC
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
