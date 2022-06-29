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

    $main = 'History Ujian';
    $sub = '';

    $idPengguna = $_SESSION['idPengguna'];
    $idPengerjaan = saring($_POST['idData']);

    //get data from DB
    $q = "
            SELECT 
                kup.id, 
                kup.id_pertanyaan, 
                kup.id_jawaban idTerjawab,
                kup.no noPertanyaan,
                kup.nilai,
                kup.nilai_pk,

                p.isi pertanyaan,

                kdj.id_jawaban idJawaban,
                kdj.no noJawaban,

                j.isi jawaban,
                j.benar

            FROM 
                karyawan_ujian_pengerjaan kup

            LEFT JOIN
                pertanyaan p
            ON
                kup.id_pertanyaan = p.id

            LEFT JOIN
                karyawan_ujian_pengerjaan_daftar_jawaban kdj
            ON
                kdj.id_pertanyaan = kup.id_pertanyaan
            AND
                kdj.id_pengerjaan = '$idPengerjaan'

            RIGHT JOIN
                jawaban j
            ON
                j.id = kdj.id_jawaban

            WHERE
                kup.id_pengerjaan = '$idPengerjaan'

            ORDER BY
                kup.no ASC,
                kdj.no ASC
    ";

    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);

    if($c=='0'){
        echo "DATA NOT FOUND";
        exit();
    }
    else{
        //creat global array
        $r = array();
        $ar = array();

        while ($d = mysqli_fetch_assoc($e)) {
            $idPengerjaanSub = $d['id'];
            $idPertanyaan = $d['id_pertanyaan'];
            $idTerjawab = $d['idTerjawab'];
            $pertanyaan = $d['pertanyaan'];

            $idJawaban = $d['idJawaban'];
            $jawaban = $d['jawaban'];
            $benar = $d['benar'];

            $nilai = $d['nilai'];
            $nilai_pk = $d['nilai_pk'];

            //input value  for gllobal array
            $r['idPengerjaanSub'] = $idPengerjaanSub;
            $r['idPertanyaan'] = $idPertanyaan;
            $r['idTerjawab'] = $idTerjawab;
            $r['pertanyaan'] = $pertanyaan;
            $r['idJawaban'] = $idJawaban;
            $r['jawaban'] = $jawaban;
            $r['benar'] = $benar;
            $r['nilai'] = $nilai;
            $r['nilai_pk'] = $nilai_pk;

            $ar[] = $r;
        }

        //max count item for global array
        $car = $c-1;

        //create indepeendent array
        $arIdPengerjaanSub = array();
        $arIdPertanyaan = array();
        $arPertanyaan = array();
        $arIdTerjawab = array();
        $arScore = array();

        //the numbers of the questions (at the final)
        $numb = 0;

        for ($i=0; $i <= $car; $i++) {
            $idPengerjaanSub = $ar[$i]['idPengerjaanSub'];
            $idPertanyaan = $ar[$i]['idPertanyaan'];
            $pertanyaan = $ar[$i]['pertanyaan'];

            $idTerjawab = $ar[$i]['idTerjawab'];
            if($idTerjawab==''){
                $idTerjawab = '0';
            }

            $idJawaban = $ar[$i]['idJawaban'];
            $jawaban = $ar[$i]['jawaban'];
            $benar = $ar[$i]['benar'];

            $nilai = $ar[$i]['nilai'];
            if($nilai==''){
                $nilai = '0';
            }
            $nilai_pk = $ar[$i]['nilai_pk'];
            if($nilai_pk==''){
                $nilai_pk = '0';
            }
            $score = $nilai+$nilai_pk;
            

            if($i==0){
                //first data of all important should be import to array
                array_push($arIdPengerjaanSub, $idPengerjaanSub);
                array_push($arIdPertanyaan, $idPertanyaan);
                array_push($arPertanyaan, $pertanyaan);
                array_push($arIdTerjawab, $idTerjawab);
                array_push($arScore, $score);

                //create array for string id question asc
                $idAns = 'arIdJawaban'.$numb;
                $$idAns = array();
                array_push($$idAns, $idJawaban);

                //create array for storing question based on id question asc
                $ans = 'arJawaban'.$numb;
                $$ans = array();
                array_push($$ans, $jawaban);

                $ben = 'arBenar'.$numb;
                $$ben = array();
                array_push($$ben, $benar);
            }
            else{
                $x = $i-1;
                $idPengerjaanSubPrev = $ar[$x]['idPengerjaanSub'];
                if($idPengerjaanSub!==$idPengerjaanSubPrev){
                    //next data if not the same with previoue should be import to array
                    array_push($arIdPengerjaanSub, $idPengerjaanSub);
                    array_push($arIdPertanyaan, $idPertanyaan);
                    array_push($arPertanyaan, $pertanyaan);
                    array_push($arIdTerjawab, $idTerjawab);
                    array_push($arScore, $score);

                    //increae the number indicate how many the questions
                    $numb = $numb+1;
                    //initialize the new (increase number) id answers array
                    $idAns = 'arIdJawaban'.$numb;
                    $$idAns = array();
                    //insert id of the answers to array
                    array_push($$idAns, $idJawaban);

                    //initialize the new (increase number) answers array
                    $ans = 'arJawaban'.$numb;
                    $$ans = array();
                    array_push($$ans, $jawaban);

                    $ben = 'arBenar'.$numb;
                    $$ben = array();
                    array_push($$ben, $benar);
                }
                else{
                    //insert id and answers to array
                    array_push($$idAns, $idJawaban);
                    array_push($$ans, $jawaban);

                    $ben = 'arBenar'.$numb;
                    array_push($$ben, $benar);
                }
            }
        }
    }






    $q = "
            SELECT 
                ku.id_pelaksanaan, 
                ku.id_karyawan, 
                ku.tanggal tglKerja, 
                ku.mulai, 
                ku.selesai waktuSelesai, 
                ku.nilai_akhir,
                ku.n,
                ku.n_pk,
                ku.nilai_grade,
                ku.n_poin,
                ku.remidi,
                ku.remidi_karena,

                up.id_periode,
                up.id_ujian, 
                up.kkm, 
                up.tanggal tglUjian, 
                up.waktu,

                u.nama namaUjian, 
                u.deskripsi deskUjian

            FROM 
                karyawan_ujian ku

            LEFT JOIN
                ujian_pelaksanaan up
            ON
                up.id = ku.id_pelaksanaan

            LEFT JOIN
                ujian u
            ON
                up.id_ujian = u.id

            WHERE
                ku.id = '$idPengerjaan'
    ";
    $e = mysqli_query($conn, $q);
    $r = mysqli_fetch_assoc($e);

    $id_ujian = $r['id_ujian'];
    $namaUjian = $r['namaUjian'];
    $deskUjian = $r['deskUjian'];
    $tglUjian = $r['tglUjian'];
    $waktu = $r['waktu'];
    $kkm = $r['kkm'];

    $tglKerja = $r['tglKerja'];
    $waktuMulai = $r['mulai'];
    $waktuSelesai = $r['waktuSelesai'];
    
    $n = $r['n'];
    $n_pk = $r['n_pk'];
    $nilai_akhir = $r['nilai_akhir'];

    $nilai_grade = $r['nilai_grade'];
    $remidi = $r['remidi'];
    $remidi_karena = $r['remidi_karena'];


    $id_periode = $r['id_periode'];

    //if final or UN
    if($id_ujian == '5483-8ABF1C'){
        $n_poin = $r['n_poin'];
    }

    $sub = $namaUjian;
    if($tglKerja !== $tglUjian){
        $sub .= ' (Susulan)';
    }
