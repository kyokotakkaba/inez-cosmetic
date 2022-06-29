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

    if($_SESSION['menu']!=='train'){
        $_SESSION['menu'] = 'train';
    }

    $tgl_sekarang  = date('Y-m-d');
?>
<h2 class="ui block header">
    <i class="address book icon"></i>
    <div class="content">
        Catatan
        <div class="sub header">
            Pelaksanaan pelatihan
        </div>
    </div>
</h2>

<?php
    if($idPeriode=='0'){
?>
        <div class="ui message">
            <div class="header">
                Tidak ada periode
            </div>
            <p>Silahkkan set periode yang sedang aktif untuk tahun ini.</p>
        </div>
<?php        
        exit();
    }
?>

<div id="dataDisplay" class="ui form">

    <div class="ui styled fluid accordion">
        <div class="title">
            <i class="dropdown icon"></i> Data topik
        </div>
        <div class="content">
            <div id="topikPlace">
                <!-- load topic here -->
                <i class="info circle icon"></i> <i>Load Data..</i>
            </div>
        </div>

        <div class="active title">
            <i class="dropdown icon"></i> Presensi - Catatan
        </div>
        <div class="active content">
            <div class="field">
                <div class="ui info message">
                    <div class="header">Info</div>
                    <p>
                        Jika ingin set pada tanggal yang telah berlalu atau merubah wilayah target pencatatan, silahkan pilih tanggal, atau atur wilayah terlebih dahulu dan klik <i class="redo icon"></i> Reload
                    </p>
                </div>
            </div>
            <div class="ui basic vertical segment clearing" style="margin: 0px; padding: 0px;">
                <div class="ui icon button right floated" onclick="updateTrain()" data-content="Reload">
                    <i class="redo icon"></i>
                </div>
            </div>
            <div class="fields" style="margin-top: 10px;">
                <div class="ten wide field">
                    <div class="ui input search" onkeyup="cariAjax('wilayah','id_wil', '../')">
                        <input class="prompt" id="nama_wil" name="nama_wil" placeholder="Wilayah" type="text" >
                        <input id="id_wil" name="id_wil" type="hidden" value="" onchange="updateTrain()">
                        <div class="results"></div>
                    </div>
                </div>
                <div class="six wide field">
                    <div class="ui input calendar" id="tglPelatihan" style="width: 100%;">
                        <div class="ui input left icon">
                            <i class="calendar alternate outline icon"></i>
                            <input type="text" placeholder="Tanggal lahir YYYY-MM-DD" name="tgl" id="tgl" value="<?php echo $tgl_sekarang; ?>" onchange="updateTrain()" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="field" style="margin-top: 10px;">
                <div class="ui icon input">
                    <input id="searchData" placeholder="Cari Data.." type="text" onkeyup="cariTrain()" />
                    <i class="search icon"></i>
                </div>
            </div>

            <table class="ui striped selectable table">
                <thead>
                    <tr>
                        <th width="4%">No</th>
                        <th>Data BA/ BC</th>
                        <th width="36%">Opsi</th>
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
                        <th colspan="3">
                            <div class="ui vertical basic segment clearing" style="padding: 0px;">
                                <select class="ui dropdown compact" id="jumlahRow"  onchange="updateTrain()">
                                    <option value="250">250 Baris</option>
                                    <option value="350">350 Baris</option>
                                    <option value="500">500 Baris</option>
                                </select>

                                <input type="hidden" id="lastPage" value="0">
                                <div class="ui right floated pagination menu" id="pageNumber">
                                    <!-- show row -->
                                    <div class="active item">
                                        0
                                    </div>
                                </div>
                            </div>
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="title">
            <i class="dropdown icon"></i> Laporan
        </div>
            <div class="content">
                <div class="field">
                    <label>Wilayah</label>
                    <select id="sel_id_wil" name="sel_id_wil" class="ui search dropdown fluid" multiple="">
<?php
    $q = "
            SELECT
                id,
                nama
            FROM
                wilayah

            WHERE
                hapus = '0'

            ORDER BY
                nama ASC
    ";
    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);
    if($c==0){
?>
                    <option value="Tidak ada data wilayah"></option>
<?php
    }
    else{
?>
        <option value="">Pilih</option>
<?php
        while ($r = mysqli_fetch_assoc($e)) {
            $idWil = $r['id'];
            $nmWil = $r['nama'];
?>
                    <option value="<?php echo $idWil; ?>">
                        <?php echo $nmWil; ?>
                    </option>
<?php            
        }
    }
