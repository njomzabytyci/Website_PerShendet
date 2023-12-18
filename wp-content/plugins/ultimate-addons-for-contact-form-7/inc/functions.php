<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//Require ultimate Promo Notice
if(file_exists( __DIR__ . '/class-promo-notice.php')){
   
    require_once( 'class-promo-notice.php' ); 
} 

/*
Function: uacf7_checked
Return: checked
*/
if( !function_exists('uacf7_checked') ){
    function uacf7_checked( $name ){
    
        //Get settings option
        $uacf7_options = get_option( apply_filters( 'uacf7_option_name', 'uacf7_option_name' ) );

        if( isset( $uacf7_options[$name] ) && $uacf7_options[$name] === 'on' ) {
            return 'checked';
        }
    }
}

/*
* Hook: uacf7_multistep_pro_features
* Multistep pro features demo
*/
add_action( 'uacf7_multistep_pro_features', 'uacf7_multistep_pro_features_demo', 5, 2 );
function uacf7_multistep_pro_features_demo( $all_steps, $form_id ){ 
    if(!isset($all_steps[0])) return;
    if( empty(array_filter($all_steps))) return;
    ?>
    <div class="multistep_fields_row" style="display: flex; flex-direction: column;">
    <?php
    $step_count = 1;
    foreach( $all_steps as $step ) {
        ?>
        <h3><strong>Step <?php echo $step_count; ?> <a style="color:red" target="_blank" href="https://cf7addons.com/preview/pro">(Pro)</a></strong></h3>
        <?php
        if( $step_count == 1 ){
            ?>
            <div>
               <p><label for="<?php echo 'next_btn_'.$step->name; ?>"><?php echo __('Change next button text for this Step', 'ultimate-addons-cf7' ) ?></label></p>
               <input id="<?php echo 'next_btn_'.$step->name; ?>" type="text" name="" value="" placeholder="<?php echo esc_html__('Next','ultimate-addons-cf7-pro') ?>">
            </div>
            <?php
        } else {

            if( count($all_steps) == $step_count ) {
                ?>
                <div>
                   <p><label for="<?php echo 'prev_btn_'.$step->name; ?>"><?php echo __('Change previous button text for this Step', 'ultimate-addons-cf7' ) ?></label></p>
                   <input id="<?php echo 'prev_btn_'.$step->name; ?>" type="text" name="" value="" placeholder="<?php echo esc_html__('Previous','ultimate-addons-cf7-pro') ?>">
                </div>
                <?php

            } else {
                ?>
                <div class="multistep_fields_row-">
                    <div class="multistep_field_column">
                       <p><label for="<?php echo 'prev_btn_'.$step->name; ?>"><?php echo __('Change previous button text for this Step', 'ultimate-addons-cf7' ) ?></label></p>
                       <input id="<?php echo 'prev_btn_'.$step->name; ?>" type="text" name="" value="" placeholder="<?php echo esc_html__('Previous','ultimate-addons-cf7-pro') ?>">
                    </div>

                    <div class="multistep_field_column">
                       <p><label for="<?php echo 'next_btn_'.$step->name; ?>"><?php echo __('Change next button text for this Step', 'ultimate-addons-cf7' ) ?></label></p>
                       <input id="<?php echo 'next_btn_'.$step->name; ?>" type="text" name="" value="" placeholder="<?php echo esc_html__('Next','ultimate-addons-cf7-pro') ?>">
                    </div>
                </div>
                <?php
            }

        }
        ?>
        <div class="uacf7_multistep_progressbar_image_row">
           <p><label for="<?php echo esc_attr('uacf7_progressbar_image_'.$step->name); ?>"><?php echo __('Add progressbar image for this step', 'ultimate-addons-cf7' ) ?></label></p>
           <input class="uacf7_multistep_progressbar_image" id="<?php echo esc_attr('uacf7_progressbar_image_'.$step->name); ?>" type="url" name="" value=""> <a class="button-primary" href="#"><?php echo __('Add or Upload Image', 'ultimate-addons-cf7' ) ?></a>
           
           <div class="multistep_fields_row step-title-description col-50">
                <div class="multistep_field_column">
                   <p><label for="<?php echo 'step_desc_'.$step->name; ?>"><?php echo __('Step description', 'ultimate-addons-cf7' ) ?></label></p>
                   <textarea id="<?php echo 'step_desc_'.$step->name; ?>" type="text" name="" cols="40" rows="3" placeholder="<?php echo esc_html__('Step description','ultimate-addons-cf7-pro') ?>"></textarea>
                </div>
    
                <div class="multistep_field_column">
                   <p><label for="<?php echo 'desc_title_'.$step->name; ?>"><?php echo __('Description title', 'ultimate-addons-cf7' ) ?></label></p>
                   <input id="<?php echo 'desc_title_'.$step->name; ?>" type="text" name="" value="" placeholder="<?php echo esc_html__('Description title','ultimate-addons-cf7-pro') ?>">
                </div>
            </div>
        </div>
        <?php
        $step_count++;
    }
    ?>
    </div>
    <?php
}

