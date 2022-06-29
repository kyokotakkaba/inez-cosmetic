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

    $id_angket   = saring($_POST['lastId']);

	if(is_numeric($start) AND is_numeric($limit)){
		$q = "
                SELECT 
                    ai.id, 
                    ai.deskripsi,
                    ai.no,

                    al.nama,
                    al.satu,
                    al.dua,
                    al.tiga,
                    al.empat

                FROM 
                    angket_item ai

                LEFT JOIN
                    angket_label al
                ON
                    ai.id_label = al.id

                WHERE
                    ai.id_angket = '$id_angket'
                AND
                    ai.hapus = '0'
        ";

        if($cari!==''){
            $q.="
                    AND 
                        ai.deskripsi LIKE '%$cari%'
            ";
        }

        $q.="   
                ORDER BY 
                    ai.no ASC
                    
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
            $r['deskripsi']          = $d['deskripsi'];
            $r['no']          = $d['no'];

            $r['nama']          = $d['nama'];
            $r['satu']          = $d['satu'];
            $r['dua']          = $d['dua'];
            $r['tiga']          = $d['tiga'];
            $r['empat']          = $d['empat'];

            $ar[]   = $r;
        }

        $cAr = $c-1;

        $nomor = $start+1;

        $qB = "
                SELECT
                    id
                    
                FROM
                    angket_item
        ";
        $eB = mysqli_query($conn, $qB);
        $cB = mysqli_num_rows($eB);

        $bebas = $cB+1;

        for ($i=0; $i <= $cAr; $i++) {
            $idItem = $ar[$i]['id'];
            $deskripsi = $ar[$i]['deskripsi'];
            $no = $ar[$i]['no'];

            $nama = $ar[$i]['nama'];
            $satu = $ar[$i]['satu'];
            $dua = $ar[$i]['dua'];
            $tiga = $ar[$i]['tiga'];
            $empat = $ar[$i]['empat'];

            if($c>1){
                if($i==0){
                    $classPrev = 'disabled';
                    $classNext = '';

                    $sasarPrev = $no;
                    $n = $i+1;
                    $sasarNext = $ar[$n]['no'];
                }
                else if($i==$cAr){
                    $classPrev = '';
                    $classNext = 'disabled';

                    $sasarNext = $no;
                    $p = $i-1;
                    $sasarPrev = $ar[$p]['no'];
                }
                else if($i>0&&$i<$cAr){
                    $classPrev = '';
                    $classNext = '';

                    $sasarNext = $no;
                    $p = $i-1;
                    $n = $i+1;
                    $sasarPrev = $ar[$p]['no'];
                    $sasarNext = $ar[$n]['no'];
                }
            }
?>
            <tr>
                <td><?php echo $nomor; ?></td>
                <td>
                    <?php echo html_entity_decode($deskripsi); ?>
                    Label : <?php echo $nama.' ('.$satu.' - '.$dua.' - '.$tiga.' - '.$empat.')'; ?>
                </td>
                <td>
<?php
    if($c>1){
?>
                    <div class="ui icon button <?php echo $classPrev; ?>" data-content="Majukan" onclick="reposisiData('<?php echo $id_angket; ?>[pisah]<?php echo $no; ?>','<?php echo $sasarPrev; ?>', '<?php echo $bebas; ?>', 'interface/questionnaire-manage-reposition.php')">
                        <i class="up chevron icon"></i>
                    </div>
                    <div class="ui icon button <?php echo $classNext; ?>" data-content="Mundurkan" onclick="reposisiData('<?php echo $id_angket; ?>[pisah]<?php echo $no; ?>','<?php echo $sasarNext; ?>', '<?php echo $bebas; ?>', 'interface/questionnaire-manage-reposition.php')">
                        <i class="down chevron icon"></i>
                    </div>
<?php        
    }
?>                                      
                    <div class="ui icon button" data-content="Edit" onclick="loadFormSub('data','<?php echo $id_angket; ?>[pisah]<?php echo $idItem; ?>')">
                        <i class="pencil alternate icon"></i>
                    </div>
                    <div class="ui icon button red" data-content="Hapus" onclick="tampilkanKonfirmasi('<?php echo $idItem; ?>','Hapus data','Yakin ingin menghapus data item angket ?<br><br><br>*Data yang diperoleh dari item ini (jika ada) tdak akan ditampilkan (dihapus) dari laporan.','interface/questionnaire-manage-delete.php')">
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