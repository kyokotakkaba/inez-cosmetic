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
    if(empty($_GET['kode'])||empty($_GET['enc'])||empty($_GET['p_sel'])||empty($_GET['w_sel'])){
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
    
    $kode = saring($_GET['kode']);

    $encrypt = saring($_GET['enc']);
    $id_p = saring($_GET['p_sel']);
    $idP = explode(',', $id_p);
    $jmlPeriode = count($idP);

    //echo 'jml periode : '.$jmlPeriode.'<br><br>';
    for ($i=1; $i <= $jmlPeriode; $i++) { 
        $x = $i-1;
        $idPeriode = saring($idP[$x]);
        //echo $i.' '.$idPeriode.'<br>';
    }

    //echo '<br><br>';

    $id_w = saring($_GET['w_sel']);
    $idW = explode(',', $id_w);
    $jmlWil = count($idW);

    //echo 'jml wilayah : '.$jmlWil.'<br><br>';
    for ($i=1; $i <= $jmlWil; $i++) { 
        $x = $i-1;
        $idWil = saring($idW[$x]);
        //echo $i.' '.$idWil.'<br>';
    }

    //echo '<br><br>';


    //UN
    $id_ujian = '5483-8ABF1C';

    $q = "
            SELECT 
                ku.id, 
                ku.id_pelaksanaan, 
                ku.id_karyawan, 
                ku.tanggal mengerjakan, 
                ku.nilai_akhir, 
                ku.n, 
                ku.n_pk, 
                ku.nilai_grade, 
                ku.remidi, 
                ku.remidi_karena,

                k.nik,
                k.nama,
                k.id_wil,

                w.kode,
                w.nama nm_wil,
                wk.standar,

                up.id_periode,
                up.id_ujian, 
                up.tanggal jadwal

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

            LEFT JOIN
                wilayah_kelompok wk
            ON
                w.id_kelompok = wk.id

            LEFT JOIN
                ujian_pelaksanaan up
            ON
                ku.id_pelaksanaan = up.id

            WHERE
                ku.hapus = '0'
            AND
                up.id_ujian = '$id_ujian'
            AND
                up.aktif = '1'
            AND
                up.hapus = '0'
    ";

    if($jmlPeriode>1){
        $q .= "
                AND
                (
        ";
    }

    for ($i=1; $i <= $jmlPeriode; $i++) { 
        $x = $i-1;
        $id_periode = saring($idP[$x]);

        if($jmlPeriode>1){
            if($i>1){
                $q .= "
                    OR
                ";
            }
        }
        else{
            $q .= "
                AND
            ";
        }

        $q .= "
                    up.id_periode = '$id_periode'
        ";
    }

    if($jmlPeriode>1){
        $q .= "
                )
        ";
    }


    if($jmlWil>1){
        $q .= "
                AND
                (
        ";
    }

    for ($i=1; $i <= $jmlWil; $i++) { 
        $x = $i-1;
        $id_wil = saring($idW[$x]);

        if($jmlWil>1){
            if($i>1){
                $q .= "
                    OR
                ";
            }
        }
        else{
            $q .= "
                AND
            ";
        }

        $q .= "
                    w.id = '$id_wil'
        ";
    }

    if($jmlWil>1){
        $q .= "
                )
        ";
    }



    $q .= "
            ORDER BY
                up.id_periode ASC,
                w.id ASC
    ";


    //echo $q.'<br><br>';

    

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


    $r = array();
    $ar = array();

    while ($d = mysqli_fetch_assoc($e)) {
        $r['id'] = $d['id'];
        $r['id_pelaksanaan'] = $d['id_pelaksanaan'];
        $r['id_karyawan'] = $d['id_karyawan'];
        $r['mengerjakan'] = $d['mengerjakan'];
        $r['nilai_akhir'] = $d['nilai_akhir'];
        $r['n'] = $d['n'];
        $r['n_pk'] = $d['n_pk'];
        $r['nilai_grade'] = $d['nilai_grade'];
        $r['remidi'] = $d['remidi'];
        $r['remidi_karena'] = $d['remidi_karena'];

        $r['nik'] = $d['nik'];
        $r['nama'] = $d['nama'];
        
        $r['id_wil'] = $d['id_wil'];
        $r['kode'] = $d['kode'];
        $r['nm_wil'] = $d['nm_wil'];
        $r['standar'] = $d['standar'];

        $r['id_periode'] = $d['id_periode'];
        $r['jadwal'] = $d['jadwal'];

        $ar[] = $r;
    }

    $car = $c-1;

    $arPeriode = array();

    $arWilayah = array();
    $arRefWilayah = array();

    for ($i=0; $i <= $car; $i++) {
        $idData = $ar[$i]['id'];
        $id_periode = $ar[$i]['id_periode'];

        $id_pelaksanaan = $ar[$i]['id_pelaksanaan'];

        $id_wil = $ar[$i]['id_wil'];
        $kode = $ar[$i]['kode'];
        $nm_wil = $ar[$i]['nm_wil'];
        $standar = $ar[$i]['standar'];


        $id_karyawan = $ar[$i]['id_karyawan'];
        $nik = $ar[$i]['nik'];
        $nama = $ar[$i]['nama'];

        $mengerjakan = $ar[$i]['mengerjakan'];
        $nilai_akhir = $ar[$i]['nilai_akhir'];
        $n = $ar[$i]['n'];
        $n_pk = $ar[$i]['n_pk'];
        $nilai_grade = $ar[$i]['nilai_grade'];
        $remidi = $ar[$i]['remidi'];
        $remidi_karena = $ar[$i]['remidi_karena'];

        if($i==0){
            array_push($arPeriode, $id_periode);
            $ins = array(
                'id_wil' => $id_wil,
                'kode' => $kode,
                'nm_wil' => $nm_wil,
                'standar' => $standar
            );

            $arWilayah[$id_wil] = $ins;

            array_push($arRefWilayah, $id_wil);



            $w = 'arWil'.$id_periode;
            $$w = array();
            array_push($$w, $id_wil);

            $p = 'jmlPeserta'.$id_periode.''.$id_wil;
            $$p = 0;
            $$p = $$p + 1;

            $l = 'jmlLulus'.$id_periode.''.$id_wil;
            $$l = 0;
            
            $r = 'jmlRemidi'.$id_periode.''.$id_wil;
            $$r = 0;
            
            if($remidi=='0'){
                $$l = $$l + 1;
            }
            else{
                $$r = $$r + 1;
            }
        }
        else{

            $p = 'jmlPeserta'.$id_periode.''.$id_wil;
            $l = 'jmlLulus'.$id_periode.''.$id_wil;
            $r = 'jmlRemidi'.$id_periode.''.$id_wil;

            $x = $i-1;
            $id_periode_prev = $ar[$x]['id_periode'];
            if($id_periode_prev!==$id_periode){
                array_push($arPeriode, $id_periode);

                $w = 'arWil'.$id_periode;
                $$w = array();
                array_push($$w, $id_wil);
            }

            if(!isset($$p)){
                $$p = 0;
                $$l = 0;                
                $$r = 0;
                
                //echo 'dibuatkan<br>';   
                //echo $idData.'<br>';
            }
            
            $$p = $$p + 1;

            if($remidi=='0'){
                $$l = $$l + 1;
            }
            else{
                $$r = $$r + 1;
            }

            

            $w = 'arWil'.$id_periode;
            //wialayah apa sudah tertampung?
            if(!in_array($id_wil, $$w)){
                array_push($$w, $id_wil);
            }

            //tampung wilayah referensi
            if(!in_array($id_wil, $arRefWilayah)){
                array_push($arRefWilayah, $id_wil);

                $ins = array(
                    'id_wil' => $id_wil,
                    'kode' => $kode,
                    'nm_wil' => $nm_wil,
                    'standar' => $standar
                );

                $arWilayah[$id_wil] = $ins;
            }
            
        }
    }



    $judul = 'Rekapitulasi standar UN';
    $deskUjian = 'Kelulusan peserta periode ';

    for ($i=0; $i < $jmlPeriode; $i++) { 
        if($i>0){
            $deskUjian .= ", ";
        }

        $idPeriode = saring($arPeriode[$i]);
        $deskUjian .= $idPeriode;

        if($i==0){
            $pointStart = $idPeriode;
        }
    }

    $deskUjian .= "<br> Wilayah ";

    for ($i=0; $i < $jmlWil; $i++) {
        $id_wil = $arRefWilayah[$i];
        $nm_wil = saring($arWilayah[$id_wil]['nm_wil']);
        if($i>0){
            $deskUjian .= ", ";
        }
        $deskUjian .= $nm_wil;
    }

?>

        <h3 class="ui header block segment">
            <i class="file text icon"></i>
            <div class="content">
                <?php echo $judul; ?>
                <div class="sub header">
                    <?php echo $deskUjian; ?>
                </div>
            </div>
        </h3>


<?php
    
?>

        <div id="globalData" class="ui segment" style="width: 100%; height: auto;">
            <!--
                populate chart here
            -->
        </div>


        
        

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
                $('#globalData').highcharts({
                    title: {
                        text: '<?php echo $judul; ?>'
                    },

                    subtitle: {
                        text: '<?php echo $deskUjian; ?>'
                    },

                    yAxis: {
                        title: {
                            text: 'Jumlah (%)'
                        }
                    },
                    legend: {
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign: 'middle'
                    },

                    plotOptions: {
                        series: {
                            label: {
                                connectorAllowed: false
                            },
                            pointStart: <?php echo $pointStart; ?>
                        }
                    },
                    series: [
<?php
    $maxWil = $jmlWil-1;
    for ($x=0; $x < $jmlWil; $x++) {
        $id_wil = $arRefWilayah[$x];
        
        $nm_wil = $arWilayah[$id_wil]['nm_wil'];
        $standar = $arWilayah[$id_wil]['standar'];

        echo "
                    {
                        name: 'Standar $nm_wil',
                        data: [";
        $maxPer = $jmlPeriode-1;
        for ($i=0; $i < $jmlPeriode; $i++) { 
            $idPeriode = saring($arPeriode[$i]);

                        echo $standar;

                        if($i < $maxPer){
                            echo ",";
                        }
        }                        
                 echo   "]
                    }
        ";

        if($x<$maxWil){
            echo ",";
        }
    }
?>  
,
<?php
    $maxWil = $jmlWil-1;
    for ($x=0; $x < $jmlWil; $x++) {
        $id_wil = $arRefWilayah[$x];
        
        $nm_wil = $arWilayah[$id_wil]['nm_wil'];
        $standar = $arWilayah[$id_wil]['standar'];

        echo "
                    {
                        name: 'Lulus $nm_wil',
                        data: [";
        $maxPer = $jmlPeriode-1;
        for ($i=0; $i < $jmlPeriode; $i++) { 
            $idPeriode = saring($arPeriode[$i]);

            $p = 'jmlPeserta'.$idPeriode.''.$id_wil;
            $l = 'jmlLulus'.$idPeriode.''.$id_wil;
            $r = 'jmlRemidi'.$idPeriode.''.$id_wil;

            $peserta = $$p;
            $lulus = $$l;
            $remidi = $$r;
            $per = ($$l / $$p) * 100;

                        echo round($per);

                        if($i < $maxPer){
                            echo ",";
                        }
        }                        
                 echo   "]
                    }
        ";
        
        if($x<$maxWil){
            echo ",";
        }
    }
?>                    
                    
                    ],
                    responsive: {
                        rules: [{
                            condition: {
                                maxWidth: 500
                            },
                            chartOptions: {
                                legend: {
                                    layout: 'horizontal',
                                    align: 'center',
                                    verticalAlign: 'bottom'
                                }
                            }
                        }]
                    }
           
    
                    
                })


            })
        </script>
        
    </body>
</html>