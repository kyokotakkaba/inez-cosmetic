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
	
	$start 	= saring($_POST['start']);
	$limit 	= saring($_POST['limit']);
	$cari 	= saring($_POST['cari']);


    $idPengguna = $_SESSION['idPengguna'];

	if(is_numeric($start) AND is_numeric($limit)){
		$q="
            SELECT 
                id, 
                judul, 
                isi, 
                untuk, 
                pada, 
                waktu 

            FROM 
                notifikasi 

            WHERE
                (
                    untuk = 'all'
                OR
                    untuk = '$jenisPengguna'
                OR
                    untuk = '$idPengguna'
                )
            AND
                id NOT IN 
                (
                    SELECT
                        id_notif
                    FROM
                        notifikasi_readed
                    WHERE
                        id_pengguna = '$idPengguna'
                )

            ORDER BY 
                pada DESC,
                waktu DESC
                    
            LIMIT 
                $limit 
            OFFSET 
                $start
        ";
	}
	else{
?>
        <div class="ui floating message">
            <p><i class="circle info icon teal"></i> <i>Parameter limit dan offset harus angka.</i></p>
        </div>
<?php		
		exit();
	}

	$e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);

    if($c>0){
        $ar = array();
        $r = array();

        while ($d = mysqli_fetch_assoc($e)) {
            $r['id']           = $d['id'];
            $r['judul']      	    = $d['judul'];
            $r['isi']             = $d['isi'];
            $r['untuk']             = $d['untuk'];
            $r['pada']             = $d['pada'];
            $r['waktu']             = $d['waktu'];
            
            $ar[]   = $r;
        }

        $cAr = $c-1;

        $nomor = $start+1;

        for ($i=0; $i <= $cAr; $i++) {
            $idData = $ar[$i]['id'];
            $judul = $ar[$i]['judul'];
            $isi = $ar[$i]['isi'];
            $untuk = $ar[$i]['untuk'];
            $pada = $ar[$i]['pada'];
            $waktu = $ar[$i]['waktu'];
?>
            <div class="ui message">
                <i class="close icon popup" data-content="Hapus" onclick="tampilkanKonfirmasi('<?php echo $idData; ?>', 'Hapus Notifikasi', 'Notifikasi tidak akan ditampilkan lagi. Yakin ingin tetap lanjut ?', 'interface/notif-read.php')"></i>
                <div class="header">
                    <?php echo $judul; ?>
                </div>
                <p><?php echo $isi; ?></p>
            </div>
<?php    
            $nomor = $nomor+1;            
        }
    }
    else{
    	if($cari==''){
    		$teksKosong = 'Tidak ada data notifikasi.';
    	}
    	else{
    		$teksKosong = "Data dengan kata kunci <strong>".$cari."</strong> tidak ditemukan.";
    	}
?>
        <div class="ui floating message">
            <p><i class="info circle icon teal"></i> <i> <?php echo $teksKosong; ?> </i></p>
        </div>
<?php    	
    }
?>





<script type="text/javascript">
	$('.button, .popup').popup();
</script>