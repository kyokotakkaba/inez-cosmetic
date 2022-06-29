<?php
    session_start();
    $appSection = 'user';

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

    if($_SESSION['menu']!=='pass'){
        $_SESSION['menu'] = 'pass';
    }
?>
<h2 class="ui block header">
    <i class="settings icon"></i>
    <div class="content">
        Password
        <div class="sub header">
            Rubah password anda
        </div>
    </div>
</h2>


<form id="frmPass" class="ui form" >
    <input type="hidden" name="view" value="1">
    <div class="field">
        <label>
            Password lama
        </label>
        <div class="ui input icon">
            <input type="password" id="pl" name="pl" placeholder="Password" maxlength="32" required="required">
            <i class="unlock icon"></i>
        </div>
    </div>
    <div class="ui hidden divider"></div>
    <div class="ui hidden divider"></div>
    <div class="ui horizontal divider">
        <i class="shield alternate icon"></i> Password baru
    </div>
    <div class="field">
        <div class="ui input icon">
            <input type="password" id="pb1" name="pb1" placeholder="Password" maxlength="32" required="required">
            <i class="lock icon"></i>
        </div>
    </div>
    <div class="field">
        <div class="ui input icon">
            <input type="password" id="pb2" name="pb2" placeholder="Konfirmasi" maxlength="32" required="required">
            <i class="lock icon"></i>
        </div>
    </div>
    <div class="field">
        <button type="submit" class="ui blue button">
            <i class="save icon"></i> Simpan
        </button>
    </div>
</form>

<script type="text/javascript">
    $('#frmPass').submit(function(e){
        e.preventDefault();

        var pl, pb1, pb2, ppl, ppb1, ppb2;
        pl = $('#pl').val();
        pb1 = $('#pb1').val();
        pb2 = $('#pb2').val();

        ppl = pl.length;
        ppb1 = pb1.length;
        ppb2 = pb2.length;

        if(pl=='' || pb1=='' || pb2==''){
            tampilkanPesan('0','Lengkapi form.');
        }
        else if(ppb1<5||ppb1<5||ppb2<5){
            tampilkanPesan('0','Isi minimal 5 karakter.'); 
        }
        else if(pl == pb1){
            tampilkanPesan('0','Password tidak boleh sama.'); 
        }
        else if(pb1 !== pb2){
            tampilkanPesan('0','Konfirmasi password tidak sama.'); 
        }
        else{
            loadingMulai();
            $.ajax({
                type:"post",
                async:true,
                url:"interface/pass-change.php",
                data:{
                    'view':'1',
                    'pl':pl,
                    'pb1':pb1,
                    'pb2':pb2
                },
                success:function(data){
                    $("#feedBack").html(data);
                    loadingSelesai();
                }
            })
        }
    })
</script>