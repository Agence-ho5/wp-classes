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
 * Commun à tous les blocks compatibles Gutenberg
 */
abstract class Block extends Singleton
{
  const BLOCK_NAME = 'undefined';

  /**
   * Constructor
   * Define default filters & actions
   */
  protected function __construct(){
    add_action( 'carbon_fields_register_fields', [$this, 'registerBlock'] );
    add_action( 'after_setup_theme', function(){ \Carbon_Fields\Carbon_Fields::boot(); } );
  }

  /**
   * Enregistre le block pour qu'il apparaisse dans Gutenberg
   */
  public static function registerBlock() { }

  /**
   * Retourne l'ensemble des champs du Block (pour sa gestion dans gutenberg)
   * @see hook BLOCK_NAME-fields
   * @param array $fields : existing fields
   * @return array : fields modified or created
   */
  public static function getFields($fields = []){
    return apply_filters(static::BLOCK_NAME.'-fields', $fields);
  }

  /**
   * Return rendered block className.
   * this classname can be edited via this filter :
   * @see hook BLOCK_NAME-classname
   * @param array $fields       : Fields value list
   * @param array attributes    : attributes lists (like className)
   * @param array inner_blocks  : Inner blocks in case the block can contain other blocks
   * @return string : ClassName of this block
   */
  public static function getClassName($attributes = null, $fields = null, $inner_blocks = null){
    $className = static::BLOCK_NAME.' ';
    if(!empty($attributes['className'])){
      $className .= $attributes['className'];
    }
    return apply_filters(static::BLOCK_NAME.'-classname', $className, $attributes, $fields, $inner_blocks);
  }

  /**
   * Render the block on front page (or in admin in viewer mode)
   * this render can be updated by remplacing this function by a new callback to call in this filter :
   * @see hoohk nsi_slider-render
   * @param array $fields       : Fields value list
   * @param array attributes    : attributes lists (like className)
   * @param array inner_blocks  : Inner blocks in case the block can contain other blocks
   */
  public static function renderBlock( $fields, $attributes, $inner_blocks ){ }

  /**
   * Render the block programaticaly (not from gutenberg page)
   */
  public static function render(){ }
}