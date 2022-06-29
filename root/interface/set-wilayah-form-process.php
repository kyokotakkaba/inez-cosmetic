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
    $id_kelompok = saring($_POST['id_kelompok']);
    $nama = saring($_POST['nama']);
    $kode = strtoupper(saring($_POST['kode']));
    $id_supervisi = saring($_POST['id_supervisi']);
    $id_karyawan = saring($_POST['id_karyawan']);

    //echo 'id data : '.$id.'<br><br>nama : '.$nama.'<br><br>id supervisi : '.$id_supervisi.'<br><br>id karyawan sebagai supervisi : '.$id_karyawan;

    if($id=='0'){
        $idBaru = UUIDBaru();
        $qP = "
                    INSERT INTO 
                        wilayah
                            (
                                id, 
                                id_kelompok,
                                nama, 
                                kode
                            ) 
                    VALUES 
                            (
                                '$idBaru',
                                '$id_kelompok',
                                '$nama',
                                '$kode'
                            )
        ";
    }
    else{
        $qP = "
                UPDATE 
                    wilayah 

                SET 
                    id_kelompok='$id_kelompok',
                    nama='$nama',
                    kode='$kode'

                WHERE
                    id='$id'
        ";
    }

    $eP = mysqli_query($conn, $qP);

    if($eP){
        if($id_karyawan!==''){
            if($id=='0'){
                $id_wil = $idBaru;
            }
            else{
                $id_wil = $id;
            }
            
            if($id_supervisi=='0'){
                $idSupBaru = UUIDBaru();
                $q = "
                        INSERT INTO 
                            wilayah_supervisi
                                (
                                    id, 
                                    id_wilayah, 
                                    id_karyawan
                                ) 
                        VALUES 
                                (
                                    '$idSupBaru',
                                    '$id_wil',
                                    '$id_karyawan'
                                )
                ";
            }
            else{
                $qP = "
                        SELECT
                            id_karyawan

                        FROM
                            wilayah_supervisi

                        WHERE
                            id = '$id_supervisi'
                        AND
                            hapus = '0'

                        LIMIT
                            1
                ";
                $eP = mysqli_query($conn, $qP);
                $cP = mysqli_num_rows($eP);
                if($cP=='1'){
                    $rP = mysqli_fetch_assoc($eP);
                    $id_old_k = $rP['id_karyawan'];

                    $qK = "
                            UPDATE
                                akun

                            SET
                                jenis = 'user'

                            WHERE
                                id_pengguna = '$id_old_k'
                    ";
                    $eK = mysqli_query($conn, $qK);
                }

                $q  = "
                        UPDATE
                            wilayah_supervisi

                        SET
                            id_karyawan = '$id_karyawan'

                        WHERE
                            id = '$id_supervisi'
                ";
            }

            $e = mysqli_query($conn, $q);

            $qA = "
                    UPDATE
                        akun
                        
                    SET
                        jenis = 'admin'

                    WHERE
                        id_pengguna = '$id_karyawan'
            ";
            $eA = mysqli_query($conn, $qA);
?>
            <script type="text/javascript">
                tampilkanPesan('1','Data berhasil disimpan');
                updateRow();
                $('#lastId').val('-');
                backToMain();
            </script>
<?php
            exit();
        }
        else{
?>
            <script type="text/javascript">
                tampilkanPesan('1','Data berhasil disimpan');
                updateRow();
                $('#lastId').val('-');
                backToMain();
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