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

    $idPengguna = $_SESSION['idPengguna'];

	if(is_numeric($start) AND is_numeric($limit)){
		$q="
			SELECT 
                t.id idPenargetan,
                t.susulan,
                t.susulan_tgl,

                up.id idP, 
                up.tanggal, 
                up.waktu, 
                up.kkm,
                up.kode,

                u.id id_ujian,
                u.nama namaUjian, 
                u.deskripsi deskUjian,
                u.tipe,

                ku.mulai,
                ku.selesai

            FROM
                ujian_pelaksanaan_target_karyawan t 
            
            LEFT JOIN
                ujian_pelaksanaan up
            ON
                t.id_pelaksanaan = up.id

            LEFT JOIN
                ujian u
            ON
                up.id_ujian = u.id

            LEFT JOIN
                karyawan_ujian ku
            ON
                t.id = ku.id

            WHERE
                t.hapus = '0'
            AND
                t.id_karyawan = '$idPengguna'
            AND
                up.hapus = '0'
            AND
                up.aktif = '1'
            AND
                up.id_periode = '$idPeriode'
            AND
                up.id
            NOT IN
            (
                SELECT
                    id_pelaksanaan

                FROM
                    karyawan_ujian

                WHERE
                    id_karyawan = '$idPengguna'
                AND
                    selesai != ''
                AND
                    hapus = '0'
            )
		";

		if($cari!==''){
			$q.="
					AND 
					(
                    OR
                        up.tanggal LIKE '%$cari%'
                    OR
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
					up.tanggal DESC
                    
				LIMIT 
					$limit 
				OFFSET 
					$start
		";
	}
	else{
?>
        <div class="ui message">
            <p>
                <i class="circle info icon teal"></i> <i>Parameter limit dan offset harus angka.</i>      
            </p>
        </div>
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
            $r['idP']            = $d['idP'];
            $r['tanggal']        = $d['tanggal'];
            $r['waktu']          = $d['waktu'];
            $r['kkm']          = $d['kkm'];

            $r['kode']          = $d['kode'];
    
            $r['id_ujian']      = $d['id_ujian'];            
            $r['namaUjian']      = $d['namaUjian'];
            $r['deskUjian']    = $d['deskUjian'];
            $r['tipe']          = $d['tipe'];

            $r['mulai']          = $d['mulai'];
            $r['selesai']          = $d['selesai'];
            
            $r['susulan']        = $d['susulan'];
            $r['susulan_tgl']        = $d['susulan_tgl'];

            $ar[]   = $r;
        }

        $cAr = $c-1;

        $nomor = $start+1;

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

            $namaUjian = $ar[$i]['namaUjian'];
            $deskUjian = '';

            $kode = $ar[$i]['kode'];

            $tipe = $ar[$i]['tipe'];

            $mulai = $ar[$i]['mulai'];
            $selesai = $ar[$i]['selesai'];

            $teksKerja = 'Kerjakan';
            if(!empty($mulai) && $selesai == ''){
                $teksKerja = 'Lanjutkan';
            }

            $susulan = $ar[$i]['susulan'];
            $susulan_tgl = $ar[$i]['susulan_tgl'];

            if($susulan=='1'){
                if($sekarang<$susulan_tgl){
                    //masih menunggu
                    $classBtn = 'yellow disabled';
                }
                else if($sekarang==$susulan_tgl){
                    //saat pengerjaan
                    $classBtn = 'primary';
                }
                else{
                    //terlewat
                    $classBtn = 'red disabled';
                }
            }
            else{
                 if($sekarang<$tanggal){
                    //masih menunggu
                    $classBtn = 'yellow disabled';
                }
                else if($sekarang==$tanggal){
                    //saat pengerjaan
                    $classBtn = 'primary';
                }
                else{
                    //terlewat
                    $classBtn = 'red disabled';
                }
            }

            

            $id_ujian = $ar[$i]['id_ujian'];

            if($id_ujian == '5483-8ABF1C'){
                //unas
                $jmlSoal = 50;
            }
            else{
                $jmlSoal = 20;
            }

?>
            <div class="item">
                <div class="image">
                    <img class="ui image" src="../files/photo/cup.png">
                </div>
                <div class="content">
                    <a class="header"><?php echo $namaUjian; ?></a>
                    <div class="meta">
                        <span><?php echo $tglUjian; ?></span>
                    </div>
                    <div class="description">
                        <p>
                            <?php echo $deskUjian; ?>
                        </p>
<?php
    if($sekarang > $tanggal && $sekarang > $susulan_tgl){
?>
                        <p>
                            Mintalah kepada <i><strong>superadmin</strong></i> untuk reschedule pengerjaan.
                        </p>
<?php
    }
    else{
?>                        
                        <p>
                            Nilai akhir minimal untuk lulus: <strong><?php echo $kkm; ?></strong>
                        </p>
<?php
    }
?>                        
                    </div>
                    <div class="extra">
                        <a href="../test/?c=<?php echo $kode; ?>" class="ui right floated button <?php echo $classBtn; ?>">
                            <?php echo $teksKerja; ?> <i class="right chevron icon"></i>
                        </a>
<?php
    if($sekarang>$tanggal && $sekarang > $susulan_tgl){
?>
                        <div class="ui red label tiny">Terlewat</div>
<?php
    }
    else{
?>
                        <div class="ui orange label tiny"><?php echo $waktu.' (menit)'; ?></div>
                        <div class="ui teal label tiny"><?php echo $jmlSoal.' (soal)'; ?></div>
<?php        
    }
?>
                    </div>
                </div>
            </div>            

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
        <div class="ui message">
            <p>
                <i class="info circle icon teal"></i> <i> <?php echo $teksKosong; ?> </i>      
            </p>
        </div>
<?php    	
    }

?>





<script type="text/javascript">
	$('.button, .popup').popup();
</script>