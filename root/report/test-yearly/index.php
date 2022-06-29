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
    if(empty($_GET['kode'])||empty($_GET['encrypt'])||empty($_GET['id_periode'])){
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

    $encrypt = saring($_GET['encrypt']);
    $id_periode = saring($_GET['id_periode']);

    $id_ujian = '5483-8ABF1C';

    $q = "
        SELECT 
            up.id, 
            up.id_ujian, 
            up.kkm, 
            up.tanggal, 
            up.waktu, 
            up.tampilan,

            u.nama namaUjian, 
            u.deskripsi deskUjian,

            (
                SELECT COUNT(id_karyawan)
                FROM
                    ujian_pelaksanaan_target_karyawan 

                WHERE
                    id_pelaksanaan = up.id
                AND
                    hapus = '0'
            ) jmlPeserta,

            (
                SELECT COUNT(id_karyawan)
                FROM
                    ujian_pelaksanaan_target_karyawan 

                WHERE
                    id_pelaksanaan = up.id
                AND
                    susulan = '1'
                AND
                    hapus = '0'
            ) jmlPesertaSusulan,

            (
                SELECT COUNT(id)
                FROM
                    karyawan_ujian 

                WHERE
                    id_pelaksanaan = up.id

                AND
                    tanggal != '0000-00-00'
                AND
                    nilai_akhir != ''
                AND
                    hapus = '0'
            ) jmlMengerjakan,

            (
                SELECT COUNT(id) 
                FROM
                    karyawan_ujian 

                WHERE
                    id_pelaksanaan = up.id

                AND
                    tanggal != '0000-00-00'
                AND
                    remidi = '0'
                AND
                    hapus = '0'
            ) jmlLulus,

            (
                SELECT COUNT(id) 
                FROM
                    karyawan_ujian 

                WHERE
                    id_pelaksanaan = up.id

                AND
                    tanggal != '0000-00-00'
                AND
                    remidi = '1'
                AND
                    hapus = '0'
            ) jmlTdkLulus

        FROM 
            ujian_pelaksanaan up

        LEFT JOIN
            ujian u
        ON
            up.id_ujian = u.id

        WHERE
            up.id_periode = '$id_periode'
        AND
            up.id_ujian = '$id_ujian'
        AND
            up.aktif = '1'
        AND
            up.hapus = '0'

        ORDER BY
            up.tanggal ASC
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

    $namaUjian = 'Ujian Nasional';
    $deskUjian = 'Info grafik status kelulusan peserta ujian';
?>

        <h3 class="ui header block segment">
            <i class="file text icon"></i>
            <div class="content">
                Perkembangan <?php echo $namaUjian; ?>
                <div class="sub header">
                    <?php echo $deskUjian; ?>
                </div>
            </div>
        </h3>


<?php
    $rIdPelaksanaan = array();
    $jmlIdPelaksanaan = 0;

    $r = array();
    $ar = array();

    while ($d = mysqli_fetch_assoc($e)) {
        $r['idPelaksanaan'] = $d['id'];
        $r['kkm'] = $d['kkm'];
        $r['tanggal'] = $d['tanggal'];
        $r['waktu'] = $d['waktu'];
        $r['jmlPeserta'] = $d['jmlPeserta'];
        $r['jmlPesertaSusulan'] = $d['jmlPesertaSusulan'];
        $r['jmlMengerjakan'] = $d['jmlMengerjakan'];
        $r['jmlLulus'] = $d['jmlLulus'];
        $r['jmlTdkLulus'] = $d['jmlTdkLulus'];

        $ar[] = $r;
    }

    $arData = array();
    $car = $c-1;

    $adaKerja = 0;

    $tglAwal = '';
    $tglAkhir = '';

    for ($i=0; $i <= $car; $i++) {
        $idPelaksanaan = $ar[$i]['idPelaksanaan'];
        $kkm = $ar[$i]['kkm'];
        $tanggal = $ar[$i]['tanggal'];
        $waktu = $ar[$i]['waktu'];
        $jmlPeserta = $ar[$i]['jmlPeserta'];
        $jmlPesertaSusulan = $ar[$i]['jmlPesertaSusulan'];
        $jmlMengerjakan = $ar[$i]['jmlMengerjakan'];
        if($jmlMengerjakan>0){
            $adaKerja = '1';
        }
        $jmlLulus = $ar[$i]['jmlLulus'];
        $jmlTdkLulus = $ar[$i]['jmlTdkLulus'];

        $ins = array(
            'idP' => $idPelaksanaan,
            'kkm' => $kkm,
            'waktu' => $waktu,
            'tgl' => $tanggal,
            'jmlPeserta' => $jmlPeserta,
            'jmlSusulan' => $jmlPesertaSusulan,
            'jmlKerja' => $jmlMengerjakan,
            'jmlLulus' => $jmlLulus,
            'jmlGagal' => $jmlTdkLulus
        );

        if($i==0){
            $arData[] = $ins;
            $jmlIdPelaksanaan = $jmlIdPelaksanaan+1;

            $tglAwal = $tanggal;
        }
        else{
            $z = $i-1;
            $idPrev = $ar[$z]['idPelaksanaan'];
            if($idPelaksanaan!==$idPrev){
                $arData[] = $ins;
                $jmlIdPelaksanaan = $jmlIdPelaksanaan+1;

                if($i==$car){
                    $tglAkhir = $tanggal;
                }
            }
        }
    }

    if($tglAkhir==''){
        $subJudul = tanggalKan($tglAwal);
    }
    else{
        $subJudul = tanggalKan($tglAwal).' sampai '.tanggalKan($tglAkhir).' ('.$jmlIdPelaksanaan.' kali ujian)';
    }
    

    //print_r($arData);

    if($adaKerja==0){
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
                        text: 'Kondisi pelaksanaan ujian tahunan'
                    },

                    subtitle: {
                        text: '<?php echo $subJudul; ?>'
                    },

                    yAxis: {
                        title: {
                            text: 'Jumlah'
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
                            pointStart: 1
                        }
                    },

                    series: [
                        {
                            name: 'Nilai Min.',
                            data:[
                    <?php
                        for ($y=1; $y <= $jmlIdPelaksanaan; $y++) {
                            $no = $y-1;
                            $kkm = $arData[$no]['kkm'];

                            echo $kkm;

                            if($jmlIdPelaksanaan>1){
                                if($y<$jmlIdPelaksanaan){
                                    echo ",";
                                }
                            }
                        }
                    ?>
                            ]
                        },
                        {
                            name: 'Waktu',
                            data:[
                    <?php
                        for ($y=1; $y <= $jmlIdPelaksanaan; $y++) {
                            $no = $y-1;
                            $waktu = $arData[$no]['waktu'];
                            
                            echo $waktu;

                            if($jmlIdPelaksanaan>1){
                                if($y<$jmlIdPelaksanaan){
                                    echo ",";
                                }
                            }
                        }
                    ?>
                            ]
                        },
                        {
                            name: 'Peserta',
                            data:[
                    <?php
                        for ($y=1; $y <= $jmlIdPelaksanaan; $y++) {
                            $no = $y-1;
                            $jmlPeserta = $arData[$no]['jmlPeserta'];
                            

                            echo $jmlPeserta;

                            if($jmlIdPelaksanaan>1){
                                if($y<$jmlIdPelaksanaan){
                                    echo ",";
                                }
                            }
                        }
                    ?>
                            ]
                        },
                        {
                            name: 'Susulan',
                            data:[
                    <?php
                        for ($y=1; $y <= $jmlIdPelaksanaan; $y++) {
                            $no = $y-1;
                            $jmlSusulan = $arData[$no]['jmlSusulan'];
                            
                            echo $jmlSusulan;

                            if($jmlIdPelaksanaan>1){
                                if($y<$jmlIdPelaksanaan){
                                    echo ",";
                                }
                            }
                        }
                    ?>
                            ]
                        },   
                        {
                            name: 'Mengerjakan',
                            data:[
                    <?php
                        for ($y=1; $y <= $jmlIdPelaksanaan; $y++) {
                            $no = $y-1;
                            $jmlKerja = $arData[$no]['jmlKerja'];
                            
                            echo $jmlKerja;

                            if($jmlIdPelaksanaan>1){
                                if($y<$jmlIdPelaksanaan){
                                    echo ",";
                                }
                            }
                        }
                    ?>
                            ]
                        },
                        {
                            name: 'Tidak mengerjakan',
                            data:[
                    <?php
                        for ($y=1; $y <= $jmlIdPelaksanaan; $y++) {
                            $no = $y-1;
                            $jmlPeserta = $arData[$no]['jmlPeserta'];
                            $jmlKerja = $arData[$no]['jmlKerja'];
                            $jmlTidak = $jmlPeserta-$jmlKerja;

                            echo $jmlTidak;

                            if($jmlIdPelaksanaan>1){
                                if($y<$jmlIdPelaksanaan){
                                    echo ",";
                                }
                            }
                        }
                    ?>
                            ]
                        },
                        {
                            name: 'Lulus',
                            data:[
                    <?php
                        for ($y=1; $y <= $jmlIdPelaksanaan; $y++) {
                            $no = $y-1;
                            $jmlLulus = $arData[$no]['jmlLulus'];

                            echo $jmlLulus;

                            if($jmlIdPelaksanaan>1){
                                if($y<$jmlIdPelaksanaan){
                                    echo ",";
                                }
                            }
                        }
                    ?>
                            ]
                        },
                        {
                            name: 'Tidak lulus',
                            data:[
                    <?php
                        for ($y=1; $y <= $jmlIdPelaksanaan; $y++) {
                            $no = $y-1;
                            $jmlGagal = $arData[$no]['jmlGagal'];

                            echo $jmlGagal;

                            if($jmlIdPelaksanaan>1){
                                if($y<$jmlIdPelaksanaan){
                                    echo ",";
                                }
                            }
                        }
                    ?>
                            ]
                        },
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