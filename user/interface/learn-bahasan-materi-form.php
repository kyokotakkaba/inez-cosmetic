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

    $idData = saring($_POST['idData']);
    $pecah = explode('[pisah]', $idData);

    $idBahasan = saring($pecah[0]);
    $idMateri = saring($pecah[1]);

    $idPengguna = $_SESSION['idPengguna'];

    $qNo = "
            SELECT 
                k.nama,

                t.no
            FROM
                karyawan k

            LEFT JOIN 
                tingkat_belajar t
            ON
                k.tingkat = t.id

            WHERE
                k.id = '$idPengguna'
    ";
    $eNo = mysqli_query($conn, $qNo);
    $rNo = mysqli_fetch_assoc($eNo);
    $noTingkat = $rNo['no'];

    $q = "
            SELECT 
                m.no, 
                m.judul, 
                m.deskripsi, 
                m.baner, 
                m.isi, 
                m.buku1, 
                m.buku2, 
                m.lampiran, 
                m.kode,

                k.nama kelompok,
                b.nama bahasan,
                b.no noBahasan,
                t.nama tingkat

            FROM 
                materi m

            LEFT JOIN
                materi_kelompok k
            ON
                m.id_kelompok = k.id

            LEFT JOIN
                materi_kelompok_bahasan b
            ON
                m.id_bahasan = b.id

            LEFT JOIN
                tingkat_belajar t
            ON
                m.id_tingkat_belajar = t.id

            WHERE
                m.id = '$idMateri'
            LIMIT
                1
    ";

    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);
    if($c=='1'){

        $idLastBaru = UUIDBaru();
        $tanggal = date('Y-m-d');
        $jam = date('H:i:s');

        $qC = "
                SELECT 
                    id
                FROM 
                    karyawan_belajar_materi 
                WHERE
                    id_karyawan = '$idPengguna'
                AND
                    id_bahasan = '$idBahasan'
                AND
                    id_materi = '$idMateri'
                LIMIT
                    1
        ";

        $eC = mysqli_query($conn, $qC);
        $cC = mysqli_num_rows($eC);
        if($cC=='1'){
            $rC = mysqli_fetch_assoc($eC);
            $idDataLast = $rC['id'];

            //updatee last activity as active again
            $qU = "
                    UPDATE 
                        karyawan_belajar_materi 
                    SET 
                        tanggal = '$tanggal',
                        jam = '$jam',
                        last='1'
                    WHERE
                        id='$idDataLast'
            ";

        }
        else{
            //set last activity
            $qU = "
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
                                '$idLastBaru',
                                '$idPeriode',
                                '$idPengguna',
                                '$idBahasan',
                                '$idMateri',
                                '$tanggal',
                                '$jam',
                                '1'
                            )
            ";

        }

        //reset last activity
        $qZ = "
                UPDATE 
                    karyawan_belajar_materi 
                SET 
                    last='0' 
                WHERE
                    id_karyawan='$idPengguna'
        ";
        $eZ = mysqli_query($conn, $qZ);

        if($eZ){
            //execute last activity
            $eU = mysqli_query($conn, $qU);
        }
        

        $r = mysqli_fetch_assoc($e);

        $noMateri = $r['no'];
        $noBahasan = $r['noBahasan'];
        $judul = $r['judul'];
        $deskripsi = $r['deskripsi'];
        $banerUrl = $r['baner'];
        if($banerUrl==''){
            $baner = '../../files/photo/agenda.png';
        }
        else{
            // $banerUrl = str_replace('%20', ' ', $banerUrl);
            // if(file_exists('../../'.$banerUrl)){
            //     $baner = '../'.$banerUrl;
            // }
            // else{
            //     $baner = '../files/photo/agenda.png';
            // }
            if(!empty($banerUrl) && $banerUrl!==''){
                $headers = @get_headers($banerUrl);
                $checkFile = strpos($headers[0],'200');
                if($checkFile){
                    $baner = $banerUrl;
                }  
            }
        }

        $kelompok = $r['kelompok'];
        $bahasan = $r['bahasan'];
        $tingkat = $r['tingkat'];



        $qB = "
                SELECT
                    nama

                FROM
                    materi_kelompok_bahasan

                WHERE
                    id = '$idBahasan'
        ";
        $eB = mysqli_query($conn, $qB);
        $rB = mysqli_fetch_assoc($eB);
        $bahasan = $rB['nama'];
