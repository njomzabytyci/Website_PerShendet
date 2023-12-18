<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class UACF7_FORM_GENERATOR{

    /*
    * Construct function
    */
    public function __construct() { 
        //
        define( 'UACF7_FORM_AI_PATH', UACF7_PATH.'/addons/form-generator-ai' );
        // admin scripts
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));

        // form tab panel
        // add_action( 'wpcf7_editor_panels', array( $this, 'uacf7_add_panel' ) );

        // add Popup Contact form 7 admin footer
        add_action( 'wpcf7_admin_footer', array( $this, 'uacf7_form_admin_footer_popup' ) );

        // Ai form generator Ajax Function
        add_action( 'wp_ajax_uacf7_form_generator_ai', array( $this, 'uacf7_form_generator_ai' ) ); 

        // Ai form Get Tag Ajax Function
        add_action( 'wp_ajax_uacf7_form_generator_ai_get_tag', array( $this, 'uacf7_form_generator_ai_get_tag' ) ); 
    }


    // Add Admin Scripts
    public function admin_scripts(){  
        wp_enqueue_script( 'uacf7-form-generator-ai-choices-js', UACF7_ADDONS . '/form-generator-ai/assets/js/choices.min.js', array(), null, true );  
        wp_enqueue_script( 'uacf7-form-generator-ai-admin-js', UACF7_ADDONS . '/form-generator-ai/assets/js/admin-form-generator-ai.js', array('jquery'), null, true ); 
        // wp_enqueue_style( 'uacf7-form-generator-ai-choices-css', UACF7_ADDONS . '/form-generator-ai/assets/css/choices.css' ); 
        wp_enqueue_style( 'uacf7-form-generator-ai-admin-css', UACF7_ADDONS . '/form-generator-ai/assets/css/admin-form-generator-ai.css' );

        wp_localize_script( 'uacf7-form-generator-ai-admin-js', 'uacf7_form_ai',
            array( 
                'ajaxurl' => admin_url( 'admin-ajax.php' ),
                'nonce' => wp_create_nonce( 'uacf7-form-generator-ai-nonce' ),
                'loader' => UACF7_ADDONS . '/form-generator-ai/assets/images/pre-loader.svg',
            ) 
        );
    }


    // Add Form Tab Panel
    public function uacf7_add_panel( $panels ) {
        $panels['uacf7-form-generator-ai-panel'] = array(
            'title' => __( 'Form Generator AI', 'contact-form-7' ),
            'callback' => array( $this, 'uacf7_form_generatorform_generator_ai_panel' ),
        );
        return $panels;
    }

    public function uacf7_form_generatorform_generator_ai_panel(){ 
           
    }


    // Add Popup Contact form 7 admin footer
    public function uacf7_form_admin_footer_popup(){
        ob_start(); 
        ?>
        <div class="uacf7-form-ai-popup">
            <div class="uacf7-form-ai-wrap"> 
                <div class="uacf7-form-ai-inner"> 
                    <div class="close" title="Exit Full Screen">╳</div>
                    
                    <div class="uacf7-ai-form-column"> 
                        <div class="uacf7-form-input-wrap">
                            
                            <h4><?php echo _e( 'Create a', 'ultimate-addons-cf7' ); ?></h4>
                            <div class="uacf7-form-input-inner">
                                <select class="form-control uacf7-choices" data-trigger name="uacf7-form-generator-ai" id="uacf7-form-generator-ai"
                                    placeholder="This is a placeholder" multiple> 
                                </select>  
                                <button class="uacf7_ai_search_button"><?php echo _e( 'Generate With AI', 'ultimate-addons-cf7' ); ?></button>
                            </div> 
                            
                        </div>
                        <div class="uacf7-doc-notice">
                            <?php echo sprintf( 
                                __( 'Not sure how to use this? Check our step by step  %1s.', 'ultimate-addons-cf7' ),
                                '<a href="https://themefic.com/docs/uacf7/free-addons/ai-form-generator/" target="_blank">documentation</a>'
                            ); ?> 
                        </div>
                    </div> 
                    <div class="uacf7-ai-form-column"> 
                        <div class="uacf7-ai-codeblock"> 
                            <div class="uacf7-ai-navigation">
                                <span class="uacf7-ai-code-reset"> <?php echo _e( 'Reset', 'ultimate-addons-cf7' ); ?></span>
                                <span class="uacf7-ai-code-copy"> <?php echo _e( 'Copy', 'ultimate-addons-cf7' ); ?></span>
                                <span class="uacf7-ai-code-insert"> <?php echo _e( 'Insert', 'ultimate-addons-cf7' ); ?></span>
                            </div>
                            <textarea name="uacf7_ai_code_content" id="uacf7_ai_code_content" ></textarea>
                        </div>
                    </div> 
                    
                </div>
            </div>
        </div> 
        <?php
        echo ob_get_clean();
    }
 
    public function uacf7_form_generator_ai_get_tag(){
        if ( !wp_verify_nonce($_POST['ajax_nonce'], 'uacf7-form-generator-ai-nonce')) {
            exit(esc_html__("Security error", 'ultimate-addons-cf7'));
        } 
        $tag_generator = WPCF7_TagGenerator::get_instance('panel', true);

        $reflector = new ReflectionClass('WPCF7_TagGenerator');
        $property = $reflector->getProperty('panels');
        $property->setAccessible(true);

        $panels = $property->getValue($tag_generator); 
        $tag_data = [];
        foreach($panels as $key => $value){  
            if($key !== 'uacf7_conversational_start' && $key != 'uacf7_conversational_end' && $key != 'uacf7_step_start' && $key != 'uacf7_step_end' && $key != 'conditional' && $key != 'repeater' ){
                $tag_value['value'] = $key;
                $tag_value['label'] = $value['title'];
                $tag_data[] = $tag_value;
            }
            
        } 
        // $form_booking =  apply_filters('uacf7_booking_ai_form_dropdown', ["value" => "booking", "label" => "Booking (Pro)", "disabled" => "false"]);
    
        $secend_option_form = [
            ["value" => "multistep", "label" => "Multistep"],
            apply_filters('uacf7_booking_ai_form_dropdown', ["value" => "booking", "label" => "Booking (Pro)"]),
            ["value" => "conditional", "label" => "Conditional"],
            ["value" => "subscription", "label" => "Subscription"],
            apply_filters('uacf7_repeater_ai_form_dropdown', ["value" => "repeater", "label" => "Repeater (Pro)"]), 
            apply_filters('uacf7_blog_submission_ai_form_dropdown', ["value" => "blog", "label" => "Blog Submission (Pro)"]),
            ["value" => "feedback", "label" => "Feedback"],
            ["value" => "application", "label" => "Application"],
            ["value" => "inquiry", "label" => "Inquiry"],
            ["value" => "survey", "label" => "Survey"],
            ["value" => "address", "label" => "Address"],
            ["value" => "event", "label" => "Event Registration"],
            ["value" => "newsletter", "label" => "Newsletter"],
            ["value" => "donation", "label" => "Donation"],
            ["value" => "product-review", "label" => "Product Review"],
            apply_filters('uacf7_service_booking_form_dropdown', ["value" => "service-booking", "label" => "Service Booking (Pro)"]), 
            apply_filters('uacf7_appointment_form_dropdown', ["value" => "appointment-form", "label" => "Appointment (Pro)"]), 
            apply_filters('uacf7_conversational_appointment_form_dropdown', ["value" => "conversational-appointment-form", "label" => "Conversational Appointment Booking  (Pro)"]), 
            apply_filters('uacf7_conversational_interview_form_dropdown', ["value" => "conversational-interview-form", "label" => "Conversational Interview Process (Pro)"]),  
            ["value" => "rating", "label" => "Rating"],
        ];
        
        $data = [
            'status' => 'success',
            'value_tag' => $tag_data,
            'value_form' => $secend_option_form,
        ];
        
        echo wp_send_json($data);
        die();
    
    }

    // Ai form Get Tag Ajax Function
    public function uacf7_form_generator_ai(){
        if ( !wp_verify_nonce($_POST['ajax_nonce'], 'uacf7-form-generator-ai-nonce')) {
            exit(esc_html__("Security error", 'ultimate-addons-cf7'));
        }
        $vaue = '';
        $uacf7_default = $_POST['searchValue'];  

        if(count($uacf7_default) > 0 && $uacf7_default[0] == 'form' ){ 
            $value =  require_once  apply_filters( 'uacf7_ai_form_generator_template', UACF7_FORM_AI_PATH . '/templates/uacf7-forms.php');
        }elseif(count($uacf7_default) > 0 && $uacf7_default[0] == 'tag' ){ 
            $value =  require_once  apply_filters( 'uacf7_ai_form_generator_template', UACF7_FORM_AI_PATH . '/templates/uacf7-tags.php');
        }  
        $data = [
            'status' => 'success',
            'value' => $value,
        ];
        echo wp_send_json($data);
        die();
    }
}

new UACF7_FORM_GENERATOR();


?>