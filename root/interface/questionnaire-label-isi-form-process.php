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

    $nama = saring($_POST['nama']);
    $satu = saring($_POST['satu']);
    $dua = saring($_POST['dua']);
    $tiga = saring($_POST['tiga']);
    $empat = saring($_POST['empat']);

    if($id=='0'){
        $idBaru = UUIDBaru();
        $qP = "
                    INSERT INTO 
                        angket_label
                            (
                                id, 
                                nama, 
                                satu, 
                                dua, 
                                tiga, 
                                empat
                            ) 
                    VALUES 
                            (
                                '$idBaru',
                                '$nama',
                                '$satu',
                                '$dua',
                                '$tiga',
                                '$empat'
                            )
        ";
    }
    else{
        $qP = "
                UPDATE 
                    angket_label 

                SET 
                    nama='$nama',
                    satu='$satu',
                    dua='$dua',
                    tiga='$tiga',
                    empat='$empat'

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
            reloadSub();
<?php
    if($id=='0'){
?>
            berhasilMenambahkanPnK('Label');
<?php        
    }
?>
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