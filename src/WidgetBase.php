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

use \Elementor\Widget_Base;

/**
 * Classe de base pour la création d'un widget elementor sur mesure.
 * Cette classe part du principe que la catégorie 'nsi' a déjà été créée par le plugin nsi-elementor
 */
abstract class WidgetBase extends Widget_Base {

/**
 * Get categories
 *
 * @since 0.0.1
 */
  public function get_categories() {
    return ['nsi'];
  }
}