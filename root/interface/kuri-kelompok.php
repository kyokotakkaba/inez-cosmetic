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
    <div class="ui green icon button right floated" onclick="loadForm('kuri-kelompok','0')">
        <i class="plus icon"></i> Kelompok
    </div>
</div>

<table class="ui striped selectable table">
    <thead>
        <th width="4%">No</th>
        <th>Kelompok</th>
        <th width="30%">Opsi</th>
    </thead>
    <tbody>
<?php
//set for lembaga
$q = "
        SELECT 
            id, 
            nama, 
            deskripsi,
            n_pk

        FROM 
            materi_kelompok

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
        $r['deskripsi']    = $d['deskripsi'];
        $r['nama']    = $d['nama'];
        $r['n_pk']    = $d['n_pk'];

        $ar[]   = $r;
    }

    $jar = $c-1;
    $no = 1;

    for ($i=0; $i <= $jar; $i++) {
        $idKi = $ar[$i]['id'];
        $deskripsi = $ar[$i]['deskripsi'];
        $nama = $ar[$i]['nama'];
        $n_pk = $ar[$i]['n_pk'];
        if($n_pk=='1'){
            $label = ' &nbsp; <span class="ui blue tiny label">PK</span>';
        }
        else{
            $label = '';
        }
?>
        <tr>
            <td><?php echo $no; ?></td>
            <td><?php echo $nama .' '.$label; ?></td>
            <td>
                <div class="ui icon tiny button" data-content="Edit" onclick="loadForm('kuri-kelompok','<?php echo $idKi; ?>')">
                    <i class="pencil alternate icon"></i>
                </div>
                <div class="ui icon tiny button red" data-content="Hapus" onclick="tampilkanKonfirmasi('<?php echo $idKi; ?>','Hapus kelompok','Yakin ingin menghapus data kelompok materi belajar ?<br></br>*Semua data bahasan dan materi yang ada pada kelompok ini serta riwayat belajar karyawan terkait juga akan dihapus.','interface/kuri-kelompok-delete.php')">
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