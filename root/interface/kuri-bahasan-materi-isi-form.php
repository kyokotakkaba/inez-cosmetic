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

    $main = 'Materi';

    $idData = saring($_POST['idData']);
    $pecah = explode('[pisah]', $idData);

    $idB = $pecah[0];
    $id = $pecah[1];

    $q = "
            SELECT
                b.nama namaB,
                b.id_kelompok,
                k.nama namaK

            FROM
                materi_kelompok_bahasan b

            LEFT JOIN
                materi_kelompok k
            ON
                b.id_kelompok = k.id

            WHERE
                b.id = '$idB'

            LIMIT
                1
    ";
    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);
    if($c == '0'){
        echo "DATA NOT FOUND";
        exit();
    }
    $r = mysqli_fetch_assoc($e);
    $sub = $r['namaB'];
    $idK = $r['id_kelompok'];

    $ar = array();
    $r = array();

    if($id=='0'){
        $tingkat = '';
        $judul = '';
        $deskripsi = '';
        $baner = '../files/photo/agenda.png';
        $isi = '';
        $buku1 = '';
        $buku2 = '';
        $lampiran = '';

        $idKuis = '0';
        $pertanyaan = '';
        $jenis = 'mchoice';
        //fill blank mchoice
        for ($i=0; $i <=2 ; $i++) { 
            $r['idJawaban'] = $i;
            $r['jawaban']   = '';
            $r['benar']     = '0';

            $ar[]   = $r;
        }

        //fill blank essay
        $idJawabanEssay = '0';
        $jawaban = '';

        $subsub = 'Tambah';
    }
    else{
        $q = "
                SELECT 
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
                    lampiran

                FROM 
                    materi 

                WHERE
                    id = '$id'

                LIMIT
                    1
        ";
        $e = mysqli_query($conn, $q);
        $c = mysqli_num_rows($e);
        if($c == '0'){
            echo "DATA NOT FOUND";
            exit();
        }

        $r = mysqli_fetch_assoc($e);
        $tingkat = $r['id_tingkat_belajar'];
        $judul = $r['judul'];
        $deskripsi = $r['judul'];
        
        $banerUrl = $r['baner'];
        $baner = '../files/photo/agenda.png';
        if(!empty($banerUrl) && $banerUrl!==''){
            // $buku1Url == str_replace('%20', ' ', $buku1Url);
            $headers = @get_headers($banerUrl);
            $checkFile = strpos($headers[0],'200');
            if($checkFile){
                $baner = $banerUrl;
            }  
        }
        // if(!empty($banerUrl) && $banerUrl!==''){
        //     $banerUrl = str_replace('%20', ' ', $banerUrl);
        //     if(file_exists('../../'.$banerUrl)){
        //         $baner = '../'.$banerUrl;
        //     } 
        // }

        $isi = $r['isi'];
        $buku1Url = $r['buku1'];
        $buku1 = '';
        if(!empty($buku1Url) && $buku1Url!==''){
            // $buku1Url == str_replace('%20', ' ', $buku1Url);
            $headers = @get_headers($buku1Url);
            $checkFile = strpos($headers[0],'200');
            if($checkFile){
                $buku1 = $buku1Url;
            }  
        }

        $buku2Url = $r['buku2'];
        $buku2 = '';
        if(!empty($buku2Url) && $buku2Url!==''){
            // $buku2Url = str_replace('%20', ' ', $buku2Url);
            $headers = @get_headers($buku2Url);
            $checkFile = strpos($headers[0],'200');
            if($checkFile){
                $buku2 = $buku2Url;
            }
        }
        
        $lampiranUrl = $r['lampiran'];
        $lampiran = '';
        if(!empty($lampiranUrl) && $lampiranUrl!==''){
            // $lampiranUrl = str_replace('%20', ' ', $lampiranUrl);
            $headers = @get_headers($lampiranUrl);
            $checkFile = strpos($headers[0],'200');
            if($checkFile){
                $lampiran = $lampiranUrl;
            }
        }








        //get the quiz
        $q = "
                SELECT 
                    id,
                    jenis, 
                    pertanyaan

                FROM 
                    materi_kuis 

                WHERE
                    id_materi = '$id'

                AND
                    hapus = '0'
                AND
                    aktif = '1'
                LIMIT
                    1
        ";
        $e = mysqli_query($conn, $q);
        $c = mysqli_num_rows($e);
        if($c == '0'){
            echo "DATA NOT FOUND";
            exit();
        }

        $r = mysqli_fetch_assoc($e);
        $idKuis = $r['id'];
        $pertanyaan = $r['pertanyaan'];
        $jenis = $r['jenis'];
        if($jenis!=='mchoice' && $jenis!=='essay'){
            echo "QUIZ TYPE NOT VALID";
            exit();
        }



        $q = "
                SELECT 
                    id, 
                    jawaban, 
                    benar

                FROM 
                    materi_kuis_jawaban 

                WHERE
                    id_kuis = '$idKuis'
                AND
                    jenis = '$jenis'
                AND
                    hapus = '0'
                    
                ORDER BY
                    id ASC
        ";
        $e = mysqli_query($conn, $q);
        $c = mysqli_num_rows($e);
        if($c == '0'){
            echo "DATA NOT FOUND";
            exit();
        }

        if($jenis=='mchoice'){
            //fill current mchoice active
            while ($d=mysqli_fetch_assoc($e)) {
                $r['idJawaban'] = $d['id'];
                $r['jawaban']   = $d['jawaban'];
                $r['benar']     = $d['benar'];

                $ar[]   = $r;    
            }

            //fill blank essay
            $idJawabanEssay = '0';
            $jawaban = '';
        }
        else{
            //fill blank mchoice
            for ($i=0; $i <=2 ; $i++) { 
                $r['idJawaban'] = $i;
                $r['jawaban']   = '';
                $r['benar']     = '0';

                $ar[]   = $r;
            }

            //fill current essay active
            $r = mysqli_fetch_assoc($e);
            $idJawaban = $r['id'];
            $jawaban = $r['jawaban'];
            $benar = $r['benar'];
        }

        $subsub = 'Edit';
    }

    $subsub .= ' Materi';
