<div id="footer" class="ui basic segment center aligned">
    <h4 class="ui header">
            <?php
    $awal = $deploy_year;
    $sekarang = date('Y');
    if($sekarang>$awal){
        echo '&copy;'.$awal.' - '.$sekarang;
    }
    else{
        echo '&copy'.$sekarang;
    }

    echo ' '.$title;
?>         
    </h4>
    <div class="ui link list small" style="margin-top: -6px;">
        <a class="item" target="_blank" href="https://<?php echo $dev_web; ?>" style="font-size: 8pt;">
            Developed By <?php echo $dev; ?>
        </a>
        <a class="item" href="#" style="font-size: 8pt;">
            V. <?php echo $version; ?>
        </a>
    </div>
</div>

<script type="text/javascript" src="<?php echo $fromHome; ?>lib/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $fromHome; ?>lib/semantic-ui/semantic.min.js"></script>