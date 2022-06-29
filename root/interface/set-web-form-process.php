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
    
    $title = saring($_POST['title']);
    $description = saring($_POST['description']);
    $keywords = saring($_POST['keywords']);
    $deploy_year = saring($_POST['deploy_year']);
    $owner = saring($_POST['owner']);
    $owner_web = saring($_POST['owner_web']);

    
    $q = "
            UPDATE 
                app_web_meta 
            SET 
                title='$title',
                description='$description',
                keywords='$keywords',
                deploy_year='$deploy_year',
                owner='$owner',
                owner_web='$owner_web' 
            WHERE
                id='$appCode'
    ";

    $e = mysqli_query($conn, $q);

    if($e){
?>
        <script type="text/javascript">
            tampilkanPesan('1','Data berhasil disimpan.');
            $('lastId').val('-');
            reloadFrame();
        </script>
<?php
        exit();            
    }
    else{
?>
        <script type="text/javascript">
            tampilkanPesan('0','Terjadi kesalahan saat memproses data.');
        </script>
<?php
        exit();            
    }
?>