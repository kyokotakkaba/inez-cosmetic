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

    if($_SESSION['menu']!=='employee'){
        $_SESSION['menu'] = 'employee';
    }
?>
<h2 class="ui block header">
    <i class="users icon"></i>
    <div class="content">
        Data BA/ BC
        <div class="sub header">
            Data peserta e-learning
        </div>
    </div>
</h2>
<div id="dataDisplay">
    <div class="ui basic vertical segment clearing" style="margin: 0px; padding: 0px;">
        <div class="ui icon button right floated" onclick="updateRow()" data-content="Reload">
            <i class="redo icon"></i>
        </div>
        <div class="ui floating icon dropdown green button right floated">
            <span class="text"><i class="plus icon"></i> Tambah</span>
            <div class="menu">
                <div class="header">
                    <i class="tags icon"></i> Dari
                </div>
                <div class="item" onclick="loadForm('employee','0')"><i class="edit icon"></i> Manual</div>
                <div class="item" onclick="loadForm('employee-upload','0')"><i class="upload icon"></i> Unggah</div>
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
                <th width="30%">Opsi</th>
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
                            <option value="250">250 Baris</option>
                            <option value="350">350 Baris</option>
                            <option value="500">500 Baris</option>
                        </select>

                        <input type="hidden" id="lastPage" value="0">
                        <div class="ui right floated pagination menu" id="pageNumber">
                            <!-- show row -->
                            <div class="active item">0</div>
                        </div>
                    </div>
                </th>
            </tr>
        </tfoot>
    </table>
</div>
<div id="formDisplay" style="display: none;">
    <!-- load form here -->
</div>


<script type="text/javascript">
    $('.button').popup();
    $('.dropdown').dropdown();

    updateRow();
</script>