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

    $main = 'Bank Soal';

    $id = saring($_POST['idData']);

    $arJ = array();
    $rJ = array();

    if($id=='0'){
        $id_kelompok = '';
        $id_bahasan = '';
        $id_materi = '';
        $isi = '';

        //fill blank mchoice
        for ($i=0; $i <=2 ; $i++) {
            $rJ['idJawaban'] = $i;
            $rJ['jawaban']   = '';
            $rJ['benar']     = '0';

            $arJ[]   = $rJ;
        }

        $sub = 'Tambah';
    }
    else{
        $q = "
                SELECT 
                    id_kelompok, 
                    id_bahasan, 
                    id_materi, 
                    isi
                FROM 
                    pertanyaan 
                WHERE
                    id = '$id'
        ";
        $e = mysqli_query($conn, $q);
        $c = mysqli_num_rows($e);
        if($c == '0'){
            echo "DATA NOT FOUND";
            exit();
        }

        $r = mysqli_fetch_assoc($e);
        $id_kelompok = $r['id_kelompok'];
        $id_bahasan = $r['id_bahasan'];
        $id_materi = $r['id_materi'];
        $isi = $r['isi'];



        $q = "
                SELECT 
                    id, 
                    isi, 
                    benar

                FROM 
                    jawaban 

                WHERE
                    id_pertanyaan = '$id'
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

         while ($dJ = mysqli_fetch_assoc($e)) {
            $rJ['idJawaban'] = $dJ['id'];
            $rJ['jawaban']   = $dJ['isi'];
            $rJ['benar']     = $dJ['benar'];

            $arJ[]   = $rJ;
        }

        $sub = 'Edit';
    }

    $sub .= ' Pertanyaan';
?>
<div class="ui message">
    <div class="ui breadcrumb">
        <div class="section"><?php echo $main; ?></div>
        <i class="right angle icon divider"></i>
        <div class="active section"><?php echo $sub; ?></div>
    </div>
</div>
<form id="frmQuestion">
    <input type="hidden" id="id_pertanyaan" name="id_pertanyaan" value="<?php echo $id; ?>">

    <div class="field">
        <div class="ui icon button" onclick="backToMain()">
            <i class="left chevron icon"></i> Kembali
        </div>    
    </div>

    <div class="field">
        <label>Kelompok</label>
        <select id="id_kelompok" name="id_kelompok" class="ui dropdown">
<?php
$q = "
        SELECT 
            id, 
            nama,
            n_pk

        FROM 
            materi_kelompok

        WHERE
            hapus = '0'

        ORDER BY
            nama ASC
";
$e = mysqli_query($conn, $q);
$c = mysqli_num_rows($e);
if($c=='0'){
?>
    <option value="">Belum ada data</option>
<?php
}
else{
    $ar = array();
    $r = array();

    while ($d = mysqli_fetch_assoc($e)) {
        $r['id']    = $d['id'];
        $r['nama']    = $d['nama'];
        $r['n_pk']    = $d['n_pk'];
        
        $ar[] = $r;
    }

    $jar = $c-1;
?>
    <option value="">Pilih</option>
<?php  
    for ($i=0; $i <= $jar; $i++) {
        $idKi = $ar[$i]['id'];
        $kel = $ar[$i]['nama'];
        $n_pk = $ar[$i]['n_pk'];
        $dis = $kel;
        if($n_pk=='1'){
            $dis .= " (PK)";
        }
?>
        <option value="<?php echo $idKi; ?>" <?php if($idKi==$id_kelompok){ ?> selected="selected" <?php } ?> ><?php echo $dis; ?></option>
<?php                  
    }
}
?>       
        </select>
    </div>
    <div class="field">
        <label>Pertanyaan</label>
        <textarea id="isi" name="isi" ><?php echo html_entity_decode($isi); ?></textarea>
    </div>
    <div class="field mchoice">
        <label>Jawaban</label>
        <table class="ui very basic collapsing celled table unstackable">
            <tbody>
<?php
    for ($i=0; $i<=2; $i++) {
        $idJawaban = $arJ[$i]['idJawaban'];
        $jwb = $arJ[$i]['jawaban'];
        $benar = $arJ[$i]['benar'];

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
                        <input type="text" id="jwb<?php echo $idJawaban; ?>" name="jwb<?php echo $idJawaban; ?>" value="<?php echo $jwb; ?>" maxlength="3000" placeholder="Jawaban" >
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

    <div class="field">
        <button class="ui icon button blue" type="submit">
            <i class="save icon"></i> Simpan
        </button>
    </div>
    
</form>



<script type="text/javascript">
    $('.dropdown').dropdown();
    $('.button').popup();

    var editorPertanyaan = CKEDITOR.replace('isi',{
        height: 140,
        filebrowserBrowseUrl : '../filemanager/dialog.php?type=2&editor=ckeditor&fldr=',
        filebrowserUploadUrl : '../filemanager/dialog.php?type=2&editor=ckeditor&fldr=',
        filebrowserImageBrowseUrl : '../filemanager/dialog.php?type=1&editor=ckeditor&fldr='
     });

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


    $('#frmQuestion').submit(function(e){
        var idKelompok, idPertanyaan, isi, jwb0, jwb1, jwb2, benar0, benar1, benar2;
        
        idKelompok = $('#id_kelompok').val();
        idPertanyaan = $('#id_pertanyaan').val();
        isi = CKEDITOR.instances.isi.getData();

<?php
    for ($i=0; $i <= 2; $i++) {
        $idJawaban = $arJ[$i]['idJawaban'];
?>
        jwb<?php echo $i; ?> = $('#jwb<?php echo $idJawaban; ?>').val();
        benar<?php echo $i; ?> = $('#benar<?php echo $idJawaban; ?>').val();
<?php        
    }
?>      
        e.preventDefault();
        loadingMulai();
        if(idKelompok==''){
            tampilkanPesan('0','Pilih kelompok materi.');
            loadingSelesai();
        }
        else if(isi==''){
            tampilkanPesan('0','Atur pertanyaan.');
            loadingSelesai();
        }
        else if(jwb0==''||jwb1==''||jwb2==''){
            tampilkanPesan('0','Sesuaikan pilihan jawaban.');
            loadingSelesai();
        }
        else if(benar0=='0'&&benar1=='0'&&benar2=='0'){
            tampilkanPesan('0','Tentukan pilihan jawaban yang benar.');
            loadingSelesai();
        }
        else{
            $.ajax({
                type:"post",
                async:true,
                url:"interface/bank-form-process.php",
                data:{
                    'view':'1',
                    'idKelompok': idKelompok,
                    'id':idPertanyaan,
                    'isi': isi, 
                    'jwb0': jwb0, 
                    'jwb1': jwb1, 
                    'jwb2': jwb2, 
                    'benar0': benar0, 
                    'benar1': benar1, 
                    'benar2': benar2
                },
                success:function(data){
                    $("#feedBack").html(data);
                    loadingSelesai();
                }
            })
        }
    })

</script>