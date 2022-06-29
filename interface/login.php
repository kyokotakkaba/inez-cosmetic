<?php
    session_start();

    require_once "../conf/function.php";

    $uname = saring($_POST['uname']);
    $pwd = saring($_POST['pass']);
	$pass 		= md5($acak.md5($pwd));

    $q = "
    		SELECT
    			id,
    			jenis,
                id_pengguna

    		FROM
    			akun

    		WHERE
    			uname = '$uname'
    		AND
    			pass = '$pass'

    		LIMIT
    			1
    ";

    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);
    if($c == '0'){
?>
        <script type="text/javascript">
            tampilkanPesan('0','Cek Username dan Password.');
            loadingSelesai();
        </script>
<?php       
        exit();
    }

    $r = mysqli_fetch_assoc($e);
    $idAkun = $r['id'];
    $jenis = $r['jenis'];
    if($jenis!=='root'){
        $tabel = 'karyawan';
    }
    else{
        $tabel = $jenis;
    }
    $idPengguna = $r['id_pengguna'];



    $q = "
            SELECT
                nama,
                jk

            FROM
                $tabel

            WHERE
                id = '$idPengguna'
            AND
                hapus = '0'

            LIMIT
                1
    ";

    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);
    if($c == '0'){
?>
        <script type="text/javascript">
            tampilkanPesan('0','Pengguna tidak valid.');
            loadingSelesai();
        </script>
<?php
        exit();
    }



    if($jenis=='admin'){
        $q = "
                SELECT 
                    id, 
                    id_wilayah

                FROM 
                    wilayah_supervisi 

                WHERE
                    hapus = '0'
                AND
                    id_karyawan = '$idPengguna'

                LIMIT
                    1
        ";

        $e = mysqli_query($conn, $q);
        $c = mysqli_num_rows($e);

        if($c == '0'){
            $_SESSION['supervisi'] = '0';
        }
        else {
            $r = mysqli_fetch_assoc($e);
            $id_wil_supervisi = $rS['id_wilayah'];
            $_SESSION['supervisi'] = '1';
            $_SESSION['idWilSupervisi'] = $id_wil_supervisi;
        }
    }

    $_SESSION['idAkun'] = $idAkun;
    $_SESSION['idPengguna'] = $idPengguna;
    $_SESSION['jenisPengguna'] = $jenis;
?> 
<script type="text/javascript">
    window.location.href = '<?php echo $jenis; ?>/';
</script>