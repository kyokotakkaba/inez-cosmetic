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

    $id = saring($_POST['id_karyawan']);
    
    $email = saring($_POST['email']);
    $emailAda = adaData($email, 'root', 'email', $id);
    $emailAda1 = adaData($email, 'karyawan', 'email', $id);
    if($emailAda=='1' || $emailAda1=='1'){
?>
        <script type="text/javascript">
            tampilkanPesan('0','E-Mail sudah digunapan.');
        </script>
<?php    
        exit();        
    }
    
    $hp = saring($_POST['hp']);
    $hpAda = adaData($hp, 'root', 'hp', $id);
    $hpAda1 = adaData($hp, 'karyawan', 'hp', $id);
    if($hpAda=='1' || $hpAda1=='1'){
?>
        <script type="text/javascript">
            tampilkanPesan('0','Nomor HP sudah digunapan.');
        </script>
<?php    
        exit();        
    }

    $nama = saring($_POST['nama']);
    $tmpt_lahir = saring($_POST['tmpt_lahir']);
    $tgl_lahir = saring($_POST['tgl_lahir']);
    $jk = saring($_POST['jk']);
    $alamat = saring($_POST['alamat']);

    $q = "
            UPDATE 
                karyawan 

            SET 
                nama='$nama',
                tmpt_lahir='$tmpt_lahir',
                tgl_lahir='$tgl_lahir',
                jk='$jk',
                email='$email',
                hp='$hp',
                alamat='$alamat'
                
            WHERE
                id='$id'
    ";
    $e = mysqli_query($conn, $q);
    if(!$e){
?>
        <script type="text/javascript">
            tampilkanPesan('0', 'Gagal memperbarui biodata.');
        </script>
<?php   
        exit();
    }
?>
    <script type="text/javascript">
        tampilkanPesan('1','Biodata berhasil diperbarui.');
        reloadFrame();
    </script>