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

    $idKelompok = saring($_POST['idKelompok']);
    $idBahasan = saring($_POST['idBahasan']);

    $idTingkat = saring($_POST['idTingkat']);

    $idMateri = saring($_POST['idMateri']);

    $judul = saring($_POST['judul']);
    $deskripsi = saring($_POST['deskripsi']);
    
    $banerMentah = saring($_POST['baner']);
    $baner = str_replace($baseLink, '', $banerMentah);
    $baner = str_replace('..//', '', $baner);
    $baner = str_replace("../", "", $baner);

    $isi = saring($_POST['isi']);
    $buku1Mentah = saring($_POST['buku1']);
    $buku1 = str_replace($baseLink, '', $buku1Mentah);
    $buku1 = str_replace("../", "", $buku1);

    $buku2Mentah = saring($_POST['buku2']);
    $buku2 = str_replace($baseLink, '', $buku2Mentah);
    $buku2 = str_replace("../", "", $buku2);

    $lampiranMentah = saring($_POST['lampiran']);
    $lampiran = str_replace($baseLink, '', $lampiranMentah);
    $lampiran = str_replace("../", "", $lampiran);

    $idKuis = saring($_POST['idKuis']);
    $pertanyaan = saring($_POST['pertanyaan']);
    $jenis = saring($_POST['jenis']);

    $jwb0 = saring($_POST['jwb0']);
    $jwb1 = saring($_POST['jwb1']);
    $jwb2 = saring($_POST['jwb2']);
    
    $benar0 = saring($_POST['benar0']);
    $benar1 = saring($_POST['benar1']);
    $benar2 = saring($_POST['benar2']);
    
    $jawaban = saring($_POST['jawaban']);

    if($idMateri=='0'){
        $idMateriBaru = UUIDBaru();

        $qN = "
                SELECT
                    COUNT(id) jml
                FROM
                    materi
                WHERE
                    id_kelompok = '$idKelompok'
                AND
                    id_bahasan = '$idBahasan'
        ";
        $eN = mysqli_query($conn, $qN);
        $rA = mysqli_fetch_assoc($eN);
        $no_a = $rA['jml'];

        $no = $no_a + 1;

        $kode = kodeBaru();

        $qM = "
                INSERT INTO 
                    materi
                        (
                            id, 
                            id_periode, 
                            id_kelompok, 
                            id_bahasan, 
                            id_tingkat_belajar, 
                            no, 
                            judul, 
                            deskripsi, 
                            baner, 
                            isi, 
                            buku1, 
                            buku2, 
                            lampiran,
                            kode
                        ) 
                VALUES 
                        (
                            '$idMateriBaru',
                            '$idPeriode',
                            '$idKelompok',
                            '$idBahasan',
                            '$idTingkat',
                            '$no',
                            '$judul',
                            '$deskripsi',
                            '$baner',
                            '$isi',
                            '$buku1',
                            '$buku2',
                            '$lampiran',
                            '$kode'
                        )
        ";

        $judulNotif = 'Penambahan materi belajar';
        $isiNotif = 'Pihak pengelola menambahkan materi belajar yang berjudul '.$judul.'.';
    }
    else{
        $qM = "
                UPDATE 
                    materi 
                SET 
                    id_tingkat_belajar='$idTingkat',
                    judul='$judul',
                    deskripsi='$deskripsi',
                    baner='$baner',
                    isi='$isi',
                    buku1='$buku1',
                    buku2='$buku2',
                    lampiran='$lampiran'
                WHERE
                    id='$idMateri'
        ";

        $judulNotif = 'Pembaruan materi belajar';
        $isiNotif = 'Pihak pengelola memperbarui materi belajar yang berjudul '.$judul.'.';
    }

    $untukNotif = 'all';

    //echo $qM.'<br><br>';

    $eM = mysqli_query($conn, $qM);

    if($eM){
        //send system notif
        sendNotif($judulNotif, $isiNotif, $untukNotif);

        if($idMateri=='0'){
            $idKuisBaru = UUIDBaru();
            $qK = "
                    INSERT INTO 
                        materi_kuis
                            (
                                id, 
                                id_materi, 
                                jenis, 
                                pertanyaan, 
                                aktif
                            ) 
                    VALUES 
                            (
                                '$idKuisBaru',
                                '$idMateriBaru',
                                '$jenis',
                                '$pertanyaan',
                                '1'
                            )
            ";

            $eK = mysqli_query($conn, $qK);

            $qJ = "
                    INSERT INTO 
                        materi_kuis_jawaban
                            (
                                id, 
                                id_kuis, 
                                jenis, 
                                jawaban, 
                                benar
                            ) 
                    VALUES 
            ";

            if($eK){
                if($jenis=='mchoice'){
                    for ($i=0; $i <=2 ; $i++) { 
                        $idJ = UUIDBaru();
                        $p = 'jwb'.$i;
                        $b = 'benar'.$i;
                        $J = $$p;
                        $B = $$b;

                        $qJ .= "
                                    (
                                        '$idJ',
                                        '$idKuisBaru',
                                        '$jenis',
                                        '$J',
                                        '$B'
                                    )
                        ";

                        if($i<2){
                            $qJ .= ",";
                        }
                    }
                }
                else {
                    $idJBaru = UUIDBaru();
                    $qJ .= "
                            (
                                '$idJBaru',
                                '$idKuisBaru',
                                '$jenis',
                                '$jawaban',
                                '1'
                            )
                    ";
                }

                //echo $qJ.'<br><br>';

                $eJ = mysqli_query($conn, $qJ);

                if($eJ){
?>
                    <script type="text/javascript">
                        tampilkanPesan('1','Data berhasil disimpan.');
                        $('#lastIdSub').val('-');
                        updateRowSub();
                        backFromSub();
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
?>
                <script type="text/javascript">
                    tampilkanPesan('0','Terjadi kesalhan saat menyimpan pertanyaan kuis.');
                </script>
<?php            
                exit();    
            }
        }
        else{
            $qC = "
                    SELECT 
                        id, 
                        jenis, 
                        pertanyaan
                    FROM 
                        materi_kuis 
                    WHERE
                        id_materi = '$idMateri'
                    AND
                        aktif = '1'
                    LIMIT
                        1
            ";
            $eC = mysqli_query($conn, $qC);
            $cC = mysqli_num_rows($eC);
            if($cC=='1'){
                $rC = mysqli_fetch_assoc($eC);

                $idK = $rC['id'];
                $J = $rC['jenis'];

                $qK = "
                        UPDATE 
                            materi_kuis 
                        SET 
                            jenis='$jenis',
                            pertanyaan='$pertanyaan',
                            aktif='1'
                        WHERE
                            id='$idK'
                ";
                $eK = mysqli_query($conn, $qK);
                if($eK){
                    if($jenis!==$J){
                        $qD = "
                                DELETE FROM 
                                    materi_kuis_jawaban 
                                WHERE
                                    id_kuis = '$idK'
                        ";
                        $eD = mysqli_query($conn, $qD);

                        $qJ = "
                                INSERT INTO 
                                    materi_kuis_jawaban
                                        (
                                            id, 
                                            id_kuis, 
                                            jenis, 
                                            jawaban, 
                                            benar
                                        ) 
                                VALUES 
                        ";

                        if($jenis=='mchoice'){
                            for ($i=0; $i <=2 ; $i++) { 
                                $idJ = UUIDBaru();
                                $p = 'jwb'.$i;
                                $b = 'benar'.$i;
                                $J = $$p;
                                $B = $$b;

                                $qJ .= "
                                            (
                                                '$idJ',
                                                '$idK',
                                                '$jenis',
                                                '$J',
                                                '$B'
                                            )
                                ";

                                if($i<2){
                                    $qJ .= ",";
                                }
                            }
                        }
                        else {
                            $idJBaru = UUIDBaru();
                            $qJ .= "
                                    (
                                        '$idJBaru',
                                        '$idK',
                                        '$jenis',
                                        '$jawaban',
                                        '1'
                                    )
                            ";
                        }

                        $eJ = mysqli_query($conn, $qJ);
                        if($eJ){
?>
                            <script type="text/javascript">
                                tampilkanPesan('1','Data berhasil disimpan.');
                                updateRowSub();
                                $('#lastIdSub').val('-');
                                backFromSub();
                            </script>
<?php                            
                            exit();
                        }
                        else{
?>
                            <script type="text/javascript">
                                tampilkanPesan('0','Terjadi kesalahan saat memproses jawaban kuis.');
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
                                    materi_kuis_jawaban 
                                WHERE
                                    id_kuis = '$idK'
                                AND
                                    jenis = '$jenis'
                                ORDER BY
                                    id ASC
                        ";
                        $eG = mysqli_query($conn, $qG);

                        if($jenis=='mchoice'){
                            $ar = array();
                            $r = array();

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
                                            materi_kuis_jawaban 
                                        SET 
                                            jawaban='$J',
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
                                    updateRowSub();
                                    $('#lastIdSub').val('-');
                                    backFromSub();
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
                        else {
                            $rG = mysqli_fetch_assoc($eG);
                            $idK = $rG['id'];

                            $qUj = "
                                        UPDATE 
                                            materi_kuis_jawaban 
                                        SET 
                                            jawaban='$jawaban'
                                        WHERE
                                            id='$idK'
                            ";
                            $eUj = mysqli_query($conn, $qUj);
                            if($eUj){

?>
                                <script type="text/javascript">
                                    tampilkanPesan('1','Data berhasil disimpan.');
                                    updateRowSub();
                                    $('#lastIdSub').val('-');
                                    backFromSub();
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
                }
            } 
        }
    }
    else{
?>
        <script type="text/javascript">
            tampilkanPesan('0','Terjadi kesalahan saat memproses data.')
        </script>
<?php
        exit();        
    }
?>