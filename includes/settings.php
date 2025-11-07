<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function ksc404_add_admin_menu() {
    add_menu_page(
        __('Kashiwazaki SEO Custom 404 設定', 'kashiwazaki-seo-custom-404'),
        __('Kashiwazaki SEO Custom 404', 'kashiwazaki-seo-custom-404'),
        'manage_options',
        'ksc404_settings_page',
        'ksc404_settings_page_html',
        'dashicons-warning',
        81
    );
}
add_action( 'admin_menu', 'ksc404_add_admin_menu' );

function ksc404_settings_init() {
    register_setting('ksc404_settings_group','ksc404_settings','ksc404_settings_sanitize');

    add_settings_section('ksc404_settings_section_general',__('表示設定', 'kashiwazaki-seo-custom-404'),null,'ksc404_settings_page');
    add_settings_field('ksc404_latest_posts_count',__('最新記事の表示件数', 'kashiwazaki-seo-custom-404'),'ksc404_settings_field_latest_posts_count_html','ksc404_settings_page','ksc404_settings_section_general');
    add_settings_field('ksc404_related_posts_count',__('関連性の高い記事の表示件数', 'kashiwazaki-seo-custom-404'),'ksc404_settings_field_related_posts_count_html','ksc404_settings_page','ksc404_settings_section_general');
    add_settings_field('ksc404_latest_post_types',__('表示する投稿タイプ', 'kashiwazaki-seo-custom-404'),'ksc404_settings_field_post_types_html','ksc404_settings_page','ksc404_settings_section_general');
    add_settings_field('ksc404_color_theme',__('配色テーマ', 'kashiwazaki-seo-custom-404'),'ksc404_settings_field_color_theme_html','ksc404_settings_page','ksc404_settings_section_general');

    add_settings_section('ksc404_settings_section_redirect',__('リダイレクト・HTTPステータス設定', 'kashiwazaki-seo-custom-404'),null,'ksc404_settings_page');
    add_settings_field('ksc404_redirect_found_action',__('移動先ページが検知できた場合の動作', 'kashiwazaki-seo-custom-404'),'ksc404_settings_field_redirect_found_action_html','ksc404_settings_page','ksc404_settings_section_redirect');
    add_settings_field('ksc404_redirect_not_found_action',__('移動先ページが見つからない場合の動作', 'kashiwazaki-seo-custom-404'),'ksc404_settings_field_redirect_not_found_action_html','ksc404_settings_page','ksc404_settings_section_redirect');
    add_settings_field('ksc404_redirect_not_found_custom_url',__('上記でリダイレクト選択時のURL', 'kashiwazaki-seo-custom-404'),'ksc404_settings_field_redirect_not_found_custom_url_html','ksc404_settings_page','ksc404_settings_section_redirect');
}
add_action( 'admin_init', 'ksc404_settings_init' );

