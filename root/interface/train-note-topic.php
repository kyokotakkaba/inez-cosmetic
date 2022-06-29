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
?>
    <div class="ui basic vertical segment clearing" style="margin: 0px; padding: 0px;">
        <div class="ui green icon button right floated" onclick="loadForm('train-note-topic','0')">
            <i class="plus icon"></i> Tambah
        </div>
    </div>

    <table class="ui striped selectable table">
            <thead>
                <th width="4%">No</th>
                <th>Topik</th>
                <th width="30%">Opsi</th>
            </thead>
            <tbody>
<?php    

    $q = "
            SELECT 
                id, 
                nama

            FROM 
                pelatihan_catatan_topik

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

            $ar[]   = $r;
        }

        $jar = $c-1;

        for ($i=0; $i <= $jar; $i++) {
            $idKi = $ar[$i]['id'];
            $nama = $ar[$i]['nama'];
?>
            <tr>
                <td>
                    <?php echo $i+1; ?>
                </td>
                <td>
                    <?php echo $nama; ?>
                </td>
                <td>
                    <div class="ui icon tiny button" data-content="Edit" onclick="loadForm('train-note-topic','<?php echo $idKi; ?>')">
                        <i class="pencil alternate icon"></i>
                    </div>
                    <div class="ui icon tiny button red" data-content="Hapus" onclick="tampilkanKonfirmasi('<?php echo $idKi; ?>','Hapus Topik','Yakin ingin menghapus data topik untuk catatan pelatihan ?<br><br>Jika catatan  pada karyawan penah diinput dengan topik ini, catatan tsb akan dihapus','interface/train-note-topic-delete.php')">
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

<script type="text/javascript">
    $('.button').popup();
</script>