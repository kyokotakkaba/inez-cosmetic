function trapBack(){
    var adaModal, formDisplay, subForm;
    adaModal = $('.modal').is(':visible');
    formDisplay = $('#formDisplay').is(':visible');
    subForm = $('#subForm').is(':visible');
    if(adaModal == false){
        if(formDisplay == true && subForm == false){
            backToMain();
        }
        else if(subForm == true){
            backFromSub();
        }
    }
}


function resizePlease(){
    setTimeout(function(){
        $(window).trigger('resize');
    }, 500);
}


function respon() {
    var panjang = $("body").width(),
        mainRow = $('#mainRow').attr('class'),
        loaderSide = $('#loaderSide').attr('class'),
        stateTrigger = $('#stateTrigger').val(),
        pushState = $('#pushState').val();

    if(panjang<481){
        if(mainRow=='two column row'){
            $('#mainRow').attr('class','row');
        }

        if(loaderSide=='twelve wide column'){
            $('#loaderSide').attr('class','column');
        }

        if(stateTrigger=='0'){
            $('#mobileMenuTrigger').transition('fade');
            $('#stateTrigger').val('1');
        }
    }
    else{
        if(mainRow=='row'){
            $('#mainRow').attr('class','two column row');
        }

        if(loaderSide=='column'){
            $('#loaderSide').attr('class','twelve wide column');
        }   

        if(stateTrigger=='1'){
            $('#mobileMenuTrigger').transition('fade');
            $('#stateTrigger').val('0');
        }
        else if(stateTrigger=='-'){
            if($('#mobileMenuTrigger').is(':visible')==true){
                $('#mobileMenuTrigger').transition('fade');
                $('#stateTrigger').val('0');
            }
        }

        if(pushState=='1'){
            triggerPushMenu();
        }
    }
}


function gantiTampil(hilang,muncul){
    $('#'+hilang).transition('slide down');
    setTimeout(function(){
        $('#'+muncul).transition('slide down');
    }, 400);
}


function loadingMulai(){
    $(".loaderArea").addClass('loading');
}


function loadingSelesai(){
    setTimeout(function(){
        $(".loaderArea").removeClass('loading');
    }, 400);
}


function reloadPage(wait){
    if(wait=="1"){
        setTimeout(function(){
            window.location.reload(true);
        }, 400);
    }
    else{
        window.location.reload(true);
    }
}





$(window).scroll(function (event) {
    var scroll = $(window).scrollTop(),
        goTopVal = $('#goTopVal').val(),
        vGoTop = $('#goTop').is(':visible');

    if(scroll >= 600){
        if(goTopVal == '0'){
            $('#goTop').transition('fade up');
            $('#goTopVal').val('1');
        }
    }
    else{
        if(goTopVal == '1'){
            $('#goTop').transition('fade down');
            $('#goTopVal').val('0');
        }
    }
});



function goTop(){
    $('html, body').animate({
        scrollTop: $("#mainLoader").offset().top - 86
    }, 1000);
}








function pilihMenu(prefix){
    var lastPrefix, pushState;
    lastPrefix = $('#lastPrefix').val();
    pushState = $('#pushState').val();
    if(prefix!==lastPrefix){
        $('.mainMenu .item').removeClass('active');
        $('.mainMenu .'+prefix).addClass('active');
        $('#lastPrefix').val(prefix);
        $('#lastId').val('-');
        $('#lastPrefixSub').val('-');
        $('#lastIdSub').val('-');
        eksekusiLoad(prefix);
    }

    if(pushState=='1'){
        setTimeout(function(){
            triggerPushMenu();
        }, 400);
    }
}


function eksekusiLoad(prefix){
    loadingMulai();
    $.ajax({
        type:"post",
        async:true,
        url:"interface/"+prefix+".php",
        data:{
            'view':'1'
        },
        success:function(data){
            $("#mainLoader").html(data);
            loadingSelesai();
        }
    })
}


function reloadFrame(){
    var prefix = $("#lastPrefix").val();
    if(prefix=='-'){
        tampilkanPesan('0','Tidak ada frame untuk di muat ulang.');
    }
    else{
        $('#lastId').val('-');
        $('#lastIdSub').val('-');
        eksekusiLoad(prefix);
    }
}










function updateRow(){
    dataList();
    showRow();
}

function cariData(){
    var number, key, pjg;
    number = event.keyCode;
    if( number == 13){
        key = $("#searchData").val();
        pjg = key.length;
        if(pjg < 3){
            tampilkanPesan('0', 'Pencarian membutuhkan minimal 3 karakter.');
        }
        else{
            $('#lastPage').val('0');
            updateRow();
        }
    }
}

