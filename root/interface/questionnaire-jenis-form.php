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
    $sub = 'Jenis Survey';

    $idData = saring($_POST['idData']);

?>
<div id="subDisplay">
    <div class="ui message">
        <div class="ui breadcrumb">
            <div class="section"><?php echo $main; ?></div>
            <i class="right angle icon divider"></i>
            <div class="active section"><?php echo $sub; ?></div>
        </div>
    </div>
    <div class="ui floating message">
      <p>Kelola jenis survey (kelompok survey berdasarkan tujuan)</p>
    </div>
    <div class="field">
        <div class="ui icon button" onclick="backToMain()">
            <i class="left chevron icon"></i> Kembali
        </div>    
    </div>

    <div class="ui basic vertical segment clearing" style="margin: 0px; padding: 0px;">
        <div class="ui button green right floated" onclick="loadFormSub('isi','0')">
            <i class="edit icon"></i> Tambah
        </div>
    </div>

    <table class="ui striped selectable table">
        <thead>
            <tr>
                <th width="4%" rowspan="2">No</th>
                <th>Jenis</th>
                <th width="25%">Opsi</th>
            </tr>
        </thead>
        <tbody>
<?php
    $q = "
            SELECT 
                id, 
                nama, 
                deskripsi 
            FROM 
                angket_kategori 
            WHERE
                hapus = '0'
    ";

    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);
    
    if($c=='0'){
?>
            <tr>
                <td colspan="3">
                    <i class="ui info icon teal cirle"></i> <i>Belum ada data.</i>
                </td>
            </tr>
<?php        
    }
    else{
        $no = 1;
        while ($r = mysqli_fetch_assoc($e)) {
            $idCat = $r['id'];
            $nama = $r['nama'];
            $deskripsi = $r['deskripsi'];
?>
            <tr>
                <td><?php echo $no; ?></td>
                <td>
                    <div class="ui header">
                        <?php echo $nama; ?>
                        <div class="sub header">
                            <?php echo $deskripsi; ?>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="ui icon button" data-content="Edit" onclick="loadFormSub('isi','<?php echo $idCat; ?>')">
                        <i class="pencil alternate icon"></i>
                    </div>
                    <div class="ui icon button red" data-content="Hapus" onclick="tampilkanKonfirmasi('<?php echo $idCat; ?>','Hapus data','Yakin ingin menghapus data jenis angket ?<br><br><br>*Data respon yang telah terkumpul dengan jenis survey ini tidak akan dihapus.','interface/questionnaire-jenis-delete.php')">
                        <i class="trash alternate icon"></i>
                    </div>
                </td>
            </tr>
<?php     
        $no = $no+1;       
        }
    }
?>            
        </tbody>
    </table>
</div>

<div id="subForm" style="display: none;">
    <!-- load other page here -->
</div>



<script type="text/javascript">
    $('.dropdown').dropdown();
    $('.button').popup();
</script>