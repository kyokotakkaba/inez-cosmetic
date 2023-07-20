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

    $idPengguna = $_SESSION['idPengguna'];

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
                k.id = '$idPengguna'

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
                    kbl.id_karyawan = '$idPengguna'
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
            l.id_karyawan = '$idPengguna'
        AND
            l.last = '1'

        WHERE
            b.hapus = '0'

        ORDER BY
            b.no ASC
    ";

    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);

    if($c>0){
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
                        
<?php
    if($i == '0'){
        if($last=='1'){
            $idLast = $idBLast;
            $noLast = $i;
?>
                        
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
                        <div id="btn<?php echo $no; ?>" class="ui right floated inverted button" onclick="loadForm('learn-bahasan','<?php echo $idData; ?>')" style="<?php echo $accentColor; ?>">
                            Buka
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
                        <div id="btn<?php echo $no; ?>" class="ui right floated inverted button <?php echo $classBaca; ?>" onclick="loadForm('learn-bahasan','<?php echo $idData; ?>')" style="<?php echo $accentColor; ?>" >
                            Buka
                        </div>
<?php        
    }
?>                        
                    </td>
                </tr>
<?php                    
            }
        }
    }
    else{
        $teksKosong = 'Belum ada data.';
?>
        <tr>
            <td>
                <p>
                    <i class="info circle icon teal"></i> <i> <?php echo $teksKosong; ?> </i>
                </p>
            </td>
        </tr>
<?php       
    }
?>