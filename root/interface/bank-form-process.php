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

    $idKelompok = saring($_POST['idKelompok']);
    $idBahasan = '';
    $idMateri = '';

    $isi = saring($_POST['isi']);

    $jwb0 = saring($_POST['jwb0']);
    $jwb1 = saring($_POST['jwb1']);
    $jwb2 = saring($_POST['jwb2']);
    
    $benar0 = saring($_POST['benar0']);
    $benar1 = saring($_POST['benar1']);
    $benar2 = saring($_POST['benar2']);
    
    if($id=='0'){
        $idPertanyaanBaru = UUIDBaru();
        $qP = "
                INSERT INTO 
                    pertanyaan
                        (
                            id, 
                            id_kelompok, 
                            id_bahasan, 
                            id_materi, 
                            isi
                        ) 
                VALUES 
                        (
                            '$idPertanyaanBaru',
                            '$idKelompok',
                            '$idBahasan',
                            '$idMateri',
                            '$isi'
                        )
        ";
    }
    else{
        $qP = "
                UPDATE 
                    pertanyaan 

                SET 
                    id_kelompok='$idKelompok',
                    id_bahasan='$idBahasan',
                    id_materi='$idMateri',
                    isi='$isi'
                    
                WHERE
                    id='$id'
        ";
    }

    //echo $qM.'<br><br>';

    $eP = mysqli_query($conn, $qP);

    if($eP){
        if($id=='0'){
            $qJ = "
                    INSERT INTO 
                        jawaban
                            (
                                id, 
                                id_pertanyaan, 
                                isi, 
                                benar
                            ) 
                    VALUES 
            ";

            for ($i=0; $i <=2 ; $i++) {
                $idJ = UUIDBaru();
                $p = 'jwb'.$i;
                $b = 'benar'.$i;
                $J = $$p;
                $B = $$b;

                $qJ .= "
                            (
                                '$idJ',
                                '$idPertanyaanBaru',
                                '$J',
                                '$B'
                            )
                ";

                if($i<2){
                    $qJ .= ",";
                }
            }

            $eJ = mysqli_query($conn, $qJ);

            if($eJ){
?>
                <script type="text/javascript">
                    tampilkanPesan('1','Data berhasil disimpan.');
                    $('#lastId').val('-');
                    backToMain();
                    updateRow();
                </script>
<?php                    
                exit();
            }
            else{
?>
                <script type="text/javascript">
                    tampilkanPesan('0','Terjadi masalah saat menyimpan jawaban kuis.');
                </script>
<?php                    
                exit();
            }
        }
        else{
            $qG = "
                    SELECT 
                        id
                    FROM 
                        jawaban 
                    WHERE
                        id_pertanyaan = '$id'
                    ORDER BY
                        id ASC
            ";
            $eG = mysqli_query($conn, $qG);

            $qUj = "";
            $numb = 0;

            while ($rG = mysqli_fetch_assoc($eG)) {
                $idJ = $rG['id'];

                $p = 'jwb'.$numb;
                $b = 'benar'.$numb;
                $J = $$p;
                $B = $$b;

                $qUj .= "
                        UPDATE 
                            jawaban 
                        SET 
                            isi='$J',
                            benar='$B'
                        WHERE
                            id='$idJ'
                ";

                if($numb<2){
                    $qUj .= ";";
                }

                $numb = $numb+1;
            }

            $eUj = mysqli_multi_query($conn, $qUj);
            if($eUj){
?>
                <script type="text/javascript">
                    tampilkanPesan('1','Data berhasil disimpan.');
                    $('#lastId').val('-');
                    backToMain();
                    updateRow();
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
    }
?>