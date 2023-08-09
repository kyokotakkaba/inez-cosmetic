<?php
    ob_start();
    session_start();

    $appSection = 'root';

    $fromHome = '../../../../';

    if(empty($_SESSION['idPengguna'])){
        header('location: '.$fromHome);
        exit();
    }

    $jenisPengguna = $_SESSION['jenisPengguna'];
    if($jenisPengguna !== $appSection){
        header('location: '.$fromHome.''.$jenisPengguna);
        exit();
    }

    require_once $fromHome.'conf/function.php';
?>

        <div class="ui basic vertical segment container form">
<?php
    
    $q="
			SELECT 
               *

            FROM 
                laporan_pembelajaran
			
			ORDER BY 
				user_id, id_modul ASC
		";	

    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);

    $ar = array();
    $r = array();

    while ($d = mysqli_fetch_assoc($e)) {
        $r['user_id']            = $d['user_id'];
        $r['nip']      	    = $d['nip'];
        $r['area']          = $d['area'];
        $r['id_modul']            = $d['id_modul'];
        $r['nama_modul']     = $d['nama_modul'];
        $r['jumlah_modul']          = $d['jumlah_modul'];
        $r['selesai_lembar']          = $d['selesai_lembar'];
        $r['terakhir_dibuka']      = $d['terakhir_dibuka'];
    
        $ar[]   = $r;
    }

    header('Content-Type: application/vnd.ms-excel');
    header('Content-disposition: attachment; filename=Laporan-Pembelajaran.xls');
    ob_get_clean();
    header('Pragma: no-cache');
    header('Expires: 0');
?>
    <table>
        <thead>
            <th width="20%">USER ID</th>
            <th width="20%">NIP</th>
            <th width="20%">AREA</th>
            <th width="20%">ID MODUL</th>
            <th width="20%">NAMA MODUL</th>
            <th width="20%">JUMLAH MODUL</th>
            <th width="20%">SELESAI</th>
            <th width="20%">TERAKHIR DIBUKA</th>
        </thead>
        <tbody>
<?php
    if($c==0){
?>
            <tr>
               <td>
                   Belum ada data.
               </td> 
            </tr>
<?php
        exit();
    }

    $cAr = $c-1;


    for ($i=0; $i <= $cAr; $i++) {        
?>
    <tr>
        <td><?php echo $ar[$i]['user_id']; ?></td>
        <td><?php echo $ar[$i]['nip']; ?></td>
        <td><?php echo $ar[$i]['area']; ?></td>
        <td><?php echo $ar[$i]['id_modul']; ?></td>
        <td><?php echo $ar[$i]['nama_modul']; ?></td>
        <td><?php echo $ar[$i]['jumlah_modul']; ?></td>
        <td><?php echo $ar[$i]['selesai_lembar']; ?></td>
        <td><?php echo $ar[$i]['terakhir_dibuka']; ?></td>
    </tr>
<?php          
    }
?>
        </tbody>
</table> 