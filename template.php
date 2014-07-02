<?php

/**
 * @file Theme functions for Bootstrap base Theme site.
 */

/**
 * Preprocess html
 *
 * @param array $vars
 * @return void
 */
function bootstrap_base_process_html(&$vars) {
  $search = array('scripts' => 'src=', 'styles' => 'href=', 'styles' => '@import\surl\(');
  foreach ( $search as $var => $word ) {
    if ( !empty($vars[$var]) ) {
      $lines = explode("\n", $vars[$var]);
      $result = array();
      foreach($lines as $line) {
        $matches = array();
        if ( preg_match('/' . $word . '"(.*)"/', $line, $matches) ) {
          global $language;
          $match = $matches[1];
          $replacement = $matches[1];
          // remove the ? and everything behind it
          $pos = strpos($replacement, '?');
          $replaced = $line;
          if ( $pos !== FALSE ) {
            $replacement = substr($replacement, 0, $pos);
            $replaced = str_ireplace($match, $replacement, $line);
          }
          $result[] = $replaced;
        }
        else {
          $result[] = $line;
        }
      }
      if ( !empty($result) ) {
        $vars[$var] = implode("\n", $result);
      }
    }
  }
}

/**
 * Implements theme_prepocess_page().
 */
function bootstrap_base_preprocess_page(&$vars) {
  // Count sidebars.
  $count = 0;
  foreach ($vars['page'] as $key => $value) {
    if (substr($key, 0, 7) == "sidebar" && !empty($value)) {
      $count++;
    }
  }
  // Calculate how many rows they take up.
  $vars['content_width'] = 'col-md-' . (12 - ($count * 4));

  // Get the entire main menu tree
  $main_menu_tree = menu_tree_all_data('main-menu');

  // Add the rendered output to the $main_menu_expanded variable
  $vars['main_menu_expanded'] = menu_tree_output($main_menu_tree);

  // If admin make full width.
  if (arg(0) == 'admin' || (arg(0) == 'node' && arg(2) == 'edit') || (arg(0) == 'node' && arg(1) == 'add')) {
    $vars['page']['header'] = array();
    $vars['page']['sidebar_right'] = array();
    $vars['content_width'] = 'col-md-12';
  }
}

/**
 * Implements hook_links__system_main_menu().
 *
 * @param array $vars
 * @return string
 *  Themed HTML for bootstrap 3 ready main menu.
 */
function bootstrap_base_links__system_main_menu($vars) {
  // Get the active trail
  $menu_active_trail = menu_get_active_trail();
  // Initialise our custom trail.
  $active_trail = array();

  // Get current path
  $dest = drupal_get_destination();
  if (is_string($dest['destination'])) {
    $paths = explode('/', $dest['destination']);
    // Loop through and add all active paths
    foreach ($paths as $path) {
      // Read previous element added to active trail (using array values
      // preserves original array).
      $safe = array_values($active_trail);
      $previous = array_pop($safe);
      if ($previous) {
        $active_trail[] = $previous . '/' . $path;
      }
      // Or this is the first one
      else {
        $active_trail[] = $path;
      }
    }
  }

  // UL classes
  $class = implode($vars['attributes']['class'], ' ');
  $html = '<ul class="' . $class . '"';
  // Check if there is an ID set (not if it's a dropdown sub-menu).
  if (isset($vars['attributes']['id'])) {
    $html .= ' id="' . $vars['attributes']['id'] . '"';
  }
  $html .= '>';
  // Iterate links to build menu.
  foreach ($vars['links'] as $key => $link) {

    // Check this is a link not a property.
    if (is_numeric($key)) {
      $sub_menu = '';
      $li_class = array();
      $a_class = array();

      // Check if link is in active trail and add class.
      if (in_array($link['#original_link']['link_path'], $active_trail)) {
        $li_class[] = 'active-trail';
      }
      if ($link['#original_link']['link_path'] == end($active_trail)) {
        $li_class[] = 'active';
      }
      // Check if last element in list and see if LI contains actual link
      $link['#attributes']['class'][] = strtolower(str_replace(array('& ', ' '), array('', '-'), $link['#title']));
      $link_title = $link['#title'];
      // Open subscribe in a new window.
      if ($link_title == 'Subscribe') {
        $link['#localized_options']['attributes']['target'] = '_blank';
      }
      if (isset($link['#localized_options']['attributes'])) {
        $link['#attributes'] = array_merge($link['#localized_options']['attributes'], $link['#attributes']);
      }

      // Check if we have a submenu.
      if (!empty($link['#below'])) {
        // Check if lvl 1, if higher do other stuff
        if ($link['#original_link']['depth'] < 2) {
          $li_class[] = 'dropdown';
          $link_title .= '<b class="caret"></b>';
          $link['#attributes']['class'][] = 'dropdown-toggle';
          $link['#attributes']['data-toggle'] = 'dropdown';
        } else {
          $li_class[] = 'dropdown-submenu';
          $link_title .= '<b class="caret"></b>';
        }
        // Theme submenu
        $sub_menu = theme('links__system_main_menu', array('links' => $link['#below'], 'attributes' => array('class' => array('dropdown-menu'))));
      }
      // Build classes string
      $classes = '';
      if (!empty($li_class)) {
        $classes = ' class="' . implode($li_class, ' ') . '"';
      }
      $html .= '<li' . $classes . '>' . l($link_title, $link['#href'], array('html' => 'true', 'attributes' => $link['#attributes'])) . $sub_menu . '</li>';
    }
  }
  $html .= '</ul>';
  return $html;
}

