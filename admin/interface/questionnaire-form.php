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

    $idPengguna = $_SESSION['idPengguna'];

    $idData = saring($_POST['idData']);

    if(empty($_SESSION['angketDibuka'])){
        $_SESSION['angketDibuka'] = $idData;    
    }
    else{
        if($idData!==$_SESSION['angketDibuka']){
            $_SESSION['angketDibuka'] = $idData;
        }
    }

    

    $q = "
            SELECT 
                a.judul, 
                a.deskripsi, 
                a.responden,

                k.nama jenis, 
                k.deskripsi deskJenis,

                
                p.nama, 
                p.deskripsi deskProduk, 
                p.gambar

            FROM 
                angket a

            LEFT JOIN
                angket_kategori k
            ON
                a.id_kategori = k.id

            LEFT JOIN
                produk p
            ON
                a.id_produk = p.id
                
            WHERE
                a.id = '$idData'
            LIMIT
                1
    ";
    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);
    if($c=='1'){
        $r = mysqli_fetch_assoc($e);

        $judul = $r['judul']; 
        $deskripsi = $r['deskripsi']; 
        $responden = $r['responden'];

        $jenis = $r['jenis']; 
        $deskJenis = $r['deskJenis'];

        $nama = $r['nama']; 
        $deskProduk = $r['deskProduk']; 
        $gambarUrl = $r['gambar'];
        if($gambarUrl!==''){
            $gambarUrl = str_replace('%20', ' ', $gambarUrl);
            if(file_exists('../../'.$gambarUrl)){
                $gambar = '../'.$gambarUrl;
            }
            else{
                $gambar = '../files/photo/pictures.png';
            }    
        }
        else{
            $gambar = '../files/photo/pictures.png';
        }    
    }
    else{
        $judul = ''; 
        $deskripsi = ''; 
        $responden = '';

        $jenis = ''; 
        $deskJenis = '';

        
        $nama = ''; 
        $deskProduk = ''; 
        $gambar = '../files/photo/pictures.png';
    }

?>
<div class="field">
    <div class="ui icon button" onclick="kembaliKeQuestonnaire()">
        <i class="left chevron icon"></i> Kembali
    </div>    
</div>

<h2 class="ui header">
    <img src="<?php echo $gambar; ?>">
    <div class="content">
        <?php echo $judul; ?>
        <div class="sub header">
            <?php echo $deskripsi; ?><br>
            Responden : <?php echo $responden; ?><br>
            Jenis angket : <?php echo $jenis; ?><br>
            <div class="ui divider"></div>
            Produk : <?php echo $nama; ?>
        </div>
    </div>
</h2>

<input type="hidden" id="id_angket" value="<?php echo $idData; ?>" >

<table class="ui striped selectable table">
    <thead>
        <tr>
            <th width="4%">No</th>
            <th colspan="4">Pertanyaan/ pernyataan</th>
        </tr>
    </thead>
    <tbody>
<?php
$q = "
        SELECT 
            ai.id, 
            ai.deskripsi,

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
            ai.id_angket = '$idData'
        AND
            ai.hapus = '0'

        ORDER BY 
            ai.no ASC
";

$e = mysqli_query($conn, $q);
$c = mysqli_num_rows($e);

