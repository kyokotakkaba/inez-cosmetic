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

    $idData = saring($_POST['idData']);
    $main = 'Materi';

    $q = "
            SELECT
                b.nama namaB,
                b.id_kelompok,
                k.nama namaK
            FROM
                materi_kelompok_bahasan b
            LEFT JOIN
                materi_kelompok k
            ON
                b.id_kelompok = k.id

            WHERE
                b.id = '$idData'
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
    $namaBahasan = $r['namaB'];

    $idKelompok = $r['id_kelompok'];
    $namaKelompok = $r['namaK'];
        
    $sub = 'Kelola Materi';
?>



<div id="subDisplay">
    <div class="ui message">
        <div class="ui breadcrumb">
            <div class="section"><?php echo $main; ?></div>
            <i class="right angle icon divider"></i>
            <div class="active section"><?php echo $sub; ?></div>
        </div>
    </div>
    <div class="ui floating message">
      <p>Menyesuaikan materi pada kelompok <strong><?php echo $namaKelompok; ?></strong> bahasan <i><strong><?php echo $namaBahasan; ?></strong></i></p>
    </div>
    <div class="field">
        <div class="ui icon button" onclick="backToMain()">
            <i class="left chevron icon"></i> Kembali
        </div>    
    </div>

    

    <div class="ui basic vertical segment clearing" style="margin: -4px; padding: 0px 4px 0px 4px;">
        <div class="ui icon button right floated" onclick="updateRowSub()" data-content="Reload">
            <i class="redo icon"></i>
        </div>
        <div class="ui icon button green right floated" onclick="loadFormSub('isi','<?php echo $idData; ?>[pisah]0')">
            <i class="plus icon"></i> Materi
        </div>

        <div class="ui icon input">
            <input id="searchDataSub" placeholder="Cari Data.." type="text" onkeyup="cariDataSub()" />
            <i class="search icon"></i>
        </div>
    </div>
    <table class="ui striped selectable table">
        <thead>
            <tr>
                <th width="4%">No</th>
                <th>Deskripsi</th>
                <th width="30%">Opsi</th>
            </tr>
        </thead>
        <tbody id="resultDataSub">
            <!-- load data here -->
            <tr>
                <td colspan="3">
                    <i class="info circle icon"></i> <i>Load Data</i>
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
                        </select>

                        <input type="hidden" id="lastPageSub" value="0">
                        <div class="ui right floated pagination menu" id="pageNumberSub">
                            <!-- show row -->
                            <div class="active item">0</div>
                        </div>
                    </div>
                </th>
            </tr>
        </tfoot>
    </table>
</div>

<div id="subForm" style="display: none;">
    <!-- load other page here -->
</div>


<script type="text/javascript">
    $('.button').popup();
    $('.dropdown').dropdown();

    updateRowSub();
</script>