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

    if($_SESSION['menu']!=='report'){
        $_SESSION['menu'] = 'report';
    }


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

    if($idPeriode=='0'){
        $tahun = date('Y');
    }
    else{
        $tahun = $idPeriode;
    }

    $ujian = '60BF-E0E0';
    $tgl_sekarang  = date('Y-m-d');
    $tgl_awal  = date('Y-04-01');

?>
<h2 class="ui block header">
    <i class="print icon"></i>
    <div class="content">
        Laporan
        <div class="sub header">
            Pelaksanaan ujian 
        </div>
    </div>
</h2>


<div class="ui basic vertical segment clearing" style="margin: 0px; padding: 0px 0px 10px 0px;">
    <!-- yoko's code here  -->
            <div class="ui input calendar" id="tglPelatihan" >
                <div class="ui input left icon">
                    <i class="calendar alternate outline icon"></i>
                    <input type="text" style="width:150px" placeholder="YYYY-MM-DD" name="tgl" id="tgl_awal" value="<?php echo $tgl_awal; ?>" onchange="updateRowR()" />
                </div>
            </div> -
            <div class="ui input calendar" id="tglPelatihan2" >
                <div class="ui input left icon">
                    <i class="calendar alternate outline icon"></i>
                    <input type="text" style="width:150px" placeholder="YYYY-MM-DD" name="tgl" id="tgl_akhir" value="<?php echo $tgl_sekarang; ?>" onchange="updateRowR()" />
                </div>
            </div>
    <!--  -->
    <div class="ui icon button" onclick="updateRowR()" data-content="Reload">
        <i class="redo icon"></i>
    </div>

    <!-- <select id="id_periode" class="ui compact dropdown" onchange="updateRowR()" > -->
<?php
/* 
if($c=='0'){
?>
        <option value="">Periode kosong</option>
<?php       
}
else{
    while ($r = mysqli_fetch_assoc($e)) {
        $idP = $r['id'];
?>
        <option value="<?php echo $idP; ?>" <?php if($idP==$tahun){ ?> selected="selected" <?php } ?> ><?php echo $idP; ?></option>
<?php           
    }
}
*/
?>          
    <!-- </select> -->

    <select id="jenis_ujian" class="ui compact dropdown" onchange="updateRowR()" >
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

    <select id="id_wilayah" class="ui search dropdown" onchange="updateRowR()">
<?php
    $q = "
            SELECT
                id,
                kode,
                nama

            FROM
                wilayah

            WHERE
                hapus = '0'

            ORDER BY
                kode ASC
    ";
    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);
    if($c=='0'){
?>
        <option value="">Wilayah Kosong</option>
<?php        
    }
    else{
        if($c>1){
?>
            <option value="all">Semua Wilayah</option>
<?php            
        }
        while ($r = mysqli_fetch_assoc($e)) {
            $idW = $r['id'];
            $kdW = $r['kode'];
            $nmW = $r['nama'];
?>
            <option value="<?php echo $idW; ?>">
                <?php echo $kdW.' - '.$nmW; ?>
            </option>
<?php            
        }
    }
?>
    </select>
</div>
<div class="field">
    <div class="ui icon input">
        <input id="searchData" placeholder="Cari Data.." type="text" />
        <i class="search icon"></i>
    </div>
</div>
<table class="ui striped selectable table">
    <thead>
        <tr>
            <th width="4%">No</th>
            <th>Deskripsi</th>
            <th width="26%">Opsi</th>
        </tr>
    </thead>
    <tbody id="resultData">
        <!-- load data here -->
        <tr>
            <td colspan="3">
                <i class="info circle icon"></i> <i>Load Data..</i>
            </td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="4">
                <div class="ui vertical basic segment clearing" style="padding: 0px;">
                    <select class="ui dropdown compact" id="jumlahRow"  onchange="updateRowR()">
                        <option value="25">25 Baris</option>
                        <option value="35">35 Baris</option>
                        <option value="50">50 Baris</option>
                    </select>

                    <input type="hidden" id="lastPage" value="0">
                    <div class="ui right floated pagination menu" id="pageNumber">
                        <!-- show row -->
                        <div class="active item">0</div>
                    </div>
                </div>
            </th>
        </tr>
    </tfoot>
</table>




