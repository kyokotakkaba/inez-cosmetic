<?php
    session_start();
    $appSection = 'admin';

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
	
	$start 	= saring($_POST['start']);
	$limit 	= saring($_POST['limit']);
	$cari 	= saring($_POST['cari']);

    $id_periode   = saring($_POST['id_periode']);
    $id_ujian   = saring($_POST['jenis_ujian']);

    $idPengguna = $_SESSION['idPengguna'];

	if(is_numeric($start) AND is_numeric($limit)){
        $q = "
            SELECT 
                ku.id, 
                ku.tanggal mengerjakan, 
                ku.mulai, 
                ku.selesai, 
                ku.nilai_akhir, 
                ku.n, 
                ku.n_pk, 
                ku.nilai_grade,
                ku.n_poin, 
                ku.remidi,
                ku.remidi_karena,

                up.id_ujian,
                up.kkm, 
                up.tanggal jadwal, 
                up.waktu, 
                up.tampilan, 
                up.aktif, 
                up.kode,

                u.nama namaUjian

            FROM 
                karyawan_ujian ku

            LEFT JOIN
                ujian_pelaksanaan up
            ON
                ku.id_pelaksanaan = up.id

            LEFT JOIN 
                ujian u
            ON
                up.id_ujian = u.id
            
            WHERE
                ku.id_karyawan = '$idPengguna'
            AND
                ku.selesai != ''
            AND
                ku.hapus = '0'
            AND
                up.hapus = '0'
        ";

        if($id_periode !== 'semua'){
            $q .= "
                    AND
                        up.id_periode = '$id_periode'
            ";
        }

        if($id_ujian !== 'semua'){
            $q .= "
                    AND
                        up.id_ujian = '$id_ujian'
            ";
        }

        if($cari!==''){
            $q.="
                    AND 
                    (
                        up.tanggal LIKE '%$cari%'
                    OR
                        ku.tanggal LIKE '%$cari%'
                    )
            ";
        }

        $q.="   
                ORDER BY
                    up.id_periode DESC,
                    ku.tanggal DESC,
                    ku.mulai DESC
                    
                LIMIT 
                    $limit 
                OFFSET 
                    $start
        ";
    }
    else{
?>
        <tr>
            <td colspan="7">
                <i class="circle info icon teal"></i> <i>Parameter limit dan offset harus angka.</i>
            </td>
        </tr>
<?php       
        exit();
    }

    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);

    if($c>0){
        
        $sekarang = date('Y-m-d');

        $ar = array();
        $r = array();

        while ($d = mysqli_fetch_assoc($e)) {
            $r['id']            = $d['id'];
            $r['mengerjakan']        = $d['mengerjakan'];
            $r['mulai']          = $d['mulai'];
            $r['selesai']          = $d['selesai'];
            $r['nilai_akhir']          = $d['nilai_akhir'];
            $r['n']          = $d['n'];
            $r['n_pk']          = $d['n_pk'];
            $r['n_poin']          = $d['n_poin'];
            $r['nilai_grade']          = $d['nilai_grade'];
            $r['remidi']          = $d['remidi'];
            $r['remidi_karena']          = $d['remidi_karena'];
            
            $r['jadwal']      = $d['jadwal'];
            $r['namaUjian']    = $d['namaUjian'];

            $r['id_ujian']    = $d['id_ujian'];

            $ar[]   = $r;
        }

        $cAr = $c-1;

        $nomor = $start+1;

        for ($i=0; $i <= $cAr; $i++) {
            $idData = $ar[$i]['id'];
            $mengerjakan = $ar[$i]['mengerjakan'];
            $jadwal = $ar[$i]['jadwal'];
            $namaUjian = $ar[$i]['namaUjian'];

            if($mengerjakan>$jadwal){
                $ketTgl = tanggalKan($mengerjakan).' [Susulan]';
            }
            else{
                $ketTgl = tanggalKan($mengerjakan);
            }

            if($mengerjakan==$sekarang){
                $tglUjian = 'Hari ini';
            }

            $mulai = $ar[$i]['mulai'];
            $selesai = $ar[$i]['selesai'];
            $nilai_akhir = $ar[$i]['nilai_akhir'];
            $n = $ar[$i]['n'];
            $n_pk = $ar[$i]['n_pk'];
            $nilai_grade = $ar[$i]['nilai_grade'];
            $remidi = $ar[$i]['remidi'];
            if($remidi=='1'){
                $classTr = 'negative';
            }
            else{
                $classTr = '';
            }

            $mulai = $ar[$i]['id_ujian'];
?>
            <tr class="<?php echo $classTr ?>">
                <td><?php echo $nomor; ?></td>
                <td>
                    <h4 class="ui header">
                        <?php echo $namaUjian; ?>
                        <div class="sub header">
                            <?php echo $ketTgl ?>
                        </div>
                    </h4>
                </td>
                <td><?php echo $n_pk; ?></td>
                <td><?php echo $n; ?></td>
                <td><?php echo $nilai_akhir; ?></td>
                <td>
                    <?php echo $nilai_grade; ?>
<?php
    //if UN
    if(strtolower($namaUjian) == 'ujian nasional'){
        $n_poin = $ar[$i]['n_poin'];
?>
                    <br><span class="ui label basic" style="font-size: 7pt;"><?php echo $n_poin; ?></span> <i class="info circle icon popup" data-content="Poin tambahan dari rerata 3 Ujian Bulanan sebelumnya. Pembulatan Nilai akhir maksimal tetap 100."></i>
<?php        
    }
?>                    
                </td>
                <td>
                    <div class="ui icon button primary" data-content="Detail" onclick="loadForm('test-history','<?php echo $idData; ?>')">
                        <i class="list icon"></i>
                    </div>
                </td>
            </tr>
<?php            
            $nomor = $nomor+1;            
        }
    }
    else{
        if($cari==''){
            $teksKosong = 'Belum ada data.';
        }
        else{
            $teksKosong = "Data dengan kata kunci <strong>".$cari."</strong> tidak ditemukan.";
        }
?>
        <tr>
            <td colspan="7">
                <i class="info circle icon teal"></i> <i> <?php echo $teksKosong; ?> </i>
            </td>
        </tr>
<?php       
    }

?>





<script type="text/javascript">
    $('.button, .popup').popup();
</script>