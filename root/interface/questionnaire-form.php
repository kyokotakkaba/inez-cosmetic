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

    $idData = saring($_POST['idData']);

    if($idData=='0'){
        $id_kategori = '';
        $id_produk = '';
        $nama_produk = '';
        $judul = '';
        $deskripsi = '';
        $responden = '';
        $kode = '';

        $sub = 'Tambah';
    }
    else{
        $q = "
                SELECT 
                    a.id_kategori, 
                    a.id_produk, 
                    a.judul, 
                    a.deskripsi, 
                    a.responden,
                    a.kode,

                    p.nama nama_produk

                FROM 
                    angket a

                LEFT JOIN
                    produk p
                ON
                    a.id_produk = p.id

                WHERE
                    a.id = '$idData'

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
        $id_kategori = $r['id_kategori'];
        $id_produk = $r['id_produk'];
        $nama_produk = $r['nama_produk'];
        $judul = $r['judul'];
        $deskripsi = $r['deskripsi'];
        $responden = $r['responden'];
        $kode = $r['kode'];
        $sub = 'Sub';
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
<form id="frmAngket">
    <input type="hidden" name="view" value="1">
    <input type="hidden" name="id" value="<?php echo $idData; ?>">

    <div class="fields">
        <div class="ten wide field">
            <label>Jenis angket</label>
            <select id="id_kategori" name="id_kategori" class="ui fluid dropdown">
<?php
    $q = "
            SELECT 
                id, 
                nama
            FROM 
                angket_kategori 
            WHERE
                hapus = '0'
    ";
    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);
    if($c=='0'){
?>
                <option value="">Kategori Kosong</option>
<?php        
    }
    else{
?>
                <option value="">Pilih</option>
<?php       
        while ($r = mysqli_fetch_assoc($e)) {
            $idKat = $r['id'];
            $nama = $r['nama'];
?>
                <option value="<?php echo $idKat; ?>" <?php if($idKat==$id_kategori){ ?> selected="selected" <?php } ?> ><?php echo $nama; ?></option>
<?php            
        }
    }
?>            
            </select>
        </div>
        <div class="six wide field">
            <label>Responden</label>
            <select id="responden" name="responden" class="ui fluid dropdown">
                <option value="">Pilih</option>
                <option value="semua" <?php if($responden=='semua'){ ?> selected="selected" <?php } ?> >Semua</option>
                <option value="admin" <?php if($responden=='admin'){ ?> selected="selected" <?php } ?> >Supervisor</option>
                <option value="user" <?php if($responden=='user'){ ?> selected="selected" <?php } ?> >Karyawan</option>
            </select>
        </div>
    </div>



    <div class="field">
        <label>Target Produk</label>
        <select id="id_produk" name="id_produk" class="ui fluid search dropdown">
<?php
$q = "
        SELECT 
            id, 
            nama

        FROM 
            produk

        WHERE
            hapus = '0'
";
$e = mysqli_query($conn, $q);
$c = mysqli_num_rows($e);
if($c=='0'){
?>
            <option value="">Produk Kosong</option>
<?php        
}
else{
?>
            <option value="">Pilih</option>
<?php       
    while ($r = mysqli_fetch_assoc($e)) {
        $idKat = $r['id'];
        $nama = $r['nama'];
?>
            <option value="<?php echo $idKat; ?>" <?php if($idKat==$id_produk){ ?> selected="selected" <?php } ?> ><?php echo $nama; ?></option>
<?php            
    }
}
?>            
        </select>
    </div>

     <div class="fields">
        <div class="ten wide field">
            <label>Judul</label>
            <input type="text" id="judul" name="judul" placeholder="judul" maxlength="64" value="<?php echo $judul; ?>">
        </div>
    </div>
    
    <div class="field">
        <label>Deskripsi</label>
        <textarea rows="3" id="deskripsi" name="deskripsi" placeholder="Deskripsi" maxlength="128"><?php echo $deskripsi; ?></textarea>
    </div>

    <input type="hidden" name="kode" value="<?php echo $kode; ?>">

    <div class="field">
        <button class="ui icon button blue" type="submit">
            <i class="save icon"></i> Simpan
        </button>
    </div>
    
</form>



<script type="text/javascript">
    $('.button').popup();
    $('.dropdown').dropdown({ fullTextSearch: "exact" });

    $('#frmAngket').submit(function(e){
        var id_kategori, id_produk, judul, responden
        
        id_kategori = $('#id_kategori').val();
        id_produk = $('#id_produk').val();
        judul = $('#judul').val();
        responden = $('#responden').val();
        
        e.preventDefault();
        loadingMulai();
        
        if(id_kategori==''||id_produk==''||judul==''||responden==''){
            tampilkanPesan('0','Lengkapi form.');
            loadingSelesai();
        }
        else{
            $.ajax({
                type:"post",
                async:true,
                url:"interface/questionnaire-form-process.php",
                data:$('#frmAngket').serialize(),
                success:function(data){
                    $("#feedBack").html(data);
                    loadingSelesai();
                }
            })
        }
    })

</script>