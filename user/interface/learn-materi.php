<?php
    session_start();
    $appSection = 'user';

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

    $idPengguna = $_SESSION['idPengguna'];
    $idBahasan   = saring($_POST['id_bahasan']);
    $noTingkat   = saring($_POST['no_tingkat']);

    $q="
        SELECT 
            m.id idMateri, 
            m.judul, 
            m.deskripsi, 
            m.baner, 
            m.kode, 
            m.no noMateri,

            b.nama bahasan, 
            b.no noBahasan, 

            k.nama kelompok, 

            t.nama tingkat,

            l.id idBLast, 
            l.tanggal, 
            l.jam,
            l.last

        FROM 
            materi m

        LEFT JOIN
            materi_kelompok_bahasan b
        ON
            m.id_bahasan = b.id

        LEFT JOIN
            materi_kelompok k
        ON
            b.id_kelompok = k.id

        LEFT JOIN
            tingkat_belajar t
        ON
            m.id_tingkat_belajar = t.id

        LEFT JOIN
            karyawan_belajar_materi l
        ON
            l.id_materi = m.id
        AND
            l.id_karyawan = '$idPengguna'

        WHERE
            m.hapus = '0'
        AND
            m.id_bahasan = '$idBahasan'
        AND
            (
                t.no <= '$noTingkat'
        OR
                m.id_tingkat_belajar = 'semua'
            )
    
        ORDER BY
            m.no ASC
    ";

    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);

    if($c>0){
        $ar = array();
        $r = array();

        while ($d = mysqli_fetch_assoc($e)) {
            $r['idMateri']            = $d['idMateri'];

            $r['judul']          = $d['judul'];
            $r['deskripsi']             = $d['deskripsi'];
            $r['baner']            = $d['baner'];
            $r['kode']          = $d['kode'];
            $r['bahasan']          = $d['bahasan'];
            $r['kelompok']          = $d['kelompok'];
            $r['tingkat']            = $d['tingkat'];

            $r['idBLast']            = $d['idBLast'];
            $r['tanggal']            = $d['tanggal'];
            $r['jam']            = $d['jam'];
            $r['last']            = $d['last'];
            $r['noMateri']            = $d['noMateri'];
            
            $ar[]   = $r;
        }

        $cAr = $c-1;
        $nomor = 1;
        $idLast = '-';
        $noLast = '0';

?>
        <table class="ui very basic inline table unstackable">
            <tbody>
<?php        

        for ($i=0; $i <= $cAr; $i++) {
            $idMateri = $ar[$i]['idMateri'];
            $judul = $ar[$i]['judul'];
            $deskripsi = $ar[$i]['deskripsi'];

            $banerUrl = $ar[$i]['baner'];
            $baner = '../files/photo/agenda.png';
            if(!empty($banerUrl) && $banerUrl!==''){
                $banerUrl = str_replace('%20', ' ', $banerUrl);
                if(file_exists('../../'.$banerUrl)){
                    $baner = '../'.$banerUrl;
                }
            }

            $kode = $ar[$i]['kode'];

            $bahasan = $ar[$i]['bahasan'];
            $kelompok = $ar[$i]['kelompok'];
            $tingkatB = $ar[$i]['tingkat'];

            if($tingkatB==''){
                $tingkat = 'semua';
            }
            else{
                $tingkat = $tingkatB;
            }

            $idBLast = $ar[$i]['idBLast'];
            $tanggal = $ar[$i]['tanggal'];
            $jam = $ar[$i]['jam'];

            $last = $ar[$i]['last'];
            $no = $ar[$i]['noMateri'];
            $idData = $idBahasan.'[pisah]'.$idMateri;
?>
            <tr>
                <td>
                    <h5 class="ui header">
                        <img class="ui image" src="<?php echo $baner; ?>">
                        <div class="content">
                            <?php echo $judul; ?>
                            <div class="sub header">
                                <p>
                                    <?php echo $deskripsi; ?>
                                </p>
                            </div>
                        </div>
                    </h5>
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
            $idLast = $idBLast;
            $noLast = $i;
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
                        <div id="btn<?php echo $no; ?>" class="ui right floated inverted button" onclick="loadFormSub('materi','<?php echo $idData; ?>')" style="<?php echo $accentColor; ?>">
                            Buka
                        </div>
<?php
    }

    if($i>0){
        if($last=='1'){
            $idLast = $idBLast;
            $noLast = $i;
            $classBaca = '';
        }
        else{
            if(!empty($idBLast)){
                $classBaca = '';
            }
            else{
                $classBaca = 'disabled';
            }
        }
?>
                        <div id="btn<?php echo $no; ?>" class="ui right floated inverted button <?php echo $classBaca; ?>" onclick="loadFormSub('materi','<?php echo $idData; ?>')" style="<?php echo $accentColor; ?>" >
                            Buka
                        </div>
<?php        
    }
?>   
                </td>
            </tr>
            
<?php               
            $nomor = $nomor+1;            
        }
?>
            </tbody>
        </table>
<?php        
    }
    else{
?>
        <div class="ui floating message">
            <i class="info circle icon teal"></i> <i>Belum ada data.</i>
        </div>
<?php       
    }
?>



<script type="text/javascript">
	$('.button, .popup, .label').popup();
</script>