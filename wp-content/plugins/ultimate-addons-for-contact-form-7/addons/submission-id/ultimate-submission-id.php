<?php

/** Prevent direct access */
if (!defined('ABSPATH')) {
    echo "You are not allowed to access directly";
    exit();
}

class UACF7_SUBMISSION_ID{

    public function __construct(){
        add_action('wp_enqueue_scripts', [$this, 'submission_id_public_assets_loading']);
        add_action('admin_enqueue_scripts', [$this, 'submission_id_admin_assets_loading']);

        add_action('admin_init', [$this, 'submission_tag_generator']);
        add_action('wpcf7_init', [$this, 'submission_id_add_shortcodes']);

        add_action( 'wp_ajax_uacf7_update_submission_id', [$this, 'uacf7_update_submission_id'] );
        add_action( 'wp_ajax_nopriv_uacf7_update_submission_id', [$this, 'uacf7_update_submission_id'] );
        
        add_filter('wpcf7_mail_sent', [$this, 'submission_id_update']);

        // Submission ID Update into Database
        add_action('uacf7_submission_id_insert', [$this, 'uacf7_submission_id_insert_callback'], 10, 4);

        require_once 'inc/submission-id.php';

    }

/** Starts Loading Essential JS & CSS */

    public function submission_id_public_assets_loading(){

        wp_enqueue_script('submission_id_public_js', UACF7_URL . '/addons/submission-id/assets/public/js/public-submission-id.js', ['jquery'], 'WPCF7_VERSION', true);
        wp_enqueue_style('submission_id_public_css', UACF7_URL . '/addons/submission-id/assets/public/css/public-submission-id.css', [], 'UAFC7_VERSION', true, 'all');
        wp_localize_script( 'submission_id_public_js', 'submission_id_obj', [
            "ajaxurl" => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'uacf7-submission-id-nonce' ),

        ] );
    }

    public function submission_id_admin_assets_loading(){

        wp_enqueue_script('submission_id_admin_js', UACF7_URL . '/addons/submission-id/assets/admin/js/admin-submission-id.js', ['jquery'], 'UAFC7_VERSION', true);
        wp_enqueue_style('submission_id_admin_css', UACF7_URL . '/addons/submission-id/assets/admin/css/admin-submission-id.css', [], 'UAFC7_VERSION', true, 'all');

    }

    /** Ends Loading Essential JS & CSS */



    /**
     * Submission ID Realtime update in the Frontend
     */

     public function uacf7_update_submission_id(){

        if ( !wp_verify_nonce($_POST['ajax_nonce'], 'uacf7-submission-id-nonce')) {
            exit(esc_html__("Security error", 'ultimate-addons-cf7'));
        } 

        $form_id = $_POST['form_id']; 
        if($form_id != 0){
            $meta_data = get_post_meta($form_id, 'uacf7_submission_id', true);
            echo wp_send_json( [
            'form_id' => $form_id,
            'meta_data' => $meta_data
            ] );
        } 
       
     }


    /**
     * Submission ID Update into Database
     */
    public function uacf7_submission_id_insert_callback( $uacf7_db_id, $form_id, $insert_data, $tags){
 
        $uacf7_submission_id_enable = get_post_meta( $form_id, 'uacf7_submission_id_enable', true ); 

        if($uacf7_submission_id_enable == 'on'){
            
            $submission_value = sanitize_text_field(get_post_meta($form_id, 'uacf7_submission_id', true)); 
            if( $submission_value != '' || $submission_value != null || $submission_value != 0){
        
                global $wpdb;  
                $table_name = $wpdb->prefix.'uacf7_form';
                $id         = $uacf7_db_id;
       
                // update submission id existing database
                $sql = $wpdb->prepare("UPDATE $table_name SET submission_id= %s WHERE id= %s", $submission_value, $id );
                
            
                $wpdb->query( $sql );  
            }  
        }
        
    }

    

    public function submission_id_update($form){

        $uacf7_submission_id_enable = get_post_meta( $form->id(), 'uacf7_submission_id_enable', true ); 

        if($uacf7_submission_id_enable == 'on'){
              
            $getCurrentData = get_post_meta($form->id(), 'uacf7_submission_id', true);
            $step_counter = get_post_meta( $form->id(), 'uacf7_submission_id_step', true );

   

            $valueIncreasing = '';


            if($step_counter > 0){
                $valueIncreasing .= $getCurrentData + $step_counter;
            }else{
                $valueIncreasing .= $getCurrentData + 1;
            }
            update_post_meta($form->id(), 'uacf7_submission_id', $valueIncreasing); 
        }
      
    }

    /**
     * Submission TAG Generator
     */
    public function submission_id_add_shortcodes(){

        wpcf7_add_form_tag(array('uacf7_submission_id', 'uacf7_submission_id*'),
            array($this, 'uacf7_submission_id_tag_handler_callback'), array('name-attr' => true));
    }

    public function uacf7_submission_id_tag_handler_callback($tag){
        if (empty($tag->name)) {
            return '';
        }
         
        /** Enable / Disable Submission ID */
        $wpcf7 = WPCF7_ContactForm::get_current(); 
        $formid = $wpcf7->id();
        $uacf7_submission_id_enable = get_post_meta( $formid, 'uacf7_submission_id_enable', true ); 
        
        if($uacf7_submission_id_enable != 'on'){
            return;
        }

        $validation_error = wpcf7_get_validation_error($tag->name);

        $class = wpcf7_form_controls_class($tag->type);


        if ($validation_error) {
            $class .= 'wpcf7-not-valid';
        }

        $atts = array();

        $atts['class'] = $tag->get_class_option($class);
        $atts['id'] = $tag->get_id_option();
        $atts['tabindex'] = $tag->get_option('tabindex', 'signed_int', true);

        if ($tag->is_required()) {
            $atts['aria-required'] = 'true';
        }

        $atts['aria-invalid'] = $validation_error ? 'true' : 'false';

        $atts['name'] = $tag->name;

        // input size
        $size = $tag->get_option('size', 'int', true);
        if ($size) {
            $atts['size'] = $size;
        } else {
            $atts['size'] = 40;
        } 
        $value = $tag->values;
        $default_value = $tag->get_default_option($value);


        $value = get_post_meta($formid, "uacf7_submission_id", true);

        $atts['value'] = $value;

        $atts['name'] = $tag->name;

        $atts = wpcf7_format_atts($atts);

        ob_start();

        ?> 
        <span  class="wpcf7-form-control-wrap <?php echo sanitize_html_class($tag->name); ?>" data-name="<?php echo sanitize_html_class($tag->name); ?>">

            <input hidden id="uacf7_<?php echo esc_attr($tag->name); ?>" <?php echo $atts;?> >
            <span><?php echo $validation_error; ?></span>
        </span>

       <?php 
   
        $submission_buffer = ob_get_clean();

        return $submission_buffer;
    }

    /*
     * Generate tag - Submission ID
     */
    public function submission_tag_generator(){
        if (!function_exists('wpcf7_add_tag_generator')) {
            return;
        }

        wpcf7_add_tag_generator('uacf7_submission_id',
            __('Submission ID', 'ultimate-addons-cf7'),
            'uacf7-tg-pane-submission-id',
            array($this, 'tg_pane_submission_id')
        );
    }

    public static function tg_pane_submission_id($contact_form, $args = ''){
        $args = wp_parse_args($args, array());
        $uacf7_field_type = 'uacf7_submission_id';
        ?>
      <div class="control-box">
      <fieldset>
                <table class="form-table">
                   <tbody>
                        <div class="uacf7-doc-notice"> 
                            <?php echo sprintf( 
                                __( 'Not sure how to set this? Check our step by step  %1s.', 'ultimate-addons-cf7' ),
                                '<a href="https://themefic.com/docs/uacf7/free-addons/unique-submission-id/" target="_blank">documentation</a>'
                            ); ?> 
                        </div>
                        <tr>
                            <th scope="row"><label for="<?php echo esc_attr($args['content'] . '-name'); ?>"><?php echo esc_html(__('Name', 'ultimate-addons-cf7')); ?></label></th>
                            <td><input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr($args['content'] . '-name'); ?>" /></td>
                        </tr> 
                        <tr>
                            <th scope="row"><label for="tag-generator-panel-text-class"><?php echo esc_html__('Class attribute', 'ultimate-addons-cf7'); ?></label></th>
                            <td><input type="text" name="class" class="classvalue oneline option" id="tag-generator-panel-text-class"></td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
      </div>

      <div class="insert-box">
          <input type="text" name="<?php echo esc_attr($uacf7_field_type); ?>" class="tag code" readonly="readonly" onfocus="this.select()" />

          <div class="submitbox">
              <input type="button" class="button button-primary insert-tag" id="prevent_multiple" value="<?php echo esc_attr(__('Insert Tag', 'ultimate-addons-cf7')); ?>" />
          </div>
      </div>
      <?php
}

} 
new UACF7_SUBMISSION_ID();