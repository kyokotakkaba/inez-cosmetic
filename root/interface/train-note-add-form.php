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

    $iniHari = date('Y-m-d');

    $idData = saring($_POST['idData']);

    $pecah = explode('[pisah]', $idData);

    $id = $pecah[0];
    $tgl = $pecah[1];
    $idCat = $pecah[2];

    $main = 'Pelatihan';
    $sub = 'Data Catatan';

    if($idCat=='0'){
        $id_topik = '';
        $nilai_before = '';
        $nilai_after = '';

        $subsub = 'Tambah';
    }
    else{
        $q = "
                SELECT 
                    id_topik, 
                    id_root, 
                    nilai_before, 
                    nilai_after

                FROM 
                    pelatihan_catatan 

                WHERE
                    id = '$idCat'
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
        $id_topik = $r['id_topik'];
        $nilai_before = $r['nilai_before'];
        $nilai_after = $r['nilai_after'];

        $subsub = 'Edit';
    }
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
<form id="frmAddNote">
    <input type="hidden" name="id" value="<?php echo $idCat; ?>">

    <input type="hidden" name="id_karyawan" value="<?php echo $id; ?>">
    <input type="hidden" name="tanggal" value="<?php echo $tgl; ?>">

    <div class="field">
        <label>Topik</label>
        <select id="id_topik" name="id_topik" class="ui dropdown">
<?php
    $q = "
            SELECT
                id,
                nama

            FROM
                pelatihan_catatan_topik

            WHERE
                hapus = '0'
    ";

    if($idCat=='0'){
        $q .= "
                AND
                id 
            NOT IN 
                (
                    SELECT
                        id_topik

                    FROM
                        pelatihan_catatan

                    WHERE
                        id_karyawan = '$id'
                    AND
                        tanggal = '$iniHari'
                    AND
                        hapus = '0'
                )
        ";
    }

    $q .="

            ORDER BY
                nama ASC
    ";
    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);

    if($c=='0'){
?>
            <option value="">Belum ada topik</option>
<?php
    }
    else{
?>
            <option value="">Pilih</option>
<?php        
        while ($r = mysqli_fetch_assoc($e)) {
            $idT = $r['id'];
            $topik = $r['nama'];
?>
            <option value="<?php echo $idT; ?>" <?php if($id_topik==$idT){ ?> selected="selected" <?php } ?> >
                <?php echo $topik; ?>
            </option>
<?php
        }
    }

?>        
        </select>
    </div>
    <div class="two fields">
        <div class="field four wide">
            <label>Nilai before</label>
            <input type="number" id="nilai_before" name="nilai_before" maxlength="2" placeholder="Nilai" value="<?php echo $nilai_before; ?>">
        </div>
        <div class="field four wide">
            <label>Nilai after</label>
            <input type="number" id="nilai_after" name="nilai_after" maxlength="2" placeholder="After" value="<?php echo $nilai_after; ?>">
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
    $('.dropdown').dropdown();

    $('#frmAddNote').submit(function(e){
        var id_topik, nb, na;
        id_topik = $('#id_topik').val();
        nb = $('#nilai_before').val();
        na = $('#nilai_after').val();

        e.preventDefault();
        loadingMulai();

        if(id_topik==''){
            tampilkanPesan('0','Pilih topik catatan.');
            loadingSelesai();
        }
        else if(nb==''||nb=='0'){
            tampilkanPesan('0','Isi nilai.');
            loadingSelesai();
        }
        else{
            $.ajax({
                type:'post',
                async:true,
                data:$('#frmAddNote').serialize(),
                url:'interface/train-note-add-form-process.php',
                success: function(data){
                    $('#feedBack').html(data);
                    loadingSelesai();
                }
            })
        }
    })
</script>