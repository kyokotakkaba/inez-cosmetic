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
    $sub = 'Data Produk';

    $idData = saring($_POST['idData']);

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
    <div class="ui basic vertical segment clearing" style="margin: 0px; padding-bottom: 0px;">
        <div class="ui icon button right floated" onclick="updateRowSub()" data-content="Reload">
            <i class="redo icon"></i>
        </div>
        <div class="ui icon button green right floated" onclick="loadFormSub('isi','0')">
            <i class="plus icon"></i> Tambah
        </div>

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
                        </select>

                        <input type="hidden" id="lastPageSub" value="0">
                        <div class="ui right floated pagination menu" id="pageNumberSub">
                            <!-- show row -->
                            <div class="active item">
                                0
                            </div>
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