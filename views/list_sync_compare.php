<div class="container">

  <div class="float-right">Hi <?= $current_user->display_name ?> ! <a href="<?= URL_LOGOUT ?>">logout</a></div><br>

  <h2>Lists comparator</h2>

  <p class="lead">
  	Getting lists data from <a href="https://lists.biodiversitydata.se" target="_blank">lists.biodiversitydata.se</a> and compare with what exists right now in the STF database, table eurolist.
  </p>

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

  <div class="form-group row">
    <label for="consoleArea" class="col-sm-2 col-form-label">CONSOLE</label>
  	<textarea class="form-control" rows=20 id="consoleArea"><?= ($consoleTxt!="" ? $consoleTxt : "No message.") ?>
  	</textarea>
  </div>
</div>