function ksc404_settings_field_latest_posts_count_html() { $o=get_option('ksc404_settings', ksc404_get_default_settings());$c=$o['latest_posts_count'];echo '<input type="number" name="ksc404_settings[latest_posts_count]" value="'.esc_attr($c).'" min="0" step="1"><p class="description">'.__('カスタム404ページに表示する最新記事の件数を指定します。0で非表示。', 'kashiwazaki-seo-custom-404').'</p>'; }
function ksc404_settings_field_related_posts_count_html() { $o=get_option('ksc404_settings', ksc404_get_default_settings());$c=$o['related_posts_count'];echo '<input type="number" name="ksc404_settings[related_posts_count]" value="'.esc_attr($c).'" min="0" step="1"><p class="description">'.__('タグに基づいて関連性の高い記事を表示する場合の最大件数を指定します。0で非表示。', 'kashiwazaki-seo-custom-404').'</p>'; }
function ksc404_settings_field_post_types_html() { $o=get_option('ksc404_settings', ksc404_get_default_settings());$s=$o['latest_post_types'];$p=get_post_types(['public'=>true,'show_ui'=>true],'objects');unset($p['attachment']);if(empty($p)){echo '<p>'.__('投稿タイプなし。', 'kashiwazaki-seo-custom-404').'</p>';return;}echo '<fieldset>';foreach($p as $pt){$ck=in_array($pt->name,$s)?'checked':'';echo '<label style="margin-right:15px;display:block;"><input type="checkbox" name="ksc404_settings[latest_post_types][]" value="'.esc_attr($pt->name).'" '.$ck.'> '.esc_html($pt->label).' (<code>'.esc_html($pt->name).'</code>)</label>';}echo '</fieldset><p class="description">'.__('関連性の高い記事・最新記事で表示する対象の投稿タイプを選択。', 'kashiwazaki-seo-custom-404').'</p>'; }
function ksc404_settings_field_color_theme_html() { $o=get_option('ksc404_settings', ksc404_get_default_settings());$ct=$o['color_theme'];$ts=ksc404_get_color_themes();echo '<fieldset><legend class="screen-reader-text"><span>'.__('配色テーマ', 'kashiwazaki-seo-custom-404').'</span></legend>';foreach($ts as $sl=>$th){echo '<div style="margin-bottom:10px;"><label><input type="radio" name="ksc404_settings[color_theme]" value="'.esc_attr($sl).'" '.checked($ct,$sl,false).'> <strong>'.esc_html($th['name']).'</strong></label><p style="margin-top:0;margin-left:25px;font-size:0.9em;color:#666;">'.esc_html($th['description']).'</p></div>';}echo '</fieldset><p class="description">'.__('カスタム404ページの配色テーマを選択。', 'kashiwazaki-seo-custom-404').'</p>'; }

function ksc404_settings_field_redirect_found_action_html() {
    $o = get_option('ksc404_settings', ksc404_get_default_settings());
    $val = $o['redirect_found_action'];
    $options = [
        'show_default_404' => __('テーマ/サーバーのデフォルト404ページを表示', 'kashiwazaki-seo-custom-404'),
        'show_404_canonical' => __('カスタム404ページを表示 (ステータス: 404, Canonicalヘッダーで移動先を示唆)', 'kashiwazaki-seo-custom-404'),
        'show_410_canonical' => __('カスタム410ページを表示 (ステータス: 410, Canonicalヘッダーで移動先を示唆)', 'kashiwazaki-seo-custom-404'),
        'show_200_canonical' => __('カスタム404ページを表示 (ステータス: 200, Canonicalヘッダーで移動先を示唆)', 'kashiwazaki-seo-custom-404'),
        'redirect_301' => __('検知したページへ301リダイレクト (恒久的移動)', 'kashiwazaki-seo-custom-404'),
        'redirect_302' => __('検知したページへ302リダイレクト (一時的移動)', 'kashiwazaki-seo-custom-404'),
    ];
    echo '<select name="ksc404_settings[redirect_found_action]">';
    foreach ($options as $key => $label) {
        echo '<option value="' . esc_attr($key) . '" ' . selected($val, $key, false) . '>' . esc_html($label) . '</option>';
    }
    echo '</select>';
    echo '<p class="description">'.__('過去記事のスラッグ変更などで移動先が特定できた場合の動作を選択します。「Canonicalヘッダー」はSEO上有利な場合があります。', 'kashiwazaki-seo-custom-404').'</p>';
}

function ksc404_settings_field_redirect_not_found_action_html() {
    $o = get_option('ksc404_settings', ksc404_get_default_settings());
    $val = $o['redirect_not_found_action'];
    $options = [
        'show_default_404' => __('テーマ/サーバーのデフォルト404ページを表示', 'kashiwazaki-seo-custom-404'),
        'show_404' => __('カスタム404ページを表示 (ステータス: 404)', 'kashiwazaki-seo-custom-404'),
        'show_410' => __('カスタム410ページを表示 (ステータス: 410)', 'kashiwazaki-seo-custom-404'),
        'show_200' => __('カスタム404ページを表示 (ステータス: 200, ソフト404となる可能性に注意)', 'kashiwazaki-seo-custom-404'),
        'redirect_301_custom' => __('指定URLへ301リダイレクト (恒久的移動)', 'kashiwazaki-seo-custom-404'),
        'redirect_302_custom' => __('指定URLへ302リダイレクト (一時的移動)', 'kashiwazaki-seo-custom-404'),
    ];
    echo '<select name="ksc404_settings[redirect_not_found_action]">';
    foreach ($options as $key => $label) {
        echo '<option value="' . esc_attr($key) . '" ' . selected($val, $key, false) . '>' . esc_html($label) . '</option>';
    }
    echo '</select>';
    echo '<p class="description">'.__('移動先ページが見つからなかった場合の動作を選択します。「指定URLへリダイレクト」を選択した場合は、下の入力欄にURLを指定してください。「200で表示」はソフト404としてGoogleに認識される可能性があります。', 'kashiwazaki-seo-custom-404').'</p>';
}

