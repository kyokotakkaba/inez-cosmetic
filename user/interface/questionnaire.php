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

    if($_SESSION['menu']!=='questionnaire'){
        $_SESSION['menu'] = 'questionnaire';
    }
?>

<h2 class="ui block header">
    <i class="check square outline icon"></i>
    <div class="content">
        Survey
        <div class="sub header">
            Isi angket dari perusahaan
        </div>
    </div>
</h2>



<div id="dataDisplay">
    <div class="field">
        <div class="ui icon input">
            <input id="searchData" placeholder="Cari Data.." type="text" onkeyup="cariData()" />
            <i class="search icon"></i>
        </div>
    </div>



    <div class="ui divided unstackable items" id="resultData">
        <!-- load ccontent here -->
        <p>
            <i class="info circle icon"></i> <i>Load Data..</i>
        </p>
    </div>



    <div class="ui vertical basic segment clearing" style="padding: 0px;">
        <select class="ui dropdown compact" id="jumlahRow"  onchange="updateRow()">
            <option value="25">25 Baris</option>
            <option value="35">35 Baris</option>
            <option value="50">50 Baris</option>
            <option value="75">75 Baris</option>
        </select>

        <input type="hidden" id="lastPage" value="0">
        <div class="ui right floated pagination menu" id="pageNumber">
            <!-- show row -->
            <div class="active icon">
                0
            </div>
        </div>
    </div>
</div>



<div id="formDisplay" style="display: none;">
    <!-- load form here -->
</div>



<div id="subDisplay" style="display: none;">
    <!-- load form here -->
</div>





<script type="text/javascript">
    $('.button').popup();
    $('.dropdown').dropdown();



<?php
    if(!empty($_SESSION['angketDibuka'])){
        if($_SESSION['angketDibuka']!=='0'){
?>
            setTimeout(function(){
                loadForm('questionnaire','<?php echo $_SESSION['angketDibuka']; ?>');            
            }, 600);

<?php        
        }
    }
?>    



    updateRow();

</script>