<div class="container">

  <h2>Excel importer</h2>

  <p class="lead">
  	Importing Excel file into Mongo database through JSON files
  </p>

  <form role="form" method="post">
  	<input type="hidden" value="OK" id="execFormListFiles" name="execFormListFiles"/>

    <div class="form-group row">
      <label for="inputProtocol" class="col-sm-2 col-form-label">Protocol</label>
      <div class="col-sm-10">
        <select class="form-control" id="inputProtocol" name="protocol" placeholder="protocol">
        	<option value="std" <?= ($protocol=="sft" ? "selected" : "") ?>>Standardrutterna</option>
        	<option value="natt" <?= ($protocol=="natt" ? "selected" : "") ?>>Nattrutterna</option>
          <option value="kust" <?= ($protocol=="kust" ? "selected" : "") ?>>Kustfågelrutterna</option>
          <option value="vinter" <?= ($protocol=="vinter" ? "selected" : "") ?>>Vinterrutterna</option>
          <option value="sommar" <?= ($protocol=="sommar" ? "selected" : "") ?>>Sommarrutterna</option>
        </select>	
      </div>
    </div>
    <div class="form-group row">
      <div class="offset-sm-2 col-sm-10">
        <input type="submit" value="Check files available" name="submit" class="btn btn-primary"/>
      </div>
    </div>
  </form>

  <div class="form-group row">
  	<label class="col-sm-2 col-form-label">List of files</label>
  	<?php if (count($listFiles)>0) { ?>
      <table class="table">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">Filename</th>
            <th scope="col">Internal Site Id</th>
            <th scope="col">Period</th>
            <th scope="col">STATUS</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($listFiles as $idF => $file) { ?>
            <tr>
              <th scope="row"><?= $idF ?></th>
              <td><?= $file["filename"] ?></td>
              <td><?= $file["internalSiteId"] ?></td>
              <td><?= $file["period"] ?></td>
              <td><?= $file["status"] ?></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>

      <?php if (count($listHiddenOkFiles)>0) { ?>

        <form role="form" method="post">
          <input type="hidden" value="OK" id="execFormProcessFiles" name="execFormProcessFiles"/>
          <input type="hidden" value="<?= $listHiddenOkFiles ?>" id="listHiddenOkFiles" name="listHiddenOkFiles"/>

          <div class="form-group row">
            <label for="inputServer" class="col-sm-2 col-form-label">Server</label>
            <div class="col-sm-10">
              <select class="form-control" id="inputServer" name="server" placeholder="server">
                <option value="DEV" <?= ($server=="DEV" ? "selected" : "") ?>>DEV - canmove-dev</option>
                <option value="PROD" <?= ($server=="PROD" ? "selected" : "") ?>>PROD - ecodata.biodivesitydata.se</option>
              </select> 
            </div>
          </div>
          <div class="form-group row">
            <div class="offset-sm-2 col-sm-10">
              <input type="submit" value="Process the OK Files" name="submit" class="btn btn-primary"/>
            </div>
          </div>
        </form>

      <?php }  ?>
    <?php }  ?>
  </div>
  <div class="form-group row">
    <label for="consoleArea" class="col-sm-2 col-form-label">Console</label>
  	<textarea class="form-control" rows=20 id="consoleArea"><?= ($consoleTxt!="" ? $consoleTxt : "No message.") ?>
  	</textarea>
  </div>
</div>