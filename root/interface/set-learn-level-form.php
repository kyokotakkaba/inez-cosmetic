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

    $id = saring($_POST['idData']);

    $q = "
            SELECT 
                nama, 
                deskripsi
            FROM 
                tingkat_belajar 
            WHERE
                id = '$id'
            LIMIT
                1
    ";
    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);

    if($c=='0'){
        $nama = '';
        $deskripsi = '';
        $sub = 'Tambah ';
    }
    else{
        $r = mysqli_fetch_assoc($e);

        $nama = $r['nama'];
        $deskripsi = $r['deskripsi'];
        $sub = 'Edit ';
    }

    $sub .= 'Data Tingkat Belajar';
?>

<div class="ui message">
    <div class="ui breadcrumb">
        <div class="section"><?php echo $main; ?></div>
        <i class="right angle icon divider"></i>
        <div class="active section"><?php echo $sub; ?></div>
    </div>
</div>

<form id="frmLearnLevel">
    <input type="hidden" name="id" value="<?php echo $id; ?>">

    <div class="field">
        <div class="ui icon button" onclick="backToMain()">
            <i class="left chevron icon"></i> Kembali
        </div>    
    </div>

    <div class="field">
        <label>Nama tingkat</label>
        <div class="ui input">
            <input type="text" id="nama" name="nama" placeholder="Nama tingkat" maxlength="32" required="required" value="<?php echo $nama; ?>">
        </div>
    </div>
    <div class="field">
        <label>Deskripsi</label>
        <div class="ui input">
            <textarea id="deskripsi" name="deskripsi" placeholder="Deskripsi" maxlength="128" required="required"><?php echo $deskripsi; ?></textarea>
        </div>
    </div>
    <div class="field">
        <p style="font-size: 9pt; color: #60646D; margin-top: 10px;">
            Pengaturan tingkat belajar kkaryawan.
        </p>
    </div>
    
    <div class="field">
        <button class="ui icon button blue" type="submit">
            <i class="save icon"></i> Simpan
        </button>
    </div>
</form>


<script type="text/javascript">

    $('#frmLearnLevel').submit(function(e){
        e.preventDefault();
        loadingMulai();
        $.ajax({
            type:'post',
            async:true,
            data:$('#frmLearnLevel').serialize(),
            url:'interface/set-learn-level-form-process.php',
            success: function(data){
                $('#feedBack').html(data);
                loadingSelesai();
            }
        })
    })

</script>