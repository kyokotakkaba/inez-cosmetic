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

    if($_SESSION['menu']!=='employee'){
        $_SESSION['menu'] = 'employee';
    }

    $qSelect ="
    SELECT waktu FROM sync_date WHERE nama='laporan pembelajaran'
    ";

    $eSelect = mysqli_query($conn, $qSelect);
    $c = mysqli_num_rows($eSelect);
    $d = mysqli_fetch_assoc($eSelect);
?>
<h2 class="ui block header">
    <i class="users icon"></i>
    <div class="content">
        Sinkronisasi Data Pembelajaran
        <div class="sub header">
            Data Pembelajaran
        </div>
    </div>
</h2>
<div id="dataDisplay">
    <div class="ui basic vertical segment clearing" style="margin: 0px; padding: 0px;">
    <div class="ui icon button right floated" onclick="updateRow()" data-content="Reload">
            <i class="redo icon"></i>
        </div>
        <div class="ui floating blue button right floated" onclick="sync()">
            <span class="text">Sinkronisasi</span>
        </div>
        <div class="ui floating green button right floated">
            <a  data-content="Unduh Excel" href="report/pembelajaran/export/" target="_BLANK" style="color:white">
                <i class="cloud download icon"></i>
                <span>Export</span>
            </a>    
        </div>
        
        
        <div class="ui icon input">
            <input id="searchData" placeholder="Cari Data.." type="text" onkeyup="cariData()" />
            <i class="search icon"></i>
        </div>
        <div class="text" style="padding-top:8px;">
            <p>Terakhir di update: <?php echo $d['waktu']; ?></p>
        </div>
    </div>
    <div style="overflow-x:auto; max-height:100vh;">
    <table class="ui striped selectable table" >
        <thead style="position: sticky; top: 0; z-index:1;">
            <tr>
                <th width="20%">USER ID</th>
                <th width="20%">NIP</th>
                <th width="20%">AREA</th>
                <th width="20%">ID MODUL</th>
                <th width="20%">NAMA MODUL</th>
                <th width="20%">JUMLAH MODUL</th>
                <th width="20%">SELESAI</th>
                <th width="20%">TERAKHIR DIBUKA</th>
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
                <th colspan="100">
                    <div class="ui vertical basic segment clearing" style="padding: 0px;">
                        <select class="ui dropdown compact" id="jumlahRow"  onchange="updateRow()">
                            <option value="10">10 Baris</option>
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
    </div>
</div>
<div id="formDisplay" style="display: none;">
    <!-- load form here -->
</div>


<script type="text/javascript">
    $('.button').popup();
    $('.dropdown').dropdown();

    updateRow();
    
    function sync(){
    if (confirm("Proses Sinkronisasi membutuhkan waktu panjang, Apakah Anda yakin?") == true) {
        loadingMulai();

        $.ajax({
            type:'get',
            url:'interface/sinkron-data.php',
            async:true,
            success:function(data){
                console.log(data);
                updateRow();
                loadingSelesai();
            }
        })
    }
    }
</script>