<?php
/***
 *                                       _           _____
 *                                      | |         |  ___|
 *      __ _  __ _  ___ _ __   ___ ___  | |__   ___ |___ \
 *     / _` |/ _` |/ _ \ '_ \ / __/ _ \ | '_ \ / _ \    \ \
 *    | (_| | (_| |  __/ | | | (_|  __/ | | | | (_) /\__/ /
 *     \__,_|\__, |\___|_| |_|\___\___| |_| |_|\___/\____/
 *            __/ |
 *           |___/
 *
 *           >> https://agenceho5.com
 */

namespace Nsi\Helpers;

/**
 * Permet de générer un block Gutenberg
 */
abstract class GBlock extends Singleton {

  const BLOCK_GROUP = 'nsi_blocks';
  const BLOCK_NAME  = 'undefined';

  protected static $args  = [];
  protected static $paths = [];

  /**
   * Constructeur
   */
  public function __construct() {
    static::$args[static::BLOCK_NAME] = \apply_filters( static::BLOCK_GROUP . '_args', [
      'name'     => static::BLOCK_NAME,
      'block_id' => static::getBlockID(),
      'args'     => \array_merge( static::getDefaultArgs(), static::getArgs() ),
      'panels'   => static::getPanels(),
    ] );
    add_action( 'init', [\get_called_class(), 'registerScripts'] );
    add_action( 'init', [\get_called_class(), 'registerBlock'], 20 );
    add_action( 'init', [\get_called_class(), 'localizeScripts'], 99 );

    add_filter( 'block_categories', [\get_called_class(), 'block_categories'], 10, 2 );
    static::hooks();

    $class_info                        = new \ReflectionClass( get_called_class() );
    static::$paths[get_called_class()] = dirname( $class_info->getFileName() );
  }

  /**
   * Function to extend to add some hook calls
   */
  protected function hooks() {}

  /**
   * Permet à l'utilisateur qui étend cette classe de définir les attributs
   */
  protected abstract function getArgs();

  /**
   * Renvoie les panels à afficher pour le block
   * @return Array : Tableau des panels à générer automatiquement
   */
  protected function getPanels() {
    return [];
  }

  /**
   * Définit les valeurs par défaut du block (si aucune configuartion n'est fournie)
   * @return Array : Les arguments par défaut du block
   */
  private static function getDefaultArgs() {
    return [
      'title'       => 'Block sans nom',
      'description' => null,
      'category'    => static::BLOCK_GROUP,
      'icon'        => 'smiley',
      'keywords'    => ['Agence', 'ho5', 'Blocks'],
      'useDefault'  => true, //If true, use default JS block, if false, block not registered automaticaly. In false case, developper need to write JS part of block declaration
      'styles'      => [
        /*['name' => 'default', 'label' => 'Normal', 'isDefault' => true],
      ['name' => 'fullwidth', 'label' => 'Contenu pleine largeur'],*/
      ],
      'attributes'  => [
        'alignment' => [
          'type'    => 'string',
          'default' => 'none',
        ],
      ],
      'example'     => [
        'attributes' => [
          'alignment' => 'center',
        ],
      ],
    ];
  }

  /**
   * Retrouve l'identifiant du Block
   */
  public static function getBlockID() {
    return \str_replace( '_', '-', static::BLOCK_GROUP . '/' . static::BLOCK_NAME );
  }

  /**
   * Retourne l'identifiant du script JS
   */
  public static function getScriptId() {
    return static::BLOCK_GROUP . '-scripts';
  }

  /**
   * Retourne l'identifiant du style csss
   */
  public static function getStyleId() {
    return static::BLOCK_GROUP . '-styles';
  }

  /**
   * Retourne le tableau des scripts requis pour charger un script
   */
  public static function getRequiredScripts() {
    return \apply_filters( static::BLOCK_GROUP . '_required_scripts', ['wp-blocks', 'wp-element', 'wp-editor', 'wp-data', 'wp-plugins', 'wp-edit-post', 'wp-components', 'lodash'] );
  }

  /**
   * Enregistre les scripts nécessaires pour le fonctionnement des blocks
   */
  public static function registerScripts() {
    wp_register_script( static::getScriptId(), plugins_url( 'dist/scripts/blocks.js', __FILE__ ), static::getRequiredScripts() );
    wp_register_style( static::getStyleId(), plugins_url( 'dist/styles/blocks.css', __FILE__ ) );
  }

  /**
   * Enregistre les variables nécessaires au fonctionnement du script par défault des blocks
   */
  public static function localizeScripts() {
    \wp_localize_script( static::getScriptId(), static::BLOCK_GROUP, static::$args );
  }

  /**
   * Déclare le block à faire fonctionner
   */
  public static function registerBlock( $args = [] ) {
    register_block_type( static::getBlockID(),
      array_merge( array(
        'attributes'      => static::$args[static::BLOCK_NAME]['args']['attributes'],
        'editor_script'   => static::getScriptId(),
        'editor_style'    => static::getStyleId(),
        'style'           => static::getStyleId(),
        'render_callback' => [\get_called_class(), 'render'],
      ), $args )
    );
  }

  public static function render( $attributes, $content ) {
    \ob_start();
    include static::$paths[get_called_class()] . '/view-' . static::BLOCK_NAME . '.php';
    return \ob_get_clean();
  }

  public static function block_categories( $categories, $post ) {
    foreach ( $categories as $cat ) {
      if ( $cat['slug'] == static::BLOCK_GROUP ) {
        return $categories;
      }
    }
    return array_merge(
      $categories,
      array(
        array(
          'slug'  => static::BLOCK_GROUP,
          'title' => __( 'Agence ho5', 'my-plugin' ),
          'icon'  => 'smiley',
        ),
      )
    );
  }
}
