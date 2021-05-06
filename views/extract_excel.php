<h2>Excel generator</h2>

<div class="row">
	Extracting data from the Mongo database for one specific scheme
</div>

<form role="form" method="post">
	<input type="hidden" value="OK" id="execFormExtract" name="execFormExtract"/>

  <div class="form-group row">
    <label for="inputProtocol" class="col-sm-2 col-form-label">Protocol</label>
    <div class="col-sm-10">
      <select class="form-control" id="inputProtocol" name="protocol" placeholder="protocol">
      	<option value="std" <?= ($protocol=="sft" ? "selected" : "") ?>>Standardrutterna</option>
      	<option value="natt" <?= ($protocol=="natt" ? "selected" : "") ?>>Nattrutterna</option>
        <option value="kust" <?= ($protocol=="kust" ? "selected" : "") ?>>Kustfågelrutterna</option>
        <option value="vinter" <?= ($protocol=="vinter" ? "selected" : "") ?>>Vinterrutterna</option>
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
		<?= ($result!="" ? $result : "No message.") ?>
	</textarea>
</div>