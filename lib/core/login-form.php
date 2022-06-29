<style type="text/css">
	#frmLogin {
        max-width: 340px;
        margin: -20px auto;
        padding: 0px;
    }

    #btnCloseLogin {
        margin-top: -105px;
    }
</style>

<?php
    if(empty($_SESSION['idPengguna'])){
?>
        <div id="modalLogin" class="ui basic modal">
            <div class="ui icon header">
                <i class="user circle icon"></i>
                Login
            </div>
            <div class="content">
                <form id="frmLogin" class="ui form loaderArea">
                    <div id="btnCloseLogin" class="ui icon button right floated basic red inverted circular" onclick="tutupLogIn()">
                        <i class="close icon"></i>
                    </div>
                    <div class="field">
                        <div class="ui input icon">
                            <input type="text" id="uname" name="uname" required="required" maxlength="64" placeholder="Username">
                            <i class="user icon"></i>
                        </div>
                    </div>
                    <div class="field">
                        <div class="ui input icon">
                            <input type="password" id="pass" name="pass" required="required" maxlength="64" placeholder="Password">
                            <i class="lock icon"></i>
                        </div>
                    </div>
                    <div class="field">
                        <button id="submitLogin" type="submit" class="ui icon button fluid" style="color: white; <?php echo $accentColor; ?>">
                            Masuk
                        </button>    
                    </div>
                    <div class="field">
                        <p align="center" style="font-size: 10pt; color: #60646D;">
                            Lupa Password ?
                            <a href="reset-password/">
                                Reset
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
<?php
    }
?>



<script type="text/javascript">
<?php
    if(empty($_SESSION['idPengguna'])){
?>
        function login(){
            $('#modalLogin').modal('show');
        }

        function tutupLogIn(){
            $('#modalLogin').modal('hide');
        }

        $('#frmLogin').submit(function(e){
            e.preventDefault();
            $('#submitLogin').addClass('loading');
            $.ajax({
                type:"post",
                async:true,
                url:"<?php echo $fromHome; ?>interface/login.php",
                data:$('#frmLogin').serialize(),
                success:function(data){
                    $("#feedBack").html(data);
                    setTimeout(function(){
                        $('#submitLogin').removeClass('loading');
                    }, 2000);
                }
            })
        })
<?php        
    }
?>	
</script>    