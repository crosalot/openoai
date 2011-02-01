<?php
$img = ro_img($item->relation, array('png', 'gif', 'jpg', 'bmp'));
$type = ro_str($item->type);
if ($type === 'book') {
  $node_path = array_pop(explode(':', $item->header_identifier[0]));
  $nid = array_pop(explode('/', $node_path));
  $url = url('species_index', array('fragment' => $nid . '--' . ro_str($item->title)));
}
else {
  $url = ro_url($item->identifier);
}
?>
<div class="roti-search-item<?php if($img): ?> have-img<?php endif ?>"> 
  <div class="layer-last">
    <div class="wrap">
      <h4><a href="<?php print $url ?>"><?php print ro_str($item->title) ?></a></h4>
      <div>
        <p><?php print ro_str($item->description) ?></p>
        <div class="meta">
          <div class="icon-<?php print ro_str($item->type) ?>">
            <span class="label"><?php print t('type') ?></span>
            : <?php print t($item->type[0]); ?>
          </div>
          <div class="terms">
            <span class="label"><?php print t("Category") ?></span>
              <?php $subject = ro_query($item->subject, 'subject'); ?> 
              <?php $subject = (empty($subject))?  '-' : $subject; ?>
            : <?php print $subject; ?>
          </div>
        </div>
      </div>
      <div class="clear"></div>
    </div>
  </div>
  <?php if($img): ?>
    <div class="layer-first">
      <div class="wrap">
        <a href="<?php print ro_url($item->identifier) ?>">
          <img alt="" title="" style = "width: 160px; height: auto" src="<?php print $img ?>" />
        </a>
      </div>
    </div>
  <?php endif; ?>
</div>

<?php

?>
