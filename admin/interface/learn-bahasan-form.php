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
    $pecah = explode('[pisah]', $idData);

    $idBahasan = saring($pecah[0]);
    $noTingkat = saring($pecah[1]);

    $q = "
            SELECT
                nama,
                deskripsi

            FROM
                materi_kelompok_bahasan

            WHERE
                id = '$idBahasan'
    ";
    $e = mysqli_query($conn, $q);
    $r = mysqli_fetch_assoc($e);
    $judul = $r['nama'];
    $deskripsi = $r['deskripsi'];
?>

<div id="subDisplay" >
    <div class="ui message">
        <div class="ui breadcrumb">
            <div class="section">Belajar</div>
            <i class="right chevron icon divider"></i>
            <div class="active section"><?php echo $judul; ?></div>
        </div>
    </div>
    <div class="field">
        <div class="ui icon button" onclick="backToMain()">
            <i class="left chevron icon"></i> Kembali
        </div>    
    </div>

    <div id="loaderMateri" class="ui divided items">
        <!-- load data materi here -->
        <i class="info circle icon"></i> <i>Load Data..</i>
    </div>
</div>

<div id="subForm" style="display: none;">
    <!-- load other page here -->
</div>

<script type="text/javascript">

    populateMateri();

    function populateMateri(){
        loadingMulai();
        
        var id_bahasan = '<?php echo $idBahasan; ?>',
            no_tingkat = '<?php echo $noTingkat; ?>';

        $.ajax({
            type:"post",
            async:true,
            url:"interface/learn-materi.php",
            data:{
                'view':'1',
                'id_bahasan': id_bahasan,
                'no_tingkat': no_tingkat
            },
            success:function(data){
                $("#loaderMateri").html(data);
                loadingSelesai();
            }
        })
    }

</script>