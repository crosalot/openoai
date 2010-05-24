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
  
  $form['rotioai']['rotioai_cross'] = array(
    '#title' => t('Cross search'),
    '#type' => 'checkbox',
    '#default_value' => variable_get('rotioai_cross', FALSE),
    '#description' => t('Allow search cross site'),
  );
  $form['rotioai']['rotioai_repo'] = array(
    '#title' => t('Repositories for cross search'),
    '#type' => 'textarea',
    '#default_value' => variable_get('rotioai_repo', variable_get('site_name', 'Repository identifier').'|'.variable_get('repository_name', 'Repository name')),
  );

  
  return system_settings_form($form);
}

?>
