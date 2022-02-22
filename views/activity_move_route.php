<div class="container">

  <div class="float-right">Hi <?= $current_user->display_name ?> ! <a href="<?= URL_LOGOUT ?>">logout</a></div><br>

  <h2>Change the route of an activity</h2>

  <p class="lead">
  	Change the route of an activity in the mongo database, based on the activityId. 
  </p>

  <p>
    Please obtain the activityId from the URL in Biocollect (the 25 digit string that stands after https://biocollect.biodiversitydata.se/sft/bioActivity/index/).
  </p>


  <form role="form" method="post">
  	<input type="hidden" value="OK" id="getActivityDetails" name="getActivityDetails"/>

    <div class="form-group row">
      <label for="activityIdToFix" class="col-sm-2 col-form-label">Activity Id</label>
      <div class="col-sm-10">
        <input type=text class="form-control" id="activityIdToFix" name="activityIdToFix" maxlength=50 placeholder="activityIdToFix" value="<?= $activityIdToFix ?>">
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
        <input type="submit" value="Get activity details" name="submit" class="btn btn-primary"/>
      </div>
    </div>
  </form>

  <?php if (isset($final_result) &&  $final_result!="") { ?>
  <p class="lead">
    <b><?= $final_result ?></b>
  </p>
  <?php } ?>

  <?php if (isset($activityDetails) && count($activityDetails)>0 && isset($siteOldWrongDetails) && isset($outputDetails)) { ?>



    <div class="form-group row">
      <label class="col-sm-2 col-form-label">Survey Date</label>
      <div class="col-sm-10">
        <input type=text class="form-control" disabled value="<?= $outputDetails->data->surveyDate ?>">
      </div>
    </div>

    <div class="form-group row">
      <label class="col-sm-2 col-form-label">Site Id</label>
      <div class="col-sm-10">
        <input type=text class="form-control" disabled value="<?= $siteOldWrongId ?>">
      </div>
    </div>

    <div class="form-group row">
      <label class="col-sm-2 col-form-label">Site Name</label>
      <div class="col-sm-10">
        <input type=text class="form-control" disabled value="<?= $siteOldWrongDetails->name ?>">
      </div>
    </div>

    <div class="form-group row">
      <label class="col-sm-2 col-form-label">Site status</label>
      <div class="col-sm-10">
        <input type=text class="form-control" disabled value="<?= $siteOldWrongDetails->status ?>">
      </div>
    </div>

    <form role="form" method="post">
      <input type="hidden" value="OK" id="getNewCorrectSiteId" name="getNewCorrectSiteId"/>
      <input type="hidden" value="<?= $activityIdToFix ?>" id="activityIdToFixHidden" name="activityIdToFixHidden">
      <input type="hidden" value="<?= $server ?>" id="serverHidden" name="serverHidden"/>

      <div class="form-group row">
        <label for="activityIdToFix" class="col-sm-2 col-form-label">New site Id</label>
        <div class="col-sm-10">
          <!--<input type=text class="form-control" id="siteNewCorrectId" name="siteNewCorrectId" maxlength=50 placeholder="activityIdToFix" value="<?= $siteNewCorrectId ?>">-->
          <select class="form-control" id="inputSelectNewSite" name="inputSelectNewSite" placeholder="lan">
            <option value="" <?= ($siteNewCorrectId=="" ? "selected" : "")?>>Please select one site</option>
            <?php
            foreach ($sitesMongo as $site) {
              echo '<option value="'.$site["locationID"].'" '.($siteNewCorrectId==$site["locationID"] ? "selected" : "").'>'.$site["name"].'</option>';
            }
            ?>
          </select> 

        </div>
      </div>

      <div class="form-group row">
        <div class="offset-sm-2 col-sm-10">
          <input type="submit" value="Change the route" name="submit" class="btn btn-primary"/>
        </div>
      </div>
    </form>

  <?php } ?>

  <div class="form-group row">
    <label for="consoleArea" class="col-sm-2 col-form-label">CONSOLE</label>
  	<textarea class="form-control" rows=20 id="consoleArea"><?= ($consoleTxt!="" ? $consoleTxt : "No message.") ?>
  	</textarea>
  </div>
</div>