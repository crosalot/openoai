<?php
// $Id: rotioai.admin.inc $

/**
 * Module settings form.
 */
function rotioai_settings(&$form_state) {
  $form['rotioai'] = array(
    '#type' => 'fieldset',
    '#title' => 'Roti OAI',
  );
  $form['rotioai']['rotioai_url'] = array(
    '#type' => 'textfield',
    '#title' => t('URL'),
    '#default_value' => variable_get('rotioai_url', ROTIOAI_DEFAULT_URL),
    '#description' => t('Contact Roti administrator for the pinged and search URL of your site.'),
  );
  $form['rotioai']['rotioai_keywords_text'] = array(
    '#type' => 'textfield',
    '#title' => t('Keywords text'),
    '#default_value' => variable_get('rotioai_keywords_text', 'Keyword(s)'),
  );
  $form['rotioai']['rotioai_ipp'] = array(
    '#title' => t('Items per page'),
    '#type' => 'textfield',
    '#default_value' => variable_get('rotioai_ipp', 10),
    '#description' => t('Number of result items that will be shown per one page.'),
  );
  foreach (ioai_get_dc() as $dc) {
    $dc = substr($dc, 3);
    $options[$dc] = $dc;
  }
  $form['rotioai']['rotioai_dc'] = array(
    '#title' => t('Search advance fields'),
    '#type' => 'checkboxes',
    '#options' => $options,
    '#default_value' => variable_get('rotioai_dc', array()),
    '#description' => t('Allow user search dc field in advance search'),
  );
  
  $form['rotioai']['cross'] = array(
    '#title' => t('Cross search'),
    '#type' => 'fieldset',
    '#collapsible' => TRUE,
    '#collapsed' => !(variable_get('rotioai_cross_cat', FALSE) && !variable_get('rotioai_cross', FALSE)),
  );
  $form['rotioai']['cross']['rotioai_cross'] = array(
    '#title' => t('Repository'),
    '#type' => 'checkbox',
    '#default_value' => variable_get('rotioai_cross', FALSE),
    '#description' => t('Allow search cross repositories'),
  );
  $form['rotioai']['cross']['rotioai_repo'] = array(
    '#title' => t('Repositories for cross search'),
    '#type' => 'textarea',
    '#default_value' => variable_get('rotioai_repo', variable_get('repository_identifier', 'Repository identifier').'|'.variable_get('repository_name', 'Repository name')),
    '#description' => t("Example:<br />[repository identifier 1]|[repository name 1]<br />[repository identifier 2]|[repository name 2]"),
  );
  $form['rotioai']['cross']['rotioai_cross_cat'] = array(
    '#title' => t('catrgories'),
    '#type' => 'checkbox',
    '#default_value' => variable_get('rotioai_cross_cat', FALSE),
    '#description' => t('Allow search from categories'),
  );
  $form['rotioai']['cross']['rotioai_repo_cat'] = array(
    '#title' => t('Categories for cross search'),
    '#type' => 'textfield',
    '#default_value' => variable_get('oai_category', ''),
    '#description' => t('Separate by , and no space'),
  );
  
  $form['rotioai']['quick'] = array(
    '#title' => t('Quick search'),
    '#type' => 'fieldset',
    '#collapsible' => TRUE,
    '#collapsed' => !variable_get('rotioai_quick_full', FALSE) && !variable_get('rotioai_quick_block', FALSE),
  );
  $form['rotioai']['quick']['rotioai_quick_full'] = array(
    '#title' => t('Full form'),
    '#type' => 'checkbox',
    '#default_value' => variable_get('rotioai_quick_full', FALSE),
    '#description' => t('Allow quick search form [full]'),
  );
    $form['rotioai']['quick']['rotioai_quick_block'] = array(
    '#title' => t('Block form'),
    '#type' => 'checkbox',
    '#default_value' => variable_get('rotioai_quick_block', FALSE),
    '#description' => t('Allow quick search form [block]'),
  );
  $form['rotioai']['quick']['rotioai_quick_rule'] = array(
    '#title' => t('Rule'),
    '#type' => 'textarea',
    '#default_value' => variable_get('rotioai_quick_rule', ''),
    '#description' => 'Example:<br />type|story<br />type|page<br />subject|health<br />subject|sport',
  );

  return system_settings_form($form);
}

/**
 * Reindex form.
 */
function rotioai_reindex_form() {
  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Re-index RotiOAI'),
  );
  $form['#redirect'] = 'admin/build/rotioai/reindex';
  return $form;
}
