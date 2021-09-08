<div class="container">

  <h2>Booking recap for SFT</h2>

  <p class="lead">
  	Recap page for SFT schemes.
  </p>

  <p>
    Columns obtained from Mongo Database<br>
    Excel files received checked from folder ""
  </p>


  <form role="form" method="post">
  	<input type="hidden" value="OK" id="execFormRecapBookingScheme" name="execFormRecapBookingScheme"/>

    <div class="form-group row">
      <label for="inputProtocol" class="col-sm-2 col-form-label">Protocol</label>
      <div class="col-sm-10">
        <select class="form-control" id="inputProtocol" name="protocol" placeholder="protocol">
        	<option value="std" <?= ($protocol=="sft" ? "selected" : "") ?>>Standardrutterna</option>
        	<option value="natt" <?= ($protocol=="natt" ? "selected" : "") ?>>Nattrutterna</option>
          <option value="kust" <?= ($protocol=="kust" ? "selected" : "") ?>>Kustfågelrutterna</option>
        </select>	
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
        <input type="submit" value="Gimme the recap dude" name="submit" class="btn btn-primary"/>
      </div>
    </div>
  </form>

  <?php if (isset($final_result) &&  $final_result!="") { ?>
  <p class="lead">
    <b><?= $final_result ?></b>
  </p>
  <?php } ?>

  <?php if (count($arrRecap)>0) { ?>

    <div class="form-group row">
      <label class="col-sm-2 col-form-label">Recap</label>

      <table class="table">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">internalSiteId</th>
            <th scope="col">Site name</th>
            <th scope="col">Last year surveyed</th>
            <th scope="col">Excel file</th>
            <th scope="col">Booked?</th>
            <th scope="col">Booking comment</th>
            <th scope="col">Name</th>
            <th scope="col">Email</th>
          </tr>
        </thead>
        <tbody>
          <?php $idS=1; foreach ($arrRecap as $siteId => $dataSite) { ?>
            <tr>
              <th scope="row"><?= ($idS++) ?></th>
              <td><a href="<?= $dataSite["urlBioCollect"] ?>" target="_blank"><?= $siteId ?></a></td>
              <td><?= $dataSite["locationName"] ?></td>
              <td><?= $dataSite["lastYearSurveyed"] ?></td>
              <td></td>
              <td><?= $dataSite["booked"] ?></td>
              <td><?= $dataSite["bookingComment"] ?></td>
              <td><?= ($dataSite["booked"]=="yes" ? $arrPersonsDetails[$dataSite["bookedBy"]]["name"] : "") ?></td>
              <td><?= ($dataSite["booked"]=="yes" ? $arrPersonsDetails[$dataSite["bookedBy"]]["email"] : "") ?></td>
              <td></td>
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