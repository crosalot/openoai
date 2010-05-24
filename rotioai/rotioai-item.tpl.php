<div class="roti-search-item">
  <?php $img = ro_img($item->relation, array()); ?>
  <?php if(!$img): ?>
    <?php $img = drupal_get_path('module', 'rotioai').'/images/image-default.png'; ?>
  <?php endif ?>
  <div class="layer-first">
    <a href="<?php print ro_url($item->identifier) ?>">
      <img alt="" title="" src="<?php print $img ?>" />
    </a>
    <div class="icon-<?php print ro_str($item->type) ?>"><?php print ro_str($item->type) ?></div>
  </div>
  <div class="layer-last<?php if($img): ?> have-img<?php endif ?>">
    <h4><a href="<?php print ro_url($item->identifier) ?>"><?php print ro_str($item->title) ?></a></h4>
    <div>
      <p><?php print ro_str($item->description) ?></p>
      <div class="meta">
        <div class="terms">
          <span class="label"><?php print t("Subjects") ?></span>
          : <?php print ro_query($item->subject, 'subject'); ?>
        </div>
        <div class="created">
          <span class="label"><?php print t("Created at")?></span>
          
          : <?php print ro_date(empty($item->date)? $item->header_datestamp: $item->date); ?>
        </div>
      </div>
      <div class="clear"></div>
    </div>
  </div>
</div>

<?php

?>
