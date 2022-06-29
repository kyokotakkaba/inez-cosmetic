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

    if($_SESSION['menu']!=='set'){
        $_SESSION['menu'] = 'set';
    }
?>

<h2 class="ui block header">
    <i class="settings icon"></i>
    <div class="content">
        Setting
        <div class="sub header">
            Sesuaikan pengaturan sistem
        </div>
    </div>
</h2>

<div id="dataDisplay">
    <div class="ui orange segment">
        <h4 class="ui header">
            <i class="globe icon"></i> Data website
        </h4>
        <div class="ui divider"></div>
<?php
        //set for lembaga
        $q = "
                SELECT 
                    title, 
                    description, 
                    keywords, 
                    deploy_year, 
                    owner, 
                    owner_web 
                FROM 
                    app_web_meta
                WHERE 
                    id = '$appCode'
                LIMIT
                    1
        ";
        $e = mysqli_query($conn, $q);
        $c = mysqli_num_rows($e);
        if($c=='0'){
?>
            <i class="info circle teal icon"></i> <i>Belum ada data.</i>
<?php
        }
        else{
            $r = mysqli_fetch_assoc($e);

            $title = $r['title'];
            $keywords = $r['keywords'];
            $description = $r['description'];
            $deploy_year = $r['deploy_year'];
            $owner = $r['owner'];
            $owner_web = $r['owner_web'];
            if($owner_web==''){
                $ketWeb = '-';
            }
            else{
                $ketWeb = "<a href='$owner_web' target='_blank'>$owner_web</a>";
            }
?>
            <table class="ui very basic table">
                <tbody>
                    <tr>
                        <td width="26%">Judul</td>
                        <td><?php echo $title; ?></td>
                    </tr>
                    <tr>
                        <td>Deskripsi</td>
                        <td><?php echo $description; ?></td>
                    </tr>
                    <tr>
                        <td>Kata kunci (mesin pencari)</td>
                        <td><?php echo $keywords; ?></td>
                    </tr>
                    <tr>
                        <td>Pemilik</td>
                        <td><?php echo $owner; ?>, Web: <?php echo $ketWeb; ?></td>
                    </tr>
                    <tr>
                        <td>Tahun mulai</td>
                        <td><?php echo $deploy_year; ?></td>
                    </tr>
                </tbody>
            </table>
<?php            
        }
?>            
            <div class="ui orange button" onclick="loadForm('set-web','0')">
                <i class="edit icon"></i> Edit
            </div>
    </div>

    <div class="ui teal segment">
        <h4 class="ui header">
            <i class="chart line icon"></i> Tingkat Belajar
        </h4>
        <div class="ui divider"></div>
        <div class="ui basic vertical segment clearing" style="margin: 0px; padding: 0px;">
            <div class="ui icon button teal right floated" onclick="loadForm('set-learn-level','0')">
                <i class="plus icon"></i> Tambah
            </div>
        </div>

        <table class="ui very basic table striped selectable unstackable">
            <thead>
                <th width="4%">No</th>
                <th>Tingkatan</th>
                <th width="26%">Opsi</th>
            </thead>
            <tbody>
