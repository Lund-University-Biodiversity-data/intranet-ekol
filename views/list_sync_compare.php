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
  	Getting lists data from <a href="https://lists.biodiversitydata.se" target="_blank">lists.biodiversitydata.se</a> and compare with what exists right now in the STF database, table eurolist.
  </p>
  <p>The recap array displays both the field from Eurolist, and then from the list</p>
  <form role="form" method="post">
  	<input type="hidden" value="OK" id="form" name="formCompareLists"/>


    <div class="form-group row">
      <label for="inputServer" class="col-sm-2 col-form-label">Available lists</label>
      <div class="col-sm-10">
        <select class="form-control" name="listIdsToCompare[]" placeholder="listIdsToCompare" multiple size=<?= count($listAvailable); ?>>
          <?php foreach ($listAvailable as $listId => $listName) { 
            echo '<option value="'.$listId.'">'.$listId." - ".$listName.'</option>';
          } ?>
        </select> 
      </div>
    </div>


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
            <th>art</th>
            <th>dyntaxaId</th>
            <th>scientificName</th>
            <th>swedishName</th>
            <th>rank</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($tabRecap as $art => $rowContent) { ?>
            <tr>
              <th><?= $art ?>/eurolist</th>
              <td scope="row"><?= $rowContent["eurolist"]["dyntaxa_id"] ?></td>
              <td scope="row"><?= $rowContent["eurolist"]["latin"] ?></td>
              <td scope="row"><?= $rowContent["eurolist"]["arthela"] ?></td>
              <td scope="row"><?= $rowContent["eurolist"]["rank"] ?></td>
            </tr>
            <?php foreach ($rowContent as $listId => $listCont) { 
              if ($listId!="eurolist") { ?>
              <tr>
                <th><?= $art."/".$listId ?></th>
                <?php if (in_array("dyntaxa_id", $listCont["errors"])) $bckgdColor="#FF6347"; else $bckgdColor=""; ?>
                <td scope="row" <?= ($bckgdColor!="" ? 'style="background-color:'.$bckgdColor.'"' : "") ?>><?= $listCont["dyntaxa_id"] ?></td>
                <?php if (in_array("latin", $listCont["errors"])) $bckgdColor="#FF6347"; else $bckgdColor=""; ?>
                <td scope="row" <?= ($bckgdColor!="" ? 'style="background-color:'.$bckgdColor.'"' : "") ?>><?= $listCont["latin"] ?></td>
                <?php if (in_array("arthela", $listCont["errors"])) $bckgdColor="#FF6347"; else $bckgdColor=""; ?>
                <td scope="row" <?= ($bckgdColor!="" ? 'style="background-color:'.$bckgdColor.'"' : "") ?>><?= $listCont["arthela"] ?></td>
                <?php if (in_array("rank", $listCont["errors"])) $bckgdColor="#FF6347"; else $bckgdColor=""; ?>
                <td scope="row" <?= ($bckgdColor!="" ? 'style="background-color:'.$bckgdColor.'"' : "") ?>><?= $listCont["rank"] ?></td>
              </tr>
            <?php }} ?>
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