?>
        <style type="text/css">
            .responsif {
                width: 100%;
                height: auto;
            }    
        </style>

        <div class="ui message">
            <div class="ui breadcrumb">
                <div class="section">Belajar</div>
                <i class="right chevron icon divider"></i>
                <div class="section"><?php echo $bahasan; ?></div>
                <i class="right chevron icon divider"></i>
                <div class="active section"><?php echo $judul; ?></div>
            </div>
        </div>

        <div class="ui basic vertical segment clearing" style="margin: 0px; padding: 0px;">
            <div class="ui icon button right floated" data-content="Tutup" onclick="backFromSub()">
                <i class="close icon"></i>
            </div>
        </div>

        <div id="tabMenu" class="ui pointing two item menu inverted">
            <a class="active item" data-tab="materi">
                <i class="file outline icon"></i> Materi
            </a>
            <a class="item" data-tab="quiz">
                <i class="cubes icon"></i> Kuis
            </a>
        </div>

        <div class="ui active tab" data-tab="materi">
<?php
            $isi = $r['isi'];
            echo html_entity_decode($isi);
?>
            <div class="ui hidden divider"></div>
            <div class="ui hidden divider"></div>

            <div class="ui horizontal divider">
                Materi pendukung    
            </div>
            <table class="ui table unstackable">
                <thead>
                    <tr>
                        <th width="4%">No</th>
                        <th>Keterangan</th>
                        <th width="10%">Opsi</th>
                    </tr>
                </thead>
                <tbody>
    <?php
            $noTabel = '0';
            $buku1Url = $r['buku1'];
            if($buku1Url==''){
                $buku1 = '';
            }
            else{
                if(!empty($buku1Url) && $buku1Url!==''){
                    $headers = @get_headers($buku1Url);
                    $checkFile = strpos($headers[0],'200');
                    if($checkFile){
                        $buku1 = $buku1Url;
                        $noTabel = $noTabel+1;
                        ?>
                        <tr>
                        <td><?php echo $noTabel; ?></td>
                        <td>Materi 1</td>
                        <td>
                            <a href="<?php echo $buku1; ?>" target="_blank" class="ui icon button" >
                                <i class="cloud download icon"></i>
                            </a>
                        </td>
                    </tr>
                    <?php
                    }else{
                        $buku1 = '';
                    }
                }
                /* $buku1Url = str_replace('%20', ' ', $buku1Url);
                    if(file_exists('../../'.$buku1Url)){
                    $buku1 = '../'.$buku1Url;
                    $noTabel = $noTabel+1;?>
                    <tr>
                        <td><?php echo $noTabel; ?></td>
                        <td>Materi 1</td>
                        <td>
                            <a href="<?php echo $buku1; ?>" target="_blank" class="ui icon button" >
                                <i class="cloud download icon"></i>
                            </a>
                        </td>
                    </tr>
    <?php                            
                }
                else{
                    $buku1 = '';
                }*/
            }

            $buku2Url = $r['buku2'];
            if($buku2Url==''){
                $buku2 = '';
            }
            else{
                if(!empty($buku2Url) && $buku2Url!==''){
                    $headers = @get_headers($buku2Url);
                    $checkFile = strpos($headers[0],'200');
                    if($checkFile){
                        $buku2 = $buku2Url;
                        $noTabel = $noTabel+1;
                        ?>
                        <tr>
                        <td><?php echo $noTabel; ?></td>
                        <td>Materi 2</td>
                        <td>
                            <a href="<?php echo $buku2; ?>" target="_blank" class="ui icon button" >
                                <i class="cloud download icon"></i>
                            </a>
                        </td>
                    </tr>
                    <?php
                    }else{
                        $buku2 = '';
                    }
                }
                /*$buku2Url = str_replace('%20', ' ', $buku2Url);
                if(file_exists('../../'.$buku2Url)){
                    $buku2 = '../'.$buku2Url;
                    $noTabel = $noTabel+1;
    ?>
                    <tr>
                        <td><?php echo $noTabel; ?></td>
                        <td>Materi 2</td>
                        <td>
                            <a href="<?php echo $buku2; ?>" target="_blank" class="ui icon button" >
                                <i class="cloud download icon"></i>
                            </a>
                        </td>
                    </tr>
    <?php                                                        
                }
                else{
                    $buku2 = '';
                }*/
            }

            $lampiranUrl = $r['lampiran'];
            if($lampiranUrl==''){
                $lampiran = '';
            }
            else{
                if(!empty($lampiranUrl) && $lampiranUrl!==''){
                    $headers = @get_headers($lampiranUrl);
                    $checkFile = strpos($headers[0],'200');
                    if($checkFile){
                        $lampiran = $lampiranUrl;
                        $noTabel = $noTabel+1;
                        ?>
                        <tr>
                            <td><?php echo $noTabel; ?></td>
                            <td>Lampiran materi</td>
                            <td>
                                <a href="<?php echo $lampiran; ?>" target="_blank" class="ui icon button" >
                                    <i class="cloud download icon"></i>
                                </a>
                            </td>
                        </tr>
                        <?php               
                    }else{
                        $lampiran = '';
                    }
                }
                /*$lampiranUrl = str_replace('%20', ' ', $lampiranUrl);
                if(file_exists('../../'.$lampiranUrl)){
                    $lampiran = '../'.$lampiranUrl;
                    $noTabel = $noTabel+1;
    ?>
                    <tr>
                        <td><?php echo $noTabel; ?></td>
                        <td>Lampiran materi</td>
                        <td>
                            <a href="<?php echo $lampiran; ?>" target="_blank" class="ui icon button" >
                                <i class="cloud download icon"></i>
                            </a>
                        </td>
                    </tr>
    <?php                                                        
                }
                else{
                    $lampiran = '';
                }*/
            }

            if($noTabel=='0'){
    ?>
                    <tr>
                        <td colspan="3">
                            <i class="info teal circle icon"></i> <i>Tidak ada lampiran/ buku yang dapat diunduh.</i>
                        </td>
                    </tr>
    <?php                        
            }
    ?>
                </tbody>
            </table>
        </div>



        <div class="ui tab" data-tab="quiz">
