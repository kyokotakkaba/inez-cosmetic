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

    if($_SESSION['menu']!=='bio'){
        $_SESSION['menu'] = 'bio';
    }

    $id = $_SESSION['idPengguna'];

    $q = "
        SELECT 
            k.id,
            k.nik, 
            k.nama, 
            k.jk, 
            k.tmpt_lahir,
            k.tgl_lahir, 
            k.email,
            k.hp,
            k.alamat,
            k.foto,
            k.tgl_masuk,

            tb.nama tingkatBelajar,

            a.jenis,

            w.nama nama_wil,

            wsw.nama nama_wil_sup

        FROM 
            karyawan k

        LEFT JOIN
            tingkat_belajar tb
        ON
            tb.id = k.tingkat

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

        LEFT JOIN
            wilayah_supervisi ws
        ON
            ws.id_karyawan = k.id
        AND
            ws.hapus = '0'

        LEFT JOIN
            wilayah wsw
        ON
            ws.id_wilayah = wsw.id
        AND
            wsw.hapus = '0'

        WHERE
            k.id = '$id'
        AND
            k.hapus = '0'

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
    $idData = $r['id'];
            
    $nik = $r['nik'];
    $nama = $r['nama'];
    
    $jk = strtolower($r['jk']);
    if($jk!=='l'&&$jk!=='p'){
        $jk = 'n';
    }

    $iniHari = date('Y-m-d');
    $tgl_lahir = $r['tgl_lahir'];
    $infoUmur = '-';
    if(!empty($tgl_lahir) && $tgl_lahir !== '' && $tgl_lahir !== '0000-00-00'){
        $infoUmur = hitungUmur($tgl_lahir);
        if($iniHari == $tgl_lahir){
            $infoUmur .= '<span class="ui pink label">ULTAH!</span>';
        }
    }
    else{
        $tgl_lahir = '';
    }
    
    $jenis = $r['jenis'];

    $nama_wil = $r['nama_wil'];
    $nama_wil_sup = $r['nama_wil_sup'];
    $infoTingkat = $jenis;
    if(!empty($nama_wil_sup)){
        if($nama_wil_sup!==$nama_wil){
            $infoTingkat .= ' - '.$nama_wil_sup;
        }
    }

    if($nama_wil==''){
        $nama_wil = '-';
    }
    
    $foto = $r['foto'];
    $avatar = '../files/photo/'.$jk.'.png';
    $ketemu = '1';
    if(!empty($foto) && $foto !== ''){
        $foto = str_replace('%20', ' ', $foto);
        if(file_exists('../../'.$foto)){
            $avatar = '../'.$foto;
        }
        else{
            $ketemu = '0';
        }
    }          

    $tgl_masuk = $r['tgl_masuk'];
    $lamaKerja = '-';
    if(!empty($tgl_masuk) && $tgl_masuk !== '' && $tgl_masuk !== '0000-00-00'){
        $lamaKerja = hitungUmur($tgl_masuk);
    }

    $tingkatBelajar = $r['tingkatBelajar'];
    $tmpt_lahir = $r['tmpt_lahir'];
    $alamat = $r['alamat'];
    $email = $r['email'];
    $hp = $r['hp'];
?>
<h2 class="ui block header">
    <i class="user icon"></i>
    <div class="content">
        Biodata
        <div class="sub header">
            Pengguna Sistem
        </div>
    </div>
</h2>




<div id="dataDisplay">
    <table class="ui table">
        <thead>
            <tr>
                <th colspan="2">
                    <img id="prevGambar" src="<?php echo $avatar; ?>" class="ui image small rounded centered">
<?php
    if($ketemu=='0'){
?>
                <p style="font-size: 9pt; color: #60646D; margin-top: 10px;">
                    File tidak ditemukan. Beralih ke <i>default</i>.
                </p>
<?php        
    }
