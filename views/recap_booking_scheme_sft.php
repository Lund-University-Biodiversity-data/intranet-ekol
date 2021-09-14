
<!-- bootstrap tables -->
<link href="https://unpkg.com/bootstrap-table@1.18.3/dist/bootstrap-table.min.css" rel="stylesheet">
<script src="https://unpkg.com/bootstrap-table@1.18.3/dist/bootstrap-table.min.js"></script>
<!-- bootstrap tables + export buttons -->
<script src="https://cdn.jsdelivr.net/npm/tableexport.jquery.plugin@1.10.21/tableExport.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tableexport.jquery.plugin@1.10.21/libs/jsPDF/jspdf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tableexport.jquery.plugin@1.10.21/libs/jsPDF-AutoTable/jspdf.plugin.autotable.js"></script>
<script src="https://unpkg.com/bootstrap-table@1.18.3/dist/extensions/export/bootstrap-table-export.min.js"></script>

<style>
/* Tooltip container */
.tooltipCustom {
  position: relative;
  display: inline-block;
  border-bottom: 1px dotted black; /* If you want dots under the hoverable text */
}

/* Tooltip text */
.tooltipCustom .tooltiptext {
  visibility: hidden;
  width: 120px;
  background-color: black;
  color: #fff;
  text-align: center;
  padding: 5px 0;
  border-radius: 6px;
 
  /* Position the tooltip text - see examples below! */
  position: absolute;
  z-index: 1;
}

/* Show the tooltip text when you mouse over the tooltip container */
.tooltipCustom:hover .tooltiptext {
  visibility: visible;
}
</style>


<div class="container">

  <h2>Booking recap for SFT</h2>

  <p class="lead">
  	Recap page for SFT schemes.
  </p>

  <p>
    Columns obtained from Mongo Database<br>
    Excel files received checked from folder <?= $pathInputFiles ?>
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
        <input type="submit" value="Search" name="submit" class="btn btn-primary"/>
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


      <table id="table"
        data-toggle="table"
        data-show-columns="true"
        data-search="true"
        data-show-export="true">
        <thead>
          <tr>
            <th data-sortable="true" scope="col">#</th>
            <th data-field="internalSiteId" data-sortable="true" scope="col">internalSiteId</th>
            <th data-field="uttnamn" data-sortable="true" scope="col">Ruttnamn</th>
            <th data-sortable="true" scope="col">Booked?</th>
            <th data-field="lan" data-sortable="true" scope="col">Lan</th>
            <th data-sortable="true" scope="col">Reported BC</th>
            <th data-sortable="true" scope="col">Status</th>
            <th data-sortable="true" scope="col">Reported Excel</th>
            <th data-sortable="true" scope="col">Booking comment</th>
            <th data-sortable="true" scope="col">Name</th>
            <th data-sortable="true" scope="col">Email</th>
          </tr>
        </thead>
        <tbody>
          <?php $idS=0; foreach ($arrRecap as $siteId => $dataSite) { $idS++;?>
            <tr>
              <th scope="row"><?= $idS ?></th>
              <td><a href="<?= $dataSite["urlBioCollect"] ?>" target="_blank"><?= $siteId ?></a></td>
              <td><?= $dataSite["commonName"] ?></td>
              <td><?= $dataSite["booked"] ?></td>
              <td><?= $dataSite["county"] ?></td>
              <td><?= $dataSite["lastYearSurveyed"] ?></td>
              <td><?= $dataSite["lastYearSurveyedStatus"] ?></td>
              <td><div class="tooltipCustom"><?= $dataSite["excelReceivedYear"] ?>
                <span class="tooltiptext"><?= $dataSite["excelReceived"] ?></span>
              </div></td>
              <td><?= $dataSite["bookingComment"] ?></td>
              <td><?= ($dataSite["booked"]=="yes" ? $arrPersonsDetails[$dataSite["bookedBy"]]["name"] : "") ?></td>
              <td><?= ($dataSite["booked"]=="yes" ? $arrPersonsDetails[$dataSite["bookedBy"]]["email"] : "") ?></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>

      <script>
        $(function() {
          $('#table').bootstrapTable()
        })
      </script>


    </div>
  <?php }  ?>

  <div class="form-group row">
    <label for="consoleArea" class="col-sm-2 col-form-label">CONSOLE</label>
  	<textarea class="form-control" rows=20 id="consoleArea"><?= ($consoleTxt!="" ? $consoleTxt : "No message.") ?>
  	</textarea>
  </div>
</div>