<?php
        //set for lembaga
        $q = "
                SELECT 
                    id, 
                    no, 
                    nama, 
                    deskripsi
                FROM 
                    tingkat_belajar 
                WHERE
                    hapus = '0'
                ORDER BY
                    no ASC
        ";
        $e = mysqli_query($conn, $q);
        $c = mysqli_num_rows($e);
        if($c=='0'){
?>
                <tr>
                    <td colspan="3">
                        <i class="info circle teal icon"></i> <i>Belum ada data.</i>
                    </td>
                </tr>
<?php
        }
        else{
            $ar = array();
            $r = array();

            while ($d = mysqli_fetch_assoc($e)) {
                $r['id']    = $d['id'];
                $r['no']    = $d['no'];
                $r['nama']    = $d['nama'];
                $r['deskripsi']    = $d['deskripsi'];

                $ar[]   = $r;
            }

            $jar = $c-1;

            $qB = "
                    SELECT
                        id
                    FROM
                        tingkat_belajar
            ";
            $eB = mysqli_query($conn, $qB);
            $cB = mysqli_num_rows($eB);

            $bebas = $cB+1;

            for ($i=0; $i <= $jar; $i++) { 
                $idTingkat = $ar[$i]['id'];
                $no = $ar[$i]['no'];
                $nama = $ar[$i]['nama'];
                $deskripsi = $ar[$i]['deskripsi'];

                if($c>1){
                    if($i==0){
                        $classPrev = 'disabled';
                        $classNext = '';

                        $sasarPrev = $no;
                        $n = $i+1;
                        $sasarNext = $ar[$n]['no'];
                    }
                    else if($i==$jar){
                        $classPrev = '';
                        $classNext = 'disabled';

                        $sasarNext = $no;
                        $p = $i-1;
                        $sasarPrev = $ar[$p]['no'];
                    }
                    else if($i>0&&$i<$jar){
                        $classPrev = '';
                        $classNext = '';

                        $sasarNext = $no;
                        $p = $i-1;
                        $n = $i+1;
                        $sasarPrev = $ar[$p]['no'];
                        $sasarNext = $ar[$n]['no'];
                    }
                }
?>
                <tr>
                    <td><?php echo $i+1; ?></td>
                    <td>
                        <h4 class="ui header">
                            <?php echo $nama; ?>
                            <div class="sub header">
                                <?php echo $deskripsi; ?>
                            </div>
                        </h4>
                    </td>
                    <td>
<?php
    if($c>1){
?>
                        <div class="ui icon button <?php echo $classPrev; ?>" data-content="Majukan" onclick="reposisiData('<?php echo $no; ?>','<?php echo $sasarPrev; ?>', '<?php echo $bebas; ?>', 'interface/set-learn-level-reposition.php')">
                            <i class="up chevron icon"></i>
                        </div>
                        <div class="ui icon button <?php echo $classNext; ?>" data-content="Mundurkan" onclick="reposisiData('<?php echo $no; ?>','<?php echo $sasarNext; ?>', '<?php echo $bebas; ?>', 'interface/set-learn-level-reposition.php')">
                            <i class="down chevron icon"></i>
                        </div>
<?php        
    }
?>
                        <div class="ui icon button" data-content="Edit" onclick="loadForm('set-learn-level','<?php echo $idTingkat; ?>')">
                            <i class="pencil alternate icon"></i>
                        </div>
                        <div class="ui icon red button" data-content="Hapus" onclick="tampilkanKonfirmasi('<?php echo $idTingkat; ?>','Hapus data','Yakin ingin menghapus tingkat belajar ?<br><br><br>Karyawan yang memiliki tingkat belajar yang telah dihapus direset tingkat belajarnya','interface/set-learn-level-delete.php')">
                            <i class="trash alternate icon"></i>
                        </div>
                    </td>
                </tr>
<?php            
                }                
            }
?>                            
            </tbody>
        </table>
<?php
    if($c>1){
?>
        <div class="ui message">
            <p>Nomor urut yang lebih besar dapat mengakses materi pada nomor urut yang lebih kecil, namun tidak sebaliknya.</p>
        </div>
<?php        
    }
?>        
    </div>







    <div class="ui blue segment">
        <h4 class="ui header">
            <i class="code branch icon"></i> 
            <div class="content">
                Grade
                <div class="sub header">
                    Kategori nilai ujian
                </div>
            </div>
        </h4>
        <div class="ui divider"></div>
        <div class="ui basic vertical segment clearing" style="margin: 0px; padding: 0px;">
            <div class="ui icon button blue right floated" onclick="loadForm('set-grade','0')">
                <i class="plus icon"></i> Tambah
            </div>
        </div>

        <table class="ui very basic table striped selectable unstackable">
            <thead>
                <tr>
                    <th rowspan="2" width="4%">No</th>
                    <th colspan="2">Nilai</th>
                    <th rowspan="2" width="26%">Grade</th>
                    <th rowspan="2" width="26%">Opsi</th>    
                </tr>
                <tr>
                    <th>Awal</th>
                    <th>Sampai</th>
                </tr>
            </thead>
            <tbody>
<?php
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
?>
                <tr>
                    <td colspan="5">
                        <i class="info circle teal icon"></i> <i>Belum ada data.</i>
                    </td>
                </tr>
<?php
        }
        else{
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
                    <td><?php echo $i+1; ?></td>
                    <td>
                        <?php echo $min; ?>
                    </td>
                    <td>
                        <?php echo $max; ?>
                    </td>
                    <td>
                        <?php echo $huruf; ?>
                    </td>
                    <td>
                        <div class="ui icon button" data-content="Edit" onclick="loadForm('set-grade','<?php echo $idGrade; ?>')">
                            <i class="pencil alternate icon"></i>
                        </div>
                        <div class="ui icon red button" data-content="Hapus" onclick="tampilkanKonfirmasi('<?php echo $idGrade; ?>','Hapus data','Yakin ingin menghapus grade ?','interface/set-grade-delete.php')">
                            <i class="trash alternate icon"></i>
                        </div>
                    </td>
                </tr>
<?php            
                }                
            }
