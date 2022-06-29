<style type="text/css">
    #desktopMenu {
        box-shadow: 0px 0px 9px 3px rgba(41, 41, 41, .25);
    }

    @media only screen and (max-width: 480px){
        #desktopMenu {
            display: none;
        }
    }
</style>
<div id="desktopMenu" class="ui secondary fixed menu inverted" style="<?php echo $bgHeader; ?>">
    <div id="deskMenuContainer" class="ui container">
        <div id="homeButton" href="#" class="item">
            <img class="logo" src="<?php echo $icon; ?>"> &nbsp; <?php echo $title; ?>
        </div>

        <div class="right menu">
<?php
    if(empty($_SESSION['idPengguna'])){
?>
            <div class="link item" onclick="login()">
                <i class="user circle icon"></i> Masuk
            </div>
<?php        
    }
    else{
        $ke = $jenisPengguna;
        if($jenisPengguna == 'admin'){
            $arTrans = array(
                'principal' => 'Kepala Sekolah',
                'academic' => 'Akademik',
                'library' => 'Pustakawan',
                'counseling' => 'BK',
                'ppdb' => 'PPDB',
                'repository' => 'Repository',
                'finance' => 'Keuangan'
            );
            $jenisPengguna = $arTrans[$untuk];
            $ke = $untuk.'/admin';
        }
?>
            <div class="ui dropdown item">
                <i class="user circle icon"></i> <?php echo $namaPengguna; ?>
                <i class="dropdown icon"></i>
                <div class="menu">
                    <div class="header">Menu</div>
                    <a href="<?php echo $ke; ?>/" class="item">
                        <i class="setting icon"></i> Panel <?php echo ucwords($jenisPengguna); ?>
                    </a>
                    <div class="item" onclick="tampilkanKonfirmasi('<?php echo $fromHome; ?>','Logout','Yakin ingin keluar?','interface/logout.php')">
                        <i class="log out icon"></i> Logout
                    </div>
                </div>
            </div>
<?php        
    }
?>                    
        </div>
    </div>
</div>