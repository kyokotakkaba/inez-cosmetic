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
    $sub = 'Jenis Label';

    $idData = saring($_POST['idData']);

    if($idData=='0'){
        $nama = '';
        $satu = '';
        $dua = '';
        $tiga = '';
        $empat = '';

        $subsub = 'Tambah';
    }
    else{
        $q = "
                SELECT 
                    nama, 
                    satu,
                    dua,
                    tiga,
                    empat

                FROM 
                    angket_label

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
        $satu = $r['satu'];
        $dua = $r['dua'];
        $tiga = $r['tiga'];
        $empat = $r['empat'];
        
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
    
<form id="frmLabelAngket">
    <input type="hidden" name="view" value="1">
    <input type="hidden" name="id" value="<?php echo $idData; ?>">

    <div class="field">
        <label>Nama</label>
        <input type="text" id="nama" name="nama" placeholder="Nama" maxlength="64" value="<?php echo $nama; ?>">
    </div>
    <div class="field">
        <p style="font-size: 9pt; color: #60646D; margin-top: 10px;">
            Nama untuk mengingat jenis label
        </p>
    </div>
    <div class="fields">
        <div class="four wide field">
            <label>Opsi 1</label>
            <input type="text" maxlength="16" id="satu" name="satu" value="<?php echo $satu; ?>" >
        </div>
        <div class="four wide field">
            <label>Opsi 2</label>
            <input type="text" maxlength="16" id="dua" name="dua" value="<?php echo $dua; ?>" >
        </div>
        <div class="four wide field">
            <label>Opsi 3</label>
            <input type="text" maxlength="16" id="tiga" name="tiga" value="<?php echo $tiga; ?>" >
        </div>
        <div class="four wide field">
            <label>Opsi 4</label>
            <input type="text" maxlength="16" id="empat" name="empat" value="<?php echo $empat; ?>" >
        </div>
    </div>

    <div class="field">
        <button class="ui icon button blue" type="submit">
            <i class="save icon"></i> Simpan
        </button>
    </div>
    
</form>



<script type="text/javascript">

    $('#frmLabelAngket').submit(function(e){
        var nama, satu, dua, tiga, empat;
        
        nama = $('#nama').val();
        satu = $('#satu').val();
        dua = $('#dua').val();
        tiga = $('#tiga').val();
        empat = $('#empat').val();
        
        e.preventDefault();
        if(nama==''){
            tampilkanPesan('0','Isi Nama Label.');
        }
        else if(satu=='' || dua=='' || tiga=='' || empat==''){
            tampilkanPesan('0','Lengkapi opsi label.');
        }
        else{
            $.ajax({
                type:"post",
                async:true,
                url:"interface/questionnaire-label-isi-form-process.php",
                data:$('#frmLabelAngket').serialize(),
                success:function(data){
                    $("#feedBack").html(data);
                    loadingSelesai();
                }
            })
        }
    })

</script>