<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
* Pre Populate Classs
*/
class UACF7_PDF_GENERATOR {
    
    /*
    * Construct function
    */
    public function __construct() {
        
        add_action( 'admin_enqueue_scripts', array($this, 'wp_enqueue_admin_script' ) );    
        add_action( 'wpcf7_editor_panels', array( $this, 'uacf7_add_panel' ) );     
        add_action( 'wpcf7_after_save', array( $this, 'uacf7_save_contact_form' ) );     
        
        add_filter( 'wpcf7_mail_components', array( $this, 'uacf7_wpcf7_mail_components' ), 10, 3 );    
        // add_filter( 'wpcf7_load_js', '__return_false' );
        add_action( 'wp_ajax_uacf7_get_generated_pdf', array( $this, 'uacf7_get_generated_pdf' ) );  
 
        
    } 

 
    /*
    * Enqueue script Backend
    */
    
    public function wp_enqueue_admin_script() {
        // jQuery
        wp_enqueue_script('jquery');
        // This will enqueue the Media Uploader script
        wp_enqueue_media();

        wp_enqueue_script('media-upload');
        
        wp_enqueue_style( 'pdf-generator-admin-style', UACF7_ADDONS . '/pdf-generator/assets/css/pdf-generator-admin.css' );
		wp_enqueue_script( 'pdf-generator-admin', UACF7_ADDONS . '/pdf-generator/assets/js/pdf-generator-admin.js', array('jquery'), 'media-upload', true ); 
        $pdf_settings['codeEditor'] = wp_enqueue_code_editor(array('type' => 'text/css'));
        $pdf_settings['ajaxurl'] = admin_url( 'admin-ajax.php' );
        $pdf_settings['nonce'] = wp_create_nonce('uacf7-pre-populate');
        wp_localize_script('jquery', 'pdf_settings', $pdf_settings);
        
        // require UACF7_PATH . 'third-party/vendor/autoload.php';

    } 
  