?>                            
            </tbody>
        </table>
<?php
    if($c>0){
?>
        <div class="ui message">
            <p>
                Nilai <strong>Grading</strong> harus mencakup semua nilai yang ada (1-100). Peserta ujian nasional dengan nilai < nilai minimal lulus <strong><i>harus remidi</i></strong>.</p>
        </div>
<?php        
    }
?>                
    </div>








    <div class="ui purple segment">
        <h4 class="ui header">
            <i class="map signs icon"></i>
            <div class="content">
                Kelompok wilayah
                <div class="sub header">
                    Standar Persentase Kelulusan UN
                </div>
            </div>
        </h4>
        <div class="ui divider"></div>
        <div class="ui basic vertical segment clearing" style="margin: 0px; padding: 0px;">
            <div class="ui icon button purple right floated" onclick="loadForm('set-wilayah-kelompok','0')">
                <i class="plus icon"></i> Tambah
            </div>
        </div>

        <table class="ui very basic table striped selectable unstackable">
            <thead>
                <th width="4%">No</th>
                <th>Kelompok</th>
                <th width="4%">Standar</th>
                <th width="26%">Opsi</th>
            </thead>
            <tbody>
<?php
        //regional set
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

                ORDER BY
                    nama ASC
        ";
        $e = mysqli_query($conn, $q);
        $c = mysqli_num_rows($e);
        if($c=='0'){
?>
                <tr>
                    <td colspan="3">
                        <i class="info circle teal icon"></i> <i>Belum ada data.</i>
                    </td>
                </tr>
<?php
        }
        else{
            $ar = array();
            $r = array();

            while ($d = mysqli_fetch_assoc($e)) {
                $r['id']    = $d['id'];
                $r['nama']    = $d['nama'];
                $r['deskripsi']    = $d['deskripsi'];
                $r['standar']    = $d['standar'];

                $ar[]   = $r;
            }

            $jar = $c-1;

            for ($i=0; $i <= $jar; $i++) { 
                $idKelWil = $ar[$i]['id'];
                $standar = $ar[$i]['standar'];
                $nama = $ar[$i]['nama'];
                $deskripsi = $ar[$i]['deskripsi'];
?>
                <tr>
                    <td><?php echo $i+1; ?></td>
                    <td>
                        <h4 class="ui header">
                            <?php echo $nama; ?>
                            <div class="sub header">
                                <?php echo $deskripsi; ?>
                            </div>
                        </h4>
                    </td>
                    <td>
                        <?php echo $standar; ?>%
                    </td>
                    <td>
                        <div class="ui icon button" data-content="Edit" onclick="loadForm('set-wilayah-kelompok','<?php echo $idKelWil; ?>')">
                            <i class="pencil alternate icon"></i>
                        </div>
                        <div class="ui icon red button" data-content="Hapus" onclick="tampilkanKonfirmasi('<?php echo $idKelWil; ?>','Hapus data','Yakin ingin menghapus kelompok wilayah ?','interface/set-wilayah-kelompok-delete.php')">
                            <i class="trash alternate icon"></i>
                        </div>
                    </td>
                </tr>
<?php            
                }                
            }
?>                            
            </tbody>
        </table>
    </div>





    <div class="ui green segment">
        <h4 class="ui header">
            <i class="sitemap icon"></i> Wilayah & Supervisi
        </h4>
        <div class="ui divider"></div>

        <div class="ui basic vertical segment clearing" style="margin: 0px; padding: 0px;">
            <div class="ui icon button right floated" onclick="updateRowW()" data-content="Reload">
                <i class="redo icon"></i>
            </div>
            <div class="ui icon button green right floated" onclick="loadForm('set-wilayah','0')">
                <i class="plus icon"></i> Tambah
            </div>

            <div class="ui icon input">
                <input id="searchData" placeholder="Cari Data.." type="text" />
                <i class="search icon"></i>
            </div>
        </div>



        <table class="ui very basic table striped selectable unstackable">
            <thead>
                <th width="4%">No</th>
                <th>Wilayah</th>
                <th width="26%">Opsi</th>
            </thead>
            <tbody id="resultData">
                <!-- load data here -->
                <tr>
                    <td colspan="3">
                        <i class="info circle icon"></i> <i>Load Data..</i>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3">
                        <div class="ui vertical basic segment clearing" style="padding: 0px;">
                            <select class="ui dropdown compact" id="jumlahRow"  onchange="updateRowW()">
                                <option value="35">35 Baris</option>
                                <option value="50">50 Baris</option>
                                <option value="100">100 Baris</option>
                            </select>

                            <div class="ui right floated pagination menu" id="pageNumber">
                                <!-- show row -->
                                <div class="active item">0</div>
                            </div>
                        </div>
                    </th>
                </tr>
            </tfoot>
        </table>

        <input type="hidden" id="lastPage" value="0">
    </div>


    <div class="ui blue segment">
        <h4 class="ui header">
            <i class="calendar outline check icon"></i> Periode
        </h4>
        <div class="ui divider"></div>

        <div class="ui basic vertical segment clearing" style="margin: 0px; padding: 0px;">
            <div class="ui icon button blue right floated" onclick="tampilkanKonfirmasi('1','Tambah periode','Yakin ingin menambah periode ?','interface/set-periode-tambah.php')">
                <i class="calendar checked icon"></i> Tambah
            </div>
        </div>

        <table class="ui very basic table striped selectable unstackable">
            <thead>
                <th width="4%">No</th>
                <th>Priode</th>
                <th width="26%">Opsi</th>
            </thead>
            <tbody>
