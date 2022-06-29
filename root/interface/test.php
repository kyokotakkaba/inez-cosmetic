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

    if($_SESSION['menu']!=='test'){
        $_SESSION['menu'] = 'test';
    }
?>
<h2 class="ui block header">
    <i class="calendar alternate outline icon"></i>
    <div class="content">
        Ujian
        <div class="sub header">
            <i>History</i> & Manajemen data ujian (<?php echo $idPeriode; ?>)
        </div>
    </div>
</h2>

<?php
    if($idPeriode=='0'){
?>
        <div class="ui message">
            <div class="header">
                Tidak ada periode
            </div>
            <p>Silahkkan set periode yang sedang aktif untuk tahun ini.</p>
        </div>
<?php        
        exit();
    }
?>

<div id="dataDisplay">
    <div class="ui basic vertical segment clearing" style="margin: 0px; padding: 0px 0px 10px 0px;">
        <div class="ui icon button right floated" onclick="updateRowT()" data-content="Reload">
            <i class="redo icon"></i>
        </div>
		<div class="ui floating icon dropdown green button right floated">
			<span class="text"><i class="plus icon"></i> Tambah</span>
			<div class="menu">
				<div class="header">
					<i class="tags icon"></i>
					Jenis ujian
				</div>
				<div class="item" onclick="loadForm('test-monthly','0')"><i class="edit icon"></i> Bulanan</div>
				<div class="item" onclick="loadForm('test-yearly','0')"><i class="check icon"></i> Nasional</div>
			</div>
		</div>

        <select id="jenis_ujian" class="ui compact dropdown" onchange="updateRowT()" >
<?php
	$q = "
			SELECT
				id,
				nama
			FROM
				ujian
			ORDER BY
				nama ASC
	";
	$e = mysqli_query($conn, $q);
	$c = mysqli_num_rows($e);

	if($c=='0'){
?>
			<option value="">Ujian kosong</option>
<?php		
	}
	else{
?>
			<option value="semua">Semua</option>
<?php		
		while ($r = mysqli_fetch_assoc($e)) {
			$idU = $r['id'];
			$namaU = $r['nama'];
?>
			<option value="<?php echo $idU; ?>"><?php echo $namaU; ?></option>
<?php			
		}
	}
?>        	
        </select>
    </div>
    <div class="field">
    	<div class="ui icon input">
            <input id="searchData" placeholder="Cari Data.." type="text" />
            <i class="search icon"></i>
        </div>
    </div>
    <table class="ui selectable table">
        <thead>
            <tr>
                <th rowspan="2" width="4%">No</th>
                <th rowspan="2">Deskripsi</th>
                <th colspan="2" width="8%">Peserta</th>
                <th rowspan="2" width="4%">Aktif ?</th>
                <th rowspan="2" width="26%">Opsi</th>
            </tr>
            <tr>
                <th><i class="users icon popup" data-content="Semua" ></i></th>
                <th><i class="check icon popup" data-content="Mengerjakan" ></i></th>
            </tr>
        </thead>
        <tbody id="resultData">
            <!-- load data here -->
            <tr>
                <td colspan="6">
                    <i class="info circle icon"></i> <i>Load Data..</i>
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="6">
                    <div class="ui vertical basic segment clearing" style="padding: 0px;">
                        <select class="ui dropdown compact" id="jumlahRow"  onchange="updateRowT()">
                            <option value="25">25 Baris</option>
                            <option value="35">35 Baris</option>
                            <option value="50">50 Baris</option>
                        </select>

                        <input type="hidden" id="id_periode" value="<?php echo $idPeriode; ?>">
                        <input type="hidden" id="lastPage" value="0">
                        <div class="ui right floated pagination menu" id="pageNumber">
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
<div id="formDisplay" style="display: none;">
    <!-- load form here -->
</div>


<script type="text/javascript">
    $('.button').popup();
    $('.dropdown').dropdown();

    updateRowT();

    function updateRowT(){
        dataListT();
        showRowT();
    }
    
    function dataListT(){
        loadingMulai();
        var start, limit, key, id_periode, jenis_ujian;
        start = $('#lastPage').val();
        limit = $("#jumlahRow").val();
        key = $("#searchData").val();

        id_periode = $("#id_periode").val();
        jenis_ujian = $("#jenis_ujian").val();

        $.post('interface/test-list.php',{view:'1', start: start, limit: limit, cari: key, id_periode: id_periode, jenis_ujian: jenis_ujian},
            function(result){
                $("#resultData").html(result);
                loadingSelesai();
            }
        );
    }

    function showRowT(){
        loadingMulai();
        var limit, key, id_periode, jenis_ujian;
        limit = $("#jumlahRow").val();
        key = $("#searchData").val();

        id_periode = $("#id_periode").val();
        jenis_ujian = $("#jenis_ujian").val();

        $.post('interface/test-list-number.php',{view:'1', limit: limit, cari: key, id_periode: id_periode, jenis_ujian: jenis_ujian},
            function(result){
                $("#pageNumber").html(result);
                loadingSelesai();
            }
        );
    }


    function updateListT(start, id){
        $('#lastPage').val(start);
        $("#pageNumber a").removeClass("active");
        dataListT();
        $("#number"+id).addClass("active");
    }

    
    $("#searchData").keyup(function(event){
        if(event.keyCode == 13){
            updateRowT();
            $('#lastPage').val('0');
        }
    });

</script>