function dataList(){
    loadingMulai();
    var prefix, start, limit, key;
    prefix = $('#lastPrefix').val();
    start = $('#lastPage').val();
    limit = $("#jumlahRow").val();
    key = $("#searchData").val();

    $.ajax({
        type:'post',
        url:'interface/'+prefix+'-list.php',
        async:true,
        data:{
            view:'1', 
            start: start, 
            limit: limit, 
            cari: key
        },
        success:function(data){
            $('#resultData').html(data);
            loadingSelesai();
        }
    })
}


function showRow(){
    loadingMulai();
    var prefix, limit, key;
    prefix = $('#lastPrefix').val();
    limit = $("#jumlahRow").val();
    key = $("#searchData").val();

    $.ajax({
        type:'post',
        url:'interface/'+prefix+'-list-number.php',
        async:true,
        data:{
            view:'1', 
            limit: limit, 
            cari: key
        },
        success:function(data){
            $('#pageNumber').html(data);
            loadingSelesai();
        }
    })
}


function updateList(start, id){
    $('#lastPage').val(start);
    $("#pageNumber a").removeClass("active");
    dataList();
    $("#number"+id).addClass("active");
}


function loadForm(prefix, idData){
    var lastPrefixSub = $('#lastPrefixSub').val();
    var id = $('#lastId').val();

    loadingMulai();
    if(prefix!==lastPrefixSub||idData!==id){
        $.ajax({
            type:"post",
            async:true,
            url:"interface/"+prefix+"-form.php",
            data:{
                'view':'1',
                'idData':idData
            },
            success:function(data){
                $('#lastId').val(idData);
                $('#lastIdSub').val('-');
                $('#lastPrefixSub').val(prefix);
                $("#formDisplay").html(data);
                gantiTampil('dataDisplay','formDisplay');
                loadingSelesai();
            }
        })
    }
    else{
        gantiTampil('dataDisplay','formDisplay');
        loadingSelesai();
    }
}


function backToMain(){
    gantiTampil('formDisplay','dataDisplay');
}







function updateRowSub(){
    dataListSub();
    showRowSub();
}


function cariDataSub(){
    var number, key, pjg;
    number = event.keyCode;
    if( number == 13){
        key = $("#searchDataSub").val();
        pjg = key.length;
        if(pjg < 3){
            tampilkanPesan('0', 'Pencarian membutuhkan minimal 3 karakter.');
        }
        else{
            $('#lastPageSub').val('0');
            updateRowSub();
        }
    }
}


function dataListSub(){
    loadingMulai();
    var lastId, prefix, start, limit, key;
    
    lastId = $('#lastId').val();
    prefix = $('#lastPrefixSub').val();
    start = $('#lastPageSub').val();
    limit = $("#jumlahRowSub").val();
    key = $("#searchDataSub").val();

    $.ajax({
        type:'post',
        url:'interface/'+prefix+'-list.php',
        async:true,
        data:{
            'view':'1', 
            'lastId': lastId,
            'start': start, 
            'limit': limit, 
            'cari': key
        },
        success:function(data){
            $('#resultDataSub').html(data);
            loadingSelesai();
        }
    })
}


function showRowSub(){
    loadingMulai();
    var lastId, prefix, limit, key;
    
    lastId = $('#lastId').val();
    prefix = $('#lastPrefixSub').val();
    limit = $("#jumlahRowSub").val();
    key = $("#searchDataSub").val();

    $.ajax({
        type:'post',
        url:'interface/'+prefix+'-list-number.php',
        async:true,
        data:{
            'view':'1', 
            'lastId': lastId,
            'limit': limit, 
            'cari': key
        },
        success:function(data){
            $('#pageNumberSub').html(data);
            loadingSelesai();
        }
    })
}


function updateListSub(start, id){
    $('#lastPageSub').val(start);
    $("#pageNumberSub a").removeClass("active");
    dataListSub();
    $("#numberSub"+id).addClass("active");
}


function loadFormSub(prefix, idData){
    var lastPrefix, lastId;
    lastPrefix = $('#lastPrefixSubSub').val();
    lastId = $('#lastIdSub').val();

    if(prefix!==lastPrefix || idData!==lastId){
        eksekusiLoadFormSub(prefix, idData);
    }
    else{
        gantiTampil('subDisplay','subForm');
        loadingSelesai();
    }
}

