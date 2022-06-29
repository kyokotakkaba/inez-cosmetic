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

    if($_SESSION['menu']!=='bank'){
        $_SESSION['menu'] = 'bank';
    }
?>
<h2 class="ui block header">
    <i class="box icon"></i>
    <div class="content">
        Bank Soal
        <div class="sub header">
            Manajemen pertanyaan untuk ujian
        </div>
    </div>
</h2>
<div id="dataDisplay">
    <div class="ui basic vertical segment clearing" style="margin: -16px 0px 16px 0px; padding-bottom: 0px;">
        <div class="ui icon button right floated" onclick="updateRow()" data-content="Reload">
            <i class="redo icon"></i>
        </div>
        <div class="ui floating icon dropdown green button right floated">
            <span class="text"><i class="plus icon"></i> Tambah</span>
            <div class="menu">
                <div class="header">
                    <i class="tags icon"></i> Dari
                </div>
                <div class="item" onclick="loadForm('bank','0')"><i class="edit icon"></i> Manual</div>
                <div class="item" onclick="loadForm('bank-upload','0')" ><i class="upload icon"></i> Unggah</div>
            </div>
        </div>

        <div class="ui icon input">
            <input id="searchData" placeholder="Cari Data.." type="text" onkeyup="cariData()" />
            <i class="search icon"></i>
        </div>
    </div>
    <table class="ui striped selectable table">
        <thead>
            <tr>
                <th width="4%">No</th>
                <th>Deskripsi</th>
                <th width="25%">Opsi</th>
            </tr>
        </thead>
        <tbody id="resultData">
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
                        <select class="ui dropdown compact" id="jumlahRow"  onchange="updateRow()">
                            <option value="75">75 Baris</option>
                            <option value="100">100 Baris</option>
                            <option value="150">150 Baris</option>
                        </select>

                        <div class="ui right floated pagination menu" id="pageNumber">
                            <!-- show row -->
                            <div class="active item">
                                0
                            </div>
                        </div>

                        <input type="hidden" id="lastPage" value="0">
                    </div>
                </th>
            </tr>
        </tfoot>
    </table>
</div>
<div id="formDisplay" style="display: none;">
    <!-- load form here -->
</div>

<div id="subDisplay" style="display: none;">
    <!-- load form here -->
</div>



<script type="text/javascript">
    $('.button').popup();
    $('.dropdown').dropdown();

    updateRow();
</script>