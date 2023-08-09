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


    $r = mysqli_fetch_assoc($e);

    $hapus = $r['hapus'];
    if($hapus=='1'){
?>
        <div class="ui icon message">
            <i class="inbox icon"></i>
            <div class="content">
                <div class="header">
                    Laporan tidak tersedia
                </div>
                <p>
                    Ujian yang coba anda buka telah dihapus.
                </p>
            </div>
        </div>
<?php
        exit();
    }

    $id_pelaksanaan = $r['id'];

    $kkm = $r['kkm'];
    $tanggal = $r['tanggal'];
    $waktu = $r['waktu'];

    $id_ujian = $r['id_ujian'];
    $namaUjian = $r['namaUjian'];
    $deskUjian = $r['deskUjian'];
?>

        <h3 class="ui header block segment">
            <i class="file text icon"></i>
            <div class="content">
                Laporan <?php echo $namaUjian; ?>
                <div class="sub header">
                    Terjadwal pada : <?php echo tanggalKan($tanggal); ?><br>
                    Waktu pengerjaan : <?php echo  $waktu; ?> (menit) <br>
                    Nilai Min. Lulus : <?php echo $kkm; ?>
                </div>
            </div>
        </h3>


<?php
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
                ku.id_karyawan, 
                ku.tanggal, 
                ku.mulai, 
                ku.selesai, 
                ku.nilai_akhir,
                ku.n,
                ku.n_pk,
                ku.nilai_grade,
                ku.remidi,

                k.nik,
                k.nama,
                k.jk,
                k.tmpt_lahir,
                k.tgl_lahir,
                k.foto

            FROM 
                karyawan_ujian ku

            LEFT JOIN
                karyawan k
            ON
                ku.id_karyawan = k.id

            WHERE
                ku.id_pelaksanaan = '$id_pelaksanaan'
            AND
                ku.hapus = '0'
            AND
                ku.nilai_akhir != ''

            ORDER BY
                k.nik ASC
    ";

    //echo $q.'<br><br>';

    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);

    if($c==0){
?>
        <div class="ui icon message">
            <i class="inbox icon"></i>
            <div class="content">
                <div class="header">
                    Laporan belum tersedia
                </div>
                <p>
                    Belum ada yang mengikuti ujian ini 
                </p>
            </div>
        </div>
<?php
        exit();
    }

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
        $id_karyawan = $d['id_karyawan'];
        $nik = $d['nik'];
        $nama = $d['nama'];
        $jk = $d['jk'];
        $foto = $d['foto'];
        $tmpt_lahir = $d['tmpt_lahir'];
        $tgl_lahir = $d['tgl_lahir'];

        $tglKerja = $d['tanggal'];
        $mulai = $d['mulai'];
        $selesai = $d['selesai'];
        $nilai_akhir = $d['nilai_akhir'];
        $n = $d['n'];
        $n_pk = $d['n_pk'];
        $nilai_grade = $d['nilai_grade'];

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
        
        $r['id_karyawan'] = $id_karyawan;
        $r['nik'] = $nik;
        $r['nama'] = $nama;
        $r['tmpt_lahir'] = $tmpt_lahir;
        $r['tgl_lahir'] = $tgl_lahir;
        $r['jk'] = $jk;
        $r['foto'] = $foto;

        $ar[] = $r;

        $nomor = $nomor+1;
    }

    $jar = $c-1;

    $jmlPesertaBelumUjian = $jmlPesertaUjian - $jmlPesertaMengerjakan;

?>

        <div class="ui grid two column stackable">
            <div class="column">
                <div id="kepesertaan" class="ui segment" style="width: 100%; height: auto;">
                    <!--
                        populate chart here
                    -->
                </div>
            </div>
            <div class="column">
                <div id="kelulusan" class="ui segment" style="width: 100%; height: auto;">
                    <!--
                        populate chart here
                    -->
                </div>
            </div>
        </div>


        <div id="sebaranNilai" class="ui segment" style="width: 100%; height: auto;">
            <!--
                populate chart here
            -->
        </div>

        <table class="ui table">
            <thead>
                <th width="4%">No</th>
                <th>Peserta</th>
                <th width="40%">Waktu</th>
                <th width="14%">Nilai</th>
            </thead>
            <tbody>
