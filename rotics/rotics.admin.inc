<?php

function rotics_settings() {
  $form['rotics_number'] = array(
    '#type' => 'textfield',
    '#title' => t('Number of roti custom page'),
    '#default_value' => variable_get('rotics_number', 2),
  );
  return system_settings_form($form);
}

function rotics_page_settings($form_state, $id) {
  $form['rotics_title_'.$id] = array(
    '#type' => 'textfield',
    '#title' => t('Title'),
    '#default_value' => variable_get('rotics_title_'.$id, ''),
  );
  $form['rotics_path_'.$id] = array(
    '#type' => 'textfield',
    '#title' => t('Path'),
    '#default_value' => variable_get('rotics_path_'.$id, ''),
  );
  $form['filters'] = array(
    '#type' => 'fieldset',
    '#title' => t('Filters'),
  );
  $form['filters']['rotics_type_'.$id] = array(
    '#type' => 'textfield',
    '#title' => t('Type'),
    '#default_value' => variable_get('rotics_type_'.$id, ''),
  );
  $form['filters']['rotics_subject_'.$id] = array(
    '#type' => 'textfield',
    '#title' => t('Subject'),
    '#default_value' => variable_get('rotics_subject_'.$id, ''),
  );
  return system_settings_form($form);
}