<?php
    $qQ = "
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
    $eQ = mysqli_query($conn, $qQ);
    $cQ = mysqli_num_rows($eQ);
    if($cQ=='1'){
        $rQ = mysqli_fetch_assoc($eQ);
        $idPertanyaan = $rQ['id'];
        $jenis = $rQ['jenis'];
        $pertanyaan = $rQ['pertanyaan'];
?>
        <p>
            <?php echo html_entity_decode($pertanyaan); ?>
        </p>
        <div class="ui horizontal divider">
            <i class="cube icon"></i> Jawaban
        </div>
<?php   
        $qA = "
                SELECT 
                    id, 
                    id_periode, 
                    jawaban, 
                    tanggal,
                    jam, 
                    benar 

                FROM 
                    karyawan_belajar_kuis 

                WHERE
                    id_karyawan = '$idPengguna'
                AND
                    id_kuis = '$idPertanyaan'
                AND
                    jenis = '$jenis'

                LIMIT
                    1
            ";
        $eA = mysqli_query($conn, $qA);
        $cA = mysqli_num_rows($eA);

        if($cA=='1'){
            $rA = mysqli_fetch_assoc($eA);
            $idJawab = $rA['id'];
            $jawab = strtoupper($rA['jawaban']);
            $benar = strtoupper($rA['benar']);
            if($benar=='1'){
                $classJawab = 'disabled';
            }
            else{
                $classJawab = '';
            }
            
        }
        else {
            $idJawab = '0';
            $jawab = '';
            $benar = '0';
            $classJawab = '';
        }
?>
            <input type="hidden" id="idMateriKuis" value="<?php echo $idMateri; ?>">
            <input type="hidden" id="idKuis" value="<?php echo $idPertanyaan; ?>">

            <input type="hidden" id="jenis" name="jenis" value="<?php echo $jenis; ?>">
            
            <input type="hidden" id="idJawab" name="idJawab" value="<?php echo $idJawab; ?>">
            <input type="hidden" id="benar" name="benar" value="<?php echo $benar; ?>">

<?php

        $qA ="
                SELECT 
                    id,
                    jawaban

                FROM 
                    materi_kuis_jawaban 

                WHERE
                    hapus = '0'
                AND
                    id_kuis = '$idPertanyaan'
                AND
                    jenis = '$jenis'

                ORDER BY
                    RAND()
        ";
        $eA = mysqli_query($conn, $qA);
        $cA = mysqli_num_rows($eA);
        if($cA=='0'){
?>
            <div class="ui message">
                <p>Jawaban tidak ditemukan.</p>
            </div>
<?php            
        }
        else{
            if($jenis=='essay'){
?>
                <div class="field">
                    <input type="text" id="jawab" name="jawab" placeholder="Jawaban" maxlength="64" value="<?php echo $jawab; ?>">
                </div>
                <div class="field">
                    <div id="btnJawabKuis" class="ui icon primary button <?php echo $classJawab; ?> " onclick="jawabKuis('0')">
                        <i class="save icon"></i> Jawab
                    </div>
                </div>
<?php                
            }
            else {
?>
                <input type="hidden" id="jawab" name="jawab" value="<?php echo $jawab; ?>" >
                <table class="ui very basic collapsing celled table unstackable">
                    <tbody>
<?php
                $abc = 0;
                while ($rA=mysqli_fetch_assoc($eA)) {
                    $idJawaban = $rA['id'];
                    $jawaban = $rA['jawaban'];

                    if($idJawaban==$jawab){
                        $clasBtn = 'green';
                    }
                    else {
                        $clasBtn = '';
                    }

                    if($benar=='1'){
                        $clasBtn .=' disabled';
                    }

                    if($abc=='0'){
                        $huruf = 'A';
                    }
                    else if($abc=='1'){
                        $huruf = 'B';
                    }
                    else if($abc=='2'){
                        $huruf = 'C';
                    }
                    else{
                        $huruf = '[X]';
                    }
                    
?>
                        <tr>
                            <td width="4%">
                                <div id="btn<?php echo $idJawaban; ?>" class="ui icon button btnOpsi <?php echo $clasBtn; ?>" onclick="jawabKuis('<?php echo $idJawaban; ?>')">
                                    <?php echo $huruf; ?>
                                </div>
                            </td>
                            <td>
                                <?php echo $jawaban; ?>
                            </td>
                        </tr>
<?php                   
                    $abc = $abc+1;
                }
?>                        
                    </tbody>
                </table>
<?php                
            }
            
            if($benar=='1'){
?>
                <div class="ui message">
                    <p>Kuis terjawab pada <strong><i><?php echo tanggalKan($tanggal).' jam '.$jam; ?></i></strong></p>
                </div>
<?php                
            }
        }
    }
    else{
?>
            <div class="ui message">
                <p>Belum ada kuis.</p>
            </div>
<?php
    }
