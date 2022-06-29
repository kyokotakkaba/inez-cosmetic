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

    $id_pelaksanaan = saring($_POST['id_pelaksanaan']);
    $susulan = saring($_POST['susulan']);
    if($susulan=='0'){
        $susulan_tgl = date('Y-m-d');    
    }
    else{
        $susulan_tgl = saring($_POST['tgl_susulan']);  
    }
    

    //jumlah id yang terpilih
    $jml = count($_POST['id_karyawan']);

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

    $sampai = 1;

    foreach ($_POST['id_karyawan'] as $idMentah) {
        $idCalon = saring($idMentah);
        $idBaru = UUIDBaru();
        $q.="
                (
                    '$idBaru',
                    '$id_pelaksanaan',
                    '$idCalon',
                    '$susulan',
                    '$susulan_tgl'
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