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

    $main = 'Setting';

    $idData = saring($_POST['idData']);

    if($idData=='0'){
        $id_kelompok = '';
        $kode = '';
        $nama = '';
        $id_supervisi = '0';
        $id_karyawan = '';
        $nama_karyawan = '';
        $sub = 'Tambah ';
    }
    else{
        $q = "
            SELECT 
                w.id, 
                w.id_kelompok,
                w.nama nmWil, 
                w.kode,

                ws.id id_supervisi,

                k.id idKar,
                k.nik,
                k.nama nama_karyawan,
                k.jk,
                k.tmpt_lahir,
                k.tgl_lahir,
                k.foto
                
            FROM 
                wilayah w
            
            LEFT JOIN
                wilayah_supervisi ws
            ON
                w.id = ws.id_wilayah
            
            LEFT JOIN 
                karyawan k
            ON
                ws.id_karyawan = k.id

            WHERE
                w.id = '$idData'

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
            
        $id_kelompok = $r['id_kelompok'];
        $nama = $r['nmWil'];
        $kode = $r['kode'];
        $id_supervisi = $r['id_supervisi'];
        if($id_supervisi==''){
            $id_supervisi = '0';
        }
        $id_karyawan = $r['idKar'];
        $nama_karyawan = $r['nama_karyawan'];

        $sub = 'Edit ';
    }

    $sub .= 'Wilayah';
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
    
<form id="frmWilayah">
    <input type="hidden" name="view" value="1">
    <input type="hidden" name="id" value="<?php echo $idData; ?>">

    <div class="field">
        <label>Kelompok wilayah</label>
        <select id="id_kelompok" name="id_kelompok" class="ui dropdown">
<?php
    $q = "
            SELECT 
                id,
                nama, 
                deskripsi,
                standar

            FROM 
                wilayah_kelompok

            WHERE
                hapus = '0'
    ";
    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);

    if($c=='0'){
?>
            <option value="">Belum ada data</option>
<?php        
    }
    else{
        if($c>1){
?>
            <option value="">Pilih</option>
<?php
        }

        while ($r = mysqli_fetch_assoc($e)) {
            $idKel = $r['id'];
            $namaKel = $r['nama'];
            $standarKel = $r['standar'];
?>
            <option value="<?php echo $idKel; ?>" <?php if($id_kelompok==$idKel){ ?> selected="selected" <?php } ?> >
                <?php echo $namaKel.' (standar '.$standarKel.'%)'; ?>
            </option>
<?php            
        }
    }
?>            
        </select>
    </div>
<?php
    if($c==1){
?>
        <div class="field">
            <p style="font-size: 9pt; color: #60646D; margin-top: 10px;">
                Otomats terpilih karena kelompok wilayah hanya satu.
            </p>
        </div>
<?php
    }
?>    

    <div class="fields">
        <div class="four wide field">
            <label>Kode</label>
            <input type="text" id="kode" name="kode" placeholder="Kode" maxlength="8" value="<?php echo $kode; ?>">
        </div>
        <div class="eight wide field">
            <label>Nama</label>
            <input type="text" id="nama" name="nama" placeholder="Nama" maxlength="64" value="<?php echo $nama; ?>">
        </div>    
    </div>

    <div class="ui hidden divider"></div>
    <div class="ui horizontal divider">Supervisor</div>
<?php
    $q = "
            SELECT
                id
            FROM
                karyawan
            WHERE
                hapus = '0'
    ";
    $e = mysqli_query($conn, $q);
    $c =  mysqli_num_rows($e);
    if($c=='0'){
        $adaKaryawan = '0';
?>
        <div class="ui icon message">
            <i class="inbox icon"></i>
            <div class="content">
                <div class="header">
                    Belum ada data karyawan
                </div>
                <p>
                    Simpan form dan pilih supervisor nanti setelah data karyawan sudah ada.
                </p>
            </div>
        </div>
<?php        
    }
    else{
        $adaKaryawan = '1';
?>
        <div class="field">
            <label>Karyawan</label>
            <div class="ui search" onkeyup="cariAjax('calon supervisor[pisah]<?php echo $id_karyawan; ?>','id_karyawan', '../')">
                <input class="prompt" id="nama_karyawan" name="nama_karyawan" placeholder="Karyawan" type="text" value="<?php echo $nama_karyawan; ?>" >
                <div class="results"></div>
            </div>
        </div>
<?php        
    }
?>
    
    

    <div class="field">
        <button class="ui icon button blue" type="submit">
            <i class="save icon"></i> Simpan
        </button>
    </div>

    <input id="adaKaryawan" name="adaKaryawan" type="hidden" value="<?php echo $adaKaryawan; ?>">
    <input type="hidden" name="id_supervisi" value="<?php echo $id_supervisi; ?>">
    <input id="id_karyawan" name="id_karyawan" type="hidden" value="<?php echo $id_karyawan; ?>">
    
</form>



<script type="text/javascript">
    $('.dropdown').dropdown();

    $('#frmWilayah').submit(function(e){
        var id_kelompok, nama, kode, id_karyawan;
        
        id_kelompok = $('#id_kelompok').val();
        nama = $('#nama').val();
        kode = $('#kode').val();
        id_karyawan = $('#id_karyawan').val();
        
        e.preventDefault();
        loadingMulai();
        if(id_kelompok==''){
            tampilkanPesan('0','Kelompok wilayah tidak boleh kosong.');
            loadingSelesai();
        }
        else if(kode==''){
            tampilkanPesan('0','Kode wilayah tidak boleh kosong.');
            loadingSelesai();
        }
        else if(nama==''){
            tampilkanPesan('0','Nama wilayah tidak boleh kosong.');
            loadingSelesai();
        }
<?php 
    /*
    if($adaKaryawan=='1'){ 
?>      
        else if(id_karyawan==''){
            tampilkanPesan('0','Pilih Supervisor.');
            loadingSelesai();
        }
<?php 
    } 
    */
?>         
        else{
            $.ajax({
                type:"post",
                async:true,
                url:"interface/set-wilayah-form-process.php",
                data:$('#frmWilayah').serialize(),
                success:function(data){
                    $("#feedBack").html(data);
                    loadingSelesai();
                }
            })
        }
    })

</script>