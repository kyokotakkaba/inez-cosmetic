<?php
    session_start();
    
    $appSection = 'student';
    
    if(empty($_SESSION['idPengguna'])){
        echo "SESSION EXPIRED";
        exit();
    }

    $jenis = $_SESSION['jenisPengguna'];

    if($jenis!=='student'){
        echo "INVALID USER";
        exit();
    }
    
    require_once "../../conf/function.php";

    if(empty($_POST['no'])){
        echo "NOT PERMITED!!";
        exit();
    }

    $id_pengerjaan = $_SESSION['id_pengerjaan'];
    $id_student = $_SESSION['idPengguna'];

    $no = saring($_POST['no']);

    $q = "
            SELECT
                tpp.id,
                tpp.id_pertanyaan idP,

                tpj.id id_menjawab,
                tpj.id_jawaban,

                p.jenis,
                p.isi,

                (
                    SELECT
                        COUNT(id)

                    FROM
                        tes_pengerjaan_pertanyaan_jawaban

                    WHERE
                        id_pengerjaan = '$id_pengerjaan'
                    AND
                        id_pertanyaan = idP
                    AND
                        hapus = '0'
                ) jmlJawaban,


                (
                    SELECT
                        COUNT(id)

                    FROM
                        tes_pengerjaan_pertanyaan

                    WHERE
                        id_pengerjaan = '$id_pengerjaan'
                    AND
                        hapus = '0'
                ) jmlPertanyaan

            FROM
                tes_pengerjaan_pertanyaan tpp

            LEFT JOIN
                pertanyaan p
            ON
                tpp.id_pertanyaan = p.id

            LEFT JOIN
                tes_pengerjaan_jawab tpj
            ON
                tpp.id = tpj.id_pengerjaan_pertanyaan
            AND
                tpj.id_pengerjaan = '$id_pengerjaan'

            WHERE
                tpp.id_pengerjaan = '$id_pengerjaan'
            AND
                tpp.no = '$no'
            AND
                tpp.hapus = '0'

            LIMIT
                1
    ";
    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);
    if($c=='0'){
        echo "DATA NOT FOUND";
?>
        <p>
            <ul class="list">
                <li>Silahkan hubungi petugas</li>
                <li>Minta reset sesi ujian kamu</li>
                <li>Pergi ke panel <a href="../student/">Student</a></li>
                <li>Logout</li>
                <li>Login ulang</li>
            </ul>
        </p>
<?php        
        exit();
    }

    $r = mysqli_fetch_assoc($e);
    $id_pengerjaan_pertanyaan = $r['id'];

    $id_pertanyaan = $r['idP'];
    $pertanyaan = $r['isi'];
    $jenis = $r['jenis'];
    $infoJenis = '-';
    $ikon = 'info circle';
    if($jenis=='mchoice'){
        $infoJenis = 'Pilihan Ganda';
        $ikon = 'cubes';
    }
    else if($jenis=='essay'){
        $infoJenis = 'Uraian';
        $ikon = 'edit';
    }

    $id_menjawab = $r['id_menjawab'];
    if(empty($id_menjawab)){
        $id_menjawab = '-';
    }
    $jawab = $r['id_jawaban'];
    if(empty($jawab)){
        $jawab = '-';
    }

    $jmlJawaban = $r['jmlJawaban'];
    
    $jmlPertanyaan = $r['jmlPertanyaan'];

?>
<h2 class="ui block header">
    <i class="<?php echo $ikon; ?> icon"></i>
    <div class="content">
        Soal No <?php echo $no; ?>
        <div class="sub header">
            <?php echo $infoJenis; ?>
        </div>
    </div>
</h2>

<input type="hidden" id="id_pengerjaan_pertanyaan" name="id_pengerjaan_pertanyaan" value="<?php echo $id_pengerjaan_pertanyaan; ?>">
<input type="hidden" id="jenis" name="jenis" value="<?php echo $jenis; ?>">

<div class="pertanyaanPlace">
    <?php echo html_entity_decode($pertanyaan); ?>
</div>



<input type="hidden" id="id_menjawab" name="id_menjawab" value="<?php echo $id_menjawab; ?>">

