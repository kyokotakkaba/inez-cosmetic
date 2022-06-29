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
    $sub = 'Edit Data Web';

    $q = "
            SELECT 
                title, 
                description, 
                keywords, 
                deploy_year, 
                owner, 
                owner_web 
            FROM 
                app_web_meta
            WHERE 
                id = '$appCode'
            LIMIT
                1
    ";
    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);

    if($c=='0'){
        $title = '';
        $description = '';
        $keywords = '';
        $deploy_year = '';
        $owner = '';
        $owner_web = '';
    }
    else{
        $r = mysqli_fetch_assoc($e);

        $title = $r['title'];
        $description = $r['description'];
        $keywords = $r['keywords'];
        $deploy_year = $r['deploy_year'];
        $owner = $r['owner'];
        $owner_web = $r['owner_web'];
    }

    $placeTahun = date('Y');
?>
<div class="ui message">
    <div class="ui breadcrumb">
        <div class="section"><?php echo $main; ?></div>
        <i class="right angle icon divider"></i>
        <div class="active section"><?php echo $sub; ?></div>
    </div>
</div>

<form id="frmWeb">
    <div class="field">
        <div class="ui icon button" onclick="backToMain()">
            <i class="left chevron icon"></i> Kembali
        </div>    
    </div>

    <div class="field">
        <label>Judul web</label>
        <div class="ui input">
            <input type="text" id="title" name="title" placeholder="Judul" maxlength="32" required="required" value="<?php echo $title; ?>">
        </div>
    </div>
    <div class="field">
        <label>Deskripsi</label>
        <div class="ui input">
            <textarea id="description" name="description" placeholder="Deskripsi" maxlength="128" required="required"><?php echo $description; ?></textarea>
        </div>
    </div>
    <div class="field">
        <label>Kata kunci</label>
        <div class="ui input">
            <input type="text" id="keywords" name="keywords" placeholder="Kata kunci" maxlength="128" required="required" value="<?php echo $keywords; ?>">
        </div>
    </div>
    <div class="field">
        <label>Tahun awal online</label>
        <div class="ui input">
            <input type="number" id="deploy_year" name="deploy_year" placeholder="<?php echo $placeTahun; ?>" maxlength="4" required="required" value="<?php echo $deploy_year; ?>">
        </div>
    </div>
    <div class="field">
        <label>Pemilik</label>
        <div class="ui input">
            <input type="text" id="owner" name="owner" placeholder="Pemilik" maxlength="64" required="required" value="<?php echo $owner; ?>">
        </div>
    </div>
    <div class="field">
        <label>Web Pemilik</label>
        <div class="ui input">
            <input type="text" id="owner_web" name="owner_web" placeholder="Web pemilik" maxlength="64" required="required" value="<?php echo $owner_web; ?>">
        </div>
    </div>
    <div class="field">
        <p style="font-size: 9pt; color: #60646D; margin-top: 10px;">
            Semua info yang disimpan untuk optimalisasi info web, kemudahan dalam pencarian melalui mesin pencari (google, ask, yahoo, dll.).
        </p>
    </div>
    
    <div class="field">
        <button class="ui icon button blue" type="submit">
            <i class="save icon"></i> Simpan
        </button>
    </div>
</form>


<script type="text/javascript">

    $('#frmWeb').submit(function(e){
        e.preventDefault();
        loadingMulai();
        $.ajax({
            type:'post',
            async:true,
            data:$('#frmWeb').serialize(),
            url:'interface/set-web-form-process.php',
            success: function(data){
                $('#feedBack').html(data);
                loadingSelesai();
            }
        })
    })

</script>