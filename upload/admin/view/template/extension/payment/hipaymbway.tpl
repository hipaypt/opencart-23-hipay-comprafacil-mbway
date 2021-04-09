<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-cod" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-body">


        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-hipaymbway" class="form-horizontal">
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-total"><?php echo $entry_cf_username; ?></label>
            <div class="col-sm-10">
              <input type="text" name="hipaymbway_username" value="<?php echo $hipaymbway_username; ?>" id="hipaymbway_username" class="form-control" />
            </div>
          </div>



          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-total"><?php echo $entry_cf_password; ?></label>
            <div class="col-sm-10"><input type="text" name="hipaymbway_password" id="hipaymbway_password" value="<?php echo $hipaymbway_password; ?>" class="form-control" /></div>
          </div>


          <div class="form-group">
            <label class="col-sm-2 control-label" for="hipaymbway_category"><?php echo $entry_cf_category; ?></label>
            <div class="col-sm-10"><input type="text" name="hipaymbway_category" id="hipaymbway_category" value="<?php echo $hipaymbway_category; ?>" class="form-control" /></div>
          </div>



          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-total"><?php echo $entry_cf_entity; ?></label>
            <div class="col-sm-10">
                <select name="hipaymbway_entity" id="hipaymbway_entity" class="form-control">
                    <option value="10241" <?php echo ($hipaymbway_entity=="10241")?'selected="selected"':''; ?> >10241 - 12029</option>
                    <option value="11249" <?php echo ($hipaymbway_entity=="11249")?'selected="selected"':''; ?> >11249 - 12101</option>
                </select>
            </div>
          </div>


          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-total"><?php echo $entry_cf_mode; ?></label>
            <div class="col-sm-10">
                <select name="hipaymbway_mode" id="hipaymbway_mode" class="form-control">
                    <option value="1" <?php echo ($hipaymbway_mode)?'selected="selected"':''; ?> ><?php echo $entry_cf_yes; ?></option>
                    <option value="0" <?php echo (!$hipaymbway_mode)?'selected="selected"':''; ?> ><?php echo $entry_cf_no; ?></option>
                </select>
           </div>
          </div>


          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-total"><?php echo $entry_status; ?></label>
            <div class="col-sm-10"><select name="hipaymbway_status" id="hipaymbway_status" class="form-control">
                <?php if ($hipaymbway_status) { ?>
                <option value="1" selected="selected"><?php echo $entry_cf_active; ?></option>
                <option value="0"><?php echo $entry_cf_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $entry_cf_active; ?></option>
                <option value="0" selected="selected"><?php echo $entry_cf_disabled; ?></option>
                <?php } ?>
              </select></div>
          </div>



                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-pending_status"><?php echo $entry_pending_status; ?></label>
                    <div class="col-sm-10">
                      <select name="hipaymbway_pending_status" id="hipaymbway_pending_status" class="form-control">
                        <?php foreach ($order_statuses as $order_status) { ?>
                        <?php if (isset($hipaymbway_pending_status) && $order_status['order_status_id'] == $hipaymbway_pending_status) {  ?>
                        <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                        <?php } else { ?>
                        <option value="<?php echo $order_status['order_status_id'] ?>"><?php echo $order_status['name']; ?></option>
                        <?php } ?>
                        <?php } ?>
                      </select>
                    </div>
                  </div>



                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-accepted_status"><?php echo $entry_accepted_status; ?></label>
                    <div class="col-sm-10">
                      <select name="hipaymbway_accepted_status" id="hipaymbway_accepted_status" class="form-control">
                        <?php foreach ($order_statuses as $order_status) { ?>
                        <?php if (isset($hipaymbway_accepted_status) && $order_status['order_status_id'] == $hipaymbway_accepted_status) {  ?>
                        <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                        <?php } else { ?>
                        <option value="<?php echo $order_status['order_status_id'] ?>"><?php echo $order_status['name']; ?></option>
                        <?php } ?>
                        <?php } ?>
                      </select>
                    </div>
                  </div>



                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-denied_status"><?php echo $entry_denied_status; ?></label>
                    <div class="col-sm-10">
                      <select name="hipaymbway_denied_status" id="hipaymbway_denied_status" class="form-control">
                        <?php foreach ($order_statuses as $order_status) { ?>
                        <?php if (isset($hipaymbway_denied_status) && $order_status['order_status_id'] == $hipaymbway_denied_status) {  ?>
                        <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                        <?php } else { ?>
                        <option value="<?php echo $order_status['order_status_id'] ?>"><?php echo $order_status['name']; ?></option>
                        <?php } ?>
                        <?php } ?>
                      </select>
                    </div>
                  </div>


                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-geo-zone"><?php echo $entry_geo_zone; ?></label>
                    <div class="col-sm-10">
                      <select name="hipaymbway_geo_zone_id" id="hipaymbway_geo_zone_id" class="form-control">
                        <option value="0"><?php echo $text_all_zones; ?></option>
                        <?php foreach ($geo_zones as $geo_zone) { ?>
                        <?php if (isset($hipaymbway_geo_zone_id) && $geo_zone['geo_zone_id'] == $hipaymbway_geo_zone_id) {  ?>
                        <option value="<?php echo $geo_zone['geo_zone_id'] ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                        <?php } else { ?>
                        <option value="<?php echo $geo_zone['geo_zone_id'] ?>"><?php echo $geo_zone['name']; ?></option>
                        <?php } ?>
                        <?php } ?>
                      </select>
                    </div>
                  </div>


          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-total"><?php echo $entry_sort_order; ?></label>
            <div class="col-sm-10"><input type="text" class="form-control" name="hipaymbway_sort_order" id="hipaymbway_sort_order" value="<?php echo $hipaymbway_sort_order; ?>" size="1" /></div>
          </div>


      </form>

      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>