?>
        </div>




        





<?php        
        $qPrev = "
            SELECT 
                m.id idMateri,
                m.judul, 
                m.no noMateri,
                m.id_bahasan idBahasan,

                b.nama bahasan, 
                b.no noBahasan

            FROM 
                materi m

            LEFT JOIN
                materi_kelompok_bahasan b
            ON
                m.id_bahasan = b.id

            LEFT JOIN
                materi_kelompok k
            ON
                b.id_kelompok = k.id

            LEFT JOIN
                tingkat_belajar t
            ON
                m.id_tingkat_belajar = t.id

            LEFT JOIN
                karyawan_belajar_materi l
            ON
                l.id_materi = m.id
            AND
                l.id_karyawan = '$idPengguna'

            WHERE
                m.hapus = '0'
            AND
                m.id_bahasan = '$idBahasan'
            AND
                    m.no < '$noMateri'
            AND
                (
                    t.no <= '$noTingkat'
            OR
                    m.id_tingkat_belajar = 'semua'
                )
        
            ORDER BY
                m.no DESC

            LIMIT
                1
        ";

        $ePrev = mysqli_query($conn, $qPrev);
        $cPrev = mysqli_num_rows($ePrev);
        if($cPrev=='1'){
            $rPrev = mysqli_fetch_assoc($ePrev);

            $rBahasan = $rPrev['idBahasan'];
            if($rBahasan!==$idBahasan){
                $idPrev = '';
                $classPrev = 'disabled';
            }
            else{
                $idPrev = $rBahasan.'[pisah]'.$rPrev['idMateri'];
                $classPrev = '';    
            }
        }
        else{
            $idPrev = '';
            $classPrev = 'disabled';
        }




        $qNext = "
            SELECT 
                m.id idMateri,
                m.judul, 
                m.no noMateri,
                m.id_bahasan idBahasan,

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
                m.id_bahasan = '$idBahasan'
            AND
                m.no > '$noMateri'
            AND
                (
                    t.no <= '$noTingkat'
            OR
                    m.id_tingkat_belajar = 'semua'
                )
        
            ORDER BY
                m.no ASC

            LIMIT
                1
        ";

        $eNext = mysqli_query($conn, $qNext);
        $cNext = mysqli_num_rows($eNext);
        if($cNext=='1'){
            $rNext = mysqli_fetch_assoc($eNext);

            $rBahasan = $rNext['idBahasan'];
            if($rBahasan!==$idBahasan){
                $idNext = '';
                $classNext = 'disabled';
            }
            else{
                $idNext = $rBahasan.'[pisah]'.$rNext['idMateri'];
                $classNext = '';
            }
        }
        else{
            $idNext = '';
            $classNext = 'disabled';
        }
?>
        <div class="ui vertical basic segment clearing" style="margin-top: 10px;">
            <div class="ui icon button left floated <?php echo $classPrev; ?>" data-content="Materi sebelumnya" onclick="loadMateri('<?php echo $idPrev; ?>','prev')" >
                <i class="left chevron icon"></i>
            </div>

            <div class="ui icon button right floated <?php echo $classNext; ?>" data-content="Materi selanjutnya" onclick="loadMateri('<?php echo $idNext; ?>', 'next')" >
                <i class="right chevron icon"></i>
            </div>
        </div>
<?php
    }
    else{
?>
        <div class="ui message">
            <p>Materi tidak ditemukan.</p>
        </div>
<?php                   
    }
?>
    


<script type="text/javascript">
    populateMateri();

    $('.button').popup();
    
    $('#tabMenu .item').tab({history:false});
    $('table').addClass('ui table');
    $('ul, ol').addClass('ui list');

    $('table, img, audio, video').removeAttr('style').addClass('responsif');

<?php
    if($c=='1'){
?>
        var lastLearn = $('#lastIdSub').val();
        if(lastLearn!=='<?php echo $idData; ?>'){
            populateMateri();
            populateBahasan();
        }
<?php
    }
?>
</script>