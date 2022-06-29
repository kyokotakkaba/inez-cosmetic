<?php
    session_start();
    $appSection = 'user';

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

    if($_SESSION['menu']!=='test'){
        $_SESSION['menu'] = 'test';
    }
?>

<div id="dataDisplay">
    <h2 class="ui block header">
        <i class="calendar alternate outline icon"></i>
        <div class="content">
            Ujian
            <div class="sub header">
                Ikuti pelaksanaan secara <i>daring</i>
            </div>
        </div>
    </h2>
    
    <div class="ui vertical basic segment clearing" style="padding: 0px; margin-top: 16px;">
        <select class="ui dropdown compact" id="jumlahRow"  onchange="updateRow()">
            <option value="4">4 Data</option>
            <option value="8">8 Data</option>
        </select>

        <input id="searchData" placeholder="Cari Data.." type="hidden" />
        <input type="hidden" id="lastPage" value="0">
        
        <div class="ui right floated pagination menu" id="pageNumber">
            <!-- show row -->
            <div class="active item">
                0
            </div>
        </div>
    </div>

    <div class="ui divided items" id="resultData" style="margin-top: 10px; margin-bottom: 0px; padding: 0px;">
        <!-- load ccontent here -->
        <i class="ifo circle icon"></i> <i>Load Data..</i>
    </div>
</div>

<div id="formDisplay" style="display: none;">
    <!-- load form here -->
</div>
<script type="text/javascript">
    $('.dropdown').dropdown();

<?php
    if(!empty($_SESSION['ujianDijalani'])){
        if($_SESSION['ujianDijalani']!=='0'){
?>
            setTimeout(function(){
                loadForm('test',"<?php echo $_SESSION['ujianDijalani']; ?>");            
            }, 600);
<?php        
        }
    }
?>    

    updateRow();

</script>