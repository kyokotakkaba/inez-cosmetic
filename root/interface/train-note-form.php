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

    $main = 'Pelatihan';

    $idData = saring($_POST['idData']);

    $pecah = explode('[pisah]', $idData);

    $id = $pecah[0];
    $tgl = $pecah[1];

    $q = "
            SELECT 
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

                a.jenis,

                t.nama tingkat,

                w.nama wilayah

            FROM 
                karyawan k

            LEFT JOIN
                akun a
            ON
                a.id_pengguna = k.id

            LEFT JOIN
                tingkat_belajar t
            ON
                t.id = k.tingkat

            LEFT JOIN
                wilayah w
            ON
                k.id_wil = w.id
            AND
                w.hapus = '0'

            WHERE
                k.id = '$id'
    ";

    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);

    if($c == '0'){
        echo "DATA NOT FOUND";
        exit();
    }

    $r = mysqli_fetch_assoc($e);

    $wilayah = $r['wilayah'];
    if($wilayah==''){
        $wilayah = '-';
    }
    $kode = $r['kode'];
    $nik = $r['nik'];
    $nama = $r['nama'];
    $jk = $r['jk'];
    if($jk==''){
        $jk = 'n';
    }
    if($jk=='l'){
        $ketJk = 'Laki-laki';
    }
    else if($jk=='p'){
        $ketJk = 'Perempuan';
    }
    else{
        $ketJk = 'Jenis kelamin belum di set';
    }
    $tmpt_lahir = $r['tmpt_lahir'];
    $tgl_lahir = $r['tgl_lahir'];
    $alamat = $r['alamat'];
    $email = $r['email'];
    $hp = $r['hp'];
    
    $foto = $r['foto'];

    if($foto==''){
        $avatar = '../files/photo/'.$jk.'.png';
    }
    else{
        $foto = str_replace('%20', ' ', $foto);
        if(file_exists('../../'.$foto)){
            $avatar = '../'.$foto;
        }
        else{
            $avatar = '../files/photo/'.$jk.'.png';
        }
    }


    $jenis = $r['jenis'];
    $tingkat = $r['tingkat'];

    $sub = 'Data Catatan';

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

    <div class="ui card">
        <div class="image">
            <img src="<?php echo $avatar ?>">
        </div>
        <div class="content">
            <a class="header"><?php echo $nama; ?></a>
            <div class="meta">
                <span class="date"><?php echo $jenis; ?></span>
            </div>
            <div class="description">
                NIK: <?php echo $nik; ?><br>
                TTL: <?php echo $tmpt_lahir.', '.tanggalKan($tgl_lahir); ?><br>
                WIl: <?php echo $wilayah; ?>
            </div>
        </div>
        <div class="extra content">
            <a>
                <i class="user icon"></i> <?php echo $tingkat; ?>
            </a>
        </div>
    </div>

    <div class="ui visible message">
      <p>Catatan pelatihan per tanggal <?php echo tanggalKan($tgl); ?></p>
    </div>


    <div class="ui basic vertical segment clearing" style="margin: 0px; padding: 0px;">
        <div class="ui button green right floated" onclick="loadFormSub('add','<?php echo $idData; ?>[pisah]0')">
            <i class="edit icon"></i> Catatan
        </div>
    </div>

    <table class="ui striped selectable green table">
        <thead>
            <tr>
                <th width="4%" rowspan="2">No</th>
                <th rowspan="2">Topik</th>
                <th colspan="2" width="8%">Nilai</th>
                <th rowspan="2" width="30%">Opsi</th>
            </tr>
            <tr>
                <th>Before</th>
                <th>After</th>
            </tr>
        </thead>
        <tbody>
<?php
    $q = "
            SELECT 
                c.id, 
                c.id_root, 
                c.nilai_before, 
                c.nilai_after,

                t.nama topik 

            FROM 
                pelatihan_catatan c

            LEFT JOIN
                pelatihan_catatan_topik t
            ON
                c.id_topik = t.id

            WHERE
                c.hapus = '0'
            AND
                c.id_karyawan = '$id'
            AND
                c.tanggal = '$tgl'

            ORDER BY
                t.nama ASC
    ";

    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);

    
    if($c=='0'){
?>
            <tr>
                <td colspan="5">
                    <i class="ui info icon teal cirle"></i> <i>Belum ada catatan.</i>
                </td>
            </tr>
<?php        
    }
    else{
        $no = 1;
        while ($r = mysqli_fetch_assoc($e)) {
            $idCat = $r['id'];
            $topik = $r['topik'];
            $id_root = $r['id_root'];
            $nilai_before = $r['nilai_before'];
            $nilai_after = $r['nilai_after'];
?>
            <tr>
                <td><?php echo $no; ?></td>
                <td>
                    <?php echo $topik; ?>
                </td>
                <td><?php echo $nilai_before; ?></td>
                <td><?php echo $nilai_after; ?></td>
                <td>
                    <div class="ui icon button" data-content="Edit" onclick="loadFormSub('add','<?php echo $idData; ?>[pisah]<?php echo $idCat; ?>')">
                        <i class="pencil alternate icon"></i>
                    </div>
                    <div class="ui icon button red" data-content="Hapus" onclick="tampilkanKonfirmasi('<?php echo $idCat; ?>','Hapus catatan','Yakin ingin menghapus data catatan pelatihan karyawan ?','interface/train-note-delete.php')">
                        <i class="trash alternate icon"></i>
                    </div>
                </td>
            </tr>
<?php     
            $no = $no+1;       
        }
    }
?>            
        </tbody>
    </table>

<?php
    if($c>0){
        $q = "
                SELECT 
                    id, 
                    id_root, 
                    rekomendasi

                FROM 
                    pelatihan_catatan_rekomendasi 

                WHERE
                    hapus = '0'
                AND
                    id_karyawan = '$id'
                AND
                    tanggal = '$tgl'

                LIMIT
                    1
        ";
        $e = mysqli_query($conn, $q);
        $c = mysqli_num_rows($e);


        if($c==1){
            $r = mysqli_fetch_assoc($e);
            $idRekom = $r['id'];
            $teks = $r['rekomendasi'];
            $rekomendasi = '<div class="field"><label>Rekomendasi</label><p>'.$teks.'</p></div>';
        }
        else{
            $idRekom = '0';
            $rekomendasi = 'Belum ada rekomendasi';
        }
?>
        <div class="ui hidden divider"></div>
        <div class="ui basic vertical segment clearing" style="margin: 0px; padding: 0px;">
            <div class="ui button teal right floated" onclick="loadFormSub('recomm-add','<?php echo $idData; ?>[pisah]<?php echo $idRekom; ?>')">
                <i class="edit icon"></i> Rekomendasi
            </div>
        </div>
        <div class="ui teal segment">
            <?php echo $rekomendasi; ?>            
        </div>
<?php
    }
?>    
</div>

<div id="subForm" style="display: none;">
    <!-- load other page here -->
</div>



<script type="text/javascript">
    $('.dropdown').dropdown();
    $('.button').popup();
</script>