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
  
  <h2>Stats programs/users per years and per origin</h2>

  <p class="lead">
    Counting the amount of validated surveys per year for each origin (BioCollect, Excel, etc). The year is the year of the survey, without corection, even for vinterrutterna. Last column is the number of distinct users reporting this year.
  </p>


  <form role="form" method="post">
    <input type="hidden" value="OK" id="formStatsProgramOrigin" name="formStatsProgramOrigin"/>

    <div class="form-group row" id="div-input-object">
      <label for="inputYearStart" class="col-sm-2 col-form-label">Starting year</label>
      <div class="col-sm-10">
        <input class="form-control" id="inputYearStart" name="inputYearStart" placeholder="inputYearStart" value=<?= $inputYearStart ?>>
      </div>
    </div>


    <div class="form-group row">
      <div class="offset-sm-2 col-sm-10">
        <input type="submit" value="GO" name="submit" class="btn btn-primary"/>
      </div>
    </div>
  </form>
  <?php if (isset($matrixYearOrigin) && count($matrixYearOrigin)>0) { ?>

    <div class="form-group row">

      <table id="table"
        data-toggle="table"
        data-show-columns="true"
        data-search="true"
        data-show-export="true">
        <thead>
          <tr>
            <th data-sortable="true" scope="col">Years</th>
            <?php  foreach ($listOrigin as $origin) { ?>
            <th data-sortable="true" scope="col"><?= $origin ?></th>
            <?php } ?>
            <th data-sortable="true" scope="col">Total</th>
            <th data-sortable="true" scope="col">%age BC</th>
            <th data-sortable="true" scope="col">Nb BC users</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($matrixYearOrigin as $year => $dataYear) { ?>
            <tr>
              <td><b><?= ((isset($year) && $year!="") ? $year : "undefined") ?></b></td>
              <?php foreach ($listOrigin as $origin) { ?>
                <td><?= (isset($matrixYearOrigin[$year][$origin]) ? $matrixYearOrigin[$year][$origin] : "&nbsp;")  ?></td>
              <?php } ?>
              <td><?= $matrixYearOrigin[$year]["total"] ?></td>
              <td><i><?= number_format(100*$matrixYearOrigin[$year]["BioCollect"]/$matrixYearOrigin[$year]["total"], 2) ?></i></td>
              <td><?= (isset($matrixYearUser[$year]) ? $matrixYearUser[$year] : "" ) ?></td>
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
    <label for="consoleArea" class="col-sm-2 col-form-label">Console</label>
  	<textarea class="form-control" rows=20 id="consoleArea">
  		<?= ($consoleTxt!="" ? $consoleTxt : "No message.") ?>
  	</textarea>
  </div>

</div>


 
