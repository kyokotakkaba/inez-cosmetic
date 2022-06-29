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
	
	$id	= saring($_POST['idData']);

    $q = "
            SELECT
                nik

            FROM
                karyawan

            WHERE
                id = '$id'

            LIMIT
                1
    ";

    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);

    if($c=='0'){
?>
        <script type="text/javascript">
            tampilkanPesan('0','ID tidak valid.');
        </script>
<?php        
        exit();
    }
    else{
        $r = mysqli_fetch_assoc($e);
        $nik = $r['nik'];
        if($nik==''){
?>
            <script type="text/javascript">
                tampilkanPesan('0','NIK Kosong.');
            </script>
<?php
            exit();
        }

        $qD = "
                DELETE FROM
                    akun

                WHERE
                    uname = '$nik'
                AND
                    id_pengguna != '$id'
        ";
        $eD = mysqli_query($conn, $qD);



        $passBaru = md5($acak.md5($nik));

        $qA = "
                UPDATE 
                    akun 

                SET 
                    uname = '$nik',
                    pass='$passBaru'

                WHERE
                    id_pengguna='$id'
        ";
        $eA = mysqli_query($conn, $qA);
        if($eA){
?>
            <script type="text/javascript">
                tampilkanPesan('1','Akun berhasil direset.');
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
    }
?>
