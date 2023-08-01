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

	if(is_numeric($limit)){
		$q="
			SELECT 
                k.id,
                k.nik, 
                k.nama, 
                k.jk, 
                k.tgl_lahir, 
                k.tingkat,
                k.foto,
                k.tgl_masuk,

                a.jenis,

                w.nama nama_wil,

                wsw.nama nama_wil_sup

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

            WHERE
                k.hapus = '0'
		";

		if($cari!==''){
			$q.="
					AND 
					(
                        k.nik = '$cari'
                    OR
						k.nama LIKE '%$cari%'
                    OR
                        wsw.nama LIKE '%$cari%'
                    OR
                        w.nama LIKE '%$cari%'
                    OR
                        a.jenis LIKE '%$cari%'
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
	if($jumlahPage<=3){
     // 123  
     for($i=1; $i<=$jumlahPage; $i++){
        ?>
            <a id="number<?php echo $i; ?>" class="item <?php if($i==1){ echo "active"; }?>" onclick="updateListRekapUser('<?php echo $startFrom; ?>','<?php echo $i; ?>')">
                <?php echo $i ?>
            </a>
        <?php
		$startFrom = $startFrom+$limit;
     }
    }else{
     //123>   
     for($i=1; $i<=3; $i++){
        ?>
            <a id="number<?php echo $i; ?>" class="item <?php if($i==1){ echo "active"; }?>" onclick="updateListRekapUser('<?php echo $startFrom; ?>','<?php echo $i; ?>')">
                <?php echo $i ?>
            </a>
        <?php
		$startFrom = $startFrom+$limit;
     }
     ?>
     <a class="item" onclick="updateListRekapUser('<?php echo $limit; ?>','2')">
         Next
     </a>
     <?php
    }
?>

<script>
    function updateListRekapUser(start, id){
    var jumlahPage = <?php echo $jumlahPage ?>;
    var limitPage = <?php echo $limit ?>;
    var startFrom = 0;
    var tempMultiplier;
    var tempPageNum;
    id = Number(id);
    console.log(jumlahPage);
    $('#lastPage').val(start);
    $("#pageNumber a").remove();
    // 123 , 123> , <234>, <345
    if(jumlahPage<=3){
        for(var i=1; i<=jumlahPage; i++){
            $("#pageNumber").append("<a id=\"number"+i+"\" class=\"item\" onclick=\"updateListRekapUser('"+startFrom+"','"+i+"')\">"+i+"</a>");
            startFrom = startFrom+limitPage;
        }   
    }else{
        if(id>2){
            tempMultiplier = id-2;
            tempPageNum = id-1;
            $("#pageNumber").append("<a class=\"item\" onclick=\"updateListRekapUser('"+(limitPage*(tempMultiplier))+"','"+(tempPageNum)+"')\">Prev</a>");
        }
        if(id==jumlahPage){
            tempMultiplier = id-3;
            tempPageNum = id-2;
            $("#pageNumber").append("<a id=\"number"+(id-2)+"\" class=\"item\" onclick=\"updateListRekapUser('"+(limitPage*(tempMultiplier))+"','"+(tempPageNum)+"')\">"+(tempPageNum)+"</a>");
        }
        tempMultiplier = id-2;
        tempPageNum = id-1;
        $("#pageNumber").append("<a id=\"number"+(id-1)+"\" class=\"item\" onclick=\"updateListRekapUser('"+(limitPage*(tempMultiplier))+"','"+(tempPageNum)+"')\">"+(tempPageNum)+"</a>");
        tempMultiplier = id-1;
        $("#pageNumber").append("<a id=\"number"+(id)+"\" class=\"item\" onclick=\"updateListRekapUser('"+(limitPage*(tempMultiplier))+"','"+(id)+"')\">"+(id)+"</a>");
        if(id!=jumlahPage){
            tempPageNum = id+1;
            $("#pageNumber").append("<a id=\"number"+(id+1)+"\" class=\"item\" onclick=\"updateListRekapUser('"+(limitPage*(id))+"','"+(tempPageNum)+"')\">"+(tempPageNum)+"</a>");
        }
        if(id<=(jumlahPage-2)){
            tempPageNum = id+1;
            $("#pageNumber").append("<a class=\"item\" onclick=\"updateListRekapUser('"+(limitPage*(id))+"','"+(tempPageNum)+"')\">Next</a>");
        }
    }
    $("#number"+id).addClass("active");
    dataList();
}
</script>