?>                    
                    </select>
                </div>
        <!-- yoko's code here  -->
        <b>Periode Laporan</b>
 
        <div class="ui basic vertical segment clearing" style="margin: 0px; padding: 0px 0px 10px 0px;">
                <div class="ui input calendar" id="tglPelatihan2" >
                    <div class="ui input left icon">
                        <i class="calendar alternate outline icon"></i>
                        <input type="text" style="width:150px" placeholder="YYYY-MM-DD" name="tgl" id="tgl_awal" value="" autocomplete="off"/>
                    </div>
                </div> -
                <div class="ui input calendar" id="tglPelatihan3" >
                    <div class="ui input left icon">
                        <i class="calendar alternate outline icon"></i>
                        <input type="text" style="width:150px" placeholder="YYYY-MM-DD" name="tgl" id="tgl_akhir" value="" autocomplete="off"/>
                    </div>
                </div>
        </div>
        <!--  -->
                <div class="field">
                    <div id="btnNoteReport" onclick="reportCatatan()" class="ui icon button">
                        <i class="print icon"></i> Laporan
                    </div>
                    <div onclick="exportCatatan()" class="ui icon green button">
                        <i class="download cloud icon"></i> Excel
                    </div>
                </div>
                
            </div>
        </div>
    </div>

    


    

</div>
<div id="formDisplay" style="display: none;">
    <!-- load form here -->
</div>


<script type="text/javascript">
    $('.accordion').accordion();
    $('.button').popup();
    $('.dropdown').dropdown({ fullTextSearch: "exact" });

    loadTopic();

    function loadTopic(){
        loadingMulai();
        $.ajax({
            type:"post",
            async:true,
            url:"interface/train-note-topic.php",
            data:{
                'view':'1'
            },
            success:function(data){
                $("#topikPlace").html(data);
                loadingSelesai();
            }
        })
    }



    




    updateTrain();

    function updateTrain(){
        var nama_wil = $('#nama_wil').val();
        if(nama_wil == ''){
            $('#id_wil').val('');
        }
        dataTrain();
        showTrain();
    }
    
    function dataTrain(){
        loadingMulai();
        var start, limit, key, id_wil, tgl;
        start = $('#lastPage').val();
        limit = $("#jumlahRow").val();
        key = $("#searchData").val();
        id_wil = $("#id_wil").val();
        tgl = $("#tgl").val();

        $.post('interface/train-list.php',{view:'1', start: start, limit: limit, cari: key, id_wil: id_wil, tgl: tgl},
            function(result){
                $("#resultData").html(result);
                loadingSelesai();
            }
        );
    }

    function showTrain(){
        loadingMulai();
        var limit, key, id_wil, tgl;
        limit = $("#jumlahRow").val();
        key = $("#searchData").val();
        id_wil = $("#id_wil").val();
        tgl = $("#tgl").val();

        $.post('interface/train-list-number.php',{view:'1', limit: limit, cari: key, id_wil: id_wil, tgl: tgl},
            function(result){
                $("#pageNumber").html(result);
                loadingSelesai();
            }
        );
    }


    function updateListTrain(start, id){
        $('#lastPage').val(start);
        $("#pageNumber a").removeClass("active");
        dataTrain();
        $("#number"+id).addClass("active");
    }

    
    function cariTrain(){
        var number, key, pjg;
        number = event.keyCode;
        if( number == 13){
            key = $("#searchData").val();
            pjg = key.length;
            if(pjg < 3){
                tampilkanPesan('0', 'Pencarian membutuhkan minimal 3 karakter.');
            }
            else{
                $('#lastPage').val('0');
                updateTrain();
            }
        }
    }


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
    $('#tglPelatihan3').calendar({
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

    function triggerNeg(idK){
        $('#trigBtn'+idK).transition('slide right');
        $('#negBtn'+idK).transition('slide left');
    }

    function setPresensi(idK, ket){
        var tgl = $('#tgl').val();
        loadingMulai();
        $.ajax({
            type:"post",
            async:true,
            url:"interface/train-set-presensi.php",
            data:{
                'view': '1',
                'idK': idK,
                'ket': ket,
                'tgl': tgl
            },
            success:function(data){
                $("#feedBack").html(data);
                loadingSelesai();
            }
        })
    }



    function reportCatatan(){
        var selected, enc,kode, url;
        enc = '<?php echo UUIDBaru(); ?>';
        kode = '<?php echo kodeBaru(); ?>';
        selected = $('#sel_id_wil').val();
        start = $('#tgl_awal').val();
        end = $('#tgl_akhir').val();
        url = 'report/train/?kode='+kode+'&enc='+enc+'&sel='+selected+'&start='+start+'&end='+end;
        if(selected==''){
            tampilkanPesan('0','Pilih wilayah terlebih dahulu.');
        }
        else{
            window.open(url, '_blank');
        }
    }


    function exportCatatan(){
        var selected, enc,kode, url;
        enc = '<?php echo UUIDBaru(); ?>';
        kode = '<?php echo kodeBaru(); ?>';
        selected = $('#sel_id_wil').val();
        url = 'report/train/export/?kode='+kode+'&enc='+enc+'&sel='+selected+'&start='+start+'&end='+end;
        if(selected==''){
            tampilkanPesan('0','Pilih wilayah terlebih dahulu.');
        }
        else{
            window.open(url, '_blank');
        }
    }

</script>