?>              
                    <div class="field" style="margin-top: 16px;">
                        <center>
                            <div class="ui icon button small inverted" onclick="loadForm('bio-upload', '<?php echo $id; ?>')" style="<?php echo $accentColor; ?>">
                                <i class="upload cloud icon"></i> Ganti Gambar
                            </div>    
                        </center>
                    </div> 
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    Info Pengguna
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td width="26%">NIK</td>
                <td>
                    <strong><?php echo $nik; ?></strong>
                    <p style="font-size: 9pt; color: #60646D; margin-top: 10px;">
                        Nomor Induk Karyawan adalah username pada sistem
                    </p>
                </td>
            </tr>
            <tr>
                <td>Level</td>
                <td><?php echo $infoTingkat; ?></td>
            </tr>
            <tr>
                <td>Tingkat Belajar</td>
                <td><?php echo $tingkatBelajar; ?></td>
            </tr>
            <tr>
                <td>Bekerja Sejak</td>
                <td><?php echo $lamaKerja; ?></td>
            </tr>
        </tbody>
    </table>

    <form id="frmBio">
        <input type="hidden" name="id_karyawan" value="<?php echo $id; ?>">

        <div class="ui teal segment">
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
                        <i class="male large icon"></i>
                    </div>
                    <div class="ui icon button p <?php if($jk=='p'){ ?> active teal <?php } ?>" data-content="Perempuan" onclick="pilihJk('p')">
                        <i class="female large icon"></i>
                    </div>
                </div>
            </div>
            <div class="fields">
                <div class="ten wide field">
                    <label>Tempat Lahir</label>
                    <div class="ui input">
                        <input type="text" id="tmpt_lahir" name="tmpt_lahir" placeholder="Tempat lahir" maxlength="64" value="<?php echo $tmpt_lahir; ?>">
                    </div>
                </div>
                <div class="six wide field">
                    <label>Tgl. Lahir</label>
                    <div class="ui calendar" id="tglLahir">
                        <div class="ui input left icon">
                            <i class="calendar alternate outline icon"></i>
                            <input type="text" placeholder="YYYY-MM-DD" name="tgl_lahir" id="tgl_lahir" value="<?php echo $tgl_lahir; ?>" />
                        </div>
                    </div>
                    <p style="font-size: 9pt; color: #60646D; margin-top: 10px;">
                        *Contoh: <i><?php echo date('Y-m-d'); ?></i>
                    </p>
                </div>
            </div>

            <div class="field">
                <label>Alamat</label>
                <div class="ui input">
                    <textarea rows="3" id="alamat" name="alamat" placeholder="Alamat" maxlength="256"><?php echo $alamat; ?></textarea>
                </div>
            </div>

        </div>

        <div class="ui orange segment form ">
            <div class="fields">
                <div class="ten wide field">
                    <label>Emai</label>
                    <div class="ui input">
                        <input type="text" id="email" name="email" placeholder="Email" maxlength="64"  value="<?php echo $email; ?>">
                    </div>
                </div>
                <div class="six wide field">
                    <label>No. HP</label>
                    <div class="ui input">
                        <input type="text" id="hp" name="hp" placeholder="No. Hp" maxlength="16"  value="<?php echo $hp; ?>">
                    </div>
                </div>    
            </div>
        </div>

        <div class="field">
            <button class="ui icon button blue" type="submit">
                <i class="save icon"></i> Simpan
            </button>
        </div>
        
    </form>
</div>
<div id="formDisplay" style="display: none;">
    <!-- load form here -->
</div>





<script type="text/javascript">
    $('.dropdown').dropdown();
    $('.button').popup();

    function pilihJk(pref){
        var jkS = $('#jk').val();
        if(pref!==jkS){
            $('.pilJk .button').removeClass('active teal');
            $('.pilJk .'+pref).addClass('active teal');
            $('#jk').val(pref);

            var alamat = $('#gambar').val(),
                newAlamat = '../files/photo/'+pref+'.png';
            if(alamat==''){
                $('#prevGambar').attr('src', newAlamat);
            }
        }
    }


    $('#tglLahir').calendar({
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

    $('#frmBio').submit(function(e){
        e.preventDefault();

        var nama = $('#nama').val(),
            jk = $('#jk').val(),
            tmpt_lahir = $('#tmpt_lahir').val(),
            tgl_lahir = $('#tgl_lahir').val(),
            alamat = $('#alamat').val(),
            email = $('#email').val(),
            hp = $('#hp').val();
        
        if(nama==''){
            tampilkanPesan('0','Isi Nama.');
        }
        else if(jk==''||jk=='n'){
            tampilkanPesan('0','Pilih jenis kelamin.');
        }
        else if(tmpt_lahir==''){
            tampilkanPesan('0','Isi tempat lahir.');
        }
        else if(tgl_lahir==''||tgl_lahir=='0000-00-00'){
            tampilkanPesan('0','Atur tangal lahir.');
        }
        else if(alamat==''){
            tampilkanPesan('0','Isi alamat lengkap.');
        }
        else if(email==''){
            tampilkanPesan('0','Isi Email.');
        }
        else if(hp==''){
            tampilkanPesan('0','Isi nomor hp.');
        }
        else{
            loadingMulai();
            $.ajax({
                type:'post',
                async:true,
                data:$('#frmBio').serialize(),
                url:'interface/bio-update.php',
                success: function(data){
                    $('#feedBack').html(data);
                    loadingSelesai();
                }
            })
        }
    })
</script>