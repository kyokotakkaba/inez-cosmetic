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
                a.id, 
                a.judul, 
                a.deskripsi, 
                a.responden,
                a.hapus,

                p.nama produk,
                k.nama produk_kategori
            FROM 
                angket a

            LEFT JOIN
                produk p
            ON
                a.id_produk = p.id

            LEFT JOIN
                produk_kategori k
            ON
                p.id_kategori = k.id

            WHERE
                a.kode = '$kode'

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
                    Survey yang coba anda buka telah dihapus.
                </p>
            </div>
        </div>
<?php
        exit();
    }

    $id_angket = $r['id'];
    $judul = $r['judul'];
    $deskripsi = $r['deskripsi'];
    $responden = $r['responden'];
?>

        <h3 class="ui header block segment">
            <i class="file text icon"></i>
            <div class="content">
                Laporan Hasil Survey
                <div class="sub header">
                    <?php echo $judul; ?>
                </div>
            </div>
        </h3>

<?php
    //check the target
    $q = "
            SELECT 
                ar.id, 
                ar.id_periode,
                ar.id_karyawan, 
                ar.id_item, 
                ar.respon, 
                ar.tanggal, 
                ar.jam,

                ai.deskripsi item,

                (
                    SELECT COUNT(respon)
                    FROM
                        angket_respon
                    WHERE
                        respon = '1'
                    AND
                        id_periode = ar.id_periode
                    AND
                        id_item = ar.id_item
                ) jml1,

                (
                    SELECT COUNT(respon)
                    FROM
                        angket_respon
                    WHERE
                        respon = '2'
                    AND
                        id_periode = ar.id_periode
                    AND
                        id_item = ar.id_item
                ) jml2,

                (
                    SELECT COUNT(respon)
                    FROM
                        angket_respon
                    WHERE
                        respon = '3'
                    AND
                        id_periode = ar.id_periode
                    AND
                        id_item = ar.id_item
                ) jml3,

                (
                    SELECT COUNT(respon)
                    FROM
                        angket_respon
                    WHERE
                        respon = '4'
                    AND
                        id_periode = ar.id_periode
                    AND
                        id_item = ar.id_item
                ) jml4,

                (
                    SELECT SUM(respon)
                    FROM
                        angket_respon
                    WHERE
                        respon = '1'
                    AND
                        id_periode = ar.id_periode
                    AND
                        id_item = ar.id_item
                ) total1,

                (
                    SELECT SUM(respon)
                    FROM
                        angket_respon
                    WHERE
                        respon = '2'
                    AND
                        id_periode = ar.id_periode
                    AND
                        id_item = ar.id_item
                ) total2,

                (
                    SELECT SUM(respon)
                    FROM
                        angket_respon
                    WHERE
                        respon = '3'
                    AND
                        id_periode = ar.id_periode
                    AND
                        id_item = ar.id_item
                ) total3,

                (
                    SELECT SUM(respon)
                    FROM
                        angket_respon
                    WHERE
                        respon = '4'
                    AND
                        id_periode = ar.id_periode
                    AND
                        id_item = ar.id_item
                ) total4,

                (
                    SELECT SUM(respon)
                    FROM
                        angket_respon
                    WHERE
                        id_periode = ar.id_periode
                    AND
                        id_item = ar.id_item
                ) totalall

            FROM 
                angket_respon ar

            LEFT JOIN
                angket_item ai
            ON
                ar.id_item = ai.id

            WHERE
                ar.id_angket  = '$id_angket'

            ORDER BY
                ar.id_periode DESC,
                ar.id_item ASC
    ";

    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);

    $jmlRespon = $c;

    if($jmlRespon==0){
?>
        <div class="ui icon message">
            <i class="inbox icon"></i>
            <div class="content">
                <div class="header">
                    Laporan belum tersedia
                </div>
                <p>
                    Belum ada yang mengisi survey ini 
                </p>
            </div>
        </div>
<?php
        exit();
    }

    $arPeriode = array();
    $jmlPeriode = 0;

    $arIdKaryawan = array();
    $jmlResponden = 0;


    $arIdItem = array();
    $arItem = array();
    $jmlItem = 0;

    $ar = array();
    $r = array();

    while ($d = mysqli_fetch_assoc($e)) {
        $idPeriode = $d['id_periode'];
        $id_karyawan = $d['id_karyawan'];
        
        $id_item = $d['id_item'];
        $respon = $d['respon'];
        
        $item = $d['item'];

        $totalall = $d['totalall'];
        
        $r['idPeriode'] = $idPeriode;
        $r['IdKaryawan'] = $id_karyawan;
        $r['idItem'] = $id_item;
        $r['item'] = $item;
        $r['respon'] = $respon;
        $r['totalall'] = $totalall;
        
        $ar[] = $r;
    }

    $jar = $c-1;
