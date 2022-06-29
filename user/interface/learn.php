<?php
    session_start();
    $appSection = 'user';

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

    if($_SESSION['menu']!=='learn'){
        $_SESSION['menu'] = 'learn';
    }

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
?>
<h3 class="ui block header">
    <i class="tags icon"></i>
    <div class="content">
        Belajar
        <div class="sub header">
            Materi untuk : <strong><i><?php echo $namatingkat; ?></i></strong>
        </div>
    </div>
</h3>
<div id="dataDisplay">
    <input type="hidden" id="lastBahasan" value="0">
    <input type="hidden" id="lastMateri" value="0">
    <table class="ui very basic inline table unstackable">
        <tbody id="loaderBahasan">
            <tr>
                <td>
                    <!-- load data here -->
                    <i class="info circle icon"></i> <i>Load Data..</i>
                </td>
            </tr>
        </tbody>
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

    populateBahasan();

    function populateBahasan(){
        loadingMulai();
        $.ajax({
            type:"post",
            async:true,
            url:"interface/learn-bahasan.php",
            data:{
                'view':'1'
            },
            success:function(data){
                $("#loaderBahasan").html(data);
                loadingSelesai();
            }
        })
    }

    function loadMateri(idData, prefix){
        loadingMulai();
        if(prefix=='prev'){
            eksekusiLoadMateri(idData);
        }
        else if(prefix=='next'){
            var jawab = $('#jawab').val(),
                benar = $('#benar').val();

            if(jawab==''){
                tampilkanPesan('0','Silahkan jawab kuis terlebih dahulu.');
                loadingSelesai();
            }
            else if(benar=='0'){
                tampilkanPesan('0','Jawab kuis dengan benar.');
                loadingSelesai();
            }
            else{
                eksekusiLoadMateri(idData);
            }
        }
    }


    function eksekusiLoadMateri(idData){
        $.ajax({
            type: 'post',
            async: true,
            url: 'interface/learn-bahasan-materi-form.php',
            data:{
                'view': '1',
                'idData': idData
            },
            success:function(data){
                $("#subForm").html(data);
                $('#lastIdSub').val(idData);
                loadingSelesai();
            }
        })
    }






    function jawabKuis(idData){
        var idMateriKuis, idKuis, jenis, idJawab, jawab, benar, teks;

        jenis = $('#jenis').val();
        if(jenis=='essay'){
            teks = 'isi';
        }
        else if(jenis=='mchoice'){
            teks = 'pilih';
            $('#jawab').val(idData);
        }

        idMateriKuis = $('#idMateriKuis').val();
        idKuis = $('#idKuis').val();
        idJawab = $('#idJawab').val();
        jawab = $('#jawab').val();
        benar = $('#benar').val();

        loadingMulai();

        if(jawab==''){
            tampilkanPesan('0','Silahkan '+teks+' jawaban anda.');
            loadingSelesai();
        }
        else if(benar=='1'){
            tampilkanPesan('0','Kuis sudah terjawab.');
            loadingSelesai();
        }
        else if(benar=='0'){
            if(jenis=='mchoice'){
                $('.btnOpsi').removeClass('orange');
                $('.btnOpsi').addClass('loading');
            }

            $.ajax({
                type:"post",
                async:true,
                url:"interface/learn-form-answer-quiz.php",
                data:{
                    'view':'1',
                    'idMateriKuis': idMateriKuis,
                    'idKuis': idKuis,
                    'idJawab': idJawab,
                    'jawab': jawab
                },
                success:function(data){
                    $("#feedBack").html(data);
                    loadingSelesai();
                }
            })
        }
    }




    function responJawabKuis(idKuis, balikIdJawab, jawab, benar) {
        var idKuisNow = $('#idKuis').val();
        var jenis = $('#jenis').val();

        if(idKuisNow==idKuis){
            $('#idJawab').val(balikIdJawab);
            $('#benar').val(benar);
            if(benar=='1'){
                if(jenis=='essay'){
                    $('#btnJawabKuis').addClass('disabled');
                }
                else if(jenis=='mchoice'){
                    $('#btn'+jawab).addClass('green');
                    $('.btnOpsi').addClass('disabled');
                    setTimeout(function(){
                        $('.btnOpsi').removeClass('loading');
                    }, 400);
                }
                
            }
            else{
                if(jenis=='mchoice'){
                    $('#btn'+jawab).addClass('orange');
                    setTimeout(function(){
                        $('.btnOpsi').removeClass('loading');
                    }, 400);
                }
            }
        }
    }

</script>