<?php
    session_start();
    $appSection = 'root';

    $fromHome = '../../../';

    if(empty($_SESSION['idPengguna'])){
        header('location: '.$fromHome);
        exit();
    }

    $jenisPengguna = $_SESSION['jenisPengguna'];
    if($jenisPengguna !== $appSection){
        header('location: '.$fromHome.''.$jenisPengguna);
        exit();
    }

    require_once $fromHome.'lib/core/head.php';
?>

        <div id="mainLoader" class="ui basic vertical segment container form">
<?php
    if(empty($_GET['kode'])){
?>
            <div class="ui icon message">
                <i class="inbox icon"></i>
                <div class="content">
                    <div class="header">
                        Laporan tidak tersedia
                    </div>
                    <p>
                        Kode preview laporan tidak terlampir.
                    </p>
                </div>
            </div>
<?php
        exit();
    }

    if(empty($_GET['enc'])){
?>
            <div class="ui icon message">
                <i class="inbox icon"></i>
                <div class="content">
                    <div class="header">
                        Laporan tidak tersedia
                    </div>
                    <p>
                        Kode encrypt tidak terlampir.
                    </p>
                </div>
            </div>
<?php
        exit();
    }
    
    $kode = saring($_GET['kode']);
    $sel_wil = saring($_GET['sel']);

    if(empty($_GET['sel'])){
?>
            <div class="ui icon message">
                <i class="inbox icon"></i>
                <div class="content">
                    <div class="header">
                        Laporan tidak tersedia
                    </div>
                    <p>
                        Pilih wilayah.
                    </p>
                </div>
            </div>
<?php
        exit();
    }
//<yoko//
    $tgl_awal = saring($_GET['start']);
    $tgl_akhir = saring($_GET['end']);

    if(empty($_GET['start']) || empty($_GET['end'])){
        ?>
            <div class="ui icon message">
                <i class="inbox icon"></i>
                <div class="content">
                    <div class="header">
                        Laporan tidak tersedia
                    </div>
                    <p>
                        Pilih Tanggal.
                    </p>
                </div>
            </div>
        <?php
        exit();
    }
//yoko>//
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
                pc.rekomendasi,

                k.nik,
                k.nama nama_karyawan,
                k.id_wil,

                w.kode kd_wil,
                w.nama nm_wil,

                pct.nama topik

            FROM 
                pelatihan_catatan pc

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

    //echo '<br>'.$q.'<br><br>';

    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);
    if($c=='0'){
?>
        <div class="ui icon message">
            <i class="inbox icon"></i>
            <div class="content">
                <div class="header">
                    Laporan tidak tersedia
                </div>
                <p>
                    Data tidak ditemukan
                </p>
            </div>
        </div>
<?php
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

?>

        <h3 class="ui header block segment">
            <i class="file text icon"></i>
            <div class="content">
                Laporan Catatan Pelatihan
                <div class="sub header">
                    Periode <?php echo $idPeriode; ?> Wilayah : 
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
                </div>
            </div>
        </h3>



        <div id="tempatChart" class="ui segment" style="width: 100%; height: auto;">
            <!--
                populate chart here
            -->
        </div>



        <table class="ui table">
            <thead>
                <tr>
                    <th rowspan="2" width="4%">No</th>
                    <th rowspan="2">Topik</th>
                    <th colspan="3" width="40%">Frekuensi</th>
                </tr>
                <tr>
                    <th>Respon</th>
                    <th>Naik</th>
                    <th>Turun</th>
                </tr>
            </thead>
            <tbody>
<?php
    if($jmlTopik=='0'){
?>
        <tr>
            <td colspan="2">
                <i class="info circle teal icon"></i> <i>Belum ada data.</i>
            </td>
        </tr>
<?php
    }
    else{
        $jar = $jmlTopik-1;

        for ($i=0; $i <= $jar; $i++) {
            $idTopik = $arTopik[$i]['id'];
            $namaTopik = $arTopik[$i]['nama'];

            $jR = 'jmlRespon'.$idTopik;
            $jN = 'jmlNaik'.$idTopik;
            $jT = 'jmlTurun'.$idTopik;
?>
            <tr>
                <td>
                    <?php echo $i+1; ?>
                </td>
                <td>
                    <?php echo $namaTopik; ?>
                </td>
                <td>
                    <?php echo $$jR; ?>
                </td>
                <td>
                    <?php echo $$jN; ?>
                </td>
                <td>
                    <?php echo $$jT; ?>
                </td>
            </tr>
<?php                  
        }   
    }
?>


        </tbody>
    </table>

    <table class="ui table">
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="14%">Wilayah</th>
                <th width="10%">NIP</th>
                <th width="26%">Karyawan</th>
                <th>Topik</th>
                <th width="4%">Pre</th>
                <th width="4%">Post</th>
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
        $classTr = '';
        if($nilai_after < $nilai_before){
            $classTr = 'error';
        }
?>      
            <tr class="<?php echo $classTr; ?>">
                <td><?php echo $i + 1; ?></td>
                <td><?php echo $kd_wil.' - '.$nm_wil; ?></td>
                <td><?php echo $nik; ?></td>
                <td><?php echo $karyawan; ?></td>
                <td><?php echo $topik; ?></td>
                <td><?php echo $nilai_before; ?></td>
                <td><?php echo $nilai_after; ?></td>
            </tr>
<?php        

    }
?>            
        </tbody>
    </table>

        
        

    </div>
<br>
<br>
<?php
        require_once $fromHome.'lib/core/snippet.php';
        require_once $fromHome.'lib/core/footer.php';
