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
    if(empty($_GET['kode']) || empty($_GET['sel'])){
        echo "NOT PERMITTED";
        exit();
    }
    
    $kode = saring($_GET['kode']);
    $sel_wil = saring($_GET['sel']);
    $tgl_awal = saring($_GET['start']);
    $tgl_akhir = saring($_GET['end']);    
    $wil = explode(',', $sel_wil);
    $jml = count($wil);


    $q = "
            SELECT 
                pc.id, 
                pc.id_karyawan, 
                pc.tanggal, 
                pc.id_topik, 
                pc.id_root, 
                pc.nilai_before, 
                pc.nilai_after, 

                pcr.rekomendasi,

                k.nik,
                k.nama nama_karyawan,
                k.id_wil,

                w.kode kd_wil,
                w.nama nm_wil,

                pct.nama topik

            FROM 
                pelatihan_catatan pc

            LEFT JOIN
                pelatihan_catatan_rekomendasi pcr
            ON
                pc.id_karyawan = pcr.id_karyawan
            AND
                pc.tanggal = pcr.tanggal
            AND
                pc.id_periode = pcr.id_periode

            LEFT JOIN
                karyawan k
            ON
                k.id = pc.id_karyawan

            LEFT JOIN
                wilayah w
            ON
                k.id_wil = w.id

            LEFT JOIN
                pelatihan_catatan_topik pct
            ON
                pc.id_topik = pct.id

            WHERE
                pc.id_periode = '$idPeriode'
            AND
                pc.hapus = '0'
            AND
                k.hapus = '0'
            AND
                w.hapus = '0'
            AND
                pc.tanggal >= '$tgl_awal'
            AND
                pc.tanggal <= '$tgl_akhir'
    ";

    if($jml=='1'){
        $q.="
                AND
                    w.id = '$sel_wil'
        ";
    }

    if($jml>1){
        $q .= "
                AND
                 (
        ";

        $di = 1;
        foreach ($wil as $key => $id_wil) {
            if($di>1 && $di<=$jml){
                $q .= "
                        OR
                ";
            }

            $q .= "
                    w.id = '$id_wil'
            ";

            $di = $di+1;
        }

        $q .= "
                )
        ";
    }

    $q .= "
            ORDER BY
                w.nama ASC,
                k.nik ASC,
                pc.id_topik ASC
    ";

    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);

    if($c == '0'){
        echo "DATA NOT FOUND";
        exit();
    }



    $qT = "
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
    $eT = mysqli_query($conn, $qT);
    $jmlTopik = mysqli_num_rows($eT);

    if($jmlTopik>0){
        $arTopik = array();
        $rTopik = array();

        while ($dT = mysqli_fetch_assoc($eT)) {
            $idT = $dT['id'];
            $rTopik['id']    = $idT;
            $rTopik['nama']    = $dT['nama'];

            $arTopik[]   = $rTopik;


            $jR = 'jmlRespon'.$idT;
            $$jR = 0;
            $jN = 'jmlNaik'.$idT;
            $$jN = 0;
            $jT = 'jmlTurun'.$idT;
            $$jT = 0;
        }
    }



    $r = array();
    $ar = array();

    while ($d = mysqli_fetch_assoc($e)) {
        $r['id_karyawan'] = $d['id_karyawan'];
        $r['id_topik'] = $d['id_topik'];
        $r['nilai_before'] = $d['nilai_before'];
        $r['nilai_after'] = $d['nilai_after'];

        $r['id_wil'] = $d['id_wil'];
        $r['kd_wil'] = $d['kd_wil'];
        $r['nm_wil'] = $d['nm_wil'];

        $r['nik'] = $d['nik'];
        $r['nama'] = $d['nama_karyawan'];
        $r['topik'] = $d['topik'];
        $r['rekom'] = $d['rekomendasi'];

        $ar[] = $r;
    }

    $car = $c-1;

    $idWila = array();
    $wila = array();

    for ($i=0; $i <= $car; $i++) {
        $id_wil = $ar[$i]['id_wil'];
        $nm_wil = $ar[$i]['nm_wil'];
        $id_karyawan = $ar[$i]['id_karyawan'];
        $id_topik = $ar[$i]['id_topik'];
        $nilai_before = $ar[$i]['nilai_before'];
        $nilai_after = $ar[$i]['nilai_after'];
        $beda = $nilai_after - $nilai_before;

        $b = 'jmlBefore'.$id_wil;
        $cb = 'countBefore'.$id_wil;
        $rb = 'rerataBefore'.$id_wil;

        $a = 'jmlAfter'.$id_wil;
        $ca = 'countAfter'.$id_wil;
        $ra = 'rerataAfter'.$id_wil;

        $t = 'jmlTurun'.$id_wil;
        $ct = 'countTurun'.$id_wil;
        $rt = 'rerataTurun'.$id_wil;
        
        $s = 'jmlStagnan'.$id_wil;
        $cs = 'countStagnan'.$id_wil;
        $rs = 'rerataStagnan'.$id_wil;

        $n = 'jmlNaik'.$id_wil;
        $cn = 'countNaik'.$id_wil;
        $rn = 'rerataNaik'.$id_wil;

        $jR = 'jmlRespon'.$id_topik;
        $jN = 'jmlNaik'.$id_topik;
        $jT = 'jmlTurun'.$id_topik;


        $$jR = $$jR + 1;

        if($i==0){
            array_push($idWila, $id_wil);
            array_push($wila, $nm_wil);

            $$b = 0;
            $$b = $$b + $nilai_before;
            $$cb = 0;
            $$cb = $$cb + 1;

            $$a = 0;
            $$a = $$a + $nilai_after;
            $$ca = 0;
            $$ca = $$ca + 1;

            $$t = 0;
            $$ct = 0;

            $$s = 0;
            $$cs = 0;

            $$n = 0;
            $$cn = 0;

            if($beda<0){
                //turun
                $$t = $$t + $beda;
                $$ct = $$ct + 1;

                $$jT = $$jT +1;
            }
            else if($beda==0){
                //stagnan
                $$s = $$s + $beda;
                $$cs = $$cs + 1;
            }
            else{
                //naik
                $$n = $$n + $beda;
                $$cn = $$cn + 1;

                $$jN = $$jN + 1;
            }
        }
        else{
            $x = $i-1;
            $old_wil = $ar[$x]['id_wil'];
            if($old_wil!==$id_wil){
                array_push($idWila, $id_wil);
                array_push($wila, $nm_wil);

                $$b = 0;
                $$b = $$b + $nilai_before;
                $$cb = 0;
                $$cb = $$cb + 1;

                $$a = 0;
                $$a = $$a + $nilai_after;
                $$ca = 0;
                $$ca = $$ca + 1;

                $$t = 0;
                $$ct = 0;

                $$s = 0;
                $$cs = 0;

                $$n = 0;
                $$cn = 0;

                if($beda<0){
                    //turun
                    $$t = $$t + $beda;
                    $$ct = $$ct + 1;
                }
                else if($beda==0){
                    //stagnan
                    $$s = $$s + $beda;
                    $$cs = $$cs + 1;
                }
                else{
                    //naik
                    $$n = $$n + $beda;
                    $$cn = $$cn + 1;
                }
            }
            
            $$b = $$b + $nilai_before;
            $$cb = $$cb + 1;

            $$a = $$a + $nilai_after;
            $$ca = $$ca + 1;

            if($beda<0){
                //turun
                $$t = $$t + $beda;
                $$ct = $$ct + 1;

                $$jT = $$jT +1;
            }
            else if($beda==0){
                //stagnan
                $$s = $$s + $beda;
                $$cs = $$cs + 1;
            }
            else{
                //naik
                $$n = $$n + $beda;
                $$cn = $$cn + 1;

                $$jN = $$jN + 1;
            }
        }
    }


    $jumWil = count($wila);


    $str = 'Laporan Catatan Pelatihan Periode '.$idPeriode.' Wilayah ';

    $ke = 0;
    foreach ($wila as $head_wil) {
        $ke = $ke + 1;
        $str .= $head_wil;
        if($ke < $jumWil){
            $str .= ", ";
        }
    }


    
    $judul = buatLink($str);

    header('Content-Type: application/vnd.ms-excel');
    header('Content-disposition: attachment; filename='.$judul.'-'.$kode.'.xls');
    ob_get_clean();
    header('Pragma: no-cache');
    header('Expires: 0');
