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

    $idData = saring($_POST['idData']);

    if($idData=='0'){
        $id_ujian = '';
        $kkm = '';
        $tanggal = '';
        $waktu = '';
        $tampilan = '';
        $aktif = '';
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
        if($c=='1'){
            $r = mysqli_fetch_assoc($e);

            $id_ujian = $r['id_ujian'];
            $kkm = $r['kkm'];
            $tanggal = $r['tanggal'];
            $waktu = $r['waktu'];
            $tampilan = $r['tampilan'];
            $aktif = $r['aktif'];
        }
        else{
            $id_ujian = '';
            $kkm = '';
            $tanggal = '';
            $waktu = '';
            $tampilan = '';
            $aktif = '';
        }
    }

    $sekarang = date('Y-m-d');

    if($sekarang > $tanggal){
        $susulan = '1';
?>
        <div class="ui floating message">
            Data karyawan akan ditambahkan sebagai peserta ujian susulan.
        </div>
<?php        
    }
    else{
        $susulan = '0';
    }
?>
<div class="ui basic vertical segment clearing" style="margin: 0px 0px 10px 0px; padding: 0px;">
    <div class="ui icon button right floated" data-content="Tutup" onclick="backFromSub()">
        <i class="close icon"></i>
    </div>
    <h4 class="ui header" style="padding-top: 6px;">
        <i class="users icon"></i> Tambah Peserta Ujian
    </h4>
</div>


<div class="ui styled fluid accordion">
    <div class="active title">
        <i class="dropdown icon"></i> Seleksi cepat
    </div>
    <div class="active content">
        <div class="field">
            <label>NIK Karyawan</label>
            <select id="id_karyawan_cepat" name="id_karyawan_cepat" class="ui search dropdown fluid" multiple="">
<?php
    $q = "
            SELECT 
                k.id idK,
                k.nik, 
                k.nama, 


                t.nama tingkat, 

                w.kode,
                w.nama wilayah,

                a.jenis

            FROM 
                karyawan k

            LEFT JOIN
                tingkat_belajar t
            ON
                k.tingkat = t.id

            LEFT JOIN
                wilayah w
            ON
                k.id_wil = w.id

            LEFT JOIN
                akun a
            ON
                a.id_pengguna = k.id

            WHERE
                k.id NOT IN 
                    (
                        SELECT 
                            id_karyawan
                        FROM 
                            ujian_pelaksanaan_target_karyawan 
                        WHERE
                            id_pelaksanaan = '$idData'
                        AND
                            hapus = '0'
                    )
            AND
                k.hapus = '0'

            ORDER BY 
                k.nik ASC
    ";
    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);
    if($c==0){
?>
                <option value="Kosong"></option>
<?php
    }
    else{
?>
        <option value="">Pilih</option>
<?php
        while ($r = mysqli_fetch_assoc($e)) {
            $idK = $r['idK'];
            $nik = $r['nik'];
            $nama = $r['nama'];
            $kode = $r['kode'];
            $wilayah = $r['wilayah'];
            $jenis = $r['jenis'];
?>
                <option value="<?php echo $idK; ?>">
                    <?php echo $nik.' - '.$nama.' ('.$kode.' - '.$wilayah.' - '.$jenis.')'; ?>
                </option>
<?php            
        }
    }
?>                    
            </select>
        </div>
<?php
            if($sekarang > $tanggal){
?>
            <div class="field">
                <label>Tanggal Susulan</label>
                <div class="ui calendar" id="tglSusulanCepat">
                    <div class="ui input left icon">
                        <i class="calendar alternate outline icon"></i>
                        <input type="text" placeholder="YYYY-MM-DD" name="tgl_susulan_cepat" id="tgl_susulan_cepat" value="" />
                    </div>
                </div>
            </div>
<?php        
            }
?>    
        <div class="field">
            <div class="ui icon primary button" onclick="tambahkanCepat()">
                <i class="save icon"></i> Simpan
            </div>
        </div>
    </div>

    <div class="title">
        <i class="dropdown icon"></i> Seleksi detail
    </div>
    <div class="content">
        <div class="ui basic vertical segment clearing" style="margin: 0px; padding: 0px;">
            <div class="ui icon button right floated" onclick="updateRowCalonPeserta()" data-content="Reload">
                <i class="redo icon"></i>
            </div>

            
            <div class="ui icon input">
                <input id="searchDataCalonPeserta" placeholder="Cari Data.." type="text" />
                <i class="search icon"></i>
            </div>
        </div>    
        <form id="frmAddParticipants">
            <input type="hidden" name="view" value="1">
            <input type="hidden" name="id" value="<?php echo $idData; ?>">
            <input type="hidden" name="susulan" value="<?php echo $susulan; ?>">

            <table class="ui selectable table">
                <thead>
                    <tr>
                        <th width="4%">No</th>
                        <th>Karyawan</th>
                        <th width="10%">
                            <div id="chkAllInPage" class="ui master disabled checkbox">
                                <input type="checkbox" name="chkAllInPage">
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody id="resultDataCalonPeserta">
                    <!-- load data here -->
                    <tr>
                        <td colspan="3">
                            <i class="info circle icon"></i> <i>Load Data</i>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3">
                            <div class="ui vertical basic segment clearing" style="padding: 0px;">
                                <select class="ui dropdown compact" id="jumlahRowCalonPeserta"  onchange="updateRowCalonPeserta()">
                                    <option value="250">250 Baris</option>
                                    <option value="350">350 Baris</option>
                                    <option value="500">500 Baris</option>
                                </select>

                                <input type="hidden" id="lastPageCalonPeserta" value="0">
                                <input type="hidden" id="calonTerpilih" value="0">
                                <div class="ui right floated pagination menu" id="pageNumberCalonPeserta">
                                    <!-- show row -->
                                    <div class="active item">
                                        0
                                    </div>
                                </div>
                            </div>
                        </th>
                    </tr>
                </tfoot>
            </table>
            
        <?php
            if($sekarang > $tanggal){
        ?>
            <div class="field">
                <label>Tanggal Susulan</label>
                <div class="ui calendar" id="tglSusulan">
                    <div class="ui input left icon">
                        <i class="calendar alternate outline icon"></i>
                        <input type="text" placeholder="YYYY-MM-DD" name="tgl_susulan" id="tgl_susulan" value="" />
                    </div>
                </div>
            </div>
        <?php        
            }
        ?>    

            <div class="field">
                <button id="btnSetPeserta" class="ui icon button blue disabled" type="submit">
                    <i class="save icon"></i> Simpan
                </button>
            </div>
            
        </form>
    </div>
