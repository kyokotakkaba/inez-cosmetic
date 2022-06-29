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

    $id_wil = '';
    if(!empty($_POST['id_wil'])){
        $id_wil = saring($_POST['id_wil']);
    }
    
    $sekarang = date('Y-m-d');
    $tgl    = saring($_POST['tgl']);

	if(is_numeric($limit)){
		$q="
            SELECT 
                k.id idK,
                k.nik, 
                k.nama, 
                k.jk, 
                k.tgl_lahir, 
                k.tingkat,
                k.foto,
                k.tgl_masuk,

                a.jenis,

                w.nama nama_wil,

                wsw.nama nama_wil_sup,

                p.masuk,
                p.keterangan

            FROM 
                karyawan k

            LEFT JOIN
                akun a
            ON
                a.id_pengguna = k.id

            LEFT JOIN
                wilayah w
            ON
                k.id_wil = w.id
            AND
                w.hapus = '0'

            LEFT JOIN
                wilayah_supervisi ws
            ON
                ws.id_karyawan = k.id
            AND
                ws.hapus = '0'

            LEFT JOIN
                wilayah wsw
            ON
                ws.id_wilayah = wsw.id
            AND
                wsw.hapus = '0'

            LEFT JOIN
                pelatihan_presensi p
            ON
                k.id = p.id_karyawan
            AND
                p.id_periode = '$idPeriode'
            AND
                p.tanggal = '$tgl'

            WHERE
                k.hapus = '0'
		";

        if($id_wil!==''){
            $q.="
                AND
                    k.id_wil = '$id_wil'
            ";
        }

		if($cari!==''){
			$q.="
					AND 
					(
						k.nama LIKE '%$cari%'
					OR
						k.nik LIKE '%$cari%'
					)
			";
		}

		$q.="	
				ORDER BY 
					k.nik ASC
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
