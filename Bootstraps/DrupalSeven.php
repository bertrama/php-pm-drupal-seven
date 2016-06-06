<?php
/**
 * @file
 * Contains \PHPPM\Bootstraps\DrupalSeven.
 */

namespace PHPPM\Bootstraps;

use Stack\Builder;
use React\Http\Request as Request;
use PHPPM\React\HttpResponse as Response;

/**
 * PHP-PM Bootstrap for Drupal Seven.
 */

class DrupalSeven implements BootstrapInterface {

  private $request;
  private $env;

  private static $keep = [
    'conf_path',
    'system_list',
    'ip_address',
    'module_implements',
    'module_implements:verified',
    'file_get_stream_wrappers',
    'drupal_alter',
    'drupal_lookup_path',
    'menu_get_custom_theme',
    'user_access',
    'user_role_permissions',
    'themekey_custom_theme',
    'path_get_admin_paths',
    'drupal_match_path',
    'themekey_custom_theme_called',
    'drupal_is_front_page',
    'list_themes',
    'menu_get_item',
    'entity_get_controller',
    'entity_get_info',
    '_node_types_build',
    'module_hook_info',
    'node_access',
    'theme_get_setting',
    'drupal_get_library',
    'ctools_plugins',
    'ctools_plugin_type_info_loaded',
    'ctools_plugin_type_info',
    'ctools_plugin_setup',
    'ctools_export_load_object',
    'ctools_export_load_object_table_exists',
    'ctools_export_load_object_all',
    'ctools_export_get_schema',
    '_ctools_export_get_defaults',
    'ctools_plugin_api_info',
    'mailsystem_get_classes',
    'og_context',
    'og_context_negotiation_info',
    'entity_get_property_info',
    '_field_info_field_cache',
    'og_get_entity_groups',
    'overlay_set_mode',
    'ctools_plugin_load_includes',
    'ctools_entity_from_field_get_children',
    'ctools_field_foreign_keys',
    'entityreference_get_behavior_handlers',
    'field_language',
    'language_list',
    'field_available_languages',
    'node_reference_field_prepare_view',
    'field_view_mode_settings',
    '_mollom_status',
    'conditional_fields_load_dependencies',
    'overlay_display_empty_page',
    'element_info',
    'libraries_get_path',
    'block_list',
    'ctools_set_no_blocks',
    'dashboard_regions',
    'overlay_set_regions_to_render',
    'menu_tree',
    'menu_tree_page_data',
    'menu_tree_set_path',
    '_menu_build_tree',
    'menu_load_all',
    'taxonomy_get_tree',
    'taxonomy_get_tree:parents',
    'taxonomy_get_tree:terms',
    '_views_fetch_data_cache',
    '_views_fetch_data_recursion_protected',
    '_views_fetch_data_fully_loaded',
    'entity_views_table_definition',
    'entity_views_get_field_handlers',
    '_entityreference_get_behavior_handler',
    'og_get_group_audience_fields',
    'mollom_form_cache',
    'template_preprocess',
    'ctools_process_classes',
    'filter_formats',
    '_rdf_get_default_mapping',
    'context_reaction_block_list',
    'panels_mini_load_all',
    'format_date',
    'image_styles',
    'og_ui_get_group_admin',
    'og_user_access',
    'og_user_access_alter',
    '_node_revision_access',
    'theme_get_registry',
    'drupal_add_css',
  ];

  private static $reset = [
    'path_is_admin',
    'arg',
    'drupal_add_js',
    'drupal_add_js:jquery_added',
    'drupal_add_library',
    'drupal_set_title',
    'drupal_add_html_head',
    'drupal_set_breadcrumb',
    'drupal_http_headers',
    'drupal_send_headers',
    'system_main_content_added',
    'drupal_set_page_content',
    'drupal_retrieve_form',
    'drupal_html_id:init',
    'drupal_html_id',
    'date_limit_format',
    'date_now',
    'panels_ipe_toolbar_buttons',
    'drupal_add_feed',
    'menu_local_tasks',
    'menu_local_tasks:root_path',
    'ctools_menu_add_tab',
    'mlibrary_zen_set_title',
    'template_preprocess_block',
    'form_set_error',
    'form_set_error:limit_validation_errors',
    'panel_body_css',
    'mollom_log',
    'drupal_page_is_cacheable',
  ];

  public function __construct($appenv, $env) {
    $this->appenv = $appenv;
    $this->env    = $env;
    $_SERVER['argc'] = NULL;
    $_SERVER['SCRIPT_NAME'] = '/install.php';
    define('DRUPAL_ROOT', getcwd());
    $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
    require_once(DRUPAL_ROOT . '/includes/bootstrap.inc');
    drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
    $this->defaultHeaders = headers_list();
    db_query("set wait_timeout = 28800");
  }

  public function reset() {

    // Remove all set headers
    foreach (headers_list() as $header) {
      header_remove(explode(':', $header)[0]);
    }

    // Replace the default headers.
    foreach($this->defaultHeaders as $header) {
      header($header);
    }

    // Reset static variables
    foreach (self::$reset as $reset) {
      drupal_static_reset($reset);
    }
  }

  private function execute() {
    if (file_exists($this->path)) {
      $this->content = file_get_contents($this->path);
    }
    else {
      ob_start();
      ob_start();
      menu_execute_active_handler($this->source);
      $this->content .= ob_get_clean();
      $this->content .= ob_get_clean();
    }
  }

  private function setup(Request $request) {
    $url = $request->getUrl();
    if ($url->getScheme() == 'https') {
      $_SERVER['HTTPS'] = 'on';
    }
    else {
      unset($_SERVER['HTTPS']);
    }
    $_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = $url->getHost();
    $this->path = ltrim($url->getPath(), '/');
    $this->source = drupal_get_normal_path($this->path);
    if (empty($this->source)) {
      $this->source = variable_get('site_frontpage', 'node');
    }
    $_GET['q'] = $this->source;

    $this->content = '';
  }

  private function write(Response $response) {
    $response->writeHead($this->getStatus(), $this->getHeaders());
    $response->end($this->content);
  }

  public function handle(Request $request, Response $response) {
    $this->setup($request);
    $this->execute();
    $this->write($response);
    $this->reset();
  }

  private function getStatus() {
    $ret = 200;
    foreach (headers_list() as $header) {
      if (strpos($header, 'Status:') === 0) {
        $ret = substr(ltrim(substr($header, 7, strlen($header))), 0, 3);
      }
    }
    return $ret;
  }

  private function getHeaders() {
    $ret = [];
    foreach (headers_list() as $header) {
      if (strpos($header, 'Status:') === 0) {
        $ret[] = $header;
      }
    }
    return $ret;
  }

  public function getApplication() {
    return $this;
  }

  public static function createFromRequest($request, $env) {
    return new self($request, $env);
  }

  public function getStaticDirectory() {
    return './';
  }

  public function getStack(Builder $stack) {
    return $stack;
  }

}
