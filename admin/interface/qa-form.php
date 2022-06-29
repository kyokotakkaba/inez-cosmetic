<?php
    session_start();
    $appSection = 'admin';

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
    $main = 'Q & A';

    $idData = saring($_POST['idData']);

    if($idData=='0'){
        $id_kelompok = '';
        $pertanyaan = '';
        $sub = 'Ajukan';
    }
    else{
        $q = "
                SELECT 
                    id_kelompok, 
                    pertanyaan
                FROM 
                    tanya_jawab 
                WHERE
                    id = '$idData'

                LIMIT
                    1
        ";
        $e = mysqli_query($conn, $q);
        $c = mysqli_num_rows($e);
        if($c=='0'){
            echo "DATA NOT FOUND";
            exit();
        }
        
        $r = mysqli_fetch_assoc($e);
        $id_kelompok = $r['id_kelompok'];
        $pertanyaan = $r['pertanyaan'];

        $sub = 'Sesuaikan';
    }

    $sub .= ' Pertanyaan';

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
    
<form id="frmQuest">
    <input type="hidden" name="view" value="1">
    <input type="hidden" name="id" value="<?php echo $idData; ?>">

    <div class="field">
        <label>Kelompok Materi</label>
        <select id="id_kel" name="id_kel" class="ui dropdown">
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
            <option value="">Pilih</option>
<?php        
        while ($d=mysqli_fetch_assoc($e)) {
            $idK = $d['id'];
            $namaK = $d['nama'];
?>
            <option value="<?php echo $idK; ?>" <?php if($idK==$id_kelompok){ ?> selected="selected" <?php } ?> >
                <?php echo $namaK; ?>
            </option>
<?php                 
             }
    }
?>
        </select>
    </div>

    <div class="field">
        <label>Deskripsi Pertanyaan</label>
        <textarea id="pertanyaan" name="pertanyaan"><?php echo html_entity_decode($pertanyaan); ?></textarea>
    </div>

    <div class="field">
        <button class="ui icon button blue" type="submit">
            <i class="save icon"></i> Simpan
        </button>
    </div>
    
</form>



<script type="text/javascript">
    $('.dropdown').dropdown();


    var editorPertanyaan = CKEDITOR.replace('pertanyaan',{
        height: 140,
        filebrowserBrowseUrl : '../filemanager/dialog.php?type=2&editor=ckeditor&fldr=',
        filebrowserUploadUrl : '../filemanager/dialog.php?type=2&editor=ckeditor&fldr=',
        filebrowserImageBrowseUrl : '../filemanager/dialog.php?type=1&editor=ckeditor&fldr='
    });



    $('#frmQuest').submit(function(e){
        var id_kelompok, pertanyaan;
        
        id_kelompok = $('#id_kel').val();
        pertanyaan = CKEDITOR.instances.pertanyaan.getData();
        
        e.preventDefault();

        if(id_kelompok==''){
            tampilkanPesan('0','Pilih Kelompok Materi.');
        }
        else if(pertanyaan==''){
            tampilkanPesan('0','Ketik pertanyaan yang ingin diajukan.');
        }
        else{
            loadingMulai();
            $.ajax({
                type:"post",
                async:true,
                url:"interface/qa-form-process.php",
                data:{
                    'view': '1',
                    'id': '<?php echo $idData; ?>',
                    'id_kelompok': id_kelompok,
                    'pertanyaan': pertanyaan
                },
                success:function(data){
                    $("#feedBack").html(data);
                    loadingSelesai();
                }
            })
        }
    })

</script>