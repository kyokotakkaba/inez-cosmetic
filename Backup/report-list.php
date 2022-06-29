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
    $id_wilayah   = saring($_POST['id_wilayah']);

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
                up.id_ujian,
                up.id_periode,

                u.nama namaUjian, 
                u.deskripsi deskUjian,
                u.tipe

            FROM 
                ujian_pelaksanaan up

            LEFT JOIN
                ujian u
            ON
                up.id_ujian = u.id

            WHERE
                up.hapus = '0'
            AND
                up.aktif = '1'
		";

        if($id_periode!=='semua'){
            $q.="
                AND
                    up.id_periode = '$id_periode'
            ";
        }

        if($id_wilayah!=='' && $id_wilayah!=='all'){
            $q .= "
                AND 
                (
                    SELECT 
                        COUNT(upt.id_karyawan)

                    FROM 
                        ujian_pelaksanaan_target_karyawan upt

                    LEFT JOIN
                        karyawan k
                    ON
                        upt.id_karyawan = k.id

                    WHERE
                        upt.id_pelaksanaan = up.id
                    AND
                        upt.hapus = '0'
                    AND
                        k.id_wil = '$id_wilayah'
                ) > 0
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
					up.tanggal DESC
                    
				LIMIT 
					$limit 
				OFFSET 
					$start
		";
	}
	else{
?>
		<tr>
			<td colspan="4">
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
            $r['kkm']            = $d['kkm'];
            $r['tampilan']       = $d['tampilan'];
            $r['aktif']          = $d['aktif'];
            $r['kode']           = $d['kode'];

            $r['id_periode']    = $d['id_periode'];
            
            $r['namaUjian']     = $d['namaUjian'];
            $r['deskUjian']     = $d['deskUjian'];
            $r['tipe']          = $d['tipe'];

            $r['id_ujian']    = $d['id_ujian'];
            
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

            if($aktif=='1'){
                $headWarna = 'teal';
                $ikon = 'calendar check';
                $ikonAktif = 'check teal';
            }
            else{
                $headWarna = '';
                $ikon = 'archive';
                $ikonAktif = 'ban';
            }

            $id_periode = $ar[$i]['id_periode'];
            $id_ujian = $ar[$i]['id_ujian'];
            $namaUjian = $ar[$i]['namaUjian'];
            $deskUjian = $ar[$i]['deskUjian'];
            $tipe = $ar[$i]['tipe'];
?>
			<tr>
				<td><?php echo $nomor; ?></td>
				<td>
                    <h4 class="ui header">
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
				<td>
<?php
    if($aktif=='1'){
?>
                    <a class="ui icon button" data-content="Laporan" href="report/test/?kode=<?php echo $kode; ?>" target="_BLANK" >
                        <i class="print icon"></i>
                    </a>
                    <a class="ui icon green button" data-content="Unduh Excel" href="report/test/export/?kode=<?php echo $kode; ?>" target="_BLANK" >
                        <i class="cloud download icon"></i>
                    </a>
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

    $kodeBaru = kodeBaru();
    if($id_ujian=='60BF-E0E0'){
        $link = 'monthly';
    }
    else{
        $link = 'yearly';
    }

    if($c>1){
?>
        <tr>
            <td colspan="2">
                Rekab <?php echo $namaUjian; ?> selama periode <?php echo $id_periode; ?> ? 
            </td>
            <td>
                <a class="ui icon button primary" data-content="Laporan" href="report/test-<?php echo $link; ?>/?kode=<?php echo $kodeBaru; ?>&encrypt=<?php echo $id_ujian; ?>&id_periode=<?php echo $id_periode; ?>" target="_BLANK" >
                    <i class="print icon"></i>
                </a>
            </td>
        </tr>
<?php        
    }

    //cek if its UN -> 5483-8ABF1C
    if($id_ujian=='5483-8ABF1C'){
        $q = "
                SELECT
                    id
                FROM
                    periode
                ORDER BY
                    id DESC
        ";
        $e = mysqli_query($conn, $q);
        $c = mysqli_num_rows($e);

        if($c>1){
?>
            <tr>
                <td colspan="3">
                    <div class="field">
                        <label>Rekab Ujian Nasional beberapa periode ?</label>
                    </div>

                    <div class="fields">
                        <div class="eight wide field">
                            <label>Periode</label>
                            <select id="id_periode_sel" class="ui search dropdown fluid" multiple="">
<?php

            if($c==0){
?>
                                <option value="">Kosong</option>
<?php            
            }
            else{
?>
                                <option value="">Pilih</option>
<?php            
                while ($r = mysqli_fetch_assoc($e)) {
                $idP = $r['id'];
?>
                            <option value="<?php echo $idP; ?>"><?php echo $idP; ?></option>
<?php            
                }
            }
?>        
                        </select>
                    </div>
                    <div class="eight wide field">
                        <label>Wilayah</label>
                        <select id="id_wilayah_sel" class="ui search dropdown fluid" multiple="">
<?php
            $q = "
                    SELECT 
                        id,
                        kode,
                        nama

                    FROM 
                        wilayah

                    WHERE
                        hapus = '0'

                    ORDER BY 
                        nama ASC
            ";
            $e = mysqli_query($conn, $q);
            $c = mysqli_num_rows($e);
            if($c==0){
?>
                        <option value="Kosong"></option>
<?php
            }
            else{
?>
                        <option value="">Pilih</option>
<?php
                while ($r = mysqli_fetch_assoc($e)) {
                    $idW = $r['id'];
                    $kode = $r['kode'];
                    $nama = $r['nama'];
?>
                        <option value="<?php echo $idW; ?>">
                            <?php echo $kode.' - '.$nama; ?>
                        </option>
<?php            
                }
            }
?>                    
            </select>
                    </div>
                </div>

                <div class="field">
                    <div class="ui icon button fluid primary" onclick="reportUNSelang()">
                        <i class="print icon"></i> Laporan
                    </div>
                </div>
            </td>
        </tr>
<?php            
        }
    }
?>




<script type="text/javascript">
	$('.button, .popup').popup();
    $('.dropdown').dropdown({ fullTextSearch: "exact" });
</script>