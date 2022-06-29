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

    if($_SESSION['menu']!=='qa'){
        $_SESSION['menu'] = 'qa';
    }
?>
<h2 class="ui block header">
    <i class="comments icon"></i>
    <div class="content">
        Question & Answer
        <div class="sub header">
            Manajemen data pertanyaan yang diajukan peserta
        </div>
    </div>
</h2>

<div id="dataDisplay">
    <div class="ui basic vertical segment clearing" style="margin: 0px; padding: 0px;">
        <div class="ui icon button right floated" onclick="updateQa()" data-content="Reload">
            <i class="redo icon"></i>
        </div>

        <select id="id_kelompok" name="id_kelompok" class="ui compact dropdown" onchange="updateQa()">
<?php
    $q = "
            SELECT 
                id, 
                nama
            FROM 
                materi_kelompok 
            WHERE
                hapus = '0'
    ";
    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);

    if($c=='0'){
?>
        <option value="">Belum ada kelompok materi</option>
<?php        
    }
    else{
?>
            <option value="semua">Semua</option>
<?php        
        while ($d=mysqli_fetch_assoc($e)) {
                 $idK = $d['id'];
                 $namaK = $d['nama'];
?>
            <option value="<?php echo $idK; ?>">
                <?php echo $namaK; ?>
            </option>
<?php                 
             }     
    }
?>
        </select>
        <select id="status" name="status" class="ui compact dropdown" onchange="updateQa()">
            <option value="semua">Semua</option>
            <option value="terjawab">Terjawab</option>
            <option value="belum">Belum terjawab</option>
        </select>
    </div>
    <div class="field" style="margin-top: 10px;">
        <div class="ui icon input">
            <input id="searchData" placeholder="Cari Data.." type="text" />
            <i class="search icon"></i>
        </div>
    </div>

    <div class="ui comments" id="resultData">
        <!-- load ccontent here -->
        <i class="info circle icon"></i> <i>Load Data..</i>
    </div>

    <div class="ui vertical basic segment clearing" style="padding: 0px;">
        <select class="ui dropdown compact" id="jumlahRow"  onchange="updateQa()">
            <option value="100">100 Baris</option>
            <option value="150">150 Baris</option>
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

    updateQa();

    function updateQa(){
        dataListQa();
        showRowQa();
    }
    
    function dataListQa(){
        loadingMulai();
        var start, limit, key, id_kelompok, status;
        start = $('#lastPage').val();
        limit = $("#jumlahRow").val();
        key = $("#searchData").val();
        id_kelompok = $("#id_kelompok").val();
        status = $("#status").val();

        $.post('interface/qa-list.php',{view:'1', start: start, limit: limit, cari: key, id_kelompok: id_kelompok, status: status},
            function(result){
                $("#resultData").html(result);
                loadingSelesai();
            }
        );
    }

    function showRowQa(){
        loadingMulai();
        var limit, key, id_kelompok, status;
        limit = $("#jumlahRow").val();
        key = $("#searchData").val();
        id_kelompok = $("#id_kelompok").val();
        status = $("#status").val();

        $.post('interface/qa-list-number.php',{view:'1', limit: limit, cari: key, id_kelompok: id_kelompok, status: status},
            function(result){
                $("#pageNumber").html(result);
                loadingSelesai();
            }
        );
    }


    function updateListQa(start, id){
        $('#lastPage').val(start);
        $("#pageNumber a").removeClass("active");
        dataListQa();
        $("#number"+id).addClass("active");
    }

    
    $("#searchData").keyup(function(event){
        if(event.keyCode == 13){
            updateQa();
            $('#lastPage').val('0');
        }
    });


    function simpanJawaban(prefix){
        var jawaban = $('#jawaban'+prefix).val();
        
        if(jawaban==''){
            tampilkanPesan('0','Isi jawaban.');
        }
        else{
            $('#formJawab'+prefix).addClass('loading');
            $.ajax({
                type:"post",
                async:true,
                url:"interface/qa-save-answer.php",
                data:{
                    'view':'1',
                    'idPertanyaan':prefix,
                    'jawaban':jawaban
                },
                success:function(data){
                    $("#feedBack").html(data);
                    $('#formJawab'+prefix).removeClass('loading');
                }
            })
        }
    }

</script>