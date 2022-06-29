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

    $main = 'Materi';

    $idData = saring($_POST['idData']);

    if($idData=='0'){
        $nama = '';
        $deskripsi = '';
        $n_pk = '0';
        $lblPk = 'Tidak';

        $sub = 'Tambah';
    }
    else{
        $q = "
                SELECT 
                    nama, 
                    deskripsi,
                    n_pk

                FROM 
                    materi_kelompok

                WHERE
                    id = '$idData'
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
        $deskripsi = $r['deskripsi'];
        $n_pk = $r['n_pk'];
        if($n_pk=='1'){
            $lblPk = 'Ya';
        }
        else{
            $lblPk = 'Tidak';
        }
        $sub = 'Edit';
    }

    $sub .= ' Data Kelompok Materi';
?>
<div class="ui message">
    <div class="ui breadcrumb">
        <div class="section"><?php echo $main; ?></div>
        <i class="right angle icon divider"></i>
        <div class="active section"><?php echo $sub; ?></div>
    </div>
</div>
<div class="field">
    <div class="ui icon button" onclick="backToMain()">
        <i class="left chevron icon"></i> Kembali
    </div>    
</div>
<form id="frmKelompok">
    <input type="hidden" name="id" value="<?php echo $idData; ?>">

    <div class="field">
        <label>Nama kelompok materi</label>
        <div class="ui input">
            <input type="text" maxlength="64" id="nama" name="nama" required="required" placeholder="Nama" value="<?php echo $nama; ?>">
        </div>
    </div>
    <div class="field">
        <label>Deskripsi</label>
        <input type="text" id="deskripsi" name="deskripsi" required="required" maxlength="128" placeholder="Deskripsi" value="<?php echo $deskripsi; ?>">
    </div>
    
    <div class="field">
        <label>Materi Product Knowledge (PK) ?</label>
        <div id="chkPk" class="ui toggle checkbox <?php if($n_pk=='1'){ ?> checked <?php } ?>">
            <input id="n_pk" type="checkbox" <?php if($n_pk=='1'){ ?> checked="checked" <?php } ?> name="n_pk" value="<?php echo $n_pk; ?>">
            <label id="lblPk" for="n_pk"><?php echo $lblPk; ?></label>
        </div>

        <p style="font-size: 9pt; color: #60646D; margin-top: 10px;">
            *Deteksi PK digunakan sistem untuk <i>grading</i> hasil ujian.<br>
            *Aktifkan hanya pada kelompok materi PK
        </p>
    </div>

    <div class="field">
        <button class="ui icon button blue" type="submit">
            <i class="save icon"></i> Simpan
        </button>
    </div>
</form>

<script type="text/javascript">
    $('.button').popup();
    $('.dropdown').dropdown();
    $('#chkPk').checkbox({
        onChecked: function() {
          $('#lblPk').text("Ya");
          $('#n_pk').val('1');
        },
        onUnchecked: function() {
          $('#lblPk').text("Tidak");
          $('#n_pk').val('0');
        }
    })


    $('#frmKelompok').submit(function(e){
        e.preventDefault();
        loadingMulai();
        $.ajax({
            type:'post',
            async:true,
            data:$('#frmKelompok').serialize(),
            url:'interface/kuri-kelompok-form-process.php',
            success: function(data){
                $('#feedBack').html(data);
                loadingSelesai();
            }
        })
    })
</script>