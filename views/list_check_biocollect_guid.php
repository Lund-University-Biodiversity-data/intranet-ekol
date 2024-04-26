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

  <h2>Lists comparator</h2>

  <p class="lead">
  	Getting the list <?= $listIdToCheck ?>  data from <a href="https://lists.biodiversitydata.se" target="_blank">lists.biodiversitydata.se</a> and checking in BioCollect the different guids linked to the species.
  </p>
  <form role="form" method="post">
  	<input type="hidden" value="OK" id="form" name="formCompareLists"/>

    <!--
    <div class="form-group row">
      <label for="inputServer" class="col-sm-2 col-form-label">Available lists</label>
      <div class="col-sm-10">
        <select class="form-control" name="listIdToCheck" placeholder="listIdToCheck" size=<?= count($listAvailable); ?>>
          <?php foreach ($listAvailable as $listId => $listName) { 
            echo '<option value="'.$listId.'">'.$listId." - ".$listName.'</option>';
          } ?>
        </select> 
      </div>
    </div>
    -->

    <div class="form-group row">
      <div class="offset-sm-2 col-sm-10">
        <input type="submit" value="Compare" name="submit" class="btn btn-primary"/>
      </div>
    </div>
  </form>

  <?php if (isset($final_result) &&  $final_result!="") { ?>
  <p class="lead">
    <b><?= $final_result ?></b>
  </p>
  <?php } ?>

  <?php if (count($tabRecap)>0) { ?>

    <div class="form-group row">
      
      <table id="table"
        data-toggle="table"
        data-show-columns="true"
        data-search="true"
        data-show-export="true">
        <thead>
          <tr>
            <th>guid in lists</th>
            <th>swedishName</th>
            <th>art</th>
            <th>scientificName</th>
            <th>nb Elts</th>
            <th>guid dupl.</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($tabRecap as $guid => $rowContent) { ?>
            <?php foreach ($rowContent["items"] as $swedishName => $rowItem) { ?>
              <tr>
                <td scope="row"><?= $guid ?></td>
                <td scope="row"><?= $swedishName ?></td>
                <td scope="row"><?= $rowItem["art"] ?></td>
                <td scope="row"><?= $rowItem["scientificName"] ?></td>
                <td scope="row"><?= $rowItem["nbItems"] ?></td>
                <td scope="row"><?= $rowContent["duplicate"] ?></td>
              </tr>
            <?php } ?>
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