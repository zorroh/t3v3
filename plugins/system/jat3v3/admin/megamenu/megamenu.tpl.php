<?php
  $db = JFactory::getDbo();
  $query = $db->getQuery(true);
  $query->select('id, title, module, position');
  $query->from('#__modules');
  $query->where('published = 1');
  $query->where('client_id = 0');
  $query->order('title');
  $db->setQuery($query);
  $modules = $db->loadObjectList();
?>
<?php if(is_file(T3V3_PATH . '/css/megamenu.css')): ?>
<link rel="stylesheet" href="<?php echo T3V3_URL ?>/css/megamenu.css" type="text/css" />
<?php endif; ?>
<?php if(is_file(T3V3_TEMPLATE_PATH . '/css/megamenu.css')): ?>
<link rel="stylesheet" href="<?php echo T3V3_TEMPLATE_URL ?>/css/megamenu.css" type="text/css" />
<?php endif; ?>
<?php if(is_file(T3V3_ADMIN_PATH . '/admin/megamenu/css/megamenu.css')): ?>
<link rel="stylesheet" href="<?php echo T3V3_ADMIN_URL ?>/admin/megamenu/css/megamenu.css" type="text/css" />
<?php endif; ?>
<script type="text/javascript" src="<?php echo T3V3_ADMIN_URL ?>/admin/megamenu/js/megamenu.js"></script>

<div id="megamenu-admin" class="hidden">
  <div class="t3-inline-toolbox clearfix">
    <div class="t3-row t3-row-mega clearfix">

      <div id="megamenu-toolbox">
        <div id="megamenu-toolitem" class="toolbox">
          <ul>
            <li>
              <label>Submenu</label>
              <fieldset class="radio btn-group toolitem-sub">
                <input type="radio" id="toggleSub0" class="toolbox-toggle" data-action="toggleSub" name="toggleSub" value="0"/>
                <label for="toggleSub0">No</label>
                <input type="radio" id="toggleSub1" class="toolbox-toggle" data-action="toggleSub" name="toggleSub" value="1" checked="checked"/>
                <label for="toggleSub1">Yes</label>
              </fieldset>
            </li>
          </ul>
          <ul>
            <li>
              <label>Group</label>
              <fieldset class="radio btn-group toolitem-group">
                <input type="radio" id="toggleGroup0" class="toolbox-toggle" data-action="toggleGroup" name="toggleGroup" value="0"/>
                <label for="toggleGroup0">No</label>
                <input type="radio" id="toggleGroup1" class="toolbox-toggle" data-action="toggleGroup" name="toggleGroup" value="1" checked="checked"/>
                <label for="toggleGroup1">Yes</label>
              </fieldset>
            </li>
          </ul>
          <ul>
            <li>
              <label>Positions</label>
              <fieldset class="btn-group">
                <a href="" class="btn toolitem-moveleft toolbox-action" data-action="moveItemsLeft"><i class="icon-arrow-left"></i>Move to Left Column</a>
                <a href="" class="btn toolitem-moveright toolbox-action" data-action="moveItemsRight"><i class="icon-arrow-right"></i>Move to Right Column</a>
              </fieldset>
            </li>
          </ul>
          <ul>
            <li>
              <label>Extra Class:</label>
              <fieldset class="">
                <input type="text" class="toolitem-exclass toolbox-input" name="toolitem-exclass" data-name="class" value="" />
              </fieldset>
            </li>
          </ul>
        </div>
        <div id="megamenu-toolsub" class="toolbox">
          <ul>
            <li>
              <label>Mega Submenu</label>
              <fieldset class="radio btn-group toolitem-megasub">
                <input type="radio" id="toggleMega0" class="toolbox-toggle" data-action="toggleMega" name="toggleMega" value="0"/>
                <label for="toggleMega0">No</label>
                <input type="radio" id="toggleMega1" class="toolbox-toggle" data-action="toggleMega" name="toggleMega" value="1" checked="checked"/>
                <label for="toggleMega1">Yes</label>
              </fieldset>
            </li>
          </ul>
          <ul>
            <li>
              <label>Menu Grid</label>
              <fieldset class="btn-group">
                <a href="" class="btn toolsub-addrow toolbox-action" data-action="addRow">Add Row</a>
              </fieldset>
            </li>
          </ul>
          <ul>
            <li>
              <label>Submenu Width (px)</label>
              <fieldset class="">
                <input type="text" class="toolsub-width toolbox-input input-small" name="toolsub-width" data-name="width" value="" />
              </fieldset>
            </li>
          </ul>
          <ul>
            <li>
              <label>Extra Class:</label>
              <fieldset class="">
                <input type="text" class="toolsub-exclass toolbox-input" name="toolsub-exclass" data-name="class" value="" />
              </fieldset>
            </li>
          </ul>
        </div>
        <div id="megamenu-toolcol" class="toolbox">
          <ul>
            <li>
              <label>Menu Grid</label>
              <fieldset class="btn-group">
                <a href="" class="btn toolcol-addcol toolbox-action" data-action="addColumn">Add Column</a>
                <a href="" class="btn toolcol-removecol toolbox-action" data-action="removeColumn">Remove Column</a>
              </fieldset>
            </li>
          </ul>
          <ul>
            <li>
              <label>Col Width (1-12)</label>
              <fieldset class="">
                <input type="text" class="toolcol-width toolbox-input input-small" name="toolcol-width" data-name="width" value="" />
              </fieldset>
            </li>
          </ul>
          <ul>
            <li>
              <label>Module</label>
              <fieldset class="">
                <select type="select" class="toolcol-position toolbox-input toolbox-select" name="toolcol-position" data-name="position">
                  <option value="">Select Module</option>
                <?php
                  foreach ($modules as $module) {
                    echo "<option value=\"{$module->id}\">{$module->title}</option>\n";
                  }
                ?>
                </select>
              </fieldset>
            </li>
          </ul>
          <ul>
            <li>
              <label>Extra Class:</label>
              <fieldset class="">
                <input type="text" class="toolcol-exclass toolbox-input" name="toolcol-exclass" data-name="class" value="" />
              </fieldset>
            </li>
          </ul>
        </div>    
      </div> 
      
      <div class="toolbox-actions-group">
        <button class="btn btn-success toolbox-action toolbox-saveConfig" data-action="saveConfig"><i class="icon-save"></i>Save</button>
        <button class="btn btn-danger toolbox-action toolbox-resetConfig"><i class="icon-undo"></i>Reset</button>
      </div>

    </div>
  </div>

  <div id="megamenu-container" class="navbar clearfix"></div> 
</div>