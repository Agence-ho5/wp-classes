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
 * Permet de gérer les autorisations relatives à ce module (capcités)
 */
class Caps{

  protected static $instance;

  protected static $caps = [];

  protected static $caps_mapping = [];

  /**
   * Constructeur
   */
  public function __construct(){
    add_action( 'admin_init', [ $this, 'process' ], 15 );
    add_filter( 'map_meta_cap', [ $this, 'map_meta_cap' ], 5, 4 );
  }

  /**
   * Retourne ou créer l'instance de cette classe
   */
  public static function getInstance(){
    if(empty(self::$instance)){
      self::$instance = new self();
    }
    return self::$instance;
  }

  /**
   * Ajoute une capacité à un ou plusieurs utilisateurs
   * @param array $caps : Capacités à ajouter et roles pour lesquels ouvrir l'accès au format [ 'nom_capacite' => ['role_allowed_1', 'role_allowed_2', ...] ]
   */
  public static function add($caps = []){
    self::$caps = array_merge(self::$caps, $caps);
    foreach(array_keys($caps) as $cap){
      if(!isset(self::$caps_mapping[$cap])){
        self::$caps_mapping[$cap] = [$cap];
      }
    }
  }

  /**
   * Ajoute un mapping personnalisé à une capacité
   * @param array $mapping : Tableau des équivalence de capacité sous la forme [ 'searched_cap' => ['allowed_cap_1', 'allowed_cap_2']]
   */
  public static function custom_mapping($mapping){
    self::$caps_mapping = array_merge(self::$caps_mapping, $mapping);
  }

  /**
   * Installe les autorisations dans Wordpress
   */
  public function process(){
    global $wp_roles;
    if ( ! isset( $wp_roles ) )
    $wp_roles = new WP_Roles();

    foreach (array_keys($wp_roles->roles) as $role_id) {
      $role = get_role($role_id);
      foreach (self::$caps as $cap => $users) {
        if(in_array($role_id, $users))
          $role->add_cap($cap, true);
        else
          $role->remove_cap($cap);
      }
    }
  }

  /**
   * Installe les équivalences de capacité dans wordpress
   * @param array $caps      : Tableau des capacités avant filtrage, ce tableau doit être retourné une fois modifié
   * @param string $cap      : La capacité que l'on évalue dans le contexte actuel
   * @param int $user_id     : Utilisateur pour lequel on teste l'autorisation dans le contexte actuel
   * @param array $args      : Autres arguments
   *    -> $args[0]          : Post concerné par la recherche de capacités dans le contexte actuel
   * @return array $caps     : Capacités finales à appliquer dans le contexte actuel
   */
  function map_meta_cap($caps, $cap, $user_id, $args){
    if(in_array($cap, array_keys(self::$caps_mapping))){
      $caps = self::$caps_mapping[$cap];
    }
    return $caps;
  }

}
Caps::getInstance();