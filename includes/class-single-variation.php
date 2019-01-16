<?php

namespace Pluginever\WCVariationSwatches;

class Single_Variation {

    public function __construct() {
        add_action('init', array($this, 'initialize_settings'), 10, 1);
    }

    public function initialize_settings() {
        $simple_settings = get_option('wc_variation_swatches_simple');

        if ($simple_settings == '') {
            $simple_settings = array(
                'enable_tooltip'    => 'on',
                'enable_stylesheet' => 'on',
                'shape_style'       => 'round'
            );
        }

        $stylesheet = esc_html($simple_settings['enable_stylesheet']);

        if ($stylesheet === 'on') {
            add_filter('woocommerce_dropdown_variation_attribute_options_html', array($this, 'render_variable_swatch_style'), 100, 2);
            add_filter('variation_swatch_render_html', array($this, 'swatch_single_page_variation_html'), 5, 4);
        }
    }

    public function render_variable_swatch_style($html, $args) {
        $types = wc_variation_swatches_types();
        $attr  = wc_variation_swatches_get_tax_attribute($args['attribute']);

        if (empty($attr)) {
            return $html;
        }

        $options   = $args['options'];
        $product   = $args['product'];
        $attribute = $args['attribute'];
        $class     = "variation-selector variation-select-{$attr->attribute_type}";
        $swatches  = '';

        if (empty($options) && !empty($product) && !empty($attribute)) {
            $attributes = $product->get_variation_attributes();
            $options    = $attributes[$attribute];
        }

        if (array_key_exists($attr->attribute_type, $types)) {
            if (!empty($options) && $product && taxonomy_exists($attribute)) {
                $all_terms = wc_get_product_terms($product->get_id(), $attribute, array('fields' => 'all'));
                foreach ($all_terms as $term) {
                    if (in_array($term->slug, $options)) {
                        $swatches .= apply_filters('variation_swatch_render_html', '', $term, $attr, $args);
                    }
                }
            }
        }

        if (!empty($swatches)) {
            $class    = "hidden";
            $swatches = '<div class="wc-ever-swatches" data-attribute_name="attribute_' . esc_attr($attribute) . '">' . $swatches . '</div>';
            $html     = '<div class="' . esc_attr($class) . '">' . $html . '</div>' . $swatches;
        }
        return $html;
    }

