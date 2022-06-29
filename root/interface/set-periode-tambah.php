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

    $q = "
            SELECT
                id
            FROM
                periode
            ORDER BY
                id DESC
            LIMIT
                1
    ";
    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);

    if($c=='0'){
        $thn = date('Y');
    }
    else{
        $r = mysqli_fetch_assoc($e);
        $last = $r['id'];
        $thnLast = substr($last, 0, 4);
        $thn = $thnLast+1;
    }

    $qI = "
            INSERT INTO 
                periode
                    (
                        id, 
                        nama
                    ) 
            VALUES 
                    (
                        '$thn',
                        'Periode pelatihan tahun $thn'
                    )
    ";

    $eI = mysqli_query($conn, $qI);

    if($eI){
?>
        <script type="text/javascript">
            tampilkanPesan('1','Periode berhasil ditambah.');
            reloadFrame();
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