<?php
    for ($k=1; $k <= $jmlPesertaMengerjakan; $k++) {
        $arN = $k-1;
        $nik = $ar[$arN]['nik'];
        $nama = $ar[$arN]['nama'];
        $tmpt_lahir = $ar[$arN]['tmpt_lahir'];
        $tgl_lahir = $ar[$arN]['tgl_lahir'];
        $jk = $ar[$arN]['jk'];
        $foto = $ar[$arN]['foto'];

        if($foto==''){
            $avatar = '../../../files/photo/'.$jk.'.png';
        }
        else{
            $foto = str_replace('%20', ' ', $foto);
            if(file_exists('../../../'.$foto)){
                $avatar = '../../../'.$foto;
            }
            else{
                $avatar = '../../../files/photo/'.$jk.'.png';
            }
        }

        $tglKerja = $ar[$arN]['tglKerja'];
        $mulai = $ar[$arN]['mulai'];

        $teksInfo = 'Pada '.tanggalKan($tglKerja).' <br> Waktu '.$mulai.' sampai '.$selesai;

        $tglKerja = $ar[$arN]['tglKerja'];
        if($tglKerja>$tanggal){
            $teksInfo .= '<br>[Susulan]';
        }

        $nilai = round($ar[$arN]['nilai'],2);
        if($nilai < $kkm){
            $classStatus = 'negative';
        }
        else{
            $classStatus = '';
        }
?>
                <tr class="<?php echo $classStatus; ?>">
                    <td><?php echo $k; ?></td>
                    <td>
                        <h4 class="ui image header">
                            <img src="<?php echo $avatar; ?>" class="ui mini rounded image">
                            <div class="content">
                                <?php echo $nik; ?>
                                <div class="sub header">
                                    <?php echo $nama; ?>
                                </div>
                            </div>
                        </h4>
                    </td>
                    <td>
                        <?php echo $teksInfo; ?>
                    </td>
                    <td><?php echo $nilai; ?></td>
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
                $('#kepesertaan').highcharts({
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false,
                        type: 'pie'
                    },
                    title: {
                            text: 'Grafik Data Peserta'
                    },
                    subtitle: {
                        text: "Jumlah <?php echo $jmlPesertaUjian; ?>"
                    },
                    tooltip: {
                        pointFormat: '{series.name}: <b>{point.y}</b>'
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b>: {point.y}',
                                style: {
                                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                                }
                            }
                        }
                    },
                    series: [{
                        name: 'Jumlah',
                        colorByPoint: true,
                        data: [{
                            name: 'Langsung',
                            y: <?php echo $jmlPesertaMengerjakanAlsi; ?>,
                            sliced: true,
                            selected: true
                        }, {
                            name: 'Susulan',
                            y: <?php echo $jmlPesertaMengerjakanSusulan; ?>
                        }, {
                            name: 'Belum',
                            y: <?php echo $jmlPesertaBelumUjian; ?>
                        }]
                    }]
                })


                $('#kelulusan').highcharts({
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false,
                        type: 'pie'
                    },
                    title: {
                            text: 'Status Kelulusan'
                    },
                    subtitle: {
                        text: "Nilai Min. = <?php echo $kkm; ?>"
                    },
                    tooltip: {
                        pointFormat: '{series.name}: <b>{point.y}</b>'
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b>: {point.y}',
                                style: {
                                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                                }
                            }
                        }
                    },
                    series: [{
                        name: 'Jumlah',
                        colorByPoint: true,
                        data: [{
                            name: 'Lulus',
                            y: <?php echo $jmlLulus; ?>,
                            sliced: true,
                            selected: true
                        }, {
                            name: 'Tidak Lulus',
                            y: <?php echo $jmlTidakLulus; ?>
                        }]
                    }]
                })


                $('#sebaranNilai').highcharts({
                    chart: {
                        type: 'scatter',
                        zoomType: 'xy'
                    },
                    title: {
                        text: 'Persebaran data nilai hasil ujian'
                    },
                    subtitle: {
                        text: "Jumlah <?php echo $jmlPesertaMengerjakan; ?>"
                    },
                    xAxis: {
                        title: {
                            enabled: true,
                            text: 'Peserta'
                        },
                        startOnTick: true,
                        endOnTick: true,
                        showLastLabel: true
                    },
                    yAxis: {
                        title: {
                            text: 'Nilai'
                        }
                    },
                    legend: {
                        layout: 'vertical',
                        align: 'left',
                        verticalAlign: 'top',
                        x: 100,
                        y: <?php echo $jmlPesertaUjian; ?>,
                        floating: true,
                        backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF',
                        borderWidth: 1
                    },
                    plotOptions: {
                        scatter: {
                            marker: {
                                radius: 5,
                                states: {
                                    hover: {
                                        enabled: true,
                                        lineColor: 'rgb(100,100,100)'
                                    }
                                }
                            },
                            states: {
                                hover: {
                                    marker: {
                                        enabled: false
                                    }
                                }
                            },
                            tooltip: {
                                headerFormat: '<b>{series.name}</b><br>',
                                pointFormat: 'No. {point.x}, Nilai {point.y}'
                            }
                        }
                    },

                    series: [
                    <?php
                        if($jmlLulus>0){
                    ?>
                                {
                            name: 'Lulus',
                            color: 'rgba(119, 152, 191, .5)',
                            data: [
                            <?php
                                for ($l=1; $l <= $jmlLulus; $l++) {
                                    $nomb = $l-1;
                                    $nomor = $arLulus[$nomb]['nomor'];
                                    $nilai = round($arLulus[$nomb]['nilai'],2);

                                    echo "[".$nomor.", ".$nilai."]";

                                    if($jmlLulus>1){
                                        if($l<$jmlLulus){
                                            echo ",";
                                        }
                                    }
                                }
                            ?>
                            ]
                        }

                    <?php
                        }

                        if($jmlLulus>0&&$jmlTidakLulus>0){
                            echo ",";
                        }

                        if($jmlTidakLulus>0){
                    ?>
                            {
                                name: 'Tidak Lulus',
                                color: 'rgba(223, 83, 83, .5)',
                                data: [
                                <?php
                                    for ($t=1; $t <= $jmlTidakLulus; $t++) {
                                        $nomb = $t-1;
                                        $nomor = $arTidakLulus[$nomb]['nomor'];
                                        $nilai = round($arTidakLulus[$nomb]['nilai'],2);

                                        echo "[".$nomor.", ".$nilai."]";

                                        if($jmlTidakLulus>1){
                                            if($t<$jmlTidakLulus){
                                                echo ",";
                                            }
                                        }
                                    }
                                ?>
                                    ]
                            }
                    <?php
                        }
                    ?>

                    ]
                })



            })
        </script>
        
    </body>
</html>