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

    $m = saring($_POST['ini']);
    $pecah = explode('[pisah]', $m);

    $id_bahasan = saring($pecah[0]);
    $ini = saring($pecah[1]);

    $sasar = saring($_POST['sasar']);
    $bebas = saring($_POST['bebas']);
    
    $q = "
            UPDATE 
                materi

            SET 
                no = '$bebas'

            WHERE
                no='$ini'
            AND
                id_bahasan = '$id_bahasan'
    ";
    $e = mysqli_query($conn,$q);
    if($e){
        $q1 = "
                UPDATE 
                    materi

                SET 
                    no = '$ini'

                WHERE
                    no='$sasar'
                AND
                    id_bahasan = '$id_bahasan'
        ";
        $e1 = mysqli_query($conn,$q1);
        if($e1){
            $q2 = "
                UPDATE 
                    materi

                SET 
                    no = '$sasar'

                WHERE
                    no='$bebas'
                AND
                    id_bahasan = '$id_bahasan'
            ";
            $e2 = mysqli_query($conn,$q2);
            if($e2){
?>
                <script type="text/javascript">
                    tampilkanPesan('1','Data berhasil dipindah.');
                    updateRowSub();
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