</div>





<script type="text/javascript">
    $('.button').popup();
    $('.dropdown').dropdown({ fullTextSearch: "exact" });
    $('.accordion').accordion();

    $('#chkAllInPage').checkbox({
    // check all children
    onChecked: function() {
        var $childCheckbox  = $('#resultDataCalonPeserta td').find('.checkbox');
        $childCheckbox.checkbox('check');
    },
    // uncheck all children
    onUnchecked: function() {
        var $childCheckbox  = $('#resultDataCalonPeserta td').find('.checkbox');
        $childCheckbox.checkbox('uncheck');
    }
  });

<?php
    if($susulan=='1'){
?>
        $('#tglSusulan, #tglSusulanCepat').calendar({
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
<?php        
    }
?>    

    updateRowCalonPeserta();

    function updateRowCalonPeserta(){
        dataListCalonPeserta();
        showRowCalonPeserta();
        $('#calonTerpilih').val('0');
        $('#btnSetPeserta').addClass('disabled');
        $('#chkAllInPage').checkbox('set unchecked');
    }
    
    function dataListCalonPeserta(){
        loadingMulai();
        var lastPageCalonPeserta, limit, key, id_pelaksanaan;
        start = $('#lastPageCalonPeserta').val();
        limit = $("#jumlahRowCalonPeserta").val();
        key = $("#searchDataCalonPeserta").val();
        id_pelaksanaan = '<?php echo $idData; ?>';

        $.post('interface/test-manage-form-add-participant-form-list.php',{view:'1', start: start, limit: limit, cari: key, id_pelaksanaan: id_pelaksanaan},
            function(result){
                $("#resultDataCalonPeserta").html(result);
                loadingSelesai();
            }
        );
    }

    function showRowCalonPeserta(){
        loadingMulai();
        var limit, key, id_pelaksanaan;
        limit = $("#jumlahRowCalonPeserta").val();
        key = $("#searchDataCalonPeserta").val();
        id_pelaksanaan = '<?php echo $idData; ?>';

        $.post('interface/test-manage-form-add-participant-form-list-number.php',{view:'1', limit: limit, cari: key, id_pelaksanaan: id_pelaksanaan},
            function(result){
                $("#pageNumberCalonPeserta").html(result);
                loadingSelesai();
            }
        );
    }


    function updateList(start, id){
        $('#lastPageCalonPeserta').val(start);
        $("#pageNumberCalonPeserta a").removeClass("active");
        dataListCalonPeserta();
        $("#number"+id).addClass("active");
    }

    
    $("#searchDataCalonPeserta").keyup(function(event){
        if(event.keyCode == 13){
            updateRowCalonPeserta();
        }
    });
    

    $('#frmAddParticipants').submit(function(e){
        var calonTerpilih = $('#calonTerpilih').val();
<?php
    if($susulan=='1'){
?>
        var tgl_susulan = $('#tgl_susulan').val();
<?php        
    }
?>        
        e.preventDefault();
        loadingMulai();
        if(calonTerpilih=='0'){
            tampilkanPesan('0','Pilih karyawan sebagai peserta ujian.');
            loadingSelesai();
        }
<?php
    if($susulan=='1'){
?>
        else if(tgl_susulan==''){
            tampilkanPesan('0','Pilih tanggal ujian susulan.');
            loadingSelesai();
        }
<?php        
    }
?>        
        else{
            $.ajax({
                type:"post",
                async:true,
                url:"interface/test-manage-form-add-participant-form-proess.php",
                data:$('#frmAddParticipants').serialize(),
                success:function(data){
                    $("#feedBack").html(data);
                    loadingSelesai();
                }
            })
        }
    })


    function tambahkanCepat(){
        var id_terpilih, tgl_susulan;
        id_terpilih = $('#id_karyawan_cepat').val();
<?php
    if($susulan=='1'){
?>
        tgl_susulan = $('#tgl_susulan_cepat').val();
<?php        
    }
    else{
?>
        tgl_susulan = '0000-00-00';
<?php        
    }
?>        
        loadingMulai();
        if(id_terpilih==''){
            tampilkanPesan('0','Pilih karyawan terlebih dahulu.');
            loadingSelesai();
        }
<?php
    if($susulan=='1'){
?>
        else if(tgl_susulan==''){
            tampilkanPesan('0','Pilih tanggal ujian susulan.');
            loadingSelesai();
        }
<?php        
    }
?>        
        else{
            $.ajax({
                type:"post",
                async:true,
                url:"interface/test-manage-form-add-participant-custom.php",
                data:{
                    'view': '1',
                    'id_pelaksanaan': '<?php echo $idData; ?>',
                    'susulan': '<?php echo $susulan; ?>',
                    'tgl_susulan': tgl_susulan,
                    'id_karyawan': id_terpilih
                },
                success:function(data){
                    $("#feedBack").html(data);
                    loadingSelesai();
                }
            })
        }
    }

</script>