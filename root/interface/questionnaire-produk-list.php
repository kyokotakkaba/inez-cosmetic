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
	
	$start 	= saring($_POST['start']);
	$limit 	= saring($_POST['limit']);
	$cari 	= saring($_POST['cari']);

	if(is_numeric($start) AND is_numeric($limit)){
		$q = "
                SELECT 
                    p.id, 
                    p.nama, 
                    p.deskripsi, 
                    p.gambar

                FROM 
                    produk p
                
                WHERE
                    p.hapus = '0'
        ";

        if($cari!==''){
            $q.="
                    AND 
                    (
                        p.nama LIKE '%$cari%'
                    OR
                        p.deskripsi LIKE '%$cari%'
                    )
            ";
        }

        $q.="   
                ORDER BY 
                    p.nama ASC
                    
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
            $r['id']            = $d['id'];
            $r['nama']        = $d['nama'];
            $r['deskripsi']          = $d['deskripsi'];
            $r['gambar']      	    = $d['gambar'];
            
            $ar[]   = $r;
        }

        $cAr = $c-1;

        $nomor = $start+1;

        for ($i=0; $i <= $cAr; $i++) {
            $idProd = $ar[$i]['id'];
            $nama = $ar[$i]['nama'];
            $deskripsi = $ar[$i]['deskripsi'];
            $gambarUrl = $ar[$i]['gambar'];
            $gambar = '../files/photo/pictures.png';  
            if(!empty($gambarUrl) && $gambarUrl!==''){
                $gambarUrl = str_replace('%20', ' ', $gambarUrl);
                if(file_exists('../../'.$gambarUrl)){
                    $gambar = '../'.$gambarUrl;
                }
            }
?>
            <tr>
                <td><?php echo $nomor; ?></td>
                <td>
                    <h4 class="ui header">
                        <img src="<?php echo $gambar; ?>">
                        <div class="content">
                            <?php echo $nama; ?>
                            <div class="sub header">
                                <?php echo $deskripsi; ?><br>
                            </div>
                        </div>
                    </h4>
                </td>
                <td>
                    <div class="ui icon button" data-content="Edit" onclick="loadFormSub('isi','<?php echo $idProd; ?>')">
                        <i class="pencil alternate icon"></i>
                    </div>
                    <div class="ui icon button red" data-content="Hapus" onclick="tampilkanKonfirmasi('<?php echo $idProd; ?>','Hapus catatan','Yakin ingin menghapus data produk ?<br><br><br>*Data respon yang telah terkumpul dengan target produk ini tidak akan dihapus.','interface/questionnaire-produk-delete.php')">
                        <i class="trash alternate icon"></i>
                    </div>
                </td>
            </tr>
<?php    
            $nomor = $nomor+1;            
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