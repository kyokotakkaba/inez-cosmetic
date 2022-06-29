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

    if($_SESSION['menu']!=='test-history'){
        $_SESSION['menu'] = 'test-history';
    }

    $tahun = date('Y');
    $ujian = '60BF-E0E0';
?>
<h2 class="ui block header">
    <i class="history icon"></i>
    <div class="content">
        Riwayat Ujian
        <div class="sub header">
            Review pelaksanaan ujian
        </div>
    </div>
</h2>
<div id="dataDisplay">
    <div class="fields">
        <div class="eight wide field">
            <select id="id_periode" class="ui compact dropdown" onchange="updateRowH()" >
<?php
$q = "
        SELECT
            id
        FROM
            periode
        ORDER BY
            id DESC
";
$e = mysqli_query($conn, $q);
$c = mysqli_num_rows($e);

if($c=='0'){
?>
        <option value="">Periode kosong</option>
<?php       
}
else{
    if($c>1){
?>
        <option value="semua">Semua</option>
<?php        
    }
    while ($r = mysqli_fetch_assoc($e)) {
        $idP = $r['id'];
?>
        <option value="<?php echo $idP; ?>" <?php if($idP==$tahun){ ?> selected="selected" <?php } ?> ><?php echo $idP; ?></option>
<?php           
    }
}
?>          
            </select>
        </div>
        <div class="eight wide field">
            <select id="jenis_ujian" class="ui compact dropdown" onchange="updateRowH()" >
<?php
$q = "
        SELECT
            id,
            nama
        FROM
            ujian
        ORDER BY
            nama ASC
";
$e = mysqli_query($conn, $q);
$c = mysqli_num_rows($e);

if($c=='0'){
?>
        <option value="">Ujian kosong</option>
<?php       
}
else{
?>
        <option value="semua">Semua</option>
<?php    
    while ($r = mysqli_fetch_assoc($e)) {
        $idU = $r['id'];
        $namaU = $r['nama'];
?>
        <option value="<?php echo $idU; ?>"><?php echo $namaU; ?></option>
<?php           
    }
}
?>          
            </select>
        </div>
    </div>
    
    <div class="field" style="display: none;">
        <div class="ui icon input">
            <input id="searchData" placeholder="Cari Data.." type="text" />
            <i class="search icon"></i>
        </div>
    </div>

    <table class="ui striped selectable table">
        <thead>
            <tr>
                <th width="4%" rowspan="2">No</th>
                <th rowspan="2">Pengerjaan</th>
                <th colspan="4" width="8%">Hasil</th>
                <th width="4%" rowspan="2">Opsi</th>
            </tr>
            <tr>
                <th>PK</th>
                <th>CH</th>
                <th>NA</th>
                <th>Grade</th>
            </tr>
        </thead>
        <tbody id="resultData">

        </tbody>
        <tfoot>
            <tr>
                <th colspan="7">
                    <div class="ui vertical basic segment clearing" style="padding: 0px;">
                        <select class="ui dropdown compact" id="jumlahRow"  onchange="updateRowH()">
                            <option value="25">25 Baris</option>
                            <option value="50">50 Baris</option>
                            <option value="75">75 Baris</option>
                            <option value="100">100 Baris</option>
                        </select>

                        <div class="ui right floated pagination menu" id="pageNumber">
                            <!-- show row -->
                        </div>
                    </div>
                </th>
            </tr>
        </tfoot>
    </table>

    <input type="hidden" id="lastPage" value="0">

</div>

<div id="formDisplay" style="display: none;">
    <!-- load form here -->
</div>

<script type="text/javascript">
    $('.button').popup();
    $('.dropdown').dropdown();

    updateRowH();

    function updateRowH(){
        dataListH();
        showRowH();
    }
    
    function dataListH(){
        loadingMulai();
        var start, limit, key, id_periode, jenis_ujian;
        start = $('#lastPage').val();
        limit = $("#jumlahRow").val();
        key = $("#searchData").val();
        id_periode = $("#id_periode").val();
        jenis_ujian = $("#jenis_ujian").val();

        $.post('interface/test-history-list.php',{view:'1', start: start, limit: limit, cari: key, id_periode: id_periode, jenis_ujian: jenis_ujian},
            function(result){
                $("#resultData").html(result);
                loadingSelesai();
            }
        );
    }

    function showRowH(){
        loadingMulai();
        var limit, key, id_periode, jenis_ujian;
        limit = $("#jumlahRow").val();
        key = $("#searchData").val();
        id_periode = $("#id_periode").val();
        jenis_ujian = $("#jenis_ujian").val();
        
        $.post('interface/test-history-list-number.php',{view:'1', limit: limit, cari: key, id_periode: id_periode, jenis_ujian: jenis_ujian},
            function(result){
                $("#pageNumber").html(result);
                loadingSelesai();
            }
        );
    }


    function updateListH(start, id){
        $('#lastPage').val(start);
        $("#pageNumber a").removeClass("active");
        dataListH();
        $("#number"+id).addClass("active");
    }

    
    $("#searchData").keyup(function(event){
        if(event.keyCode == 13){
            updateRowH();
            $('#lastPage').val('0');
        }
    });

</script>