?>
<div class="ui message">
    <div class="ui breadcrumb">
        <div class="section"><?php echo $main; ?></div>
        <i class="right angle icon divider"></i>
        <div class="section"><?php echo $sub; ?></div>
        <i class="right angle icon divider"></i>
        <div class="active section"><?php echo $subsub; ?></div>
    </div>
</div>

<div class="ui basic vertical segment clearing" style="margin: 0px; padding: 0px;">
    <div class="ui icon button right floated" data-content="Tutup" onclick="backFromSub()">
        <i class="close icon"></i>
    </div>
</div>

<form id="frmMateri">
    <input type="hidden" id="idKelompok" name="idKelompok" value="<?php echo $idK; ?>">
    <input type="hidden" id="idBahasan" name="idBahasan" value="<?php echo $idB; ?>">
    <input type="hidden" id="idMateri" name="idMateri" value="<?php echo $id; ?>">

    <div class="ui orange segment">
        <div class="field">
                <label>Tingkat belajar</label>
                <select id="tingkat" name="tingkat" class="ui dropdown">
<?php
    $qT = "
            SELECT
                id,
                nama
            FROM
                tingkat_belajar
            WHERE
                hapus = '0'
            ORDER BY
                no ASC
    ";
    $eT = mysqli_query($conn, $qT);
    $cT = mysqli_num_rows($eT);

    if($cT=='0'){
?>
                    <option value="">Tingkat belajar kosong</option>
<?php        
    }
    else{
?>
                    <option value="">Pilih</option>
                    <option value="semua" <?php if($tingkat=='semua'){ ?> selected="selected" <?php } ?> >Semua</option>
<?php        
        while ($rT = mysqli_fetch_assoc($eT)) {
            $idTingkat = $rT['id'];
            $namaTingkat = $rT['nama'];
?>
                    <option value="<?php echo $idTingkat; ?>" <?php if($tingkat==$idTingkat){ ?> selected="selected" <?php } ?> >
                        <?php echo $namaTingkat; ?>
                    </option>
<?php            
        }
    }
