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

	// $id_periode   = saring($_POST['id_periode']);
    $tgl_awal   = saring($_POST['tgl_awal']);
    $tgl_akhir   = saring($_POST['tgl_akhir']);
    $id_ujian   = saring($_POST['jenis_ujian']);
    $id_wilayah   = saring($_POST['id_wilayah']);

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
                up.id_ujian,
                up.id_periode,

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
            AND
                up.aktif = '1'
            AND
                up.tanggal >= '$tgl_awal'
            AND
                up.tanggal <= '$tgl_akhir'
        ";

        // if($id_periode!=='semua'){
        //     $q.="
        //         AND
        //             up.id_periode = '$id_periode'
        //     ";
        // }

        if($id_wilayah!=='' && $id_wilayah!=='all'){
            $q .= "
                AND 
                (
                    SELECT 
                        COUNT(upt.id_karyawan)

                    FROM 
                        ujian_pelaksanaan_target_karyawan upt

                    LEFT JOIN
                        karyawan k
                    ON
                        upt.id_karyawan = k.id

                    WHERE
                        upt.id_pelaksanaan = up.id
                    AND
                        upt.hapus = '0'
                    AND
                        k.id_wil = '$id_wilayah'
                ) > 0
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
                    up.tanggal DESC
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
	<a id="number<?php echo $i; ?>" class="item <?php if($i==1){ echo "active"; }?>" onclick="updateListR('<?php echo $startFrom; ?>','<?php echo $i; ?>')">
		<?php echo $i ?>
	</a>
<?php
		$startFrom = $startFrom+$limit;
	}
?>
