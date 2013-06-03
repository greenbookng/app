<?php
/**
 * XML-RPC protocol support for WordPress
 *
 * @package WordPress
 */

/**
 * WordPress XMLRPC server implementation.
 *
 * Implements compatibility for Blogger API, MetaWeblog API, MovableType, and
 * pingback. Additional WordPress API for managing comments, pages, posts,
 * options, etc.
 *
 * Since WordPress 2.6.0, WordPress XMLRPC server can be disabled in the
 * administration panels.
 *
 * @package WordPress
 * @subpackage Publishing
 * @since 1.5.0
 */
class wp_xmlrpc_server extends IXR_Server {

	/**
	 * Register all of the XMLRPC methods that XMLRPC server understands.
	 *
	 * Sets up server and method property. Passes XMLRPC
	 * methods through the 'xmlrpc_methods' filter to allow plugins to extend
	 * or replace XMLRPC methods.
	 *
	 * @since 1.5.0
	 *
	 * @return wp_xmlrpc_server
	 */
	function __construct() {
		$this->methods = array(
			// WordPress API
			'wp.getUsersBlogs'		=> 'this:wp_getUsersBlogs',
			'wp.newPost'			=> 'this:wp_newPost',
			'wp.editPost'			=> 'this:wp_editPost',
			'wp.deletePost'			=> 'this:wp_deletePost',
			'wp.getPost'			=> 'this:wp_getPost',
			'wp.getPosts'			=> 'this:wp_getPosts',
			'wp.newTerm'			=> 'this:wp_newTerm',
			'wp.editTerm'			=> 'this:wp_editTerm',
			'wp.deleteTerm'			=> 'this:wp_deleteTerm',
			'wp.getTerm'			=> 'this:wp_getTerm',
			'wp.getTerms'			=> 'this:wp_getTerms',
			'wp.getTaxonomy'		=> 'this:wp_getTaxonomy',
			'wp.getTaxonomies'		=> 'this:wp_getTaxonomies',
			'wp.getUser'			=> 'this:wp_getUser',
			'wp.getUsers'			=> 'this:wp_getUsers',
			'wp.getProfile'			=> 'this:wp_getProfile',
			'wp.editProfile'		=> 'this:wp_editProfile',
			'wp.getPage'			=> 'this:wp_getPage',
			'wp.getPages'			=> 'this:wp_getPages',
			'wp.newPage'			=> 'this:wp_newPage',
			'wp.deletePage'			=> 'this:wp_deletePage',
			'wp.editPage'			=> 'this:wp_editPage',
			'wp.getPageList'		=> 'this:wp_getPageList',
			'wp.getAuthors'			=> 'this:wp_getAuthors',
			'wp.getCategories'		=> 'this:mw_getCategories',		// Alias
			'wp.getTags'			=> 'this:wp_getTags',
			'wp.newCategory'		=> 'this:wp_newCategory',
			'wp.deleteCategory'		=> 'this:wp_deleteCategory',
			'wp.suggestCategories'	=> 'this:wp_suggestCategories',
			'wp.uploadFile'			=> 'this:mw_newMediaObject',	// Alias
			'wp.getCommentCount'	=> 'this:wp_getCommentCount',
			'wp.getPostStatusList'	=> 'this:wp_getPostStatusList',
			'wp.getPageStatusList'	=> 'this:wp_getPageStatusList',
			'wp.getPageTemplates'	=> 'this:wp_getPageTemplates',
			'wp.getOptions'			=> 'this:wp_getOptions',
			'wp.setOptions'			=> 'this:wp_setOptions',
			'wp.getComment'			=> 'this:wp_getComment',
			'wp.getComments'		=> 'this:wp_getComments',
			'wp.deleteComment'		=> 'this:wp_deleteComment',
			'wp.editComment'		=> 'this:wp_editComment',
			'wp.newComment'			=> 'this:wp_newComment',
			'wp.getCommentStatusList' => 'this:wp_getCommentStatusList',
			'wp.getMediaItem'		=> 'this:wp_getMediaItem',
			'wp.getMediaLibrary'	=> 'this:wp_getMediaLibrary',
			'wp.getPostFormats'     => 'this:wp_getPostFormats',
			'wp.getPostType'		=> 'this:wp_getPostType',
			'wp.getPostTypes'		=> 'this:wp_getPostTypes',
			'wp.getRevisions'		=> 'this:wp_getRevisions',
			'wp.restoreRevision'	=> 'this:wp_restoreRevision',

			// Blogger API
			'blogger.getUsersBlogs' => 'this:blogger_getUsersBlogs',
			'blogger.getUserInfo' => 'this:blogger_getUserInfo',
			'blogger.getPost' => 'this:blogger_getPost',
			'blogger.getRecentPosts' => 'this:blogger_getRecentPosts',
			'blogger.newPost' => 'this:blogger_newPost',
			'blogger.editPost' => 'this:blogger_editPost',
			'blogger.deletePost' => 'this:blogger_deletePost',

			// MetaWeblog API (with MT extensions to structs)
			'metaWeblog.newPost' => 'this:mw_newPost',
			'metaWeblog.editPost' => 'this:mw_editPost',
			'metaWeblog.getPost' => 'this:mw_getPost',
			'metaWeblog.getRecentPosts' => 'this:mw_getRecentPosts',
			'metaWeblog.getCategories' => 'this:mw_getCategories',
			'metaWeblog.newMediaObject' => 'this:mw_newMediaObject',

			// MetaWeblog API aliases for Blogger API
			// see http://www.xmlrpc.com/stories/storyReader$2460
			'metaWeblog.deletePost' => 'this:blogger_deletePost',
			'metaWeblog.getUsersBlogs' => 'this:blogger_getUsersBlogs',

			// MovableType API
			'mt.getCategoryList' => 'this:mt_getCategoryList',
			'mt.getRecentPostTitles' => 'this:mt_getRecentPostTitles',
			'mt.getPostCategories' => 'this:mt_getPostCategories',
			'mt.setPostCategories' => 'this:mt_setPostCategories',
			'mt.supportedMethods' => 'this:mt_supportedMethods',
			'mt.supportedTextFilters' => 'this:mt_supportedTextFilters',
			'mt.getTrackbackPings' => 'this:mt_getTrackbackPings',
			'mt.publishPost' => 'this:mt_publishPost',

			// PingBack
			'pingback.ping' => 'this:pingback_ping',
			'pingback.extensions.getPingbacks' => 'this:pingback_extensions_getPingbacks',

			'demo.sayHello' => 'this:sayHello',
			'demo.addTwoNumbers' => 'this:addTwoNumbers'
		);

		$this->initialise_blog_option_info();
		$this->methods = apply_filters('xmlrpc_methods', $this->methods);
	}

