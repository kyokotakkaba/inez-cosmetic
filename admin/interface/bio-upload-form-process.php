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



    if(empty($_FILES)){
?>
        <script type="text/javascript">
            tampilkanPesan('0','Pilih file dan upload.');
        </script>
<?php                               
        exit();
    }



    
    $fileDiUnggah = $_FILES['browseFile']['name'];



    $extension = strtolower(pathinfo($fileDiUnggah, PATHINFO_EXTENSION));

    $arExt = array(
        'jpg',
        'jpeg',
        'png',
        'gif'
    );

    if(!in_array($extension, $arExt)){
?>
        <script type="text/javascript">
            tampilkanPesan('0','File yang diperbolehkan hanya dokumen gambar (*.jpg, *png, *.gif).');
        </script>
<?php       
        exit();
    }


    $idPengguna = $_SESSION['idPengguna'];
    $namaFileBaru = UUIDBaru().'.'.$extension;
    $sourcePath = $_FILES['browseFile']['tmp_name'];
    $baseFolder = 'files/user/'.$idPengguna.'/';
    $destFolder = $fromHome.''.$baseFolder;
    $destPath = $destFolder.''.$namaFileBaru;
    $fileDb = $baseFolder.''.$namaFileBaru;

    if(!is_dir($destFolder)){   
        mkdir($destFolder, 0777);
    }

    $quality = 60;                          

    compressImage($sourcePath, $destPath, $quality);

    function compressImage($source, $destination, $quality) {
        $info = getimagesize($source);
        if ($info['mime'] == 'image/jpeg') 
            $image = imagecreatefromjpeg($source);
        elseif ($info['mime'] == 'image/gif') 
            $image = imagecreatefromgif($source);
        elseif ($info['mime'] == 'image/png') 
            $image = imagecreatefrompng($source);
        imagejpeg($image, $destination, $quality);
    }


    $pada = date('Y-m-d');
    $waktu = date('H:i:s');
    $qU = "
            UPDATE
                karyawan

            SET
                foto = '$fileDb'

            WHERE
                id = '$idPengguna'
    ";
    $eU = mysqli_query($conn, $qU);
    if(!$eU){
?>
        <script type="text/javascript">
            tampilkanPesan('0','Terjadi kesalahan saat memproses data.');
        </script>
<?php               
        exit();
    }

?>
    <script type="text/javascript">
        tampilkanPesan('1','File berhasil diunggah.');
        reloadFrame();
    </script>