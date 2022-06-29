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

    $main = 'Data BA/ BC';

    $id = saring($_POST['idData']);

    $q = "
            SELECT 
                k.id_wil, 
                k.kode, 
                k.nik, 
                k.nama, 
                k.jk, 
                k.tmpt_lahir, 
                k.tgl_lahir, 
                k.email, 
                k.hp, 
                k.alamat, 
                k.foto,
                k.tgl_masuk,

                a.jenis,

                w.kode kode_wil,
                w.nama nama_wil,

                t.nama tingkat,
                t.no noTingkat

            FROM 
                karyawan k

            LEFT JOIN
                akun a
            ON
                a.id_pengguna = k.id

            LEFT JOIN
                wilayah w
            ON
                k.id_wil = w.id
            AND
                w.hapus = '0'

            LEFT JOIN
                tingkat_belajar t
            ON
                t.id = k.tingkat

            WHERE
                k.id = '$id'

            LIMIT
                1
    ";
    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);
    if($c == '0'){
        echo "DATA NOT FOUND";
        exit();
    }

    $r = mysqli_fetch_assoc($e);

    $id_wil = $r['id_wil'];
    $nama_wil = $r['nama_wil'];
    $kode_wil = $r['kode_wil'];
    $kode = $r['kode'];
    $nik = $r['nik'];
    $nama = $r['nama'];
    $jk = $r['jk'];
    if($jk==''){
        $jk = 'n';
    }
    $tmpt_lahir = $r['tmpt_lahir'];
    $tgl_lahir = $r['tgl_lahir'];
    if($tgl_lahir=='0000-00-00'){
        $tgl_lahir = '';
    }
    $alamat = $r['alamat'];
    $email = $r['email'];
    $hp = $r['hp'];
    
    $foto = $r['foto'];

    if($foto==''){
        $avatar = '../files/photo/'.$jk.'.png';
    }
    else{
        $avatar = str_replace('%20', ' ', $foto);
        if(file_exists('../../'.$foto)){
            $avatar = '../'.$foto;
        }
        else{
            $avatar = '../files/photo/'.$jk.'.png';
        }
    }

    $jenis = $r['jenis'];
    $tingkat = $r['tingkat'];
    $tgl_masuk = $r['tgl_masuk'];
    if($tgl_masuk=='0000-00-00'){
        $tgl_masuk = '';
    }

    $jenis = $r['jenis'];
    $tingkat = $r['tingkat'];
    $noTingkat = $r['noTingkat'];

    $sub = 'Detail Ujian & Belajar';

?>

<div id="subDisplay">
    <div class="ui message">
        <div class="ui breadcrumb">
            <div class="section"><?php echo $main; ?></div>
            <i class="right angle icon divider"></i>
            <div class="active section"><?php echo $sub; ?></div>
        </div>
    </div>

    <div class="field">
        <div class="ui icon button" onclick="backToMain()">
            <i class="left chevron icon"></i> Kembali
        </div>    
    </div>

    <table class="ui table">
        <thead>
            <tr>
                <th colspan="2">
                    <img src="<?php echo $avatar; ?>" class="ui small centered image">
                </th>
            </tr>
            <tr>
                <th colspan="2">Data Karyawan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td width="26%">NIK</td>
                <td><?php echo $nik; ?></td>
            </tr>
            <tr>
                <td>Nama</td>
                <td><?php echo $nama; ?></td>
            </tr>
            <tr>
                <td>Wilayah</td>
                <td><?php echo $kode_wil.' - '.$nama_wil; ?></td>
            </tr>
            <tr>
                <td></td>
                <td><?php echo $jenis; ?></td>
            </tr>
            <tr>
                <td>Tingkat Belajar</td>
                <td><?php echo $tingkat; ?></td>
            </tr>
        </tbody>
    </table>

    <br>
    <div class="ui horizontal divider">
        <i class="edit icon"></i> Ujian
    </div>
    <table class="ui table" style="margin: 0px;">
        <thead>
            <tr>
                <th width="4%" rowspan="2">No</th>
                <th rowspan="2">Pengerjaan</th>
                <th colspan="4" width="8%">Hasil</th>
            </tr>
            <tr>
                <th>PK</th>
                <th>CH</th>
                <th>NA</th>
                <th>Grade</th>
            </tr>
        </thead>
        <tbody>
