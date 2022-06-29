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

	$idPengguna = $_SESSION['idPengguna'];

	if(is_numeric($limit)){
		$q="
            SELECT 
                t.id idPenargetan,
                t.susulan,
                t.susulan_tgl,

                up.id idP, 
                up.tanggal, 
                up.waktu, 
                up.kkm,
                up.kode,

                u.id id_ujian,
                u.nama namaUjian, 
                u.deskripsi deskUjian,
                u.tipe,

                ku.mulai,
                ku.selesai

            FROM
                ujian_pelaksanaan_target_karyawan t 
            
            LEFT JOIN
                ujian_pelaksanaan up
            ON
                t.id_pelaksanaan = up.id

            LEFT JOIN
                ujian u
            ON
                up.id_ujian = u.id

            LEFT JOIN
                karyawan_ujian ku
            ON
                t.id = ku.id

            WHERE
                t.hapus = '0'
            AND
                t.id_karyawan = '$idPengguna'
            AND
                up.hapus = '0'
            AND
                up.aktif = '1'
            AND
                up.id_periode = '$idPeriode'
            AND
                up.id
            NOT IN
            (
                SELECT
                    id_pelaksanaan

                FROM
                    karyawan_ujian

                WHERE
                    id_karyawan = '$idPengguna'
                AND
                    selesai != ''
                AND
                    hapus = '0'
            )
        ";

        if($cari!==''){
            $q.="
                    AND 
                    (
                    OR
                        up.tanggal LIKE '%$cari%'
                    OR
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
	<a id="number<?php echo $i; ?>" class="item <?php if($i==1){ echo "active"; }?>" onclick="updateList('<?php echo $startFrom; ?>','<?php echo $i; ?>')">
		<?php echo $i ?>
	</a>
<?php
		$startFrom = $startFrom+$limit;
	}
?>