?>
        <div id="globalResult" class="ui segment" style="width: 100%; height: auto;">
            <!--
                populate chart here
            -->
        </div>

        <table class="ui table">
            <thead>
                <th width="4%">No</th>
                <th>Item</th>
            </thead>
            <tbody>
<?php
    $num = 1;

    for ($k=1; $k <= $jar; $k++) {
        $arN = $k-1;
        $idPeriode = $ar[$arN]['idPeriode'];
        $idItem = $ar[$arN]['idItem'];
        $item = $ar[$arN]['item'];
        $respon = $ar[$arN]['respon'];

        $totalall = $ar[$arN]['totalall'];

        if(!in_array($idPeriode, $arPeriode)){
            array_push($arPeriode, $idPeriode);
            $jmlPeriode = $jmlPeriode+1;

            $ito = 'JmlRespon'.$idPeriode;
            $$ito = array();
        }

        if(!in_array($idItem, $arIdItem)){
            array_push($arIdItem, $idItem);
            array_push($arItem, $item);
            $jmlItem = $jmlItem+1;
            $ito = 'JmlRespon'.$idPeriode;
            $into = array(
                $idItem => $totalall
            );

            array_push($$ito, $into);
?>
            <tr>
                <td><?php echo $num; ?></td>
                <td><?php echo html_entity_decode($item); ?></td>
            </tr>
<?php           
            $num = $num+1;
        }
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
                $('#globalResult').highcharts({
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: 'Data Respon Survey'
                    },
                    subtitle: {
                        text: '<?php echo $judul; ?>'
                    },
                    xAxis: {
                        categories: [

                        <?php
                            for ($p=1; $p <= $jmlPeriode; $p++) { 
                                $nom = $p-1;
                                $periode = $arPeriode[$nom];
                                echo "'".$periode."'";
                                if($jmlPeriode>1){
                                    if($p<$jmlPeriode){
                                        echo ",";
                                    }
                                }
                            }
                        ?>
                        ],
                        crosshair: true
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: 'Jumlah'
                        }
                    },
                    tooltip: {
                        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                            '<td style="padding:0"><b>{point.y:.1f} mm</b></td></tr>',
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
                    <?php
                        for ($i=1; $i <= $jmlItem; $i++) {
                            $nom = $i-1;
                            echo "{
                                name: '".$i."',
                                data: [";
                            
                            for ($p=1; $p <= $jmlPeriode; $p++) {
                                $go = $p-1;
                                $periode = $arPeriode[$go];
                                
                                $ito = 'JmlRespon'.$periode;
                                $idItem = $arIdItem[$nom];
                                $jml = ${$ito}[$nom][$idItem];

                                echo $jml;

                                if($jmlPeriode>1){
                                    if($p<$jmlPeriode){
                                        echo ",";
                                    }
                                }
                            }

                            echo "]
                            }
                            ";
                            if($jmlItem>1){
                                if($i<$jmlItem){
                                    echo ",";
                                }
                            }
                        }
                    ?>
                    ]
                })


                



            })
        </script>
        
    </body>
</html>