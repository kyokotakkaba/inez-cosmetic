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
    $main = 'Survey';
    $sub = 'Data Produk';

    $idData = saring($_POST['idData']);

    if($idData=='0'){
        $nama = '';
        $deskripsi = '';
        $gambar = '';
        $avatar = '../files/photo/pictures.png';
        $subsub = 'Tambah';
    }
    else{
        $q = "
                SELECT 
                    nama, 
                    deskripsi,
                    gambar

                FROM 
                    produk

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
        $gambar = $r['gambar'];
        $avatar = '../files/photo/pictures.png';
        if(!empty($gambar) && $gambar !==''){
            $gambar = str_replace('%20', ' ', $gambar);
            if(file_exists('../../'.$gambar)){
                $avatar = '../'.$gambar;
            }
        }

        $subsub = 'Edit';
    }

    $subsub .= ' '.$sub;
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

<div class="field">
    <div class="ui icon button" onclick="backFromSub()">
        <i class="left chevron icon"></i> Kembali
    </div>    
</div>
    
<form id="frmProduk" style="margin-top: 10px;">
    <input type="hidden" name="view" value="1">
    <input type="hidden" name="id" value="<?php echo $idData; ?>">

    <div class="fields">
        <div class="six wide field">
            <label>Gambar produk</label>
            <img id="prevGambar" src="<?php echo $avatar; ?>" class="ui image small">
        </div>
        <div class="ten wide field">
            <div class="ui action input">
                <input type="text" id="gambar" name="gambar" readonly="readonly" placeholder="Pilih file" value="<?php echo $gambar; ?>" onchange="gantiGambar()">
                <a id="pickGambar" class="ui icon button" type="button" href="../filemanager/dialog.php?type=1&field_id=gambar">
                    <i class="open folder icon"></i>
                </a>
            </div>
            <p style="font-size: 9pt; color: #60646D; margin-top: 10px;">
                *Merupakan file produk yang dimaksud.<br>
                *Nama file <strong>tidak boleh ada spasi</strong>. Ganti tanda spasi pada nama file (jika ada) dengan tanda penghubung (-) melalui popup <i>file manager selector</i><br>
            </p>
        </div>
    </div>
    <div class="field">
        <label>Nama</label>
        <input type="text" id="nama" name="nama" placeholder="Nama" maxlength="64" value="<?php echo $nama; ?>">
    </div>
    <div class="field">
        <label>Deskripsi</label>
        <textarea id="deskripsi" rows="3" name="deskripsi" placeholder="Deskripsi" maxlength="128"><?php echo $deskripsi; ?></textarea>
    </div>

    <div class="field">
        <button class="ui icon button blue" type="submit">
            <i class="save icon"></i> Simpan
        </button>
    </div>
</form>



<script type="text/javascript">
    $('.dropdown').dropdown();
    $('.button').popup();

    $('#pickGambar').fancybox({
        'width'     : '100%',
        'height'    : '100%',
        'type'      : 'iframe',
        'fitToView' : false,
        'autoSize'  : false
    });


    function gantiGambar(){
        var alamat = $('#gambar').val();
        if(alamat!==''){
            $('#prevGambar').attr('src',alamat);
        }
    }

    $('#frmProduk').submit(function(e){
        var nama = $('#nama').val();
        
        e.preventDefault();
        loadingMulai();
        if(nama==''){
            tampilkanPesan('0','Isi Nama Produk.');
            loadingSelesai();
        }
        else{
            $.ajax({
                type:"post",
                async:true,
                url:"interface/questionnaire-produk-isi-form-process.php",
                data:$('#frmProduk').serialize(),
                success:function(data){
                    $("#feedBack").html(data);
                    loadingSelesai();
                }
            })
        }
    })

</script>