<?php
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
                ku.id_karyawan = '$id'
            AND
                ku.selesai != ''
            AND
                ku.hapus = '0'
            AND
                up.hapus = '0'
          
            ORDER BY
                up.id_periode DESC,
                ku.tanggal DESC,
                ku.mulai DESC
    ";

    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);

    
    if($c=='0'){
?>
            <tr>
                <td colspan="6">
                    <i class="ui info icon teal circle"></i> <i>Belum ada data.</i>
                </td>
            </tr>
<?php        
    }
    else{
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

        $nomor = 1;

        for ($i=0; $i <= $cAr; $i++) {
            $idData = $ar[$i]['id'];
            $mengerjakan = $ar[$i]['mengerjakan'];
            $jadwal = $ar[$i]['jadwal'];
            $namaUjian = $ar[$i]['namaUjian'];

            if($mengerjakan > $jadwal){
                $ketTgl = tanggalKan($mengerjakan).' [Susulan]';
            }
            else{
                $ketTgl = tanggalKan($mengerjakan);
            }

            if($mengerjakan == $sekarang){
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
            </tr>
<?php            
            $nomor = $nomor+1;            
        }
    }
?>            
        </tbody>
    </table>





    
<?php
    $q = "
            SELECT 
                k.tingkat,

                t.id, 
                t.no, 
                t.nama

            FROM 
                karyawan k

            LEFT JOIN
                tingkat_belajar t
            ON
                k.tingkat = t.id

            WHERE
                k.id = '$id'

            LIMIT
                1
    ";
    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);
    if($c == '0'){
        echo "DATA NOT FOUND";
        exit();
    }

    $r = mysqli_fetch_assoc($e);
    $idTingkat = $r['tingkat'];
    $noTingkat = $r['no'];
    $namatingkat = $r['nama'];
?>
    <br>
    <br>
    <div class="ui horizontal divider">
        <i class="book icon"></i> History Belajar
    </div>
<?php
    $qT = "
            SELECT 
                id

            FROM 
                tingkat_belajar 

            WHERE
                no <= '$noTingkat'
            AND
                hapus = '0'
    ";
    $eT = mysqli_query($conn, $qT);

    $rT = array();

    while ($dT = mysqli_fetch_assoc($eT)) {
        $idT = $dT['id'];
        array_push($rT, $idT);
    }
    

    $q="
        SELECT 
            b.id idB,
            b.no, 
            b.nama namaB, 
            b.deskripsi,

            (
                SELECT
                    COUNT(id)

                FROM
                    materi

                WHERE
                    id_bahasan = idB
                
                AND
                    hapus = '0'
                AND
                    (
                        id_tingkat_belajar = 'semua'
        ";

        foreach ($rT as $idTi) {
            $q.= "
                    OR
                        id_tingkat_belajar = '$idTi'
            ";
        }

        $q.="
                    )
            ) jmlMateri,

            (
                SELECT 
                    COUNT(kbl.id)

                FROM 
                    karyawan_belajar_kuis kbl

                LEFT JOIN
                    materi_kuis mk
                ON
                    mk.id = kbl.id_kuis
                AND
                    mk.aktif = '1'

                LEFT JOIN
                    materi m
                ON
                    m.id = mk.id_materi
                AND 
                    m.hapus = '0'

                WHERE
                    kbl.id_karyawan = '$id'
                AND
                    kbl.benar  = '1'

                AND
                    m.id_bahasan = idB
                
            ) jmlTuntas,

            k.nama namaK,

            l.id idBLast, 
            l.tanggal, 
            l.jam,
            l.last

        FROM
            materi_kelompok_bahasan b

        LEFT JOIN
            materi_kelompok k
        ON
            b.id_kelompok = k.id

        LEFT JOIN
            karyawan_belajar_materi l
        ON
            l.id_bahasan = b.id
        AND
            l.id_karyawan = '$id'
        AND
            l.last = '1'

        WHERE
            b.hapus = '0'

        ORDER BY
            b.no ASC
    ";

    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);
?>  
    <table class="ui very basic inline table unstackable">
        <tbody id="loaderBahasan">
