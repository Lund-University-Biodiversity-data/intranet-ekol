<script>
$(document).ready(function (){
  //alert("ready");

  var objectToDisplay=$("#inputDataObject" ).val();
  $("#div-filters-"+objectToDisplay ).css("display", "");
  

  $("#inputDataObject" ).change(function () {

      $("#div-filters-data").css("display", "none");
      $("#div-filters-persons").css("display", "none");
      $("#div-filters-sites").css("display", "none");

      var divId="#div-filters-" + $( this ).val();
      $(divId).css("display", "");

  });
});

</script>

<div class="container">
  <div class="float-right">Hi <?= $current_user->display_name ?> ! <a href="<?= URL_LOGOUT ?>">logout</a></div><br>
  
  <h2>Excel generator</h2>

  <p class="lead">
  	Extracting data from the Mongo database for one specific scheme, or specific objects
  </p>

  <form role="form" method="post">
  	<input type="hidden" value="OK" id="execFormExtract" name="execFormExtract"/>

    <div class="form-group row">
      <label for="inputProtocol" class="col-sm-2 col-form-label">Protocol</label>
      <div class="col-sm-10">
        <select class="form-control" id="inputProtocol" name="inputProtocol" placeholder="inputProtocol">
          <option value="kust" <?= ($protocol=="kust" ? "selected" : "") ?>>Kustfågelrutterna</option>
          <option value="natt" <?= ($protocol=="natt" ? "selected" : "") ?>>Nattrutterna</option>
          <option value="iwc" <?= ($protocol=="iwc" ? "selected" : "") ?>>Sjöfågelrutterna</option>
          <option value="sommar" <?= ($protocol=="sommar" ? "selected" : "") ?>>Sommarrutterna</option>
        	<option value="std" <?= ($protocol=="std" ? "selected" : "") ?>>Standardrutterna</option>
          <option value="vinter" <?= ($protocol=="vinter" ? "selected" : "") ?>>Vinterrutterna</option>
        </select>	
      </div>
    </div>
    <div class="form-group row">
      <label for="inputDataObject" class="col-sm-2 col-form-label">Protocol</label>
      <div class="col-sm-10">
        <select class="form-control" id="inputDataObject" name="inputDataObject" placeholder="inputDataObject">
          <option value="data" <?= ($inputDataObject=="data" ? "selected" : "") ?>>Records (approved + under review)</option>
          <option value="persons" <?= ($inputDataObject=="persons" ? "selected" : "") ?>>Persons</option>
          <option value="sites" <?= ($inputDataObject=="sites" ? "selected" : "") ?>>Sites</option>
        </select> 
      </div>
    </div>

    <div class="form-group row" id="div-filters-persons" style="display:none">
      <label class="col-sm-2 col-form-label">Years</label>
      <div class="col-sm-10">
        From <input type="text" maxlength=4 class="form-control" id="inputYearStart" name="inputYearStart" placeholder="YYYY" value="<?= $inputYearStart ?>">
        To <input type="text" maxlength=4 class="form-control" id="inputYearEnd" name="inputYearEnd" placeholder="YYYY" value="<?= $inputYearEnd ?>">
      </div>

    </div>


    <div class="form-group row">
      <div class="offset-sm-2 col-sm-10">
        <input type="submit" value="GO" name="submit" class="btn btn-primary"/>
      </div>
    </div>
  </form>

  <div class="form-group row">
  	<label class="col-sm-2 col-form-label">Result</label>
  	<?= $file_download ?>
  </div>
  <div class="form-group row">
    <label for="consoleArea" class="col-sm-2 col-form-label">Console</label>
  	<textarea class="form-control" rows=20 id="consoleArea">
  		<?= ($result!="" ? $result : "No message.") ?>
  	</textarea>
  </div>

</div>


 