    // Generate PDF and export form ultimate db
    public function uacf7_get_generated_pdf(){ 
        if ( ! isset( $_POST ) || empty( $_POST ) ) {
			return;
		}
        
        if ( !wp_verify_nonce($_POST['ajax_nonce'], 'uacf7-pre-populate')) {
            exit(esc_html__("Security error", 'ultimate-addons-cf7'));
        }

        $form_id = !empty($_POST['form_id']) ? $_POST['form_id'] : '';
        $data_id = !empty($_POST['id']) ? $_POST['id'] : '';
        require UACF7_PATH . 'third-party/vendor/autoload.php';
        $enable_pdf = !empty(get_post_meta( $form_id, 'uacf7_enable_pdf_generator', true )) ? get_post_meta( $form_id, 'uacf7_enable_pdf_generator', true ) : '';
        if( $enable_pdf != 'on'){ die; }
        
        $upload_dir    = wp_upload_dir(); 
        $dir = $upload_dir['basedir'];
        $url = $upload_dir['baseurl'];
        global $wpdb; 
        $data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."uacf7_form WHERE id = %s AND form_id = %s", $data_id, $form_id ) ); 

        $uacf7_pdf_name = !empty(get_post_meta( $form_id, 'uacf7_pdf_name', true )) ? get_post_meta( $form_id, 'uacf7_pdf_name', true ) : get_the_title( $form_id );
        $disable_header = !empty(get_post_meta( $form_id, 'uacf7_pdf_disable_header', true )) ? get_post_meta( $form_id, 'uacf7_pdf_disable_header', true ) : '';
        $disable_footer = !empty(get_post_meta( $form_id, 'uacf7_pdf_disable_footer', true )) ? get_post_meta( $form_id, 'uacf7_pdf_disable_footer', true ) : '';
        $customize_pdf = !empty(get_post_meta( $form_id, 'customize_pdf', true )) ? get_post_meta( $form_id, 'customize_pdf', true ) : '';
        $pdf_bg_upload_image = !empty(get_post_meta( $form_id, 'pdf_bg_upload_image', true )) ? get_post_meta( $form_id, 'pdf_bg_upload_image', true ) : '';
        $customize_pdf_header = !empty(get_post_meta( $form_id, 'customize_pdf_header', true )) ? get_post_meta( $form_id, 'customize_pdf_header', true ) : '';
        $pdf_header_upload_image = !empty(get_post_meta( $form_id, 'pdf_header_upload_image', true )) ? get_post_meta( $form_id, 'pdf_header_upload_image', true ) : '';
        $pdf_header_img_height = !empty(get_post_meta( $form_id, 'pdf_header_img_height', true )) ? get_post_meta( $form_id, 'pdf_header_img_height', true ) : '';
        $pdf_header_img_width = !empty(get_post_meta( $form_id, 'pdf_header_img_width', true )) ? get_post_meta( $form_id, 'pdf_header_img_width', true ) : '';
        $pdf_header_img_aline = !empty(get_post_meta( $form_id, 'pdf_header_img_aline', true )) ? get_post_meta( $form_id, 'pdf_header_img_aline', true ) : '';
        $customize_pdf_footer = !empty(get_post_meta( $form_id, 'customize_pdf_footer', true )) ? get_post_meta( $form_id, 'customize_pdf_footer', true ) : '';
        $custom_pdf_css = !empty(get_post_meta( $form_id, 'custom_pdf_css', true )) ? get_post_meta( $form_id, 'custom_pdf_css', true ) : ''; 
        $pdf_content_color = !empty(get_post_meta( $form_id, 'pdf_content_color', true )) ? get_post_meta( $form_id, 'pdf_content_color', true ) : ''; 
        $pdf_content_bg_color = !empty(get_post_meta( $form_id, 'pdf_content_bg_color', true )) ? get_post_meta( $form_id, 'pdf_content_bg_color', true ) : '';  
        $pdf_header_color = !empty(get_post_meta( $form_id, 'pdf_header_color', true )) ? get_post_meta( $form_id, 'pdf_header_color', true ) : ''; 
        $pdf_header_bg_color = !empty(get_post_meta( $form_id, 'pdf_header_bg_color', true )) ? get_post_meta( $form_id, 'pdf_header_bg_color', true ) : '';  
        $pdf_footer_color = !empty(get_post_meta( $form_id, 'pdf_footer_color', true )) ? get_post_meta( $form_id, 'pdf_footer_color', true ) : ''; 
        $pdf_footer_bg_color = !empty(get_post_meta( $form_id, 'pdf_footer_bg_color', true )) ? get_post_meta( $form_id, 'pdf_footer_bg_color', true ) : '';  
        $pdf_bg_upload_image =  !empty($pdf_bg_upload_image) ? 'background-image: url("'.esc_attr($pdf_bg_upload_image).'");' : '';
        $pdf_header_upload_image =  !empty($pdf_header_upload_image) ? '<img src="'.esc_attr( $pdf_header_upload_image ).'" style="height: 60; max-width: 100%; ">' : '';

        $mpdf = new \Mpdf\Mpdf([ 
            'fontdata' => [ // lowercase letters only in font key
                'dejavuserifcond' => [
                    'R' => 'DejaVuSansCondensed.ttf',
                ]
            ],
            'mode' => 'utf-8',
            'default_font' => 'dejavusanscond',
            'margin_header' => 0,
            'margin_footer' => 0,
            'format' => 'A4', 
            'margin_left' => 0,
            'margin_right' => 0
        ]); 
        

        // PDF Style
        $pdf_style = ' <style>
            body {
                 '.esc_attr( $pdf_bg_upload_image ).'
                background-repeat:no-repeat;
                background-image-resize: 6; 
            }
            .pdf-header{
                height: 60px;   
                background-color: '.esc_attr( $pdf_header_bg_color ).';
                color : '.esc_attr( $pdf_header_color ).'; 
            }
            .pdf-footer{ 
                background-color: '.esc_attr( $pdf_footer_bg_color ).';
                color : '.esc_attr( $pdf_footer_color ).'; 
            }
            .pdf-content{ 
                background-color: '.esc_attr($pdf_content_bg_color).';
                color : '.esc_attr( $pdf_content_color ).';
                padding: 20px;
                height: 100%;
            }
            .header-logo{
                text-align: '.esc_attr( $pdf_header_img_aline ).'; 
                float: left; 
                width: 20%;
            }
            .header-content{
                float: right; 
                width: 80%
                
            }
            '.$custom_pdf_css.'
        </style>';
     

        // PDF Header checked( 'on', $disable_header );
        if( $disable_header != 'on' ){
            $mpdf->SetHTMLHeader('
            <div class="pdf-header"  >
                    <div class="header-logo"  >
                        '.$pdf_header_upload_image.'
                    </div>    
                    <div class="header-content">
                    '.$customize_pdf_header.'
                    </div>
            </div>
            ');
        }
        

        // PDF Footer
        if( $disable_footer != 'on' ){
            $mpdf->SetHTMLFooter('<div class="pdf-footer">'.$customize_pdf_footer.'</div>');
        }
        
        $replace_key = [];
        $repeaters = [];
        $repeater_value = [];
        $replace_value = []; 
        $uploaded_files = [];
        
       $form_value =  json_decode($data->form_value); 
        foreach($form_value as $key => $value){
            // Repeater value gate
            if (strpos($key, '__') !== false) {
                $name_parts = explode('__', $key); 
                if(is_array($name_parts)){
                    $repeater_value[$name_parts[0]][$name_parts[1]] = $name_parts[0];  
                }
            }

            if(strpos($key,"_count") !== false){ 
                $repeaters[] = str_replace('_count', '', $key) ;
            }

            $replace_key[] = '['.$key.']';
            if( is_array($value)){
                $data = '';
                $count_value = count($value);
                for ($x = 0; $x < $count_value ; $x++) {
                    if($x == 0){ 
                        $data .= $value[$x];
                    }else{
                        $data .= ', '.$value[$x]; 
                    }
                    
                } 
                $value = $data;
            }
            $replace_value[] = $value;
        }   

        // Repeater value
        if(isset($repeaters) || is_array($repeaters)){
            $repeater_data = apply_filters('uacf7_pdf_generator_replace_data', $repeater_value, $repeaters, $customize_pdf); 
            $customize_pdf = str_replace($repeater_data['replace_re_key'], $repeater_data['replace_re_value'], $customize_pdf); 
        }  

        $pdf_content = str_replace($replace_key, $replace_value, $customize_pdf);
 
        $mpdf->SetTitle($uacf7_pdf_name);

        // PDF Footer Content
        $mpdf->WriteHTML($pdf_style.'<div class="pdf-content">'.nl2br($pdf_content).'   </div>');

        $pdf_dir = $dir.'/uacf7-uploads/'.$uacf7_pdf_name.'_db_.pdf';
        $pdf_url = $url.'/uacf7-uploads/'.$uacf7_pdf_name.'_db_.pdf';
        $mpdf->Output($pdf_dir, 'F'); // Dwonload
        
        wp_send_json( 
            array(
                'status' => 'success',
                'url' => $pdf_url
            )
        );
       
        die();
    }
    function uacf7_wpcf7_mail_components( $components, $form = null, $mail = null  ) { 
       

        $wpcf7 = WPCF7_ContactForm::get_current(); 
        $enable_pdf = !empty(get_post_meta( $wpcf7->id(), 'uacf7_enable_pdf_generator', true )) ? get_post_meta( $wpcf7->id(), 'uacf7_enable_pdf_generator', true ) : '';
        $pdf_send_to = !empty(get_post_meta( $wpcf7->id(), 'pdf_send_to', true )) ? get_post_meta( $wpcf7->id(), 'pdf_send_to', true ) : '';
        if(($pdf_send_to == 'mail-1' && $mail->name() == 'mail_2') || ($pdf_send_to == 'mail-2' && $mail->name() == 'mail') ){
            return $components;
        }
        if($enable_pdf == 'on'){ 
            $submission = WPCF7_Submission::get_instance();
            $contact_form_data = $submission->get_posted_data();
            $files            = $submission->uploaded_files();
           
            require UACF7_PATH . 'third-party/vendor/autoload.php';
            $upload_dir    = wp_upload_dir(); 
            $time_now      = time();
            $dir = $upload_dir['basedir'];
            $uploaded_files = [];
            $uacf7_dirname = $upload_dir['basedir'].'/uacf7-uploads';
            if ( ! file_exists( $uacf7_dirname ) ) {
                wp_mkdir_p( $uacf7_dirname ); 
            } 
            foreach ($_FILES as $file_key => $file) {
                array_push($uploaded_files, $file_key);
            }

            //  
            $uacf7_pdf_name = !empty(get_post_meta( $wpcf7->id(), 'uacf7_pdf_name', true )) ? get_post_meta( $wpcf7->id(), 'uacf7_pdf_name', true ) : get_the_title( $wpcf7->id() );
            $disable_header = !empty(get_post_meta( $wpcf7->id(), 'uacf7_pdf_disable_header', true )) ? get_post_meta( $wpcf7->id(), 'uacf7_pdf_disable_header', true ) : '';
            $disable_footer = !empty(get_post_meta( $wpcf7->id(), 'uacf7_pdf_disable_footer', true )) ? get_post_meta( $wpcf7->id(), 'uacf7_pdf_disable_footer', true ) : '';
            $customize_pdf = !empty(get_post_meta( $wpcf7->id(), 'customize_pdf', true )) ? get_post_meta( $wpcf7->id(), 'customize_pdf', true ) : '';
            $pdf_bg_upload_image = !empty(get_post_meta( $wpcf7->id(), 'pdf_bg_upload_image', true )) ? get_post_meta( $wpcf7->id(), 'pdf_bg_upload_image', true ) : '';
            $customize_pdf_header = !empty(get_post_meta( $wpcf7->id(), 'customize_pdf_header', true )) ? get_post_meta( $wpcf7->id(), 'customize_pdf_header', true ) : '';
            $pdf_header_upload_image = !empty(get_post_meta( $wpcf7->id(), 'pdf_header_upload_image', true )) ? get_post_meta( $wpcf7->id(), 'pdf_header_upload_image', true ) : '';
            $pdf_header_img_height = !empty(get_post_meta( $wpcf7->id(), 'pdf_header_img_height', true )) ? get_post_meta( $wpcf7->id(), 'pdf_header_img_height', true ) : '';
            $pdf_header_img_width = !empty(get_post_meta( $wpcf7->id(), 'pdf_header_img_width', true )) ? get_post_meta( $wpcf7->id(), 'pdf_header_img_width', true ) : '';
            $pdf_header_img_aline = !empty(get_post_meta( $wpcf7->id(), 'pdf_header_img_aline', true )) ? get_post_meta( $wpcf7->id(), 'pdf_header_img_aline', true ) : '';
            $customize_pdf_footer = !empty(get_post_meta( $wpcf7->id(), 'customize_pdf_footer', true )) ? get_post_meta( $wpcf7->id(), 'customize_pdf_footer', true ) : '';
            $custom_pdf_css = !empty(get_post_meta( $wpcf7->id(), 'custom_pdf_css', true )) ? get_post_meta( $wpcf7->id(), 'custom_pdf_css', true ) : ''; 
            $pdf_content_color = !empty(get_post_meta( $wpcf7->id(), 'pdf_content_color', true )) ? get_post_meta( $wpcf7->id(), 'pdf_content_color', true ) : ''; 
            $pdf_content_bg_color = !empty(get_post_meta( $wpcf7->id(), 'pdf_content_bg_color', true )) ? get_post_meta( $wpcf7->id(), 'pdf_content_bg_color', true ) : '';  
            $pdf_header_color = !empty(get_post_meta( $wpcf7->id(), 'pdf_header_color', true )) ? get_post_meta( $wpcf7->id(), 'pdf_header_color', true ) : ''; 
            $pdf_header_bg_color = !empty(get_post_meta( $wpcf7->id(), 'pdf_header_bg_color', true )) ? get_post_meta( $wpcf7->id(), 'pdf_header_bg_color', true ) : '';  
            $pdf_footer_color = !empty(get_post_meta( $wpcf7->id(), 'pdf_footer_color', true )) ? get_post_meta( $wpcf7->id(), 'pdf_footer_color', true ) : ''; 
            $pdf_footer_bg_color = !empty(get_post_meta( $wpcf7->id(), 'pdf_footer_bg_color', true )) ? get_post_meta( $wpcf7->id(), 'pdf_footer_bg_color', true ) : '';  
            $pdf_bg_upload_image =  !empty($pdf_bg_upload_image) ? 'background-image: url("'.esc_attr( $pdf_bg_upload_image ).'");' : '';
            $pdf_header_upload_image =  !empty($pdf_header_upload_image) ? '<img src="'.esc_attr( $pdf_header_upload_image ).'" style="height: 60; max-width: 100%; ">' : '';
            $mpdf = new \Mpdf\Mpdf([ 
                'fontdata' => [ // lowercase letters only in font key
                    'dejavuserifcond' => [
                        'R' => 'DejaVuSansCondensed.ttf',
                    ]
                ],
                'mode' => 'utf-8',
                'default_font' => 'dejavusanscond',
                'margin_header' => 0,
                'margin_footer' => 0,
                'format' => 'A4', 
                'margin_left' => 0,
                'margin_right' => 0
            ]); 
            $replace_key = [];

            // PDF Style
            $pdf_style = ' <style>
                body {
                     '.$pdf_bg_upload_image.'
                    background-repeat:no-repeat;
                    background-image-resize: 6; 
                }
                .pdf-header{
                    height: 60px;   
                    background-color: '.esc_attr( $pdf_header_bg_color ).';
                    color : '.esc_attr( $pdf_header_color ).'; 
                }
                .pdf-footer{ 
                    background-color: '.esc_attr( $pdf_footer_bg_color ).';
                    color : '.esc_attr( $pdf_footer_color ).'; 
                }
                .pdf-content{ 
                    background-color: '.esc_attr( $pdf_content_bg_color ).';
                    color : '.esc_attr( $pdf_content_color ).';
                    padding: 20px;
                    height: 100%;
                }
                .header-logo{
                    text-align: '.esc_attr( $pdf_header_img_aline ).'; 
                    float: left; 
                    width: 20%;
                }
                .header-content{
                    float: right; 
                    width: 80%
                    
                }
                '.$custom_pdf_css.'
            </style>';
            $replace_value = []; 

            // PDF Header
            if($disable_header != 'on'){
                $mpdf->SetHTMLHeader('
                <div class="pdf-header"  >
                        <div class="header-logo"  >
                            '.$pdf_header_upload_image.'
                        </div>    
                        <div class="header-content">
                        '.$customize_pdf_header.'
                        </div>
                </div>
                ');
            }
             
            // PDF Footer
            if($disable_footer != 'on'){ 
                $mpdf->SetHTMLFooter('<div class="pdf-footer">'.$customize_pdf_footer.'</div>');
            }

            $repeater_value = []; 
            foreach($contact_form_data as $key => $value){
                if(!in_array($key, $uploaded_files)){ 
                    $replace_key[] = '['.$key.']';
                    
                    // Repeater value gate
                    if (strpos($key, '__') !== false) {
                        $name_parts = explode('__', $key); 
                        if(is_array($name_parts)){
                            $repeater_value[$name_parts[0]][$name_parts[1]] = $name_parts[0];  
                        }
                    }
                    
                    if( is_array($value)){
                        
                        $data = '';
                        $count_value = count($value);
                        for ($x = 0; $x < $count_value ; $x++) {
                            if($x == 0){ 
                                $data .= $value[$x];
                            }else{
                                $data .= ', '.$value[$x]; 
                            }
                            
                        } 
                        $value = $data;
                    } 
                    $replace_value[] = $value;
                }
                
            } 
            foreach ($files as $file_key => $file) {
                if(!empty($file)){
                    if(in_array($file_key, $uploaded_files)){ 
                        $file = is_array( $file ) ? reset( $file ) : $file; 
                        $dir_link = '/uacf7-uploads/'.$time_now.'-'.$file_key.'-'.basename($file);
                        copy($file, $dir.$dir_link); 
                        $replace_key[] = '['.$file_key.']';
                        $replace_value[] = $upload_dir['baseurl'].$dir_link; 
                    }  
                }
                
            } 
 
            // Repeater value
            if(isset($_POST['_uacf7_repeaters'])){
                $repeaters = json_decode(stripslashes($_POST['_uacf7_repeaters'])); 
                
                if(isset($repeaters) || is_array($repeaters)){
                    $repeater_data = apply_filters('uacf7_pdf_generator_replace_data', $repeater_value, $repeaters, $customize_pdf);
                    
                    $customize_pdf = str_replace($repeater_data['replace_re_key'], $repeater_data['replace_re_value'], $customize_pdf);
                } 
            } 

            $pdf_content = str_replace($replace_key, $replace_value, $customize_pdf);

            // Replace PDF Name
            $uacf7_pdf_name = str_replace($replace_key, $replace_value, $uacf7_pdf_name);

            $mpdf->SetTitle($uacf7_pdf_name);

             // PDF Footer Content
            $mpdf->WriteHTML($pdf_style.'<div class="pdf-content">'.nl2br($pdf_content).'   </div>');

            $pdf_url = $dir.'/uacf7-uploads/'.$uacf7_pdf_name.'.pdf';
            $mpdf->Output($pdf_url, 'F'); // save to databaes 
           
            $components['attachments'][] = $pdf_url; 
        }
        return $components;
      
    }

    /*
    * Function create tab panel
    */
    public function uacf7_add_panel( $panels ) {
		$panels['uacf7-pdf-generator-panel'] = array(
            'title'    => __( 'UACF7 PDF Generator', 'ultimate-addons-cf7' ),
			'callback' => array( $this, 'uacf7_create_pdf_generator_panel_fields' ),
		);
		return $panels;

	}
    
   
    /*
    * Function PDF Generator fields
    */
    public function uacf7_create_pdf_generator_panel_fields($post) {

         // get existing value 
         $all_fields = $post->scan_form_tags();
         
         $uacf7_enable_pdf_generator = get_post_meta( $post->id(), 'uacf7_enable_pdf_generator', true ); 
         $pdf_send_to = get_post_meta( $post->id(), 'pdf_send_to', true ); 
         $uacf7_pdf_name = get_post_meta( $post->id(), 'uacf7_pdf_name', true ); 
         $disable_header = get_post_meta( $post->id(), 'uacf7_pdf_disable_header', true ); 
         $disable_footer = get_post_meta( $post->id(), 'uacf7_pdf_disable_footer', true ); 
         $customize_pdf = get_post_meta( $post->id(), 'customize_pdf', true ); 
         $pdf_bg_upload_image = get_post_meta( $post->id(), 'pdf_bg_upload_image', true );  
         $customize_pdf_header = get_post_meta( $post->id(), 'customize_pdf_header', true ); 
         $pdf_header_upload_image = get_post_meta( $post->id(), 'pdf_header_upload_image', true );  
         $customize_pdf_footer = get_post_meta( $post->id(), 'customize_pdf_footer', true ); 
         $custom_pdf_css = get_post_meta( $post->id(), 'custom_pdf_css', true ); 
         $pdf_content_color = get_post_meta( $post->id(), 'pdf_content_color', true ); 
         $pdf_content_bg_color = get_post_meta( $post->id(), 'pdf_content_bg_color', true ); 
         $pdf_header_color = get_post_meta( $post->id(), 'pdf_header_color', true ); 
         $pdf_header_bg_color = get_post_meta( $post->id(), 'pdf_content_bg_color', true ); 
         $pdf_footer_color = get_post_meta( $post->id(), 'pdf_footer_color', true ); 
         $pdf_footer_bg_color = get_post_meta( $post->id(), 'pdf_footer_bg_color', true ); 
        ?>
        <h2><?php echo esc_html__( 'PDF Generator', 'ultimate-addons-cf7' ); ?></h2>
        <p><?php echo esc_html__('This feature will help you to create pdf after form submission, send to mail, stored pdf into the server and export pdf form the admin.','ultimate-addons-cf7'); ?></p>
        <div class="uacf7-doc-notice">
            <?php echo sprintf( 
                __( 'Not sure how to set this? Check our step by step  %1s.', 'ultimate-addons-cf7' ),
                '<a href="https://themefic.com/docs/uacf7/free-addons/contact-form-7-pdf-generator/" target="_blank">documentation</a>'
            ); ?>  
        </div> 
         <fieldset>
           <div class="ultimate-placeholder-admin pdf-generator-admin">
               <div class="ultimate-placeholder-wrapper pdf-generator-wrap"> 
                  <h3> Option</h3>
                    <div class="uacf7pdf-fourcolumns">
                       <h4><?php _e('Enable PDF Generator', 'ultimate-addons-cf7'); ?></h4>
                       <label for="uacf7_enable_pdf_generator">  
                            <input id="uacf7_enable_pdf_generator" type="checkbox" name="uacf7_enable_pdf_generator" <?php checked( 'on', $uacf7_enable_pdf_generator ); ?> > <?php echo esc_html__( 'Enable', 'ultimate-addons-cf7' ); ?>
                        </label><br><br>
                    </div>
                    <div class="uacf7pdf-fourcolumns">
                       <h4><?php _e('PDF Title', 'ultimate-addons-cf7'); ?></h4>
                       <label for="uacf7_pdf_name">  
                            <input id="uacf7_pdf_name" type="text" ize="100%" name="uacf7_pdf_name"  value="<?php  echo esc_attr_e($uacf7_pdf_name); ?>" >.pdf
                        </label><br><br>
                    </div>
                 
                    <div class="uacf7pdf-fourcolumns">
                       <h4 ><?php _e('PDF Send To', 'ultimate-addons-cf7'); ?></h4>
                       <select name="pdf_send_to" id="event_summary">
                            <option <?php if($pdf_send_to == 'default') echo "selected"; ?> value="default" selected="selected"><?php echo esc_html__( 'Default', 'ultimate-addons-cf7' ); ?></option>
                            <option <?php if($pdf_send_to == 'mail-1') echo "selected"; ?> value="mail-1"><?php echo esc_html__( 'Mail 1', 'ultimate-addons-cf7' ); ?></option> 
                            <option <?php if($pdf_send_to == 'mail-2') echo "selected"; ?> value="mail-2"><?php echo esc_html__( 'Mail 2', 'ultimate-addons-cf7' ); ?></option>   
                        </select><br><br>
                    </div>
                 
                    <div class="uacf7pdf-fourcolumns">
                       <h4 ><?php _e('Disable Header and Footer', 'ultimate-addons-cf7'); ?></h4> 
                       <label for="uacf7_pdf_disable_header">  
                            <input id="uacf7_pdf_disable_header" type="checkbox" name="uacf7_pdf_disable_header" <?php checked( 'on', $disable_header ); ?> > <?php echo esc_html__( ' Disable Header ', 'ultimate-addons-cf7' ); ?>
                        </label> 
                        <br>
                       <label for="uacf7_pdf_disable_footer">  
                            <input id="uacf7_pdf_disable_footer" type="checkbox" name="uacf7_pdf_disable_footer" <?php checked( 'on', $disable_footer ); ?> ><?php echo esc_html__( 'Disable Footer', 'ultimate-addons-cf7' ); ?> 
                        </label>
                    </div>
                 
                    <hr>
                    <div class="uacf7pdf-onecolumns">
                        <h3><?php _e('Customize PDF', 'ultimate-addons-cf7'); ?></h3> 
                        
                         <hr>
                    </div> 
                   <div class="uacf7pdf-twocolumns">
                       <h4><?php _e('Background Image', 'ultimate-addons-cf7'); ?></h4>
                       <input id="pdf_bg_upload_image" size="60%" class="wpcf7-form-field" name="pdf_bg_upload_image" value="<?php echo esc_attr_e($pdf_bg_upload_image); ?>" type="text" /> 
                       <a href="#" id="upload_pdf_image_button" class="button" ><span> <?php _e('Select or Upload picture', 'ultimate-addons-cf7'); ?> </span></a> <br /> 
              
                   </div>
                   <div class="uacf7pdf-fourcolumns">
                        <h4><?php _e('Color', 'ultimate-addons-cf7'); ?> </h4> 
                        <input type="text" id="uacf7-uacf7style-input-color" name="pdf_content_color" class="uacf7-color-picker" value="<?php echo esc_attr_e($pdf_content_color); ?>" placeholder="<?php echo esc_html__( 'Color', 'ultimate-addons-cf7' ); ?>">  
                    </div>
                    <div class="uacf7pdf-fourcolumns">
                        <h4><?php _e('Background Color', 'ultimate-addons-cf7'); ?>  </h4> 
                        <input type="text" id="uacf7-uacf7style-input-color" name="pdf_content_bg_color" class="uacf7-color-picker" value="<?php echo esc_attr_e($pdf_content_bg_color); ?>" placeholder="<?php echo esc_html__( 'Background color', 'ultimate-addons-cf7' ); ?>">  
                    </div>  
                   <div class="uacf7pdf-onecolumns">
                        <p> <strong><?php echo esc_html__( 'Form Tags :', 'ultimate-addons-cf7' ); ?>  </strong>
                            <strong>
                                <?php
                                    foreach ($all_fields as $tag) {
                                        if ($tag['type'] != 'submit') {
                                            echo '<span>['.esc_attr($tag['name']).']</span> ';
                                        }
                                    }
                                ?>
                            </strong>
                        </p>

                        <label for="customize_pdf">  
                        <?php  
                        wp_editor( $customize_pdf, 'post_meta_box', array('textarea_name'=>'customize_pdf', 'media_buttons' => false )); ?>

                            <!-- <input type="text" id="customize_pdf" name="customize_pdf" class="large-text" value="<?php echo esc_attr_e($customize_pdf); ?>" placeholder="<?php echo esc_html__( 'Enter Your Custom CSS', 'ultimate-addons-cf7' ); ?>">  -->
                        </label><br><br>
                   </div>
                  
                    <hr>
                    <div class="uacf7pdf-onecolumns">
                        <h3><?php echo esc_html__( 'Customize PDF header', 'ultimate-addons-cf7' ); ?> </h3> 
                        <hr> 
                        <p> <strong><?php echo esc_html__( 'header and footer page numbers & date Tags :', 'ultimate-addons-cf7' ); ?>  
                                <span>{PAGENO}</span>, 
                                <span>{DATE j-m-Y}</span>, 
                                <span>{nb}</span>, 
                                <span>{nbpg}</span>
                            </strong>
                            
                        </p>
                    </div>  
                   <div class="uacf7pdf-twocolumns">
                       <h4><?php _e('Header Image', 'ultimate-addons-cf7'); ?></h4>
                       <input id="upload_image" size="60%" class="wpcf7-form-field" name="pdf_header_upload_image" value="<?php echo esc_attr_e($pdf_header_upload_image); ?>" type="text" /> 
                       <a href="#" id="upload_image_button" class="button" ><span> <?php _e('Select or Upload picture', 'ultimate-addons-cf7'); ?> </span></a> <br /> 
                    </div> 
                    <div class="uacf7pdf-fourcolumns">
                        <h4><?php _e('Color', 'ultimate-addons-cf7'); ?> </h4> 
                        <input type="text" id="uacf7-uacf7style-input-color" name="pdf_header_color" class="uacf7-color-picker" value="<?php echo esc_attr_e($pdf_header_color); ?>" placeholder="<?php echo esc_html__( 'Color', 'ultimate-addons-cf7' ); ?>">  
                    </div>
                    <div class="uacf7pdf-fourcolumns">
                        <h4><?php _e('Background Color', 'ultimate-addons-cf7'); ?>  </h4> 
                        <input type="text" id="uacf7-uacf7style-input-color" name="pdf_header_bg_color" class="uacf7-color-picker" value="<?php echo esc_attr_e($pdf_header_bg_color); ?>" placeholder="<?php echo esc_html__( 'Background color', 'ultimate-addons-cf7' ); ?>">  
                    </div>  
                     
                   <div class="uacf7pdf-onecolumns">
                    <br>
                    <br>
                        <label for="customize_pdf">  
                             <?php wp_editor( $customize_pdf_header, 'post_meta_box2', array('textarea_name'=>'customize_pdf_header', 'media_buttons' => false )); ?> 
                        </label><br><br>
                   </div>
                   <div class="uacf7pdf-onecolumns">
                        <h3><?php echo esc_html__( 'Customize PDF footer', 'ultimate-addons-cf7' ); ?> </h3>
                        <hr> 
                        <div class="uacf7pdf-fourcolumns">
                            <h4><?php _e('Color', 'ultimate-addons-cf7'); ?> </h4> 
                            <input type="text" id="uacf7-uacf7style-input-color" name="pdf_footer_color" class="uacf7-color-picker" value="<?php echo esc_attr_e($pdf_footer_color); ?>" placeholder="<?php echo esc_html__( 'Color', 'ultimate-addons-cf7' ); ?>">  
                        </div>
                        <div class="uacf7pdf-fourcolumns">
                            <h4><?php _e('Background Color', 'ultimate-addons-cf7'); ?>  </h4> 
                            <input type="text" id="uacf7-uacf7style-input-color" name="pdf_footer_bg_color" class="uacf7-color-picker" value="<?php echo esc_attr_e($pdf_footer_bg_color); ?>" placeholder="<?php echo esc_html__( 'Background color', 'ultimate-addons-cf7' ); ?>"> 
        
                        </div>  
                        <div class="uacf7pdf-onecolumns">
                            <label for="customize_pdf">  
                                <?php wp_editor( $customize_pdf_footer, 'post_meta_box4', array('textarea_name'=>'customize_pdf_footer', 'media_buttons' => false )); ?> 
                            </label><br><br>
                         </div>  
                   </div>
                   <div class="uacf7pdf-onecolumns">
                        <h3><?php echo esc_html__( 'Custom CSS', 'ultimate-addons-cf7' ); ?></h3>
                        <hr> 
                        <label for="customize_pdf">  
                            <input type="text" id="custom_pdf_css" name="custom_pdf_css" class="large-text" value="<?php echo esc_attr_e($custom_pdf_css); ?>" placeholder="<?php echo esc_html__( 'Customize PDF CSS', 'ultimate-addons-cf7' ); ?>"> 
                        </label><br><br>
                   </div>
                   
                  
               </div>
           </div>
        </fieldset>
        <?php
        wp_nonce_field( 'uacf7_pdf_generator_nonce_action', 'uacf7_pdf_generator_nonce' );
	}
    public function uacf7_save_contact_form( $form ) {
        
        if ( ! isset( $_POST ) || empty( $_POST ) ) {
			return;
		}
        if ( ! wp_verify_nonce( $_POST['uacf7_pdf_generator_nonce'], 'uacf7_pdf_generator_nonce_action' ) ) {
            return;
        } 
        if(isset($_POST['uacf7_enable_pdf_generator'])){
            update_post_meta( $form->id(), 'uacf7_enable_pdf_generator', sanitize_text_field($_POST['uacf7_enable_pdf_generator']) );
        }else{
            update_post_meta( $form->id(), 'uacf7_enable_pdf_generator', 'off' );
        }

        if(isset($_POST['uacf7_pdf_disable_header'])){
            update_post_meta( $form->id(), 'uacf7_pdf_disable_header', sanitize_text_field($_POST['uacf7_pdf_disable_header']) );
        }else{
            update_post_meta( $form->id(), 'uacf7_pdf_disable_header', 'off' );
        }
    
        if(isset($_POST['uacf7_pdf_disable_footer'])){
            update_post_meta( $form->id(), 'uacf7_pdf_disable_footer', sanitize_text_field($_POST['uacf7_pdf_disable_footer']) );
        }else{
            update_post_meta( $form->id(), 'uacf7_pdf_disable_footer', 'off' );
        }
        
        if(isset($_POST['uacf7_pdf_name'])){ 
            update_post_meta( $form->id(), 'uacf7_pdf_name', sanitize_text_field($_POST['uacf7_pdf_name']) );
        }
        if(isset($_POST['customize_pdf'])){ 
            update_post_meta( $form->id(), 'customize_pdf', $_POST['customize_pdf']) ;
        } 
        if(isset($_POST['pdf_send_to'])){ 
            update_post_meta( $form->id(), 'pdf_send_to', sanitize_text_field($_POST['pdf_send_to']) );
        }  
        if(isset($_POST['pdf_bg_upload_image'])){ 
            update_post_meta( $form->id(), 'pdf_bg_upload_image', sanitize_text_field($_POST['pdf_bg_upload_image']) );
        }   
        if(isset($_POST['pdf_bg_upload_image'])){ 
            update_post_meta( $form->id(), 'pdf_bg_upload_image', sanitize_text_field($_POST['pdf_bg_upload_image']) );
        }    
        if(isset($_POST['customize_pdf_header'])){ 
            update_post_meta( $form->id(), 'customize_pdf_header', $_POST['customize_pdf_header']);
        }     
        if(isset($_POST['pdf_header_upload_image'])){ 
            update_post_meta( $form->id(), 'pdf_header_upload_image', sanitize_text_field($_POST['pdf_header_upload_image']) );
        }     
        if(isset($_POST['pdf_header_upload_image'])){ 
            update_post_meta( $form->id(), 'pdf_header_upload_image', sanitize_text_field($_POST['pdf_header_upload_image']) );
        }      
        if(isset($_POST['customize_pdf_footer'])){ 
            update_post_meta( $form->id(), 'customize_pdf_footer', $_POST['customize_pdf_footer']);
        }      
        if(isset($_POST['custom_pdf_css'])){ 
            update_post_meta( $form->id(), 'custom_pdf_css', sanitize_text_field($_POST['custom_pdf_css']) );
        }       
        if(isset($_POST['pdf_content_bg_color'])){ 
            update_post_meta( $form->id(), 'pdf_content_bg_color', sanitize_text_field($_POST['pdf_content_bg_color']) );
        }      
        if(isset($_POST['pdf_header_color'])){ 
            update_post_meta( $form->id(), 'pdf_header_color', sanitize_text_field($_POST['pdf_header_color']) );
        }          
        if(isset($_POST['pdf_header_bg_color'])){ 
            update_post_meta( $form->id(), 'pdf_header_bg_color', sanitize_text_field($_POST['pdf_header_bg_color']) );
        }           
        if(isset($_POST['pdf_footer_color'])){ 
            update_post_meta( $form->id(), 'pdf_footer_color', sanitize_text_field($_POST['pdf_footer_color']) );
        }            
        if(isset($_POST['pdf_footer_bg_color'])){ 
            update_post_meta( $form->id(), 'pdf_footer_bg_color', sanitize_text_field($_POST['pdf_footer_bg_color']) );
        }
         
    }
   
     
}
 
new UACF7_PDF_GENERATOR();
