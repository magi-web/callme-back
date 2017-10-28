<?php
if ( defined('ABSPATH') ) {
    /** Sets up WordPress vars and included files. */
    require_once( ABSPATH . '../wp-config.php' );
    require_once( ABSPATH . 'wp-settings.php' );
    require_once( ABSPATH . WPINC . '/class-wp-rewrite.php' );
    require_once( ABSPATH . WPINC . '/rewrite.php' );
    require_once( ABSPATH . WPINC . '/embed.php' );
    require_once( ABSPATH . WPINC . '/capabilities.php' );
    require_once( ABSPATH . WPINC . '/class-wp-oembed-controller.php' );
    require_once( ABSPATH . WPINC . '/class-wp-user.php' );
    require_once( ABSPATH . WPINC . '/http.php' );
    require_once( ABSPATH . WPINC . '/kses.php' );
    require_once( ABSPATH . WPINC . '/l10n.php' );
    require_once( ABSPATH . WPINC . '/link-template.php' );
    require_once( ABSPATH . WPINC . '/meta.php' );
    require_once( ABSPATH . WPINC . '/pluggable.php' );
    require_once( ABSPATH . WPINC . '/post.php' );
    require_once( ABSPATH . WPINC . '/post-formats.php' );
    require_once( ABSPATH . WPINC . '/taxonomy.php' );
    require_once( ABSPATH . WPINC . '/user.php' );
    require_once( ABSPATH . WPINC . '/class-wp-http-response.php' );
    require_once( ABSPATH . WPINC . '/rest-api.php' );
    require_once( ABSPATH . WPINC . '/rest-api/class-wp-rest-server.php' );
    require_once( ABSPATH . WPINC . '/rest-api/class-wp-rest-response.php' );
    require_once( ABSPATH . WPINC . '/rest-api/class-wp-rest-request.php' );
    require_once( ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-controller.php' );
    require_once( ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-posts-controller.php' );
    require_once( ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-attachments-controller.php' );
    require_once( ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-post-types-controller.php' );
    require_once( ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-post-statuses-controller.php' );
    require_once( ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-revisions-controller.php' );
    require_once( ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-taxonomies-controller.php' );
    require_once( ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-terms-controller.php' );
    require_once( ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-users-controller.php' );
    require_once( ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-comments-controller.php' );
    require_once( ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-settings-controller.php' );
    require_once( ABSPATH . WPINC . '/rest-api/fields/class-wp-rest-meta-fields.php' );
    require_once( ABSPATH . WPINC . '/rest-api/fields/class-wp-rest-comment-meta-fields.php' );
    require_once( ABSPATH . WPINC . '/rest-api/fields/class-wp-rest-post-meta-fields.php' );
    require_once( ABSPATH . WPINC . '/rest-api/fields/class-wp-rest-term-meta-fields.php' );
    require_once( ABSPATH . WPINC . '/rest-api/fields/class-wp-rest-user-meta-fields.php' );
    require_once( ABSPATH . WPINC . '/formatting.php' );
    require_once( ABSPATH . 'wp-content/plugins/callme-back/callme-back.php' );
}
