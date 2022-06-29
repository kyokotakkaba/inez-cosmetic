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
    
    $limit  = saring($_POST['limit']);
    $cari   = saring($_POST['cari']);

    $id_pelaksanaan     = saring($_POST['lastId']);

    if(is_numeric($limit)){
        $q = "
                SELECT 
                    up.id idP

                FROM 
                    ujian_pelaksanaan_target_karyawan up
                
                LEFT JOIN
                    karyawan k
                ON
                    up.id_karyawan = k.id

                LEFT JOIN
                    tingkat_belajar t
                ON
                    k.tingkat = t.id

                LEFT JOIN
                    wilayah w
                ON
                    k.id_wil = w.id

                LEFT JOIN
                    karyawan_ujian ku
                ON
                    up.id_karyawan = ku.id_karyawan
                AND
                    up.id_pelaksanaan = ku.id_pelaksanaan

                WHERE
                    up.id_pelaksanaan = '$id_pelaksanaan'
                AND
                    up.hapus = '0'
        ";

        if($cari!==''){
            $q.="
                    AND 
                    (
                        k.nik LIKE '%$cari%'
                    OR
                        k.nama LIKE '%$cari%'
                    OR
                        w.nama LIKE '%$cari%'
                    )
            ";
        }

        $q.="   
                ORDER BY 
                    k.nik ASC
        ";

    }
    else{
?>
        <div class="item active">
            !NUM
        </div>
<?php
        exit();
    }

    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);

    if($c=='0'){
?>
        <div class="item active">
            0
        </div>
<?php
        exit();
    }
    
    $jumlahPage = ceil($c/$limit);
    $startFrom = 0;
    
    for($i=1; $i<=$jumlahPage; $i++){
?>
    <a id="numberSub<?php echo $i; ?>" class="item <?php if($i==1){ echo "active"; }?>" onclick="updateListSub('<?php echo $startFrom; ?>','<?php echo $i; ?>')">
        <?php echo $i ?>
    </a>
<?php
        $startFrom = $startFrom+$limit;
    }
?>
