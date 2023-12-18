<?php

if( !function_exists( 'uacf7_addons_included' ) ) {

    function uacf7_addons_included(){
    
        //Addon - Ultimate redirect
        if( uacf7_checked('uacf7_enable_redirection') != '' ){
            require_once( 'redirection/redirect.php' );
        }

        //Addon - Ultimate conditional field
        if( uacf7_checked('uacf7_enable_conditional_field') != '' ){
            require_once( 'conditional-field/conditional-fields.php' );
        }

        //Addon - Ultimate field Column
        if( uacf7_checked('uacf7_enable_field_column') != '' ){
            require_once( 'column/column.php' );
        }

        //Addon - Ultimate Placeholder
        if( uacf7_checked('uacf7_enable_placeholder') != '' ){
            require_once( 'placeholder/placeholder.php' );
        }

        //Addon - Ultimate Mutlistep
        if( uacf7_checked('uacf7_enable_multistep') != '' ){
            require_once( 'multistep/multistep.php' );
        }

        //Addon - Ultimate Style
        if( uacf7_checked('uacf7_enable_uacf7style') != '' ){
            require_once( 'styler/uacf7style.php' );
        }

        //Addon - Ultimate Product Dropdown
        if( uacf7_checked('uacf7_enable_product_dropdown') != '' ){
            require_once( 'product-dropdown/product-dropdown.php' );
        }

        //Addon - Ultimate Star Rating
        if( uacf7_checked('uacf7_enable_star_rating') != '' ){
            require_once( 'star-rating/star-rating.php' );
        }

        //Addon - Ultimate Price Slider
        if( uacf7_checked( 'uacf7_enable_range_slider') != ''){
            require_once( 'range-slider/range-slider.php');
        }
		
        //Addon - Country Dropdown
        if( uacf7_checked( 'uacf7_enable_country_dropdown_field') != ''){
            require_once( 'country-dropdown/country-dropdown.php');
        }

        //Addon - Mailchimp
        if( uacf7_checked( 'uacf7_enable_mailchimp') != ''){
            require_once( 'mailchimp/mailchimp.php');
        }
		
        //Addon - Dynamic Text
        if( uacf7_checked( 'uacf7_enable_dynamic_text') != ''){
            require_once( 'dynamic-text/dynamic-text.php');
        } 

        //Addon - Pre Populate 
        if( uacf7_checked( 'uacf7_enable_pre_populate_field') != ''){
            require_once( 'pre-populate-field/pre-populate-field.php');
        }

        //Addon - Database 
        if( uacf7_checked( 'uacf7_enable_database_field') != ''){
            require_once( 'database/database.php');
        }
 
        //Addon - PDF Gererator 
        if( uacf7_checked( 'uacf7_enable_pdf_generator_field') != ''){
            require_once( 'pdf-generator/pdf-generator.php');
        } 
        
        //Form Generator AI
        if( uacf7_checked( 'uacf7_enable_form_generator_ai_field') != ''){
            require_once( 'form-generator-ai/form-generator-ai.php');
        }else{
            $uacf7_options = get_option('uacf7_option_name');
            $update_form_generator_ai = get_option('update_form_generator_ai'); 
            if(!isset($uacf7_options['uacf7_enable_form_generator_ai_field'])  && $update_form_generator_ai == false){ 
                $uacf7_options['uacf7_enable_form_generator_ai_field'] = 'on';
                
                update_option('uacf7_option_name', $uacf7_options);
                update_option('update_form_generator_ai', 1);
            } 
           
        }

        //Addon - Submission ID
        if( uacf7_checked( 'uacf7_enable_submission_id_field') != ''){
            require_once( 'submission-id/ultimate-submission-id.php');
        }
         //Addon - Signature
         if( uacf7_checked( 'uacf7_enable_signature_field') != ''){
            require_once( 'signature/ultimate-signature.php');
        }
		

        //Addon - Telegram
        if( uacf7_checked( 'uacf7_enable_telegram_field') != ''){
            require_once( 'telegram/ultimate-telegram.php');
        }
		
    }
}

uacf7_addons_included();