	function serve_request() {
		$this->IXR_Server($this->methods);
	}

	/**
	 * Test XMLRPC API by saying, "Hello!" to client.
	 *
	 * @since 1.5.0
	 *
	 * @param array $args Method Parameters.
	 * @return string
	 */
	function sayHello($args) {
		return 'Hello!';
	}

	/**
	 * Test XMLRPC API by adding two numbers for client.
	 *
	 * @since 1.5.0
	 *
	 * @param array $args Method Parameters.
	 * @return int
	 */
	function addTwoNumbers($args) {
		$number1 = $args[0];
		$number2 = $args[1];
		return $number1 + $number2;
	}

	/**
	 * Log user in.
	 *
	 * @since 2.8.0
	 *
	 * @param string $username User's username.
	 * @param string $password User's password.
	 * @return mixed WP_User object if authentication passed, false otherwise
	 */
	function login( $username, $password ) {
		// Respect any old filters against get_option() for 'enable_xmlrpc'.
		$enabled = apply_filters( 'pre_option_enable_xmlrpc', false ); // Deprecated
		if ( false === $enabled )
			$enabled = apply_filters( 'option_enable_xmlrpc', true ); // Deprecated

		// Proper filter for turning off XML-RPC. It is on by default.
		$enabled = apply_filters( 'xmlrpc_enabled', $enabled );

		if ( ! $enabled ) {
			$this->error = new IXR_Error( 405, sprintf( __( 'XML-RPC services are disabled on this site.' ) ) );
			return false;
		}

		$user = wp_authenticate($username, $password);

		if (is_wp_error($user)) {
			$this->error = new IXR_Error( 403, __( 'Incorrect username or password.' ) );
			$this->error = apply_filters( 'xmlrpc_login_error', $this->error, $user );
			return false;
		}

		wp_set_current_user( $user->ID );
		return $user;
	}

	/**
	 * Check user's credentials. Deprecated.
	 *
	 * @since 1.5.0
	 * @deprecated 2.8.0
	 * @deprecated use wp_xmlrpc_server::login
	 * @see wp_xmlrpc_server::login
	 *
	 * @param string $username User's username.
	 * @param string $password User's password.
	 * @return bool Whether authentication passed.
	 */
	function login_pass_ok( $username, $password ) {
		return (bool) $this->login( $username, $password );
	}

	/**
	 * Sanitize string or array of strings for database.
	 *
	 * @since 1.5.2
	 *
	 * @param string|array $array Sanitize single string or array of strings.
	 * @return string|array Type matches $array and sanitized for the database.
	 */
	function escape(&$array) {
		global $wpdb;

		if (!is_array($array)) {
			return($wpdb->escape($array));
		} else {
			foreach ( (array) $array as $k => $v ) {
				if ( is_array($v) ) {
					$this->escape($array[$k]);
				} else if ( is_object($v) ) {
					//skip
				} else {
					$array[$k] = $wpdb->escape($v);
				}
			}
		}
	}

	/**
	 * Retrieve custom fields for post.
	 *
	 * @since 2.5.0
	 *
	 * @param int $post_id Post ID.
	 * @return array Custom fields, if exist.
	 */
	function get_custom_fields($post_id) {
		$post_id = (int) $post_id;

		$custom_fields = array();

		foreach ( (array) has_meta($post_id) as $meta ) {
			// Don't expose protected fields.
			if ( ! current_user_can( 'edit_post_meta', $post_id , $meta['meta_key'] ) )
				continue;

			$custom_fields[] = array(
				"id"    => $meta['meta_id'],
				"key"   => $meta['meta_key'],
				"value" => $meta['meta_value']
			);
		}

		return $custom_fields;
	}

	/**
	 * Set custom fields for post.
	 *
	 * @since 2.5.0
	 *
	 * @param int $post_id Post ID.
	 * @param array $fields Custom fields.
	 */
	function set_custom_fields($post_id, $fields) {
		$post_id = (int) $post_id;

		foreach ( (array) $fields as $meta ) {
			if ( isset($meta['id']) ) {
				$meta['id'] = (int) $meta['id'];
				$pmeta = get_metadata_by_mid( 'post', $meta['id'] );
				if ( isset($meta['key']) ) {
					$meta['key'] = stripslashes( $meta['key'] );
					if ( $meta['key'] != $pmeta->meta_key )
						continue;
					$meta['value'] = stripslashes_deep( $meta['value'] );
					if ( current_user_can( 'edit_post_meta', $post_id, $meta['key'] ) )
						update_metadata_by_mid( 'post', $meta['id'], $meta['value'] );
				} elseif ( current_user_can( 'delete_post_meta', $post_id, $pmeta->meta_key ) ) {
					delete_metadata_by_mid( 'post', $meta['id'] );
				}
			} elseif ( current_user_can( 'add_post_meta', $post_id, stripslashes( $meta['key'] ) ) ) {
				add_post_meta( $post_id, $meta['key'], $meta['value'] );
			}
		}
	}