<script type="text/javascript">
    $('.button').popup();
    $('.dropdown').dropdown({ fullTextSearch: "exact" });
    $('#tglPelatihan').calendar({
      type: 'date',
      formatter:{
          date: function(date, setting){
              if (!date) return '';
              var day = ("0"+date.getDate()).slice(-2);
              var month = ("0"+(date.getMonth() + 1)).slice(-2);
              var year = date.getFullYear();
              return year + '-' + month + '-' + day;
          }
      }
    });
    
    $('#tglPelatihan2').calendar({
      type: 'date',
      formatter:{
          date: function(date, setting){
              if (!date) return '';
              var day = ("0"+date.getDate()).slice(-2);
              var month = ("0"+(date.getMonth() + 1)).slice(-2);
              var year = date.getFullYear();
              return year + '-' + month + '-' + day;
          }
      }
    });
    updateRowR();

    function updateRowR(){
        console.log("test")
        dataListR();
        showRowR();
    }
    
    // function dataListR(){
    //     loadingMulai();
    //     var start, limit, key, id_periode, jenis_ujian, id_wilayah;
    //     start = $('#lastPage').val();
    //     limit = $("#jumlahRow").val();
    //     key = $("#searchData").val();

    //     id_periode = $("#id_periode").val();
    //     jenis_ujian = $("#jenis_ujian").val();
    //     id_wilayah = $("#id_wilayah").val();

    //     $.post('interface/report-list.php',{view:'1', start: start, limit: limit, cari: key, id_periode: id_periode, jenis_ujian: jenis_ujian, id_wilayah: id_wilayah},
    //         function(result){
    //             $("#resultData").html(result);
    //             loadingSelesai();
    //         }
    //     );
    // }

    // function showRowR(){
    //     loadingMulai();
    //     var limit, key, id_periode, jenis_ujian, id_wilayah;
    //     limit = $("#jumlahRow").val();
    //     key = $("#searchData").val();

    //     id_periode = $("#id_periode").val();
    //     jenis_ujian = $("#jenis_ujian").val();
    //     id_wilayah = $("#id_wilayah").val();

    //     $.post('interface/report-list-number.php',{view:'1', limit: limit, cari: key, id_periode: id_periode, jenis_ujian: jenis_ujian, id_wilayah: id_wilayah},
    //         function(result){
    //             $("#pageNumber").html(result);
    //             loadingSelesai();
    //         }
    //     );
    // }

    function dataListR(){
        loadingMulai();
        var start, limit, key, tgl_awal, tgl_akhir, jenis_ujian, id_wilayah;
        start = $('#lastPage').val();
        limit = $("#jumlahRow").val();
        key = $("#searchData").val();

        tgl_awal = $("#tgl_awal").val();
        tgl_akhir = $("#tgl_akhir").val();
        jenis_ujian = $("#jenis_ujian").val();
        id_wilayah = $("#id_wilayah").val();

        $.post('interface/report-list.php',{view:'1', start: start, limit: limit, cari: key, tgl_awal: tgl_awal, tgl_akhir: tgl_akhir, jenis_ujian: jenis_ujian, id_wilayah: id_wilayah},
            function(result){
                $("#resultData").html(result);
                loadingSelesai();
            }
        );
    }

    function showRowR(){
        loadingMulai();
        var start, limit, key, tgl_awal, tgl_akhir, jenis_ujian, id_wilayah;
        limit = $("#jumlahRow").val();
        key = $("#searchData").val();

        tgl_awal = $("#tgl_awal").val();
        tgl_akhir = $("#tgl_akhir").val();
        jenis_ujian = $("#jenis_ujian").val();
        id_wilayah = $("#id_wilayah").val();

        $.post('interface/report-list-number.php',{view:'1', limit: limit, cari: key, tgl_awal: tgl_awal, tgl_akhir: tgl_akhir, jenis_ujian: jenis_ujian, id_wilayah: id_wilayah},
            function(result){
                $("#pageNumber").html(result);
                loadingSelesai();
            }
        );
    }


    function updateListR(start, id){
        $('#lastPage').val(start);
        $("#pageNumber a").removeClass("active");
        dataListR();
        $("#number"+id).addClass("active");
    }

    
    $("#searchData").keyup(function(event){
        if(event.keyCode == 13){
            updateRowR();
            $('#lastPage').val('0');
        }
    });



    function reportUNSelang(){
        var id_periode_sel, id_wilayah_sel, enc, kode, url;
        enc = '<?php echo UUIDBaru(); ?>';
        kode = '<?php echo kodeBaru(); ?>';
        id_periode_sel = $('#id_periode_sel').val();
        id_wilayah_sel = $('#id_wilayah_sel').val();
        url = 'report/test-yearly-recap/?kode='+kode+'&enc='+enc+'&p_sel='+id_periode_sel+'&w_sel='+id_wilayah_sel;
        if(id_periode_sel==''){
            tampilkanPesan('0','Pilih periode dahulu.');
        }
        else if(id_wilayah_sel==''){
            tampilkanPesan('0','Pilih wilayah terlebih dahulu.');
        }
        else{
            window.open(url, '_blank');
        }
    }

    

</script>