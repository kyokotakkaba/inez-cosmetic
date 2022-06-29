<?php
    session_start();
    $appSection = 'admin';

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
    $id_wil = saring($_POST['id_wil']);
    if($id_wil==''){
?>
        <script type="text/javascript">
            tampilkanPesan('0','Wilayah harus ditentukan.');
        </script>
<?php    
        exit();        
    }    

    //tingkat belajar
    $tingkat = saring($_POST['tingkat']);
    if($tingkat==''){
?>
        <script type="text/javascript">
            tampilkanPesan('0','Tingkatan belajar karyawan harus ditentukan.');
        </script>
<?php    
        exit();        
    }    
    
    $nik = saring($_POST['nik']);    
    $nikAda = cekNIK($nik, 'karyawan', $id);
    $nikAda2 = cekNIK($nik, 'root', $id);
    if($nikAda=='1' || $nikAda2=='1'){
?>
        <script type="text/javascript">
            tampilkanPesan('0','NIK sudah digunakan.');
        </script>
<?php    
        exit();        
    }

    $jenis = saring($_POST['jenis']);
    $email = saring($_POST['email']);
    $hp = saring($_POST['hp']);
    $nama = saring($_POST['nama']);
    $jk = saring($_POST['jk']);
    if($jk !== 'l' && $jk !== 'p'){
        $jk = 'n';
    }
    $tgl_masuk = saring($_POST['tgl_masuk']);

    if($id=='0'){
        $idBaru = UUIDBaru();
        $kode = kodeBaru();
        $q = "
                INSERT INTO 
                    karyawan
                        (
                            id, 
                            id_wil, 
                            kode, 
                            nik, 
                            nama, 
                            jk,
                            email, 
                            hp, 
                            tingkat,
                            tgl_masuk
                        ) 
                VALUES 
                        (
                            '$idBaru',
                            '$id_wil',
                            '$kode',
                            '$nik',
                            '$nama',
                            '$jk',
                            '$email',
                            '$hp',
                            '$tingkat',
                            '$tgl_masuk'
                        )
        ";
    }
    else{
        $q = "
                UPDATE 
                    karyawan 

                SET 
                    id_wil = '$id_wil',
                    nik = '$nik',
                    nama = '$nama',
                    jk = '$jk',
                    email = '$email',
                    hp = '$hp',
                    tgl_masuk = '$tgl_masuk'

                WHERE
                    id='$id'
        ";
    }

    $e = mysqli_query($conn, $q);

    if(!$e){
?>
        <script type="text/javascript">
            tampilkanPesan('0','Terjadi kesalahan saat memproses data.');
        </script>
<?php
        exit();            
    }






    if($id=='0'){
        //create new account
        $idAkunBaru = UUIDBaru();
        $passBaru   = md5($acak.md5($nik));

        $q = "
                INSERT INTO 
                    akun
                        (
                            id, 
                            uname, 
                            pass, 
                            id_pengguna, 
                            jenis
                        ) 
                VALUES (
                            '$idAkunBaru',
                            '$nik',
                            '$passBaru',
                            '$idBaru',
                            '$jenis'
                        )
        ";
    }
    else{
        //update the account info
        $q = "
                UPDATE 
                    akun 

                SET 
                    uname='$nik'

                WHERE
                    id_pengguna='$id'
        ";
    }

    $e = mysqli_query($conn, $q);
    if(!$e){
?>
        <script type="text/javascript">
            tampilkanPesan('0','Terjadi kesalahan saat pembuatan akun e-earning.');
        </script>
<?php
        exit();            
    }

?>
    <script type="text/javascript">
        tampilkanPesan('1','Data berhasil disimpan.');
        updateRow();
        $('#lastId').val('-');
        backToMain();
    </script>