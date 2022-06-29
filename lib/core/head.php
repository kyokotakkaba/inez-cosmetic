<?php
    require_once $fromHome."conf/function.php";

    $icon = $fromHome.'icon.png';
    if(!file_exists($icon)){
        $icon = $fromHome.'files/photo/icon-default.png';
    }

    $profilePicture = $icon;

    if(!empty($_SESSION['idPengguna'])){
        $idPengguna = $_SESSION['idPengguna'];

        $tabel = $jenisPengguna;
        if($jenisPengguna !== 'root'){
            $tabel = 'karyawan';
        }

        $q = "
                SELECT
                    nama,
                    jk,
                    foto

                FROM
                    $tabel

                WHERE
                    id = '$idPengguna'
                    
                LIMIT
                    1
        ";

        $e = mysqli_query($conn, $q);
        $r = mysqli_fetch_assoc($e);
        $namaPengguna = $r['nama'];
        $pNama = explode(' ', $namaPengguna);
        $jmlN = count($pNama);
        if($jmlN > 1){
            $namaPengguna = '';
            foreach ($pNama as $w) {
                $namaPengguna .= $w[0];
            }    
        }
        $jk = $r['jk'];
        if($jk !== 'l' && $jk !== 'p'){
            $jk = 'n';
        }
        $alamatGambar = $fromHome.'files/photo/'.$jk.'.png';
        $urlG = $r['foto'];
        if(!empty($urlG) && $urlG !== ''){
            if(file_exists($fromHome.''.$urlG)){
                $alamatGambar = $fromHome.''.$urlG;
            }
        }
        $profilePicture = $alamatGambar;
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title><?php echo $title; ?></title>
        
        <meta name="description" content="<?php echo $description; ?>" />
        <meta name="keywords" content="<?php echo $keywords; ?>" />
        <meta name="author" content="<?php echo $author; ?>" />
        
        <link rel="shortcut icon" href="<?php echo $icon; ?>" />
        
        <link rel="stylesheet" type="text/css" href="<?php echo $fromHome; ?>lib/semantic-ui/semantic.min.css">
        <link rel="stylesheet" type="text/css" href="<?php echo $fromHome; ?>lib/calendar.min.css">

        <style type="text/css">
            body {
            <?php echo $bgColor; ?>
            }

            #desktopMenu, #mobileMenuTrigger, #goTop {
                box-shadow: 0px 0px 9px 3px rgba(41, 41, 41, .25);
            }

            #goTop {
              <?php echo $goTopColor; ?>
              display: none;
              position: fixed;
              z-index: 999;
              bottom: 16px;
              right: 16px;
              color: #FFFFFF;
            }

            #pesan {
              position: fixed;
              top: 20px;
              right: 10px;
              z-index: 999999;
              display: none;
              width: 70%;         
            }

            .sembunyi {
            display: none;
            }

            @media only screen and (max-width: 767px){
            .fields .field {
              padding-top: 8px;
              padding-bottom: 8px;
            }
            }
        </style>
    </head>
    <body>