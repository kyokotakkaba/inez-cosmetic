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
            UPDATE 
                materi_kelompok_bahasan

            SET 
                hapus='1'

            WHERE
                id='$id'
    ";

    $e = mysqli_query($conn, $q);

    if($e){
        $q = "
                UPDATE 
                    materi 

                SET 
                    hapus='1'

                WHERE
                    id_bahasan='$id'
        ";
        $e = mysqli_query($conn, $q);

        if($e){
            $q = "
                    UPDATE 
                        karyawan_belajar_materi k,
                        materi m

                    SET 
                        k.hapus='1'
                        
                    WHERE
                        k.id_materi = m.id
                    AND
                        m.id_bahasan='$id'
            ";
            $e = mysqli_query($conn, $q);
            if($e){
?>
                <script type="text/javascript">
                    tampilkanPesan('1','Data berhasil dihapus.');
                    $('#lastId').val('-');
                    $('#lastIdSub').val('-');
                    updateRow();
                </script>
<?php                            
                exit();
            }
            else{
?>
                <script type="text/javascript">
                    tampilkanPesan('0','Error saat menghapus riwayat belajar karyawan.');
                </script>
<?php        
                exit();
            }
        }
        else{
?>
            <script type="text/javascript">
                tampilkanPesan('0','Error saat menghapus materi.');
            </script>
<?php        
            exit();
        }
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