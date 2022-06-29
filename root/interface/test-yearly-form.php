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
    $main = 'Ujian';

    $idData = saring($_POST['idData']);

    if($idData=='0'){
        $id_ujian = '5483-8ABF1C';
        $kkm = '';
        $tanggal = '';
        $waktu = '45';
        $tampilan = '';
        $aktif = '';
        $sub = 'Tambah';
    }
    else{
        $q = "
                SELECT 
                    id_ujian, 
                    kkm,
                    tanggal, 
                    waktu, 
                    tampilan, 
                    aktif
                FROM 
                    ujian_pelaksanaan 
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
        $id_ujian = $r['id_ujian'];
        $kkm = $r['kkm'];
        $tanggal = $r['tanggal'];
        $waktu = $r['waktu'];
        $tampilan = $r['tampilan'];
        $aktif = $r['aktif'];
        $sub = 'Edit';
    }

     $sub .= ' Data Ujian Nasional';
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
    
<form id="frmJadwalUjian">
    <input type="hidden" name="view" value="1">
    <input type="hidden" name="id" value="<?php echo $idData; ?>">
    <input type="hidden" name="id_ujian" value="<?php echo $id_ujian; ?>">
    <input type="hidden" name="tampilan" value="all">

    <div class="fields">
        <div class="six wide field">
            <label>Tanggal pelaksanaan</label>
            <div class="ui calendar" id="tglUjian">
                <div class="ui input left icon">
                    <i class="calendar alternate outline icon"></i>
                    <input type="text" placeholder="YYYY-MM-DD" name="tanggal" id="tanggal" value="<?php echo $tanggal; ?>" />
                </div>
            </div>
        </div>
        <div class="four wide field">
            <label>Waktu (menit)</label>
            <input type="number" id="waktu" placeholder="<?php echo $waktu; ?>" name="waktu" maxlength="3" value="<?php echo $waktu; ?>" min='5'>
        </div>
        
    </div>
    <div class="fields">
        <div class="four wide field">
            <label>Nilai Min Lulus</label>
            <input type="number" id="kkm" placeholder="75" name="kkm" min="35" max="85" maxlength="2" value="<?php echo $kkm; ?>">
        </div>
    </div>

    <div class="ui floating message">
        <div class="header">
            Informasi
        </div>
        <p style="font-size: 9pt; color: #60646D; margin-top: 10px;">
            Soal akan secara acak diberikan oleh sistem kepada peserta ujian berdasarkan bank pertanyaan yang ada.<br>
            *Jumlah soal PK : 20<br>
            *Jumlah soal CH : 30
        </p>
    </div>


    <div class="ui hidden divider"></div>
    <div class="ui horizontal divider">
        <i class="chart line icon"></i> Grade Nilai
    </div>
    <div class="field">
        <table class="ui table unstackable">
            <thead>
                <tr>
                    <th colspan="2">Nilai</th>
                    <th rowspan="2">Grade</th>
                </tr>
                <tr>
                    <th>Awal</th>
                    <th>Sampai</th>
                </tr>
            </thead>
            <tbody>
<?php
        //regional set
        $q = "
                SELECT 
                    id, 
                    huruf, 
                    min, 
                    max

                FROM 
                    ujian_grade

                WHERE
                    hapus = '0'

                ORDER BY
                    min DESC
        ";
        $e = mysqli_query($conn, $q);
        $c = mysqli_num_rows($e);
        if($c=='0'){
            $adaGradePk = '0';
?>
                <tr>
                    <td colspan="4">
                        <i class="info circle green icon"></i> <i>Belum ada data..</i>
                    </td>
                </tr>
<?php
        }
        else{
            $adaGradePk = '1';
            $ar = array();
            $r = array();

            while ($d = mysqli_fetch_assoc($e)) {
                $r['id']    = $d['id'];
                $r['huruf']    = $d['huruf'];
                $r['min']    = $d['min'];
                $r['max']    = $d['max'];

                $ar[]   = $r;
            }

            $jar = $c-1;

            for ($i=0; $i <= $jar; $i++) { 
                $idGrade = $ar[$i]['id'];
                $huruf = $ar[$i]['huruf'];
                $min = $ar[$i]['min'];
                $max = $ar[$i]['max'];
?>
                <tr>
                    <td>
                        <?php echo $min; ?>
                    </td>
                    <td>
                        <?php echo $max; ?>
                    </td>
                    <td>
                        <?php echo $huruf; ?>
                    </td>
                </tr>
<?php            
                }                
            }
?>                            
            </tbody>
        </table>
        <input type="hidden" id="adaGradePk" value="<?php echo $adaGradePk; ?>">
<?php
    if($c>0){
?>
        <div class="ui message">
            <p>
                Peserta UN dengan nilai <strong><i>< [Nilai minimal lulus]</i></strong> dinyatakan <strong><i>harus remidi</i></strong>.
            </p>
        </div>
<?php        
    }
?>
    </div>
    
    <div class="field">
        <button class="ui icon button blue" type="submit">
            <i class="save icon"></i> Simpan
        </button>
    </div>
    
</form>



<script type="text/javascript">
    $('.dropdown').dropdown();

    $('#tglUjian').calendar({
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

    

    $('#frmJadwalUjian').submit(function(e){
        var kkm, tanggal, waktu, tampilan, adaGradePk;
        
        kkm = $('#kkm').val();
        tanggal = $('#tanggal').val();
        waktu = $('#waktu').val();
        tampilan = $('#tampilan').val();

        adaGradePk = $('#adaGradePk').val();

        e.preventDefault();
        loadingMulai();
        if(tanggal==''){
            tampilkanPesan('0','Pilih tanggal pelaksanaan.');
            loadingSelesai();
        }
        else if(waktu==''||waktu=='0'){
            tampilkanPesan('0','Isi waktu lamanya pengerjaan soal.');
            loadingSelesai();
        }
        else if(kkm==''){
            tampilkanPesan('0','Isi nilai minimal untuk dapat dinyatakan lulus.');
            loadingSelesai();
        }
        else if(adaGradePk=='0'){
            tampilkanPesan('0','Set Grade PK terselbih dahulu melalui menu setting.');
            loadingSelesai();
        }
        else{
            $.ajax({
                type:"post",
                async:true,
                url:"interface/test-form-process.php",
                data:$('#frmJadwalUjian').serialize(),
                success:function(data){
                    $("#feedBack").html(data);
                    loadingSelesai();
                }
            })
        }
    })

</script>