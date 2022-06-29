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

    $idData = $_SESSION['idPengguna'];

    $main = 'Biodata';
    $sub = 'Unggah Foto';
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

<form id="frmPrevUpload" enctype='multipart/form-data'>
    <input type="hidden" name="view" value="1">
    <input type="hidden" name="id_karyawan" value="<?php echo $idData; ?>">

    <div class="field">
        <label>Pilih File</label>
        <div class="ui left action input">
            <div class="ui labeled icon black button" onclick="pilihFile()">
                <i class="pin icon"></i> Pilih
            </div>
            <input type="text" id="textFile" placeholder="Pilih File" onchange="cekFile()" readonly="readonly" onclick="pilihFile()">
            <input type="file" accept=".jpg, .png, .gif" name="browseFile" id="browseFile" style="display: none;" onchange="terpilih()">
        </div>
        <p style="font-size: 9pt; color: #60646D; margin-top: 10px;">
            Tombol Upload akan otomatis muncul saat file terpilih
        </p>
    </div>
    <div class="field eksekusi" style="display: none;">
        <button type="submit" Class="ui labeled icon button inverted" style="<?php echo $accentColor; ?>">
            <i class="cloud upload icon"></i> Upload
        </button>
    </div>
    <div class="field">
        <div class="ui icon message">
            <div class="content">
                <div class="header">
                    Istruksi :
                </div>
                <ul>
                    <li>Pilih file gambar (*.jpg, *.png, atau *.gif).</li>
                    <li>Disarankan gambar dengan ukuran file kurang dari Emat Mega byte (< 4 Mb).</li>
                </ul>
            </div>
        </div>
    </div>
</form>

<script type="text/javascript">
    function terpilih(){
        var terpilih = $('#browseFile').val();
        $('#textFile').val(terpilih);
        cekFile();
    }

    function cekFile(){
        var file = $('#textFile').val();
        if (file == ""){
            if($('.eksekusi').is(':visible')==true){
                $('.eksekusi').transition('drop');
            }
        }
        else{
            if($('.eksekusi').is(':visible')==false){
                $('.eksekusi').transition('drop');
            }
        }
    }

    function pilihFile(){
        $('#browseFile').click();
    }


    $("#frmPrevUpload").submit(function(e){
        var formData, filenya;
        
        formData = new FormData(this);
        filenya = $('#textFile').val();
        
        e.preventDefault();

        if(filenya==''){
            tampilkanPesan('0','Pilih file.');
        }
        else{
            loadingMulai();
            $.ajax({
                type:'post',
                url:'interface/bio-upload-form-process.php',
                async:true,
                data: formData,
                cache:false,
                contentType: false,
                processData: false,
                success:function(data){
                    $('#feedBack').html(data);
                    loadingSelesai();
                }
            })
        }
    })
</script>