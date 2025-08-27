<!-- bootstrap tables -->
<link href="https://unpkg.com/bootstrap-table@1.18.3/dist/bootstrap-table.min.css" rel="stylesheet">
<script src="https://unpkg.com/bootstrap-table@1.18.3/dist/bootstrap-table.min.js"></script>
<!-- bootstrap tables + export buttons -->
<script src="https://cdn.jsdelivr.net/npm/tableexport.jquery.plugin@1.10.21/tableExport.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tableexport.jquery.plugin@1.10.21/libs/jsPDF/jspdf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tableexport.jquery.plugin@1.10.21/libs/jsPDF-AutoTable/jspdf.plugin.autotable.js"></script>
<script src="https://unpkg.com/bootstrap-table@1.18.3/dist/extensions/export/bootstrap-table-export.min.js"></script>


<div class="container">
  <div class="float-right">Hi <?= $current_user->display_name ?> ! <a href="<?= URL_LOGOUT ?>">logout</a></div><br>
  
  <h2>Stats geographic areas per programs per years</h2>

  <p class="lead">
    Counting the amount of surveys per year for each specific program. Only based on the year of the surveyDate (so far).
  </p>

  <form role="form" method="post">
  	<input type="hidden" value="OK" id="formStatsLanPrograms" name="formStatsLanPrograms"/>

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
        </select>	
      </div>
    </div>
    <div class="form-group row" id="div-input-object">
      <label for="inputGeoObject" class="col-sm-2 col-form-label">Geographic field</label>
      <div class="col-sm-10">
        <select class="form-control" id="inputGeoObject" name="inputGeoObject" placeholder="inputGeoObject">
          <option value="lan" <?= ($inputGeoObject=="lan" ? "selected" : "") ?>>Län</option>
          <option value="lsk" <?= ($inputGeoObject=="lsk" ? "selected" : "") ?>>Landskap</option>
        </select> 
      </div>
    </div>
    <div class="form-group row" id="div-input-object">
      <label for="inputNbYears" class="col-sm-2 col-form-label">Number of years displayed</label>
      <div class="col-sm-10">
        <input class="form-control" id="inputNbYears" name="inputNbYears" placeholder="inputNbYears" value=<?= $inputNbYears ?>>
      </div>
    </div>


    <div class="form-group row">
      <div class="offset-sm-2 col-sm-10">
        <input type="submit" value="GO" name="submit" class="btn btn-primary"/>
      </div>
    </div>
  </form>

  <?php if (isset($matrixLanYear) && count($matrixLanYear)>0) { ?>

    <div class="form-group row">

      <table id="table"
        data-toggle="table"
        data-show-columns="true"
        data-search="true"
        data-show-export="true">
        <thead>
          <tr>
            <th data-sortable="true" scope="col"><?= $inputGeoObject ?></th>
            <?php  foreach ($listYear as $year) { ?>
            <th data-sortable="true" scope="col"><?= $year ?></th>
            <?php } ?>
            <th data-sortable="true" scope="col">Total</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($matrixLanYear as $areageo => $dataArea) { ?>
            <tr>
              <td><b><?= ((isset($areageo) && $areageo!="") ? $areageo : "undefined") ?></b></td>
              <?php foreach ($listYear as $year) { ?>
                <td><?= (isset($matrixLanYear[$areageo][$year]) ? $matrixLanYear[$areageo][$year] : "&nbsp;")  ?></td>
              <?php } ?>
              <td><?= $matrixLanYear[$areageo]["total"] ?></td>
            </tr>
          <?php } ?>
          <tr>
            <td><b>TOTAL</b></td>
              <?php foreach ($listYear as $year) { ?>
                <td><b><?= (isset($totalYears[$year]) ? $totalYears[$year] : "&nbsp;")  ?></b></td>
              <?php } ?>
            <td><b><?= $totalYears["total"] ?></b></td>
          </tr>
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
    <label for="consoleArea" class="col-sm-2 col-form-label">Console</label>
  	<textarea class="form-control" rows=20 id="consoleArea">
  		<?= ($consoleTxt!="" ? $consoleTxt : "No message.") ?>
  	</textarea>
  </div>

</div>


 
