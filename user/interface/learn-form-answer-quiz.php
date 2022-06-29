<?php
    session_start();
    $appSection = 'user';

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

    $idPengguna = $_SESSION['idPengguna'];

    $idMateriKuis = saring($_POST['idMateriKuis']);
    $idKuis = saring($_POST['idKuis']);
    $idJawab = saring($_POST['idJawab']);
    $jawab = trim(strtoupper(saring($_POST['jawab'])));

    $q = "
            SELECT 
                mk.id_materi, 
                mk.jenis,

                m.no noMateri, 

                b.no noBahasan,
                
                t.no noTingkat
                

            FROM 
                materi_kuis mk

            LEFT JOIN
                materi m
            ON
                m.id = mk.id_materi

            LEFT JOIN
                materi_kelompok_bahasan b
            ON
                b.id = m.id_bahasan

            LEFT JOIN
                tingkat_belajar t 
            ON
                t.id = m.id_tingkat_belajar

            WHERE
                mk.id = '$idKuis'
            AND
                mk.aktif = '1'

            LIMIT
                1
    ";
    $e = mysqli_query($conn, $q);
    if($e){
        $r = mysqli_fetch_assoc($e);
        $noMateri = $r['noMateri'];
        $noBahasan = $r['noBahasan'];
        $noTingkat = $r['noTingkat'];

        $jenis = $r['jenis'];

        $qA = "
                SELECT 
                    id, 
                    jawaban
                FROM 
                    materi_kuis_jawaban 
                WHERE
                    id_kuis = '$idKuis'
                AND
                    jenis = '$jenis'
                AND
                    benar = '1'
        ";
        $eA = mysqli_query($conn, $qA);
        if($eA){
            $rA = mysqli_fetch_assoc($eA);
            if($jenis=='essay'){
                $jawaban = trim(strtoupper($rA['jawaban']));    
            }
            else if($jenis=='mchoice'){
                $jawaban = $rA['id'];
            }
            
            if($jawaban == $jawab){
                $benar = '1';
                $teksInfo = 'Jawaban benar. Anda dapat lanjut ke materi selanjutnya.';
            }
            else{
                $benar = '0';
                $teksInfo = 'Jawaban salah.';
            }

            $tanggal = date('Y-m-d');
            $jam = date('H:i:s');

            if($idJawab=='0'){
                $idJawabBaru = UUIDBaru();
                $qI = "
                        INSERT INTO 
                            karyawan_belajar_kuis
                                (
                                    id, 
                                    id_periode, 
                                    id_karyawan, 
                                    id_kuis, 
                                    jenis, 
                                    jawaban, 
                                    tanggal, 
                                    jam, 
                                    benar
                                ) 
                        VALUES 
                                (
                                    '$idJawabBaru',
                                    '$idPeriode',
                                    '$idPengguna',
                                    '$idKuis',
                                    '$jenis',
                                    '$jawab',
                                    '$tanggal',
                                    '$jam',
                                    '$benar'
                                )
                ";
            }
            else {
                $qI = "
                        UPDATE 
                            karyawan_belajar_kuis 
                        SET 
                            jawaban='$jawab',
                            tanggal='$tanggal',
                            jam='$jam',
                            benar='$benar' 
                        WHERE
                            id='$idJawab'
                ";
            }

            $eI = mysqli_query($conn, $qI);

            if($eI){
                if($benar=='1'){
                    $qNext = "
                        SELECT 
                            m.id_bahasan idBahasan,
                            m.id idMateri,
                            m.judul, 
                            m.no noMateri,

                            b.nama bahasan, 
                            b.no noBahasan,

                            t.nama tingkat,
                            t.no noTingkat

                        FROM 
                            materi m

                        LEFT JOIN
                            materi_kelompok_bahasan b
                        ON
                            m.id_bahasan = b.id

                        LEFT JOIN
                            tingkat_belajar t
                        ON
                            m.id_tingkat_belajar = t.id

                        WHERE
                            m.hapus = '0'
                        AND
                            (
                                m.no > '$noMateri'
                        OR
                                b.no > '$noBahasan'
                            )
                            
                        AND
                            (
                                t.no <= '$noTingkat'
                        OR
                                m.id_tingkat_belajar = 'semua'
                            )
                    
                        ORDER BY
                            b.no ASC,
                            m.no ASC

                        LIMIT
                            1
                    ";

                    $eNext = mysqli_query($conn, $qNext);
                    $cNext = mysqli_num_rows($eNext);
                    if($cNext=='1'){
                        $rNext = mysqli_fetch_assoc($eNext);

                        $idBahN = $rNext['idBahasan'];
                        $idMatN = $rNext['idMateri'];

                        $idBBaru = UUIDBaru();
                        $tanggal = date('Y-m-d');
                        $jam = date('h:i:s');
                        $q = "
                                INSERT INTO 
                                    karyawan_belajar_materi
                                        (
                                            id, 
                                            id_periode, 
                                            id_karyawan, 
                                            id_bahasan,
                                            id_materi, 
                                            tanggal, 
                                            jam, 
                                            last
                                        )    
                                VALUES 
                                        (
                                            '$idBBaru',
                                            '$idPeriode',
                                            '$idPengguna',
                                            '$idBahN',
                                            '$idMatN',
                                            '$tanggal',
                                            '$jam',
                                            '0'
                                        )
                        ";
                        $e = mysqli_query($conn, $q);
                    }
                }

                if($idJawab=='0'){
                    $balikIdJawab = $idJawabBaru;
                }
                else{
                    $balikIdJawab = $idJawab;
                }
?>
                <script type="text/javascript">
                    tampilkanPesan('<?php echo $benar; ?>','<?php echo $teksInfo; ?>');
                    responJawabKuis('<?php echo $idKuis; ?>', '<?php echo $balikIdJawab; ?>', '<?php echo $jawab; ?>' , '<?php echo $benar; ?>');
                    populateMateri();
                    populateBahasan();
                </script>
<?php                
            }
        }
    }
    else{
?>
        <script type="text/javascript">
            tampilkanPesan('0','Terjadi kesalahan saat memproses data.');
        </script>
<?php        
    }
?>