function ksc404_settings_field_redirect_not_found_custom_url_html() {
    $o = get_option('ksc404_settings', ksc404_get_default_settings());
    $val = $o['redirect_not_found_custom_url'];
    echo '<input type="url" name="ksc404_settings[redirect_not_found_custom_url]" value="' . esc_attr($val) . '" class="regular-text" placeholder="' . esc_attr(home_url('/')) . '">';
    echo '<p class="description">'.__('「移動先ページが見つからない場合の動作」でリダイレクトを選択した場合のリダイレクト先URLです。空の場合はトップページへのリダイレクトも検討されますが、明示的な指定を推奨します。', 'kashiwazaki-seo-custom-404').'</p>';
}

function ksc404_settings_sanitize($in){
    $out = [];
    $defaults = ksc404_get_default_settings();

    $out['latest_posts_count'] = isset($in['latest_posts_count']) ? absint($in['latest_posts_count']) : $defaults['latest_posts_count'];
    $out['related_posts_count'] = isset($in['related_posts_count']) ? absint($in['related_posts_count']) : $defaults['related_posts_count'];
    $out['latest_post_types'] = isset($in['latest_post_types']) && is_array($in['latest_post_types'])
        ? array_filter(array_map('sanitize_key', $in['latest_post_types']), function($t){ return post_type_exists($t) && $t !== 'attachment'; })
        : $defaults['latest_post_types'];
    if(empty($out['latest_post_types'])){ $out['latest_post_types'] = ['post']; }

    $out['color_theme'] = isset($in['color_theme']) && array_key_exists(sanitize_key($in['color_theme']), ksc404_get_color_themes())
        ? sanitize_key($in['color_theme'])
        : $defaults['color_theme'];

    $allowed_redirect_found_actions = ['show_default_404', 'show_404_canonical', 'show_410_canonical', 'show_200_canonical', 'redirect_301', 'redirect_302'];
    $out['redirect_found_action'] = isset($in['redirect_found_action']) && in_array($in['redirect_found_action'], $allowed_redirect_found_actions)
        ? sanitize_key($in['redirect_found_action'])
        : $defaults['redirect_found_action'];

    $allowed_redirect_not_found_actions = ['show_default_404', 'show_404', 'show_410', 'show_200', 'redirect_301_custom', 'redirect_302_custom'];
    $out['redirect_not_found_action'] = isset($in['redirect_not_found_action']) && in_array($in['redirect_not_found_action'], $allowed_redirect_not_found_actions)
        ? sanitize_key($in['redirect_not_found_action'])
        : $defaults['redirect_not_found_action'];

    $out['redirect_not_found_custom_url'] = isset($in['redirect_not_found_custom_url'])
        ? esc_url_raw(trim($in['redirect_not_found_custom_url']))
        : $defaults['redirect_not_found_custom_url'];

    return $out;
}

function ksc404_settings_page_html() {
    if(!current_user_can('manage_options')){
        wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'kashiwazaki-seo-custom-404'));
    }
    echo '<div class="wrap">';
    echo '<h1>' . esc_html(get_admin_page_title()) . '</h1>';
    echo '<form action="options.php" method="post">';
    settings_fields('ksc404_settings_group');
    do_settings_sections('ksc404_settings_page');
    submit_button(__('Save Settings', 'kashiwazaki-seo-custom-404'));
    echo '</form>';
    echo '</div>';
}
?>
