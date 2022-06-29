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
    $main = 'Survey';


    $idData = saring($_POST['idData']);

    $q = "
            SELECT 
                a.judul, 
                a.deskripsi, 
                a.responden,
                a.kode,

                k.nama jenis, 
                k.deskripsi deskJenis,

                p.nama, 
                p.deskripsi deskProduk, 
                p.gambar

            FROM 
                angket a

            LEFT JOIN
                angket_kategori k
            ON
                a.id_kategori = k.id

            LEFT JOIN
                produk p
            ON
                a.id_produk = p.id
                
            WHERE
                a.id = '$idData'

            LIMIT
                1
    ";
    $e = mysqli_query($conn, $q);
    $c = mysqli_num_rows($e);
    if($c == ''){
        echo "DATA NOT FOUND";
        exit();
    }

    $r = mysqli_fetch_assoc($e);
    $judul = $r['judul']; 
    $deskripsi = $r['deskripsi']; 
    $responden = $r['responden'];
    $kode = $r['kode'];

    $jenis = $r['jenis']; 
    $deskJenis = $r['deskJenis'];

    $nama = $r['nama']; 
    $deskProduk = $r['deskProduk']; 
    $gambarUrl = $r['gambar'];
    if($gambarUrl!==''){
        $gambarUrl = str_replace('%20', ' ', $gambarUrl);
        if(file_exists('../../'.$gambarUrl)){
            $gambar = '../'.$gambarUrl;
        }
        else{
            $gambar = '../files/photo/pictures.png';
        }    
    }
    else{
        $gambar = '../files/photo/pictures.png';
    }

    $sub = $judul;

    $subsub = 'Kelola Pertanyaan';

?>


<div id="subDisplay">
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
    <div class="ui icon button" onclick="backToMain()">
        <i class="left chevron icon"></i> Kembali
    </div>    
</div>


<table class="ui table">
    <thead>
        <tr>
           <th colspan="2">
               <img src="<?php echo $gambar; ?>" class="ui small image centered">
           </th> 
        </tr>
        <tr>
            <th colspan="2">
                Info Angket
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td width="26%">Judul</td>
            <td><?php echo $judul; ?></td>
        </tr>
        <tr>
            <td></td>
            <td><?php echo $deskripsi; ?></td>
        </tr>
        <tr>
            <td>Responden</td>
            <td><?php echo $responden; ?></td>
        </tr>
        <tr>
            <td>Jenis</td>
            <td><?php echo $jenis; ?></td>
        </tr>
        <tr>
            <td>Produk</td>
            <td><?php echo $nama; ?></td>
        </tr>
    </tbody>
</table>

    <div class="ui basic vertical segment clearing" style="margin: 0px; padding-bottom: 0px;">
        <div class="ui icon button right floated" onclick="updateRowSub()" data-content="Reload">
            <i class="redo icon"></i>
        </div>
        <div class="ui icon button green right floated" onclick="loadFormSub('data','<?php echo $idData; ?>[pisah]0')">
            <i class="plus icon"></i> Tambah
        </div>

        <a class="ui icon teal button right floated" href="preview/survey/?kode=<?php echo $kode; ?>" target="_blank">
            <i class="external link icon"></i> Preview
        </a>

        <div class="ui icon input">
            <input id="searchDataSub" placeholder="Cari Data.." type="text" onkeyup="cariDataSub()" />
            <i class="search icon"></i>
        </div>
    </div>

    <table class="ui selectable table">
        <thead>
            <tr>
                <th width="4%" rowspan="2">No</th>
                <th>Produk</th>
                <th width="25%">Opsi</th>
            </tr>
        </thead>
        <tbody id="resultDataSub">
            <!-- load data here -->
            <tr>
                <td colspan="3">
                    <i class="info circle icon"></i> <i>Load Data..</i>
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3">
                    <div class="ui vertical basic segment clearing" style="padding: 0px;">
                        <select class="ui dropdown compact" id="jumlahRowSub"  onchange="updateRowSub()">
                            <option value="15">15 Baris</option>
                            <option value="25">25 Baris</option>
                            <option value="35">35 Baris</option>
                            <option value="50">50 Baris</option>
                        </select>

                        <div class="ui right floated pagination menu" id="pageNumberSub">
                            <!-- show row -->
                            <div class="active item">0</div>
                        </div>
                    </div>
                </th>
            </tr>
        </tfoot>
    </table>

    <input type="hidden" id="lastPageSub" value="0">          
        
</div>

<div id="subForm" style="display: none;">
    <!-- load other page here -->
</div>



<script type="text/javascript">
    $('.button').popup();
    $('.dropdown').dropdown();

    updateRowSub();
</script>