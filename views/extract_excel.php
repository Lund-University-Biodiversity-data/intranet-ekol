<script>
$(document).ready(function (){
  //alert("ready");



  var objectToDisplay=$("#inputDataObject" ).val();
  $("#div-filters-"+objectToDisplay ).css("display", "");

  $("#inputProtocol" ).change(function () {

      if ($( this ).val()=="all") {

        $("#inputDataObject option[value=data]").hide();
        $("#inputDataObject option[value=sites]").hide();
        $("#inputDataObject").val("persons").change();

        $('#div-input-object').css("display", "");

      }

      else if ($( this ).val()=="") {

        $('#div-input-object').css("display", "none");

        $("#div-filters-data").css("display", "none");
        $("#div-filters-persons").css("display", "none");
        $("#div-filters-sites").css("display", "none");
      }
      else {

        $('#div-input-object').css("display", "");

        $("#inputDataObject option[value=data]").show();
        $("#inputDataObject option[value=sites]").show();
        $("#inputDataObject option[value=persons]").show();
      }

      var divId="#div-filters-" + $( this ).val();
      $(divId).css("display", "");

  });


  $("#inputDataObject" ).change(function () {

      $("#div-filters-data").css("display", "none");
      $("#div-filters-persons").css("display", "none");
      $("#div-filters-sites").css("display", "none");

      var divId="#div-filters-" + $( this ).val();
      $(divId).css("display", "");

  });



  $("#inputProtocol" ).change();

});

</script>

<div class="container">
  <div class="float-right">Hi <?= $current_user->display_name ?> ! <a href="<?= URL_LOGOUT ?>">logout</a></div><br>
  
  <h2>Excel generator</h2>

  <p class="lead">
  	Extracting data from the Mongo database for one specific scheme, or specific objects<br>
    Specific rules for the surveys years :<br>
    <ul>
      <li><b>IWC</b> : if surveyDateMonth is december, the final year will be year +1 (survey the 15 dec 2021 = year 2022)</li>
      <li><b>VinterPunktrutter</b> : </li>
      <li>1) records : if surveyDateMonth earlier than june, year = year -1 (survey the 15 feb 2021 = year 2020)</li>
      <li>2) persons : if surveyDateMonth later than june, year = year +1 (survey the 15 sep 2021 = year 2022)</li>
  </p>

  <form role="form" method="post">
  	<input type="hidden" value="OK" id="execFormExtract" name="execFormExtract"/>

    <div class="form-group row">
      <label for="inputProtocol" class="col-sm-2 col-form-label">Protocol</label>
      <div class="col-sm-10">
        <select class="form-control" id="inputProtocol" name="inputProtocol" placeholder="inputProtocol">
          <option value="" <?= ($protocol=="" ? "selected" : "") ?>>---Please choose a protocol---</option>
          <option value="kust" <?= ($protocol=="kust" ? "selected" : "") ?>>Kustfågelrutterna</option>
          <option value="natt" <?= ($protocol=="natt" ? "selected" : "") ?>>Nattrutterna</option>
          <option value="iwc" <?= ($protocol=="iwc" ? "selected" : "") ?>>Sjöfågelrutterna</option>
          <option value="sommar" <?= ($protocol=="sommar" ? "selected" : "") ?>>Sommarrutterna</option>
        	<option value="std" <?= ($protocol=="std" ? "selected" : "") ?>>Standardrutterna</option>
          <option value="vinter" <?= ($protocol=="vinter" ? "selected" : "") ?>>Vinterrutterna</option>
          <option value="all" <?= ($protocol=="all" ? "selected" : "") ?>>ALL PROTOCOLS</option>
        </select>	
      </div>
    </div>
    <div class="form-group row" id="div-input-object" style="display:none">
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
        From (included) <input type="text" maxlength=4 class="form-control" id="inputYearStart" name="inputYearStart" placeholder="YYYY" value="<?= $inputYearStart ?>">
        To (included) <input type="text" maxlength=4 class="form-control" id="inputYearEnd" name="inputYearEnd" placeholder="YYYY" value="<?= $inputYearEnd ?>">
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


 