/*
* Progressbar style
*/
add_action( 'uacf7_multistep_before_form', 'uacf7_multistep_progressbar_style', 10 );
function uacf7_multistep_progressbar_style( $form_id ) {
    $uacf7_multistep_circle_width = get_post_meta( $form_id, 'uacf7_multistep_circle_width', true ); 
    $uacf7_multistep_circle_height = get_post_meta( $form_id, 'uacf7_multistep_circle_height', true ); 
    $uacf7_multistep_circle_bg_color = get_post_meta( $form_id, 'uacf7_multistep_circle_bg_color', true ); 
    $uacf7_multistep_circle_font_color = get_post_meta( $form_id, 'uacf7_multistep_circle_font_color', true ); 
    $uacf7_multistep_circle_border_radious = get_post_meta( $form_id, 'uacf7_multistep_circle_border_radious', true ); 
    $uacf7_multistep_font_size = get_post_meta( $form_id, 'uacf7_multistep_font_size', true ); 
    $uacf7_multistep_circle_active_color = get_post_meta( $form_id, 'uacf7_multistep_circle_active_color', true );
    $uacf7_multistep_progress_line_color = get_post_meta( $form_id, 'uacf7_multistep_progress_line_color', true );
    ?>
    <style>
    .steps-form .steps-row .steps-step .btn-circle {
        <?php if(!empty($uacf7_multistep_circle_width)) echo 'width: '.esc_attr($uacf7_multistep_circle_width).'px;'; ?>
        <?php if(!empty($uacf7_multistep_circle_height)) echo 'height: '.esc_attr($uacf7_multistep_circle_height).'px;'; ?>
        <?php if($uacf7_multistep_circle_border_radious != '' ) echo 'border-radius: '.$uacf7_multistep_circle_border_radious.'px;'; ?>
        <?php if(!empty($uacf7_multistep_circle_height)) echo 'line-height: '.esc_attr($uacf7_multistep_circle_height).'px;'; ?>
        <?php if(!empty($uacf7_multistep_circle_bg_color)) echo 'background-color: '.esc_attr($uacf7_multistep_circle_bg_color).' !important;'; ?>
        <?php if(!empty($uacf7_multistep_circle_font_color)) echo 'color: '.esc_attr($uacf7_multistep_circle_font_color).' !important;'; ?>
        <?php if(!empty($uacf7_multistep_font_size)) echo 'font-size: '.esc_attr($uacf7_multistep_font_size).'px;'; ?>
    }
	.steps-form .steps-row .steps-step .btn-circle img {
		<?php if( $uacf7_multistep_circle_border_radious != 0 ) echo 'border-radius: '.$uacf7_multistep_circle_border_radious.'px !important;'; ?>
	}
    .steps-form .steps-row .steps-step .btn-circle.uacf7-btn-active,
    .steps-form .steps-row .steps-step .btn-circle:hover,
    .steps-form .steps-row .steps-step .btn-circle:focus,
    .steps-form .steps-row .steps-step .btn-circle:active{
        <?php if(!empty($uacf7_multistep_circle_active_color)) echo 'background-color: '.esc_attr($uacf7_multistep_circle_active_color).' !important;'; ?>
        <?php if(!empty($uacf7_multistep_circle_font_color)) echo 'color: '.esc_attr($uacf7_multistep_circle_font_color).';'; ?>
    }
    .steps-form .steps-row .steps-step p {
        <?php if(!empty($uacf7_multistep_font_size)) echo 'font-size: '.esc_attr($uacf7_multistep_font_size).'px;'; ?>
    }
    .steps-form .steps-row::before {
        <?php if(!empty($uacf7_multistep_circle_height)) echo 'top: '.esc_attr($uacf7_multistep_circle_height / 2).'px;'; ?>
    }
    <?php if(!empty($uacf7_multistep_progress_line_color)): ?>
    .steps-form .steps-row::before {
    	background-color: <?php echo esc_attr($uacf7_multistep_progress_line_color); ?>;
    }
    <?php endif; ?>
    </style>
    <?php
}


