
<script>
$(document).ready(function (){
  //alert("ready");

  $("#formCleanBookings").submit(function( event ) {

    return confirm( "Are you sure that you want to clean all the bookings for the scheme "+ $('#inputProtocol option:selected').text() + " ?" );
  });

});

</script>



<div class="container">
  <div class="float-right">Hi <?= $current_user->display_name ?> ! <a href="<?= URL_LOGOUT ?>">logout</a></div><br>
  
  <h2>Clean the bookings for a SFT scheme</h2>

  <p class="lead">
  	Clean the bookings for a scheme.
  </p>

  <form role="form" method="post" id="formCleanBookings">
  	<input type="hidden" value="OK" id="execCleanBookings" name="execCleanBookings"/>

    <div class="form-group row">
      <label for="inputProtocol" class="col-sm-2 col-form-label">Protocol</label>
      <div class="col-sm-10">
        <select class="form-control" id="inputProtocol" name="inputProtocol" placeholder="protocol">
        	<option value="std" <?= ($protocol=="sft" ? "selected" : "") ?>>Standardrutterna (<?= $recapBookedSites["std"] ?> booked)</option>
        	<option value="natt" <?= ($protocol=="natt" ? "selected" : "") ?>>Nattrutterna (<?= $recapBookedSites["natt"] ?> booked)</option>
          <option value="kust" <?= ($protocol=="kust" ? "selected" : "") ?>>Kustfågelrutterna (<?= $recapBookedSites["kust"] ?> booked)</option>
          <option value="iwc" <?= ($protocol=="iwc" ? "selected" : "") ?>>Sjöfågelrutterna (<?= $recapBookedSites["iwc"] ?> booked)</option>
        </select>	
      </div>
    </div>

    <div class="form-group row">
      <div class="offset-sm-2 col-sm-10">
        <input type="submit" value="Clean" name="submit" class="btn btn-primary"/>
      </div>
    </div>
  </form>

  <?php if (isset($final_result) &&  $final_result!="") { ?>
  <p class="lead">
    <b><?= $final_result ?></b>
  </p>
  <?php } ?>


  <div class="form-group row">
    <label for="consoleArea" class="col-sm-2 col-form-label">CONSOLE</label>
  	<textarea class="form-control" rows=20 id="consoleArea"><?= ($consoleTxt!="" ? $consoleTxt : "No message.") ?>
  	</textarea>
  </div>
</div>

