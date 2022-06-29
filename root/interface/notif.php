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

    if($_SESSION['menu']!=='notif'){
        $_SESSION['menu'] = 'notif';
    }
?>
<h2 class="ui block header">
    <i class="inbox icon"></i>
    <div class="content">
        Notifikasi
        <div class="sub header">
            Info sistem E-Learning
        </div>
    </div>
</h2>

<div class="ui vertical basic segment clearing" style="padding: 0px;">
    <select class="ui dropdown compact" id="jumlahRow"  onchange="updateRow()">
        <option value="50">50 Baris</option>
        <option value="75">75 Baris</option>
        <option value="100">100 Baris</option>
    </select>

     <input id="searchData" placeholder="Cari Data.." type="hidden" onkeyup="cariData()" />
     <input type="hidden" id="lastPage" value="0">
    <div class="ui right floated pagination menu" id="pageNumber">
        <!-- show row -->
        <div class="active item">
            0
        </div>
    </div>
</div>
<div id="resultData" style="margin-top: 16px;">
    <!-- load data here -->
    <i class="info circle icon"></i> <i>Load Data..</i>
</div>

<script type="text/javascript">
    $('.dropdown').dropdown();

    updateRow();
</script>