<?php
    $ar = array();
        $r = array();

        while ($d = mysqli_fetch_assoc($e)) {
            $r['namaK']         = $d['namaK'];
            
            $r['idB']           = $d['idB'];
            $r['no']           = $d['no'];
            $r['namaB']           = $d['namaB'];
            $r['deskripsi']           = $d['deskripsi'];

            $r['jmlMateri']           = $d['jmlMateri'];
            $r['jmlTuntas']           = $d['jmlTuntas'];

            $r['idBLast']            = $d['idBLast'];
            $r['tanggal']            = $d['tanggal'];
            $r['jam']            = $d['jam'];
            $r['last']            = $d['last'];
            
        
            $ar[]   = $r;
        }

        $cAr = $c-1;

        $nomor = 1;

        $idLast = '';
        $noLast = '0';

        $adaMateri = 0;

        for ($i=0; $i <= $cAr; $i++) {
            $namaK = $ar[$i]['namaK'];
            
            $idB = $ar[$i]['idB'];
            $no = $ar[$i]['no'];
            $namaB = $ar[$i]['namaB'];
            $deskripsi = $ar[$i]['deskripsi'];
            
            $jmlMateri = $ar[$i]['jmlMateri'];
            $jmlTuntas = $ar[$i]['jmlTuntas'];
            if($jmlTuntas > $jmlMateri){
                $jmlTuntas = $jmlMateri;
            }

            $classTuntas = 'green';
            if($jmlTuntas < $jmlMateri){
                $classTuntas = 'red';
            }

            $baner = '../files/photo/briefcase.png';

            $idBLast = $ar[$i]['idBLast'];
            $tanggal = $ar[$i]['tanggal'];
            $jam = $ar[$i]['jam'];

            $last = $ar[$i]['last'];

            $idData = $idB.'[pisah]'.$noTingkat;

            if($jmlMateri>0){
                $adaMateri = '1';
?>
                <tr>
                    <td>
                        <h5 class="ui header">
                            <img class="ui image" src="<?php echo $baner; ?>">
                            <div class="content">
                                <?php echo $namaB; ?>
                                <div class="sub header">
                                    <p>
                                        <?php echo $deskripsi; ?>
                                    </p>
                                </div>
                            </div>
                        </h5>
                        <div class="ui label">
                            <?php echo $namaK; ?>
                        </div>
                        <div class="ui label">
                            Materi: <?php echo $jmlMateri; ?>
                        </div>
                        <div class="ui label <?php echo $classTuntas; ?>">
                            Tuntas: <?php echo $jmlTuntas; ?>
                        </div>
<?php
    if($i == '0'){
        if($last=='1'){
            $idLast = $idBLast;
            $noLast = $i;
?>
                        <div class="ui label orange" data-content="<?php echo tanggalKan($tanggal).' jam '.$jam; ?>">
                            Terakhir dibuka
                        </div>
<?php                
        }
    }

    if($i>0){
        if($last=='1'){
?>
                        <div class="ui label orange" data-content="<?php echo tanggalKan($tanggal).' jam '.$jam; ?>">
                            Terakhir dibuka
                        </div>
<?php                            
        }
    }
?>                        
                    </td>
                    <td width="8%">
<?php
    if($i == '0'){
?>                        
                        <div id="btn<?php echo $no; ?>" class="ui right floated inverted button" onclick="loadFormSub('materi','<?php echo $id; ?>[pisah]<?php echo $idData; ?>[pisah]<?php echo $noTingkat; ?>')" style="<?php echo $accentColor; ?>">
                            Detail
                        </div>
<?php
    }

    if($i>0){
        $x = $i-1;
        $jmlMateriPrev = $ar[$x]['jmlMateri'];
        $jmlTuntasPrev = $ar[$x]['jmlTuntas'];
        
        if($jmlTuntasPrev > $jmlMateriPrev){
            $jmlTuntasPrev = $jmlMateriPrev;
        }

        $classBaca = 'disabled';
        if($jmlTuntasPrev == $jmlMateriPrev){
            $classBaca = '';
        }
        else if($jmlTuntas > 0){
            $classBaca = '';
        }

        if($last=='1'){
            $idLast = $idBLast;
            $noLast = $i;
            $classBaca = '';        
        }
?>
                        <div id="btn<?php echo $no; ?>" class="ui right floated inverted button <?php echo $classBaca; ?>" onclick="loadFormSub('materi','<?php echo $id; ?>[pisah]<?php echo $idData; ?>[pisah]<?php echo $noTingkat; ?>')" style="<?php echo $accentColor; ?>">
                            Detail
                        </div>
<?php        
    }
?>                        
                    </td>
                </tr>
<?php                    
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