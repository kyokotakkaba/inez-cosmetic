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

    $main = 'Setting';

    $idData = saring($_POST['idData']);

    $q = "
            SELECT 
                huruf, 
                min, 
                max

            FROM 
                ujian_grade

            WHERE 
                id = '$idData'

            LIMIT
                1
    ";
    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);

    if($c=='0'){
        $huruf = '';
        $min = '';
        $max = '';
        $sub = 'Tambah ';
    }
    else{
        $r = mysqli_fetch_assoc($e);

        $huruf = $r['huruf'];
        $min = $r['min'];
        $max = $r['max'];
        $sub = 'Edit ';
    }

    $sub .= 'Grade Nilai Ujian';

?>
<div class="ui message">
    <div class="ui breadcrumb">
        <div class="section"><?php echo $main; ?></div>
        <i class="right angle icon divider"></i>
        <div class="active section"><?php echo $sub; ?></div>
    </div>
</div>

<form id="frmGrade">
    <input type="hidden" name="id" value="<?php echo $idData; ?>">

    <div class="field">
        <div class="ui icon button" onclick="backToMain()">
            <i class="left chevron icon"></i> Kembali
        </div>    
    </div>
    <div class="fields">
        <div class="four wide field">
            <label>Awal</label>
            <div class="ui input">
                <input type="number" id="min" name="min" placeholder="Awal" maxlength="2" required="required" min="0" max="100" value="<?php echo $min; ?>">
            </div>
        </div>
        <div class="four wide field">
            <label>Sampai</label>
            <div class="ui input">
                <input type="number" id="max" name="max" placeholder="Sampai" maxlength="2" required="required" min="0" max="100" value="<?php echo $max; ?>">
            </div>
        </div>    
    </div>

    <div class="field">
        <label>Grade</label>
        <div class="ui input">
            <input type="text" id="huruf" name="huruf" placeholder="Grade" maxlength="16" required="required" value="<?php echo $huruf; ?>">
        </div>
    </div>
    
    <div class="field">
        <button class="ui icon button blue" type="submit">
            <i class="save icon"></i> Simpan
        </button>
    </div>
</form>


<script type="text/javascript">

    $('#frmGrade').submit(function(e){
        e.preventDefault();
        loadingMulai();
        $.ajax({
            type:'post',
            async:true,
            data:$('#frmGrade').serialize(),
            url:'interface/set-grade-form-process.php',
            success: function(data){
                $('#feedBack').html(data);
                loadingSelesai();
            }
        })
    })

</script>