<?php
        $tahun = date('Y');
        $maks = $tahun+2;
        //set for lembaga
        $q = "
                SELECT 
                    id, 
                    nama, 
                    aktif 
                FROM 
                    periode 
                ORDER BY
                    id DESC
        ";
        $e = mysqli_query($conn, $q);
        $c = mysqli_num_rows($e);
        if($c=='0'){
?>
                <tr>
                    <td colspan="3">
                        <i class="info circle teal icon"></i> <i>Belum ada data.</i>
                    </td>
                </tr>
<?php
        }
        else{
            $ar = array();
            $r = array();

            while ($d = mysqli_fetch_assoc($e)) {
                $r['id']    = $d['id'];
                $r['nama']    = $d['nama'];
                $r['aktif']    = $d['aktif'];

                $ar[]   = $r;
            }

            $jar = $c-1;

            for ($i=0; $i <= $jar; $i++) { 
                $idPeriode = $ar[$i]['id'];
                $nama = $ar[$i]['nama'];
                $aktif = $ar[$i]['aktif'];
?>
                <tr>
                    <td><?php echo $i+1; ?></td>
                    <td>
                        <?php echo $nama; ?>
                    </td>
                    <td>
<?php
                    if($aktif=='1'){
?>
                        <div class="ui icon circular green button" data-content="Aktif">
                            <i class="check icon"></i>
                        </div>
<?php        
                    }
                    else{
?>
                        <div class="ui icon button" data-content="Aktifkan" onclick="tampilkanKonfirmasi('<?php echo $idPeriode; ?>','Aktifkan periode','Yakin ingin mengaktifkan periode ?','interface/set-periode-aktif.php')">
                            <i class="check icon"></i>
                        </div>
<?php                
                    }

                    if($idPeriode>=$maks&&$aktif=='0'){
?>
                        <div class="ui icon orange button" data-content="Hapus" onclick="tampilkanKonfirmasi('<?php echo $idPeriode; ?>','Hapus data','Yakin ingin menghapus periode ?','interface/set-periode-delete.php')">
                            <i class="trash icon"></i>
                        </div>
<?php                        
                    }
?>                        
                    </td>
                </tr>
<?php            
                }                
            }
?>                            
            </tbody>
        </table>
    </div>

</div>





<div id="formDisplay" style="display: none;">
    <!-- load form here -->
</div>


<script type="text/javascript">
    $('.button').popup();
    $('.dropdown').dropdown();

    updateRowW();

    function updateRowW(){
        dataListW();
        showRowW();
    }
    
    function dataListW(){
        loadingMulai();
        var start, limit, key;
        start = $('#lastPage').val();
        limit = $("#jumlahRow").val();
        key = $("#searchData").val();

        $.post('interface/set-wilayah-list.php',{view:'1', start: start, limit: limit, cari: key},
            function(result){
                $("#resultData").html(result);
                loadingSelesai();
            }
        );
    }

    function showRowW(){
        loadingMulai();
        var limit, key;
        limit = $("#jumlahRow").val();
        key = $("#searchData").val();

        $.post('interface/set-wilayah-list-number.php',{view:'1', limit: limit, cari: key},
            function(result){
                $("#pageNumber").html(result);
                loadingSelesai();
            }
        );
    }


    function updateListW(start, id){
        $('#lastPage').val(start);
        $("#pageNumber a").removeClass("active");
        dataListW();
        $("#number"+id).addClass("active");
    }

    
    $("#searchData").keyup(function(event){
        if(event.keyCode == 13){
            updateRowW();
            $('#lastPage').val('0');
        }
    });
</script>