?>                    
                </select>
            </div>
        </div>
    </div>

    <div class="ui teal segment">
        <div class="ui horizontal divider">
            <i class="book icon"></i> Materi
        </div>
        <div class="field">
            <label>Judul</label>
            <div class="ui input">
                <input type="text" maxlength="64" id="judul" name="judul" placeholder="Judul" value="<?php echo $judul; ?>">
            </div>
        </div>
        <div class="field">
            <label>Deskripsi</label>
            <input type="text" id="deskripsi" name="deskripsi" maxlength="128" placeholder="Deskripsi" value="<?php echo $deskripsi; ?>">
        </div>

        <div class="fields">
            <div class="six wide field">
                <label>Baner <i>preview</i></label>
                <img id="prevBaner" src="<?php echo $baner; ?>" class="ui image small">
            </div>
            <div class="ten wide field">
                <label>Baner</label>
                <div class="ui action input">
                    <input type="text" id="baner" name="baner" readonly="readonly" placeholder="Pilih file" value="<?php echo $baner; ?>" onchange="gantiBaner()">
                    <a id="pilihBaner" class="ui icon button" type="button" href="../filemanager/dialog.php?type=1&field_id=baner">
                        <i class="open folder icon"></i>
                    </a>
                </div>
                <p style="font-size: 9pt; color: #60646D; margin-top: 10px;">
                    *Merupakan file gambar yang mewakili materi.<br>
                    *Nama file <strong>tidak boleh ada spasi</strong>. Ganti tanda spasi pada nama file (jika ada) dengan tanda penghubung (-) melalui popup <i>file manager selecctor</i><br>
                </p>
            </div>
        </div>

        <div class="field">
            <label>Isi materi</label>
            <textarea id="isi" name="isi" ><?php echo html_entity_decode($isi); ?></textarea>
        </div>


        <div class="ui hidden divider"></div>
        <div class="ui horizontal divider">
            <i class="file pdf icon"></i> Lampiran
        </div>
        <?php 
            if($_SESSION['idPengguna']=="X-001X"){
            ?>
            <div class="two fields">
                <div class="field">
                    <label>Materi 1</label>
                    <div class="ui action fluid input">
                        <input type="text" id="buku1" name="buku1" readonly="readonly" placeholder="Pilih file" value="<?php echo $buku1; ?>">
                        <a id="pilihBuku1" class="ui icon button" type="button" href="../filemanager/dialog.php?extension=['pdf']&field_id=buku1&relative_url=0&akey=key&fldr=Departemen%20A%2F&6388b56b46cfd">
                            <i class="open folder icon"></i>
                        </a>
                    </div>
                </div>
                <div class="field">
                    <label>Materi 2</label>
                    <div class="ui action fluid input">
                        <input type="text" id="buku2" name="buku2" readonly="readonly" placeholder="Pilih file" value="<?php echo $buku2; ?>">
                        <a id="pilihBuku2" class="ui icon button" type="button" href="../filemanager/dialog.php?extension=['pdf']&field_id=buku2&relative_url=0&akey=key&fldr=Departemen%20A%2F&6388b56b46cfd">
                            <i class="open folder icon"></i>
                        </a>
                    </div>
                </div>    
            </div>
            <div class="field">
                <label>Lampiran</label>
                <div class="ui action fluid input">
                    <input type="text" id="lampiran" name="lampiran" readonly="readonly" placeholder="Pilih file" value="<?php echo $lampiran; ?>">
                    <a id="pilihLampiran" class="ui icon button" type="button" href="../filemanager/dialog.php?extension=['zip']&field_id=lampiran&relative_url=0&akey=key&fldr=Departemen%20A%2F&6388b56b46cfd">
                        <i class="open folder icon"></i>
                    </a>
                </div>
            </div>
            <?php
            }if($_SESSION['idPengguna']=="X-002X"){?>
            <div class="two fields">
                <div class="field">
                    <label>Materi 1</label>
                    <div class="ui action fluid input">
                        <input type="text" id="buku1" name="buku1" readonly="readonly" placeholder="Pilih file" value="<?php echo $buku1; ?>">
                        <a id="pilihBuku1" class="ui icon button" type="button" href="../filemanager/dialog.php?extension=['pdf']&field_id=buku1&relative_url=0&akey=key&fldr=Departemen%20B%2F&6388b56b46cfd">
                            <i class="open folder icon"></i>
                        </a>
                    </div>
                </div>
                <div class="field">
                    <label>Materi 2</label>
                    <div class="ui action fluid input">
                        <input type="text" id="buku2" name="buku2" readonly="readonly" placeholder="Pilih file" value="<?php echo $buku2; ?>">
                        <a id="pilihBuku2" class="ui icon button" type="button" href="../filemanager/dialog.php?extension=['pdf']&field_id=buku2&relative_url=0&akey=key&fldr=Departemen%20B%2F&6388b56b46cfd">
                            <i class="open folder icon"></i>
                        </a>
                    </div>
                </div>    
            </div>
            <div class="field">
                <label>Lampiran</label>
                <div class="ui action fluid input">
                    <input type="text" id="lampiran" name="lampiran" readonly="readonly" placeholder="Pilih file" value="<?php echo $lampiran; ?>">
                    <a id="pilihLampiran" class="ui icon button" type="button" href="../filemanager/dialog.php?extension=['zip']&field_id=lampiran&relative_url=0&akey=key&fldr=Departemen%20B%2F&6388b56b46cfd">
                        <i class="open folder icon"></i>
                    </a>
                </div>
            </div>
            <?php}($_SESSION['idPengguna']!="X-001X"&&$_SESSION['idPengguna']!="X-002X"){?>
        <div class="two fields">
            <div class="field">
                <label>Materi 1</label>
                <div class="ui action fluid input">
                    <input type="text" id="buku1" name="buku1" readonly="readonly" placeholder="Pilih file" value="<?php echo $buku1; ?>">
                    <a id="pilihBuku1" class="ui icon button" type="button" href="../filemanager/dialog.php?extension=['pdf']&field_id=buku1">
                        <i class="open folder icon"></i>
                    </a>
                </div>
            </div>
            <div class="field">
                <label>Materi 2</label>
                <div class="ui action fluid input">
                    <input type="text" id="buku2" name="buku2" readonly="readonly" placeholder="Pilih file" value="<?php echo $buku2; ?>">
                    <a id="pilihBuku2" class="ui icon button" type="button" href="../filemanager/dialog.php?extension=['pdf']&field_id=buku2">
                        <i class="open folder icon"></i>
                    </a>
                </div>
            </div>    
        </div>

        <div class="field">
            <label>Lampiran</label>
            <div class="ui action fluid input">
                <input type="text" id="lampiran" name="lampiran" readonly="readonly" placeholder="Pilih file" value="<?php echo $lampiran; ?>">
                <a id="pilihLampiran" class="ui icon button" type="button" href="../filemanager/dialog.php?extension=['zip']&field_id=lampiran">
                    <i class="open folder icon"></i>
                </a>
            </div>
        </div>    
        <?php}?>
    </div>

    <div class="ui green segment">
        <div class="ui horizontal divider">
            <i class="cubes icon"></i> Kuis
        </div>

        <input type="hidden" id="idKuis" name="idKuis" value="<?php echo $idKuis; ?>">
        
        <div class="field">
            <label>Pertanyaan</label>
            <textarea id="pertanyaan" name="pertanyaan"><?php echo html_entity_decode($pertanyaan); ?></textarea>
        </div>
        <div class="field">
            <label>Jenis kuis</label>
            <select id="jenis" name="jenis" class="ui dropdown" onchange="gantiJenis()">
                <option value="">Pilih</option>
                <option value="mchoice" <?php if($jenis=='mchoice'){ ?> selected="selected" <?php } ?> >Pilihan ganda</option>
                <option value="essay" <?php if($jenis=='essay'){ ?> selected="selected" <?php } ?> >Essay</option>
            </select>
        </div>
        <div class="field mchoice" <?php if($jenis=='essay'){ ?> style="display: none;" <?php } ?> >
            <label>Jawaban</label>
            <table class="ui very basic collapsing celled table unstackable">
                <tbody>