	/**
	 * Set up blog options property.
	 *
	 * Passes property through 'xmlrpc_blog_options' filter.
	 *
	 * @since 2.6.0
	 */
	function initialise_blog_option_info() {
		global $wp_version;

		$this->blog_options = array(
			// Read only options
			'software_name'     => array(
				'desc'          => __( 'Software Name' ),
				'readonly'      => true,
				'value'         => 'WordPress'
			),
			'software_version'  => array(
				'desc'          => __( 'Software Version' ),
				'readonly'      => true,
				'value'         => $wp_version
			),
			'blog_url'          => array(
				'desc'          => __( 'Site URL' ),
				'readonly'      => true,
				'option'        => 'siteurl'
			),
			'home_url'          => array(
				'desc'          => __( 'Home URL' ),
				'readonly'      => true,
				'option'        => 'home'
			),
			'image_default_link_type' => array(
				'desc'          => __( 'Image default link type' ),
				'readonly'      => true,
				'option'        => 'image_default_link_type'
			),
			'image_default_size' => array(
				'desc'          => __( 'Image default size' ),
				'readonly'      => true,
				'option'        => 'image_default_size'
			),
			'image_default_align' => array(
				'desc'          => __( 'Image default align' ),
				'readonly'      => true,
				'option'        => 'image_default_align'
			),
			'template'          => array(
				'desc'          => __( 'Template' ),
				'readonly'      => true,
				'option'        => 'template'
			),
			'stylesheet'        => array(
				'desc'          => __( 'Stylesheet' ),
				'readonly'      => true,
				'option'        => 'stylesheet'
			),
			'post_thumbnail'    => array(
				'desc'          => __('Post Thumbnail'),
				'readonly'      => true,
				'value'         => current_theme_supports( 'post-thumbnails' )
			),

			// Updatable options
			'time_zone'         => array(
				'desc'          => __( 'Time Zone' ),
				'readonly'      => false,
				'option'        => 'gmt_offset'
			),
			'blog_title'        => array(
				'desc'          => __( 'Site Title' ),
				'readonly'      => false,
				'option'        => 'blogname'
			),
			'blog_tagline'      => array(
				'desc'          => __( 'Site Tagline' ),
				'readonly'      => false,
				'option'        => 'blogdescription'
			),
			'date_format'       => array(
				'desc'          => __( 'Date Format' ),
				'readonly'      => false,
				'option'        => 'date_format'
			),
			'time_format'       => array(
				'desc'          => __( 'Time Format' ),
				'readonly'      => false,
				'option'        => 'time_format'
			),
			'users_can_register' => array(
				'desc'          => __( 'Allow new users to sign up' ),
				'readonly'      => false,
				'option'        => 'users_can_register'
			),
			'thumbnail_size_w'  => array(
				'desc'          => __( 'Thumbnail Width' ),
				'readonly'      => false,
				'option'        => 'thumbnail_size_w'
			),
			'thumbnail_size_h'  => array(
				'desc'          => __( 'Thumbnail Height' ),
				'readonly'      => false,
				'option'        => 'thumbnail_size_h'
			),
			'thumbnail_crop'    => array(
				'desc'          => __( 'Crop thumbnail to exact dimensions' ),
				'readonly'      => false,
				'option'        => 'thumbnail_crop'
			),
			'medium_size_w'     => array(
				'desc'          => __( 'Medium size image width' ),
				'readonly'      => false,
				'option'        => 'medium_size_w'
			),
			'medium_size_h'     => array(
				'desc'          => __( 'Medium size image height' ),
				'readonly'      => false,
				'option'        => 'medium_size_h'
			),
			'large_size_w'      => array(
				'desc'          => __( 'Large size image width' ),
				'readonly'      => false,
				'option'        => 'large_size_w'
			),
			'large_size_h'      => array(
				'desc'          => __( 'Large size image height' ),
				'readonly'      => false,
				'option'        => 'large_size_h'
			),
			'default_comment_status' => array(
				'desc'          => __( 'Allow people to post comments on new articles' ),
				'readonly'      => false,
				'option'        => 'default_comment_status'
			),
			'default_ping_status' => array(
				'desc'          => __( 'Allow link notifications from other blogs (pingbacks and trackbacks)' ),
				'readonly'      => false,
				'option'        => 'default_ping_status'
			)
		);

		$this->blog_options = apply_filters( 'xmlrpc_blog_options', $this->blog_options );
	}

	/**
	 * Retrieve the blogs of the user.
	 *
	 * @since 2.6.0
	 *
	 * @param array $args Method parameters. Contains:
	 *  - username
	 *  - password
	 * @return array. Contains:
	 *  - 'isAdmin'
	 *  - 'url'
	 *  - 'blogid'
	 *  - 'blogName'
	 *  - 'xmlrpc' - url of xmlrpc endpoint
	 */
	function wp_getUsersBlogs( $args ) {
		global $current_site;
		// If this isn't on WPMU then just use blogger_getUsersBlogs
		if ( !is_multisite() ) {
			array_unshift( $args, 1 );
			return $this->blogger_getUsersBlogs( $args );
		}

		$this->escape( $args );

		$username = $args[0];
		$password = $args[1];

		if ( !$user = $this->login($username, $password) )
			return $this->error;

		do_action( 'xmlrpc_call', 'wp.getUsersBlogs' );

		$blogs = (array) get_blogs_of_user( $user->ID );
		$struct = array();

		foreach ( $blogs as $blog ) {
			// Don't include blogs that aren't hosted at this site
			if ( $blog->site_id != $current_site->id )
				continue;

			$blog_id = $blog->userblog_id;

			switch_to_blog( $blog_id );

			$is_admin = current_user_can( 'manage_options' );

			$struct[] = array(
				'isAdmin'		=> $is_admin,
				'url'			=> home_url( '/' ),
				'blogid'		=> (string) $blog_id,
				'blogName'		=> get_option( 'blogname' ),
				'xmlrpc'		=> site_url( 'xmlrpc.php', 'rpc' ),
			);

			restore_current_blog();
		}

		return $struct;
	}

