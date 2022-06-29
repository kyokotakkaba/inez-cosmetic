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
    $sub = 'Jenis Label';

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
      <p>Kelola jenis label (pilihan yang muncul pada setiap pertanyaan survey)</p>
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
                <th>Label</th>
                <th width="25%">Opsi</th>
            </tr>
        </thead>
        <tbody>
<?php
    $q = "
            SELECT 
                id, 
                nama, 
                satu,
                dua,
                tiga,
                empat

            FROM 
                angket_label

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
            $satu = $r['satu'];
            $dua = $r['dua'];
            $tiga = $r['tiga'];
            $empat = $r['empat'];
?>
            <tr>
                <td><?php echo $no; ?></td>
                <td>
                    <h4 class="ui header">
                        <?php echo $nama; ?>
                        <div class="sub header">
                            <?php echo $satu.' - '.$dua.' - '.$tiga.' - '.$empat; ?>
                        </div>
                    </h4>
                </td>
                <td>
                    <div class="ui icon button" data-content="Edit" onclick="loadFormSub('isi','<?php echo $idCat; ?>')">
                        <i class="pencil alternate icon"></i>
                    </div>
                    <div class="ui icon button red" data-content="Hapus" onclick="tampilkanKonfirmasi('<?php echo $idCat; ?>','Hapus data label','Yakin ingin menghapus data label ?<br><br><br>*Data respon yang telah terkumpul dengan jenis label ini tidak akan dihapus.','interface/questionnaire-label-delete.php')">
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