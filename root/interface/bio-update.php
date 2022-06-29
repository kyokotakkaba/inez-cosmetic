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
    
    $nik = saring($_POST['nik']);

    if($nik!==''){
        $nikAda = cekNIK($nik, 'root', $id);
        $nikAda2 = cekNIK($nik, 'karyawan', $id);
        if($nikAda=='1'||$nikAda2=='1'){
?>
            <script type="text/javascript">
                tampilkanPesan('0','NIK sudah digunakan.');
            </script>
<?php    
            exit();        
        }
        else{
            $qG = "
                    SELECT
                        nik
                    FROM
                        root
                    WHERE
                        id = '$id'
            ";
            $eG = mysqli_query($conn, $qG);
            $rG = mysqli_fetch_assoc($eG);
            $oldNik = $rG['nik'];

            if($oldNik!==$nik){
                $gantiUname = '1';
            }
            else{
                $gantiUname = '0';
            }
        }
    }
    else{
        $gantiUname = '0';
    }

    $pass = saring($_POST['pass']);

    $nama = saring($_POST['nama']);
    $jk = saring($_POST['jk']);
    $tmpt_lahir = saring($_POST['tmpt_lahir']);
    $tgl_lahir = saring($_POST['tgl_lahir']);
    $alamat = saring($_POST['alamat']);
    $email = saring($_POST['email']);
    $hp = saring($_POST['hp']);

    $emailAda = cekEmail($email, 'root', $id);
    if($emailAda=='1'){
?>
        <script type="text/javascript">
            tampilkanPesan('0','Email sudah digunakan.');
        </script>
<?php    
        exit();        
    }

    $hpAda = cekHp($hp, 'root', $id);
    if($hpAda=='1'){
?>
        <script type="text/javascript">
            tampilkanPesan('0','No HP sudah digunakan.');
        </script>
<?php    
        exit();        
    }
    
    $fotoMentah = saring($_POST['foto']);
    $fotoPros = str_replace($baseLink, '', $fotoMentah);
    $foto = str_replace("../", "", $fotoPros);


    //jenis hak akses
    $jenis = saring($_POST['jenis']);
    //tingkat belajar
    $tingkat = saring($_POST['tingkat']);

    
    $q = "
            UPDATE 
                root 
            SET 
                nik='$nik',
                nama='$nama',
                jk='$jk',
                tmpt_lahir='$tmpt_lahir',
                tgl_lahir='$tgl_lahir',
                email='$email',
                hp='$hp',
                alamat='$alamat',
                foto='$foto'
            WHERE
                id='$id'
    ";

    $e = mysqli_query($conn, $q);

    if($e){
        $_SESSION['namaPengguna'] = $nama;
        $_SESSION['jkPengguna'] = $jk;

        if($gantiUname=='1'){
            $qU = "
                    UPDATE 
                        akun 
                    SET 
                        uname='$nik'
                    WHERE
                        id_pengguna='$id'
            ";
            $eU = mysqli_query($conn, $qU);

            if($eU){
?>
                <script type="text/javascript">
                    tampilkanPesan('1','Data berhasil disimpan, username berhasil dirubah.');
                    reloadFrame();
                </script>
<?php
                exit();            
            }
            else {
?>
                <script type="text/javascript">
                    tampilkanPesan('2','Data disimpan, Gagal merubah username.');
                </script>
<?php
                exit();                            
            }
        }
        else{
?>
            <script type="text/javascript">
                tampilkanPesan('1','Data berhasil disimpan, jika mengganti nama silahkan reload ulang laman.');
                reloadFrame();
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