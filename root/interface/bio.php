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

    if($_SESSION['menu']!=='bio'){
        $_SESSION['menu'] = 'bio';
    }

    $idPengguna = $_SESSION['idPengguna'];

    $q = "
            SELECT 
                nik,
                nama, 
                jk, 
                tmpt_lahir,
                tgl_lahir,
                alamat,
                email, 
                hp, 
                foto
            FROM 
                root 
            WHERE
                id = '$idPengguna'
            LIMIT
                1
    ";
    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);
    if($c=='1'){
        $r = mysqli_fetch_assoc($e);
        $nik = $r['nik'];
        $nama = $r['nama'];
        $jk = $r['jk'];
        $tmpt_lahir = $r['tmpt_lahir'];
        $tgl_lahir = $r['tgl_lahir'];
        $email = $r['email'];
        $alamat = $r['alamat'];
        $hp = $r['hp'];
        $urlFoto = $r['foto'];
        if($urlFoto==''){
            $foto = '../files/photo/'.$jk.'.png';
        }
        else{
            $urlFoto = str_replace('%20', ' ', $urlFoto);
            if(file_exists('../../'.$urlFoto)){
                $foto = '../'.$urlFoto;
            }
            else{
                $foto = '../files/photo/'.$jk.'.png';
            }
        }
    }
    else{
        $nik = '';
        $nama = '';
        $jk = 'n';
        $tgl_lahir = '';
        $tmpt_lahir = '';
        $email = '';
        $alamat = '';
        $hp = '';
        $foto = '../files/photo/'.$jk.'.png';
    }
?>
<h2 class="ui block header">
    <i class="user icon"></i>
    <div class="content">
        Biodata
        <div class="sub header">
            Pengguna E-Learning
        </div>
    </div>
</h2>

<form id="frmBiodata">
    <input type="hidden" name="id" value="<?php echo $idPengguna; ?>">

    <div class="ui orange segment">
        <div class="two fields">
            <div class="field">
                <label>Nomor Induk Karyawan</label>
                <div class="ui input">
                    <input type="text" id="nik" name="nik" placeholder="NIK" maxlength="32" required="required" value="<?php echo $nik; ?>">
                </div>
                <p style="font-size: 9pt; color: #60646D; margin-top: 10px;">
                    *NIK digunakan sebagai username<br>
                    *Mengganti NIK, merubah username yang sudah ada
                </p>
            </div>
            <div class="field">
                <label>Password</label>
                <div class="ui input">
                    <input type="text" id="pass" name="pass" placeholder="Isi untuk mengganti" maxlength="32" value="<?php echo $nik; ?>">
                </div>
                <p style="font-size: 9pt; color: #60646D; margin-top: 10px;">
                    *Tetap kosongkan jika ingin password tetap sama<br>
                    *Isi password jika ingin merubahnya
                </p>
            </div>
        </div>
    </div>

    <div class="ui grey segment">
        <div class="fields">
            <div class="six wide field">
                <label>Foto profil</label>
                <img id="prevFoto" src="<?php echo $foto; ?>" class="ui image small">
            </div>
            <div class="ten wide field">
                <div class="ui action input">
                    <input type="text" id="foto" name="foto" readonly="readonly" placeholder="Pilih file" value="<?php echo $foto; ?>" onchange="gantiFoto()">
                    <a id="pickFoto" class="ui icon button" type="button" href="../filemanager/dialog.php?type=1&field_id=foto">
                        <i class="open folder icon"></i>
                    </a>
                </div>
                <p style="font-size: 9pt; color: #60646D; margin-top: 10px;">
                    *Merupakan file gambar karyawan yang dimaksud.<br>
                    *Nama file <strong>tidak boleh ada spasi</strong>. Ganti tanda spasi pada nama file (jika ada) dengan tanda penghubung (-) melalui popup <i>file manager selecctor</i><br>
                </p>
            </div>
        </div>
    </div>

    <div class="ui teal segment">
        <div class="fields">
            <div class="ten wide field">
                <div class="ui input">
                    <input type="text" id="nama" name="nama" placeholder="Nama" maxlength="32" required="required" value="<?php echo $nama; ?>">
                </div>
            </div>
            <div class="six wide field pilJk">
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
                <div class="ui input">
                    <input type="text" id="tmpt_lahir" name="tmpt_lahir" placeholder="Tempat lahir" maxlength="64" required="required" value="<?php echo $tmpt_lahir; ?>">
                </div>
            </div>
            <div class="six wide field">
                <div class="ui calendar" id="tglLahir">
                    <div class="ui input left icon">
                        <i class="calendar outline icon"></i>
                        <input type="text" placeholder="Tanggal lahir YYYY-MM-DD" name="tgl_lahir" id="tgl_lahir" value="<?php echo $tgl_lahir; ?>" />
                    </div>
                </div>
            </div>
        </div>

        <div class="field">
            <div class="ui input">
                <input type="text" id="alamat" name="alamat" placeholder="Alamat" maxlength="256" required="required" value="<?php echo $alamat; ?>">
            </div>
        </div>
        <div class="fields">
            <div class="ten wide field">
                <div class="ui input">
                    <input type="text" id="email" name="email" placeholder="Email" maxlength="64" required="required" value="<?php echo $email; ?>">
                </div>
            </div>
            <div class="six wide field">
                <div class="ui input">
                    <input type="text" id="hp" name="hp" placeholder="No. Hp" maxlength="16" required="required" value="<?php echo $hp; ?>">
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

    $('#pickFoto').fancybox({
        'width'     : '100%',
        'height'    : '100%',
        'type'      : 'iframe',
        'fitToView' : false,
        'autoSize'  : false
    });


    function gantiFoto(){
        var alamat = $('#foto').val();
        if(alamat!==''){
            $('#prevFoto').attr('src',alamat);
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

    $('#frmBiodata').submit(function(e){
        e.preventDefault();
        loadingMulai();
        $.ajax({
            type:'post',
            async:true,
            data:$('#frmBiodata').serialize(),
            url:'interface/bio-update.php',
            success: function(data){
                $('#feedBack').html(data);
                loadingSelesai();
            }
        })
    })
</script>