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
	
	$start 	= saring($_POST['start']);
	$limit 	= saring($_POST['limit']);
	$cari 	= saring($_POST['cari']);

    $iniHari = date('Y-m-d');

	if(is_numeric($start) AND is_numeric($limit)){
		$q="
			SELECT 
               *

            FROM 
                laporan_pembelajaran
		";

		if($cari!==''){
			$q.="
					AND 
					(
                        user_id LIKE '%$cari%'
                    OR
						nip LIKE '%$cari%'
                    OR
                        area LIKE '%$cari%'
                    OR
                        id_modul LIKE '%$cari%'
                    OR
                        nama_modul LIKE '%$cari%'
                    OR
                        jumlah_modul LIKE '%$cari%'
                    OR
                        selesai_lembar LIKE '%$cari%'
                    OR
                        terakhir_dibuka LIKE '%$cari%'
					)
			";
		}

		$q.="	
				ORDER BY 
					user_id, id_modul ASC
                    
				LIMIT 
					$limit 
				OFFSET 
					$start
		";	
	}
	else{
?>
		<tr>
			<td colspan="3">
				<i class="circle info icon teal"></i> <i>Parameter limit dan offset harus angka.</i>
			</td>
		</tr>
<?php		
		exit();
	}

	$e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);

    if($c>0){
        $ar = array();
        $r = array();

        while ($d = mysqli_fetch_assoc($e)) {
            $r['user_id']            = $d['user_id'];
            $r['nip']      	    = $d['nip'];
            $r['area']          = $d['area'];
            $r['id_modul']            = $d['id_modul'];
            $r['nama_modul']     = $d['nama_modul'];
            $r['jumlah_modul']          = $d['jumlah_modul'];
            $r['selesai_lembar']          = $d['selesai_lembar'];
            $r['terakhir_dibuka']      = $d['terakhir_dibuka'];
        
            $ar[]   = $r;
        }

        $cAr = $c-1;


        for ($i=0; $i <= $cAr; $i++) {
            
?>
			<tr>
				<td><?php echo $ar[$i]['user_id']; ?></td>
				<td><?php echo $ar[$i]['nip']; ?></td>
				<td><?php echo $ar[$i]['area']; ?></td>
				<td><?php echo $ar[$i]['id_modul']; ?></td>
				<td><?php echo $ar[$i]['nama_modul']; ?></td>
				<td><?php echo $ar[$i]['jumlah_modul']; ?></td>
				<td><?php echo $ar[$i]['selesai_lembar']; ?></td>
				<td><?php echo $ar[$i]['terakhir_dibuka']; ?></td>
			</tr>
<?php          
        }
    }
    else{
    	if($cari==''){
    		$teksKosong = 'Belum ada data.';
    	}
    	else{
    		$teksKosong = "Data dengan kata kunci <strong>".$cari."</strong> tidak ditemukan.";
    	}
?>
		<tr>
			<td colspan="3">
				<i class="info circle icon teal"></i> <i> <?php echo $teksKosong; ?> </i>
			</td>
		</tr>
<?php    	
    }

?>

<script type="text/javascript">
	$('.button, .popup').popup();
</script>