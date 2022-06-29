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
                deskripsi,
                standar

            FROM 
                wilayah_kelompok

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
        $standar = '';
        $sub = 'Tambah ';
    }
    else{
        $r = mysqli_fetch_assoc($e);

        $nama = $r['nama'];
        $deskripsi = $r['deskripsi'];
        $standar = $r['standar'];
        $sub = 'Edit ';
    }

    $sub .= ' Kelompok Wilayah Standar UN';
?>
<div class="ui message">
    <div class="ui breadcrumb">
        <div class="section"><?php echo $main; ?></div>
        <i class="right angle icon divider"></i>
        <div class="active section"><?php echo $sub; ?></div>
    </div>
</div>
<form id="frmWilayahKelompok">
    <input type="hidden" name="id" value="<?php echo $id; ?>">

    <div class="field">
        <div class="ui icon button" onclick="backToMain()">
            <i class="left chevron icon"></i> Kembali
        </div>    
    </div>

    <div class="field">
        <label>Nama kelompok wilayah</label>
        <div class="ui input">
            <input type="text" id="nama" name="nama" placeholder="Nama kelompok wilayah" maxlength="32" required="required" value="<?php echo $nama; ?>">
        </div>
    </div>
    <div class="field">
        <label>Deskripsi</label>
        <div class="ui input">
            <textarea id="deskripsi" name="deskripsi" placeholder="Deskripsi" maxlength="128" required="required"><?php echo $deskripsi; ?></textarea>
        </div>
    </div>
    <div class="four wide field">
        <label>Stadnar kelulusan</label>
        <div class="ui right labeled input">
            <input type="number" id="standar" name="standar" placeholder="50" value="<?php echo $standar; ?>" required="required">
            <div class="ui label">%</div>
        </div>
    </div>
    <div class="field">
        <p style="font-size: 9pt; color: #60646D; margin-top: 10px;">
            Pengaturan target harapan prosentase kelulusan peserta ujian nasional pada kelompok wilayah.
        </p>
    </div>
    
    <div class="field">
        <button class="ui icon button blue" type="submit">
            <i class="save icon"></i> Simpan
        </button>
    </div>
</form>


<script type="text/javascript">

    $('#frmWilayahKelompok').submit(function(e){
        e.preventDefault();
        loadingMulai();
        $.ajax({
            type:'post',
            async:true,
            data:$('#frmWilayahKelompok').serialize(),
            url:'interface/set-wilayah-kelompok-form-process.php',
            success: function(data){
                $('#feedBack').html(data);
                loadingSelesai();
            }
        })
    })

</script>