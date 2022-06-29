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
    $huruf = strtoupper(saring($_POST['huruf']));
    $min = saring($_POST['min']);
    $max = saring($_POST['max']);

    if($min <= 0){
?>
        <script type="text/javascript">
            tampilkanPesan('0','Nilai Min tidak boleh kurang dari atau sama dengan 0');
        </script>
<?php        
        exit();
    }

    if($max > 100){
?>
        <script type="text/javascript">
            tampilkanPesan('0','Nilai Max tidak boleh lebih dari 100');
        </script>
<?php        
        exit();
    }


    if($min>=$max){
?>
        <script type="text/javascript">
            tampilkanPesan('0','Nilai Min tidak boleh sama atau melebihi nilai Max');
        </script>
<?php        
        exit();
    }

    $qC = "
            SELECT
                id

            FROM
                ujian_grade

            WHERE
                hapus = '0'

            LIMIT
                1
    ";
    $eC = mysqli_query($conn, $qC);
    $rC = mysqli_num_rows($eC);
    $adaGrade = '0';
    if($rC=='1'){
        $adaGrade = '1';
    }
    
    if($id=='0'){
        $idBaru = UUIDBaru();
        $q = "
                INSERT INTO 
                    ujian_grade
                        (
                            id, 
                            huruf, 
                            min, 
                            max
                        ) 
                VALUES 
                        (
                            '$idBaru',
                            '$huruf',
                            '$min',
                            '$max'
                        )
        ";
    }
    else{
        $q = "
                UPDATE 
                    ujian_grade 
                SET 
                    huruf='$huruf',
                    min='$min',
                    max='$max'
                WHERE
                    id='$id'
        ";
    }


    if($adaGrade=='1'){
        $qF = "
                SELECT
                    id,
                    min,
                    max,
                    huruf

                FROM
                    ujian_grade

                WHERE
                    hapus = '0'
                AND
                    id != '$id'
                AND

                    (
                        min = $min
                    OR
                        min = $max
                    OR
                        max = $min
                    OR
                        max = $max
                    )

                OR
                    (
                        hapus = '0'
                    AND
                        id != '$id'
                    AND
                        min > $min
                    AND
                        max > $max
                    )
                OR
                    (
                        hapus = '0'
                    AND
                        id != '$id'
                    AND
                        min > $min
                    AND
                        max < $max
                    )
                OR
                    (
                        hapus = '0'
                    AND
                        id != '$id'
                    AND
                        min < $min
                    AND
                        max > $max
                    )

                OR
                    hapus = '0'
                AND
                    id != '$id'
                AND
                    huruf = '$huruf'

                LIMIT
                    1
        ";

        echo $qF;

        $eF = mysqli_query($conn, $qF);
        $cF = mysqli_num_rows($eF);
        if($cF=='1'){
?>
            <script type="text/javascript">
                tampilkanPesan('0','Batas awal dan akhir tidak valid (bentrok dengan data lain) atau huruf grade telah dipakai.');
            </script>
<?php            
            exit();
        }
    }
    
    $e = mysqli_query($conn, $q);

    if($e){
?>
        <script type="text/javascript">
            tampilkanPesan('1','Data berhasil disimpan.');
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