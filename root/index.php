<?php
    session_start();
    $appSection = 'root';
    $fromHome = '../';

    if(empty($_SESSION['idPengguna'])){
        header('location: '.$fromHome);
        exit();
    }

    $jenisPengguna = $_SESSION['jenisPengguna'];
    if($jenisPengguna !== $appSection){
        header('location: '.$fromHome.''.$jenisPengguna);
        exit();
    }


    if(empty($_SESSION['menu'])){
        $_SESSION['menu'] = 'bio';
    }

    require_once $fromHome.'lib/core/head.php';

    $q = "
            SELECT 
                COUNT(id) jmlNotif

            FROM 
                notifikasi 

            WHERE
                (
                    untuk = 'all'
                OR
                    untuk = 'root'
                OR
                    untuk = '$idPengguna'
                )
            AND
                id NOT IN 
                (
                    SELECT
                        id_notif

                    FROM
                        notifikasi_readed

                    WHERE
                        id_pengguna = '$idPengguna'
                )
    ";
    $e = mysqli_query($conn, $q);
    $r = mysqli_fetch_assoc($e);
    $jmlNotif = $r['jmlNotif'];
    
    require_once $fromHome.'lib/core/mobile-push-menu.php';
?>



    <link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css" media="screen">
    <style type="text/css">

        .table .button, .table .label {
            margin-bottom: 8px;
        }

        @media only screen and (max-width: 480px){
            #mainContainer {
                padding: 0px;
            }

            #sideMenu {
                display: none;
            }

            #loaderSide {
                display: block;
                width: 100%;
                margin: 53px 14px 0px 14px;
                padding: 0px;
            }

            #desktopMenu {
                display: none;
            }
        }

        @media only screen and (min-width: 481px){
            #mainContainer {
                top: 56px;
            }

            #footer {
                margin-top: 100px;
            }
        }

        #deskProfilePicture {
            background-color: #FFFFF7;
            background-image: url(<?php echo $profilePicture; ?>);
            background-size: cover;
            background-position: center;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            margin: 0px auto;
            border: #D4D4D4 solid 1px;
        }
    </style>
        
        
    <div id="desktopMenu" class="ui fixed inverted menu" style="<?php echo $bgHeader; ?>">
        <div class="ui container">
            <div class="item">
                <img class="logo" src="<?php echo $icon; ?>"> &nbsp; <?php echo ucfirst($jenisPengguna); ?> E-Learning Panel
            </div>
            
            <div class="right menu">
                <div class="item">
                    <img class="logo" src="<?php echo $profilePicture; ?>">
                </div>
                <div class="ui dropdown item">
                    <?php echo $namaPengguna; ?>
                    <i class="dropdown icon"></i>
                    <div class="menu">
                        <a class="item" onclick="pilihMenu('notif')">
                            <i class="bell icon"></i> Notif &nbsp; <span id="jmlNotif" class="ui label purple jmlNotif"><?php echo $jmlNotif; ?></span>
                        </a>
                        <div class="item" onclick="pilihMenu('bio')">
                            <i class="user icon"></i> Biodata
                        </div>
                        <a class="item pass" onclick="pilihMenu('pass')">
                            <i class="lock icon"></i> Password
                        </a>
                        <div class="link item" onclick="tampilkanKonfirmasi('1','Logout','Yakin ingin keluar ?','<?php echo $fromHome; ?>interface/logout.php')">
                            <i class="logout icon"></i> Logout
                        </div>
                    </div>                    
                </div>
            </div>
        </div>
    </div>

    <div id="mainContainer" class="ui basic segment container">
        <div class="ui grid">
            <div id="mainRow" class="two column row">
                <div id="menuSide" class="four wide column">
                    <div id="sideMenu" class="ui vertical fluid inverted menu mainMenu" >
                        <div class="item">
                            <div id="deskProfilePicture">
                                <!-- profile picture -->
                            </div>
                            <div class="ui horizontal divider" style="color: white;">
                                <?php echo $namaPengguna; ?>
                            </div>
                        </div>
                        <a class="item set" onclick="pilihMenu('set')">
                            Setting <i class="setting icon"></i>
                        </a>
                        <a class="item employee" onclick="pilihMenu('employee')">
                            Data BA/ BC <i class="users icon"></i>
                        </a>
                        <a class="item train" onclick="pilihMenu('train')">
                            Pelatihan <i class="address book icon"></i>
                        </a>
                        <a href="../filemanager/" class="item" target="_blank">
                            File Manager <i class="open folder icon"></i>
                        </a>
                        <a class="item kuri" onclick="pilihMenu('kuri')">
                            Materi <i class="tags icon"></i>
                        </a>
                        <a class="item bank" onclick="pilihMenu('bank')">
                            Bank Soal <i class="box icon"></i>
                        </a>
                        <a class="item test" onclick="pilihMenu('test')">
                            Ujian <i class="calendar outline alternate icon"></i>
                        </a>
                        <a class="item qa" onclick="pilihMenu('qa')">
                            Q & A <i class="comments icon"></i>
                        </a>
                        <a class="item questionnaire" onclick="pilihMenu('questionnaire')">
                            Survey <i class="chart bar icon"></i>
                        </a>
                        <a class="item report" onclick="pilihMenu('report')">
                            Laporan <i class="print icon"></i>
                        </a>
                    </div>
                </div>

                <div id="loaderSide" class="twelve wide column">
                    <div class="ui form clearing">
                        <div class="ui segment loaderArea">
                            <div id="mainLoader">
                                <!-- load all page here -->
                                <i class="orange info circle icon"></i> <i>Waiting load command. Try to reload page..</i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
        require_once $fromHome.'lib/core/snippet.php';
        require_once $fromHome.'lib/core/footer.php';
?>
        <script type="text/javascript" src="<?php echo $fromHome; ?>lib/jquery.backDetect.min.js"></script>
        <script type="text/javascript" src="<?php echo $fromHome; ?>lib/calendar.min.js"></script>
        <script type="text/javascript" src="<?php echo $fromHome; ?>lib/ckeditor/ckeditor.js"></script>
        <script type="text/javascript" src="fancybox/jquery.fancybox.pack.js"></script>
        <script type="text/javascript" src="<?php echo $fromHome; ?>lib/highchart/highcharts.js"></script>
        <script type="text/javascript" src="<?php echo $fromHome; ?>lib/highchart/modules/exporting.js"></script>
        <script type="text/javascript">

            //disable back button for redirect
            (function (global) {
                if (typeof (global) === "undefined") {
                    throw new Error("window is undefined");
                }

                var _hash = "!";
                var noBackPlease = function () {
                    global.location.href += "#";

                    global.setTimeout(function () {
                        global.location.href += "!";
                    }, 50);
                };

                global.onhashchange = function () {
                    if (global.location.hash !== _hash) {
                        global.location.hash = _hash;
                    }
                };

                global.onload = function () {
                    noBackPlease();
                    
                    document.body.onkeydown = function (e) {
                        var elm = e.target.nodeName.toLowerCase();
                        if (e.which === 8 && (elm !== 'input' && elm  !== 'textarea')) {
                            e.preventDefault();
                            trapBack();
                        }
                        e.stopPropagation();
                    };
                };
            })(window);


            respon();

            $(window).resize(
                function(){
                    respon();
                }
            );

            pilihMenu("<?php echo $_SESSION['menu']; ?>");
        </script>
    </body>
</html>