<?php
        for ($i=0; $i <=2 ; $i++) {
            $idJawaban = $ar[$i]['idJawaban'];
            $jwb = $ar[$i]['jawaban'];
            $benar = $ar[$i]['benar'];

            if($benar=='1'){
                $ikonBtn = 'check';
                $clasBtn = 'green';
            }
            else{
                $clasBtn = '';
                $ikonBtn = 'ban';
            }
?>
                    <tr>
                        <td>
                            <input type="text" id="jwb<?php echo $idJawaban; ?>" name="jwb<?php echo $idJawaban; ?>" value="<?php echo $jwb; ?>" maxlength="64" placeholder="Jawaban" >
                        </td>
                        <td width="4%">
                            <input type="hidden" class="benarMchoice" id="benar<?php echo $idJawaban; ?>" name="benar<?php echo $idJawaban; ?>" value="<?php echo $benar; ?>">

                            <div id="btn<?php echo $idJawaban; ?>" class="ui icon button <?php echo $clasBtn; ?>" onclick="setBenar('<?php echo $idJawaban; ?>')">
                                <i class="<?php echo $ikonBtn; ?> icon"></i>
                            </div>
                        </td>
                    </tr>
<?php            
        }            
?>      
                </tbody>
            </table>
        </div>
        <div class="field essay" <?php if($jenis=='mchoice'){ ?> style="display: none;" <?php } ?> >
            <input type="hidden" name="idJawabanEssay" value="<?php echo $idJawabanEssay; ?>">
            <label>Jawaban</label>
            <input type="text" id="jawaban" name="jawaban" maxlength="999" placeholder="Jawaban" value="<?php echo $jawaban; ?>" >
        </div>
    </div>




    <div class="field">
        <button class="ui icon button blue" type="submit">
            <i class="save icon"></i> Simpan
        </button>
    </div>
