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

    $main = 'Pelatihan';

    $id = saring($_POST['idData']);

    if($id=='0'){
        $nama = '';
        $sub = 'Tambah';
    }
    else{
        $q = "
                SELECT 
                    nama

                FROM 
                    pelatihan_catatan_topik

                WHERE
                    id = '$id'

                LIMIT
                    1
        ";
        $e = mysqli_query($conn, $q);
        $c = mysqli_num_rows($e);
        
        if($c == '0'){
            echo "DATA NOT FOUND";
            exit();
        }

        $r = mysqli_fetch_assoc($e);
        $nama = $r['nama'];
        $sub = 'Edit';
    }

    $sub .= ' Data Topik Pelatihan';
?>
<div class="ui message">
    <div class="ui breadcrumb">
        <div class="section"><?php echo $main; ?></div>
        <i class="right angle icon divider"></i>
        <div class="active section"><?php echo $sub; ?></div>
    </div>
</div>
<div class="ui basic vertical segment clearing" style="margin: 0px; padding: 0px;">
    <div class="ui icon button right floated" data-content="Tutup" onclick="backToMain()">
        <i class="close icon"></i>
    </div>
</div>
<form id="frmNoteTopic">
    <input type="hidden" name="id" value="<?php echo $id; ?>">

    <div class="field">
        <label>Topik</label>
        <div class="ui input">
            <input type="text" maxlength="128" id="nama" name="nama" placeholder="Topik" value="<?php echo $nama; ?>">
        </div>
    </div>
    
    <div class="field">
        <button class="ui icon button blue" type="submit">
            <i class="save icon"></i> Simpan
        </button>
    </div>
</form>

<script type="text/javascript">
    $('.button').popup();

    $('#frmNoteTopic').submit(function(e){
        e.preventDefault();
        loadingMulai();
        $.ajax({
            type:'post',
            async:true,
            data:$('#frmNoteTopic').serialize(),
            url:'interface/train-note-topic-form-process.php',
            success: function(data){
                $('#feedBack').html(data);
                loadingSelesai();
            }
        })
    })
</script>