<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'template_redirect', 'ksc404_custom_404_page_handler', 10 );

function ksc404_custom_404_page_handler() {
    if ( ! is_404() ) { return; }

    global $wpdb, $wp_query;

    $options = get_option( 'ksc404_settings', ksc404_get_default_settings() );
    $latest_posts_count = (int) $options['latest_posts_count'];
    $related_posts_count = (int) $options['related_posts_count'];
    $display_post_types = !empty($options['latest_post_types']) && is_array($options['latest_post_types']) ? $options['latest_post_types'] : ['post'];

    $redirect_found_action = $options['redirect_found_action'];
    $redirect_not_found_action = $options['redirect_not_found_action'];
    $redirect_not_found_custom_url = $options['redirect_not_found_custom_url'];

    $requested_slug = ''; $redirect_url = null; $redirect_post_id = null; $redirect_post = null; $redirect_thumbnail_url = '';
    $requested_uri = isset($_SERVER['REQUEST_URI']) ? esc_url_raw(wp_unslash($_SERVER['REQUEST_URI'])) : '';
    $requested_uri_path = parse_url($requested_uri, PHP_URL_PATH) ?: '';

    if(!empty($requested_uri)){
        $path_parts = explode('/', trim($requested_uri_path, '/'));
        if(!empty($path_parts)){
            $potential_slug = end($path_parts);
            $requested_slug = sanitize_title(urldecode($potential_slug));
        }
    }
    if(isset($wp_query->query_vars['name']) && !empty($wp_query->query_vars['name'])){$requested_slug = sanitize_title($wp_query->query_vars['name']);}
    elseif(isset($wp_query->query_vars['pagename']) && !empty($wp_query->query_vars['pagename'])){$requested_slug = sanitize_title(basename($wp_query->query_vars['pagename']));}
    elseif(isset($wp_query->query_vars['category_name']) && !empty($wp_query->query_vars['category_name'])){$requested_slug = sanitize_title($wp_query->query_vars['category_name']);}
    elseif(isset($wp_query->query_vars['tag']) && !empty($wp_query->query_vars['tag'])){$requested_slug = sanitize_title($wp_query->query_vars['tag']);}

    if(!empty($requested_slug) && $wpdb instanceof wpdb){
        $redirect_post_id_query = $wpdb->prepare("SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wp_old_slug' AND meta_value = %s", $requested_slug);
        $redirect_post_id_result = $wpdb->get_var($redirect_post_id_query);
        if($redirect_post_id_result){
            $redirect_post_id = (int) $redirect_post_id_result;
            $redirect_post = get_post($redirect_post_id);
            if($redirect_post && $redirect_post->post_status === 'publish'){
                $redirect_url = get_permalink($redirect_post);
                if(function_exists('has_post_thumbnail') && has_post_thumbnail($redirect_post)){
                    $redirect_thumbnail_url = get_the_post_thumbnail_url($redirect_post, 'thumbnail');
                }
            } else {
                $redirect_url = null; $redirect_post = null; $redirect_post_id = null;
            }
        }
    }

    $final_status_code = 404;
    $html_canonical_url = null;
    $http_link_header_canonical_url = null;
    $error_title_en = ""; $error_message_en = ""; $error_message_en_suffix = "";
    $page_main_title_jp = ""; $page_sub_message_jp = "";
    $show_english_error_details = true;
    $show_actions_in_primary = false;

    if ( ! empty( $redirect_url ) && $redirect_post_id) {
        switch ($redirect_found_action) {
            case 'show_default_404':
                // Let WordPress handle the default 404 page (theme's 404.php or server default)
                return;
            case 'redirect_301': wp_redirect( esc_url_raw( $redirect_url ), 301 ); exit;
            case 'redirect_302': wp_redirect( esc_url_raw( $redirect_url ), 302 ); exit;
            case 'show_200_canonical':
                $final_status_code = 200;
                $html_canonical_url = $redirect_url;
                $http_link_header_canonical_url = $redirect_url;
                $redirected_post_title = get_the_title($redirect_post_id);
                $page_main_title_jp = "404 Not Found - " . __("ページが見つかりませんでした", 'kashiwazaki-seo-custom-404');
                $page_sub_message_jp = sprintf(__("お探しのページは見つかりませんでしたが、%s というページが見つかりました。アクセスしようとしたページ (<code>%s</code>) は見つかりませんでしたが、代わりに以下のページへの移動をおすすめします。こちらがご希望のページかもしれません。", 'kashiwazaki-seo-custom-404'), '<u>' . esc_html($redirected_post_title) . '</u>', esc_html(basename($requested_uri)));
                $show_english_error_details = false;
                break;
            case 'show_410_canonical':
                $final_status_code = 410;
                $html_canonical_url = $redirect_url;
                $http_link_header_canonical_url = $redirect_url;
                $page_main_title_jp = "410 Gone - " . __("ページが削除されました", 'kashiwazaki-seo-custom-404');
                $error_title_en = "410. That's gone.";
                $error_message_en = sprintf( "The requested URL <code>%s</code> has been permanently removed from this server.", esc_html($requested_uri_path) );
                $error_message_en_suffix = "The resource is no longer available.";
                $page_sub_message_jp = __("お探しのページは削除されましたが、代わりに以下のページが関連するかもしれません。", 'kashiwazaki-seo-custom-404');
                break;
            case 'show_404_canonical':
            default:
                $final_status_code = 404;
                $html_canonical_url = $redirect_url;
                $http_link_header_canonical_url = $redirect_url;
                $page_main_title_jp = "404 Not Found - " . __("ページが見つかりませんでした", 'kashiwazaki-seo-custom-404');
                $error_title_en = "404. That's an error.";
                $error_message_en = sprintf( "The requested URL <code>%s</code> was not found on this server.", esc_html($requested_uri_path) );
                $error_message_en_suffix = "That's all we know.";
                $page_sub_message_jp = __("お探しのページは見つかりませんでしたが、代わりに以下のページが関連するかもしれません。", 'kashiwazaki-seo-custom-404');
                break;
        }
    } else {
        $show_actions_in_primary = true; // No redirect candidate, so show actions in primary content area
        switch ($redirect_not_found_action) {
            case 'show_default_404':
                // Let WordPress handle the default 404 page (theme's 404.php or server default)
                return;
            case 'redirect_301_custom':
                $custom_redirect_target = !empty($redirect_not_found_custom_url) && filter_var($redirect_not_found_custom_url, FILTER_VALIDATE_URL) ? $redirect_not_found_custom_url : home_url('/');
                wp_redirect( esc_url_raw( $custom_redirect_target ), 301 ); exit;
            case 'redirect_302_custom':
                $custom_redirect_target = !empty($redirect_not_found_custom_url) && filter_var($redirect_not_found_custom_url, FILTER_VALIDATE_URL) ? $redirect_not_found_custom_url : home_url('/');
                wp_redirect( esc_url_raw( $custom_redirect_target ), 302 ); exit;
            case 'show_200':
                $final_status_code = 200;
                $page_main_title_jp = "404 Not Found - " . __("ページ情報", 'kashiwazaki-seo-custom-404');
                $page_sub_message_jp = sprintf(__("要求されたページ (<code>%s</code>) に関する情報、または関連情報をご案内します。", 'kashiwazaki-seo-custom-404'), esc_html(basename($requested_uri)));
                $show_english_error_details = false;
                break;
            case 'show_410':
                $final_status_code = 410;
                $page_main_title_jp = "410 Gone - " . __("ページが削除されました", 'kashiwazaki-seo-custom-404');
                $error_title_en = "410. That's gone.";
                $error_message_en = sprintf( "The requested URL <code>%s</code> has been permanently removed from this server.", esc_html($requested_uri_path) );
                $error_message_en_suffix = "The resource is no longer available.";
                $page_sub_message_jp = __("お探しのページは削除されました。URLをご確認いただくか、トップページへお戻りください。", 'kashiwazaki-seo-custom-404');
                break;
            case 'show_404':
            default:
                $final_status_code = 404;
                $page_main_title_jp = "404 Not Found - " . __("ページが見つかりませんでした", 'kashiwazaki-seo-custom-404');
                $error_title_en = "404. That's an error.";
                $error_message_en = sprintf( "The requested URL <code>%s</code> was not found on this server.", esc_html($requested_uri_path) );
                $error_message_en_suffix = "That's all we know.";
                $page_sub_message_jp = __("お探しのページは見つかりませんでした。URLをご確認いただくか、トップページへお戻りください。", 'kashiwazaki-seo-custom-404');
                break;
        }
    }

    status_header( $final_status_code );
    nocache_headers();
    if ( ! empty( $html_canonical_url ) ) {
        header( 'Link: <' . esc_url_raw( $html_canonical_url ) . '>; rel="canonical"' );
    }

    $related_posts_query = null; $found_related_tags = []; $exclude_from_latest_ids = [];
    if ($redirect_post_id) { $exclude_from_latest_ids[] = $redirect_post_id; }

    if ( $related_posts_count > 0 && ! empty( $requested_slug ) ) {
        $search_term_for_tags = str_replace('-', ' ', $requested_slug);
        $potential_tags_args = ['taxonomy' => 'post_tag', 'name__like' => $search_term_for_tags, 'hide_empty' => true, 'number' => 5, 'fields' => 'all'];
        $potential_tags = get_terms( $potential_tags_args );
        if ( ! is_wp_error( $potential_tags ) && ! empty( $potential_tags ) ) {
            $found_related_tags = $potential_tags;
            $related_tag_slugs = wp_list_pluck( $potential_tags, 'slug' );
            $related_posts_args = ['post_type' => $display_post_types, 'post_status' => 'publish', 'posts_per_page' => $related_posts_count, 'orderby' => 'date', 'order' => 'DESC', 'tag_slug__in' => $related_tag_slugs, 'no_found_rows' => true, 'update_post_meta_cache' => false, 'update_post_term_cache' => false, 'post__not_in' => $exclude_from_latest_ids];
            $related_posts_query = new WP_Query( $related_posts_args );
            if ($related_posts_query && $related_posts_query->have_posts()) {
                $exclude_from_latest_ids = array_merge($exclude_from_latest_ids, wp_list_pluck($related_posts_query->posts, 'ID'));
                $exclude_from_latest_ids = array_map('intval', array_unique($exclude_from_latest_ids));
            }
        }
    }

    $latest_posts_query = null;
    if ( $latest_posts_count > 0 ) {
        $latest_posts_args = ['post_type' => $display_post_types, 'post_status' => 'publish', 'posts_per_page' => $latest_posts_count, 'orderby' => 'date', 'order' => 'DESC', 'no_found_rows' => true, 'update_post_meta_cache' => false, 'update_post_term_cache' => false];
        if (!empty($exclude_from_latest_ids)) { $latest_posts_args['post__not_in'] = array_map('intval', array_unique($exclude_from_latest_ids)); }
        $latest_posts_query = new WP_Query( $latest_posts_args );
    }

    $plugin_file_path = KSC404_PLUGIN_DIR . 'kashiwazaki-seo-custom-404.php';
    $plugin_data = file_exists($plugin_file_path) && function_exists('get_plugin_data') ? get_plugin_data( $plugin_file_path ) : [];
    $schema_version = KSC404_VERSION;
    // 構造化マークアップのページ名を設定
    if ($final_status_code == 404 && empty($redirect_url)) {
        $schema_page_name_for_title = "404 Not Found - " . __("ページが見つかりませんでした", 'kashiwazaki-seo-custom-404');
    } elseif ($final_status_code == 410 && empty($redirect_url)) {
        $schema_page_name_for_title = "410 Gone - " . __("ページが削除されました", 'kashiwazaki-seo-custom-404');
    } elseif ($final_status_code == 200 && !empty($redirect_url) && $redirect_post_id) {
        $schema_page_name_for_title = sprintf(__("%sの関連情報", 'kashiwazaki-seo-custom-404'), get_the_title($redirect_post_id));
    } else {
        $schema_page_name_for_title = $page_main_title_jp;
    }

    // 構造化マークアップの基本構造
    $schema = [
        "@context" => "https://schema.org",
        "@type" => "WebPage",
        "name" => $schema_page_name_for_title . " - " . get_bloginfo('name'),
        "description" => $final_status_code == 410
            ? __("お探しのページは削除されました。他のコンテンツをご覧いただくか、サイト内検索をお試しください。", 'kashiwazaki-seo-custom-404')
            : __("お探しのページは見つかりませんでした。URLをご確認いただくか、サイト内検索をお試しください。", 'kashiwazaki-seo-custom-404'),
        "url" => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . esc_url_raw(wp_unslash($_SERVER['REQUEST_URI'])),
        "isPartOf" => [
            "@type" => "WebSite",
            "url" => home_url('/'),
            "name" => get_bloginfo('name')
        ],
        "provider" => [
            "@type" => "SoftwareApplication",
            "name" => isset($plugin_data['Name']) ? $plugin_data['Name'] : 'Kashiwazaki SEO Custom 404',
            "softwareVersion" => $schema_version,
            "author" => [
                "@type" => "Person",
                "name" => isset($plugin_data['Author']) ? wp_strip_all_tags($plugin_data['Author']) : '柏崎剛',
                "url" => isset($plugin_data['AuthorURI']) ? $plugin_data['AuthorURI'] : 'https://www.tsuyoshikashiwazaki.jp'
            ],
            "applicationCategory" => "WordPress Plugin",
            "operatingSystem" => "WordPress",
            "url" => isset($plugin_data['PluginURI']) ? $plugin_data['PluginURI'] : 'https://www.tsuyoshikashiwazaki.jp'
        ]
    ];

    // エラーのメインエンティティ（ステータスコードに応じて設定）
    if ($final_status_code == 410) {
        $schema["mainEntity"] = [
            "@type" => "Thing",
            "name" => "410 Gone",
            "description" => "The requested page has been permanently removed."
        ];
    } else {
        $schema["mainEntity"] = [
            "@type" => "Thing",
            "name" => "404 Not Found",
            "description" => "The requested page could not be found."
        ];
    }

    // リダイレクト先がある場合の処理
    if (!empty($redirect_url) && $redirect_post_id) {
        // WordPressの_wp_old_slugで検出されたリダイレクト先のみを含める
        $schema['mentions'] = [
            "@type" => "WebPage",
            "name" => get_the_title($redirect_post_id),
            "url" => esc_url($redirect_url),
            "description" => "WordPressのスラッグ変更により移動されたページ"
        ];

        $schema['potentialAction'] = [
            [
                "@type" => "ReadAction",
                "name" => "移動先のページを読む",
                "target" => esc_url($redirect_url)
            ],
            [
                "@type" => "ViewAction",
                "name" => "トップページに戻る",
                "target" => home_url('/')
            ]
        ];

        $schema['mainEntityOfPage'] = [
            "@type" => "WebPage",
            "@id" => esc_url($redirect_url)
        ];

        $schema['relatedLink'] = [esc_url($redirect_url)];
    }

    $selected_theme_slug = $options['color_theme'];
    $themes = ksc404_get_color_themes();
    $current_theme_colors = isset($themes[$selected_theme_slug]['colors']) && is_array($themes[$selected_theme_slug]['colors']) ? $themes[$selected_theme_slug]['colors'] : $themes['default-blue']['colors'];
    $has_suggestions = (!empty($redirect_url) && $redirect_post) || ($related_posts_query && $related_posts_query->have_posts()) || ($latest_posts_query && $latest_posts_query->have_posts());

    ?>
    <!DOCTYPE html>
    <html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo( 'charset' ); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo esc_html($schema_page_name_for_title); ?> - <?php bloginfo( 'name' ); ?></title>
        <?php if ( ! empty( $html_canonical_url ) ) : ?><link rel="canonical" href="<?php echo esc_url( $html_canonical_url ); ?>" /><?php endif; ?>
        <link rel="stylesheet" id="ksc404-public-style-css" href="<?php echo esc_url( KSC404_PLUGIN_URL . 'assets/css/ksc404-public.css?ver=' . KSC404_VERSION ); ?>" type="text/css" media="all" />
        <style id="ksc404-color-vars">
            :root { <?php foreach ( $current_theme_colors as $var => $value ) : echo esc_html( $var ) . ': ' . esc_html( $value ) . '; '; endforeach; ?> }
        </style>
        <script type="application/ld+json"><?php echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ); ?></script>
    </head>
    <body class="ksc404-body <?php echo esc_attr('theme-' . $selected_theme_slug); ?>">
        <div class="ksc404-wrapper">
            <div class="ksc404-container">
                <div class="ksc404-content-primary <?php if (!$has_suggestions && !$show_actions_in_primary) echo 'ksc404-content-primary-no-suggestions'; ?>">
                    <h1 class="ksc404-main-title-jp"><?php echo esc_html($page_main_title_jp); ?></h1>
                    <?php if ($show_english_error_details): ?>
                        <div class="ksc404-error-details-en">
                            <p class="ksc404-error-title-en"><?php echo esc_html($error_title_en); ?></p>
                            <p class="ksc404-error-message-en"><?php echo wp_kses_post($error_message_en); ?></p>
                            <?php if(!empty($error_message_en_suffix)): ?>
                                <p class="ksc404-error-message-en-suffix"><?php echo esc_html($error_message_en_suffix); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <p class="ksc404-sub-message-jp"><?php echo wp_kses_post($page_sub_message_jp); ?></p>
                    <?php if ($show_actions_in_primary): ?>
                        <div class="ksc404-actions">
                            <ul class="ksc404-actions-list">
                                <li class="ksc404-action-item">
                                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="ksc404-button"><?php _e("トップページに戻る", 'kashiwazaki-seo-custom-404'); ?></a>
                                </li>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ( ! empty( $redirect_url ) && $redirect_post ) : ?>
                <div class="ksc404-content-secondary ksc404-redirect-info">
                    <h2 class="ksc404-section-title ksc404-redirect-info-title">
                        <?php
                        if($final_status_code == 200 && $redirect_found_action == 'show_200_canonical') {
                            _e("こちらのページに移動しますか？", 'kashiwazaki-seo-custom-404');
                        } elseif($final_status_code == 410) {
                            _e("削除されたページの代替として、こちらのページがあります", 'kashiwazaki-seo-custom-404');
                        } else {
                            _e("もしかして、こちらのページをお探しですか？", 'kashiwazaki-seo-custom-404');
                        }
                        ?>
                    </h2>
                    <?php if ( $redirect_thumbnail_url ) : ?>
                    <div class="ksc404-redirect-thumbnail-container">
                        <a href="<?php echo esc_url( $redirect_url ); ?>" aria-label="<?php echo esc_attr( sprintf( __('記事「%s」へ移動', 'kashiwazaki-seo-custom-404'), get_the_title( $redirect_post ) ) ); ?>">
                            <div class="ksc404-js-bg-image-loader" data-bg-src="<?php echo esc_url( $redirect_thumbnail_url ); ?>" role="img" aria-label="<?php echo esc_attr( get_the_title( $redirect_post ) ); ?>"></div>
                        </a>
                    </div>
                    <?php endif; ?>
                    <div class="ksc404-redirect-details">
                        <?php if ( get_the_title( $redirect_post ) ) : ?><p class="ksc404-redirect-title"><a href="<?php echo esc_url( $redirect_url ); ?>"><?php echo esc_html( get_the_title( $redirect_post ) ); ?></a></p><?php endif; ?>
                        <?php $redirect_excerpt = has_excerpt( $redirect_post ) ? get_the_excerpt( $redirect_post ) : wp_trim_words( $redirect_post->post_content, 60, ' [...]' ); if ( wp_strip_all_tags( $redirect_excerpt ) ) : ?><p class="ksc404-redirect-excerpt"><?php echo esc_html( wp_strip_all_tags( $redirect_excerpt ) ); ?></p><?php endif; ?>
                    </div>
                    <div class="ksc404-redirect-link-wrapper">
                        <p><a href="<?php echo esc_url( $redirect_url ); ?>" class="ksc404-button"><?php echo esc_html( get_the_title( $redirect_post ) ? sprintf(__( '「%s」へ進む', 'kashiwazaki-seo-custom-404' ), get_the_title( $redirect_post )) : __('移動先のページへ進む', 'kashiwazaki-seo-custom-404') ); ?></a></p>
                        <p><small><a href="<?php echo esc_url( $redirect_url ); ?>" class="ksc404-redirect-url-link"><?php echo esc_url( $redirect_url ); ?></a></small></p>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ( ($related_posts_query && $related_posts_query->have_posts()) || ($latest_posts_query && $latest_posts_query->have_posts()) ) : ?>
                    <?php if ( ($redirect_url && $redirect_post) || ($final_status_code == 200 && $redirect_found_action == 'show_200_canonical' && $redirect_url) ) : ?>
                        <div class="ksc404-suggestions-divider"></div>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ( $related_posts_query && $related_posts_query->have_posts() ) : ?>
                <div class="ksc404-content-secondary ksc404-posts-section ksc404-related-posts-section">
                    <h2 class="ksc404-section-title"><?php $tag_names_array = wp_list_pluck($found_related_tags, 'name'); echo esc_html( sprintf( __('「%s」関連の記事', 'kashiwazaki-seo-custom-404'), implode( __("」や「", 'kashiwazaki-seo-custom-404'), $tag_names_array )));?></h2>
                    <?php while ( $related_posts_query->have_posts() ) : $related_posts_query->the_post(); $post_id_in_loop = get_the_ID(); $post_title_in_loop = get_the_title(); $post_permalink_in_loop = get_permalink(); $post_excerpt_in_loop = has_excerpt() ? get_the_excerpt() : wp_trim_words( strip_shortcodes(get_the_content()), 30, ' [...]' ); $post_excerpt_in_loop = wp_strip_all_tags( $post_excerpt_in_loop ); $post_thumbnail_url_in_loop = (function_exists('has_post_thumbnail') && has_post_thumbnail()) ? get_the_post_thumbnail_url( $post_id_in_loop, 'thumbnail' ) : ''; ?>
                    <div class="ksc404-post-item">
                        <?php if ( $post_thumbnail_url_in_loop ) : ?><div class="ksc404-post-thumbnail-container"><a href="<?php echo esc_url( $post_permalink_in_loop ); ?>" aria-label="<?php echo esc_attr(sprintf(__('記事「%s」へ移動', 'kashiwazaki-seo-custom-404'), $post_title_in_loop)); ?>"><div class="ksc404-js-bg-image-loader" data-bg-src="<?php echo esc_url( $post_thumbnail_url_in_loop ); ?>" role="img" aria-label="<?php echo esc_attr( $post_title_in_loop ); ?>"></div></a></div><?php endif; ?>
                        <div class="ksc404-post-content"><h3 class="ksc404-post-title"><a href="<?php echo esc_url( $post_permalink_in_loop ); ?>"><?php echo esc_html( $post_title_in_loop ); ?></a></h3><?php if ( $post_excerpt_in_loop ) : ?><p class="ksc404-post-excerpt"><?php echo esc_html( $post_excerpt_in_loop ); ?></p><?php endif; ?></div>
                    </div>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
                <?php endif; ?>

                <?php if ( $latest_posts_query && $latest_posts_query->have_posts() ) : ?>
                <div class="ksc404-content-secondary ksc404-posts-section ksc404-latest-posts-section">
                    <h2 class="ksc404-section-title"><?php $latest_title_key = __("最近の投稿", 'kashiwazaki-seo-custom-404'); if ($related_posts_query && $related_posts_query->have_posts() && $related_posts_count > 0) { $latest_title_key = !empty($redirect_url) ? __("その他の関連情報", 'kashiwazaki-seo-custom-404') : __("その他の最近の投稿", 'kashiwazaki-seo-custom-404');} echo esc_html($latest_title_key); ?></h2>
                    <?php while ( $latest_posts_query->have_posts() ) : $latest_posts_query->the_post(); $post_id_in_loop = get_the_ID(); $post_title_in_loop = get_the_title(); $post_permalink_in_loop = get_permalink(); $post_excerpt_in_loop = has_excerpt() ? get_the_excerpt() : wp_trim_words( strip_shortcodes(get_the_content()), 30, ' [...]' ); $post_excerpt_in_loop = wp_strip_all_tags( $post_excerpt_in_loop ); $post_thumbnail_url_in_loop = (function_exists('has_post_thumbnail') && has_post_thumbnail()) ? get_the_post_thumbnail_url( $post_id_in_loop, 'thumbnail' ) : ''; ?>
                    <div class="ksc404-post-item">
                        <?php if ( $post_thumbnail_url_in_loop ) : ?><div class="ksc404-post-thumbnail-container"><a href="<?php echo esc_url( $post_permalink_in_loop ); ?>" aria-label="<?php echo esc_attr(sprintf(__('記事「%s」へ移動', 'kashiwazaki-seo-custom-404'), $post_title_in_loop)); ?>"><div class="ksc404-js-bg-image-loader" data-bg-src="<?php echo esc_url( $post_thumbnail_url_in_loop ); ?>" role="img" aria-label="<?php echo esc_attr( $post_title_in_loop ); ?>"></div></a></div><?php endif; ?>
                        <div class="ksc404-post-content"><h3 class="ksc404-post-title"><a href="<?php echo esc_url( $post_permalink_in_loop ); ?>"><?php echo esc_html( $post_title_in_loop ); ?></a></h3><?php if ( $post_excerpt_in_loop ) : ?><p class="ksc404-post-excerpt"><?php echo esc_html( $post_excerpt_in_loop ); ?></p><?php endif; ?></div>
                    </div>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
                <?php elseif ( !($related_posts_query && $related_posts_query->have_posts()) && $latest_posts_count === 0 && empty($redirect_url) ) : ?>
                <div class="ksc404-content-secondary ksc404-posts-section ksc404-no-posts-message"> <p><?php _e("現在表示できる記事がありません。", 'kashiwazaki-seo-custom-404'); ?></p> </div>
                <?php endif; ?>

                <?php if ( !$show_actions_in_primary && !$has_suggestions ) : ?>
                <div class="ksc404-back-to-top"> <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="ksc404-button"><?php _e("トップページに戻る", 'kashiwazaki-seo-custom-404'); ?></a> </div>
                <?php endif; ?>

            </div>
        </div>
        <footer class="ksc404-footer">
            <?php $footer_text = sprintf(__('Copyright © %1$s %2$s. All Rights Reserved.', 'kashiwazaki-seo-custom-404'), date('Y'), '<a href="' . esc_url( home_url( '/' ) ) . '">' . get_bloginfo( 'name' ) . '</a>'); echo wp_kses_post( $footer_text ); ?>
        </footer>
        <script id="ksc404-image-loader-script">
        document.addEventListener('DOMContentLoaded',function(){
            const imageLoaders=document.querySelectorAll('.ksc404-js-bg-image-loader');
            imageLoaders.forEach(function(loader){
                const src=loader.dataset.bgSrc;
                if(src){
                    loader.style.backgroundImage='url("'+src.replace(/"/g,'%22').replace(/'/g,'%27')+'")';
                    const img=new Image();
                    img.onload=function(){ loader.style.backgroundColor='transparent'; };
                    img.onerror=function(){ loader.style.backgroundColor='#ccc'; };
                    img.src=src;
                }else{
                    loader.style.display='none';
                }
            });
        });
        </script>
    </body>
    </html>
    <?php
    exit;
}
?>
