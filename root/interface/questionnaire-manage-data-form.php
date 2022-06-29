
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
    $main = 'Survey';

    $idMentah = saring($_POST['idData']);

    $pecah = explode('[pisah]', $idMentah);
    $id_angket = $pecah[0];
    $q = "
            SELECT 
                a.judul, 
                a.deskripsi, 
                a.responden,
                a.kode,

                k.nama jenis, 
                k.deskripsi deskJenis,

                p.nama, 
                p.deskripsi deskProduk, 
                p.gambar

            FROM 
                angket a

            LEFT JOIN
                angket_kategori k
            ON
                a.id_kategori = k.id

            LEFT JOIN
                produk p
            ON
                a.id_produk = p.id
                
            WHERE
                a.id = '$id_angket'

            LIMIT
                1
    ";
    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);
    if($c == ''){
        echo "DATA NOT FOUND";
        exit();
    }

    $r = mysqli_fetch_assoc($e);
    $sub = $r['judul']; 




    $idData = $pecah[1];

    if($idData=='0'){
        $deskripsi = '';
        $id_label = '';
        $subsub = 'Tambah';
    }
    else{
        $q = "
                SELECT 
                    deskripsi,
                    id_label

                FROM 
                    angket_item 

                WHERE
                    id = '$idData'

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
        $deskripsi = $r['deskripsi'];
        $id_label = $r['id_label'];

        $subsub = 'Edit';
    }

    $subsub .= ' Item Pertanyaan/ Pernyataan';
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
<div class="field">
    <div class="ui icon button" onclick="backFromSub()">
        <i class="left chevron icon"></i> Kembali
    </div>    
</div>
    
<form id="frmItemAngket">
    <input type="hidden" name="view" value="1">
    <input type="hidden" name="id" value="<?php echo $idData; ?>">

    <div class="field">
        <label>Pertanyaan/ Pernyataan</label>
        <textarea id="deskripsi" name="deskripsi" placeholder="Deskripsi"><?php echo html_entity_decode($deskripsi) ?></textarea>
    </div>
    <div class="field">
        <label>Jenis Label</label>
        <select id="id_label" name="id_label" class="ui search dropdown">
<?php
    $q = "
            SELECT
                id,
                nama,
                satu,
                dua,
                tiga,
                empat

            FROM
                angket_label

            WHERE
                hapus = '0'
    ";
    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);

    if($c=='0'){
?>
            <option value="">Kosong</option>
<?php        
    }
    else{
?>
            <option value="">Pilih</option>
<?php        
        while ($r = mysqli_fetch_assoc($e)) {
            $idL = $r['id'];
            $nama = $r['nama'];
            $satu = $r['satu'];
            $dua = $r['dua'];
            $tiga = $r['tiga'];
            $empat = $r['empat'];
?>
            <option value="<?php echo $idL; ?>" <?php if($idL==$id_label){ ?> selected="selected" <?php } ?> >
                <?php echo $nama.' ('.$satu.' - '.$dua.' - '.$tiga.' - '.$empat.')'; ?>
            </option>
<?php            
        }
    }
?>            
        </select>
    </div>

    <div class="field">
        <button class="ui icon button blue" type="submit">
            <i class="save icon"></i> Simpan
        </button>
    </div>
    
</form>



<script type="text/javascript">
    $('.dropdown').dropdown({ fullTextSearch: "exact" });

    var editorIsiItem = CKEDITOR.replace('deskripsi',{
        height: 200,
        filebrowserBrowseUrl : '../filemanager/dialog.php?type=2&editor=ckeditor&fldr=',
        filebrowserUploadUrl : '../filemanager/dialog.php?type=2&editor=ckeditor&fldr=',
        filebrowserImageBrowseUrl : '../filemanager/dialog.php?type=1&editor=ckeditor&fldr='
     });

    $('#frmItemAngket').submit(function(e){
        var deskripsi, id_label;

        deskripsi = CKEDITOR.instances.deskripsi.getData();
        id_label = $('#id_label').val();
        
        e.preventDefault();
        loadingMulai();
        if(deskripsi==''){
            tampilkanPesan('0','Isi item angket.');
            loadingSelesai();
        }
        else if(id_label==''){
            tampilkanPesan('0','Pilih jenis label.');
            loadingSelesai();
        }
        else{
            $.ajax({
                type:"post",
                async:true,
                url:"interface/questionnaire-manage-data-form-process.php",
                data:{
                    'view':'1',
                    'id': '<?php echo $idData; ?>',
                    'id_angket': '<?php echo $id_angket; ?>',
                    'deskripsi':deskripsi,
                    'id_label': id_label
                },
                success:function(data){
                    $("#feedBack").html(data);
                    loadingSelesai();
                }
            })
        }
    })

</script>