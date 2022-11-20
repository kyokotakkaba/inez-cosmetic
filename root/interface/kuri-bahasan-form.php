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

    if($idData=='0'){
        $id_kelompok = '';
        $nama = '';
        $deskripsi = '';
        $sub = 'Tambah';
    }
    else{
        $q = "
                SELECT 
                    id_kelompok, 
                    nama, 
                    deskripsi
                    
                FROM 
                    materi_kelompok_bahasan 

                WHERE
                    id = '$idData'

                LIMIT
                    1
        ";
        $e = mysqli_query($conn, $q);
        $c = mysqli_num_rows($e);
        if($c=='0'){
            echo "DATA NOT FOUND";
            exit();
        }

        $r = mysqli_fetch_assoc($e);
        $id_kelompok = $r['id_kelompok'];
        $nama = $r['nama'];
        $deskripsi = $r['deskripsi'];

        $sub = 'Edit';
    }

    $sub .= ' Data Bahasan Materi';
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
<form id="frmBahasan">
    <input type="hidden" name="id" value="<?php echo $idData; ?>">

    <div class="field">
        <label>Kelompok materi</label>
        <select id="id_kelompok" name="id_kelompok" class="ui dropdown">
<?php
    $q = "
            SELECT 
                id, 
                nama, 
                deskripsi
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
            <option value="">Kosong</option>
<?php        
    }
    else{
?>
            <option value="">Pilih</option>
<?php        
        while ($r = mysqli_fetch_assoc($e)) {
            $idK = $r['id'];
            $namaK = $r['nama'];
            $deskK = $r['deskripsi'];
?>
            <option value="<?php echo $idK; ?>" <?php if($idK==$id_kelompok){ ?> selected="selected" <?php } ?> >
                <?php echo $namaK; ?>
            </option>
<?php            
        }
    }
?>            
        </select>
    </div>

    <div class="field">
        <label>Nama bahasan</label>
        <div class="ui input">
            <input type="text" maxlength="64" id="nama" name="nama" placeholder="Nama" value="<?php echo $nama; ?>">
        </div>
    </div>
    <div class="field">
        <label>Deskripsi</label>
        <input type="text" id="deskripsi" name="deskripsi" maxlength="128" placeholder="Deskripsi" value="<?php echo $deskripsi; ?>">
    </div>

    <div class="field">
        <button class="ui icon button blue" type="submit">
            <i class="save icon"></i> Simpan
        </button>
    </div>
</form>

<script type="text/javascript">
    $('.button').popup();
    $('.dropdown').dropdown();
    $('#frmBahasan').submit(function(e){
        <?php
        $judulNotif = 'Penambahan materi belajar baru';
        $isiNotif = 'Pihak pengelola menambahkan materi belajar baru.';
        $untukNotif = 'all';
        sendNotif($judulNotif, $isiNotif, $untukNotif);
        ?>
        e.preventDefault();
        loadingMulai();
        $.ajax({
            type:'post',
            async:true,
            data:$('#frmBahasan').serialize(),
            url:'interface/kuri-bahasan-form-process.php',
            success: function(data){
                $('#feedBack').html(data);
                loadingSelesai();
            }
        })
    })
</script>