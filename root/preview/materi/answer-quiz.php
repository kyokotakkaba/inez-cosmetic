<?php
    session_start();
    $appSection = 'root';

    $fromHome = '../../../';

    if(empty($_SESSION['idPengguna'])){
        echo "SESSION EXPIRED";
        exit();
    }

    $jenisPengguna = $_SESSION['jenisPengguna'];
    if($jenisPengguna !== $appSection){
        echo "NOT PERMITTED";
        exit();
    }

    require_once $fromHome.'conf/function.php';

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
            
            if($jawaban==$jawab){
                $benar = '1';
                $teksInfo = 'Jawaban benar.';
            }
            else{
                $benar = '0';
                $teksInfo = 'Jawaban salah.';
            }

            $tanggal = date('Y-m-d');
            $jam = date('H:i:s');

            if($idJawab=='0'){
                $idJawabBaru = UUIDBaru();
            }

            if($idJawab=='0'){
                $balikIdJawab = $idJawabBaru;
            }
            else{
                $balikIdJawab = $idJawab;
            }                          
?>
            <script type="text/javascript">
                tampilkanPesan('1','<?php echo $teksInfo; ?>');

                $('#btn<?php echo $idJawab; ?>').addClass('orange');
                $('#btnJawabKuis').removeClass('loading');
                $('.btnOpsi').removeClass('loading');
            </script>
<?php                
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