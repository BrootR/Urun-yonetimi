<?php
// 404 için taksonomi terim sayfalarını engelle
add_action('template_redirect', function () {
    if (is_tax('marka')) {
        global $wp_query;
        $wp_query->set_404();
        status_header(404);
        nocache_headers();
        include(get_query_template('404'));
        exit;
    }
});
/*
Plugin Name: Products Style Boxes Pro
Description: Marka/model filtreleme, responsive satır sayısı, profesyonel pagination ve tam stil kontrolü ile gelişmiş ürün kutusu! (2025)
Version: 3.0
Author: BrootR
*/

if (!defined('ABSPATH')) exit;

// Varsayılan ayarlar
function psb_default_options() {
    return [
        // Gösterim
        'show_image' => 1,
        'show_title' => 1,
        'show_price' => 1,
        'show_button' => 1,
        'products_per_row' => 4,
        'products_per_page' => 8,
        'products_per_row_tablet' => 2,
        'products_per_row_mobile' => 1,
        // Kutu stilleri
        'box_bg' => '#fff',
        'box_color' => '#222',
        'box_border' => '#ddd',
        'box_border_width' => '1px',
        'box_radius' => '10px',
        'box_padding' => '15px',
        'box_margin' => '10px',
        'box_shadow' => '0 4px 24px #0001',
        'box_shadow_hover' => '0 8px 32px #0002',
        'box_border_none' => 0,
        // Başlık ve yazı
        'title_font' => 'Arial',
        'title_size' => '1.2em',
        'title_weight' => 'bold',
        'title_underline' => 0,
        'title_color' => '#222',
        'price_size' => '1.1em',
        'price_color' => '#008000',
        'price_bg' => '#f7f7f7',
        'desc_font' => 'inherit',
        'desc_size' => '1em',
        'desc_color' => '#333',
        // Buton
        'btn_bg' => '#1e73be',
        'btn_color' => '#fff',
        'btn_radius' => '4px',
        'btn_border' => '#1e73be',
        'btn_border_width' => '1px',
        'btn_shadow' => '0 1px 6px #0001',
        'btn_hover_bg' => '#155a8a',
        'btn_hover_color' => '#fff',
        'btn_font' => 'inherit',
        'btn_weight' => 'bold',
        // Pagination
        'pagination_bg' => '#f3f3f3',
        'pagination_color' => '#222',
        'pagination_active_bg' => '#fff',
        'pagination_active_color' => '#111',
        'pagination_border' => '#111',
        'pagination_radius' => '0px',
        'pagination_font' => 'inherit',
        'pagination_size' => '1em',
        'pagination_btn_bg' => '#fff',
        'pagination_btn_color' => '#222',
        'pagination_btn_hover_bg' => '#f3f3f3',
        'pagination_btn_hover_color' => '#1e73be',
        // Responsive
        'responsive_padding' => '8px',
        // Marka/model filtre
        'filter_enabled' => 1,
        'filter_bg' => '#f8f8f8',
        'filter_border' => '#e3e3e3',
        'filter_radius' => '12px',
        'filter_label_color' => '#222',
        'filter_input_bg' => '#fff',
        'filter_input_color' => '#222',
        'filter_input_border' => '#ccc',
        'filter_input_radius' => '7px',
        'filter_btn_bg' => '#b4b4b4',
        'filter_btn_color' => '#fff',
        'filter_btn_radius' => '8px',
        'filter_btn_weight' => 'bold',
        // Diğer
        'custom_css' => '',
        'use_google_fonts' => 0,
        'google_fonts_family' => 'Roboto',
        // Pagination aktif/pasif
        'pagination_enabled' => 1,
    ];
}

// Admin paneli menüsü
add_action('admin_menu', function() {
    add_menu_page(
        'Ürün Kutusu Ayarları',
        'Ürün Kutusu',
        'manage_options',
        'products-style-boxes',
        'psb_settings_page',
        'dashicons-grid-view'
    );
});

// Ayarları kaydet
add_action('admin_init', function() {
    register_setting('psb_options_group', 'psb_options');
});

