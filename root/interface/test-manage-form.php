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

    $q = "
            SELECT 
                up.id_ujian,
                up.tanggal, 
                up.waktu, 
                up.tampilan, 
                up.aktif,

                u.nama namaUjian, 
                u.deskripsi deskUjian

            FROM 
                ujian_pelaksanaan up

            LEFT JOIN
                ujian u
            ON
                up.id_ujian = u.id

            WHERE
                up.id = '$idData'
                
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
    $tanggal = $r['tanggal'];
    $waktu = $r['waktu'];
    $tampilan = $r['tampilan'];
    $aktif = $r['aktif'];

    $namaUjian = $r['namaUjian'];
    $deskUjian = $r['deskUjian'];
    


    $sekarang = date('Y-m-d');
    $jmlRemidial = 0;
    //Check UN remidial
    if($id_ujian=='5483-8ABF1C'){
        //getting latest id pelaksanaan to check remidial
        $q = "
                SELECT 
                    id, 
                    kkm, 
                    waktu, 
                    tampilan, 
                    kode

                FROM 
                    ujian_pelaksanaan 

                WHERE
                    id_periode = '$idPeriode'
                AND
                    id_ujian = '$id_ujian'
                AND
                    hapus = '0'
                AND
                    tanggal <= '$sekarang'
                AND
                    aktif = '1'
                AND
                    id != '$idData'

                ORDER BY
                    tanggal DESC

                LIMIT
                    1
        ";

        $e = mysqli_query($conn, $q);
        $c = mysqli_num_rows($e);
        if($c==1){
            $r = mysqli_fetch_assoc($e);
            $idPrev = $r['id'];
            $kkm = $r['kkm'];

            //get the id karyawan that remidi
            $q = "
                    SELECT 
                        id, 
                        id_karyawan, 
                        tanggal, 
                        mulai, 
                        selesai
                        
                    FROM 
                        karyawan_ujian 

                    WHERE
                        hapus = '0' 
                    AND
                        id_pelaksanaan = '$idPrev'
                    AND
                        remidi = '1'
                    AND
                        id_karyawan

                        NOT IN (
                            SELECT 
                                id_karyawan
                                
                            FROM 
                                ujian_pelaksanaan_target_karyawan 

                            WHERE
                                id_pelaksanaan = '$idData'
                            AND
                                hapus = '0'
                        )
            ";

            $e = mysqli_query($conn, $q);
            $jmlRemidial = mysqli_num_rows($e);
        }
    }

?>

<div class="ui message">
    <p>
        Kelola <strong><?php echo $namaUjian; ?></strong>, dengan tanggal pelaksanaan <strong><?php echo tanggalKan($tanggal); ?></strong>.
    </p>
</div>

<div id="subDisplay">
    <?php
    if($jmlRemidial>0){
        //only for UN
        if($id_ujian=='5483-8ABF1C'){
?>
        <div id="imporMsg<?php echo $idData; ?>" class="ui message">
            <i class="close icon"></i>
            <div class="header">
                Impor peserta.
            </div>
            <p>
                Terdapat <strong><?php echo $jmlRemidial; ?></strong> peserta remidial dari <?php echo $namaUjian ?> sebelumnya.
            </p>
            <div class="ui icon violet button" onclick="tampilkanKonfirmasi('<?php echo $idData; ?>','Impor data','Yakin ingin mengimpor data peserta ujian dari data peserta remidial ujian sebelumnya ?','interface/test-manage-import-participant.php')">
                <i class="magic icon"></i> Impor
            </div>
        </div>        
<?php
        }
    }
?>


<div class="field">
    <div class="ui icon button" onclick="backToMain()">
        <i class="left chevron icon"></i> Kembali
    </div>
</div>

    <div class="ui basic vertical segment clearing" style="margin: 0px; padding: 0px;">
        <div class="ui icon button right floated" onclick="updateRowSub()" data-content="Reload">
            <i class="redo icon"></i>
        </div>
        <div class="ui button green right floated" onclick="loadFormSub('add-participant','<?php echo $idData; ?>')">
            <i class="user add icon"></i> Tambah
        </div>
        
        <div class="ui icon input">
            <input id="searchDataSub" placeholder="Cari Data.." type="text" onkeyup="cariDataSub()" />
            <i class="search icon"></i>
        </div>
    </div>
    
    <table class="ui selectable table">
        <thead>
            <tr>
                <th width="4%">No</th>
                <th>Peserta</th>
                <th width="26%">Pelaksanaan</th>
                <th width="26%">Opsi</th>
            </tr>
        </thead>
        <tbody id="resultDataSub">
            <!-- load data here -->
            <tr>
                <td colspan="4">
                    <i class="info circle icon"></i> <i>Load Data..</i>
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4">
                    <div class="ui vertical basic segment clearing" style="padding: 0px;">
                        <select class="ui dropdown compact" id="jumlahRowSub"  onchange="updateRowSub()">
                            <option value="50">50 Baris</option>
                            <option value="75">75 Baris</option>
                            <option value="100">100 Baris</option>
                        </select>

                        <input type="hidden" id="lastPageSub" value="0">
                        <div class="ui right floated pagination menu" id="pageNumberSub">
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
</div>

<div id="subForm" style="display: none;">
    <!-- load other page here -->
</div>


<script type="text/javascript">
    $('.button').popup();
    $('.dropdown').dropdown();

    updateRowSub();





    function reSusulan(prefix){
        $('#mainBtnBox'+prefix).transition('slide left');
        $('#opsiReSusulan'+prefix).transition('slide right');
    }

    function batalReSusulan(prefix){
        $('#opsiReSusulan'+prefix).transition('slide right');
        $('#mainBtnBox'+prefix).transition('slide left');
    }

    function simpanReSusulan(prefix){
        var newTgl = $('#tglReSusulan'+prefix).val();
        if(newTgl==''){
            tampilkanPesan('0','Silahkan set tanggal susulan baru.');
        }
        else{
            loadingMulai();
            $.ajax({
                type:"post",
                async:true,
                url:"interface/test-manage-form-resusulan.php",
                data:{
                    'view':'1',
                    'idData':prefix,
                    'tanggal':newTgl
                },
                success:function(data){
                    $("#feedBack").html(data);
                    loadingSelesai();
                }
            })
        }
    }

</script>