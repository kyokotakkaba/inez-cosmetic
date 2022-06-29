<?php
    session_start();
    require_once "../conf/function.php";

    $idData = saring($_POST['idData']);

    if($idData !== '1'){
?>
        <script type="text/javascript">
            tampilkanPesan('0','Log out gagal, Terjadi kesalahan saat memproses data.');
        </script>
<?php        
        exit();
    }


    if(!empty($_SESSION['test'])){
        $id_pengerjaan = $_SESSION['id_pengerjaan'];

        $saatIni = date('H:i:s');
        $q = "
                UPDATE
                    karyawan_ujian

                SET
                    last_time = '$saatIni'

                WHERE
                    id = '$id_pengerjaan'
        ";
        $e = mysqli_query($conn, $q);
        if(!$e){
?>
            <script type="text/javascript">
                tampilkanPesan('0', 'Terjadi kesalahan saat memproses data.');
            </script>
<?php        
            exit();
        }

        unset($_SESSION['test']);
        unset($_SESSION['id_pelaksanaan']);
        unset($_SESSION['id_ujian']);
        unset($_SESSION['kkm']);
        unset($_SESSION['akhir']);
        unset($_SESSION['kode']);
    }

	session_destroy();
?>
	<script type="text/javascript">
		tampilkanPesan('1','Log out berhasil.');
		setTimeout(function(){
            window.location.href='../';
        }, 2000);
	</script>