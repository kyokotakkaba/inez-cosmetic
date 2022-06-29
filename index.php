<?php
    session_start();

    if(!empty($_SESSION['idPengguna'])){
        $redirect = $_SESSION['jenisPengguna'];
        header('location:'.$redirect);
        exit();
    }

    $fromHome = '';
    require_once $fromHome."conf/function.php";
    require_once $fromHome.'lib/core/head.php';
    require_once $fromHome."lib/core/snippet.php";
?>
    <style type="text/css">
        body .grid {
            height: 100%;
        }
        
        .column {
            max-width: 450px;
            margin: 0px auto;
        }
    </style>
    <div class="ui middle aligned center grid">
        <div class="column">
            <form id="frmLogin" class="ui form basic segment">
                <input type="hidden" name="view" value="1">
                <div class="ui segment loaderArea">
                    <div class="field">
                        <img src="<?php echo $icon; ?>" class="ui image small centered">
                    </div>
                    <div class="field">
                        <div class="ui input icon">
                            <input type="text" id="uname" name="uname" required="required" maxlength="32" placeholder="Username">
                            <i class="user icon"></i>
                        </div>
                    </div>
                    <div class="field">
                        <div class="ui input icon">
                            <input type="password" id="pass" name="pass" required="required" maxlength="32" placeholder="Password">
                            <i class="lock icon"></i>
                        </div>
                    </div>
                    <div class="field">
                        <button id="submitLogin" type="submit" class="ui icon button fluid" style="color: white; <?php echo $accentColor; ?>">
                            Login
                        </button>    
                    </div>
                </div>
<?php
    require_once $fromHome."lib/core/footer.php";
?>                
            </form>
        </div>
    </div>

        <script type="text/javascript">

            $('#frmLogin').submit(function(e){
                e.preventDefault();
                loadingMulai();
                $.ajax({
                    type:"post",
                    async:true,
                    url:"interface/login.php",
                    data:$('#frmLogin').serialize(),
                    success:function(data){
                        $("#feedBack").html(data);
                        loadingSelesai();
                    }
                })
            })

        </script>
    </body>
</html>