?>

        <script type="text/javascript" src="<?php echo $fromHome; ?>lib/jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo $fromHome; ?>lib/semantic-ui/semantic.min.js"></script>
        <script type="text/javascript" src="<?php echo $fromHome; ?>lib/highchart/highcharts.js"></script>
        <script type="text/javascript" src="<?php echo $fromHome; ?>lib/highchart/exporting.js"></script>
        <script type="text/javascript" src="<?php echo $fromHome; ?>lib/core/snippet.js"></script>
        <script type="text/javascript">
            //highcharts v 4.0.4

            $(function () {
                $('#tempatChart').highcharts({
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: 'Laporan Catatan Pelatihan'
                    },
                    subtitle: {
                        text: 'Periode <?php echo $idPeriode; ?> Wilayah : <?php $ke = 0; foreach ($wila as $head_wil) { $ke = $ke + 1; echo $head_wil; if($ke < $jumWil){ echo ", "; } } ?>'
                    },
                    xAxis: {
                        categories: [
<?php
    $ke = 1;
    foreach ($wila as $key => $nm_wil) {
        echo "'".$nm_wil."'";
        if($ke<$jumWil){
            echo ",";
        }
        $ke = $ke+1;
    }
?>                            
                        ],
                        crosshair: true
                    },
                    yAxis: {
                        title: {
                            text: 'Jumlah'
                        }
                    },
                    tooltip: {
                        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                            '<td style="padding:0"><b>{point.y}</b></td></tr>',
                        footerFormat: '</table>',
                        shared: true,
                        useHTML: true
                    },
                    plotOptions: {
                        column: {
                            pointPadding: 0.2,
                            borderWidth: 0
                        }
                    },
                    series: [
                    {
                        name: 'Rerata Before',
                        data: [
<?php
    $ke = 0;
    $maxi = $jumWil-1;
    foreach ($idWila as $key => $id_wil) {
        $b = 'jmlBefore'.$id_wil;
        $cb = 'countBefore'.$id_wil;
        $rb = 'rerataBefore'.$id_wil;

        if($$b=='0'||$$cb=='0'){
            $$rb = 0;
        }
        else{
            $$rb = $$b / $$cb;
        }
        echo round($$rb);
        
        if($ke<$maxi){
            echo ",";
        }
        $ke = $ke+1;
    }
?>                                            
                        ]
                    },{
                        name: 'Rerata After',
                        data: [
<?php
    $ke = 0;
    $maxi = $jumWil-1;
    foreach ($idWila as $key => $id_wil) {
        $a = 'jmlAfter'.$id_wil;
        $ca = 'countAfter'.$id_wil;
        $ra = 'rerataAfter'.$id_wil;

        $$ra = $$a / $$ca;

        if($$a=='0'||$$ca=='0'){
            $$ra = 0;
        }
        else{
            $$ra = $$a / $$ca;
        }
        echo round($$ra);
        
        if($ke<$maxi){
            echo ",";
        }
        $ke = $ke+1;
    }
?>                                    
                        ]

                    },
                    {
                        name: 'Turun',
                        data: [
<?php
    $ke = 0;
    $maxi = $jumWil-1;
    foreach ($idWila as $key => $id_wil) {
        $ct = 'countTurun'.$id_wil;
        echo $$ct;
        
        if($ke<$maxi){
            echo ",";
        }
        $ke = $ke+1;
    }
?>                                       
                        ]
                    },
                    {
                        name: 'Rerata Turun',
                        data: [
<?php
    $ke = 0;
    $maxi = $jumWil-1;
    foreach ($idWila as $key => $id_wil) {
        $t = 'jmlTurun'.$id_wil;
        $ct = 'countTurun'.$id_wil;
        $rt = 'rerataTurun'.$id_wil;
        if($$t==0||$$ct==0){
            $$rt = 0;
        }
        else{
            $$rt = $$t / $$ct;    
        }
        echo round($$rt);
        
        if($ke<$maxi){
            echo ",";
        }
        $ke = $ke+1;
    }
?>                                         
                        ]
                    },
                    {
                        name: 'Stagnan',
                        data: [
<?php
    $ke = 0;
    $maxi = $jumWil-1;
    foreach ($idWila as $key => $id_wil) {
        $cs = 'countStagnan'.$id_wil;
        echo $$cs;
        
        if($ke<$maxi){
            echo ",";
        }
        $ke = $ke+1;
    }
?>                                       
                        ]
                    },
                    {
                        name: 'Naik',
                        data: [
<?php
    $ke = 0;
    $maxi = $jumWil-1;
    foreach ($idWila as $key => $id_wil) {
        $cn = 'countNaik'.$id_wil;
        echo $$cn;
        
        if($ke<$maxi){
            echo ",";
        }
        $ke = $ke+1;
    }
?>
                        ]

                    },
                    {
                        name: 'Rerata Naik',
                        data: [
<?php
    $ke = 0;
    $maxi = $jumWil-1;
    foreach ($idWila as $key => $id_wil) {
        $n = 'jmlNaik'.$id_wil;
        $cn = 'countNaik'.$id_wil;
        $rn = 'rerataNaik'.$id_wil;
        if($$n==0||$$cn==0){
            $$rn = 0;
        }
        else{
            $$rn = $$n / $$cn;    
        }
        echo round($$rn);
        
        if($ke<$maxi){
            echo ",";
        }
        $ke = $ke+1;
    }
?>
                        ]

                    }

                    ]
                })

            })
        </script>
        
    </body>
</html>