/**
 * Theme secondary menu.
 *
 * @param array $vars
 *  Links and attributes.
 *
 * @return string
 *  Themed HTML.
 */
function bootstrap_base_links__system_secondary_menu($vars) {
  // Class attrubtes, rude not to include.
  $class = implode($vars['attributes']['class'], ' ');
  $html = '<ul class="' . $class . '" id="' . $vars['attributes']['id'] . '">';
  // Add links.
  foreach ($vars['links'] as $link) {
    // Add title as class.
    $link['attributes']['class'][] = strtolower(str_replace(array('& ', ' '), array('', '-'), $link['title']));
    // Subscribe always openes in a new window.
    if ($link['title'] == 'Subscribe'  || $link['title'] == 'The Magazine') {
      $link['attributes']['target'] = '_blank';
    }
    $html .= "<li>".l($link['title'], $link['href'], $link)."</li>";
  }
  $html .= "  </ul>\n";
  return $html;
}

/**
 * Implements theme_menu_local_tasks().
 *
 * Tabs!
 */
function bootstrap_base_menu_local_tasks(&$vars) {
  $output = '';
  if (!empty($vars['primary'])) {
    $vars['primary']['#prefix'] = '<h2 class="element-invisible">' . t('Primary tabs') . '</h2>';
    $vars['primary']['#prefix'] .= '<ul class="tabs-primary nav nav-tabs">';
    $vars['primary']['#suffix'] = '</ul>';
    $output .= drupal_render($vars['primary']);
  }
  if (!empty($vars['secondary'])) {
    $vars['secondary']['#prefix'] = '<h2 class="element-invisible">' . t('Secondary tabs') . '</h2>';
    $vars['secondary']['#prefix'] .= '<ul class="tabs-secondary nav nav-tabs">';
    $vars['secondary']['#suffix'] = '</ul>';
    $output .= drupal_render($vars['secondary']);
  }
  return $output;
}

/**
 * Implements theme_breadcrumb().
 */
function bootstrap_base_breadcrumb($vars) {
  $breadcrumbs = $vars['breadcrumb'];

  if (!empty($breadcrumbs)) {
    // Provide a navigational heading to give context for breadcrumb links to
    // screen-reader users. Make the heading invisible with .element-invisible.
    $output = '<h2 class="element-invisible">' . t('You are here') . '</h2>';
    // Create an array to pass to theme_item_list.
    $crumbs = array();
    foreach ($breadcrumbs as $delta => $crumb) {
      $class = array();
      // If it's the last one it's active.
      if ((int) $delta + 1 == count($breadcrumbs)) {
        $class[] = 'active';
      }
      $crumbs[] = array(
        'data' => $crumb,
        'class' => $class,
      );
    }
    $output .= theme('item_list', array('type' => 'ol', 'items' => $crumbs, 'attributes' => array('class' => array('breadcrumb'))));
    return $output;
  }
}

/**
 * Implements theme_pager().
 */