	/**
	 * Checks if the method received at least the minimum number of arguments.
	 *
	 * @since 3.4.0
	 *
	 * @param string|array $args Sanitize single string or array of strings.
	 * @param int $count Minimum number of arguments.
	 * @return boolean if $args contains at least $count arguments.
	 */
	protected function minimum_args( $args, $count ) {
		if ( count( $args ) < $count ) {
			$this->error = new IXR_Error( 400, __( 'Insufficient arguments passed to this XML-RPC method.' ) );
			return false;
		}

		return true;
	}

	/**
	 * Prepares taxonomy data for return in an XML-RPC object.
	 *
	 * @access protected
	 *
	 * @param object $taxonomy The unprepared taxonomy data
	 * @param array $fields The subset of taxonomy fields to return
	 * @return array The prepared taxonomy data
	 */
	protected function _prepare_taxonomy( $taxonomy, $fields ) {
		$_taxonomy = array(
			'name' => $taxonomy->name,
			'label' => $taxonomy->label,
			'hierarchical' => (bool) $taxonomy->hierarchical,
			'public' => (bool) $taxonomy->public,
			'show_ui' => (bool) $taxonomy->show_ui,
			'_builtin' => (bool) $taxonomy->_builtin,
		);

		if ( in_array( 'labels', $fields ) )
			$_taxonomy['labels'] = (array) $taxonomy->labels;

		if ( in_array( 'cap', $fields ) )
			$_taxonomy['cap'] = (array) $taxonomy->cap;

		if ( in_array( 'object_type', $fields ) )
			$_taxonomy['object_type'] = array_unique( (array) $taxonomy->object_type );

		return apply_filters( 'xmlrpc_prepare_taxonomy', $_taxonomy, $taxonomy, $fields );
	}

	/**
	 * Prepares term data for return in an XML-RPC object.
	 *
	 * @access protected
	 *
	 * @param array|object $term The unprepared term data
	 * @return array The prepared term data
	 */
	protected function _prepare_term( $term ) {
		$_term = $term;
		if ( ! is_array( $_term) )
			$_term = get_object_vars( $_term );

		// For Intergers which may be largeer than XMLRPC supports ensure we return strings.
		$_term['term_id'] = strval( $_term['term_id'] );
		$_term['term_group'] = strval( $_term['term_group'] );
		$_term['term_taxonomy_id'] = strval( $_term['term_taxonomy_id'] );
		$_term['parent'] = strval( $_term['parent'] );

		// Count we are happy to return as an Integer because people really shouldn't use Terms that much.
		$_term['count'] = intval( $_term['count'] );

		return apply_filters( 'xmlrpc_prepare_term', $_term, $term );
	}

	/**
	 * Convert a WordPress date string to an IXR_Date object.
	 *
	 * @access protected
	 *
	 * @param string $date
	 * @return IXR_Date
	 */
	protected function _convert_date( $date ) {
		if ( $date === '0000-00-00 00:00:00' ) {
			return new IXR_Date( '00000000T00:00:00Z' );
		}
		return new IXR_Date( mysql2date( 'Ymd\TH:i:s', $date, false ) );
	}

	/**
	 * Convert a WordPress GMT date string to an IXR_Date object.
	 *
	 * @access protected
	 *
	 * @param string $date_gmt
	 * @param string $date
	 * @return IXR_Date
	 */
	protected function _convert_date_gmt( $date_gmt, $date ) {
		if ( $date !== '0000-00-00 00:00:00' && $date_gmt === '0000-00-00 00:00:00' ) {
			return new IXR_Date( get_gmt_from_date( mysql2date( 'Y-m-d H:i:s', $date, false ), 'Ymd\TH:i:s' ) );
		}
		return $this->_convert_date( $date_gmt );
	}

	/**
	 * Prepares post data for return in an XML-RPC object.
	 *
	 * @access protected
	 *
	 * @param array $post The unprepared post data
	 * @param array $fields The subset of post type fields to return
	 * @return array The prepared post data
	 */
	protected function _prepare_post( $post, $fields ) {
		// holds the data for this post. built up based on $fields
		$_post = array( 'post_id' => strval( $post['ID'] ) );

		// prepare common post fields
		$post_fields = array(
			'post_title'        => $post['post_title'],
			'post_date'         => $this->_convert_date( $post['post_date'] ),
			'post_date_gmt'     => $this->_convert_date_gmt( $post['post_date_gmt'], $post['post_date'] ),
			'post_modified'     => $this->_convert_date( $post['post_modified'] ),
			'post_modified_gmt' => $this->_convert_date_gmt( $post['post_modified_gmt'], $post['post_modified'] ),
			'post_status'       => $post['post_status'],
			'post_type'         => $post['post_type'],
			'post_name'         => $post['post_name'],
			'post_author'       => $post['post_author'],
			'post_password'     => $post['post_password'],
			'post_excerpt'      => $post['post_excerpt'],
			'post_content'      => $post['post_content'],
			'post_parent'       => strval( $post['post_parent'] ),
			'post_mime_type'    => $post['post_mime_type'],
			'link'              => post_permalink( $post['ID'] ),
			'guid'              => $post['guid'],
			'menu_order'        => intval( $post['menu_order'] ),
			'comment_status'    => $post['comment_status'],
			'ping_status'       => $post['ping_status'],
			'sticky'            => ( $post['post_type'] === 'post' && is_sticky( $post['ID'] ) ),
		);

		// Thumbnail
		$post_fields['post_thumbnail'] = array();
		$thumbnail_id = get_post_thumbnail_id( $post['ID'] );
		if ( $thumbnail_id ) {
			$thumbnail_size = current_theme_supports('post-thumbnail') ? 'post-thumbnail' : 'thumbnail';
			$post_fields['post_thumbnail'] = $this->_prepare_media_item( get_post( $thumbnail_id ), $thumbnail_size );
		}

		// Consider future posts as published
		if ( $post_fields['post_status'] === 'future' )
			$post_fields['post_status'] = 'publish';

		// Fill in blank post format
		$post_fields['post_format'] = get_post_format( $post['ID'] );
		if ( empty( $post_fields['post_format'] ) )
			$post_fields['post_format'] = 'standard';

		// Merge requested $post_fields fields into $_post
		if ( in_array( 'post', $fields ) ) {
			$_post = array_merge( $_post, $post_fields );
		} else {
			$requested_fields = array_intersect_key( $post_fields, array_flip( $fields ) );
			$_post = array_merge( $_post, $requested_fields );
		}

		$all_taxonomy_fields = in_array( 'taxonomies', $fields );

		if ( $all_taxonomy_fields || in_array( 'terms', $fields ) ) {
			$post_type_taxonomies = get_object_taxonomies( $post['post_type'], 'names' );
			$terms = wp_get_object_terms( $post['ID'], $post_type_taxonomies );
			$_post['terms'] = array();
			foreach ( $terms as $term ) {
				$_post['terms'][] = $this->_prepare_term( $term );
			}
		}

		if ( in_array( 'custom_fields', $fields ) )
			$_post['custom_fields'] = $this->get_custom_fields( $post['ID'] );

		if ( in_array( 'enclosure', $fields ) ) {
			$_post['enclosure'] = array();
			$enclosures = (array) get_post_meta( $post['ID'], 'enclosure' );
			if ( ! empty( $enclosures ) ) {
				$encdata = explode( "\n", $enclosures[0] );
				$_post['enclosure']['url'] = trim( htmlspecialchars( $encdata[0] ) );
				$_post['enclosure']['length'] = (int) trim( $encdata[1] );
				$_post['enclosure']['type'] = trim( $encdata[2] );
			}
		}

		return apply_filters( 'xmlrpc_prepare_post', $_post, $post, $fields );
	}

