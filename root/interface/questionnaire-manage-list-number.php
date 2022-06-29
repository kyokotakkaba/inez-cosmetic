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

    $id_angket   = saring($_POST['lastId']);

	if(is_numeric($limit)){
		$q = "
                SELECT 
                    id

                FROM 
                    angket_item 
                WHERE
                    id_angket = '$id_angket'
                AND
                    hapus = '0'
        ";

        if($cari!==''){
            $q.="
                    AND 
                        deskripsi LIKE '%$cari%'
            ";
        }

        $q.="   
                ORDER BY 
                    no ASC
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