function bootstrap_base_pager($vars) {
  $tags = $vars['tags'];
  $element = $vars['element'];
  $parameters = $vars['parameters'];
  $quantity = $vars['quantity'];
  global $pager_page_array, $pager_total;

  // Calculate various markers within this pager piece:
  // Middle is used to "center" pages around the current page.
  $pager_middle = ceil($quantity / 2);
  // current is the page we are currently paged to
  $pager_current = $pager_page_array[$element] + 1;
  // first is the first page listed by this pager piece (re quantity)
  $pager_first = $pager_current - $pager_middle + 1;
  // last is the last page listed by this pager piece (re quantity)
  $pager_last = $pager_current + $quantity - $pager_middle;
  // max is the maximum page number
  $pager_max = $pager_total[$element];
  // End of marker calculations.

  // Prepare for generation loop.
  $i = $pager_first;
  if ($pager_last > $pager_max) {
    // Adjust "center" if at end of query.
    $i = $i + ($pager_max - $pager_last);
    $pager_last = $pager_max;
  }
  if ($i <= 0) {
    // Adjust "center" if at start of query.
    $pager_last = $pager_last + (1 - $i);
    $i = 1;
  }
  // End of generation loop preparation.

  $li_first = theme('pager_first', array('text' => (isset($tags[0]) ? $tags[0] : t('«')), 'element' => $element, 'parameters' => $parameters));
  $li_previous = theme('pager_previous', array('text' => (isset($tags[1]) ? $tags[1] : t('‹')), 'element' => $element, 'interval' => 1, 'parameters' => $parameters));
  $li_next = theme('pager_next', array('text' => (isset($tags[3]) ? $tags[3] : t('›')), 'element' => $element, 'interval' => 1, 'parameters' => $parameters));
  $li_last = theme('pager_last', array('text' => (isset($tags[4]) ? $tags[4] : t('»')), 'element' => $element, 'parameters' => $parameters));

  if ($pager_total[$element] > 1) {
    if ($li_first) {
      $items[] = array(
        'class' => array('pager-first'),
        'data' => $li_first,
      );
    }
    if ($li_previous) {
      $items[] = array(
        'class' => array('pager-previous'),
        'data' => $li_previous,
      );
    }

    // When there is more than one page, create the pager list.
    if ($i != $pager_max) {
      if ($i > 1) {
        $items[] = array(
          'class' => array('pager-ellipsis', 'disabled'),
          'data' => '<span>…</span>',
        );
      }
      // Now generate the actual pager piece.
      for (; $i <= $pager_last && $i <= $pager_max; $i++) {
        if ($i < $pager_current) {
          $items[] = array(
            'class' => array('pager-item'),
            'data' => theme('pager_previous', array('text' => $i, 'element' => $element, 'interval' => ($pager_current - $i), 'parameters' => $parameters)),
          );
        }
        if ($i == $pager_current) {
          $items[] = array(
            'class' => array('pager-current', 'active'),
            'data' => '<span>' . $i . '</span>',
          );
        }
        if ($i > $pager_current) {
          $items[] = array(
            'class' => array('pager-item'),
            'data' => theme('pager_next', array('text' => $i, 'element' => $element, 'interval' => ($i - $pager_current), 'parameters' => $parameters)),
          );
        }
      }
      if ($i < $pager_max) {
        $items[] = array(
          'class' => array('pager-ellipsis', 'disabled'),
          'data' => '<span>…</span>',
        );
      }
    }
    // End generation.
    if ($li_next) {
      $items[] = array(
        'class' => array('pager-next'),
        'data' => $li_next,
      );
    }
    if ($li_last) {
      $items[] = array(
        'class' => array('pager-last'),
        'data' => $li_last,
      );
    }
    return '<h2 class="element-invisible">' . t('Pages') . '</h2>' . theme('item_list', array(
      'items' => $items,
      'attributes' => array('class' => array('pagination')),
      'wrapper_attributes' => array('class' => array('pager-wrapper'))
    ));
  }
}

/**
 * Implements theme_preprocess_node().
 *
 * @param array $vars
 * @param string $hook
 */
function bootstrap_base_preprocess_node(&$vars, $hook) {
  // Add a full class if full node view.
  if ($vars['view_mode'] == 'full') {
    $vars['classes_array'][] = 'node-full';
  }
}

/**
 * Implements theme_form_element().
 *
 *  Add classes etc. to help integrate with Bootstrap.
 *
 */
function bootstrap_base_form_element($vars) {
  $element = &$vars['element'];
  // This function is invoked as theme wrapper, but the rendered form element
  // may not necessarily have been processed by form_builder().
  $element += array(
    '#title_display' => 'before',
  );

  // Add element #id for #type 'item'.
  if (isset($element['#markup']) && !empty($element['#id'])) {
    $attributes['id'] = $element['#id'];
  }
  // Add element's #type and #name as class to aid with JS/CSS selectors.
  $attributes['class'] = array('form-item');
  
  if (in_array($element['#type'], array('textfield'))) {
    $attributes['class'][] = 'form-group';
  }
  
  if (!empty($element['#type'])) {
    $attributes['class'][] = 'form-type-' . strtr($element['#type'], '_', '-');
  }
  if (!empty($element['#name'])) {
    $attributes['class'][] = 'form-item-' . strtr($element['#name'], array(' ' => '-', '_' => '-', '[' => '-', ']' => ''));
  }
  // Add a class for disabled elements to facilitate cross-browser styling.
  if (!empty($element['#attributes']['disabled'])) {
    $attributes['class'][] = 'form-disabled';
  }
  $output = '<div' . drupal_attributes($attributes) . '>' . "\n";

  // If #title is not set, we don't display any label or required marker.
  if (!isset($element['#title'])) {
    $element['#title_display'] = 'none';
  }
  $prefix = isset($element['#field_prefix']) ? '<span class="field-prefix">' . $element['#field_prefix'] . '</span> ' : '';
  $suffix = isset($element['#field_suffix']) ? ' <span class="field-suffix">' . $element['#field_suffix'] . '</span>' : '';

  switch ($element['#title_display']) {
    case 'before':
    case 'invisible':
      $output .= ' ' . theme('form_element_label', $vars);
      $output .= ' ' . $prefix . $element['#children'] . $suffix . "\n";
      break;

    case 'after':
      $output .= ' ' . $prefix . $element['#children'] . $suffix;
      $output .= ' ' . theme('form_element_label', $vars) . "\n";
      break;

    case 'none':
    case 'attribute':
      // Output no label and no required marker, only the children.
      $output .= ' ' . $prefix . $element['#children'] . $suffix . "\n";
      break;
  }

  if (!empty($element['#description'])) {
    $output .= '<div class="description">' . $element['#description'] . "</div>\n";
  }

  $output .= "</div>\n";

  return $output;
}

