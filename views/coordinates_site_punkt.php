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

  <h2>Punktrutter coordinates list and import</h2>

  <p class="lead">
  	Listing/importing coordinates for punktrutterna from/in Ecodata/BioCollect
  </p>
  <?php  if ($modeDisplay=="coord") { ?>
    <p><a href="?display=default"><input type="button" value="Show all the sites" name="submit" class="btn btn-primary"/></a></p>
  <?php } else { ?>
    <p><a href="?display=coord"><input type="button" value="Show all the coordinates" name="submit" class="btn btn-primary"/></a></p>
  <?php } ?>

  <form role="form" method="post">
    <label>Import new coordinates from an excel file following the template :</label>
  	<input type="hidden" value="OK" id="formCoordPunktSite" name="formCoordPunktSite"/>

    <div class="form-group row">
      <div class="offset-sm-2 col-sm-10">
        <input type="submit" value="Import" name="submit" class="btn btn-primary"/>
      </div>
    </div>
  </form>

  <?php if (isset($final_result) &&  $final_result!="") { ?>
  <p class="lead">
    <b><?= $final_result ?></b>
  </p>
  <?php } ?>

  <?php if (isset($arrCoordSites) && count($arrCoordSites)>0) { ?>

    <div class="form-group row">

      <table id="table"
        data-toggle="table"
        data-show-columns="true"
        data-search="true"
        data-show-export="true">
        <thead>
          <tr>
            <th data-sortable="true" scope="col">#</th>
            <th data-sortable="true" scope="col">Internal Site Id</th>
            <th data-sortable="true" scope="col">kartaTx</th>
            <?php  if ($modeDisplay=="coord") { ?>
            <th data-sortable="true" scope="col">Name</th>
            <th data-sortable="true" scope="col">Latitude</th>
            <th data-sortable="true" scope="col">Longitude</th>
            <?php } else { ?>
            <th data-sortable="true" scope="col">Nb coordinates</th>
            <?php } ?>
            <th data-sortable="true" scope="col">Biocollect link</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($arrCoordSites as $idS => $coordSite) { ?>
            <tr>
              <th scope="row"><?= ($idS+1) ?></th>
              <td><?= $coordSite["internalSiteID"] ?></td>
              <td><?= $coordSite["kartaTx"] ?></td>
              <?php  if ($modeDisplay=="coord") { ?>
              <td><?= $coordSite["name"] ?></td>
              <td><?= $coordSite["latitude"] ?></td>
              <td><?= $coordSite["longitude"] ?></td>
              <?php } else { ?>
              <td><?= $coordSite["nbTransectParts"] ?></td>
              <?php } ?>                
              <td><a href="<?= $coordSite["urlBioCollect"] ?>" target="_blank">biocollect link</a></td>
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