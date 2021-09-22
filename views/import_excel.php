<div class="container">

  <div class="float-right">Hi <?= $current_user->display_name ?> ! <a href="<?= URL_LOGOUT ?>">logout</a></div><br>

  <h2>Excel importer</h2>

  <p class="lead">
  	Importing Excel files into Mongo database for SFT. 
  </p>

  <p>
    Excel files should be stored on server : <?= $hostExcelFiles ?>.<br>
    Please use a <a href="https://filezilla-project.org/download.php?type=client" target="_blank">FTP client</a> to drop files. Contact <?= EMAIL_PROBLEM ?> if needed ;-)<br>
    Folder path on server : <?= PATH_INPUT_EXCEL.$database ?>/<br>
    Keep in mind that you can control the surveys inside BioCollect with the direct link right after a successful import, <strong>but you have to wait tomorrow to see the records in the different pages of BioCollect</strong>, once the next full reindexing of the database has been achieved.
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

      <p >
        The Excel files has to follow a specific template :
        <ul>
          <li><b>Line 9</b> must contain personnummer, ruttnummer, date, etc.</li>
          <li><b>Line 12</b> must contain ruttnamn, karta, etc.</li>
          <li><b>Line 21</b> must contain comments.</li>
          <li><b>Line 30</b> must be the first line with species observations.</li>
        </ul>
      </p>

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
        <input type="submit" value="Check files available" name="submit" class="btn btn-primary"/>
      </div>
    </div>
  </form>

  <?php if (isset($final_result) &&  $final_result!="") { ?>
  <p class="lead">
    <b><?= $final_result ?></b>
  </p>
  <?php } ?>

  <?php if (count($listFiles)>0) { ?>

    <div class="form-group row">
      <label class="col-sm-2 col-form-label">List of files</label>

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
              <th scope="row"><?= ($idF+1) ?></th>
              <td><?= $file["filename"] ?></td>
              <td><?= $file["internalSiteId"] ?></td>
              <td><?= $file["period"] ?></td>
              <td><?= $file["status"] ?></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>

      <?php if ($listHiddenOkFiles!="") { ?>

        <form role="form" method="post">
          <input type="hidden" value="OK" id="execFormProcessFiles" name="execFormProcessFiles"/>
          <input type="hidden" value="<?= $listHiddenOkFiles ?>" id="listHiddenOkFiles" name="listHiddenOkFiles"/>
          <input type="hidden" value="<?= $server ?>" id="serverHidden" name="serverHidden"/>

          <div class="form-group row">
            <div class="offset-sm-2 col-sm-10">
              <input type="submit" value="Process the OK Files" name="submit" class="btn btn-primary"/>
            </div>
          </div>
        </form>

      <?php }  ?>
    </div>
  <?php }  ?>

  <div class="form-group row">
    <label for="consoleArea" class="col-sm-2 col-form-label">CONSOLE</label>
  	<textarea class="form-control" rows=20 id="consoleArea"><?= ($consoleTxt!="" ? $consoleTxt : "No message.") ?>
  	</textarea>
  </div>
</div>