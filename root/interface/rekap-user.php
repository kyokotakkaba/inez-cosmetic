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


    if($_SESSION['menu']!=='employee'){
        $_SESSION['menu'] = 'employee';
    }
    
    // new
    $qMateri="
        SELECT 
            nama     
        FROM 
            `materi_kelompok_bahasan` 
        WHERE 
            `hapus` = 0
        ORDER BY 
            `no` ASC
			";

    $eMateri = mysqli_query($conn, $qMateri);
    $cMateri = mysqli_num_rows($eMateri);

     
?>
<h2 class="ui block header">
    <i class="users icon"></i>
    <div class="content">
        Rekap Belajar User
    </div>
</h2>
<div id="dataDisplay" >
    <div class="ui basic vertical segment clearing" style="margin: 0px; padding: 0px;">
        <div class="ui icon button right floated" onclick="updateRow()" data-content="Reload">
            <i class="redo icon"></i>
        </div>
        
        
        <div class="ui icon input">
            <input id="searchData" placeholder="Cari Data.." type="text" onkeyup="cariData()" />
            <i class="search icon"></i>
        </div>
    </div>
    <div style="overflow-x:auto; max-height:100vh;">
    <table class="ui striped selectable table" >
        <thead style="position: sticky; top: 0; z-index:1;">
            <tr >
                <th width="4%">No</th>
                <th>Deskripsi</th>
                <?php 
                  if($cMateri>0){
                    while ($dMateri = mysqli_fetch_assoc($eMateri)) {
                        echo "<th >".$dMateri['nama']."</th>";
                    }
                }     
                ?>
                
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
                <th colspan="100">
                    <div class="ui vertical basic segment clearing" style="padding: 0px;">
                        <select class="ui dropdown compact" id="jumlahRow"  onchange="updateRow()">
                            <option value="10">10 Baris</option>
                            <!-- <option value="75">750 Baris</option>
                            <option value="100">100 Baris</option> -->
                        </select>

                        <input type="hidden" id="lastPage" value="0">
                        <div class="ui right floated pagination menu" id="pageNumber" style="flex-wrap:wrap;">
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
<div id="formDisplay" style="display: none;">
    <!-- load form here -->
</div>


<script type="text/javascript">
    $('.button').popup();
    $('.dropdown').dropdown();

    updateRow();
</script>