	/**
	 * Prepares post data for return in an XML-RPC object.
	 *
	 * @access protected
	 *
	 * @param object $post_type Post type object
	 * @param array $fields The subset of post fields to return
	 * @return array The prepared post type data
	 */
	protected function _prepare_post_type( $post_type, $fields ) {
		$_post_type = array(
			'name' => $post_type->name,
			'label' => $post_type->label,
			'hierarchical' => (bool) $post_type->hierarchical,
			'public' => (bool) $post_type->public,
			'show_ui' => (bool) $post_type->show_ui,
			'_builtin' => (bool) $post_type->_builtin,
			'has_archive' => (bool) $post_type->has_archive,
			'supports' => get_all_post_type_supports( $post_type->name ),
		);

		if ( in_array( 'labels', $fields ) ) {
			$_post_type['labels'] = (array) $post_type->labels;
		}

		if ( in_array( 'cap', $fields ) ) {
			$_post_type['cap'] = (array) $post_type->cap;
			$_post_type['map_meta_cap'] = (bool) $post_type->map_meta_cap;
		}

		if ( in_array( 'menu', $fields ) ) {
			$_post_type['menu_position'] = (int) $post_type->menu_position;
			$_post_type['menu_icon'] = $post_type->menu_icon;
			$_post_type['show_in_menu'] = (bool) $post_type->show_in_menu;
		}

		if ( in_array( 'taxonomies', $fields ) )
			$_post_type['taxonomies'] = get_object_taxonomies( $post_type->name, 'names' );

		return apply_filters( 'xmlrpc_prepare_post_type', $_post_type, $post_type );
	}

	/**
	 * Prepares media item data for return in an XML-RPC object.
	 *
	 * @access protected
	 *
	 * @param object $media_item The unprepared media item data
	 * @param string $thumbnail_size The image size to use for the thumbnail URL
	 * @return array The prepared media item data
	 */
	protected function _prepare_media_item( $media_item, $thumbnail_size = 'thumbnail' ) {
		$_media_item = array(
			'attachment_id'    => strval( $media_item->ID ),
			'date_created_gmt' => $this->_convert_date_gmt( $media_item->post_date_gmt, $media_item->post_date ),
			'parent'           => $media_item->post_parent,
			'link'             => wp_get_attachment_url( $media_item->ID ),
			'title'            => $media_item->post_title,
			'caption'          => $media_item->post_excerpt,
			'description'      => $media_item->post_content,
			'metadata'         => wp_get_attachment_metadata( $media_item->ID ),
		);

		$thumbnail_src = image_downsize( $media_item->ID, $thumbnail_size );
		if ( $thumbnail_src )
			$_media_item['thumbnail'] = $thumbnail_src[0];
		else
			$_media_item['thumbnail'] = $_media_item['link'];

		return apply_filters( 'xmlrpc_prepare_media_item', $_media_item, $media_item, $thumbnail_size );
	}

