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
                materi_kelompok_bahasan

            SET 
                no = '$bebas'

            WHERE
                no='$ini'
    ";
    $e = mysqli_query($conn,$q);
    if($e){
        $q1 = "
                UPDATE 
                    materi_kelompok_bahasan

                SET 
                    no = '$ini'

                WHERE
                    no='$sasar'
        ";
        $e1 = mysqli_query($conn,$q1);
        if($e1){
            $q2 = "
                UPDATE 
                    materi_kelompok_bahasan

                SET 
                    no = '$sasar'

                WHERE
                    no='$bebas'
            ";
            $e2 = mysqli_query($conn,$q2);
            if($e2){
?>
                <script type="text/javascript">
                    tampilkanPesan('1','Data berhasil dipindah.');
                    updateRow();
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