?>
<div class="ui message">
    <div class="ui breadcrumb">
        <div class="section"><?php echo $main; ?></div>
        <i class="right angle icon divider"></i>
        <div class="active section"><?php echo $sub; ?></div>
    </div>
</div>

<div class="field">
    <div class="ui icon button" onclick="backToMain()">
        <i class="left chevron icon"></i> Kembali
    </div>
</div>

<div class="ui very relaxed ordered list">
<?php
    //populate the answer and question display
    for ($i=0; $i <= $numb; $i++) {
        $nomorSoal = $i+1;
        $idPengerjaanSub = $arIdPengerjaanSub[$i];
        $idPertanyaan = $arIdPertanyaan[$i];
        $pertanyaan = $arPertanyaan[$i];

        $idTerjawab = $arIdTerjawab[$i];
        if($idTerjawab!=='0'&&$idTerjawab!==''){
            $classLabel = 'blue';
        }
        else{
            $classLabel = '';
        }

        $score = $arScore[$i];

        if($score=='0'){
            $objTitle = 'div';
        }
        else{
            $objTitle = 'a';
        }

        $idJwb = 'arIdJawaban'.$i;
        $jwb = 'arJawaban'.$i;
        $bnr = 'arBenar'.$i;

        $no = $i+1;
        $classAct = '';
?>        
<div class="item">
    <div id="lbl<?php echo $idPertanyaan; ?>" class="ui lblStatus empty circular label <?php echo $classLabel; ?>" style="position: absolute; margin: 4px auto; right: 10px;">
    </div>
    <div class="content">
        <<?php echo $objTitle; ?> class="header">Nilai : <?php echo $score; ?></<?php echo $objTitle; ?>>
        <div class="description">
            <div style="margin: 6px 4px 6px 0px;">
                <?php echo html_entity_decode($pertanyaan); ?>    
            </div>
            

            <table class="ui very basic collapsing celled table unstackable">
                <tbody>
<?php
                $aSudah = 0;
                $bSudah = 0;
                $cSudah = 0;

                for ($z=0; $z <= 2; $z++) {
                    $idJ = ${$idJwb}[$z];
                    $jawaban = ${$jwb}[$z];
                    $benar = ${$bnr}[$z];

                    if($aSudah=='0'){
                        $teks = 'A';    
                        $aSudah = '1';
                    }
                    else if($bSudah=='0'){
                        $teks = 'B';
                        $bSudah = '1';
                    }
                    else if($cSudah=='0'){
                        $teks = 'C';
                        $cSudah = '1';
                    }



                    if($idJ==$idTerjawab){
                        $classBtnOpsi = 'ative primary';
                    }
                    else{
                        $classBtnOpsi = '';
                    }


                    if($benar=='1'){
                        $classKet = 'positive';
                        $teksKet = '(Benar)';
                    }
                    else{
                        $classKet = 'negative';
                        $teksKet = '';
                    }
                    
?>
                    <tr class="<?php echo $classKet; ?>">
                        <td width="4%">
                            <div id="btn<?php echo $idJ; ?>" class="ui icon button btnOpsi<?php echo $idPertanyaan; ?> <?php echo $classBtnOpsi; ?>">
                                <?php echo $teks; ?>
                            </div>
                        </td>
                        <td>
                            <?php echo $jawaban; ?> &nbsp; <span><?php echo $teksKet; ?></span>
                        </td>
                    </tr>
<?php           
                }
?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
    }
?> 
    </div>

<?php

    
?>
<div class="ui grey segment">
    Pengerjaan dilakukan pada <strong><?php echo tanggalKan($tglKerja).'</strong> (<strong>'.$waktuMulai.'</strong> sampai <strong>'.$waktuSelesai.'</strong>)'; ?><br><br>
    Nilai Akhir :<br>
    <strong><?php echo $nilai_akhir; ?></strong><br><br>
    Terdiri Dari :
    <ul class="ui list">
        <li>Product Knowledge (PK) = <?php echo $n_pk; ?></li>
        <li>CH (Customer Handling) = <?php echo $n; ?></li>
    </ul>
<?php
    //if UN
    if($id_ujian == '5483-8ABF1C'){
        echo 'Poin Tambahan : <br> <strong>'.$n_poin.'</strong>';
?>
        <p style="font-size: 9pt; color: #60646D;">
            Poin diambil dari rerata maksimal 3 ujian bulanan sebelumnya. Pembulatan <strong>Nilai akhir</strong> maksimal setelah ditambah poin tetap 100.
        </p>
<?php        
    }
?>
</div>

<script type="text/javascript">
    $('.button').popup();
    $('.accordion').accordion();
</script>