<?php
    session_start();
    $appSection = 'user';

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

    $id_angket = saring($_POST['id_angket']);
    $id_item = saring($_POST['id_item']);
    
    $id_respon = saring($_POST['id_respon']);
    $respon = saring($_POST['respon']);

    $tanggal = date('Y-m-d');
    $jam = date('H:i:s');

    $idPengguna = $_SESSION['idPengguna'];

    if($id_respon=='0'){
        $idResBaru = UUIDBaru();
        $q = "
                INSERT INTO 
                    angket_respon
                        (
                            id, 
                            id_periode, 
                            id_karyawan, 
                            id_angket, 
                            id_item, 
                            respon, 
                            tanggal, 
                            jam
                        ) 
                VALUES 
                        (
                            '$idResBaru',
                            '$idPeriode',
                            '$idPengguna',
                            '$id_angket',
                            '$id_item',
                            '$respon',
                            '$tanggal',
                            '$jam'
                        )
        ";
        $balikIdRespon = $idResBaru;
    }
    else{
        $q = "
                UPDATE 
                    angket_respon 
                SET 
                    respon='$respon',
                    tanggal='$tanggal',
                    jam='$tanggal' 
                WHERE
                    id='$id_respon'
        ";
        $balikIdRespon = $id_respon;
    }
 
    $e = mysqli_query($conn, $q);

    if($eP){
?>
        <script type="text/javascript">
            tampilkanPesan('1','Data berhasil disimpan.');
            kembalianJawabItem('<?php echo $id_angket; ?>', '<?php echo $id_item; ?>', '<?php echo $balikIdRespon; ?>', '<?php echo $respon; ?>', '1');
        </script>
<?php           
    }         
    else{
?>
        <script type="text/javascript">
            tampilkanPesan('0','Terjadi kesalahan saat menyimpan jawaban.');
            kembalianJawabItem('<?php echo $id_angket; ?>', '<?php echo $id_item; ?>', '<?php echo $balikIdRespon; ?>', '0', '0');
        </script>
<?php                                
    }
?>