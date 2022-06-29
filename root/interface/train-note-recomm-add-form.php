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

    $idData = saring($_POST['idData']);

    $pecah = explode('[pisah]', $idData);

    $id = $pecah[0];
    $tgl = $pecah[1];
    $idRekom = $pecah[2];

    $main = 'Pelatihan';
    $sub = 'Data Catatan';

    if($idRekom=='0'){
        $rekomendasi = '';
        $subsub = 'Tambah';
    }
    else{
        $q = "
                SELECT 
                    rekomendasi
                FROM 
                    pelatihan_catatan_rekomendasi
                WHERE
                    id = '$idRekom'
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
        $rekomendasi = $r['rekomendasi'];

        $subsub = 'Edit';
    }

    $subsub .= ' Rekomendasi';
?>
<div class="ui message">
    <div class="ui breadcrumb">
        <div class="section"><?php echo $main; ?></div>
        <i class="right angle icon divider"></i>
        <div class="section"><?php echo $sub; ?></div>
        <i class="right angle icon divider"></i>
        <div class="active section"><?php echo $subsub; ?></div>
    </div>
</div>
<div class="ui basic vertical segment clearing" style="margin: 0px; padding: 0px;">
    <div class="ui icon button right floated" data-content="Tutup" onclick="backFromSub()">
        <i class="close icon"></i>
    </div>
</div>
<form id="frmAddRecom">
    <input type="hidden" name="id" value="<?php echo $idRekom; ?>">

    <input type="hidden" name="id_karyawan" value="<?php echo $id; ?>">
    <input type="hidden" name="tanggal" value="<?php echo $tgl; ?>">

    <div class="field">
        <label>Rekomendasi</label>
        <textarea id="rekomendasi" name="rekomendasi" maxlength="256" placeholder="Rekomendasi"><?php echo $rekomendasi; ?></textarea>
    </div>
    
    <div class="field">
        <button class="ui icon button blue" type="submit">
            <i class="save icon"></i> Simpan
        </button>
    </div>
</form>

<script type="text/javascript">

    $('#frmAddRecom').submit(function(e){
        var rekomendasi = $('#rekomendasi').val();

        e.preventDefault();
        loadingMulai();

        if(rekomendasi==''){
            tampilkanPesan('0','Isi rekomendasi.');
            loadingSelesai();
        }
        else{
            $.ajax({
                type:'post',
                async:true,
                data:$('#frmAddRecom').serialize(),
                url:'interface/train-note-recomm-add-form-process.php',
                success: function(data){
                    $('#feedBack').html(data);
                    loadingSelesai();
                }
            })
        }
    })
</script>