function eksekusiLoadFormSub(prefix, idData){
    loadingMulai();

    var lastPrefix = $('#lastPrefixSub').val();
    var newPrefix = lastPrefix+'-'+prefix;
    var lastId = $('#lastId').val();

    $.ajax({
        type:"post",
        async:true,
        url:"interface/"+newPrefix+"-form.php",
        data:{
            'view':'1',
            'lastId': lastId,
            'idData':idData
        },
        success:function(data){
            $('#lastPrefixSubSub').val(prefix);
            $('#lastIdSub').val(idData);
            $("#subForm").html(data);
            gantiTampil('subDisplay','subForm');
            loadingSelesai();
        }
    })
}


function reloadSub(){
    var lastPrefixSub, lastId;
    lastPrefixSub = $('#lastPrefixSub').val();
    lastId = $('#lastId').val();
    loadingMulai();
    $.ajax({
        type:"post",
        async:true,
        url:"interface/"+lastPrefixSub+"-form.php",
        data:{
            'view':'1',
            'idData':lastId
        },
        success:function(data){
            $("#formDisplay").html(data);
            $('#lastIdSub').val('-');
            $('#lastPrefixSubSub').val('-');
            loadingSelesai();
        }
    })
}


function backFromSub(){
    gantiTampil('subForm','subDisplay');
    $('#lastJenisImpor').val('-');
}








function reloadSubSub(){
    var a, b, lastPrefixSubSub, lastId, lastIdSub;
    a = $('#lastPrefixSub').val();
    b = $('#lastPrefixSubSub').val();
    lastPrefixSubSub = a+'-'+b;
    lastId = $('#lastId').val();
    lastIdSub = $('#lastIdSub').val();
    loadingMulai();
    $.ajax({
        type:"post",
        async:true,
        url:"interface/"+lastPrefixSubSub+"-form.php",
        data:{
            'view':'1',
            'lastId': lastId,
            'idData':lastIdSub
        },
        success:function(data){
            $("#subForm").html(data);
            loadingSelesai();
        }
    })
}




function kurangiJmlmNotif(){
    var jml = parseInt($('#jmlNotif').html()),
        newJml = jml - 1;

    $('.jmlNotif').html(newJml);
}








function resetForm(idObj){
    $('#'+idObj).each(function(){
        this.reset();
    });
}


function tampilkanPesan(jenis,isi){
    if(jenis=="1"){
        jenis = "success";
        ikon = 'check';
        judul = 'Perintah berhasil';
    }
    else if(jenis=="0"){
        jenis = "error";
        ikon = 'ban';
        judul = 'Perintah gagal';
    }
    else if(jenis=="2"){
        jenis = "warning";
        ikon = 'warning sign';
        judul = 'Perhatian';
    }

    $("#pesan").removeClass("success");
    $("#pesan").removeClass("error");
    $("#pesan").removeClass("waring");
    $("#pesan").removeClass("blue");
    $("#pesan").addClass(jenis);
    $("#pesanIkon").removeClass();
    $("#pesanIkon").addClass(ikon+" icon");
    $("#pesanHeader").html(judul);
    $("#pesanIsi").html(isi);
    $("#pesan").transition("drop");
    setTimeout(function(){
        $("#pesan").transition("drop");
    }, 2000);
}


function tampilkanKonfirmasi(idData, judul, isi, alamat){
    $("#konfirmasiHeader").html(judul);
    $("#konfirmasiIsi").html(isi);
    $("#konfirmasiTujuan").val(alamat);
    $("#idDataTerkait").val(idData);
    $("#konfirmasi").modal({
        //blurring:true,
        onApprove : function() {
            var url = $("#konfirmasiTujuan").val();
            var idData = $('#idDataTerkait').val();
            $.ajax({
                type:'post',
                url:url,
                async:true,
                data:{
                    'view':'1',
                    'idData':idData
                    },
                success: function(data){
                    $("#feedBack").html(data);
                }
            })
        }
    }).modal('show');
}


function reposisiData(ini, sasar, bebas, tujuan){
    loadingMulai();
    $.ajax({
        type:"post",
        async:true,
        url: tujuan,
        data:{
            'view':'1',
            'ini':ini,
            'sasar':sasar,
            'bebas':bebas
        },
        success:function(data){
            $("#feedBack").html(data);
            loadingSelesai();
        }
    })
}


function cariAjax(kategori, target, alamat){
    var tujuan = alamat+'lib/core/ajax-search.php?search={query}&category='+kategori;

    $('.ui.search').search({
        apiSettings: {
            url: tujuan
        },
        fields: {
          results : 'result',
          title   : 'title'
        },
        minCharacters : 3,
        onSelect: function(result, response){
            $("#"+target).val(result.attrib);       
        }
    });
}


function responsive_filemanager_callback(field_id){
    //console.log(field_id);
    var url=jQuery('#'+field_id).val();
    //your code
}



//prevent right click
$(this).bind("contextmenu", function(e) {
    e.preventDefault();
});