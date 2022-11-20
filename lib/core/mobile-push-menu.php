<style type="text/css">
    .bgPushMenu{
        display: none;
        position: fixed;
        width: 100%;
        height: 100%;
        top: 0px;
        left: 0px;
        background-color: rgba(0,0,0,0.8);
        z-index: 102;
    }

    .pushMenu {
        display: none;
        <?php echo $bgMenu; ?>;
        padding: 14px 0px 14px 45px;
        top: 0px;
        left: 0px;
        width: 71%;
        height: 100%;
        margin-top: 0px;
        margin-left: -34px;
        max-width: 340px;
        min-width: 290px;
        position: fixed;
        z-index: 103;
        color: white;
    }

    #pushMenu, #pushMenu .item {
        color: white;
    }

    #mobileProfilePicture {
        background-image: url(<?php echo $profilePicture; ?>); 
        background-size: cover; 
        background-position: center; 
        width: 56px; 
        height: 56px; 
        border-radius: 50%; 
        margin: -10px auto;
    }

    @media only screen and (min-width: 481px){
        #mobileMenuTrigger {
            display: none;
        }
    }
</style>

<div id="mobileMenuTrigger" class="ui fixed inverted secondary menu" style="<?php echo $bgHeader; ?> padding: 3px 0px 3px 0px; font-size: 14pt;"> 
    <div class="ui container">
        <div class="item" style="padding: 0px 0px 0px 6px;">
            <div class="ui icon basic inverted tiny button" style="padding: 8px;" onclick="triggerPushMenu()">
                <i class="bars icon"></i>
            </div>
        </div>
        <div class="item" style="padding-left: 0px;">
            <?php echo ucfirst($jenisPengguna); ?> E-Learning Panel
        </div>
    </div>
</div>


<!-- Push Menu -->
<div class="bgPushMenu" onclick="triggerPushMenu()">
    <!-- only for focus on menu -->
</div>

<!-- Push Menu -->
<div class="pushMenu">
    <div id="pushMenu" class="ui vertical fluid secondary menu">
        <div class="ui icon basic text right floated" onclick="triggerPushMenu()">
            <i class="left arrow large icon"></i>
        </div>
        <div class="item">
            <div id="mobileProfilePicture"></div>
        </div>
<?php
    if(!empty($_SESSION['idPengguna'])){
?>
        
        <div class="ui horizontal divider" style="color: white;">
            <?php echo $namaPengguna; ?>
        </div>
        <a class="item notif" onclick="pilihMenu('notif')">
            <i class="bell icon"></i> Notif &nbsp; <span class="ui label purple jmlNotif"><?php echo $jmlNotif; ?></span>
        </a>
<?php
        if($jenisPengguna !== 'root'){
            if($jenisPengguna == 'admin'){
?>
            <a class="item employee" onclick="pilihMenu('employee')">
                Data BA/ BC <i class="users icon"></i>
            </a>
<?php                            
            }
?>
            <a class="item learn" onclick="pilihMenu('learn')">
                Belajar <i class="tags icon"></i>
            </a>
<?php            
        }
        if($jenisPengguna == 'root'){
            if(substr($_SESSION['idPengguna'],0,3)=="X-0"){
                $_SESSION['menu'] = 'kuri';
            ?>
                <a class="item kuri" onclick="pilihMenu('kuri')">
                Materi <i class="tags icon"></i>
                </a>
            <?php
            }else{
            
?>
            <a class="item set" onclick="pilihMenu('set')">
                Setting <i class="setting icon"></i>
            </a>
            <a class="item employee" onclick="pilihMenu('employee')">
                Data BA/ BC <i class="users icon"></i>
            </a>
            <a class="item train" onclick="pilihMenu('train')">
                Pelatihan <i class="address book icon"></i>
            </a>
            <a href="../filemanager/" class="item" target="_blank">
                File Manager <i class="open folder icon"></i>
            </a>
            <a class="item kuri" onclick="pilihMenu('kuri')">
                Materi <i class="tags icon"></i>
            </a>
            <a class="item bank" onclick="pilihMenu('bank')">
                Bank Soal <i class="box icon"></i>
            </a>
<?php            
        }}
        if(substr($_SESSION['idPengguna'],0,3)=="X-0"){
            ?>
                <div class="link item" onclick="tampilkanKonfirmasi('1','Logout','Yakin ingin keluar ?','<?php echo $fromHome; ?>interface/logout.php')">
                <i class="logout icon"></i> Logout
                </div>
            <?php
        }else{
?>        
        
        <a class="item test" onclick="pilihMenu('test')">
            Ujian <i class="stopwatch icon"></i>
        </a>
<?php
    if($jenisPengguna !== 'root'){
?>
        <a class="item test-history" onclick="pilihMenu('test-history')">
            Riwayat Ujian <i class="history icon"></i>
        </a>
<?php        
    }
?>        
        <a class="item qa" onclick="pilihMenu('qa')">
            Q & A <i class="comments icon"></i>
        </a>
        <a class="item questionnaire" onclick="pilihMenu('questionnaire')">
            Survey <i class="chart bar icon"></i>
        </a>
<?php
        if($jenisPengguna == 'root'){
?>
            <a class="item report" onclick="pilihMenu('report')">
                Laporan <i class="print icon"></i>
            </a>
<?php            
        }
?>        
        <a class="item bio" onclick="pilihMenu('bio')">
            Biodata <i class="user icon"></i>
        </a>
        <a class="item pass" onclick="pilihMenu('pass')">
            Password <i class="lock icon"></i>
        </a>
        <div class="link item" onclick="tampilkanKonfirmasi('1','Logout','Yakin ingin keluar ?','<?php echo $fromHome; ?>interface/logout.php')">
            <i class="logout icon"></i> Logout
        </div>
<?php
    }}
    else{
?>
        <a href="<?php echo $fromHome; ?>" class="item">
            <i class="user circle icon"></i> Login
        </a>
<?php    
    }
?>                  
    </div>
</div>





<script type="text/javascript">
    function triggerPushMenu(){
        var pushState = $('#pushState').val();

        if(pushState=="1"){
            $('#pushState').val('0');
        }
        else{
            $('#pushState').val('1');
        }
        
        $('.bgPushMenu').transition('fade');
        $('.pushMenu').transition('fly right');
    }
</script>