?>
    <h4><?php echo 'Laporan Catatan Pelatihan'; ?></h4>
    Periode : <?php echo $idPeriode; ?><br>
    Wilayah : 
<?php
    $ke = 0;
    foreach ($wila as $head_wil) {
        $ke = $ke + 1;
        echo $head_wil;
        if($ke < $jumWil){
            echo ", ";
        }
    }
?>    
    <br>
    
    <table>
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="14%">Wilayah</th>
                <th width="10%">NIP</th>
                <th width="26%">Karyawan</th>
                <th>Topik</th>
                <th width="4%">Pre</th>
                <th width="4%">Post</th>
                <th width="16%">rekomendasi</th>
            </tr>
        </thead>
        <tbody>
<?php
    for ($i=0; $i <= $car; $i++) {
        $kd_wil = $ar[$i]['kd_wil'];
        $nm_wil = $ar[$i]['nm_wil'];
        

        $idK = $ar[$i]['id_karyawan'];
        $nik = $ar[$i]['nik'];
        $karyawan = $ar[$i]['nama'];


        $topik = $ar[$i]['topik'];
        $nilai_before = $ar[$i]['nilai_before'];
        $nilai_after = $ar[$i]['nilai_after'];

        $warDpna = '#badc58';
        if($nilai_after < $nilai_before){
            $warDpna = '#ff7979';
        }

        $rekomendasi = $ar[$i]['rekom'];
?>      
            <tr style="background: <?php echo $warDpna; ?>;">
                <td><?php echo $i + 1; ?></td>
                <td><?php echo $kd_wil.' - '.$nm_wil; ?></td>
                <td><?php echo $nik; ?></td>
                <td><?php echo $karyawan; ?></td>
                <td><?php echo $topik; ?></td>
                <td><?php echo $nilai_before; ?></td>
                <td><?php echo $nilai_after; ?></td>
                <td><?php echo $rekomendasi; ?></td>
            </tr>
<?php        

    }
?>            
        </tbody>
    </table>