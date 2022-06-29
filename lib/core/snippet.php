<div id="goTop" class="ui circular icon button" onclick="goTop()">
    <i class="chevron up icon"></i>
</div>

<div class="sembunyi">
    <div class="ui form container">
        <div class="field">
            lastPrefix: <input type="text" id="lastPrefix" value="-"> <br><br>
            
            lastId: <input type="text" id="lastId" value="-"> <br>
            lastPrefixSub: <input type="text" id="lastPrefixSub" value="-"> <br><br>
            
            lastIdSub: <input type="text" id="lastIdSub" value="-"> <br>
            lastPrefixSubSub: <input type="text" id="lastPrefixSubSub" value="-"> <br><br>

            trigger: <input type="text" id="stateTrigger" value="-">  <br>
            <!-- State indicating the push menu for helping togggle auto when window resize -->
            push: <input type="text" id="pushState" value="0">  <br>

            goTopVal: <input type="text" id="goTopVal" value="0">  <br>
        </div>
    </div>
</div>


<div id="feedBack" class="sembunyi">

</div>


<div class="ui icon message" id="pesan" style="display: none;">
  <i id="pesanIkon" class="inbox icon"></i>
  <div class="content">
    <div id="pesanHeader" class="header"></div>
    <p id="pesanIsi"></p>
  </div>
</div>


<div class="ui tiny modal" id="konfirmasi">
  <i class="close icon"></i>
  <div class="header" id="konfirmasiHeader"></div>
  <div class="content">
    <div class="description">
      <p id="konfirmasiIsi"></p>
      <input type="hidden" id="konfirmasiTujuan" value="-">
      <input type="hidden" id="idDataTerkait" value="-">
    </div>
  </div>
  <div class="actions">
    <div class="ui cancel button">
      <i class="remove icon"></i>
      Batal
    </div>
    <div class="ui ok green button">
      <i class="checkmark icon"></i>
      Ya
    </div>
  </div>
</div>



<script type="text/javascript" src="<?php echo $fromHome; ?>lib/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $fromHome; ?>lib/core/snippet.js"></script>
