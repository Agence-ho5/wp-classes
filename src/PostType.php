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
class PostType {
  protected $post_type; // Identifiant du post type

  protected $title_placeholder = null;

  /**
   * Constructeur
   * @param post_type string  : Identifiant du post_type
   * @param title_placeholder : Définit le placeholder du champ titre pour ce post_type
   * @param args array        : Surcharge des arguments par défaut pour la déclaration du post type.
   * @param labels array      : Surcharge des labels par défaut pour la déclaration du post type.
   * @param caps array        : Surcharge des autorisation par défaut pour ce type de post
   */
  public function __construct( $post_type, $title_placeholder = null, $args = [], $labels = [], $caps = [] ) {
    $this->post_type = $post_type;
    $this->declare_post_type( $args, $labels );
    $this->set_caps( $caps );
    add_action( 'map_meta_cap', [$this, 'map_meta_cap'], 10, 4 );
    if ( $title_placeholder ) {
      $this->title_placeholder = $title_placeholder;
      add_filter( 'enter_title_here', [$this, 'change_title_placeholder'] );
    }
  }

  /**
   * Déclare un type de posts wordpress
   * @param args array   : Surcharge des arguments par défaut pour la déclaration du post type.
   * @param labels array : Surcharge des labels par défaut pour la déclaration du post type.
   * @return void        : Cette fonctionne ne retourne rien, le post_type est directement créé dans la fonction
   */
  protected function declare_post_type( $args = [], $labels = [] ) {
    $default_labels = array(
      'name'                  => _x( ucfirst( $this->post_type ) . 's', 'Post Type General Name', 'agenceho5' ),
      'singular_name'         => _x( ucfirst( $this->post_type ), 'Post Type Singular Name', 'agenceho5' ),
      'menu_name'             => __( ucfirst( $this->post_type ) . 's', 'agenceho5' ),
      'name_admin_bar'        => __( ucfirst( $this->post_type ) . 's', 'agenceho5' ),
      'archives'              => __( 'Tout voir', 'agenceho5' ),
      'attributes'            => __( 'Attributs', 'agenceho5' ),
      'parent_item_colon'     => __( ucfirst( $this->post_type ) . ' Parent(e)', 'agenceho5' ),
      'all_items'             => __( ucfirst( $this->post_type ) . 's', 'agenceho5' ),
      'add_new_item'          => __( 'Ajouter ' . $this->post_type, 'agenceho5' ),
      'add_new'               => __( 'Ajouter', 'agenceho5' ),
      'new_item'              => __( 'Nouveau', 'agenceho5' ),
      'edit_item'             => __( 'Modifier ' . $this->post_type, 'agenceho5' ),
      'update_item'           => __( 'Mettre à jour', 'agenceho5' ),
      'view_item'             => __( 'Voir', 'agenceho5' ),
      'view_items'            => __( 'Voir les ' . $this->post_type, 'agenceho5' ),
      'search_items'          => __( 'Rechercher ' . $this->post_type, 'agenceho5' ),
      'not_found'             => __( 'Aucun(e) ' . $this->post_type, 'agenceho5' ),
      'not_found_in_trash'    => __( 'Aucun(e) ' . $this->post_type . ' dans la corbeille', 'agenceho5' ),
      'featured_image'        => __( 'Image', 'agenceho5' ),
      'set_featured_image'    => __( 'Définir le l\'image', 'agenceho5' ),
      'remove_featured_image' => __( 'Retirer le l\'image', 'agenceho5' ),
      'use_featured_image'    => __( 'Définir un l\'image', 'agenceho5' ),
      'insert_into_item'      => __( 'Insérer', 'agenceho5' ),
      'uploaded_to_this_item' => __( 'Télécharger', 'agenceho5' ),
      'items_list'            => __( 'Liste des ' . $this->post_type, 'agenceho5' ),
      'items_list_navigation' => __( 'Items list navigation', 'agenceho5' ),
      'filter_items_list'     => __( 'Filter items list', 'agenceho5' ),
    );

    $labels = array_merge( $default_labels, $labels );

    $default_args = array(
      'label'               => __( ucfirst( $this->post_type ), 'agenceho5' ),
      'description'         => null,
      'labels'              => $labels,
      'supports'            => array( 'title', 'revisions', 'thumbnail', 'author', 'editor', 'page-attributes', 'excerpt' ),
      'hierarchical'        => false,
      'public'              => true,
      'show_ui'             => true,
      'show_in_menu'        => true,
      'menu_position'       => 22,
      'show_in_admin_bar'   => true,
      'show_in_nav_menus'   => true,
      'map_meta_cap'        => true,
      'can_export'          => true,
      'has_archive'         => false,
      'exclude_from_search' => false,
      'publicly_queryable'  => true,
      'capability_type'     => $this->post_type,
      'rewrite'             => ['with_front' => false, 'slug' => $this->post_type . 's'],
      'show_in_rest'        => true,
    );

    $args = array_merge( $default_args, $args );

    register_post_type( $this->post_type, $args );

  }