// Ayarlar sayfası (tam hali)
function psb_settings_page() {
    $o = get_option('psb_options', psb_default_options());
    ?>
    <div class="wrap">
        <h2>Ürün Kutusu Ayarları</h2>
        <form method="post" action="options.php">
            <?php settings_fields('psb_options_group'); ?>
            <h3><b>Genel Alanlar</b></h3>
            <table class="form-table">
                <tr>
                    <th>Resim</th>
                    <td><input type="checkbox" name="psb_options[show_image]" value="1" <?php checked($o['show_image'], 1); ?>></td>
                </tr>
                <tr>
                    <th>Başlık</th>
                    <td><input type="checkbox" name="psb_options[show_title]" value="1" <?php checked($o['show_title'], 1); ?>></td>
                </tr>
                <tr>
                    <th>Fiyat</th>
                    <td><input type="checkbox" name="psb_options[show_price]" value="1" <?php checked($o['show_price'], 1); ?>></td>
                </tr>
                <tr>
                    <th>Sepete Ekle Butonu</th>
                    <td><input type="checkbox" name="psb_options[show_button]" value="1" <?php checked($o['show_button'], 1); ?>></td>
                </tr>
                <tr>
                    <th>Sayfalama (Pagination) Aktif</th>
                    <td><input type="checkbox" name="psb_options[pagination_enabled]" value="1" <?php checked($o['pagination_enabled'], 1); ?>></td>
                </tr>
                <tr>
                    <th>Marka/Model Filtre Aktif</th>
                    <td><input type="checkbox" name="psb_options[filter_enabled]" value="1" <?php checked($o['filter_enabled'], 1); ?>></td>
                </tr>
                <tr>
                    <th>Bir Satırda Kutu</th>
                    <td><input type="number" min="1" max="12" name="psb_options[products_per_row]" value="<?php echo esc_attr($o['products_per_row']); ?>"></td>
                </tr>
                <tr>
                    <th>Bir Sayfa Ürün</th>
                    <td><input type="number" min="1" max="48" name="psb_options[products_per_page]" value="<?php echo esc_attr($o['products_per_page']); ?>"></td>
                </tr>
                <tr>
                    <th>Tablet Satırda Kutu</th>
                    <td><input type="number" min="1" max="6" name="psb_options[products_per_row_tablet]" value="<?php echo esc_attr($o['products_per_row_tablet']); ?>"></td>
                </tr>
                <tr>
                    <th>Mobil Satırda Kutu</th>
                    <td><input type="number" min="1" max="4" name="psb_options[products_per_row_mobile]" value="<?php echo esc_attr($o['products_per_row_mobile']); ?>"></td>
                </tr>
            </table>
            <h3><b>Kutu ve Yazı Stilleri</b></h3>
            <table class="form-table">
                <tr>
                    <th>Kutu Arkaplan</th><td><input type="color" name="psb_options[box_bg]" value="<?php echo esc_attr($o['box_bg']); ?>"></td>
                    <th>Kutu Yazı Rengi</th><td><input type="color" name="psb_options[box_color]" value="<?php echo esc_attr($o['box_color']); ?>"></td>
                </tr>
                <tr>
                    <th>Kutu Çerçeve Rengi</th><td><input type="color" name="psb_options[box_border]" value="<?php echo esc_attr($o['box_border']); ?>"></td>
                    <th>Kutu Çerçeve Kalınlığı</th><td><input type="text" name="psb_options[box_border_width]" value="<?php echo esc_attr($o['box_border_width']); ?>"></td>
                </tr>
                <tr>
                    <th>Kutu Kenarlık None</th><td><input type="checkbox" name="psb_options[box_border_none]" value="1" <?php checked($o['box_border_none'], 1); ?>></td>
                    <th>Kutuda Gölge</th><td><input type="text" name="psb_options[box_shadow]" value="<?php echo esc_attr($o['box_shadow']); ?>"> <small>Örn: 0 4px 24px #0001</small></td>
                </tr>
                <tr>
                    <th>Hover Gölge</th><td><input type="text" name="psb_options[box_shadow_hover]" value="<?php echo esc_attr($o['box_shadow_hover']); ?>"></td>
                    <th>Kutu Radius</th><td><input type="text" name="psb_options[box_radius]" value="<?php echo esc_attr($o['box_radius']); ?>"></td>
                </tr>
                <tr>
                    <th>Kutu Padding</th><td><input type="text" name="psb_options[box_padding]" value="<?php echo esc_attr($o['box_padding']); ?>"></td>
                    <th>Kutu Margin</th><td><input type="text" name="psb_options[box_margin]" value="<?php echo esc_attr($o['box_margin']); ?>"></td>
                </tr>
                <tr>
                    <th>Responsive Padding</th><td><input type="text" name="psb_options[responsive_padding]" value="<?php echo esc_attr($o['responsive_padding']); ?>"></td>
                </tr>
                <tr>
                    <th>Başlık Font</th><td><input type="text" name="psb_options[title_font]" value="<?php echo esc_attr($o['title_font']); ?>"></td>
                    <th>Başlık Boyutu</th><td><input type="text" name="psb_options[title_size]" value="<?php echo esc_attr($o['title_size']); ?>"></td>
                </tr>
                <tr>
                    <th>Başlık Kalınlığı</th><td><input type="text" name="psb_options[title_weight]" value="<?php echo esc_attr($o['title_weight']); ?>"></td>
                    <th>Başlık Alt Çizgi</th><td><input type="checkbox" name="psb_options[title_underline]" value="1" <?php checked($o['title_underline'], 1); ?>></td>
                </tr>
                <tr>
                    <th>Başlık Rengi</th><td><input type="color" name="psb_options[title_color]" value="<?php echo esc_attr($o['title_color']); ?>"></td>
                    <th>Açıklama Font</th><td><input type="text" name="psb_options[desc_font]" value="<?php echo esc_attr($o['desc_font']); ?>"></td>
                </tr>
                <tr>
                    <th>Açıklama Boyutu</th><td><input type="text" name="psb_options[desc_size]" value="<?php echo esc_attr($o['desc_size']); ?>"></td>
                    <th>Açıklama Rengi</th><td><input type="color" name="psb_options[desc_color]" value="<?php echo esc_attr($o['desc_color']); ?>"></td>
                </tr>
                <tr>
                    <th>Fiyat Boyutu</th><td><input type="text" name="psb_options[price_size]" value="<?php echo esc_attr($o['price_size']); ?>"></td>
                    <th>Fiyat Rengi</th><td><input type="color" name="psb_options[price_color]" value="<?php echo esc_attr($o['price_color']); ?>"></td>
                </tr>
                <tr>
                    <th>Fiyat Arka Planı</th><td><input type="color" name="psb_options[price_bg]" value="<?php echo esc_attr($o['price_bg']); ?>"></td>
                </tr>
            </table>
            <h3><b>Buton Stilleri</b></h3>
            <table class="form-table">
                <tr>
                    <th>Buton Arka Plan</th><td><input type="color" name="psb_options[btn_bg]" value="<?php echo esc_attr($o['btn_bg']); ?>"></td>
                    <th>Buton Yazı Rengi</th><td><input type="color" name="psb_options[btn_color]" value="<?php echo esc_attr($o['btn_color']); ?>"></td>
                </tr>
                <tr>
                    <th>Buton Radius</th><td><input type="text" name="psb_options[btn_radius]" value="<?php echo esc_attr($o['btn_radius']); ?>"></td>
                    <th>Buton Kenarlık Rengi</th><td><input type="color" name="psb_options[btn_border]" value="<?php echo esc_attr($o['btn_border']); ?>"></td>
                </tr>
                <tr>
                    <th>Buton Kenarlık Kalınlığı</th><td><input type="text" name="psb_options[btn_border_width]" value="<?php echo esc_attr($o['btn_border_width']); ?>"></td>
                    <th>Buton Font</th><td><input type="text" name="psb_options[btn_font]" value="<?php echo esc_attr($o['btn_font']); ?>"></td>
                </tr>
                <tr>
                    <th>Buton Ağırlığı</th><td><input type="text" name="psb_options[btn_weight]" value="<?php echo esc_attr($o['btn_weight']); ?>"></td>
                    <th>Buton Gölge</th><td><input type="text" name="psb_options[btn_shadow]" value="<?php echo esc_attr($o['btn_shadow']); ?>"></td>
                </tr>
                <tr>
                    <th>Buton Hover BG</th><td><input type="color" name="psb_options[btn_hover_bg]" value="<?php echo esc_attr($o['btn_hover_bg']); ?>"></td>
                    <th>Buton Hover Renk</th><td><input type="color" name="psb_options[btn_hover_color]" value="<?php echo esc_attr($o['btn_hover_color']); ?>"></td>
                </tr>
            </table>
            <h3><b>Pagination Stilleri</b></h3>
            <table class="form-table">
                <tr>
                    <th>Arkaplan</th><td><input type="color" name="psb_options[pagination_bg]" value="<?php echo esc_attr($o['pagination_bg']); ?>"></td>
                    <th>Yazı Rengi</th><td><input type="color" name="psb_options[pagination_color]" value="<?php echo esc_attr($o['pagination_color']); ?>"></td>
                </tr>
                <tr>
                    <th>Aktif Arkaplan</th><td><input type="color" name="psb_options[pagination_active_bg]" value="<?php echo esc_attr($o['pagination_active_bg']); ?>"></td>
                    <th>Aktif Yazı Rengi</th><td><input type="color" name="psb_options[pagination_active_color]" value="<?php echo esc_attr($o['pagination_active_color']); ?>"></td>
                </tr>
                <tr>
                    <th>Köşe Radius</th><td><input type="text" name="psb_options[pagination_radius]" value="<?php echo esc_attr($o['pagination_radius']); ?>"></td>
                    <th>Border Rengi</th><td><input type="color" name="psb_options[pagination_border]" value="<?php echo esc_attr($o['pagination_border']); ?>"></td>
                </tr>
                <tr>
                    <th>Font</th><td><input type="text" name="psb_options[pagination_font]" value="<?php echo esc_attr($o['pagination_font']); ?>"></td>
                    <th>Yazı Boyutu</th><td><input type="text" name="psb_options[pagination_size]" value="<?php echo esc_attr($o['pagination_size']); ?>"></td>
                </tr>
                <tr>
                    <th>Buton BG</th><td><input type="color" name="psb_options[pagination_btn_bg]" value="<?php echo esc_attr($o['pagination_btn_bg']); ?>"></td>
                    <th>Buton Renk</th><td><input type="color" name="psb_options[pagination_btn_color]" value="<?php echo esc_attr($o['pagination_btn_color']); ?>"></td>
                </tr>
                <tr>
                    <th>Buton Hover BG</th><td><input type="color" name="psb_options[pagination_btn_hover_bg]" value="<?php echo esc_attr($o['pagination_btn_hover_bg']); ?>"></td>
                    <th>Buton Hover Renk</th><td><input type="color" name="psb_options[pagination_btn_hover_color]" value="<?php echo esc_attr($o['pagination_btn_hover_color']); ?>"></td>
                </tr>
            </table>
            <h3><b>Marka/Model Filtre Stilleri</b></h3>
            <table class="form-table">
                <tr>
                    <th>Filtre Kutusu BG</th><td><input type="color" name="psb_options[filter_bg]" value="<?php echo esc_attr($o['filter_bg']); ?>"></td>
                    <th>Filtre Kutusu Border</th><td><input type="color" name="psb_options[filter_border]" value="<?php echo esc_attr($o['filter_border']); ?>"></td>
                </tr>
                <tr>
                    <th>Filtre Radius</th><td><input type="text" name="psb_options[filter_radius]" value="<?php echo esc_attr($o['filter_radius']); ?>"></td>
                    <th>Label Renk</th><td><input type="color" name="psb_options[filter_label_color]" value="<?php echo esc_attr($o['filter_label_color']); ?>"></td>
                </tr>
                <tr>
                    <th>Input BG</th><td><input type="color" name="psb_options[filter_input_bg]" value="<?php echo esc_attr($o['filter_input_bg']); ?>"></td>
                    <th>Input Renk</th><td><input type="color" name="psb_options[filter_input_color]" value="<?php echo esc_attr($o['filter_input_color']); ?>"></td>
                </tr>
                <tr>
                    <th>Input Border</th><td><input type="color" name="psb_options[filter_input_border]" value="<?php echo esc_attr($o['filter_input_border']); ?>"></td>
                    <th>Input Radius</th><td><input type="text" name="psb_options[filter_input_radius]" value="<?php echo esc_attr($o['filter_input_radius']); ?>"></td>
                </tr>
                <tr>
                    <th>Buton BG</th><td><input type="color" name="psb_options[filter_btn_bg]" value="<?php echo esc_attr($o['filter_btn_bg']); ?>"></td>
                    <th>Buton Renk</th><td><input type="color" name="psb_options[filter_btn_color]" value="<?php echo esc_attr($o['filter_btn_color']); ?>"></td>
                </tr>
                <tr>
                    <th>Buton Radius</th><td><input type="text" name="psb_options[filter_btn_radius]" value="<?php echo esc_attr($o['filter_btn_radius']); ?>"></td>
                    <th>Buton Kalınlık</th><td><input type="text" name="psb_options[filter_btn_weight]" value="<?php echo esc_attr($o['filter_btn_weight']); ?>"></td>
                </tr>
            </table>
            <h3><b>Ekstra</b></h3>
            <table class="form-table">
                <tr>
                    <th>Google Fonts Kullan</th>
                    <td><input type="checkbox" name="psb_options[use_google_fonts]" value="1" <?php checked($o['use_google_fonts'], 1); ?>></td>
                    <th>Google Font Ailesi</th>
                    <td><input type="text" name="psb_options[google_fonts_family]" value="<?php echo esc_attr($o['google_fonts_family']); ?>"></td>
                </tr>
                <tr>
                    <th>Ekstra CSS</th>
                    <td colspan="3"><textarea name="psb_options[custom_css]" rows="4" style="width:70%"><?php echo esc_textarea($o['custom_css']); ?></textarea></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Google Fonts ekle
add_action('wp_head', function() {
    $o = get_option('psb_options', psb_default_options());
    if (!empty($o['use_google_fonts']) && !empty($o['google_fonts_family'])) {
        $family = str_replace(' ', '+', $o['google_fonts_family']);
        echo '<link href="https://fonts.googleapis.com/css?family='.$family.':400,700&display=swap" rel="stylesheet">';
    }
});

// Frontend CSS
add_action('wp_head', function() {
    $o = get_option('psb_options', psb_default_options());
    $columns = max(1, min(12, intval($o['products_per_row'])));
    $columns_tablet = max(1, min(6, intval($o['products_per_row_tablet'])));
    $columns_mobile = max(1, min(4, intval($o['products_per_row_mobile'])));
    $gap = 20;
    $border = !empty($o['box_border_none']) ? 'none' : $o['box_border_width'].' solid '.$o['box_border'];
    $family = (!empty($o['use_google_fonts']) && !empty($o['google_fonts_family'])) ? $o['google_fonts_family'] : 'inherit';
    ?>
    <style>
    .psb-product-grid {
        display: grid;
        grid-template-columns: repeat(<?php echo $columns; ?>, 1fr);
        gap: <?php echo $gap; ?>px;
        margin-bottom: 30px;
    }
    @media(max-width: 900px) {
        .psb-product-grid { grid-template-columns: repeat(<?php echo $columns_tablet; ?>, 1fr); }
    }
    @media(max-width: 600px) {
        .psb-product-grid { grid-template-columns: repeat(<?php echo $columns_mobile; ?>, 1fr); }
    }
    .psb-product-box {
        background: <?php echo esc_attr($o['box_bg']); ?>;
        color: <?php echo esc_attr($o['box_color']); ?>;
        border: <?php echo $border; ?>;
        border-radius: <?php echo esc_attr($o['box_radius']); ?>;
        padding: <?php echo esc_attr($o['box_padding']); ?>;
        margin: <?php echo esc_attr($o['box_margin']); ?>;
        box-shadow: <?php echo esc_attr($o['box_shadow']); ?>;
        text-align: center;
        transition: box-shadow .2s, border .2s;
        font-family: <?php echo esc_attr($family); ?>;
        box-sizing: border-box;
        min-width: 0;
    }
    .psb-product-box:hover {
        box-shadow: <?php echo esc_attr($o['box_shadow_hover']); ?>;
    }
    .psb-product-box h3 {
        margin: 10px 0 5px;
        font-size: <?php echo esc_attr($o['title_size']); ?>;
        font-family: <?php echo esc_attr($o['title_font']); ?>,<?php echo esc_attr($family); ?>;
        font-weight: <?php echo esc_attr($o['title_weight']); ?>;
        color: <?php echo esc_attr($o['title_color']); ?>;
        text-decoration: <?php echo (!empty($o['title_underline']) ? 'underline' : 'none'); ?>;
    }
    .psb-product-box .psb-desc {
        font-family: <?php echo esc_attr($o['desc_font']); ?>,<?php echo esc_attr($family); ?>;
        font-size: <?php echo esc_attr($o['desc_size']); ?>;
        color: <?php echo esc_attr($o['desc_color']); ?>;
        margin-bottom: 10px;
        opacity: .95;
    }
    .psb-product-box .psb-price {
        font-size: <?php echo esc_attr($o['price_size']); ?>;
        color: <?php echo esc_attr($o['price_color']); ?>;
        background: <?php echo esc_attr($o['price_bg']); ?>;
        font-weight: bold;
        display: inline-block;
        margin: 10px 0;
        padding: 6px 14px;
        border-radius: 3px;
    }
    .psb-product-box .psb-btn {
        display: inline-block;
        padding: 10px 26px;
        border-radius: <?php echo esc_attr($o['btn_radius']); ?>;
        background: <?php echo esc_attr($o['btn_bg']); ?>;
        color: <?php echo esc_attr($o['btn_color']); ?>;
        border: <?php echo esc_attr($o['btn_border_width']).' solid '.esc_attr($o['btn_border']); ?>;
        text-decoration: none;
        margin-top: 10px;
        font-family: <?php echo esc_attr($o['btn_font']); ?>,<?php echo esc_attr($family); ?>;
        font-weight: <?php echo esc_attr($o['btn_weight']); ?>;
        box-shadow: <?php echo esc_attr($o['btn_shadow']); ?>;
        font-size: 1em;
        transition: background .2s, color .2s;
        cursor: pointer;
    }
    .psb-product-box .psb-btn:hover {
        background: <?php echo esc_attr($o['btn_hover_bg']); ?>;
        color: <?php echo esc_attr($o['btn_hover_color']); ?>;
    }
    @media(max-width: 600px) {
        .psb-product-box {
            padding: <?php echo esc_attr($o['responsive_padding']); ?>;
        }
    }

    /* Marka/Model filtre */
    .psb-filter-bar {
        background: <?php echo esc_attr($o['filter_bg']); ?>;
        border: 1.5px solid <?php echo esc_attr($o['filter_border']); ?>;
        border-radius: <?php echo esc_attr($o['filter_radius']); ?>;
        padding: 18px 25px;
        margin: 25px 0 28px 0;
        display: flex;
        align-items: center;
        gap: 18px;
        flex-wrap: wrap;
        box-sizing: border-box;
    }
    .psb-filter-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
        min-width: 170px;
        flex: 1 0 170px;
    }
    .psb-filter-label {
        color: <?php echo esc_attr($o['filter_label_color']); ?>;
        font-size: 1em;
        margin-bottom: 2px;
        font-weight: 500;
    }
    .psb-filter-select {
        background: <?php echo esc_attr($o['filter_input_bg']); ?>;
        color: <?php echo esc_attr($o['filter_input_color']); ?>;
        border: 1.5px solid <?php echo esc_attr($o['filter_input_border']); ?>;
        border-radius: <?php echo esc_attr($o['filter_input_radius']); ?>;
        padding: 7px 12px;
        font-size: 1em;
        font-family: inherit;
        outline: none;
        min-width: 120px;
        transition: border .16s;
    }
    .psb-filter-select:focus {
        border-color: <?php echo esc_attr($o['btn_bg']); ?>;
    }
    .psb-filter-btn {
        background: <?php echo esc_attr($o['filter_btn_bg']); ?>;
        color: <?php echo esc_attr($o['filter_btn_color']); ?>;
        border: none;
        border-radius: <?php echo esc_attr($o['filter_btn_radius']); ?>;
        font-weight: <?php echo esc_attr($o['filter_btn_weight']); ?>;
        font-size: 1em;
        padding: 10px 35px;
        margin-left: 16px;
        cursor: pointer;
        transition: background .16s;
        white-space: nowrap;
    }
    .psb-filter-btn:active {
        filter: brightness(0.95);
    }
    /* Responsive filtre bar */
    @media(max-width: 800px) {
        .psb-filter-bar { flex-direction: column; align-items: stretch; gap: 12px; padding: 16px 8px; }
        .psb-filter-btn { margin-left: 0; width: 100%; }
    }
.psb-pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0;
    margin: 32px 0 0 0;
    font-family: inherit;
    font-size: 1.1em;
    flex-wrap: nowrap;
    max-width: 100vw;
    overflow-x: auto;
    padding: 0 4vw;
    min-height: 56px;
    border-radius: 8px;
    box-sizing: border-box;
    scrollbar-width: thin;
    scrollbar-color: #bbb #f3f3f3;
}
.psb-pagination .psb-page-btn,
.psb-pagination .psb-page-active,
.psb-pagination .psb-page-dots {
    min-width: 42px;
    width: 42px;
    height: 42px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 2px;
    background: #fff;
    color: #222;
    border: 1px solid #111;
    border-radius: 5px;
    text-align: center;
    font-weight: 500;
    cursor: pointer;
    box-sizing: border-box;
    user-select: none;
    font-size: 1.15em;
    overflow: hidden;
    text-overflow: ellipsis;
    outline: none;
    transition: background .16s, color .16s;
}
.psb-pagination .psb-page-active {
    background: #fff;
    color: #111;
    border: 2px solid #111;
    font-weight: 600;
    box-shadow: 0 0 0 2px #fff, 0 0 4px #2222;
}
.psb-pagination .psb-page-btn:hover {
    background: #f3f3f3;
    color: #1e73be;
}
.psb-pagination .psb-page-dots {
    font-weight: 900;
    pointer-events: none;
    background: #fff;
    color: #888;
	border-radius: 5px;
    border: 1px solid #111;
    font-size: 1.5em;
    letter-spacing: 0;
    align-items: center;
    justify-content: center;
}
.psb-pagination .psb-page-btn[aria-label], .psb-pagination .psb-page-btn[aria-label]:hover {
    background: #fff;
    color: #111;
    border: 1px solid #111;
	border-radius: 5px;
    font-size: 1.4em;
    min-width: 42px;
    width: 42px;
}
@media (max-width: 600px) {
    .psb-pagination {
        padding: 0 1vw;
        font-size: 1em;
        min-height: 42px;
    }
    .psb-pagination .psb-page-btn,
    .psb-pagination .psb-page-active,
    .psb-pagination .psb-page-dots {
        min-width: 34px;
        width: 34px;
        height: 34px;
        font-size: 1em;
    }
    .psb-pagination .psb-page-dots {
        font-size: 1.2em;
    }
}
    <?php if (trim($o['custom_css'])) echo $o['custom_css']; ?>
    </style>
    <?php
});

