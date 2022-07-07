
<div class="container">
  <div class="float-right">Hi <?= $current_user->display_name ?> ! <a href="<?= URL_LOGOUT ?>">logout</a></div><br>
  
  <h2>Excel generator - custom queries</h2>

  <p class="lead">
  	Extracting data from the Mongo database for specific purposes
  </p>

  <form role="form" method="post">
  	<input type="hidden" value="OK" id="execFormExtract" name="execFormExtract"/>

    <div class="form-group row" id="div-input-object">
      <label for="queryExtract" class="col-sm-2 col-form-label">Query</label>
      <div class="col-sm-10">
        <select class="form-control" id="queryExtract" name="queryExtract" placeholder="queryExtract">
          <option value="stdRecpaComments" <?= ($queryExtract=="stdRecpaComments" ? "selected" : "") ?>>Comments recap for STD sites + Years</option>
          <option value="sftCentroidTopokartan" <?= ($queryExtract=="sftCentroidTopokartan" ? "selected" : "") ?>>Mongo version of koordinater_mittpunkt_topokartan</option>
        </select> 
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
  		<?= ($consoleTxt!="" ? $consoleTxt : "No message.") ?>
  	</textarea>
  </div>

</div>


 