    public function swatch_single_page_variation_html($html, $term, $attr, $args) {

        $selected         = sanitize_title($args['selected']) == $term->slug ? 'selected' : '';
        $name             = esc_html($term->name);
        $simple_settings  = get_option('wc_variation_swatches_simple');
        $advance_settings = get_option('wc_variation_swatches_advance');

        if ($simple_settings == '') {
            $simple_settings = array(
                'enable_tooltip'    => 'on',
                'enable_stylesheet' => 'on',
                'shape_style'       => 'round'
            );
        }

        if ($advance_settings == '') {
            $advance_settings = array(
                'width'              => '30px',
                'height'             => '30px',
                'font_size'          => '15px',
                'tooltip_bg_color'   => '#555',
                'tooltip_text_color' => '#fff',
                'border_style'       => 'enable'
            );
        }


        if (array_key_exists('shape_style', $simple_settings)) {
            $class_shapes = esc_html($simple_settings['shape_style']);
        }

        if (array_key_exists('enable_tooltip', $simple_settings)) {
            $tooltips = esc_html($simple_settings['enable_tooltip']);
        }

        if (array_key_exists('width', $advance_settings)) {
            $swatches_width = esc_html($advance_settings['width']);
        }

        if (array_key_exists('font_size', $advance_settings)) {
            $tooltip_font_size = esc_html($advance_settings['font_size']);
        }

        if (array_key_exists('height', $advance_settings)) {
            $swatches_height = esc_html($advance_settings['height']);
        }

        if (array_key_exists('tooltip_bg_color', $advance_settings)) {
            $tooltip_bg_color = esc_html($advance_settings['tooltip_bg_color']);
        }

        if (array_key_exists('tooltip_text_color', $advance_settings)) {
            $tooltip_text_color = esc_html($advance_settings['tooltip_text_color']);
        }

        if (array_key_exists('border_style', $advance_settings)) {
            $border_styles = esc_html($advance_settings['border_style']);
        }

        if (!empty($class_shapes)) {
            if ($class_shapes === 'round') {
                $class_shape       = 'round-box';
                $class_shape_image = 'round-box-image';
            } else {
                $class_shape       = 'square-box ';
                $class_shape_image = 'square-box-image';
            }
        } else {
            $class_shape       = '';
            $class_shape_image = '';
        }

        if (!empty($tooltips)) {
            if ($tooltips === 'on') {
                $tooltip_class = 'wcvs-color-tooltip';
            } else {
                $tooltip_class = 'hidden';
            }
        } else {
            $tooltip_class = 'wcvs-color-tooltip';
        }

        if (!empty($tooltip_font_size)) {
            $font_size = $tooltip_font_size;
        } else {
            $font_size = '15px';
        }

        if (!empty($swatches_width)) {
            $swatches_width = $swatches_width;
        } else {
            $swatches_width = '30px';
        }

        if (!empty($swatches_height)) {
            $swatches_height = $swatches_height;
        } else {
            $swatches_height = '30px';
        }

        if (!empty($border_styles)) {
            if ($border_styles === 'enable') {
                $border_style = 'wcvs-border-style';
            } else {
                $border_style = 'wcvs-border-style-none';
            }
        } else {
            $border_style = 'wcvs-border-style';
        }

        switch ($attr->attribute_type) {
            case 'wcvs-color':
                $color = get_term_meta($term->term_id, 'wcvs-color', true);
                list($r, $g, $b) = sscanf($color, "#%02x%02x%02x");
                $html = sprintf(
                    '<div class="swatch %s %s wcvs-attr-enable wcvs-attr-behaviour wcvs-swatch-color swatch-%s %s" style="background-color:%s;color:%s; width:%s; height:%s;" title="%s" data-value="%s"><span class="%s" style="background-color:%s; color:%s; font-size:%s;">%s</span></div>',
                    $class_shape,
                    $border_style,
                    esc_attr($term->slug),
                    $selected,
                    esc_attr($color),
                    "rgba($r,$g,$b,0.5)",
                    esc_attr($swatches_width),
                    esc_attr($swatches_height),
                    esc_attr($name),
                    esc_attr($term->slug),
                    $tooltip_class,
                    $tooltip_bg_color,
                    $tooltip_text_color,
                    $font_size,
                    esc_attr($name)
                );
                break;

            case 'wcvs-image':
                $image = get_term_meta($term->term_id, 'wcvs-image', true);
                $image = $image ? wp_get_attachment_image_src($image) : '';
                $image = $image ? $image[0] : WC()->plugin_url() . '/assets/images/placeholder.png';

                $html = sprintf(
                    '<div style="width:%s; height:%s;" class="swatch %s %s wcvs-swatch-image swatch-%s %s" title="%s" data-value="%s"><img src="%s" alt="%s"><span class="%s" style="background-color:%s; color:%s; font-size:%s;">%s</span></div>',

                    esc_attr($swatches_width),
                    esc_attr($swatches_height),
                    $class_shape_image,
                    $border_style,
                    esc_attr($term->slug),
                    $selected,
                    esc_attr($name),
                    esc_attr($term->slug),
                    esc_url($image),
                    esc_attr($name),
                    $tooltip_class,
                    $tooltip_bg_color,
                    $tooltip_text_color,
                    $font_size,
                    esc_attr($name)
                );
                break;

            case 'wcvs-label':
                $label = get_term_meta($term->term_id, 'wcvs-label', true);
                $label = $label ? $label : $name;
                $html  = sprintf(
                    '<div style="width:%s; height: %s;" class="swatch %s wcvs-swatch-label %s swatch-%s %s" title="%s" data-value="%s">%s<span class="%s" style="background-color:%s; color:%s; font-size:%s;">%s</span></div>',
                    esc_attr($swatches_width),
                    esc_attr($swatches_height),
                    $border_style,
                    $class_shape,
                    esc_attr($term->slug),
                    $selected,
                    esc_attr($name),
                    esc_attr($term->slug),
                    esc_html($label),
                    $tooltip_class,
                    $tooltip_bg_color,
                    $tooltip_text_color,
                    $font_size,
                    esc_attr($name)
                );
                break;
        }
        return $html;
    }
}