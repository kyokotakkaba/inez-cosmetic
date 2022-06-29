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
    $sub = 'Jenis Survey';

    $idData = saring($_POST['idData']);

    if($idData=='0'){
        $nama = '';
        $deskripsi = '';
        $subsub = 'Tambah';
    }
    else{
        $q = "
                SELECT 
                    nama, 
                    deskripsi
                FROM 
                    angket_kategori 
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
    
<form id="frmAngketJenis">
    <input type="hidden" name="view" value="1">
    <input type="hidden" name="id" value="<?php echo $idData; ?>">

    <div class="field">
        <label>Nama</label>
        <input type="text" id="nama" name="nama" placeholder="Nama" maxlength="64" value="<?php echo $nama; ?>">
    </div>
    <div class="field">
        <label>Deskripsi</label>
        <textarea id="deskripsi" name="deskripsi" rows="3" placeholder="Deskripsi" maxlength="128"><?php echo $deskripsi; ?></textarea>
    </div>

    <div class="field">
        <button class="ui icon button blue" type="submit">
            <i class="save icon"></i> Simpan
        </button>
    </div>
    
</form>



<script type="text/javascript">

    $('#frmAngketJenis').submit(function(e){
        var nama = $('#nama').val();
        
        e.preventDefault();
        loadingMulai();
        if(nama==''){
            tampilkanPesan('0','Isi Nama Jenis Survey.');
            loadingSelesai();
        }
        else{
            $.ajax({
                type:"post",
                async:true,
                url:"interface/questionnaire-jenis-isi-form-process.php",
                data:$('#frmAngketJenis').serialize(),
                success:function(data){
                    $("#feedBack").html(data);
                    loadingSelesai();
                }
            })
        }
    })

</script>