  /**
   * Définit les capacités et autorisations pour cet objet
   * @return void : Ne retourne aucune information, les capacitées sont directement appliquées
   */
  public function set_caps( $caps_overrite ) {
    Caps::add( array_merge( [
      'read_' . $this->post_type                   => ['administrator', 'editor', 'author', 'contributor', 'abonne'],
      'read_private_' . $this->post_type . 's'     => ['administrator', 'editor'],

      'edit_' . $this->post_type                   => ['administrator', 'editor'],
      'edit_' . $this->post_type . 's'             => ['administrator', 'editor'],
      'create_' . $this->post_type . 's'           => ['administrator', 'editor'],
      'edit_others_' . $this->post_type . 's'      => ['administrator', 'editor'],
      'edit_private_' . $this->post_type . 's'     => ['administrator', 'editor'],
      'edit_published_' . $this->post_type . 's'   => ['administrator', 'editor'],
      'publish_' . $this->post_type . 's'          => ['administrator', 'editor'],

      'delete_' . $this->post_type                 => ['administrator'],
      'delete_' . $this->post_type . 's'           => ['administrator'],
      'delete_private_' . $this->post_type . 's'   => ['administrator'],
      'delete_published_' . $this->post_type . 's' => ['administrator'],
      'delete_others_' . $this->post_type . 's'    => ['administrator'],
    ], $caps_overrite ) );
  }

  /**
   * Définit des autorisations personnalisées
   * @param array $caps      : Tableau des capacités avant filtrage, ce tableau doit être retourné une fois modifié
   * @param string $cap      : La capacité que l'on évalue dans le contexte actuel
   * @param int $user_id     : Utilisateur pour lequel on teste l'autorisation dans le contexte actuel
   * @param array $args      : Autres arguments
   *    -> $args[0]          : Post concerné par la recherche de capacités dans le contexte actuel
   * @return array $caps     : Capacités finales à appliquer dans le contexte actuel
   */
  public function map_meta_cap( $caps, $cap, $user_id, $args ) {
    // Ecriture
    if ( $cap == 'edit_' . $this->post_type ) {
      if ( empty( $args[0] ) || $user_id == get_post_field( 'post_author', $args[0] ) ) {
        $caps = 'edit_' . $this->post_type;
      } else {
        $caps = 'edit_others_' . $this->post_type . 's';
      }
    }

    if ( $cap == 'delete_' . $this->post_type . 's' ) {
      if ( empty( $args[0] ) || $user_id == get_post_field( 'post_author', $args[0] ) ) {
        $caps = 'delete_' . $this->post_type . 's';
      } else {
        $caps = 'delete_others_' . $this->post_type . 's';
      }
    }
    return $caps;
  }

  /**
   * Définit le placeholder du champ titre si ce dernier est définit
   * @param string $title : Nouveau placeholder à appliquer
   * @return $title       : Retourne le placeholder modifié
   */
  public function change_title_placeholder( $title ) {
    $screen = get_current_screen();

    if ( $this->post_type == $screen->post_type && !empty( $this->title_placeholder ) ) {
      $title = $this->title_placeholder;
    }

    return $title;
  }

  /**
   * Retourne l'identifiant du post type courant de l'instance
   * @return string : l'identifiant du post type
   */
  public function getKey() {
    return $this->post_type;
  }

  /**
   * Retourne l'étiquette du post type
   * @param boolean $plural : si true, retourne l'étiquette au pluriel, au singulier si false
   * @return string : l'étiquette du post type
   */
  public function getLabel( $plural = false ) {
    if ( $plural ) {
      return get_post_type_object( $this->post_type )->labels->name;
    }

    return get_post_type_object( $this->post_type )->labels->singular_name;
  }

}