<?php
    /*
    jenis :
    1. mchoice
    2.essay
    */

    if($jenis=='mchoice'){
        if($jmlJawaban=='0'){
            $q = "
                    SELECT
                        id idJ,
                        isi

                    FROM
                        jawaban

                    WHERE
                        id_pertanyaan = '$id_pertanyaan'

                    AND
                        hapus = '0'

                    ORDER BY
                        RAND()
            ";
        }
        else{
            $q = "
                    SELECT
                        tppj.id_jawaban idJ,

                        j.isi

                    FROM
                        tes_pengerjaan_pertanyaan_jawaban tppj

                    LEFT JOIN
                        jawaban j
                    ON
                        tppj.id_jawaban = j.id

                    WHERE
                        tppj.id_pengerjaan = '$id_pengerjaan'
                    AND
                        tppj.id_pertanyaan = '$id_pertanyaan'
                    AND
                        tppj.hapus = '0'

                    ORDER BY
                        tppj.no ASC

            ";
        }

        $e = mysqli_query($conn, $q);
        $c = mysqli_num_rows($e);
        if($c=='0'){
            echo "DATA NOT FOUND";
            exit();
        }

        $arJawaban = array();
        $rJ = array();
        while ($d = mysqli_fetch_assoc($e)) {
            $idJ = $d['idJ'];
            $jawaban = $d['isi'];

            $rJ['idJ'] = $idJ;
            $rJ['jawaban'] = $jawaban;

            $arJawaban[] = $rJ;
        }

        if($jmlJawaban=='0'){
            $q = "
                    INSERT INTO 
                        tes_pengerjaan_pertanyaan_jawaban
                            (
                                id, 
                                id_pengerjaan, 
                                id_pertanyaan, 
                                id_jawaban, 
                                no
                            ) 

                    VALUES
            ";

            $ke = 0;
            foreach ($arJawaban as $dJ) {
                $idD = UUIDBaru();
                $idJ = $dJ['idJ'];
                $ke = $ke +1;
                $q .= "
                            (
                                '$idD',
                                '$id_pengerjaan',
                                '$id_pertanyaan',
                                '$idJ',
                                '$ke'
                            )
                ";
                if($ke<4){
                    $q .= ",";
                }
            }

            $e = mysqli_query($conn, $q);
        }

?>
        <table class="ui very basic collapsing celled table unstackable">
            <tbody>
<?php
            $arMask = array(
                '1' => 'A',
                '2' => 'B',
                '3' => 'C',
                '4' => 'D'
            );

            $ke = 0;
            foreach ($arJawaban as $dJ) {
                $idJ = $dJ['idJ'];
                $jawaban = $dJ['jawaban'];
                $ke = $ke +1;

                $teks = $arMask[$ke];

                if($idJ==$jawab){
                    $classBtnOpsi = 'ative blue';
                }
                else{
                    $classBtnOpsi = '';
                }
?>
                <tr>
                    <td width="4%">
                        <div class="ui icon button <?php echo $classBtnOpsi; ?> btnOpsi btnOpsi<?php echo $idJ; ?>" onclick="jawabSoal('<?php echo $id_pertanyaan; ?>', '<?php echo $idJ; ?>')">
                            <?php echo $teks; ?>
                        </div>
                    </td>
                    <td>
                        <?php echo html_entity_decode($jawaban); ?>
                    </td>
                </tr>
<?php                
            }
?>
            </tbody>
        </table>
<?php        
    }
    else{
        if($jawab=='-'){
            $jawab = '';
        }
?>
        <div class="ui hidden divider"></div>
        <div class="field">
            <label>Jawaban</label>
            <textarea id="jawab" name="jawab" ><?php echo html_entity_decode($jawab); ?></textarea>
        </div>
        <div class="field">
            <div class="ui icon primary button" onclick="jawabSoal('<?php echo $id_pertanyaan; ?>', '0')">
                <i class="save icon"></i> Simpan Jawaban
            </div>
        </div>
<?php        
    }

   
    //populate daa for display
    if($no=='1'){
        $classPrev = 'disabled';
        $classNext = '';
    }
    else if ($no==$jmlPertanyaan){
        $classPrev = '';
        $classNext = 'disabled';
    }
    else{
        $classPrev = '';
        $classNext = '';
    }

    $idPrev = $no-1;
    $idNext = $no+1;

?>

<div class="ui vertical basic segment clearing" style="margin-top: 10px;">
    <div class="ui icon button left floated <?php echo $classPrev; ?>" data-content="Soal sebelumnya" onclick="loadSoal('<?php echo $idPrev; ?>')" >
        <i class="left chevron icon"></i>
    </div>

    <div class="ui icon button right floated <?php echo $classNext; ?>" data-content="Soal selanjutnya" onclick="loadSoal('<?php echo $idNext; ?>')" >
        <i class="right chevron icon"></i>
    </div>
</div>

<?php
    $qR = "
           UPDATE 
                tes_pengerjaan_pertanyaan 

            SET 
                last='0'
    ";
    $eR = mysqli_query($conn, $qR);

    //update last activity
    $qL = "
            UPDATE 
                tes_pengerjaan_pertanyaan 

            SET 
                last='1'

            WHERE
                id='$id_pengerjaan_pertanyaan'
    ";
    $eL = mysqli_query($conn, $qL);
?>

<script type="text/javascript">
    $('.button').popup();

<?php
    if($jenis=='essay'){
?>
        var editorJawab = CKEDITOR.replace('jawab',{
            height : 200,
            filebrowserBrowseUrl : '../filemanager/dialog.php?type=2&editor=ckeditor&fldr=',
            filebrowserUploadUrl : '../filemanager/dialog.php?type=2&editor=ckeditor&fldr=',
            filebrowserImageBrowseUrl : '../filemanager/dialog.php?type=1&editor=ckeditor&fldr='
         });
<?php        
    }
?>    
</script>