//Dispal repeater pro feature

if( !function_exists('uacf7_tg_pane_repeater') ) {
    add_action( 'admin_init', 'uacf7_repeater_pro_tag_generator' );
}

function uacf7_repeater_pro_tag_generator() {
    if (! function_exists( 'wpcf7_add_tag_generator'))
        return;

    wpcf7_add_tag_generator('repeater',
        __('Ultimate Repeater (pro)', 'ultimate-addons-cf7'),
        'uacf7-tg-pane-repeater',
        'uacf7_tg_pane_repeater_pro'
    );

}

function uacf7_tg_pane_repeater_pro( $contact_form, $args = '' ) {
    $args = wp_parse_args( $args, array() );
    $uacf7_field_type = 'repeater';
    ?>
    <div class="control-box">
        <fieldset>
            <legend>
                <?php echo esc_html__( "This is a Pro feature of Ultimate Addons for contact form 7. You can add repeatable field and repeatable fields group with this addon.", "ultimate-addons-cf7" ); ?> <a href="https://cf7addons.com/preview/repeater-field/" target="_blank">Check Preview</a>
            </legend>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Name', 'ultimate-addons-cf7' ) ); ?></label></th>
                        <td><input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" /></td>
                    </tr>
                    <tr>
                    	<th scope="row"><label for="tag-generator-panel-text-values"><?php echo __('Add Button Text', 'ultimate-addons-cf7' ) ?></label></th>
                    	<td><input type="text" name="" class="tg-name oneline uarepeater-add" value="Add more" id="tag-generator-panel-uarepeater-nae"></td>
                	</tr>
                	<tr>
                    	<th scope="row"><label for="tag-generator-panel-text-values-remove"><?php echo __('Remove Button Text', 'ultimate-addons-cf7' ) ?></label></th>
                    	<td><input type="text" name="" class="tg-name oneline uarepeater-remove" value="Remove" id="tag-generator-panel-uarepeater-n"></td>
                	</tr>
                    
                </tbody>
            </table>
        </fieldset>
    </div>
    <?php
}

//Add wrapper to contact form 7
add_filter( 'wpcf7_contact_form_properties', 'uacf7_add_wrapper_to_cf7_form', 10, 2 );
function uacf7_add_wrapper_to_cf7_form($properties, $cfform) {
    if (!is_admin() || (defined('DOING_AJAX') && DOING_AJAX)) {
    
        $form = $properties['form'];
        ob_start();
        echo '<div class="uacf7-form-'.$cfform->id().'">'.$form.'</div>';
        $properties['form'] = ob_get_clean();
        
    }
	return $properties;
}

 

// Themefic Plugin Set Admin Notice Status
if(!function_exists('uacf7_review_activation_status')){

    function uacf7_review_activation_status(){ 
        $uacf7_installation_date = get_option('uacf7_installation_date'); 
        if( !isset($_COOKIE['uacf7_installation_date']) && empty($uacf7_installation_date) && $uacf7_installation_date == 0){
            setcookie('uacf7_installation_date', 1, time() + (86400 * 7), "/"); 
        }else{
            update_option( 'uacf7_installation_date', '1' );
        }
    }
    add_action('admin_init', 'uacf7_review_activation_status');
}

