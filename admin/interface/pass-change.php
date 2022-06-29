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

    $idAkun = $_SESSION['idAkun'];
    $idPengguna = $_SESSION['idPengguna'];
    $jenisPengguna = $_SESSION['jenisPengguna'];

    $pl = saring($_POST['pl']);
    $pb1 = saring($_POST['pb1']);
    $pb2 = saring($_POST['pb2']);

    $pOld = md5($acak.md5($pl));

    $q = "
            SELECT
                pass

            FROM
                akun

            WHERE
                id = '$idAkun'

            LIMIT
                1
    ";
    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);
    if($c == '0'){
?>
        <script type="text/javascript">
            tampilkanPesan('0','Terjadi kesalahan saat memproses data.');
        </script>
<?php            
        exit();
    }



    $r = mysqli_fetch_array($e);
    $pDb = $r['pass'];
    if($pDb !== $pOld){
?>
        <script type="text/javascript">
            tampilkanPesan('0','Password lama salah.');
        </script>
<?php    
        exit();        
    }


    if($pb1 !== $pb2){
?>
        <script type="text/javascript">
            tampilkanPesan('0','Password baru dan konfirmasi tidak sama.');
        </script>
<?php            
        exit();
    }


    $pNew = md5($acak.md5($pb2));
    $q = "
            UPDATE 
                akun 

            SET 
                pass = '$pNew'

            WHERE
                id='$idAkun'
    ";
    $e = mysqli_query($conn,$q);
    if(!$e){
?>
        <script type="text/javascript">
            tampilkanPesan('0','Terjasi kesalahan saat merubah password.');
        </script>
<?php
        exit();
    }
?>
    <script type="text/javascript">
        tampilkanPesan('1','Password berhasil dirubah.');
    </script>