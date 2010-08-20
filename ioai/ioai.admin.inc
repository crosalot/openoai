<?php

function ioai_settings_form($type_name, $options) {
  drupal_add_css(drupal_get_path('module', 'ioai').'/ioai.css');
  drupal_add_js(drupal_get_path('module', 'ioai').'/ioai.js');
  
  foreach (ioai_get_dc() as $dc) {
    $form[$dc.'-before'] = array('#value' => '<div class="oai-wrap">');
    $form[$dc] = array(
      '#title' => $dc,
      '#type' => 'select',
      '#options' => $options,
      '#multiple' => TRUE,
      '#default_value' => ioai_get_current_map($type_name, $dc),
    );
    $form[$dc.'_custom'] = array(
      '#type' => 'textfield', 
      '#default_value' => '',
      '#size' => 20, 
      '#attributes' => array('class' => 'global-field custom'),
      '#default_value' => ioai_get_current_global_map($type_name, $dc, 'custom')
    );
    $form[$dc.'_type'] = array(
      '#type' => 'select', 
      '#options' => ioai_get_global_types(), 
      '#default_value' => '',
      '#attributes' => array('class' => 'global-field type'),
      '#default_value' => ioai_get_current_global_map($type_name, $dc, 'type')
    );
    $form[$dc.'_format'] = array(
      '#type' => 'select', 
      '#options' => ioai_get_global_formats(),
      '#default_value' => '',
      '#attributes' => array('class' => 'global-field format'.''),
      '#default_value' => ioai_get_current_global_map($type_name, $dc, 'format')
    );
    $form[$dc.'-after'] = array('#value' => '</div>');
  }
  
  $form['type'] = array('#type' => 'hidden', '#value' => $type_name);
  $form['submit'] = array('#type' => 'submit', '#value' => 'Submit');
  return $form;
}

function ioai_settings_form_submit($form, &$form_state) {
  $values = $form_state['values'];
  $type = $values['type'];
  
  unset(
    $values['type']          ,
    $values['op']            ,
    $values['submit']        ,
    $values['form_build_id'] ,
    $values['form_token']    ,
    $values['form_id']
  );
  
  db_query("DELETE FROM {ioai_map} WHERE type = '%s'", $type);
  foreach ($values as $dc => $fields) {
    if (is_array($fields)) {
      foreach ($fields as $field) {
        list($group, $code) = explode(':', $field);
        if ($group == 'global') {
          $field .= ':'.$values[$dc.'_'.$code];
        }
        db_query("INSERT INTO {ioai_map} VALUES ('%s', '%s', '%s')", $dc, $field, $type);
      }
    }
  }
}

function ioai_global_settings_form() {
  $form['#submit'][] = 'ioai_global_settings_form_submit';
  
  $form['repository_name'] = array(
    '#title' => 'Repository name',
    '#type' => 'textfield',
    '#default_value' => variable_get('repository_name', 'opensearch'),
  );
  
  $form['repository_identifier'] = array(
    '#title' => 'Repository identifier',
    '#type' => 'textfield',
    '#default_value' => variable_get('repository_identifier', variable_get('site_name', 'opensearch')),
  );
  
  $form['oai_category'] = array(
    '#title' => 'Category',
    '#type' => 'textfield',
    '#default_value' => variable_get('oai_category', ''),
  );
  
  $form['ioai_allow_type'] = array(
    '#title' => 'Allow Content Types',
    '#type' => 'checkboxes',
    '#options' => node_get_types('names'),
    '#default_value' => variable_get('ioai_allow_type', array()),
  );
  
  $form['ioai_allow_comment'] = array(
    '#title' => t('Comment'),
    '#type' => 'checkbox',
    '#default_value' => variable_get('ioai_allow_comment', FALSE),
  );
  
  $form['ioai_allow_user'] = array(
    '#title' => t('User'),
    '#type' => 'checkbox',
    '#default_value' => variable_get('ioai_allow_user', FALSE),
  );
  
  $options[0] = t('None');
  foreach (taxonomy_get_vocabularies() as $voc) {
    $options[$voc->vid] = $voc->name;
  }
  $form['sets'] = array(
    '#title' => 'Select vocabulary for oai sets',
    '#type' => 'radios',
    '#options' => $options, 
    '#default_value' => variable_get('sets', 0),
  );
  $form['ioai_preset'] = array(
    '#title' => 'Image cache preset',
    '#type' => 'textfield',
    '#default_value' => variable_get('ioai_preset', ''),
  );
  
  $form = system_settings_form($form);
  $form['#submit'][] = 'ioai_clear_cache';
  
  return $form;
}