// Themefic Plugin Review Admin Notice
if(!function_exists('uacf7_review_notice')){
    
     function uacf7_review_notice(){ 
        $get_current_screen = get_current_screen();  
        if($get_current_screen->base == 'dashboard'){
            $current_user = wp_get_current_user();
        ?>
            <div class="notice notice-info themefic_review_notice"> 
               
                <?php echo sprintf( 
                        __( ' <p>Hey %1$s 👋, You have been using <b>%2$s</b> for quite a while. If you feel %2$s is helping your business to grow in any way, would you please help %2$s to grow by simply leaving a 5* review on the WordPress Forum?', 'ultimate-addons-cf7' ),
                        $current_user->display_name,
                        'Ultimate Addons for Contact Form 7'
                    ); ?> 
                
                <ul>
                    <li><a target="_blank" href="<?php echo esc_url('https://wordpress.org/plugins/ultimate-addons-for-contact-form-7/#reviews') ?>" class=""><span class="dashicons dashicons-external"></span><?php _e(' Ok, you deserve it!', 'ultimate-addons-cf7' ) ?></a></li>
                    <li><a href="#" class="already_done" data-status="already"><span class="dashicons dashicons-smiley"></span> <?php _e('I already did', 'ultimate-addons-cf7') ?></a></li>
                    <li><a href="#" class="later" data-status="later"><span class="dashicons dashicons-calendar-alt"></span> <?php _e('Maybe Later', 'ultimate-addons-cf7') ?></a></li>
                    <li><a target="_blank"  href="<?php echo esc_url('https://themefic.com/docs/ultimate-addons-for-contact-form-7/') ?>" class=""><span class="dashicons dashicons-sos"></span> <?php _e('I need help', 'ultimate-addons-cf7') ?></a></li>
                    <li><a href="#" class="never" data-status="never"><span class="dashicons dashicons-dismiss"></span><?php _e('Never show again', 'ultimate-addons-cf7') ?> </a></li> 
                </ul>
                <button type="button" class="notice-dismiss review_notice_dismiss"><span class="screen-reader-text"><?php _e('Dismiss this notice.', 'ultimate-addons-cf7') ?></span></button>
            </div>

            <!--   Themefic Plugin Review Admin Notice Script -->
            <script>
                jQuery(document).ready(function($) {
                    $(document).on('click', '.already_done, .later, .never', function( event ) {
                        event.preventDefault();
                        var $this = $(this);
                        var status = $this.attr('data-status'); 
                        $this.closest('.themefic_review_notice').css('display', 'none')
                        data = {
                            action : 'uacf7_review_notice_callback',
                            status : status,
                        };

                        $.ajax({
                            url: ajaxurl,
                            type: 'post',
                            data: data,
                            success: function (data) { ;
                            },
                            error: function (data) { 
                            }
                        });
                    });

                    $(document).on('click', '.review_notice_dismiss', function( event ) {
                        event.preventDefault(); 
						var $this = $(this);
                        $this.closest('.themefic_review_notice').css('display', 'none')
                        
                    });
                });

            </script>
        <?php  
        }
     }
     $uacf7_review_notice_status = get_option('uacf7_review_notice_status'); 
     $uacf7_installation_date = get_option('uacf7_installation_date'); 
     if(isset($uacf7_review_notice_status) && $uacf7_review_notice_status <= 0 && $uacf7_installation_date == 1 && !isset($_COOKIE['uacf7_review_notice_status']) && !isset($_COOKIE['uacf7_installation_date'])){ 
        add_action( 'admin_notices', 'uacf7_review_notice' );  
     }
     
}

 
// Themefic Plugin Review Admin Notice Ajax Callback 
if(!function_exists('uacf7_review_notice_callback')){

    function uacf7_review_notice_callback(){
        $status = $_POST['status'];
        if( $status == 'already'){ 
            update_option( 'uacf7_review_notice_status', '1' );
        }else if($status == 'never'){ 
            update_option( 'uacf7_review_notice_status', '2' );
        }else if($status == 'later'){
            $cookie_name = "uacf7_review_notice_status";
            $cookie_value = "1";
            setcookie($cookie_name, $cookie_value, time() + (86400 * 7), "/"); 
            update_option( 'uacf7_review_notice_status', '0' ); 
        }  
        wp_die();
    }
    add_action( 'wp_ajax_uacf7_review_notice_callback', 'uacf7_review_notice_callback' );

}

