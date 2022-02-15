<div class="container">

  <div class="float-right">Hi <?= $current_user->display_name ?> ! <a href="<?= URL_LOGOUT ?>">logout</a></div><br>

  <h2>Site creator for Punktrutter</h2>

  <p class="lead">
  	Creating sites in Ecodata/BioCollect for Punktrutterna
  </p>

  <form role="form" method="post">
  	<input type="hidden" value="OK" id="formPunktSite" name="formPunktSite"/>

    <div class="form-group row">
      <label for="inputKartaTx" class="col-sm-2 col-form-label">Karta TX</label>
      <div class="col-sm-10">
        <select class="form-control" id="inputKartaTx" name="inputKartaTx" placeholder="kartaTx">
          <option value="" <?= ($kartaTx=="" ? "selected" : "")?>>Please select one KartaTX</option>
          <?php
          foreach ($arrKartaTx as $kTx) {
            echo '<option value="'.$kTx.'" '.($kartaTx==$kTx ? "selected" : "").'>'.$kTx.'</option>';
          }
          ?>
        </select>	
      </div>
    </div>

    <div class="form-group row">
      <label for="inputLan" class="col-sm-2 col-form-label">Län</label>
      <div class="col-sm-10">
        <select class="form-control" id="inputLan" name="inputLan" placeholder="lan">
          <option value="" <?= ($lan=="" ? "selected" : "")?>>Please select one län</option>
          <?php
          foreach ($arrLan as $la) {
            echo '<option value="'.$la.'" '.($lan==$la ? "selected" : "").'>'.$la.'</option>';
          }
          ?>
        </select> 
      </div>
    </div>

    <div class="form-group row">
      <label for="inputInternalSiteId" class="col-sm-2 col-form-label">InternalSiteId</label>
      <div class="col-sm-10">
        <input type="text" maxlength=20 class="form-control" id="inputInternalSiteId" name="inputInternalSiteId" placeholder="YYMMDD-X-0Z" value="<?= $internalSiteId ?>">
      </div>
    </div>

    <div class="form-group row">
      <label for="inputSiteName" class="col-sm-2 col-form-label">Popular name</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" id="inputSiteName" name="inputSiteName" placeholder="popular name WITHOUT internalId" value="<?= $siteName ?>">
      </div>
    </div>

    <div class="form-group row">
      <label for="inputServer" class="col-sm-2 col-form-label">MongoDb Server</label>
      <div class="col-sm-10">
        <select class="form-control" id="inputServer" name="inputServer" placeholder="server">
          <?php if (isset($mongoConnection["TEST"])) { ?>
          <option value="TEST" <?= ($server=="TEST" ? "selected" : "") ?>>TEST - local</option>
          <?php } ?>
          <option value="DEV" <?= ($server=="DEV" ? "selected" : "") ?>>DEV - canmove-dev</option>
          <option value="PROD" <?= ($server=="PROD" ? "selected" : "") ?>>PROD - ecodata.biodivesitydata.se [89.45.234.73]</option>
        </select> 
      </div>
    </div>


    <div class="form-group row">
      <div class="offset-sm-2 col-sm-10">
        <input type="submit" value="Create in MongoDB" name="submit" class="btn btn-primary"/>
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