	/**
	 * Prepares page data for return in an XML-RPC object.
	 *
	 * @access protected
	 *
	 * @param object $page The unprepared page data
	 * @return array The prepared page data
	 */
	protected function _prepare_page( $page ) {
		// Get all of the page content and link.
		$full_page = get_extended( $page->post_content );
		$link = post_permalink( $page->ID );

		// Get info the page parent if there is one.
		$parent_title = "";
		if ( ! empty( $page->post_parent ) ) {
			$parent = get_post( $page->post_parent );
			$parent_title = $parent->post_title;
		}

		// Determine comment and ping settings.
		$allow_comments = comments_open( $page->ID ) ? 1 : 0;
		$allow_pings = pings_open( $page->ID ) ? 1 : 0;

		// Format page date.
		$page_date = $this->_convert_date( $page->post_date );
		$page_date_gmt = $this->_convert_date_gmt( $page->post_date_gmt, $page->post_date );

		// Pull the categories info together.
		$categories = array();
		foreach ( wp_get_post_categories( $page->ID ) as $cat_id ) {
			$categories[] = get_cat_name( $cat_id );
		}

		// Get the author info.
		$author = get_userdata( $page->post_author );

		$page_template = get_page_template_slug( $page->ID );
		if ( empty( $page_template ) )
			$page_template = 'default';

		$_page = array(
			'dateCreated'            => $page_date,
			'userid'                 => $page->post_author,
			'page_id'                => $page->ID,
			'page_status'            => $page->post_status,
			'description'            => $full_page['main'],
			'title'                  => $page->post_title,
			'link'                   => $link,
			'permaLink'              => $link,
			'categories'             => $categories,
			'excerpt'                => $page->post_excerpt,
			'text_more'              => $full_page['extended'],
			'mt_allow_comments'      => $allow_comments,
			'mt_allow_pings'         => $allow_pings,
			'wp_slug'                => $page->post_name,
			'wp_password'            => $page->post_password,
			'wp_author'              => $author->display_name,
			'wp_page_parent_id'      => $page->post_parent,
			'wp_page_parent_title'   => $parent_title,
			'wp_page_order'          => $page->menu_order,
			'wp_author_id'           => (string) $author->ID,
			'wp_author_display_name' => $author->display_name,
			'date_created_gmt'       => $page_date_gmt,
			'custom_fields'          => $this->get_custom_fields( $page->ID ),
			'wp_page_template'       => $page_template
		);

		return apply_filters( 'xmlrpc_prepare_page', $_page, $page );
	}

	/**
	 * Prepares comment data for return in an XML-RPC object.
	 *
	 * @access protected
	 *
	 * @param object $comment The unprepared comment data
	 * @return array The prepared comment data
	 */
	protected function _prepare_comment( $comment ) {
		// Format page date.
		$comment_date = $this->_convert_date( $comment->comment_date );
		$comment_date_gmt = $this->_convert_date_gmt( $comment->comment_date_gmt, $comment->comment_date );

		if ( '0' == $comment->comment_approved )
			$comment_status = 'hold';
		else if ( 'spam' == $comment->comment_approved )
			$comment_status = 'spam';
		else if ( '1' == $comment->comment_approved )
			$comment_status = 'approve';
		else
			$comment_status = $comment->comment_approved;

		$_comment = array(
			'date_created_gmt' => $comment_date_gmt,
			'user_id'          => $comment->user_id,
			'comment_id'       => $comment->comment_ID,
			'parent'           => $comment->comment_parent,
			'status'           => $comment_status,
			'content'          => $comment->comment_content,
			'link'             => get_comment_link($comment),
			'post_id'          => $comment->comment_post_ID,
			'post_title'       => get_the_title($comment->comment_post_ID),
			'author'           => $comment->comment_author,
			'author_url'       => $comment->comment_author_url,
			'author_email'     => $comment->comment_author_email,
			'author_ip'        => $comment->comment_author_IP,
			'type'             => $comment->comment_type,
		);

		return apply_filters( 'xmlrpc_prepare_comment', $_comment, $comment );
	}

	/**
	 * Prepares user data for return in an XML-RPC object.
	 *
	 * @access protected
	 *
	 * @param WP_User $user The unprepared user object
	 * @param array $fields The subset of user fields to return
	 * @return array The prepared user data
	 */
	protected function _prepare_user( $user, $fields ) {
		$_user = array( 'user_id' => strval( $user->ID ) );

		$user_fields = array(
			'username'          => $user->user_login,
			'first_name'        => $user->user_firstname,
			'last_name'         => $user->user_lastname,
			'registered'        => $this->_convert_date( $user->user_registered ),
			'bio'               => $user->user_description,
			'email'             => $user->user_email,
			'nickname'          => $user->nickname,
			'nicename'          => $user->user_nicename,
			'url'               => $user->user_url,
			'display_name'      => $user->display_name,
			'roles'             => $user->roles,
		);

		if ( in_array( 'all', $fields ) ) {
			$_user = array_merge( $_user, $user_fields );
		} else {
			if ( in_array( 'basic', $fields ) ) {
				$basic_fields = array( 'username', 'email', 'registered', 'display_name', 'nicename' );
				$fields = array_merge( $fields, $basic_fields );
			}
			$requested_fields = array_intersect_key( $user_fields, array_flip( $fields ) );
			$_user = array_merge( $_user, $requested_fields );
		}

		return apply_filters( 'xmlrpc_prepare_user', $_user, $user, $fields );
	}

	/**
	 * Create a new post for any registered post type.
	 *
	 * @since 3.4.0
	 *
	 * @param array $args Method parameters. Contains:
	 *  - int     $blog_id
	 *  - string  $username
	 *  - string  $password
	 *  - array   $content_struct
	 *      $content_struct can contain:
	 *      - post_type (default: 'post')
	 *      - post_status (default: 'draft')
	 *      - post_title
	 *      - post_author
	 *      - post_excerpt
	 *      - post_content
	 *      - post_date_gmt | post_date
	 *      - post_format
	 *      - post_password
	 *      - comment_status - can be 'open' | 'closed'
	 *      - ping_status - can be 'open' | 'closed'
	 *      - sticky
	 *      - post_thumbnail - ID of a media item to use as the post thumbnail/featured image
	 *      - custom_fields - array, with each element containing 'key' and 'value'
	 *      - terms - array, with taxonomy names as keys and arrays of term IDs as values
	 *      - terms_names - array, with taxonomy names as keys and arrays of term names as values
	 *      - enclosure
	 *      - any other fields supported by wp_insert_post()
	 * @return string post_id
	 */
	function wp_newPost( $args ) {
		if ( ! $this->minimum_args( $args, 4 ) )
			return $this->error;

		$this->escape( $args );

		$blog_id        = (int) $args[0];
		$username       = $args[1];
		$password       = $args[2];
		$content_struct = $args[3];

		if ( ! $user = $this->login( $username, $password ) )
			return $this->error;

		do_action( 'xmlrpc_call', 'wp.newPost' );

		unset( $content_struct['ID'] );

		return $this->_insert_post( $user, $content_struct );
	}

