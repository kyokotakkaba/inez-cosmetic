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

    if(empty($_POST['id_calon'])){
?>
        <script type="text/javascript">
            tampilkanPesan('0','Pilih karyawan untu dijadikan peserta ujian.');
        </script>
<?php        
        exit();
    }

    //jumlah id yang terpilih
    $jml = count($_POST['id_calon']);

    $sampai = 1;

    $id = saring($_POST['id']);
    $susulan = saring($_POST['susulan']);

    if($susulan=='1'){
        $tanggal = saring($_POST['tgl_susulan']);
    }
    else{
        $tanggal = '';
        $tanggal = date('Y-m-d');
    }

    $q = "
            INSERT INTO 
                ujian_pelaksanaan_target_karyawan
                    (
                        id, 
                        id_pelaksanaan, 
                        id_karyawan,
                        susulan,
                        susulan_tgl
                    ) 
            VALUES
    ";

    

    foreach ($_POST['id_calon'] as $idMentah) {
        $idCalon = saring($idMentah);
        $idBaru = UUIDBaru();
        $q.="
                (
                    '$idBaru',
                    '$id',
                    '$idCalon',
                    '$susulan',
                    '$tanggal'
                )
            ";

        if($jml>1){
            if($sampai<$jml){
                $q.="
                        ,
                ";
            }
        }

        $sampai = $sampai+1;
    }
    
    $e = mysqli_query($conn, $q);


    if($e){
?>
        <script type="text/javascript">
            tampilkanPesan('1','Data karyawan berhasil dijadikan peserta ujian.');
            reloadSub();
            $('#lastIdSub').val('-');
            updateRowT();
        </script>
<?php
        exit();            
    }
    else{
?>
        <script type="text/javascript">
            tampilkanPesan('0','Terjadi kesalahan saat memproses data.');
        </script>
<?php
        exit();            
    }
?>