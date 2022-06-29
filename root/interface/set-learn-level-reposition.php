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

    $ini = saring($_POST['ini']);
    $sasar = saring($_POST['sasar']);
    $bebas = saring($_POST['bebas']);
    
    $q = "
            UPDATE 
                tingkat_belajar 

            SET 
                no = '$bebas'

            WHERE
                no='$ini'
    ";

    //echo $q.'<br>';

    $e = mysqli_query($conn,$q);
    if($e){
        $q1 = "
                UPDATE 
                    tingkat_belajar 

                SET 
                    no = '$ini'

                WHERE
                    no='$sasar'
        ";

        //echo $q1.'<br>';

        $e1 = mysqli_query($conn,$q1);
        if($e1){
            $q2 = "
                UPDATE 
                    tingkat_belajar 

                SET 
                    no = '$sasar'
                    
                WHERE
                    no='$bebas'
            ";

            //echo $q2.'<br>';

            $e2 = mysqli_query($conn,$q2);
            if($e2){
?>
                <script type="text/javascript">
                    tampilkanPesan('1','Data berhasil dipindah.');
                    reloadFrame();
                </script>
<?php
                exit();
            }
            else{
?>
                <script type="text/javascript">
                    tampilkanPesan('0','Terjasi kesalahan saat memproses data.');
                </script>
<?php                
                exit();
            }
        }
        else{
?>
            <script type="text/javascript">
                tampilkanPesan('0','Terjasi kesalahan saat memproses data.');
            </script>
<?php            
            exit();
        }
    }
    else{
?>
        <script type="text/javascript">
            tampilkanPesan('0','Terjasi kesalahan saat memproses data.');
        </script>
<?php
        exit();
    }
?>