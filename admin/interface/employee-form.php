<?php
    session_start();
    $appSection = 'admin';

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

    $idPengguna = $_SESSION['idPengguna'];

    $q = "
            SELECT 
                ws.id, 
                ws.id_wilayah, 
                
                w.kode, 
                w.nama

            FROM 
                wilayah_supervisi ws

            LEFT JOIN
                wilayah w
            ON
                ws.id_wilayah = w.id

            WHERE
                ws.hapus  = '0'
            AND
                ws.id_karyawan = '$idPengguna'
            AND
                w.hapus = '0'

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
    $id_wil = $r['id_wilayah'];
    $kode = $r['kode'];
    $nama = $r['nama'];

    $infoWil = $kode.' - '.$nama;

    $main = 'Data BA/ BC';

    $id = saring($_POST['idData']);

    if($id=='0'){
        $nama_wil = '';
        $kode = '';
        $nik = ''; 
        $nama = ''; 
        $jk = 'n'; 
        $tmpt_lahir = ''; 
        $tgl_lahir = ''; 
        $alamat = ''; 
        $email = ''; 
        $hp = ''; 
        $foto = ''; 
        $avatar = '../files/photo/'.$jk.'.png';

        $jenis = 'user';
        $tingkat = '';
        $tgl_masuk = '';
        $sub = 'Tambah ';
    }
    else{
        $q = "
                SELECT 
                    k.id_wil, 
                    k.kode, 
                    k.nik, 
                    k.nama, 
                    k.jk, 
                    k.tmpt_lahir, 
                    k.tgl_lahir, 
                    k.email, 
                    k.hp, 
                    k.alamat, 
                    k.tingkat,
                    k.foto,
                    k.tgl_masuk,

                    a.jenis,

                    w.nama nama_wil
                FROM 
                    karyawan k

                LEFT JOIN
                    akun a
                ON
                    a.id_pengguna = k.id

                LEFT JOIN
                    wilayah w
                ON
                    k.id_wil = w.id
                AND
                    w.hapus = '0'

                WHERE
                    k.id = '$id'

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

        $nama_wil = $r['nama_wil'];
        $kode = $r['kode'];
        $nik = $r['nik'];
        $nama = $r['nama'];
        $jk = $r['jk'];
        if($jk==''){
            $jk = 'n';
        }
        $tmpt_lahir = $r['tmpt_lahir'];
        $tgl_lahir = $r['tgl_lahir'];
        if($tgl_lahir=='0000-00-00'){
            $tgl_lahir = '';
        }
        $alamat = $r['alamat'];
        $email = $r['email'];
        $hp = $r['hp'];
        
        $foto = $r['foto'];

        if($foto==''){
            $avatar = '../files/photo/'.$jk.'.png';
        }
        else{
            $avatar = str_replace('%20', ' ', $foto);
            if(file_exists('../../'.$foto)){
                $avatar = '../'.$foto;
            }
            else{
                $avatar = '../files/photo/'.$jk.'.png';
            }
        }

        $jenis = $r['jenis'];
        $tingkat = $r['tingkat'];
        $tgl_masuk = $r['tgl_masuk'];
        if($tgl_masuk=='0000-00-00'){
            $tgl_masuk = '';
        }
        
        $sub = 'Edit ';
    }

    $sub .= 'Data BA/ BC';

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

<form id="frmEmployee">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <input type="hidden" name="jenis" value="<?php echo $jenis; ?>">
    <input type="hidden" name="id_wil" value="<?php echo $id_wil; ?>">

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
            <option value="">Belum ada data tingkat belajar</option>
<?php        
}
else{
?>
            <option value="">Pilih</option>
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


    <div class="field">
        <label>Nomor Induk Karyawan</label>
        <div class="ui input">
            <input type="text" id="nik" name="nik" placeholder="NIK" maxlength="32" value="<?php echo $nik; ?>">
        </div>
        <p style="font-size: 9pt; color: #60646D; margin-top: 10px;">
            *NIK digunakan sebagai <i>username</i>
<?php
if($id!=='0'){
?>
            <br>
            *Mengganti NIK, merubah username yang sudah ada
<?php        
}
?>                
        </p>
    </div>

    <div class="ui hidden divider"></div>

    <div class="fields">
        <div class="ten wide field">
            <label>Nama</label>
            <div class="ui input">
                <input type="text" id="nama" name="nama" placeholder="Nama" maxlength="32"  value="<?php echo $nama; ?>">
            </div>
        </div>
        <div class="six wide field pilJk">
            <label>Jenis Kelamin</label>
            <input type="hidden" id="jk" name="jk" value="<?php echo $jk; ?>">
            <div class="ui icon button l <?php if($jk=='l'){ ?> active teal <?php } ?>" data-content="Laki-laki" onclick="pilihJk('l')">
                <i class="male icon"></i>
            </div>
            <div class="ui icon button p <?php if($jk=='p'){ ?> active teal <?php } ?>" data-content="Perempuan" onclick="pilihJk('p')">
                <i class="female icon"></i>
            </div>
        </div>
    </div>

    <div class="fields">
        <div class="ten wide field">
            <label>Email</label>
            <div class="ui icon input">
                <i class="mail icon"></i>
                <input type="text" id="email" name="email" placeholder="Email" maxlength="64" value="<?php echo $email; ?>">
            </div>
        </div>
        <div class="six wide field">
            <label>No. HP</label>
            <div class="ui icon input">
                <i class="phone icon"></i>
                <input type="text" id="hp" name="hp" placeholder="No. Hp" maxlength="16"  value="<?php echo $hp; ?>">
            </div>
        </div>    
    </div>

    
    
    <div class="field">
        <label>Bekerja sejak</label>
        <div class="ui calendar" id="tglKerja">
            <div class="ui input left icon">
                <i class="calendar alternate outline icon"></i>
                <input type="text" placeholder="YYYY-MM-DD" name="tgl_masuk" id="tgl_masuk" value="<?php echo $tgl_masuk; ?>" />
            </div>
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

    function pilihJk(pref){
        var jkS = $('#jk').val();
        if(pref!==jkS){
            $('.pilJk .button').removeClass('active teal');
            $('.pilJk .'+pref).addClass('active teal');
            $('#jk').val(pref);
        }
    }

    $('#tglKerja').calendar({
      type: 'date',
      formatter:{
          date: function(date, setting){
              if (!date) return '';
              var day = ("0"+date.getDate()).slice(-2);
              var month = ("0"+(date.getMonth() + 1)).slice(-2);
              var year = date.getFullYear();
              return year + '-' + month + '-' + day;
          }
      }
    });

    $('#frmEmployee').submit(function(e){
        e.preventDefault();
        loadingMulai();

        var jenis = $('#jenis').val(),
            tingkat = $('#tingkat').val(),
            nik = $('#nik').val(),
            nama = $('#nama').val();

        if(jenis==''){
            tampilkanPesan('0','Jenis/ Level karyawan tidak valid.');
            loadingSelesai();
        }
        else if(tingkat==''){
            tampilkanPesan('0','Pilih tingkat belajar Karyawan.');
            loadingSelesai();
        }
        else if(nik==''){
            tampilkanPesan('0','Input Nomor Induk Karyawan.');
            loadingSelesai();
        }
        else if(nama==''){
            tampilkanPesan('0','Input Nama Karyawan.');
            loadingSelesai();
        }
        else{
            $.ajax({
                type:'post',
                async:true,
                data:$('#frmEmployee').serialize(),
                url:'interface/employee-form-process.php',
                success: function(data){
                    $('#feedBack').html(data);
                    loadingSelesai();
                }
            })
        }
    })
</script>