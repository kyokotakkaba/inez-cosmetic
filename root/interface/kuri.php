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

    if($_SESSION['menu']!=='kuri'){
        $_SESSION['menu'] = 'kuri';
    }
?>
<h2 class="ui block header">
    <i class="tags icon"></i>
    <div class="content">
        Materi
        <div class="sub header">
            Sesuaikan materi belajar
        </div>
    </div>
</h2>
<div id="dataDisplay">
    <div class="ui styled fluid accordion">
        <div class="title">
            <i class="dropdown icon"></i> Kelompok materi 
        </div>
        <div class="content">
            <div id="kelompokPlace">
                <!-- load kelompok materi di sini -->
                <i class="info circle icon"></i> <i>Load Data..</i>
            </div>
        </div>



        <div class="title active">
            <i class="dropdown icon"></i> Bahasan - Materi
        </div>
        <div class="content active">
            <div class="ui basic vertical segment clearing" style="margin: 0px; padding-bottom: 0px;">
                <div class="ui icon button right floated" onclick="updateRow()" data-content="Reload">
                    <i class="redo icon"></i>
                </div>
                <div class="ui icon button green right floated" onclick="loadForm('kuri-bahasan','0')">
                    <i class="plus icon"></i> Bahasan
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
                        <th width="50%">Opsi</th>
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
                                    <option value="15">15 Baris</option>
                                    <option value="25">25 Baris</option>
                                    <option value="35">35 Baris</option>
                                    <option value="50">50 Baris</option>
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
    </div> 
</div>
<div id="formDisplay" style="display: none;">
    <!-- load form here -->
</div>


<script type="text/javascript">
    $('.accordion').accordion();
    $('.button').popup();
    $('.dropdown').dropdown();

    loadKelompok();

    function loadKelompok(){
        loadingMulai();
        $.ajax({
            type:"post",
            async:true,
            url:"interface/kuri-kelompok.php",
            data:{
                'view':'1'
            },
            success:function(data){
                $("#kelompokPlace").html(data);
                loadingSelesai();
            }
        })
    }





    updateRow();
</script>