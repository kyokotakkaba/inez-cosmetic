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


    $q = "
            DELETE FROM
                karyawan_ujian

            WHERE
                id = '$id'
    ";
    $e = mysqli_query($conn, $q);

    if($e){
        $q = "
                DELETE FROM
                    karyawan_ujian_pengerjaan

                WHERE
                    id_pengerjaan = '$id'
        ";
        $e = mysqli_query($conn, $q);
        if($e){
            $q = "
                    DELETE FROM
                        karyawan_ujian_pengerjaan_daftar_jawaban

                    WHERE
                        id_pengerjaan = '$id'
            ";
            $e = mysqli_query($conn, $q);
            if($e){
?>
                <script type="text/javascript">
                    tampilkanPesan('1','Reset pengerjaan berhasil.');
                    updateRowSub();
                    updateRow();
                </script>
<?php           
            }
            else{
?>
                <script type="text/javascript">
                    tampilkanPesan('0','Terjadi kesalahan saat menghapus list jawaban peserta.');
                </script>
<?php                                
            }
        }
        else{
?>
            <script type="text/javascript">
                tampilkanPesan('0','Terjadi kesalahan saat menghapus list pertanyaan peserta.');
            </script>
<?php                                            
        }
    }
    else{
?>
        <script type="text/javascript">
            tampilkanPesan('0','Terjadi kesalahan saat memproses data.');
        </script>                          
<?php        
    }
?>