	/**
	 * Helper method for filtering out elements from an array.
	 *
	 * @since 3.4.0
	 *
	 * @param int $count Number to compare to one.
	 */
	private function _is_greater_than_one( $count ) {
		return $count > 1;
	}

	/**
	 * Helper method for wp_newPost and wp_editPost, containing shared logic.
	 *
	 * @since 3.4.0
	 * @uses wp_insert_post()
	 *
	 * @param WP_User $user The post author if post_author isn't set in $content_struct.
	 * @param array $content_struct Post data to insert.
	 */
	protected function _insert_post( $user, $content_struct ) {
		$defaults = array( 'post_status' => 'draft', 'post_type' => 'post', 'post_author' => 0,
			'post_password' => '', 'post_excerpt' => '', 'post_content' => '', 'post_title' => '' );

		$post_data = wp_parse_args( $content_struct, $defaults );

		$post_type = get_post_type_object( $post_data['post_type'] );
		if ( ! $post_type )
			return new IXR_Error( 403, __( 'Invalid post type' ) );

		$update = ! empty( $post_data['ID'] );

		if ( $update ) {
			if ( ! get_post( $post_data['ID'] ) )
				return new IXR_Error( 401, __( 'Invalid post ID.' ) );
			if ( ! current_user_can( $post_type->cap->edit_post, $post_data['ID'] ) )
				return new IXR_Error( 401, __( 'Sorry, you are not allowed to edit this post.' ) );
			if ( $post_data['post_type'] != get_post_type( $post_data['ID'] ) )
				return new IXR_Error( 401, __( 'The post type may not be changed.' ) );
		} else {
			if ( ! current_user_can( $post_type->cap->create_posts ) || ! current_user_can( $post_type->cap->edit_posts ) )
				return new IXR_Error( 401, __( 'Sorry, you are not allowed to post on this site.' ) );
		}

		switch ( $post_data['post_status'] ) {
			case 'draft':
			case 'pending':
				break;
			case 'private':
				if ( ! current_user_can( $post_type->cap->publish_posts ) )
					return new IXR_Error( 401, __( 'Sorry, you are not allowed to create private posts in this post type' ) );
				break;
			case 'publish':
			case 'future':
				if ( ! current_user_can( $post_type->cap->publish_posts ) )
					return new IXR_Error( 401, __( 'Sorry, you are not allowed to publish posts in this post type' ) );
				break;
			default:
				if ( ! get_post_status_object( $post_data['post_status'] ) )
					$post_data['post_status'] = 'draft';
			break;
		}

		if ( ! empty( $post_data['post_password'] ) && ! current_user_can( $post_type->cap->publish_posts ) )
			return new IXR_Error( 401, __( 'Sorry, you are not allowed to create password protected posts in this post type' ) );

		$post_data['post_author'] = absint( $post_data['post_author'] );
		if ( ! empty( $post_data['post_author'] ) && $post_data['post_author'] != $user->ID ) {
			if ( ! current_user_can( $post_type->cap->edit_others_posts ) )
				return new IXR_Error( 401, __( 'You are not allowed to create posts as this user.' ) );

			$author = get_userdata( $post_data['post_author'] );

			if ( ! $author )
				return new IXR_Error( 404, __( 'Invalid author ID.' ) );
		} else {
			$post_data['post_author'] = $user->ID;
		}

		if ( isset( $post_data['comment_status'] ) && $post_data['comment_status'] != 'open' && $post_data['comment_status'] != 'closed' )
			unset( $post_data['comment_status'] );

		if ( isset( $post_data['ping_status'] ) && $post_data['ping_status'] != 'open' && $post_data['ping_status'] != 'closed' )
			unset( $post_data['ping_status'] );

		// Do some timestamp voodoo
		if ( ! empty( $post_data['post_date_gmt'] ) ) {
			// We know this is supposed to be GMT, so we're going to slap that Z on there by force
			$dateCreated = rtrim( $post_data['post_date_gmt']->getIso(), 'Z' ) . 'Z';
		} elseif ( ! empty( $post_data['post_date'] ) ) {
			$dateCreated = $post_data['post_date']->getIso();
		}

		if ( ! empty( $dateCreated ) ) {
			$post_data['post_date'] = get_date_from_gmt( iso8601_to_datetime( $dateCreated ) );
			$post_data['post_date_gmt'] = iso8601_to_datetime( $dateCreated, 'GMT' );
		}

		if ( ! isset( $post_data['ID'] ) )
			$post_data['ID'] = get_default_post_to_edit( $post_data['post_type'], true )->ID;
		$post_ID = $post_data['ID'];

		if ( $post_data['post_type'] == 'post' ) {
			// Private and password-protected posts cannot be stickied.
			if ( $post_data['post_status'] == 'private' || ! empty( $post_data['post_password'] ) ) {
				// Error if the client tried to stick the post, otherwise, silently unstick.
				if ( ! empty( $post_data['sticky'] ) )
					return new IXR_Error( 401, __( 'Sorry, you cannot stick a private post.' ) );
				if ( $update )
					unstick_post( $post_ID );
			} elseif ( isset( $post_data['sticky'] ) )  {
				if ( ! current_user_can( $post_type->cap->edit_others_posts ) )
					return new IXR_Error( 401, __( 'Sorry, you are not allowed to stick this post.' ) );
				if ( $post_data['sticky'] )
					stick_post( $post_ID );
				else
					unstick_post( $post_ID );
			}
		}

		if ( isset( $post_data['post_thumbnail'] ) ) {
			// empty value deletes, non-empty value adds/updates
			if ( ! $post_data['post_thumbnail'] )
				delete_post_thumbnail( $post_ID );
			elseif ( ! get_post( absint( $post_data['post_thumbnail'] ) ) )
				return new IXR_Error( 404, __( 'Invalid attachment ID.' ) );
			set_post_thumbnail( $post_ID, $post_data['post_thumbnail'] );
			unset( $content_struct['post_thumbnail'] );
		}

		if ( isset( $post_data['custom_fields'] ) )
			$this->set_custom_fields( $post_ID, $post_data['custom_fields'] );

		if ( isset( $post_data['terms'] ) || isset( $post_data['terms_names'] ) ) {
			$post_type_taxonomies = get_object_taxonomies( $post_data['post_type'], 'objects' );

			// accumulate term IDs from terms and terms_names
			$terms = array();

			// first validate the terms specified by ID
			if ( isset( $post_data['terms'] ) && is_array( $post_data['terms'] ) ) {
				$taxonomies = array_keys( $post_data['terms'] );

				// validating term ids
				foreach ( $taxonomies as $taxonomy ) {
					if ( ! array_key_exists( $taxonomy , $post_type_taxonomies ) )
						return new IXR_Error( 401, __( 'Sorry, one of the given taxonomies is not supported by the post type.' ) );

					if ( ! current_user_can( $post_type_taxonomies[$taxonomy]->cap->assign_terms ) )
						return new IXR_Error( 401, __( 'Sorry, you are not allowed to assign a term to one of the given taxonomies.' ) );

					$term_ids = $post_data['terms'][$taxonomy];
					foreach ( $term_ids as $term_id ) {
						$term = get_term_by( 'id', $term_id, $taxonomy );

						if ( ! $term )
							return new IXR_Error( 403, __( 'Invalid term ID' ) );

						$terms[$taxonomy][] = (int) $term_id;
					}
				}
			}

			// now validate terms specified by name
			if ( isset( $post_data['terms_names'] ) && is_array( $post_data['terms_names'] ) ) {
				$taxonomies = array_keys( $post_data['terms_names'] );

				foreach ( $taxonomies as $taxonomy ) {
					if ( ! array_key_exists( $taxonomy , $post_type_taxonomies ) )
						return new IXR_Error( 401, __( 'Sorry, one of the given taxonomies is not supported by the post type.' ) );

					if ( ! current_user_can( $post_type_taxonomies[$taxonomy]->cap->assign_terms ) )
						return new IXR_Error( 401, __( 'Sorry, you are not allowed to assign a term to one of the given taxonomies.' ) );

					// for hierarchical taxonomies, we can't assign a term when multiple terms in the hierarchy share the same name
					$ambiguous_terms = array();
					if ( is_taxonomy_hierarchical( $taxonomy ) ) {
						$tax_term_names = get_terms( $taxonomy, array( 'fields' => 'names', 'hide_empty' => false ) );

						// count the number of terms with the same name
						$tax_term_names_count = array_count_values( $tax_term_names );

						// filter out non-ambiguous term names
						$ambiguous_tax_term_counts = array_filter( $tax_term_names_count, array( $this, '_is_greater_than_one') );

						$ambiguous_terms = array_keys( $ambiguous_tax_term_counts );
					}

					$term_names = $post_data['terms_names'][$taxonomy];
					foreach ( $term_names as $term_name ) {
						if ( in_array( $term_name, $ambiguous_terms ) )
							return new IXR_Error( 401, __( 'Ambiguous term name used in a hierarchical taxonomy. Please use term ID instead.' ) );

						$term = get_term_by( 'name', $term_name, $taxonomy );

						if ( ! $term ) {
							// term doesn't exist, so check that the user is allowed to create new terms
							if ( ! current_user_can( $post_type_taxonomies[$taxonomy]->cap->edit_terms ) )
								return new IXR_Error( 401, __( 'Sorry, you are not allowed to add a term to one of the given taxonomies.' ) );

							// create the new term
							$term_info = wp_insert_term( $term_name, $taxonomy );
							if ( is_wp_error( $term_info ) )
								return new IXR_Error( 500, $term_info->get_error_message() );

							$terms[$taxonomy][] = (int) $term_info['term_id'];
						} else {
							$terms[$taxonomy][] = (int) $term->term_id;
						}
					}
				}
			}

			$post_data['tax_input'] = $terms;
			unset( $post_data['terms'], $post_data['terms_names'] );
		} else {
			// do not allow direct submission of 'tax_input', clients must use 'terms' and/or 'terms_names'
			unset( $post_data['tax_input'], $post_data['post_category'], $post_data['tags_input'] );
		}

		if ( isset( $post_data['post_format'] ) ) {
			$format = set_post_format( $post_ID, $post_data['post_format'] );

			if ( is_wp_error( $format ) )
				return new IXR_Error( 500, $format->get_error_message() );

			unset( $post_data['post_format'] );
		}

		// Handle enclosures
		$enclosure = isset( $post_data['enclosure'] ) ? $post_data['enclosure'] : null;
		$this->add_enclosure_if_new( $post_ID, $enclosure );

		$this->attach_uploads( $post_ID, $post_data['post_content'] );

		$post_data = apply_filters( 'xmlrpc_wp_insert_post_data', $post_data, $content_struct );

		$post_ID = $update ? wp_update_post( $post_data, true ) : wp_insert_post( $post_data, true );
		if ( is_wp_error( $post_ID ) )
			return new IXR_Error( 500, $post_ID->get_error_message() );

		if ( ! $post_ID )
			return new IXR_Error( 401, __( 'Sorry, your entry could not be posted. Something wrong happened.' ) );

		return strval( $post_ID );
	}

	/**
	 * Edit a post for any registered post type.
	 *
	 * The $content_struct parameter only needs to contain fields that
	 * should be changed. All other fields will retain their existing values.
	 *
	 * @since 3.4.0
	 *
	 * @param array $args Method parameters. Contains:
	 *  - int     $blog_id
	 *  - string  $username
	 *  - string  $password
	 *  - int    