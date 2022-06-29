<?php
    session_start();

    if(empty($_SESSION['idPengguna'])){
        echo "SESSION EXPIRED";
        exit();
    }

    $alamat = $_SESSION['jenisPengguna'];

    if($alamat!=='root'){
        echo "INVALID USER";
        exit();
    }

    require_once "../../../conf/function.php";

    $id_angket = saring($_POST['id_angket']);
    $id_item = saring($_POST['id_item']);
    
    $id_respon = saring($_POST['id_respon']);
    $respon = saring($_POST['respon']);

    $tanggal = date('Y-m-d');
    $jam = date('H:i:s');

    $idPengguna = $_SESSION['idPengguna'];

    if($id_respon=='0'){
        $idResBaru = UUIDBaru();

        $balikIdRespon = $idResBaru;
    }
    else{

        $balikIdRespon = $id_respon;
    }
    
?>
    <script type="text/javascript">
        tampilkanPesan('1','Data diterima. Respon yang disimpan adalah: <strong><i><?php echo $respon; ?></i></strong>');
        kembalianJawabItem('<?php echo $id_angket; ?>', '<?php echo $id_item; ?>', '<?php echo $balikIdRespon; ?>', '<?php echo $respon; ?>', '1');
    </script>