</form>

<script type="text/javascript">
    $('.dropdown').dropdown();
    $('.button').popup();

    var editorIsiMateri = CKEDITOR.replace('isi',{
        height : 320,
        filebrowserBrowseUrl : '../filemanager/dialog.php?type=2&editor=ckeditor&fldr=',
        filebrowserUploadUrl : '../filemanager/dialog.php?type=2&editor=ckeditor&fldr=',
        filebrowserImageBrowseUrl : '../filemanager/dialog.php?type=1&editor=ckeditor&fldr='
     });

    var editorPertanyaan = CKEDITOR.replace('pertanyaan',{
        height : 140,
        filebrowserBrowseUrl : '../filemanager/dialog.php?type=2&editor=ckeditor&fldr=',
        filebrowserUploadUrl : '../filemanager/dialog.php?type=2&editor=ckeditor&fldr=',
        filebrowserImageBrowseUrl : '../filemanager/dialog.php?type=1&editor=ckeditor&fldr='
     });

    $('#pilihBaner, #pilihBuku1, #pilihBuku2, #pilihLampiran').fancybox({
        'width'     : '100%',
        'height'    : '100%',
        'type'      : 'iframe',
        'fitToView' : false,
        'autoSize'  : false
    });     



    function gantiBaner(){
        var alamat = $('#baner').val();
        if(alamat!==''){
            $('#prevBaner').attr('src',alamat);
        }
    }




    function gantiJenis(){
        var jenis, mchoiceVis, essayVis;
        jenis = $('#jenis').val();
        mchoiceVis = $('.mchoice').is(':visible');
        essayVis = $('.essay').is(':visible');

        if(jenis=='mchoice'){
            if(mchoiceVis==false){
                $('.mchoice').transition('fade');
            }
            if(essayVis==true){
                $('.essay').transition('fade');
            }
        }
        else{
            if(mchoiceVis==true){
                $('.mchoice').transition('fade');
            }
            if(essayVis==false){
                $('.essay').transition('fade');
            }   
        }
    }



    function setBenar(idX){
        $('.mchoice .button').addClass('loading');
        $('.mchoice .button').removeClass('green');
        $('.mchoice .button').html('<i class="ban icon"></i>');
        $('.mchoice #btn'+idX).html('<i class="check icon"></i>');
        $('.mchoice #btn'+idX).addClass('green');
        $('.benarMchoice').val('0');
        $('#benar'+idX).val('1');
        setTimeout(function(){
            $('.mchoice .button').removeClass('loading');
        }, 600);
    }


    $('#frmMateri').submit(function(e){
        var idKelompok, idBahasan, idMateri, tingkat, judul, deskripsi, baner, isi, buku1, buku2, lampiran, idKuis, pertanyaan, jenis, jwb0, jwb1, jwb2, benar0, benar1, benar2, jawaban;
        
        idKelompok = $('#idKelompok').val();
        idBahasan = $('#idBahasan').val();
        idMateri = $('#idMateri').val();

        tingkat = $('#tingkat').val();

        judul  = $('#judul').val();
        deskripsi  = $('#deskripsi').val();
        baner  = $('#baner').val();
        isi = CKEDITOR.instances.isi.getData();
        buku1 = $('#buku1').val();
        buku2 = $('#buku2').val();
        lampiran = $('#lampiran').val();

        idKuis = $('#idKuis').val();
        pertanyaan = CKEDITOR.instances.pertanyaan.getData();
        jenis = $('#jenis').val();

<?php
    for ($i=0; $i <=2 ; $i++) {
        $idJawaban = $ar[$i]['idJawaban'];
?>
        jwb<?php echo $i; ?> = $('#jwb<?php echo $idJawaban; ?>').val();
        benar<?php echo $i; ?> = $('#benar<?php echo $idJawaban; ?>').val();
<?php        
    }
?>        
        jawaban = $('#jawaban').val();
        
        e.preventDefault();
        loadingMulai();

        if(tingkat==''){
            tampilkanPesan('0','Pilih tingkat belajar.');
            loadingSelesai();
        }
        else if(judul==''||deskripsi==''||baner==''||isi==''){
            tampilkanPesan('0','Lengkapi form.');
            loadingSelesai();
        }
        else if(pertanyaan==''){
            tampilkanPesan('0','Isi pertanyaan kuis.');
            loadingSelesai();    
        }
        else if(jenis=='mchoice'){
            if(jwb0==''||jwb1==''||jwb2==''){
                tampilkanPesan('0','Sesuaikan jawaban pilihan ganda.');
                loadingSelesai();
            }
            else if(benar0=='0'&&benar1=='0'&&benar2=='0'){
                tampilkanPesan('0','Tentukan jawaban pilihan ganda yang benar.');
                loadingSelesai();
            }
            else{
                $.ajax({
                    type:"post",
                    async:true,
                    url:"interface/kuri-bahasan-materi-isi-form-process.php",
                    data:{
                        'view':'1',
                        'idKelompok': idKelompok,
                        'idBahasan': idBahasan,
                        'idTingkat': tingkat,
                        'idMateri': idMateri,
                        'judul': judul, 
                        'deskripsi': deskripsi,
                        'baner': baner,
                        'isi': isi, 
                        'buku1': buku1, 
                        'buku2': buku2, 
                        'lampiran': lampiran, 
                        'idKuis': idKuis, 
                        'pertanyaan': pertanyaan, 
                        'jenis': jenis, 
                        'jwb0': jwb0, 
                        'jwb1': jwb1, 
                        'jwb2': jwb2, 
                        'benar0': benar0, 
                        'benar1': benar1, 
                        'benar2': benar2, 
                        'jawaban': jawaban
                    },
                    success:function(data){
                        $("#feedBack").html(data);
                        loadingSelesai();
                    }
                })
            }
        }
        else if(jenis=='essay' && jawaban==''){
            tampilkanPesan('0','Silahkan isi jawaban kuis.');
            loadingSelesai();   
        }
        else{
            $.ajax({
                type:"post",
                async:true,
                url:"interface/kuri-bahasan-materi-isi-form-process.php",
                data:{
                    'view':'1',
                    'idKelompok': idKelompok,
                    'idBahasan': idBahasan,
                    'idTingkat': tingkat,
                    'idMateri': idMateri,
                    'judul': judul, 
                    'deskripsi': deskripsi,
                    'baner': baner,
                    'isi': isi, 
                    'buku1': buku1, 
                    'buku2': buku2, 
                    'lampiran': lampiran, 
                    'idKuis': idKuis, 
                    'pertanyaan': pertanyaan, 
                    'jenis': jenis, 
                    'jwb0': jwb0, 
                    'jwb1': jwb1, 
                    'jwb2': jwb2, 
                    'benar0': benar0, 
                    'benar1': benar1, 
                    'benar2': benar2, 
                    'jawaban': jawaban
                },
                success:function(data){
                    $("#feedBack").html(data);
                    loadingSelesai();
                }
            })
        }
    })


    
</script>