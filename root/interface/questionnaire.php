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

    if($_SESSION['menu']!=='questionnaire'){
        $_SESSION['menu'] = 'questionnaire';
    }
?>
<h2 class="ui block header">
    <i class="chart bar icon"></i>
    <div class="content">
        Survey
        <div class="sub header">
            Manajemen data angket
        </div>
    </div>
</h2>
<div id="dataDisplay">
    <div class="ui three doubling cards">
<?php
    $q = "
            SELECT
                id

            FROM
                produk
                
            WHERE
                hapus = '0'
    ";
    $e = mysqli_query($conn, $q);
    $jmlProduk = mysqli_num_rows($e);
?>                
            <div class="ui card" style="padding-top:4px;">
                <div class="content">
                    <div class="center aligned header">Produk</div>
                    <div class="center aligned description">
                        <p>Data untuk dijadikan fokus angket terkait penggunaan</p>
                    </div>
                </div>
                <div class="extra content" style="padding: 8px;">
                    <span class="right floated" onclick="loadForm('questionnaire-produk','0')">
                        <i class="setting icon"></i>    
                    </span>
                    <span>
                        <i class="list icon"></i> <span id="txtJmlProduk"><?php echo $jmlProduk; ?></span>
                    </span>
                </div>
            </div>

<?php
    $q = "
            SELECT
                id

            FROM
                angket_kategori

            WHERE
                hapus = '0'
    ";
    $e = mysqli_query($conn, $q);
    $jmlJenisA = mysqli_num_rows($e);
?>                
            <div class="ui card" style="padding-top:4px;">
                <div class="content">
                    <div class="center aligned header">Jenis angket</div>
                    <div class="center aligned description">
                        <p>Pembagian angket berdasarkan tujuan diterbitkannya</p>
                    </div>
                </div>
                <div class="extra content" style="padding: 8px;">
                    <span class="right floated" onclick="loadForm('questionnaire-jenis','0')">
                        <i class="setting icon"></i>    
                    </span>
                    <span>
                        <i class="list icon"></i> <span id="txtJmlJenisA"><?php echo $jmlJenisA; ?></span>
                    </span>
                </div>
            </div> 

<?php
    $q = "
            SELECT
                id

            FROM
                angket_label

            WHERE
                hapus = '0'
    ";
    $e = mysqli_query($conn, $q);
    $jmlLabel = mysqli_num_rows($e);
?>                
            <div class="ui card" style="padding-top:4px;">
                <div class="content">
                    <div class="center aligned header">Jenis label</div>
                    <div class="center aligned description">
                        <p>Tampilan teks (label) pada tombol respon di tiap pertanyaan/ pernyataan survey</p>
                    </div>
                </div>
                <div class="extra content" style="padding: 8px;">
                    <span class="right floated" onclick="loadForm('questionnaire-label','0')">
                        <i class="setting icon"></i>    
                    </span>
                    <span>
                        <i class="list icon"></i> <span id="txtJmlLabel"><?php echo $jmlLabel; ?></span>
                    </span>
                </div>
            </div>             
    </div>


    
    <div class="ui basic vertical segment clearing" style="margin: 0px; padding-bottom: 0px;">
        <div class="ui icon button right floated" onclick="updateRowQ()" data-content="Reload">
            <i class="redo icon"></i>
        </div>
        <div class="ui icon button green right floated" onclick="loadForm('questionnaire','0')">
            <i class="plus icon"></i> Tambah
        </div>

        <select id="responden" class="ui compact dropdown" onchange="updateRowQ()">
            <option value="semua">Semua</option>
            <option value="admin">Supervisor</option>
            <option value="user">Karyawan</option>
        </select>

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
                <th width="4%">Aktif?</th>
                <th width="30%">Opsi</th>
            </tr>
        </thead>
        <tbody id="resultData">
            <!-- load data here -->
            <tr>
                <td colspan="4">
                    <i class="info circle icon"></i> <i>Load Data..</i>
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4">
                    <div class="ui vertical basic segment clearing" style="padding: 0px;">
                        <select class="ui dropdown compact" id="jumlahRow"  onchange="updateRowQ()">
                            <option value="25">25 Baris</option>
                            <option value="35">35 Baris</option>
                            <option value="50">50 Baris</option>
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
<div id="formDisplay" style="display: none;">
    <!-- load form here -->
</div>


<script type="text/javascript">
    $('.button').popup();
    $('.dropdown').dropdown();

    updateRowQ();

    function updateRowQ(){
        dataListQ();
        showRowQ();
    }
    
    function dataListQ(){
        loadingMulai();
        var start, limit, key, responden;
        start = $('#lastPage').val();
        limit = $("#jumlahRow").val();
        key = $("#searchData").val();

        responden = $("#responden").val();

        $.post('interface/questionnaire-list.php',{view:'1', start: start, limit: limit, cari: key, responden: responden},
            function(result){
                $("#resultData").html(result);
                loadingSelesai();
            }
        );
    }

    function showRowQ(){
        loadingMulai();
        var limit, key, responden;
        limit = $("#jumlahRow").val();
        key = $("#searchData").val();

        responden = $("#responden").val();

        $.post('interface/questionnaire-list-number.php',{view:'1', limit: limit, cari: key, responden: responden},
            function(result){
                $("#pageNumber").html(result);
                loadingSelesai();
            }
        );
    }


    function updateList(start, id){
        $('#lastPage').val(start);
        $("#pageNumber a").removeClass("active");
        dataListQ();
        $("#number"+id).addClass("active");
    }

    
    $("#searchData").keyup(function(event){
        if(event.keyCode == 13){
            updateRowQ();
            $('#lastPage').val('0');
        }
    });


    



    function berhasilMenambahkanPnK(prefix){
        var jmlA, jmlNew;
        jmlA = $('#txtJml'+prefix).text();
        jmlNew = parseInt(jmlA)+1;
        $('#txtJml'+prefix).text(jmlNew);
    }

    function berhasilMenghapusPnK(prefix){
        var jmlA, jmlNew;
        jmlA = $('#txtJml'+prefix).text();
        jmlNew = parseInt(jmlA)-1;
        $('#txtJml'+prefix).text(jmlNew);
    }
</script>