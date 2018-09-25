<?php

namespace Pluginever\WcVariationSwatches\Admin;

class Term_Meta {

    private $taxonomy;
    private $attribute_type;
    private $attr_name;

    public function __construct($taxonomy, $attribute_type, $attr_name) {

        $this->taxonomy       = $taxonomy;
        $this->attribute_type = $attribute_type;
        $this->attr_name      = $attr_name;

        add_action( "{$this->taxonomy}_add_form_fields", array( $this, 'taxonomy_add_new_meta_field'), 10, 1);
        add_action( "{$this->taxonomy}_edit_form_fields", array( $this, 'edit_taxonomy_meta_field'), 10, 1);
        add_action( "created_{$this->taxonomy}", array( $this, 'save_taxonomy_custom_meta'), 10, 2);
        add_action( "edited_{$this->taxonomy}", array( $this, 'save_taxonomy_custom_meta'), 10, 2);
        
    }

    /**
     * Add Product Taxonomy Meta.
     */

    public function taxonomy_add_new_meta_field(){

        if ($this->attribute_type === 'image'){
    ?>
        <div class="form-field">
            <label for="term_meta_image"><?php _e( 'Image', 'wc-variation-swatches' ); ?></label>
            <input type="file" class="term_meta_image" name="term_meta_image" id="term_meta_image" required>
            <p class="description"><?php _e( 'Upload Image','wc-variation-swatches' ); ?></p>
        </div>

    <?php
        } else if ($this->attribute_type === 'color'){
    ?>
        <div class="form-field">
            <label for="term_meta_color"><?php _e( 'Color', 'wc-variation-swatches' ); ?></label>
            <input type="text" id='color-picker' class="taxonomy-color-field" name="color" value="#dd3333">
            <p class="description"><?php _e( 'Select A Color','wc-variation-swatches' ); ?></p>
        </div>
    <?php

        } else {

        }
    }

   public function edit_taxonomy_meta_field( $term ) {
    ?>
        <tr class="form-field">
            <th scope="row" valign="top"><label for="term_meta_image"><?php _e( 'Image', 'wc-variation-swatches' ); ?></label></th>
            <td>
                <input type="file" name="term_meta_image" id="term_meta_image" value="<?php echo esc_url( get_term_meta( $term->term_id, 'image', true ) ); ?>" />
            </td>
        </tr>
        
    <?php
    }

    public function save_taxonomy_custom_meta($term_id){
        
        $old_image         = get_term_meta( $term_id, 'image', true );
        $term_meta_image      = esc_url( $_POST['term_meta_image'] );
        update_term_meta( $term_id, 'image', $term_meta_image, $old_image );
        // $new_twitter = esc_url( $_POST['twitter'] );


        // if ($this->attribute_type === 'image'){
        //     error_log('image completed');
        //     $image   = empty($_POST['term_meta_image']) ? $_POST['term_meta_image'] : null;
        //     update_term_meta( $term_id, '_image', $image );

        // } else if ($this->attribute_type === 'color'){
        //     error_log('color completed');
        //     $color   = isset( $_POST['color'] ) ? $_POST['color'] : null;
        //     update_term_meta( $term_id, '_color', $color );

        // } else{

        // }
        
        
    }
}
















// function save_vendor_custom_fields( $term_id ) {
//     $old_fb      = get_term_meta( $term_id, 'facebook', true );
//     $old_twitter = get_term_meta( $term_id, 'twitter', true );
//     $new_fb      = esc_url( $_POST['facebook'] );
//     $new_twitter = esc_url( $_POST['twitter'] );
//     if ( ! empty( $old_fb ) && $new_fb === '' ) {
//         delete_term_meta( $term_id, 'facebook' );
//     } else if ( $old_fb !== $new_fb ) {
//         update_term_meta( $term_id, 'facebook', $new_fb, $old_fb );
//     }
//     if ( ! empty( $old_twitter ) && $new_twitter === '' ) {
//         delete_term_meta( $term_id, 'twitter' );
//     } else if ( $old_twitter !== $new_twitter ) {
//         update_term_meta( $term_id, 'twitter', $new_twitter, $old_twitter );
//     }
// }