if($c>0){
    $ar = array();
    $r = array();

    while ($d = mysqli_fetch_assoc($e)) {
        $r['id']            = $d['id'];
        $r['deskripsi']     = $d['deskripsi'];

        $r['satu']     = $d['satu'];
        $r['dua']     = $d['dua'];
        $r['tiga']     = $d['tiga'];
        $r['empat']     = $d['empat'];

        $r['idRespon']      = '0';
        $r['respon']        = '0';

        $ar[]   = $r;
    }

    $cAr = $c-1;


    $qA = "
            SELECT 
                id, 
                id_periode, 
                id_item, 
                respon, 
                tanggal, 
                jam 
            FROM 
                angket_respon 
            WHERE
                id_karyawan = '$idPengguna'
            AND
                id_angket = '$idData'
            ORDER BY
                id_item ASC
    ";
    $eA = mysqli_query($conn, $qA);
    $cA = mysqli_num_rows($eA);

    if($cA>'0'){
        while ($dj = mysqli_fetch_assoc($eA)) {
            $idItemI = $dj['id_item'];
            
            $idRes = $dj['id'];
            $res = $dj['respon'];

            for ($o=0; $o <= $cAr; $o++) { 
                $idItemO = $ar[$o]['id'];
                if($idItemO==$idItemI){
                    $ar[$o]['idRespon'] = $idRes;
                    $ar[$o]['respon'] = $res;
                    $o = $cAr;
                }
            }
        }
    }

    $nomor = 1;

    $jmlTerjawab = 0;

    for ($i=0; $i <= $cAr; $i++) {
        $idItem = $ar[$i]['id'];
        $deskripsi = $ar[$i]['deskripsi'];

        $satu = $ar[$i]['satu'];
        $dua = $ar[$i]['dua'];
        $tiga = $ar[$i]['tiga'];
        $empat = $ar[$i]['empat'];

        $idRes = $ar[$i]['idRespon'];
        $respon = $ar[$i]['respon'];
?>
        <tr>
            <td><?php echo $nomor; ?></td>
            <td colspan="4">
                <?php echo html_entity_decode($deskripsi); ?>
            </td>
        </tr>
        <tr>
            <td>
                <input type="hidden" id="idRes<?php echo $idItem; ?>" value="<?php echo $idRes; ?>">
                <input type="hidden" id="item<?php echo $idItem; ?>" value="<?php echo $respon; ?>">
            </td>
<?php
        for ($z=1; $z <=4 ; $z++) {
            if($z=='1'){
                $huruf = $satu;
            }
            else if($z=='2'){
                $huruf = $dua;
            }
            else if($z=='3'){
                $huruf = $tiga;
            }
            else if($z=='4'){
                $huruf = $empat;
            }

            if($respon==$z){
                $classChoice = 'active primary';
                $jmlTerjawab = $jmlTerjawab+1;
            }
            else{
                $classChoice = '';
            }
?>
            <td>
                <div id="btn<?php echo $idItem.'-'.$z; ?>" class="ui icon small button <?php echo $classChoice; ?> btn<?php echo $idItem; ?>" onclick="jawabItem('<?php echo $idItem; ?>','<?php echo $z; ?>')">
                    <?php echo $huruf; ?>
                </div>
            </td>
<?php        
        }
?>
        </tr>
<?php    
        $nomor = $nomor+1;            
    }
}
else{
    $teksKosong = 'Belum ada data.';
?>
    <tr>
        <td colspan="5">
            <i class="info circle icon teal"></i> <i> <?php echo $teksKosong; ?> </i>
        </td>
    </tr>
<?php       
}
?>            
    </tbody>
    <tfoot>
        <tr>
            <th colspan="5">
<?php
    if($c>0){
?>
                <input type="hidden" id="jmlItem" value="<?php echo $c; ?>">
                <input type="hidden" id="jmlTerjawab" value="<?php echo $jmlTerjawab; ?>">
                Anda menjawab <span id="txtTerjawab"><?php echo $jmlTerjawab; ?></span> dari <?php echo $c; ?>
<?php        
    }
?>                
            </th>
        </tr>
    </tfoot>
</table>

<script type="text/javascript">
    $('.button').popup();
    
    function jawabItem(idItem, numb){
        var id_angket, idRes, jawabItem;
        id_angket = $('#id_angket').val();
        idRes = $('#idRes'+idItem).val();
        jawabItem = $('#item'+idItem).val();
        if(numb!==jawabItem){
            $('.btn'+idItem).addClass('loading');
            $.ajax({
                type:"post",
                async:true,
                url:"interface/questionnaire-form-jawab-item.php",
                data:{
                    'view':'1',
                    'id_angket': id_angket,
                    'id_item': idItem,
                    'id_respon': idRes,
                    'respon': numb
                },
                success:function(data){
                    $("#feedBack").html(data);
                    loadingSelesai();
                }
            })
        }
    }

    function kembalianJawabItem(idAngket, idItem, balikIdRespon, respon, status){
        var id_angket, id_respon, jmlItem, jmlTerjawab, newJmlTerjawab;
        id_angket = $('#id_angket').val();

        if(id_angket==idAngket){
            if(status=='1'){
                $('#item'+idItem).val(respon);

                id_respon = $('#idRes'+idItem).val();
                if(id_respon=='0'){
                    $('#idRes'+idItem).val(balikIdRespon);

                    jmlItem = parseInt($('#jmlItem').val());
                    jmlTerjawab = parseInt($('#jmlTerjawab').val());
                    newJmlTerjawab = jmlTerjawab+1;
                    $('#jmlTerjawab').val(newJmlTerjawab);
                    $('#txtTerjawab').text(newJmlTerjawab);
                }


                $('.btn'+idItem).removeClass('active primary');
                $('.btn'+idItem).addClass('loading');
                $('#btn'+idItem+'-'+respon).addClass('active primary');
                setTimeout(function(){
                    $('.btn'+idItem).removeClass('loading');
                }, 400);
            }
            else{
                $('.btn'+idItem).removeClass('loading');
            }
        }
    }


    function kembaliKeQuestonnaire(){
        var jmlItem, jmlTerjawab;
        jmlItem = parseInt($('#jmlItem').val());
        jmlTerjawab = parseInt($('#jmlTerjawab').val());
        if(jmlTerjawab<jmlItem){
            tampilkanPesan('0','Silahkan berikan tanggapan kesemua item angket yang tersedia.');
        }
        else{
            $.ajax({
                type:"post",
                async:true,
                url:"interface/questionnaire-form-finish.php",
                data:{
                    'view':'1'
                },
                success:function(data){
                    $("#feedBack").html(data);
                    loadingSelesai();
                }
            })
        }
    }

</script>