// AJAX: Alt kategori (model) getir
add_action('wp_ajax_psb_get_models', 'psb_get_models_ajax');
add_action('wp_ajax_nopriv_psb_get_models', 'psb_get_models_ajax');
function psb_get_models_ajax() {
    $parent = intval($_POST['parent']);
    $models = get_terms([
        'taxonomy' => 'marka',
        'hide_empty' => false,
        'parent' => $parent
    ]);
    $out = [];
    foreach($models as $model) {
        $out[] = ['id'=>$model->term_id, 'name'=>$model->name];
    }
    wp_send_json($out);
}

// Shortcode: [products_box filter="1/0" pagination="1/0" ...]
add_shortcode('products_box', function($atts) {
    $o = get_option('psb_options', psb_default_options());
    $atts = shortcode_atts([
        'category' => '',
        'orderby' => 'date',
        'order' => 'DESC',
        'per_page' => $o['products_per_page'],
        'per_row' => $o['products_per_row'],
        'per_row_tablet' => $o['products_per_row_tablet'],
        'per_row_mobile' => $o['products_per_row_mobile'],
        'pagination' => '', // override
        'filter' => '',  // override
    ], $atts);

    // Filtre ve pagination ayarı: shortcode'da varsa onu al, yoksa global ayar
    $filter_enabled = ($atts['filter'] !== '') ? (intval($atts['filter']) ? 1 : 0) : (!empty($o['filter_enabled']) ? 1 : 0);
    $pagination_enabled = ($atts['pagination'] !== '') ? (intval($atts['pagination']) ? 1 : 0) : (!empty($o['pagination_enabled']) ? 1 : 0);

    // GET parametreleri
    $current_marka = isset($_GET['psb_marka']) ? intval($_GET['psb_marka']) : 0;
    $current_model = isset($_GET['psb_model']) ? intval($_GET['psb_model']) : 0;
    $paged = ($pagination_enabled && isset($_GET['product_page'])) ? max(1, intval($_GET['product_page'])) : 1;

    ob_start();
    // Filtre barı
    if ($filter_enabled) {
        // Kök markalar (parent=0)
        $brands = get_terms(['taxonomy' => 'marka', 'hide_empty' => false, 'parent' => 0]);
        // Mevcut marka seçiliyse modelleri getir
        $models = [];
        if ($current_marka) {
            $models = get_terms(['taxonomy'=>'marka', 'hide_empty'=>false, 'parent'=>$current_marka]);
        }
        ?>
        <form class="psb-filter-bar" method="get" id="psb-filter-form">
            <div class="psb-filter-item">
                <label class="psb-filter-label" for="psb-marka-select">Marka:</label>
                <select class="psb-filter-select" id="psb-marka-select" name="psb_marka">
                    <option value="0">Seçiniz</option>
                    <?php foreach($brands as $brand): ?>
                        <option value="<?php echo $brand->term_id; ?>" <?php selected($current_marka, $brand->term_id); ?>>
                            <?php echo esc_html($brand->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="psb-filter-item">
                <label class="psb-filter-label" for="psb-model-select">Model:</label>
                <select class="psb-filter-select" id="psb-model-select" name="psb_model">
                    <option value="0"><?php echo $current_marka ? 'Tümü' : 'Önce marka seçiniz'; ?></option>
                    <?php if($current_marka && $models) foreach($models as $model): ?>
                        <option value="<?php echo $model->term_id; ?>" <?php selected($current_model, $model->term_id); ?>>
                            <?php echo esc_html($model->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="psb-filter-btn">Ürünleri Göster</button>
        </form>
        <script>
        // AJAX ile marka seçilince model dropdownu yenile
        document.addEventListener('DOMContentLoaded', function(){
            var markaSel = document.getElementById('psb-marka-select');
            var modelSel = document.getElementById('psb-model-select');
            markaSel.addEventListener('change', function(){
                var v = this.value;
                modelSel.innerHTML = '<option value="0">Yükleniyor...</option>';
                if(v==0) {
                    modelSel.innerHTML = '<option value="0">Önce marka seçiniz</option>';
                } else {
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', '<?php echo admin_url('admin-ajax.php'); ?>', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onload = function(){
                        var data = JSON.parse(this.responseText);
                        var opt = '<option value="0">Tümü</option>';
                        data.forEach(function(m){
                            opt += '<option value="'+m.id+'">'+m.name+'</option>';
                        });
                        modelSel.innerHTML = opt;
                    };
                    xhr.send('action=psb_get_models&parent='+v);
                }
            });
        });
        </script>
        <?php
    }

    // Sorgu
    $args = [
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => intval($atts['per_page']),
        'orderby' => $atts['orderby'],
        'order' => $atts['order'],
        'paged' => $paged,
    ];
    // Marka/model taksonomilerine göre filtrele
    if ($current_model) {
        $args['tax_query'] = [[
            'taxonomy' => 'marka',
            'field' => 'term_id',
            'terms' => $current_model,
        ]];
    } elseif ($current_marka) {
        // Hem marka hem alt modelleri dahil et
        $childs = get_terms(['taxonomy'=>'marka','parent'=>$current_marka,'hide_empty'=>false]);
        $ids = [$current_marka];
        foreach($childs as $c) $ids[] = $c->term_id;
        $args['tax_query'] = [[
            'taxonomy' => 'marka',
            'field' => 'term_id',
            'terms' => $ids,
        ]];
    }
    if ($atts['category']) {
        // product_cat ile de filtrele
        $args['tax_query'][] = [
            'taxonomy' => 'product_cat',
            'field' => 'slug',
            'terms' => $atts['category'],
        ];
    }

    // Dinamik responsive için grid ayarlarını data attribute olarak yaz
    echo '<div class="psb-product-grid" 
        data-cols="'.intval($atts['per_row']).'"
        data-cols-tablet="'.intval($atts['per_row_tablet']).'"
        data-cols-mobile="'.intval($atts['per_row_mobile']).'"
    >';

    $q = new WP_Query($args);
    if ($q->have_posts()) {
        while ($q->have_posts()) : $q->the_post();
            $product = wc_get_product(get_the_ID());
            echo '<div class="psb-product-box">';
            if ($o['show_image'] && has_post_thumbnail()) {
                echo '<a href="'.get_permalink().'">'.get_the_post_thumbnail(get_the_ID(), 'medium').'</a>';
            }
            if ($o['show_title']) {
                echo '<h3><a href="'.get_permalink().'" style="color:inherit;text-decoration:none;">'.get_the_title().'</a></h3>';
            }
            if ($o['show_price']) {
                echo '<span class="psb-price">'.$product->get_price_html().'</span>';
            }
            if ($o['show_button']) {
                echo '<a class="psb-btn" href="?add-to-cart='.get_the_ID().'">'.__('Sepete Ekle','woocommerce').'</a>';
            }
            echo '</div>';
        endwhile;
    } else {
        echo '<div>Ürün bulunamadı.</div>';
    }
    echo '</div>';
    // Pagination (gösterişli, kutulu, < > ok, ... kutusu)
    if ($pagination_enabled) {
        $total_pages = $q->max_num_pages;
        if ($total_pages > 1) {
            $current_page = $paged;
            $max_visible = 3;
            $range = 1;
            $output = '<div class="psb-pagination">';

            // Prev
            if ($current_page > 1) {
                $url = add_query_arg('product_page', $current_page-1);
                $output .= '<a class="psb-page-btn" href="'.$url.'" aria-label="Önceki">&lt;</a>';
            }

            // Sayfa kutuları (kısa, kutulu ve ... kutusu)
            if ($total_pages <= $max_visible + 2) {
                for ($i = 1; $i <= $total_pages; $i++) {
                    if ($i == $current_page) {
                        $output .= '<span class="psb-page-active">'.$i.'</span>';
                    } else {
                        $url = add_query_arg('product_page', $i);
                        $output .= '<a class="psb-page-btn" href="'.$url.'">'.$i.'</a>';
                    }
                }
            } else {
                // 1. kutu
                if ($current_page == 1) {
                    $output .= '<span class="psb-page-active">1</span>';
                } else {
                    $output .= '<a class="psb-page-btn" href="'.add_query_arg('product_page', 1).'">1</a>';
                }

                // ... kutusu (sol)
                if ($current_page > ($range + 2)) {
                    $output .= '<span class="psb-page-dots">...</span>';
                }

                // Orta kutular
                $start = max(2, $current_page - $range);
                $end = min($total_pages - 1, $current_page + $range);

                // Sol yakınındaysa baştan başlat
                if ($current_page <= ($range + 2)) {
                    $start = 2;
                    $end = 2 + $range * 2;
                }
                // Sağ yakınındaysa sona çek
                if ($current_page >= $total_pages - ($range + 1)) {
                    $start = $total_pages - $range * 2 - 1;
                    $end = $total_pages - 1;
                }

                for ($i = $start; $i <= $end; $i++) {
                    if ($i > 1 && $i < $total_pages) {
                        if ($i == $current_page) {
                            $output .= '<span class="psb-page-active">'.$i.'</span>';
                        } else {
                            $url = add_query_arg('product_page', $i);
                            $output .= '<a class="psb-page-btn" href="'.$url.'">'.$i.'</a>';
                        }
                    }
                }

                // ... kutusu (sağ)
                if ($current_page < $total_pages - ($range + 1)) {
                    $output .= '<span class="psb-page-dots">...</span>';
                }

                // Son kutu
                if ($total_pages > 1) {
                    if ($current_page == $total_pages) {
                        $output .= '<span class="psb-page-active">'.$total_pages.'</span>';
                    } else {
                        $output .= '<a class="psb-page-btn" href="'.add_query_arg('product_page', $total_pages).'">'.$total_pages.'</a>';
                    }
                }
            }

            // Next
            if ($current_page < $total_pages) {
                $url = add_query_arg('product_page', $current_page+1);
                $output .= '<a class="psb-page-btn" href="'.$url.'" aria-label="Sonraki">&gt;</a>';
            }

            $output .= '</div>';
            echo $output;
        }
    }
    wp_reset_postdata();
    return ob_get_clean();
});