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
    if(empty($_GET['kode'])){
        echo "NOT PERMITTED";
        exit();
    }
    
    $kode = saring($_GET['kode']);


    $q = "
        SELECT 
            up.id, 
            up.id_periode, 
            up.id_ujian, 
            up.kkm, 
            up.tanggal, 
            up.waktu, 
            up.tampilan, 
            up.aktif,
            up.hapus,

            u.nama namaUjian, 
            u.deskripsi deskUjian

        FROM 
            ujian_pelaksanaan up

        LEFT JOIN
            ujian u
        ON
            up.id_ujian = u.id

        WHERE
            up.kode = '$kode'

        LIMIT
            1
    ";

    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);
    if($c=='0'){
        echo "DATA NOT FOUND";
        exit();
    }


    $r = mysqli_fetch_assoc($e);

    $hapus = $r['hapus'];
    if($hapus=='1'){
        echo "DATA DELETED";
        exit();
    }

    $id_pelaksanaan = $r['id'];

    $kkm = $r['kkm'];
    $tanggal = $r['tanggal'];
    $waktu = $r['waktu'];

    $id_ujian = $r['id_ujian'];
    $namaUjian = $r['namaUjian'];
    $deskUjian = $r['deskUjian'];



    //check the atarget
    $q = "
            SELECT 
                id, 
                id_karyawan, 
                susulan, 
                susulan_alasan, 
                susulan_tgl
                
            FROM 
                ujian_pelaksanaan_target_karyawan 

            WHERE
                hapus ='0'
            AND
                id_pelaksanaan  = '$id_pelaksanaan'
    ";
    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);

    $jmlPesertaUjian = $c;

    $q = "
            SELECT 
                ku.id, 
                ku.tanggal, 
                ku.mulai, 
                ku.selesai, 
                ku.nilai_akhir,
                ku.n,
                ku.n_pk,
                ku.nilai_grade,
                ku.remidi,
                ku.n_poin,

                k.nik,
                k.nama,
                
                w.kode kodeWil,
                w.nama namaWil

            FROM 
                karyawan_ujian ku

            LEFT JOIN
                karyawan k
            ON
                ku.id_karyawan = k.id

            LEFT JOIN
                wilayah w
            ON
                k.id_wil = w.id

            WHERE
                ku.id_pelaksanaan = '$id_pelaksanaan'
            AND
                ku.hapus = '0'
            AND
                ku.selesai != ''
            AND
                ku.selesai != '00:00:00'

            ORDER BY
                k.nik ASC
    ";

    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);

    $jmlPesertaMengerjakan = $c;
    $jmlPesertaMengerjakanAlsi = 0;
    $jmlPesertaMengerjakanSusulan = 0;
    $jmlLulus = 0;
    $jmlTidakLulus = 0;




    $ar = array();
    $r = array();

    $arTidakLulus = array();
    $rTdk = array();

    $arLulus = array();
    $rLulus = array();

    $nomor = 1;

    while ($d = mysqli_fetch_assoc($e)) {
        $idPengerjaan = $d['id'];

        $nik = $d['nik'];
        $nama = $d['nama'];
        
        $kodeWil = $d['kodeWil'];
        $namaWil = $d['namaWil'];
        
        $tglKerja = $d['tanggal'];
        $mulai = $d['mulai'];
        $selesai = $d['selesai'];
        $nilai_akhir = $d['nilai_akhir'];
        $n = $d['n'];
        $n_pk = $d['n_pk'];
        $nilai_grade = $d['nilai_grade'];
        $n_poin = $d['n_poin'];

        if($tglKerja==$tanggal){
            $jmlPesertaMengerjakanAlsi = $jmlPesertaMengerjakanAlsi+1;
        }
        else{
            $jmlPesertaMengerjakanSusulan = $jmlPesertaMengerjakanSusulan+1;
        }

        if($nilai_akhir >= $kkm){
            $jmlLulus = $jmlLulus+1;
            $rLulus['nama'] = $nama;
            $rLulus['nomor'] = $nomor;
            $rLulus['nilai'] = $nilai_akhir;
            $arLulus[] = $rLulus;
        }
        else{
            $jmlTidakLulus = $jmlTidakLulus+1;
            $rTdk['nama'] = $nama;
            $rTdk['nomor'] = $nomor;
            $rTdk['nilai'] = $nilai_akhir;
            $arTidakLulus[] = $rTdk;
        }

        $r['idPengerjaan'] = $idPengerjaan;
        $r['tglKerja'] = $tglKerja;
        $r['mulai'] = $mulai;
        $r['selesai'] = $selesai;
        $r['nilai'] = $nilai_akhir;

        $r['n'] = $n;
        $r['n_pk'] = $n_pk;

        $r['n_poin'] = $n_poin;
        $r['grade'] = $nilai_grade;
        
        $r['nik'] = $nik;
        $r['nama'] = $nama;
        $r['kodeWil'] = $kodeWil;
        $r['namaWil'] = $namaWil;
        
        $ar[] = $r;

        $nomor = $nomor+1;
    }

    $jmlPesertaBelumUjian = $jmlPesertaUjian - $jmlPesertaMengerjakan;

    $judul = buatLink(saring($namaUjian));

    header('Content-Type: application/vnd.ms-excel');
    header('Content-disposition: attachment; filename='.$judul.'-'.$kode.'.xls');
    ob_get_clean();
    header('Pragma: no-cache');
    header('Expires: 0');
