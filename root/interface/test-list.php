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
	
	$start 	= saring($_POST['start']);
	$limit 	= saring($_POST['limit']);
	$cari 	= saring($_POST['cari']);

    $id_periode   = saring($_POST['id_periode']);
    $id_ujian   = saring($_POST['jenis_ujian']);

	if(is_numeric($start) AND is_numeric($limit)){
		$q="
			SELECT 
                up.id idP, 
                up.tanggal, 
                up.waktu, 
                up.kkm,
                up.tampilan, 
                up.aktif,
                up.kode,
                up.tgl_aktif,
                up.waktu_aktif,

                u.nama namaUjian, 
                u.deskripsi deskUjian,
                u.tipe,

                (
                    SELECT 
                        COUNT(id)
                    FROM 
                        ujian_pelaksanaan_target_karyawan 
                    WHERE 
                        id_pelaksanaan = up.id
                    AND
                        hapus = '0'
                ) semua,

                (
                    SELECT 
                        COUNT(id)
                    FROM 
                        karyawan_ujian 
                    WHERE
                        id_pelaksanaan = up.id
                    AND
                        hapus = '0'
                    AND
                        mulai !=''
                ) mengerjakan

            FROM 
                ujian_pelaksanaan up

            LEFT JOIN
                ujian u
            ON
                up.id_ujian = u.id

            WHERE
                up.hapus = '0'
		";

        if($id_periode!=='semua'){
            $q.="
                AND
                    up.id_periode = '$id_periode'
            ";
        }

        if($id_ujian!=='semua'){
            $q.="
                AND
                    up.id_ujian = '$id_ujian'
            ";
        }

		if($cari!==''){
			$q.="
					AND 
					(
                    OR
                        up.tanggal LIKE '%$cari%'
						s.nama LIKE '%$cari%'
					OR
						s.deskripsi LIKE '%$cari%'
					OR
						u.nama LIKE '%$cari%'
					OR
						u.deskripsi LIKE '%$cari%'
					)
			";
		}

		$q.="	
				ORDER BY 
					up.tanggal DESC,
                    up.aktif ASC,
                    up.tgl_aktif DESC,
                    up.waktu_aktif DESC
                    
				LIMIT 
					$limit 
				OFFSET 
					$start
		";	
	}
	else{
?>
		<tr>
			<td colspan="6">
				<i class="circle info icon teal"></i> <i>Parameter limit dan offset harus angka.</i>
			</td>
		</tr>
<?php		
		exit();
	}

	$e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);


    if($c>0){
        $ar = array();
        $r = array();

        while ($d = mysqli_fetch_assoc($e)) {
            $r['idP']            = $d['idP'];
            $r['tanggal']        = $d['tanggal'];
            $r['waktu']          = $d['waktu'];
            $r['kkm']          = $d['kkm'];
            $r['tampilan']       = $d['tampilan'];
            $r['aktif']          = $d['aktif'];
            $r['kode']          = $d['kode'];
            
            $r['namaUjian']      = $d['namaUjian'];
            $r['deskUjian']    = $d['deskUjian'];
            $r['tipe']    = $d['tipe'];
            
            $r['semua']        = $d['semua'];
            $r['mengerjakan']        = $d['mengerjakan'];

            $r['tgl_aktif']        = $d['tgl_aktif'];
            $r['waktu_aktif']        = $d['waktu_aktif'];
            
            $ar[]   = $r;
        }

        $cAr = $c-1;

        $nomor = $start+1;

        $sekarang = date('Y-m-d');

        for ($i=0; $i <= $cAr; $i++) {
            $idData = $ar[$i]['idP'];
            $tanggal = $ar[$i]['tanggal'];
            if($tanggal==$sekarang){
                $tglUjian = 'Hari ini';
            }
            else{
                $tglUjian = tanggalKan($tanggal);
            }


            $kkm = $ar[$i]['kkm'];
            $waktu = $ar[$i]['waktu'];
            $tampilan = $ar[$i]['tampilan'];
            $aktif = $ar[$i]['aktif'];
            $kode = $ar[$i]['kode'];

            $namaUjian = $ar[$i]['namaUjian'];
            if(strtolower(trim($namaUjian))=='ujian bulanan'){
                $ikon = 'calendar alternate outline icon';
            }
            else{
                $ikon = 'calendar check outline icon';
            }

            if($aktif=='1'){
                $headWarna = 'teal';
                $ikon .= ' teal';
                $ikonAktif = 'check teal';
            }
            else{
                $headWarna = '';
                $ikonAktif = 'ban';
            }

            $deskUjian = $ar[$i]['deskUjian'];
            $tipe = $ar[$i]['tipe'];
            
            $semua = $ar[$i]['semua'];
            $mengerjakan = $ar[$i]['mengerjakan'];
?>
			<tr>
				<td><?php echo $nomor; ?></td>
				<td>
                    <h4 class="ui header <?php echo $headWarna; ?>">
                        <i class="<?php echo $ikon; ?> icon"></i>
                        <div class="content">
                            <?php echo $namaUjian; ?>
                            <div class="sub header">
                                
                                <p>
                                    Pada : <?php echo $tglUjian; ?><br>
                                    Waktu : <?php echo $waktu.' (menit)'; ?><br>
                                    Nilai Min. : <?php echo $kkm; ?>
                                </p>
                            </div>
                        </div>
                    </h4>
				</td>
<?php
    if($semua=='0'){
?>
                <td colspan="2">
                    Belum ada data.
                </td>
<?php        
    }
    else{
?>
                <td><?php echo $semua; ?></td>
                <td><?php echo $mengerjakan; ?></td>
<?php
    }
?>                
                <td>
                    <i class="<?php echo $ikonAktif; ?> icon"></i>
                </td>
				<td>
<?php
    if($aktif=='0'){
?>
                    <div class="ui icon button teal" data-content="Aktifkan" onclick="tampilkanKonfirmasi('<?php echo $idData; ?>','Aktifkan','Yakin ingin mengaktifkan ujian ?','interface/test-activate.php')">
                        <i class="check icon"></i>
                    </div>
<?php
    }
?>                    
                    <div class="ui icon blue button" data-content="Kelola" onclick="loadForm('test-manage','<?php echo $idData; ?>')">
                        <i class="list icon"></i>
                    </div>

<?php
    if($aktif=='1'){
?>
                    <a class="ui icon button" data-content="Laporan" href="report/test/?kode=<?php echo $kode; ?>" target="_BLANK" >
                        <i class="print icon"></i>
                    </a>
<?php
        if($mengerjakan > 0){
?>
                    <a class="ui icon green button" data-content="Unduh Excel" href="report/test/export/?kode=<?php echo $kode; ?>" target="_BLANK" >
                        <i class="cloud download icon"></i>
                    </a>
<?php            
        }
    }

    if($aktif=='0'){
?>
                    <div class="ui icon button" data-content="Edit" onclick="loadForm('test-<?php echo $tipe; ?>','<?php echo $idData; ?>')">
                        <i class="pencil alternate icon"></i>
                    </div>
                    <div class="ui icon button red" data-content="Hapus" onclick="tampilkanKonfirmasi('<?php echo $idData; ?>','Hapus data','Yakin ingin menghapus data karyawan ?','interface/test-delete.php')">
                        <i class="trash alternate icon"></i>
                    </div>
<?php
    }
?>	                
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
			<td colspan="4">
				<i class="info circle icon teal"></i> <i> <?php echo $teksKosong; ?> </i>
			</td>
		</tr>
<?php    	
    }

?>





<script type="text/javascript">
	$('.button, .popup').popup();
</script>