/**
 * Implements theme_textarea();
 */
function bootstrap_base_textarea($vars) {
  $element = $vars['element'];
  element_set_attributes($element, array('id', 'name', 'cols', 'rows'));
  _form_set_class($element, array('form-textarea', 'form-control'));

  $wrapper_attributes = array(
    'class' => array('form-textarea-wrapper'),
  );

  // Add resizable behavior.
  if (!empty($element['#resizable'])) {
    drupal_add_library('system', 'drupal.textarea');
    $wrapper_attributes['class'][] = 'resizable';
  }

  $output = '<div' . drupal_attributes($wrapper_attributes) . '>';
  $output .= '<textarea' . drupal_attributes($element['#attributes']) . '>' . check_plain($element['#value']) . '</textarea>';
  $output .= '</div>';
  return $output;
}

/**
 * Implements hook_form_search_block_form_alter().
 */
function bootstrap_base_form_search_block_form_alter(&$form, &$form_state, $form_id) {
  $form['search_block_form']['#size'] = 20;
  // SHIIIIITT, hide the fucking input field as nothing else seems to work.
  $form['actions']['submit']['#attributes']['class'] = array('hidden');
  // Add our other button button.
  $form['actions']['button'] = array (
    '#prefix' => '<button type="submit" id="edit-submit-btn" name="op" class="fa fa-search form-submit">',
    '#suffix' => '</button>',
    '#markup' => '', // This line is required to force the element to render
  '#weight' => 1000,
  );
}

// Field display.
// Included as functions for performance reasons, see
// https://api.drupal.org/api/drupal/modules!field!field.module/function/theme_field/7

/**
 * Override or insert variables for process_field().
 */
function bootstrap_base_process_field(&$vars) {

}

/**
 * Implements theme_item_list();
 */
function bootstrap_base_item_list($vars) {
  $items = $vars['items'];
  $title = $vars['title'];
  $type = $vars['type'];
  $attributes = $vars['attributes'];
  if (isset($vars['wrapper_attributes'])) {
    $wrapper_attributes = array_merge_recursive($vars['wrapper_attributes'], array('class' => array('item-list')));
  }
  else {
    $wrapper_attributes = array('class' => array('item-list'));
  }
  // Only output the list container and title, if there are any list items.
  // Check to see whether the block title exists before adding a header.
  // Empty headers are not semantic and present accessibility challenges.
  $output = '<div' . drupal_attributes($wrapper_attributes). '>';
  if (isset($title) && $title !== '') {
    $output .= '<h3>' . $title . '</h3>';
  }

  if (!empty($items)) {
    $output .= "<$type" . drupal_attributes($attributes) . '>';
    $num_items = count($items);
    $i = 0;
    foreach ($items as $item) {
      $attributes = array();
      $children = array();
      $data = '';
      $i++;
      if (is_array($item)) {
        foreach ($item as $key => $value) {
          if ($key == 'data') {
            $data = $value;
          }
          elseif ($key == 'children') {
            $children = $value;
          }
          else {
            $attributes[$key] = $value;
          }
        }
      }
      else {
        $data = $item;
      }
      if (count($children) > 0) {
        // Render nested list.
        $data .= theme_item_list(array('items' => $children, 'title' => NULL, 'type' => $type, 'attributes' => $attributes));
      }
      if ($i == 1) {
        $attributes['class'][] = 'first';
      }
      if ($i == $num_items) {
        $attributes['class'][] = 'last';
      }
      $output .= '<li' . drupal_attributes($attributes) . '>' . $data . "</li>\n";
    }
    $output .= "</$type>";
  }
  $output .= '</div>';
  return $output;
}