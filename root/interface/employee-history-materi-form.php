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

    $main = 'Data BA/ BC';
    $subsub = '';

    $mentah = saring($_POST['idData']);
    $pecah = explode('[pisah]', $mentah);

    $idPengguna  = saring($pecah[0]);

    $q = "
            SELECT
                nik,
                nama

            FROM
                karyawan

            WHERE
                id = '$idPengguna'

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
    $sub = $r['nik'].' - '.$r['nama'];

    $idBahasan   = saring($pecah[1]);

    $q = "
            SELECT
                nama

            FROM
                materi_kelompok_bahasan

            WHERE
                id = '$idBahasan'

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
    $subsub = 'History Belajar '.$r['nama'];


    $noTingkat   = saring($pecah[2]);

?>
    <div class="ui message">
        <div class="ui breadcrumb">
            <div class="section"><?php echo $main; ?></div>
            <i class="right angle icon divider"></i>
            <div class="section"><?php echo $sub; ?></div>
            <i class="right angle icon divider"></i>
            <div class="active section"><?php echo $subsub; ?></div>
        </div>
    </div>

    <div class="field">
        <div class="ui icon button" onclick="backFromSub()">
            <i class="left chevron icon"></i> Kembali
        </div>    
    </div>
<?php


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
?>
                        <div class="ui label orange" data-content="<?php echo tanggalKan($tanggal).' jam '.$jam; ?>">
                            Terakhir dibuka
                        </div>
<?php                            
        }
    }
?>                    
                </td>
                <td width="4%">
<?php
    if($i == '0'){
        if(!empty($idBLast) && $idBLast !== ''){
?>                        
                        <i class="check circle icon green"></i>
<?php
        }
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

        if($classBaca == ''){
?>
                        <i class="check circle icon green"></i>
<?php            
        }
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