function _add_quote(&$str) {
  $str = "'".$str."'";
}

function ioai_global_settings_form_submit($form, $form_state) {
  $last_allow_type = array_filter(variable_get('ioai_allow_type', array()));
  $allow_type = (array_filter($form_state['values']['ioai_allow_type']));
  
  $delete_type = array_diff($last_allow_type, $allow_type);
  $add_type = array_diff($allow_type, $last_allow_type);
 
  array_walk($delete_type, '_add_quote');
  array_walk($add_type, '_add_quote');
  

  // NODE
  if (!empty($delete_type)) {
    db_query("DELETE FROM {ioai_node} WHERE type IN (".implode(',', $delete_type).") AND status != 0");
  }
  if (!empty($add_type)) {
    db_query("INSERT INTO {ioai_node} SELECT node.nid, node.changed, node.type, node.status, NULL FROM {node} node WHERE node.type IN (".implode(',', $add_type).") ON DUPLICATE KEY UPDATE datestamp = node.changed, status = node.status");
    $res = db_query("SELECT nid FROM {ioai_node} WHERE status != 0");
    while ($node = db_fetch_object($res)) {
      db_query("UPDATE {ioai_node} SET sets = '%s' WHERE nid = %d", ioai_node_get_sets($node->nid), $node->nid);
    }
    
  }

  // COMMENT
  $allow_comment = $form_state['values']['ioai_allow_comment'];
  $last_allow_comment = variable_get('ioai_allow_comment', FALSE);
  if ($allow_comment != $last_allow_comment) {
    if ($allow_comment) {
      db_query("INSERT INTO {ioai_node} SELECT comment.cid, comment.timestamp, 'comment', NOT comment.status, NULL FROM {comments} comment ON DUPLICATE KEY UPDATE datestamp = comment.timestamp, status = NOT comment.status");
    }
    else {
      db_query("DELETE FROM {ioai_node} WHERE type = 'comment' AND status != 0");
    }
  }
  
  // USER
  $allow_user= $form_state['values']['ioai_allow_user'];
  $last_allow_user = variable_get('ioai_allow_user', FALSE);
  if ($allow_user != $last_allow_user) {
    if ($allow_user) {
      db_query("INSERT INTO {ioai_node} SELECT user.uid, user.created, 'user', user.status, NULL FROM {users} user ON DUPLICATE KEY UPDATE datestamp = user.created, status = user.status");
    }
    else {
      db_query("DELETE FROM {ioai_node} WHERE type = 'user' AND status != 0");
    }
  }
  
}

function ioai_clear_cache() {
  menu_rebuild();
}

// NODE SETTINGS =================================================
function ioai_type_settings_form($form_state, $type_name) {
  $type = content_types($type_name);
  
  // Global
  $options['Global'] = ioai_get_global_map();
  
  // Node
  $options['Node'] = ioai_get_node_map();
  
  // Taxonomy
  foreach (taxonomy_get_vocabularies($type_name) as $voc) {
    $options['Taxonomy']['taxonomy:'.$voc->vid] = $voc->name;
  }
  
  // Field
  $fields = $type['fields'];
  foreach ($fields as $field) {
    $options['Field']['field:'.$field['field_name']] = $field['widget']['label']. ': ('.$field['field_name'].')';    
  }

  $form = ioai_settings_form($type_name, $options);
  
  return $form;
}

function ioai_type_settings_form_submit($form, &$form_state) {
  ioai_settings_form_submit($form, &$form_state);
}

// COMMENT SETTINGS ===============================================
function ioai_comment_settings_form() {
  // Global
  $options['Global'] = ioai_get_global_map();
  
  // Node
  $options['Comment'] = ioai_get_comment_map();
  
  $form = ioai_settings_form('comment', $options);
  
  return $form;
}
function ioai_comment_settings_form_submit($form, &$form_state) {
  ioai_settings_form_submit($form, &$form_state);
}

// USER SETTINGS ===================================================
function ioai_user_settings_form() {
  // Global
  $options['Global'] = ioai_get_global_map();
  
  // Node
  $options['User'] = ioai_get_user_map();
  
  $form = ioai_settings_form('user', $options);
  
  return $form;
}
function ioai_user_settings_form_submit($form, &$form_state) {
  ioai_settings_form_submit($form, &$form_state);
}
