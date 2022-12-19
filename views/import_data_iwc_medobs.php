<div class="container">

  <div class="float-right">Hi <?= $current_user->display_name ?> ! <a href="<?= URL_LOGOUT ?>">logout</a></div><br>

  <h2>Excel importer for IWC MedObs data</h2>

  <p class="lead">
  	Importing Excel file into Mongo database for SFT-IWC (January/September), MedObs. 
  </p>

  <p>
    Please upload your file by using the form.<Br>
    <b>The excel format should follow these requirements :</b><br>
    <i>For survey data :</i><br>
    <ul>
    	<li>xlsx format</li>
    	<li>Column A : persnr (YYMMDD-X). *mandatory</li>
    	<li>Column B : internalSiteID (XXXX). *mandatory</li>
    	<li>Column C : year. *mandatory</li>
    	<li>Column D : period (Januari/September). *mandatory</li>
    	<li>Column E : metod (båt/land). *mandatory</li>
    </ul>
    <p>The data rows must start on row 2 (row 1 is for headers)</p>
  </p>


  <form role="form" method="post" enctype="multipart/form-data" >
  	<input type="hidden" value="OK" id="execFormImportDataIWCMedObs" name="execFormImportDataIWCMedObs"/>
    <div class="form-group row">
      <label for="inputFile" class="col-sm-2 col-form-label">Upload a file</label>
      <div class="col-sm-10">
        <input type="file" id="inputFile" name="inputFile">	
      </div>
    </div>

    <div class="form-group row">
      <label for="inputAddInDb" class="col-sm-2 col-form-label">Add in database</label>
      <div class="col-sm-10">
        <select class="form-control" id="inputAddInDb" name="inputAddInDb" placeholder="inputAddInDb">
          <option value="YES" <?= ($inputAddInDb=="YES" ? "selected" : "")?>>YES, add in MongoDB</option>
          <option value="NO" <?= ($inputAddInDb=="NO" ? "selected" : "")?>>NO, only check the file</option>
        </select> 
      </div>
    </div>

    <div class="form-group row">
      <div class="offset-sm-2 col-sm-10">
        <input type="submit" value="Import" name="submit" class="btn btn-primary"/>
      </div>
    </div>
  </form>

  <?php if (isset($medObsAdded) && count($medObsAdded)>0) { ?>

    <div class="form-group row">
      <label class="col-sm-2 col-form-label">List of surveys</label>

      <table class="table">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">Biocollect link</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($medObsAdded as $idF => $survey) { ?>
            <tr>
              <th scope="row"><?= ($idF+1) ?></th>
              <td><?= $survey ?></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>

    </div>
  <?php }  ?>

  <div class="form-group row">
    <label for="consoleArea" class="col-sm-2 col-form-label">CONSOLE</label>
  	<textarea class="form-control" rows=20 id="consoleArea"><?= ($consoleTxt!="" ? $consoleTxt : "No message.") ?>
  	</textarea>
  </div>
</div>