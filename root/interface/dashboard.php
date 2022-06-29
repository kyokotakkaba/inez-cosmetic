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

    if($_SESSION['menu']!=='dashboard'){
        $_SESSION['menu'] = 'dashboard';
    }

    $q = "
            SELECT 
                id
            FROM 
                app_web_meta 
            LIMIT
                1
    ";
    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);
    if($c=='0'){
?>
        <div class="ui icon message">
            <i class="inbox icon"></i>
            <div class="content">
                <div class="header">
                    NULL
                </div>
                <p>
                    Silahkan setup lembaga pada menu setting.
                </p>
            </div>
        </div>
<?php
        exit();
    }
    
?>