?>
    <h2><?php echo $kode; ?></h2>
    <h4><?php echo ucwords($namaUjian); ?></h4>
    Terjadwal Tanggal: <?php echo tanggalKan($tanggal); ?><br>
    KKM: <?php echo $kkm; ?><br>
    Waktu: <?php echo $waktu; ?> (menit)<br>
    Jumlah Peserta: <?php echo $jmlPesertaUjian; ?><br>
    Lulus : <?php echo $jmlLulus; ?><br>
    Tidak Lulus : <?php echo $jmlTidakLulus; ?><br>
    On Time : <?php echo $jmlPesertaMengerjakanAlsi; ?><br>
    Susulan : <?php echo $jmlPesertaMengerjakanSusulan; ?><br>
    Belum : <?php echo $jmlPesertaBelumUjian; ?><br><br>

    <table>
        <thead>
            <th width="4%">No</th>
            <th width="16%">Area</th>
            <th width="10%">NIP</th>
            <th>Nama</th>
            <th width="40%">Pengerjaan</th>
            <th width="4%">PK</th>
            <th width="4%">CH</th>
            <th width="8%">Total</th>
            <th width="8%">Status</th>
<?php
    if(strtolower($namaUjian) == 'ujian nasional'){
?>
            <th width="4%">Poin</th>
<?php        
    }
?>            
            <th width="8%">Grade</th>
        </thead>
        <tbody>
<?php
    if($c==0){
?>
            <tr>
               <td>
                   Belum ada yang mengerjakan
               </td> 
            </tr>
<?php
        exit();
    }

    $jar = $c-1;
    for ($k=1; $k <= $jmlPesertaMengerjakan; $k++) {
        $arN = $k-1;
        
        $nik = $ar[$arN]['nik'];
        $nama = $ar[$arN]['nama'];
        $kodeWil = $ar[$arN]['kodeWil'];
        $namaWil = $ar[$arN]['namaWil'];

        $tglKerja = $ar[$arN]['tglKerja'];
        $mulai = $ar[$arN]['mulai'];

        $teksInfo = 'Pada '.tanggalKan($tglKerja).' <br> Waktu '.$mulai.' sampai '.$selesai;

        $tglKerja = $ar[$arN]['tglKerja'];
        if($tglKerja > $tanggal){
            $teksInfo .= ' (Susulan)';
        }

        $nilai = round($ar[$arN]['nilai'],2);
        $warDpna = '#badc58';
        $status = 'Lulus';
        if($nilai < $kkm){
            $warDpna = '#ff7979';
            $status = 'Tidak Lulus';
        }

        $n = $ar[$arN]['n'];
        $n_pk = $ar[$arN]['n_pk'];

        $n_poin = $ar[$arN]['n_poin'];
        $grade = $ar[$arN]['grade'];
?>
                <tr style="background: <?php echo $warDpna; ?>;">
                    <td><?php echo $k; ?></td>
                    <td><?php echo $kodeWil.' - '.$namaWil; ?></td>
                    <td><?php echo $nik; ?></td>
                    <td><?php echo $nama; ?></td>
                    <td><?php echo $teksInfo; ?></td>
                    <td><?php echo $n_pk; ?></td>
                    <td><?php echo $n; ?></td>
                    <td><?php echo $nilai; ?></td>
                    <td><?php echo $status; ?></td>
<?php
    if(strtolower($namaUjian) == 'ujian nasional'){
?>
                    <td><?php echo $n_poin; ?></td>
<?php        
    }
?>                        
                    <td><?php echo $grade; ?></td>
                </tr>
<?php           
    }
?>                

        </tbody>
</table> 