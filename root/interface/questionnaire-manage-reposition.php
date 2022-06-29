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

    $mentah = saring($_POST['ini']);
    $pecah = explode('[pisah]', $mentah);
    $id_angket = saring($pecah[0]);

    $ini = saring($pecah[1]);
    $sasar = saring($_POST['sasar']);
    $bebas = saring($_POST['bebas']);
    
    $q = "
            UPDATE 
                angket_item

            SET 
                no = '$bebas'

            WHERE
                id_angket = '$id_angket'
            AND
                no='$ini'
    ";

    //echo $q.'<br>';

    $e = mysqli_query($conn,$q);
    if($e){
        $q1 = "
                UPDATE 
                    angket_item

                SET 
                    no = '$ini'

                WHERE
                    id_angket = '$id_angket'
                AND
                    no='$sasar'
        ";

        //echo $q1.'<br>';

        $e1 = mysqli_query($conn,$q1);
        if($e1){
            $q2 = "
                UPDATE 
                    angket_item

                SET 
                    no = '$sasar'

                WHERE
                    id_angket = '$id_angket'
                AND
                    no='$bebas'
            ";

            //echo $q2.'<br>';

            $e2 = mysqli_query($conn,$q2);
            if($e2){
?>
                <script type="text/javascript">
                    tampilkanPesan('1','Data berhasil dipindah.');
                    updateRowSub();
                </script>
<?php
            }
            else{
?>
                <script type="text/javascript">
                    tampilkanPesan('0','Terjasi kesalahan saat memproses data.');
                </script>
<?php                
            }
        }
        else{
?>
            <script type="text/javascript">
                tampilkanPesan('0','Terjasi kesalahan saat memproses data.');
            </script>
<?php            
        }
    }
    else{
?>
        <script type="text/javascript">
            tampilkanPesan('0','Terjasi kesalahan saat memproses data.');
        </script>
<?php
    }
?>