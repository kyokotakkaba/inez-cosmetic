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

    $id = saring($_POST['idData']);

    $q1 = "
            SELECT 
                COUNT(id) jml

            FROM 
                angket_item 

            WHERE 
                id_angket = '$id'
            AND
                hapus = '0'
    ";
    $e1 = mysqli_query($conn, $q1);
    $r1 = mysqli_fetch_assoc($e1);

    $jmlItem = $r1['jml'];

    if($jmlItem=='0'){
?>
        <script type="text/javascript">
            tampilkanPesan('0','Item pertanyaan atau pernyataan pada angket tidak ada.');
        </script>
<?php        
        exit();
    }

    $qP = "
                UPDATE 
                    angket

                SET 
                    aktif = '1'

                WHERE
                    id='$id'
        ";

    $eP = mysqli_query($conn, $qP);

    if($eP){
?>
        <script type="text/javascript">
            tampilkanPesan('1','Survey berhasil diaktifkan.');
            $('#lastId').val('-');
            updateRowQ();
        </script>
<?php           
    }         
    else{
?>
        <script type="text/javascript">
            tampilkanPesan('0','Terjadi kesalahan saat memproses data.');
        </script>
<?php                                
    }
?>