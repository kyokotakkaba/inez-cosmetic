<?php
    session_start();
    $appSection = 'root';

    $fromHome = '../../../';

    if(empty($_SESSION['idPengguna'])){
        header('location: '.$fromHome);
        exit();
    }

    $jenisPengguna = $_SESSION['jenisPengguna'];
    if($jenisPengguna !== $appSection){
        header('location: '.$fromHome.''.$jenisPengguna);
        exit();
    }

    require_once $fromHome.'lib/core/head.php';
?>

        <div id="mainLoader" class="ui basic vertical segment container form">
<?php
    if(empty($_GET['kode'])){
?>
            <div class="ui icon message">
                <i class="inbox icon"></i>
                <div class="content">
                    <div class="header">
                        Preview tidak tersedia
                    </div>
                    <p>
                        Kode preview materi tidak terlampir.
                    </p>
                </div>
            </div>
<?php
        exit();
    }
    
    $kode = saring($_GET['kode']);


    $q = "
        SELECT 
            m.id,
            m.no, 
            m.judul, 
            m.deskripsi, 
            m.baner, 
            m.isi, 
            m.buku1, 
            m.buku2, 
            m.lampiran, 
            m.kode,
            m.hapus,

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
            m.kode = '$kode'
        LIMIT
            1
    ";

    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);
    if($c=='0'){
?>
        <div class="ui icon message">
            <i class="inbox icon"></i>
            <div class="content">
                <div class="header">
                    Preview tidak tersedia
                </div>
                <p>
                    Data tidak ditemukan
                </p>
            </div>
        </div>
<?php
        exit();
    }


    $r = mysqli_fetch_assoc($e);

    $hapus = $r['hapus'];
    if($hapus=='1'){
?>
        <div class="ui icon message">
            <i class="inbox icon"></i>
            <div class="content">
                <div class="header">
                    Preview tidak tersedia
                </div>
                <p>
                    Materi yang coba anda buka telah dihapus.
                </p>
            </div>
        </div>
<?php
        exit();
    }

    $idData = $r['id'];
    $judul = $r['judul'];
    $deskripsi = $r['deskripsi'];
    $banerUrl = $r['baner'];
    if($banerUrl==''){
        $baner = '../../../files/photo/agenda.png';
    }
    else{
        $banerUrl = str_replace('%20', ' ', $banerUrl);
        if(file_exists('../../../'.$banerUrl)){
            $baner = '../../../'.$banerUrl;
        }
        else{
            $baner = '../../../files/photo/agenda.png';
        }
    }
?>

    <h3 class="ui header block segment">
        <img src="<?php echo $baner; ?>">
        <div class="content">
            <?php echo $judul; ?>
            <div class="sub header">
                <?php echo $deskripsi; ?>
            </div>
        </div>
    </h3>

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
            // $buku1Url = str_replace('%20', ' ', $buku1Url);
            // echo $buku1Url;
            $headers = @get_headers($buku1Url);
            $check = strpos($headers[0],'200');
            if($check){
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
            }
            else{
                $buku1 = '';
            }
        }

        $buku2Url = $r['buku2'];
        if($buku2Url==''){
            $buku2 = '';
        }
        else{
            // $buku2Url = str_replace('%20', ' ', $buku2Url);
            $headers = @get_headers($buku2Url);
            $check = strpos($headers[0],'200');
            if($check){
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
            }
            else{
                $buku2 = '';
            }
        }

        $lampiranUrl = $r['lampiran'];
        if(!empty($lampiranUrl)&& $lampiranUrl !==''){
            // $lampiranUrl = str_replace('%20', ' ', $lampiranUrl);
            $headers = @get_headers($lampiranUrl);
            $check = strpos($headers[0],'200');
            if($check){
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
            }
            else{
                $lampiran = '';
            }
        }

        if($noTabel=='0'){
?>
                <tr>
                    <td colspan="3">
                        <i class="info teal circle icon"></i> <i>Tidak ada lampiran/ buku yand dabat diunduh.</i>
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
                id_materi = '$idData'
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
        <input type="hidden" id="idMateriKuis" value="<?php echo $idData; ?>">
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
                id ASC
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

    <div class="ui hidden divider"></div>
    <div class="ui hidden divider"></div>




        
    </div>
<br>
<br>
<?php
        require_once $fromHome.'lib/core/snippet.php';
        require_once $fromHome.'lib/core/footer.php';
?>

        <script type="text/javascript" src="<?php echo $fromHome; ?>lib/jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo $fromHome; ?>lib/semantic-ui/semantic.min.js"></script>
        <script type="text/javascript" src="<?php echo $fromHome; ?>lib/core/snippet.js"></script>
        <script type="text/javascript">
            $('#tabMenu .item').tab({history:false});
            $('table').addClass('ui table');
            $('ul, ol').addClass('ui list');
            $('h3, h4').addClass('ui header');

            $('table, img, audio, video').removeAttr('style').addClass('responsif');


            function jawabKuis(idData){
                var idMateriKuis, idKuis, jenis, idJawab, jawab, benar, teks;

                jenis = $('#jenis').val();
                if(jenis=='essay'){
                    teks = 'isi';
                }
                else if(jenis=='mchoice'){
                    teks = 'pilih';
                    $('#jawab').val(idData);
                }

                idMateriKuis = $('#idMateriKuis').val();
                idKuis = $('#idKuis').val();
                idJawab = $('#idJawab').val();
                jawab = $('#jawab').val();
                benar = $('#benar').val();

                if(jawab==''){
                    tampilkanPesan('0','Silahkan '+teks+' jawaban anda.');
                    loadingSelesai();
                }
                else if(benar=='1'){
                    tampilkanPesan('0','Kuis sudah terjawab.');
                    loadingSelesai();
                }
                else if(benar=='0'){
                    if(jenis=='mchoice'){
                        $('.btnOpsi').removeClass('orange');
                        $('.btnOpsi').addClass('loading');
                    }
                    else{
                        $('#btnJawabKuis').addClass('loading');
                    }

                    $.ajax({
                        type:"post",
                        async:true,
                        url:"answer-quiz.php",
                        data:{
                            'view':'1',
                            'idMateriKuis': idMateriKuis,
                            'idKuis': idKuis,
                            'idJawab': idJawab,
                            'jawab': jawab
                        },
                        success:function(data){
                            $("#feedBack").html(data);
                            loadingSelesai();
                        }
                    })
                }
            }
        </script>
        
    </body>
</html>