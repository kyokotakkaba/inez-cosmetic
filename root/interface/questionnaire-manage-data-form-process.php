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

    $id = saring($_POST['id']);

    $id_angket = saring($_POST['id_angket']);
    $deskripsi = saring($_POST['deskripsi']);
    $id_label = saring($_POST['id_label']);

    if($id=='0'){
        $qN = "
                SELECT
                    COUNT(id) jml

                FROM
                    angket_item

                WHERE
                    id_angket = '$id_angket'
        ";
        $eN = mysqli_query($conn, $qN);
        $rA = mysqli_fetch_assoc($eN);
        $no_a = $rA['jml'];

        $no = $no_a + 1;


        $idBaru = UUIDBaru();
        $qP = "
                    INSERT INTO 
                        angket_item
                            (
                                id, 
                                id_angket, 
                                deskripsi,
                                id_label,
                                no
                            ) 
                    VALUES 
                            (
                                '$idBaru',
                                '$id_angket',
                                '$deskripsi',
                                '$id_label',
                                '$no'
                            )
        ";
    }
    else{
        $qP = "
                UPDATE 
                    angket_item 
                SET 
                    deskripsi='$deskripsi'
                WHERE
                    id='$id'
        ";
    }

    $eP = mysqli_query($conn, $qP);

    if($eP){
?>
        <script type="text/javascript">
            tampilkanPesan('1','Data berhasil disimpan.');
            $('#lastIdSub').val('-');
            updateRowSub();
            backFromSub();
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