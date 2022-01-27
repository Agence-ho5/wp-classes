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
 * Classe pour faciliter la création de Post Type personnalisés
 */
class Taxonomy {
  protected $taxonomy; // Identifiant de la taxonomy
  protected $singular_name; // Singular name de la taxonomy, Par défaut, identique à $taxonomy
  protected $postTypes; // postTypes auxquels cette taxonomie doit être rattachée

  /**
   * Constructeur
   * @see https://developer.wordpress.org/reference/functions/register_taxonomy/
   * @param taxonomy string     : Identifiant du taxonomy
   * @param postTypes array     : Tableau des postTypes auxquels cette taxonomie doit être rattachée
   * @param args array          : Surcharge des arguments par défaut pour la déclaration de la taxonomy.
   * @param labels array|string : Surcharge des labels par défaut pour la déclaration de la taxonomy. Accepte soit un tableau au format préconiser pour les labels, soit une chaîne de caractères au singulier qui sera automatiquement adaptée.
   * @param caps array          : Surcharge des autorisation par défaut pour ce type de post
   */
  public function __construct( $taxonomy, $postTypes, $args = [], $labels = [], $caps = [] ) {
    $this->taxonomy      = $taxonomy;
    $this->singular_name = \is_string( $labels ) ? $labels : $taxonomy;
    $this->postTypes     = $postTypes;
    $this->declare_taxonomy( $args, is_array( $labels ) ? $labels : [] );
    $this->set_caps( $caps );
  }

  /**
   * Déclare un type de posts wordpress
   * @param args array   : Surcharge des arguments par défaut pour la déclaration de la taxonomy.
   * @param labels array : Surcharge des labels par défaut pour la déclaration de la taxonomy.
   * @return void        : Cette fonctionne ne retourne rien, le taxonomy est directement créé dans la fonction
   */
  protected function declare_taxonomy( $args = [], $labels = [] ) {
    $default_labels = array(
      'name'                       => _x( ucfirst( $this->singular_name ) . 's', 'Catégorie', 'text_domain' ),
      'singular_name'              => _x( ucfirst( $this->singular_name ), 'Catégorie', 'text_domain' ),
      'menu_name'                  => __( ucfirst( $this->singular_name ) . 's', 'text_domain' ),
      'parent_item'                => __( ucfirst( $this->singular_name ) . ' Parent(e)', 'agenceho5' ),
      'parent_item_colon'          => __( ucfirst( $this->singular_name ) . ' Parent(e)', 'agenceho5' ),
      'all_items'                  => __( ucfirst( $this->singular_name ) . 's', 'agenceho5' ),
      'add_new_item'               => __( 'Ajouter ' . $this->singular_name, 'agenceho5' ),
      'new_item_name'              => __( 'Nouveau', 'agenceho5' ),
      'edit_item'                  => __( 'Modifier ' . $this->singular_name, 'agenceho5' ),
      'update_item'                => __( 'Mettre à jour', 'agenceho5' ),
      'view_item'                  => __( 'Voir', 'agenceho5' ),
      'search_items'               => __( 'Rechercher ' . $this->singular_name, 'agenceho5' ),
      'not_found'                  => __( 'Aucun(e) ' . $this->singular_name, 'agenceho5' ),
      'not_found'                  => __( 'Aucun(e) ' . $this->singular_name, 'agenceho5' ),
      'items_list'                 => __( 'Liste des ' . $this->singular_name, 'agenceho5' ),
      'items_list_navigation'      => __( 'Items list navigation', 'agenceho5' ),
      'separate_items_with_commas' => __( 'Séparer les ' . ucfirst( $this->singular_name ) . 's par des virgules', 'agenceho5' ),
      'add_or_remove_items'        => __( 'Ajouter ou supprimer des ' . ucfirst( $this->singular_name ) . 's par des virgules', 'agenceho5' ),
      'choose_from_most_used'      => __( 'Choisir parmis les plus utilisé(e)s', 'agenceho5' ),
      'popular_items'              => __( ucfirst( $this->singular_name ) . 's populaires', 'agenceho5' ),
    );

    $labels = array_merge( $default_labels, $labels );

    $default_args = array(
      'labels'             => $labels,
      'hierarchical'       => false,
      'public'             => true,
      'publicly_queryable' => true,
      'show_ui'            => true,
      'show_admin_column'  => true,
      'show_in_nav_menus'  => true,
      'show_tagcloud'      => true,
      'show_in_quick_edit' => true,
      'show_in_rest'       => true,
      'capabilities'       => array(
        'manage_terms' => 'manage_' . $this->taxonomy,
        'edit_terms'   => 'manage_' . $this->taxonomy,
        'delete_terms' => 'manage_' . $this->taxonomy,
        'assign_terms' => 'edit_posts',
      ),
      'rewrite'            => ['with_front' => false, 'slug' => $this->taxonomy . 's'],
      'show_in_rest'       => true,
    );

    $args = array_merge( $default_args, $args );

    register_taxonomy( $this->taxonomy, $this->postTypes, $args );

  }

  /**
   * Définit les capacités et autorisations pour cet objet
   * @return void : Ne retourne aucune information, les capacitées sont directement appliquées
   */
  public function set_caps( $caps_overrite ) {
    Caps::add( array_merge( [
      'manage_' . $this->taxonomy => ['administrator', 'editor'],
    ], $caps_overrite ) );
  }

  /**
   * Retourne l'identifiant de la taxonomy courant de l'instance
   * @return string : l'identifiant de la taxonomy
   */
  public function getKey() {
    return $this->taxonomy;
  }

  /**
   * Retourne l'étiquette de la taxonomy
   * @param boolean $plural : si true, retourne l'étiquette au pluriel, au singulier si false
   * @return string : l'étiquette de la taxonomy
   */
  public function getLabel( $plural = false ) {
    if ( $plural ) {
      return get_taxonomy( $this->taxonomy )->labels->name;
    }

    return get_taxonomy( $this->taxonomy )->labels->singular_name;
  }

}

