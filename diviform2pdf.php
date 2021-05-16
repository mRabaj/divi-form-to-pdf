<?php
/**
 * Plugin Name: Divi Form2PDF
 * Plugin URI: https://www.undefined.fr/
 * Description: Retrieve the data in order to download it as a PDF   
 * Version: 1.0.0 
 * Author Name: Mohammed Rabaj (rabaj.mohammed@outlook.com)  
 * Author: Mohammed Rabaj 
 * Domain Path: /languages  
 * Text Domain: linky 
 * Author URI: https://www.aboujihade.com  
 */


//constants
define('FORM_TO_PDF_VERSION', '1.0');
define('FORM_TO_PDF_ITEM_NAME', 'Divi Form to PDF');
define('FORM_TO_PDF_AUTHOR_NAME', 'Mohammed RABAJ');
define('FORM_TO_PDF_FILE',__FILE__);
define('IMG_TABLE_NAME', $wpdb->prefix.'df2p_img');
define('TMP_TABLE_NAME', $wpdb->prefix.'df2p_templates');

add_action('plugins_loaded', 'form_to_pdf_init');

function form_to_pdf_init() {
    load_plugin_textdomain('form-pdf', false, dirname(plugin_basename(FORM_TO_PDF_FILE)) . '/languages/');


 add_filter('et_contact_page_headers', 'forms_to_pdf_et_contact_page_headers', 10, 10);
//    add_action('add_meta_boxes', 'sb_divi_cfd_register_meta_box');

    add_action('admin_enqueue_scripts', 'forms_to_pdf_enqueue', 9999);
    add_action('init', 'forms_to_pdf_pt_init');
    
    add_action('admin_head', 'forms_to_pdf_admin_head');
    add_action('admin_init', 'forms_to_pdf_download_csv', 1, 1);
    add_action('admin_init', 'forms_to_pdf_download_pdf', 1, 1);

    add_action('admin_init', 'delete_in_database_f2p', 1);
    add_action('admin_init', 'update_in_database_f2p', 1);
    add_action('admin_init', 'download_or_delete_img_f2p', 1);
    add_action('admin_menu', 'form_to_pdf_submenu');

}

function forms_to_pdf_enqueue() {

    wp_enqueue_style ('boostrap'      ,'https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css');
    wp_enqueue_style ('style'         , plugins_url('/css/style.css',FORM_TO_PDF_FILE));
    wp_enqueue_script('boostrap'      ,'https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js',"","",true);
    wp_enqueue_script('feather'       ,'https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.min.js',"","",true);
    wp_enqueue_script('form_to_pdf_js', plugins_url('/script/script.js', FORM_TO_PDF_FILE));    
}

function form_to_pdf_submenu() {
    add_submenu_page(
        'edit.php?post_type=formstopdf_db',
        __('Export', 'form-pdf'),
        __('Export', 'form-pdf'),
        'manage_options',
        'forms_to_pdf_home',
        'form_to_pdf_submenu_cb'
    );

    add_submenu_page(
        'edit.php?post_type=formstopdf_db',
        __('Templates', 'form-pdf'),
        __('Templates', 'form-pdf'),
        'manage_options',
        'forms_to_pdf_templates',
        'forms_to_pdf_templates_submenu_cb'
    );
    add_submenu_page(
        'edit.php?post_type=formstopdf_db',
        __('Imports', 'form-pdf'),
        __('Imports', 'form-pdf'),
        'manage_options',
        'forms_to_pdf_import',
        'forms_to_pdf_import_submenu_cb'
    );
}
if(!function_exists("removeAccents")){
    function remove_accents($str) {
        $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ', 'Ά', 'ά', 'Έ', 'έ', 'Ό', 'ό', 'Ώ', 'ώ', 'Ί', 'ί', 'ϊ', 'ΐ', 'Ύ', 'ύ', 'ϋ', 'ΰ', 'Ή', 'ή', '–');
        $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o', 'Α', 'α', 'Ε', 'ε', 'Ο', 'ο', 'Ω', 'ω', 'Ι', 'ι', 'ι', 'ι', 'Υ', 'υ', 'υ', 'υ', 'Η', 'η', '-');
        return str_replace($a, $b, $str);
    }
}

function CharacterCleaner($ch = '')
{ 
    // trim — Supprime les espaces (ou d'autres caractères) en début et fin de chaîne
    // preg_replace — Rechercher et remplacer par expression rationnelle standard
    // mb_strtolower — Met tous les caractères en minuscules
    $ch = remove_accents($ch);        
    $ch = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $ch);
    $ch= trim($ch);
    $ch = mb_strtolower($ch, 'UTF-8');
    $ch = preg_replace("/[_|+ -]+/", '-', $ch);
    return $ch;
}


function form_to_pdf_submenu_cb() {


    $url="/edit.php?post_type=formstopdf_db&page=forms_to_pdf_home";
    echo '<div class="container">';
                if ($posts = get_posts('post_type=formstopdf_db&posts_per_page=-1')) {
                    $forms = array();
                                        
                    foreach ($posts as $post) {
                        if ($data = get_post_meta($post->ID, 'forms_to_pdf', true)) {                        
                            $forms[$data['extra']['submitted_on']] = $data['extra']['submitted_on']; 
                        }
                    }                    
                    echo '<h5>' . __('View Form Information', 'form-pdf') . ':</h5>';
                 
                    echo '<form method="" name="f2p_name" id="f2p_name" action="'.admin_url(esc_url($url)).'">';   
                        echo '<div class="row">';                 
                            echo '<div class="col-12">';
                                echo' <div class="card">
                                    <div class="card-body">';
                                     echo '<h5 class="card-subtitle mb-2 text-muted">'. __('Select form name :', 'form-pdf') .'</h5>';
                                        echo '<select class="form-select" id="form-name" onchange="select_f2p()" name="form-name">';
                                            echo '<option value="1">'. __('Select form name', 'form-pdf') .'</option>';
                                            $alpha_forms = array();
                                            foreach ($forms as $form) {
                                                $alpha_forms[$form] = $form;
                                            }
                                            // form sorting
                                            ksort($alpha_forms);
                                            foreach ($alpha_forms as $form) {   
                                                echo '<option  value="' . $form . '" ' . (isset($_REQUEST['form-name']) && $_REQUEST['form-name'] == $form ? 'selected="selected"' : '') . '>' . $form . '</option>';
                                            }
                                        echo '</select>';
                                echo '</div></div>';
                            echo '</div>';
                        // echo '<input type="submit" name="" class="button-primary" value="'. __('View Form', 'form-pdf').'" />';
                    echo '</form>'; // fin de form 
                        
                    echo '<div class="col-12" id="display_setup">';
                        echo '<div  class="alert alert-light" role="alert">'.__('To change the Field title, Hide field and change the position of fields ','form-pdf').'<a href="#" class="btn btn-outline-info" onclick="displaySettingsModal()">'.__('from here.','form-pdf').'</a></div>';
                    echo '</div>';

                    if (isset($_REQUEST['form-name']) && !empty($_REQUEST['form-name'])) {
                            
                        $form_name= $_REQUEST['form-name'];
                        $url.='&form-name='.$form_name;

                        foreach ($posts as $post) {
                            if ($data = get_post_meta($post->ID, 'forms_to_pdf', true)) {  
                                if ($data['extra']['submitted_on'] == $_REQUEST['form-name']) {                                    
                                    $nombreId[]=$post->ID;                                    
                                }
                            }
                        }
                        //calculates the number of entries 
                        $total_entries=count($nombreId);
                            ?>
                             <!-- Modal Display settings -->
                        <form method="post">
                            <div class="modal fade" id="f2p_display-settings" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="staticBackdropLabel"><?php echo __('Display settings','form-pdf');?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <?php                                            
                                                        
                                            echo'<ul class="list-group">';
                                                   if ($posts = get_posts('post_type=formstopdf_db&posts_per_page=-1')) {                                  
                                                        foreach ($posts as $post) {
                                                            if ($data = get_post_meta($post->ID, 'forms_to_pdf', true)) {   
                                                                if ($data['extra']['submitted_on'] == $_REQUEST['form-name']){     
                                                                    
                                                                    foreach($data['data'] as $key => $field){
                                                                        echo'<li class="list-group-item">';
                                                                            echo '<div class="input-group mb-3">';                                                                            
                                                                                echo '<span class="input-group-text" id="basic-addon3">'.__($field['original_name'],'form-pdf').'</span>';
                                                                                echo '<input  type="text" class="form-control" id="basic-url" name="'.$field['original_name'].'" value="'.__($field['original_name'],'form-pdf').'" aria-describedby="basic-addon3">';
                                                                                echo'<span class="input-group-text"><span class="dashicons dashicons-visibility"></span><input class="txt_show" type="hidden" name="visible_'.$field['original_name'].'" id="" value="1"></span>'; 
                                                                            echo'</div>';                                                                
                                                                        echo'</li>';
                                                                    }
                                                                    echo'<li class="list-group-item">';
                                                                        echo '<div class="input-group mb-3">';  
                                                                            echo '<span class="input-group-text" id="basic-addon3">'.__('Submit date','form-pdf').'</span>';
                                                                            echo '<input  type="text" class="form-control" id="basic-url" name="submit_date" value="'.__('Submit date','form-pdf').'" aria-describedby="basic-addon3">';
                                                                            echo'<span class="input-group-text"><span class="dashicons dashicons-visibility"></span><input class="txt_show" type="hidden" name="visible_submit_date" id="" value="1"></span>'; 
                                                                        echo'</div>';  
                                                                    echo'</li>';
                                                                    break; 
                                                                }                      
                                                            }
                                                        }
                                                    }
                                            echo'</ul>';                                                                                                     
                                                                                                                                            
                                            ?>         
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo __('Close','form-pdf') ?></button>
                                            <button type="submit" name="save_field_settings" value="" class="btn btn-primary"><?php echo __('Save Changes','form-pdf') ?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>  <!-- End Modal Display settings -->
                        </form>

                         <!-- start of the form -->
                        <form method="post" name="" id="" action="<?php echo(admin_url(esc_url($url)))?>">                           
                            <!-- Modal Edit Information -->
                             <?php                            
                             foreach ($posts as $key => $post) { 
                                if($data = get_post_meta($post->ID, 'forms_to_pdf', true)){ 
                                    $id_update=$post->ID;  
                                    ?>            
                                    <div class="modal fade" id="f2p_edit-information<?= $post->ID ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="staticBackdropLabel"><?php echo __('Edit Information','form-pdf');?></h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                <div class="modal-body">
                                                    <?php                                              
                                                    if ($data['extra']['submitted_on'] == $_REQUEST['form-name']) {                      
                                                        foreach ($data['data'] as $key => $field) {
                                                            echo '<div class="input-group mb-3">';
                                                                echo '<input type="hidden" class="form_control" id="basic-addon3" name="field_label_'.$key.'_'.$id_update.'" value="'.$field['label'].'">';
                                                                echo '<span class="input-group-text" id="basic-addon3">'.$field['label'].'</span>';
                                                                echo '<input type="text" class="form-control" id="basic-url" name="field_value_'.$key.'_'.$id_update.'" value="'.$field['value'].'" aria-describedby="basic-addon3">';                            
                                                            echo '</div>';       
                                                        }
                                                            echo '<div class="input-group mb-3">';  
                                                                echo '<span class="input-group-text" id="basic-addon3">'.__('Date','form-pdf').'</span>';
                                                                echo '<input type="text" class="form-control" id="basic-url" disabled value="'.$post->post_date.'" aria-describedby="basic-addon3">';
                                                            echo '</div>';                                                                                                     
                                                    }                                                                                                    
                                                    ?>         
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo __('Close','form-pdf') ?></button>
                                                    <button type="submit" name="update_data" value="<?= $id_update ?>" class="btn btn-primary"><?php echo __('Save Changes','form-pdf') ?></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>  <!-- End Modal Edit Information   -->                 
                                   
                             <?php }
                             } ?>

                                <div class="row" >
                                    <div class="card" id="data-filter">  
                                        <div class="card-body">                             
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <input type="date" name="startdate" id="startdate" onchange="verifyDate(1);" value="<?php echo ((isset($_REQUEST['startdate']) && !empty($_REQUEST['startdate'])) ? ($_REQUEST['startdate']) : '');?>" class="form-control">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input type="date" name="enddate" id="enddate" onchange="verifyDate(2);" value="<?php echo((isset($_REQUEST['enddate']) && !empty($_REQUEST['enddate'])) ? ($_REQUEST['enddate']) : '');?>" class="form-control">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <button type="submit" name="searchdate" id="searchdate"  value="" title="<?php echo(__('Search By Date','form-pdf'));?>" class="btn btn-outline-success"><?php echo(__('Search By Date','form-pdf'));?></button>
                                                    </div>
                                                    <div class="form-floating col-md-3">                
                                                        <input type="text" class="form-control" id="f2p-search" name="f2p-search" placeholder=""  value="<?php echo ((isset($_REQUEST['f2p-search'])) && !empty($_REQUEST['f2p-search']) ? htmlspecialchars(stripslashes(sanitize_text_field($_REQUEST['f2p-search']))) : ''); ?>"/>
                                                        <label for="f2p-search"><?php echo __('Type something...','form-pdf'); ?></label>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="submit" name="f2p-search-btn" id="f2p-search-btn" title="<?php echo __('Search','form-pdf'); ?>" class="btn btn-outline-success" ><?php echo __('Search','form-pdf'); ?></button>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <a href="<?php echo(admin_url(esc_url($url)));?>" title="<?php echo __('Reset All','form-pdf'); ?>" class="btn btn-outline-secondary"><?php echo __('Reset All','form-pdf'); ?></a>
                                                    </div>
                                                </div>
                                        </div>
                                    </div>  <!-- end card-->  
                                </div> <!-- end div row -->
                                <?php                              
                                    echo' <div class="alert alert-light" id="bulk-export">';
                                        echo '<div class="row">';
                                            echo '<div class="col-4">';                                   
                                            //    echo'<div class="card-body">';                                          
                                                        echo'<div class="row">'; 
                                                            echo '<div class="col-md-6">';
                                                                echo '<select class="form-select" id="bulk-action-selector" name="action_selector">';
                                                                    echo '<option value="-1">'. __('Bulk Actions', 'form-pdf') .'</option>';
                                                                    echo '<option value="delete">' . __('Delete', 'form-pdf') . '</option>';
                                                                echo '</select>';
                                                            echo '</div>';  //fin col 
                                                        echo '<input type="submit" id="todoaction" name="btnaction"  class="btn btn-outline-primary col-md-3" value="'. __('Apply', 'form-pdf').'"/>';
                                                    echo '</div>'; //  fin row
                                            //     echo '</div>'; //  fin card-body
                                            // echo '</div>'; // fin card select export
                                            echo '</div>';  //fin col    
                                            echo '<div class="col-4">';                                                                                  
                                                echo'<div class="row">'; 
                                                    echo '<div class="col-md-6">';
                                                        echo '<select class="form-select" id="export_form" name="export_form">';
                                                            echo '<option value="-1">'. __('Export to...', 'form-pdf') .'</option>';
                                                            echo '<option value="pdf">' . __('Download PDF FILE', 'form-pdf') . '</option>';
                                                            echo '<option value="csv">' . __('Download CSV File', 'form-pdf') . '</option>';
                                                        echo '</select>';
                                                    echo '</div>';  //fin col 
                                                     echo '<input type="submit" name="download" id="buttonDownload"  class="btn btn-outline-primary col-md-6" value="'. __('Export the Form', 'form-pdf').'">';      
                                                echo '</div>';                                                  
                                            echo '</div>'; //fin col  
                                            // Number of item in table
                                            echo '<div class="d-flex justify-content-end"><span class="">'.(($total_entries == 1) ? "1 " . __('item','form-pdf') : $total_entries . ' ' . __('items','form-pdf')).'</span></div>';         
                                        echo '</div>'; //  fin row                                           
                                    echo '</div>';  // fin card select bulk actions  id="bulk-export"   

                                    echo '<div class="row">'; 
                                    echo '<div class="col-12">'; 
                                   
                                        ?>

                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <td id="cb" class="manage-column column-cb check-column">
                                                    <?php
                                                        echo '<td id="cb" class="manage-column column-cb check-column"><input type="checkbox" id="cb-select-all-1" /></td>';
                                                        echo '<th style="width: 32px;" class="manage-column"></th>';

                                                        if ($posts = get_posts('post_type=formstopdf_db&posts_per_page=-1')) {
                                                            foreach ($posts as $post) { 
                                                                if ($data = get_post_meta($post->ID, 'forms_to_pdf', true)){
                                                                    if ($data['extra']['submitted_on'] == $_REQUEST['form-name']){
                                                                            foreach($data['data'] as $key => $field){
                                                                                echo '<th class="manage-column" ><div class="'.$key.'" data-key="'.$field['original_name'].'">'.__($field['original_name'],'form-pdf').'</div></th>';
                                                                                }
                                                                                echo '<th class="manage-column" >'.__('Submit date','form-name').'</th>';
                                                                            break;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                                                                               
                                                    ?>
                                            </td>
                                        </tr>
                                    </thead>
                                    <tbody id="the-list">  <?php 
                                            //appliquer un filtre de limitation de mots à 30
                                            $display_character = (int) apply_filters('vsz_display_character_count',30);
                                            $id_find_search =array();  
                                            $id_find_date=array();
                                            $find_date=false;
                                            $find_searche=false;   
                                            // recherche par date 
                                            if(isset($_POST['searchdate']) || isset($_POST['f2p-search-btn'])){                               
                                                if (isset($_POST['startdate']) && isset($_POST['enddate']) && isset($_POST['f2p-search']) && !empty($_POST['startdate']) && !empty($_POST['enddate'])){                                                   
                                                    if(empty($_POST['f2p-search'])){
                                                      
                                                        foreach ($posts as $post) { 
                                                                if ($data = get_post_meta($post->ID, 'forms_to_pdf', true)){
                                                                        if ($data['extra']['submitted_on'] == $form_name){        
                                                                            if ($postdate=$post->post_date){                                                             
                                                                                $formatDate =date('Y-m-d\TH:i', strtotime($postdate));                                    
                                                                                $Year  = date("Y", strtotime($formatDate)); 
                                                                                $month = date("m", strtotime($formatDate));
                                                                                $day   = date("d", strtotime($formatDate));
                                        
                                                                                $startdate=$_POST['startdate'];
                                        
                                                                                $s_year  = date("Y", strtotime($startdate)); 
                                                                                $s_month = date("m", strtotime($startdate));
                                                                                $s_day   = date("d", strtotime($startdate));
                                        
                                                                                $enddate=$_POST['enddate'];
                                        
                                                                                $e_year  = date("Y", strtotime($enddate)); 
                                                                                $e_month = date("m",strtotime($enddate));
                                                                                $e_day   = date("d",strtotime($enddate));
                                
                                                                                if($Year >=$s_year){
                                                                                    if($month >= $s_month){
                                                                                        if($day>= $s_day){
                                                                                            if($Year <=$e_year){
                                                                                                if($month <= $e_month){
                                                                                                    if($day <= $e_day){                                                                                                    
                                                                                                        echo '<tr>';            
                                                                                                            echo '<th class="manage-column"></th>';
                                                                                                            echo '<td class="manage-column column-cb check-column" ><input type="checkbox" name="export_id[]" value="'.$post->ID.'" /></td>';
                                                                                                            echo '<td class="manage-column"><a href="#" data-id='.$post->ID.' onclick="displayModal(this);"><span data-feather="edit-3"></span></a></td>';                                                                            
                                                                                                            foreach ($data['data'] as $key => $field) { 
                                    
                                                                                                                if( !isset($_POST['visible_et_pb_contact_name_0']) || $_POST['visible_et_pb_contact_name_0']==1){
                                                                                                                
                                                                                                                    $name = esc_html(html_entity_decode($field['value']));
                                                                                                                    $name_value=trim(ucfirst(strtolower($name)));
                                                                                                                    
                                                                                                                    if(strlen($name_value) > $display_character){
                                                                                                                        echo '<td>'.substr($name_value, 0, $display_character).'...</td>';
                                                                                                                    }else{
                                                                                                                        echo '<td>'.$name_value.'</td>';
                                                                                                                    }
                                                                                                                }                                                                           
                                                                                                                if($field['value'] == 'email'){
                                                                                                                    
                                                                                                                    $email = esc_html(html_entity_decode($field['value']));
                                                                                                                    $email_value=trim(strtolower($email));                                                                  
                                                                                                            
                                                                                                                    if(strlen($email_value) > $display_character){
                                                                                                                        echo '<td><a href="mailto:'.$email_value.'" target="_blank">'.substr($email_value, 0, $display_character).'...</a></td>';
                                                                                                                    }else{
                                                                                                                        echo '<td><a href="mailto:'.$email_value.'" target="_blank">'.$email_value.'</a></td>';
                                                                                                                    }
                                                                                                                }                                                                                                                                               
                                                                                                            }                                                                      
                                                                                                            echo '<td data-head="post_date">'.$post->post_date.'</td>';  
                                                                                                                                                                        
                                                                                                        echo '</tr>';
                                                                                                            $find_date=true;                                                                                           
                                                                                                    }    
                                                                                                }
                                                                                            }
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                            }
                                                        } if(!$find_date) {
                                                                echo '<tr>';      
                                                                    echo'<div id="noRecordsFounds"> </div>';                                                                                                                             
                                                                    echo '<td colspan="6" >'.__('No records found.','form-pdf').'</td>';                                                          
                                                                echo '</tr>';
                                                            } 
                                                    }else if(!empty($_POST['f2p-search'])) {
                                                        foreach ($posts as $post) { 
                                                            if ($data = get_post_meta($post->ID, 'forms_to_pdf', true)){
                                                                    if ($data['extra']['submitted_on'] == $form_name){        
                                                                        if ($postdate=$post->post_date){                                                             
                                                                            $formatDate =date('Y-m-d\TH:i', strtotime($postdate));                                    
                                                                            $Year  = date("Y", strtotime($formatDate)); 
                                                                            $month = date("m", strtotime($formatDate));
                                                                            $day   = date("d", strtotime($formatDate));
                                    
                                                                            $startdate=$_POST['startdate'];
                                    
                                                                            $s_year  = date("Y", strtotime($startdate)); 
                                                                            $s_month = date("m", strtotime($startdate));
                                                                            $s_day   = date("d", strtotime($startdate));
                                    
                                                                            $enddate=$_POST['enddate'];
                                    
                                                                            $e_year  = date("Y", strtotime($enddate)); 
                                                                            $e_month = date("m",strtotime($enddate));
                                                                            $e_day   = date("d",strtotime($enddate));
                            
                                                                            if($Year >=$s_year){
                                                                                if($month >= $s_month){
                                                                                    if($day>= $s_day){
                                                                                        if($Year <=$e_year){
                                                                                            if($month <= $e_month){
                                                                                                if($day <= $e_day){                                                                                                   
                                                                                                    $find_date=true;
                                                                                                    $id_find_date[]=$post->ID;                                                                                            
                                                                                                }    
                                                                                            }
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                        }  
                                                    }
                                                }
                                                    // recherche par catégorie : Nom, adresse mail, message ..
                                                 if (isset($_POST['f2p-search']) && !empty($_POST['f2p-search']) && isset($_POST['startdate']) && isset($_POST['enddate'])){
                                                    if(!empty($_POST['startdate']) && !empty($_POST['enddate'])){
                                                        $f2psearch=htmlspecialchars(stripslashes(sanitize_text_field($_REQUEST['f2p-search'])));
                                                        $f2psearch=CharacterCleaner($f2psearch);
                                                            foreach ($posts as $post) { 
                                                                if($data = get_post_meta($post->ID, 'forms_to_pdf', true)) {                                                                        
                                                                        if ($data['extra']['submitted_on'] == $form_name) {                
                                                                            foreach ($data['data'] as $field) { 
                                                                                $fieldValue=CharacterCleaner($field['value']);
                                                                                     // recherche d'un terme dans une chaîne de caractère
                                                                                    if(stristr($fieldValue,$f2psearch)){                                               
                                                                                        $id_find_search[]=$post->ID;
                                                                                        $find_searche=true;
                                                                                    }
                                                                            }  
                                                                        } 
                                                                }
                                                            }                                                     
                                                        if($find_searche && !empty($id_find_date)){
                                                            $id_find_search = array_unique( $id_find_search);
                                                            $id_find_date = array_unique($id_find_date);
                                                            // Retourne un tableau contenant toutes les valeurs du tableau array dont les valeurs existent dans tous les arguments.
                                                            $intersect=array_intersect($id_find_search,$id_find_date);
                                                                foreach($intersect as $id){
                                                                    if($data = get_post_meta($id, 'forms_to_pdf', true)) {                                                                        
                                                                        if ($data['extra']['submitted_on'] == $form_name) {
                                                                            echo '<tr>';            
                                                                                    echo '<th class="manage-column"></th>';
                                                                                    echo '<td class="manage-column column-cb check-column" ><input type="checkbox" name="export_id[]" value="'.$post->ID.'" /></td>';
                                                                                    echo '<td class="manage-column"><a href="#" data-id='.$post->ID.' onclick="displayModal(this);"><span data-feather="edit-3"></span></a></td>';                                                                            
                                                                                    foreach ($data['data'] as $key => $field) { 
            
                                                                                        if( !isset($_POST['visible_et_pb_contact_name_0']) || $_POST['visible_et_pb_contact_name_0']==1){
                                                                                           
                                                                                            $name = esc_html(html_entity_decode($field['value']));
                                                                                            $name_value=trim(ucfirst(strtolower($name)));
                                                                                            
                                                                                            if(strlen($name_value) > $display_character){
                                                                                                echo '<td>'.substr($name_value, 0, $display_character).'...</td>';
                                                                                            }else{
                                                                                                echo '<td>'.$name_value.'</td>';
                                                                                            }
                                                                                        }                                                                           
                                                                                        if($field['value'] == 'email'){
                                                                                            
                                                                                            $email = esc_html(html_entity_decode($field['value']));
                                                                                            $email_value=trim(strtolower($email));                                                                  
                                                                                    
                                                                                            if(strlen($email_value) > $display_character){
                                                                                                echo '<td><a href="mailto:'.$email_value.'" target="_blank">'.substr($email_value, 0, $display_character).'...</a></td>';
                                                                                            }else{
                                                                                                echo '<td><a href="mailto:'.$email_value.'" target="_blank">'.$email_value.'</a></td>';
                                                                                            }
                                                                                        }                                                                                                                                               
                                                                                    }                                                                      
                                                                                    echo '<td data-head="post_date">'.$post->post_date.'</td>';  
                                                                                                                                                 
                                                                            echo '</tr>';
                                                                        } 
                                                                    }   
                                                                }
                                                        }                                                                                         
                                                        else {
                                                                echo '<tr>';                                                                                                                                   
                                                                    echo '<td colspan="6" >'.__('No records found.','form-pdf').'</td>';                                                          
                                                                echo '</tr>';
                                                            }                          
                                                    }else{
                                                        $f2psearch=htmlspecialchars(stripslashes(sanitize_text_field($_REQUEST['f2p-search'])));
                                                        $f2psearch=CharacterCleaner($f2psearch);
                                                        foreach ($posts as $post) { 
                                                            if($data = get_post_meta($post->ID, 'forms_to_pdf', true)) {                                                                        
                                                                    if ($data['extra']['submitted_on'] == $form_name) {                
                                                                        foreach ($data['data'] as $field) { 
                                                                            $fieldValue=CharacterCleaner($field['value']);
                                                                            if(stristr($fieldValue,$f2psearch)){                                               
                                                                                $id_find_search[]=$post->ID;
                                                                                $find_searche=true;
                                                                            }
                                                                        }  
                                                                    } 
                                                            }
                                                        }                                                     
                                                        if($find_searche){
                                                            $id_find_search = array_unique( $id_find_search);
                                                                foreach($id_find_search as $id){
                                                                    if($data = get_post_meta($id, 'forms_to_pdf', true)) {                                                                        
                                                                        if ($data['extra']['submitted_on'] == $form_name) {
                                                                            echo '<tr>';            
                                                                                    echo '<th class="manage-column"></th>';
                                                                                    echo '<td class="manage-column column-cb check-column" ><input type="checkbox" name="export_id[]" value="'.$post->ID.'" /></td>';
                                                                                    echo '<td class="manage-column"><a href="#" data-id='.$post->ID.' onclick="displayModal(this);"><span data-feather="edit-3"></span></a></td>';                                                                            
                                                                                    foreach ($data['data'] as $key => $field) { 
            
                                                                                        if( !isset($_POST['visible_et_pb_contact_name_0']) || $_POST['visible_et_pb_contact_name_0']==1){
                                                                                           
                                                                                            $name = esc_html(html_entity_decode($field['value']));
                                                                                            $name_value=trim(ucfirst(strtolower($name)));
                                                                                            
                                                                                            if(strlen($name_value) > $display_character){
                                                                                                echo '<td>'.substr($name_value, 0, $display_character).'...</td>';
                                                                                            }else{
                                                                                                echo '<td>'.$name_value.'</td>';
                                                                                            }
                                                                                        }                                                                           
                                                                                        if($field['value'] == 'email'){
                                                                                            
                                                                                            $email = esc_html(html_entity_decode($field['value']));
                                                                                            $email_value=trim(strtolower($email));                                                                  
                                                                                    
                                                                                            if(strlen($email_value) > $display_character){
                                                                                                echo '<td><a href="mailto:'.$email_value.'" target="_blank">'.substr($email_value, 0, $display_character).'...</a></td>';
                                                                                            }else{
                                                                                                echo '<td><a href="mailto:'.$email_value.'" target="_blank">'.$email_value.'</a></td>';
                                                                                            }
                                                                                        }                                                                                                                                               
                                                                                    }                                                                      
                                                                                    echo '<td data-head="post_date">'.$post->post_date.'</td>';  
                                                                                                                                                 
                                                                            echo '</tr>';
                                                                        } 
                                                                    }   
                                                                }
                                                        }else {
                                                            echo '<tr>';                                                                                                                                   
                                                                echo '<td colspan="6" >'.__('No records found.','form-pdf').'</td>';                                                          
                                                            echo '</tr>'; 
                                                        }             
                                                    } 
                                                }
                                                if(isset($_POST['f2p-search']) && empty($_POST['f2p-search']) && isset($_POST['startdate']) && isset($_POST['enddate']) && empty($_POST['startdate']) && empty($_POST['enddate'])){
                                                    foreach ($posts as $post) { 
                                                        if($data = get_post_meta($post->ID, 'forms_to_pdf', true)) {                                                                       
                                                            if ($data['extra']['submitted_on'] == $form_name) {
                                                                echo '<tr>';            
                                                                        echo '<th class="manage-column"></th>';
                                                                        echo '<td class="manage-column column-cb check-column" ><input type="checkbox" name="export_id[]" value="'.$post->ID.'" /></td>';
                                                                        echo '<td class="manage-column"><a href="#" data-id='.$post->ID.' onclick="displayModal(this);"><span data-feather="edit-3"></span></a></td>';                                                                            
                                                                        foreach ($data['data'] as $key => $field) { 

                                                                            if( !isset($_POST['visible_et_pb_contact_name_0']) || $_POST['visible_et_pb_contact_name_0']==1){
                                                                               
                                                                                $name = esc_html(html_entity_decode($field['value']));
                                                                                $name_value=trim(ucfirst(strtolower($name)));
                                                                                
                                                                                if(strlen($name_value) > $display_character){
                                                                                    echo '<td>'.substr($name_value, 0, $display_character).'...</td>';
                                                                                }else{
                                                                                    echo '<td>'.$name_value.'</td>';
                                                                                }
                                                                            }                                                                           
                                                                            if($field['value'] == 'email'){
                                                                                
                                                                                $email = esc_html(html_entity_decode($field['value']));
                                                                                $email_value=trim(strtolower($email));                                                                  
                                                                        
                                                                                if(strlen($email_value) > $display_character){
                                                                                    echo '<td><a href="mailto:'.$email_value.'" target="_blank">'.substr($email_value, 0, $display_character).'...</a></td>';
                                                                                }else{
                                                                                    echo '<td><a href="mailto:'.$email_value.'" target="_blank">'.$email_value.'</a></td>';
                                                                                }
                                                                            }                                                                                                                                               
                                                                        }                                                                      
                                                                        echo '<td data-head="post_date">'.$post->post_date.'</td>';  
                                                                                                                                     
                                                                echo '</tr>';
                                                            }  
                                                        }
                                                    }
                                                }
                                            } 
                                            else {                                             
                                                foreach ($posts as $post) { 
                                                    if($data = get_post_meta($post->ID, 'forms_to_pdf', true)) {                                                                        
                                                            if ($data['extra']['submitted_on'] == $form_name) {
                                                                echo '<tr>';            
                                                                        echo '<th class="manage-column"></th>';
                                                                        echo '<td class="manage-column column-cb check-column" ><input type="checkbox" name="export_id[]" value="'.$post->ID.'" /></td>';
                                                                        echo '<td class="manage-column"><a href="#" data-id='.$post->ID.' onclick="displayModal(this);"><span data-feather="edit-3"></span></a></td>';                                                                            
                                                                        foreach ($data['data'] as $key => $field) { 

                                                                            if( !isset($_POST['visible_et_pb_contact_name_0']) || $_POST['visible_et_pb_contact_name_0']==1){
                                                                               
                                                                                $name = esc_html(html_entity_decode($field['value']));
                                                                                $name_value=trim(ucfirst(strtolower($name)));
                                                                                
                                                                                if(strlen($name_value) > $display_character){
                                                                                    echo '<td>'.substr($name_value, 0, $display_character).'...</td>';
                                                                                }else{
                                                                                    echo '<td>'.$name_value.'</td>';
                                                                                }
                                                                            }                                                                           
                                                                            if($field['value'] == 'email'){
                                                                                
                                                                                $email = esc_html(html_entity_decode($field['value']));
                                                                                $email_value=trim(strtolower($email));                                                                  
                                                                        
                                                                                if(strlen($email_value) > $display_character){
                                                                                    echo '<td><a href="mailto:'.$email_value.'" target="_blank">'.substr($email_value, 0, $display_character).'...</a></td>';
                                                                                }else{
                                                                                    echo '<td><a href="mailto:'.$email_value.'" target="_blank">'.$email_value.'</a></td>';
                                                                                }
                                                                            }                                                                                                                                               
                                                                        }                                                                      
                                                                        echo '<td data-head="post_date">'.$post->post_date.'</td>';  
                                                                                                                                     
                                                                echo '</tr>';
                                                            }  
                                                    }
                                                }
                                            } 
                                        ?>
                                    </tbody>
                                    <tfoot>
                                            <tr>
                                                <td id="cb" class="manage-column column-cb check-column">
                                                    <?php
                                                        echo '<td id="cb" class="manage-column column-cb check-column"><input type="checkbox" id="cb-select-all-1" /></td>';
                                                        echo '<th style="width: 32px;" class="manage-column"></th>';                                                            
                                                        if ($posts = get_posts('post_type=formstopdf_db&posts_per_page=-1')) {
                                                            foreach ($posts as $post) { 
                                                                if ($data = get_post_meta($post->ID, 'forms_to_pdf', true)){
                                                                    if ($data['extra']['submitted_on'] == $_REQUEST['form-name']){                                                                       
                                                                        foreach($data['data'] as $key => $field){
                                                                            echo '<th class="manage-column" ><div class="" data-key="'.$field['original_name'].'">'.__($field['original_name'],'form-pdf').'</div></th>';
                                                                        }                                                                        
                                                                        echo '<th class="manage-column" >'.__('Submit date','form-name').'</th>';
                                                                        break;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    ?>
                                                </td>
                                            </tr>
                                    </tfoot>
                                </table>                            
                                            <?php
                                echo '</div>'; // fin col table
                                echo '</div>'; // fin row table
                        echo '</form>'; // fin form post
                                // print_r($data);
                                // echo '<hr>';
                    }                    
                } else {
                        // if there is no form submitted then a picture of a cat is displayed 
                        echo '<p>' . __('This page will show a form when you have at least one submission. Until then, enjoy this picture of a cat!', 'form-pdf') . '</p>';
                        echo '<img src="http://placekitten.com/g/500/500" />';
                    }
     echo '</div>';//fin container
}

function forms_to_pdf_templates_submenu_cb(){

    global $wpdb;
    
    // ajout d'un nouveau template 
    if(isset($_POST['addDataTemplate'])){
    
        $title_pdf      = htmlspecialchars(stripslashes(sanitize_text_field($_POST['addTitlePDF'])));   
        $width_pdf      = $_POST['addWidthPDF'];
        $height_pdf     = $_POST['addHeightPDF']; 
        $size_paper     = $_POST['addSizePaper'];  
        $font_pdf       = htmlspecialchars(stripslashes(sanitize_text_field($_POST['addFontPDF']))); 
        $line_height    = $_POST['addLineHeight'];
        $size_font      = $_POST['addSizeFont'];
        $media_type     = htmlspecialchars(stripslashes(sanitize_text_field($_POST['addMediaType'])));         
        $tmpStatus      = (int)($_POST['addStatus']); 
        $img            = (int)($_POST['addImg']);

        // on affecte les autre status a false
        if($tmpStatus == 1){
            $templateChoiceFalse = false;            
            $wpdb->query($wpdb->prepare("UPDATE `".TMP_TABLE_NAME."` SET `tmp_status`=%s where 1",$templateChoiceFalse));
        }

        switch($size_paper){
            case "a4_portrait" :
                $paper_orientation = "portrait";
                $width_pdf  = 595;
                $height_pdf = 842;
                break;
            case "a4_landscape":
                $paper_orientation = "landscape";
                $width_pdf  = 842;
                $height_pdf = 595;
                break;
            case "letter" :
                $paper_orientation = "portrait";
                $width_pdf  = 612;
                $height_pdf = 792;
                break;
            case "note":
                $paper_orientation = "portrait";
                $width_pdf  = 540;
                $height_pdf = 720;
                break;
            case "legal" :
                $paper_orientation = "portrait";
                $width_pdf  = 612;
                $height_pdf = 1008;
                break;
            case "tabloid":
                $paper_orientation = "portrait";
                $width_pdf  = 792;
                $height_pdf = 1224;
                break;
            case "executive" :
                $paper_orientation = "portrait";
                $width_pdf  = 522;
                $height_pdf = 756;
                break;
            default:
                $paper_orientation = "";
        }   
        // les templates
        $sql_tmp = $wpdb->prepare("SELECT  `id_template`,`title_pdf`, `width_pdf`, `height_pdf`, `size_paper`, `tmp_font`, `size_font`, `line_height`, `paper_orientation`, `media_type`,`tmp_status`  FROM `".TMP_TABLE_NAME."` ORDER BY `title_pdf` ASC");
        $result_tmp = $wpdb->get_results($sql_tmp);


        //Condition si aucun modèle n'a été trouvé dans la bdd, par defaut est celui-ci est sélectionné.
        if(!empty($result_tmp)){
            //insertion dans la bdd d'un nouveau template
            $newtemplate = $wpdb->query($wpdb->prepare('INSERT INTO '.TMP_TABLE_NAME.'(`title_pdf`, `width_pdf`, `height_pdf`, `size_paper`, `tmp_font`, `size_font`, `line_height`, `paper_orientation`, `media_type`, `tmp_status`,`img_id`) VALUES (%s,%d,%d,%s,%s,%d,%d,%s,%s,%d,%d)', $title_pdf, $width_pdf, $height_pdf, $size_paper, $font_pdf, $size_font, $line_height, $paper_orientation, $media_type, $tmpStatus, $img));
        }else{
            // on affecte le statut a true par 1
            $newtemplate = $wpdb->query($wpdb->prepare('INSERT INTO '.TMP_TABLE_NAME.'(`title_pdf`, `width_pdf`, `height_pdf`, `size_paper`, `tmp_font`, `size_font`, `line_height`, `paper_orientation`, `media_type`, `img_id`,`tmp_status`) VALUES (%s,%d,%d,%s,%s,%d,%d,%s,%s,%d,%d)', $title_pdf, $width_pdf, $height_pdf, $size_paper, $font_pdf, $size_font, $line_height, $paper_orientation, $media_type, $img,1));
        }
        // une alert s'affiche pour ajout d'un nouveau modèle
        if($newtemplate !==false){
            ?>
             <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
             <symbol id="info-fill" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
            </symbol>
            </svg>
                
            <div class="alert alert-primary alert-dismissible fade show" role="alert">
                <svg class="bi flex-shrink-0 me-2" width="24" height="24"><use xlink:href="#info-fill"/></svg>
                    <?php echo __('Template saved successfully','form-pdf') ; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php 
        }
        
    }

    // update of a template
    if(isset($_POST['updateDataTemplate']) && !empty($_POST['updateDataTemplate'])){

        //on recupère l'id et le nom du template dans tableau
        $choice = explode('_',$_POST['updateDataTemplate']);
        //recovery only of the entire
        $id_template   =(int) $choice[0];
        $name_template = $choice[1]; 
        $title_pdf     = htmlspecialchars(stripslashes(sanitize_text_field($_POST['editTitlePDF_'.$id_template])));   
        $width_pdf     = $_POST['editWidthPDF_'.$id_template];
        $height_pdf    = $_POST['editHeightPDF_'.$id_template]; 
        $size_paper    = $_POST['editSizePaper_'.$id_template];  
        $font_pdf      = htmlspecialchars(stripslashes(sanitize_text_field($_POST['editFontPDF_'.$id_template]))); 
        $line_height   = $_POST['editLineHeight_'.$id_template];
        $size_font     = $_POST['editSizeFont_'.$id_template];
        $media_type    = htmlspecialchars(stripslashes(sanitize_text_field($_POST['editMediaType_'.$id_template]))); 
        $tmpStatus     = $_POST['editStatus_'.$id_template]; 
        // on affecte le(s) statut(s) de(s) modèle(s) par false 
        if($tmpStatus == 1){
            $wpdb->query($wpdb->prepare("UPDATE `".TMP_TABLE_NAME."` SET `tmp_status`=%d where 1",0));
        }

        switch($size_paper){
            case "a4_portrait" :
                $paper_orientation = "portrait";
                $width_pdf  = 595;
                $height_pdf = 842;
                break;
            case "a4_landscape":
                $paper_orientation = "paysage";
                $width_pdf  = 842;
                $height_pdf = 595;
                break;
            case "letter" :
                $paper_orientation = "";
                $width_pdf  = 612;
                $height_pdf = 792;
                break;
            case "note":
                $paper_orientation = "";
                $width_pdf  = 540;
                $height_pdf = 720;
                break;
            case "legal" :
                $paper_orientation = "";
                $width_pdf  = 612;
                $height_pdf = 1008;
                break;
            case "tabloid":
                $paper_orientation = "";
                $width_pdf  = 792;
                $height_pdf = 1224;
                break;
            case "executive" :
                $paper_orientation = "";
                $width_pdf  = 522;
                $height_pdf = 756;
                break;
            case "postcard" :
                $paper_orientation = "";
                $width_pdf  = 283;
                $height_pdf = 416;
                break;
            default:
                $paper_orientation = "";
        }   
    
            //màj dans la bdd
            $updateTmp = $wpdb->query($wpdb->prepare("UPDATE `".TMP_TABLE_NAME."` SET `title_pdf`=%s , `width_pdf`=%d , `height_pdf`=%d , `size_paper`=%s , `tmp_font`=%s , `size_font`=%d , `line_height`=%d , `paper_orientation`=%s , `media_type`=%s, `tmp_status`=%d where `id_template`=%d ", $title_pdf, $width_pdf, $height_pdf, $size_paper, $font_pdf, $size_font, $line_height, $paper_orientation, $media_type, $tmpStatus,$id_template));
        

            // si màj a bien été effectuer dans la bdd, dans ce cas confirmation
            if($updateTmp !==false){
                ?>
                 <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
                    <symbol id="check-circle-fill" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                    </symbol>
                </svg>                    
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <svg class="bi flex-shrink-0 me-2" width="24" height="24"><use xlink:href="#check-circle-fill"/></svg>
                        <?php echo __('Template : ','form-pdf');
                        echo $name_template; 
                        echo __(' change successfully','form-pdf'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php 
            }
    }

    // suppression d'un template dans la BDD
    if(isset($_POST['moveToTrashTemplate']) && !empty($_POST['moveToTrashTemplate'])){

        $idTrash = $_POST['moveToTrashTemplate'];

        // suppression d'un template par son ID
        $tmpTrash = $wpdb->query("DELETE FROM ".TMP_TABLE_NAME." WHERE id_template IN($idTrash)");

            // avertissement d'un ajout de nouveau template
          if($tmpTrash!==false){
                // $alertDeleteSuccess = true;
                    ?>
                <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
                    <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                    </symbol>
                </svg>                
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <svg class="bi flex-shrink-0 me-2" width="24" height="24"><use xlink:href="#exclamation-triangle-fill"/></svg>
                        <?php echo __('Template remove','form-pdf') ; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php
            }
    }

    // on recupère les données depuis la BDD
        // les templates
        $sql_tmp = $wpdb->prepare("SELECT  `id_template`,`title_pdf`, `width_pdf`, `height_pdf`, `size_paper`, `tmp_font`, `size_font`, `line_height`, `paper_orientation`, `media_type`,`tmp_status`, `img_id`  FROM `".TMP_TABLE_NAME."` ORDER BY `title_pdf` ASC");
        $result_tmp = $wpdb->get_results($sql_tmp);

        // all images
        $sql_img = $wpdb->prepare("SELECT `id_img`,`img_blob`, `img_type`,`img_title` FROM `".IMG_TABLE_NAME."`");
        $result_img = $wpdb->get_results($sql_img);


        //Condition d'affichage en fonction du resultat dans la bdd.
        if(!empty($result_tmp)){
            $find_template=true;
        }else{
            $find_template=false;
        }

        if(!empty($result_tmp)){
            $find_capture=true;
        }else{
            $find_capture=false;
        }
    
?>         
    <!-- Modifier un template -->
    <form method="post" >
            <?php foreach($result_tmp as $key => $tmp){?>
                <div class="modal fade" id="editTemplateModal<?= $tmp->id_template ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="staticBackdropLabel"><?php echo __('Edit template','form-pdf');?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                        <div class="modal-body">                    
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">
                                        <div class="input-group mb-3">
                                            <input type="file" class="form-control" name="editImgTemplate_<?= $tmp->id_template?>" id="chooseFile" placeholder="" aria-label="">
                                            <span class="input-group-text" id="basic-addon1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-arrow-up" viewBox="0 0 16 16">
                                                    <path d="M8 11a.5.5 0 0 0 .5-.5V6.707l1.146 1.147a.5.5 0 0 0 .708-.708l-2-2a.5.5 0 0 0-.708 0l-2 2a.5.5 0 1 0 .708.708L7.5 6.707V10.5a.5.5 0 0 0 .5.5z"></path>
                                                    <path d="M4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H4zm0 1h8a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1z"></path>
                                                </svg>
                                            </span>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text"><?php echo __('Title on PDF','form-pdf');?></span>
                                            <input type="text" class="form-control" name="editTitlePDF_<?= $tmp->id_template?>" id="editTitlePDF" value ="<?= $tmp->title_pdf ?>" aria-label="">
                                            <span class="input-group-text"><?php echo __('Status','form-pdf');?></span>
                                            <select class="form-select"  name="editStatus_<?= $tmp->id_template?>" aria-label=""> 
                                                <option value="0" <?php if($tmp->tmp_status==false) {print "selected";}?>  ><?php echo __('Not activated','form-pdf');?></option>  
                                                <option value="1" <?php if($tmp->tmp_status==true) {print "selected";}?> ><?php echo __('Activated','form-pdf');?></option>  
                                            </select>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="input-group mb-3">
                                                <span class="input-group-text"><?php echo __('Width','form-pdf');?></span>
                                            <input type="text" class="form-control" name="editWidthPDF_<?= $tmp->id_template?>" id="editWidthPDF" value ="<?= $tmp->width_pdf ?>" aria-label="">
                                                <span class="input-group-text">&</span>
                                            <input type="text" class="form-control" name="editHeightPDF_<?= $tmp->id_template?>" id="editHeightPDF" value ="<?= $tmp->height_pdf ?>" aria-label="">
                                                <span class="input-group-text"><?php echo __('Height','form-pdf');?></span>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text"><?php echo __('Size paper','form-pdf');?></span>
                                            <select class="form-select" id="" name="editSizePaper_<?= $tmp->id_template?>" onchange="changeWidthNHeight(this);"  aria-label=""> 
                                                <option value="" >---<?php echo __('Select size paper','form-pdf');?>---</option>                                        
                                                <option value="A4PORTRAIT" data-width='595' data-height="842" <?php if($tmp->size_paper=="a4_portrait") {print "selected";}?> >A4 (<?php echo __('PORTRAIT','form-pdf');?>) (595x842)</option>
                                                <option value="a4_landscape" data-width="842" data-height="595" <?php if($tmp->size_paper=="a4_landscape") {print "selected";}?> >A4 (<?php echo __('LANDSCAPE','form-pdf');?>) (842x595)</option>
                                                <option value="letter" data-width="612" data-height="792" <?php if($tmp->size_paper=="letter") {print "selected";}?> ><?php echo __('letter','form-pdf');?> (612x792)</option>
                                                <option value="note" data-width="540" data-height="720" <?php if($tmp->size_paper=="note") {print "selected";}?>><?php echo __('note','form-pdf');?> (540x720)</option>
                                                <option value="legal" data-width="612" data-height="1008" <?php if($tmp->size_paper=="legal") {print "selected";}?> ><?php echo __('legal','form-pdf');?> (612x1008)</option>
                                                <option value="tabloid" data-width="792" data-height="1224" <?php if($tmp->size_paper=="tabloid") {print "selected";}?> ><?php echo __('tabloid','form-pdf');?> (792x1224)</option>
                                                <option value="executive" data-width="522" data-height="756" <?php if($tmp->size_paper=="executive") {print "selected";}?> ><?php echo __('executive','form-pdf');?> (522x756)</option>
                                                <option value="postcard" data-width="283" data-height="416" <?php if($tmp->size_paper=="postcard") {print "selected";}?> ><?php echo __('postcard','form-pdf');?> (283x416)</option>
                                            </select>
                                            <span class="input-group-text"><?php echo __('Media type ','form-pdf');?></span>
                                            <select class="form-select"  name="editMediaType" aria-label=""> 
                                                <option value="" >---<?php echo __('Select media type','form-pdf');?>---</option>                                        
                                                <option value="screen" <?php if($tmp->media_type=="screen") {print "selected";}?> data-desc="<?php echo __('Intended for non-paged computer screens.','form-pdf');?>"><?php echo __('Screen','form-pdf');?></option>
                                                <option value="tty" <?php if($tmp->media_type=="tty") {print "selected";}?> data-desc="<?php echo __('Intended for media using a fixed-pitch character grid, such as teletypes, terminals, or portable devices with limited display capabilities.','form-pdf');?>" ><?php echo __('TTY','form-pdf');?></option>
                                                <option value="tv" <?php if($tmp->media_type=="tv") {print "selected";}?> data-desc="Intended for television-type devices (low resolution, color, limited scrollability)." ><?php echo __('TV','form-pdf');?></option>
                                                <option value="projection" <?php if($tmp->media_type=="projection") {print "selected";}?>  data-desc="Intended for projectors"><?php echo __('PROJECTION','form-pdf');?></option>
                                                <option value="handheld" <?php if($tmp->media_type=="handheld") {print "selected";}?>  data-desc="Intended for handheld devices (small screen, monochrome, bitmapped graphics, limited bandwidth)."><?php echo __('HANDHELD','form-pdf');?> (612x1008)</option>
                                                <option value="print" <?php if($tmp->media_type=="print") {print "selected";}?> data-desc="Intended for braille tactile feedback devices." ><?php echo __('PRINT','form-pdf');?></option>
                                                <option value="aural" <?php if($tmp->media_type=="aural") {print "selected";}?> data-desc="Intended for speech synthesizers." ><?php echo __('AURAL','form-pdf');?></option>
                                                <option value="all" <?php if($tmp->media_type=="all") {print "selected";}?> data-desc="Suitable for all devices" ><?php echo __('ALL','form-pdf');?></option>
                                            </select>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="input-group mb-3">
                                                <span class="input-group-text"><?php echo __('Font','form-pdf');?></span>
                                            <select class="form-select" id="" name="editFontPDF_<?= $tmp->id_template?>">                                          
                                                    <option value="courier"   <?php if($tmp->tmp_font=="courier"){print "selected";} ?>>Courier</option>  
                                                    <option value="helvetica" <?php if($tmp->tmp_font=="helvetica"){print "selected";} ?>>Helvetica</option>   
                                                    <option value="times"     <?php if($tmp->tmp_font=="times"){print "selected";} ?>>Times</option> 
                                                    <option value="times-roman" <?php if($tmp->tmp_font=="times-roman"){print "selected";} ?>>Times Roman</option>   
                                                    <option value="symbol"      <?php if($tmp->tmp_font=="symbol"){print "selected";} ?>>symbol</option>  
                                                    <option value="zapfdinbats" <?php if($tmp->tmp_font=="zapfdinbats"){print "selected";} ?>>Zapfdinbats</option>                                                                       
                                            </select> 
                                                <span class="input-group-text"><?php echo __('Size','form-pdf');?></span>
                                            <select class="form-select" id="validationDefault04" name="editSizeFont_<?= $tmp->id_template?>">
                                                <option selected value=""></option>
                                                <?php for($i=0;$i<=512;$i++){?>
                                                        <option value="<?=$i; ?>" <?php if($i==$tmp->size_font) {print "selected";}?> ><?php echo $i; ?></option>
                                                <?php } ?>                                            
                                            </select>
                                                <span class="input-group-text"><?php echo __('Line Height','form-pdf');?></span>
                                            <select class="form-select" id="validationDefault04" name="editLineHeight_<?= $tmp->id_template?>">
                                                <option selected value=""></option>
                                                <?php for($i=0;$i<=512;$i++){?>
                                                        <option value="<?=$i; ?>" <?php if($i==$tmp->line_height) {print "selected";}?> ><?php echo $i; ?></option>
                                                <?php } ?>                                        
                                            </select>
                                        </div>                                           
                                    </li>
                                </ul>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo __('Close','form-pdf') ?></button>
                            <button type="submit" name="updateDataTemplate" value="<?= $tmp->id_template.'_'.$tmp->title_pdf ?>" class="btn btn-primary"><?php echo __('Save Changes','form-pdf') ?></button>
                        </div>
                        </div>
                    </div>
                </div>  <!-- End Modal Edit Information   -->    
            <?php }  ?>
        </form>

        <!-- ajouter un nouveau template -->
        <form method="post" >
                <div class="modal fade" id="addTemplateModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="staticBackdropLabel"><?php echo __('Create PDF','form-pdf');?></h5>                               
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>                               
                            </div>
                        <div class="modal-body">                    
                                <ul class="list-group list-group-flush">
                                    <!-- <li class="list-group-item">
                                        <div class="input-group mb-3">
                                            <input type="file" class="form-control" name="editImgTemplate" id="chooseFile" placeholder="" aria-label="">
                                            <span class="input-group-text" id="basic-addon1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-arrow-up" viewBox="0 0 16 16">
                                                    <path d="M8 11a.5.5 0 0 0 .5-.5V6.707l1.146 1.147a.5.5 0 0 0 .708-.708l-2-2a.5.5 0 0 0-.708 0l-2 2a.5.5 0 1 0 .708.708L7.5 6.707V10.5a.5.5 0 0 0 .5.5z"></path>
                                                    <path d="M4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H4zm0 1h8a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1z"></path>
                                                </svg>
                                            </span>
                                        </div>
                                    </li> -->
                                    <li class="list-group-item">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text"><?php echo __('Title PDF','form-pdf');?></span>
                                            <input type="text" class="form-control" name="addTitlePDF" id="editTitlePDF" value ="<?php echo __('(No title)','form-pdf');?>" aria-label="" required>
                                            <span class="input-group-text"><?php echo __('Status','form-pdf');?></span>
                                            <select class="form-select"  name="addStatus" aria-label=""> 
                                                <option value="0" ><?php echo __('Not activated','form-pdf');?></option>  
                                                <option value="1" ><?php echo __('Activated','form-pdf');?></option>  
                                            </select>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="input-group mb-3">
                                                <span class="input-group-text"><?php echo __('Width','form-pdf');?></span>
                                            <input type="text" class="form-control" name="addWidthPDF" id="addWidthPDF" value ="<?php echo 595;?>" aria-label="">
                                                <span class="input-group-text">&</span>
                                            <input type="text" class="form-control" id="addHeightPDF" name="addHeightPDF" value ="<?php echo 842;?>" aria-label="">
                                                <span class="input-group-text"><?php echo __('Height','form-pdf');?></span>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text"><?php echo __('Size paper','form-pdf');?></span>
                                            <select class="form-select"  name="addSizePaper" onchange="changeWidthNHeight(this);" aria-label=""> 
                                                <option value="" >---<?php echo __('Select size paper','form-pdf');?>---</option>                                        
                                                <option value="a4_portrait" data-width='595' data-height='842'>A4 (<?php echo __('PORTRAIT','form-pdf');?>) (595x842)</option>
                                                <option value="a4_landscape" data-width="842" data-height="595" >A4 (<?php echo __('LANDSCAPE','form-pdf');?>) (842x595)</option>
                                                <option value="letter" data-width="612" data-height="792" ><?php echo __('letter','form-pdf');?> (612x792)</option>
                                                <option value="note" data-width="540" data-height="720" ><?php echo __('note','form-pdf');?> (540x720)</option>
                                                <option value="legal" data-width="612" data-height="1008" ><?php echo __('legal','form-pdf');?> (612x1008)</option>
                                                <option value="tabloid" data-width="792" data-height="1224" ><?php echo __('tabloid','form-pdf');?> (792x1224)</option>
                                                <option value="executive" data-width="522" data-height="756" ><?php echo __('executive','form-pdf');?> (522x756)</option>
                                                <option value="postcard" data-width="283" data-height="416" ><?php echo __('postcard','form-pdf');?> (283x416)</option>
                                            </select>
                                            <span class="input-group-text"><?php echo __('Media type ','form-pdf');?></span>
                                            <select class="form-select"  name="addMediaType" aria-label=""> 
                                                <option value="" >---<?php echo __('Select media type','form-pdf');?>---</option>                                        
                                                <option value="screen" data-desc="<?php echo __('Intended for non-paged computer screens.','form-pdf');?>"><?php echo __('Screen','form-pdf');?></option>
                                                <option value="tty" data-desc="<?php echo __('Intended for media using a fixed-pitch character grid, such as teletypes, terminals, or portable devices with limited display capabilities.','form-pdf');?>" ><?php echo __('TTY','form-pdf');?></option>
                                                <option value="tv" data-desc="Intended for television-type devices (low resolution, color, limited scrollability)." ><?php echo __('TV','form-pdf');?></option>
                                                <option value="projection" data-desc="Intended for projectors"><?php echo __('PROJECTION','form-pdf');?></option>
                                                <option value="handheld" data-desc="Intended for handheld devices (small screen, monochrome, bitmapped graphics, limited bandwidth)."><?php echo __('HANDHELD','form-pdf');?> (612x1008)</option>
                                                <option value="print" data-desc="Intended for braille tactile feedback devices." ><?php echo __('PRINT','form-pdf');?></option>
                                                <option value="aural" data-desc="Intended for speech synthesizers." ><?php echo __('AURAL','form-pdf');?></option>
                                                <option value="all" data-desc="Suitable for all devices" ><?php echo __('ALL','form-pdf');?></option>
                                            </select>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="input-group mb-3">
                                                <span class="input-group-text"><?php echo __('Font','form-pdf');?></span>
                                            <select class="form-select" id="" name="addFontPDF">                                          
                                                    <option value="courier">Courier</option>  
                                                    <option value="helvetica" selected>Helvetica</option>   
                                                    <option value="times">Times</option> 
                                                    <option value="times-roman">Times Roman</option>   
                                                    <option value="symbol">symbol</option>  
                                                    <option value="zapfdinbats">Zapfdinbats</option>                                                                       
                                            </select>   
                                                <span class="input-group-text"><?php echo __('Size','form-pdf');?></span>
                                            <select class="form-select" id="addSizeFont" name="addSizeFont">                                              
                                                <?php for($i=1;$i<=512;$i++){?>
                                                        <option value="<?=$i;?>" <?php if($i=="14") {print "selected";}?>><?php echo $i; ?></option>
                                                <?php } ?>                                            
                                            </select>
                                                <span class="input-group-text"><?php echo __('Line Height','form-pdf');?></span>
                                            <select class="form-select" id="addLineHeight" name="addLineHeight">                                          
                                                <?php for($i=1;$i<=512;$i++){?>
                                                        <option value="<?=$i; ?>" <?php if($i=="14") {print "selected";}?>><?php echo $i; ?></option>
                                                <?php } ?>                                        
                                            </select>
                                        </div>                                           
                                    </li>
                                    <li class="list-group-item">
                                        <div class="input-group mb-3">                                      
                                        <!-- <span style="display: inline-block;">you can choose a logo from the drop-down list at the bottom </span> -->
                                                <span class="input-group-text"><?php echo __('choose your image ','form-pdf');?></span>
                                            <select class="form-select" name="addImg" id="addImg" aria-label="">
                                                <option value="" >---<?php echo __('Select media type','form-pdf');?>---</option> 
                                                <?php  foreach($result_img as $key => $img){                                        
                                                    echo '<option value="'.$img->id_img.'" >'.$img->img_title.'</option>';
                                                }  ?>                                                                                     
                                            </select>
                                        </div>
                                    </li>
                                </ul>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo __('Close','form-pdf') ?></button>
                            <button type="submit" name="addDataTemplate" value="" class="btn btn-primary"><?php echo __('Save Changes','form-pdf') ?></button>
                        </div>
                        </div>
                    </div>
                </div>  <!-- End Modal Add Template   -->    
        </form>

        <!-- debut sous-menu "templates" -->
    <main>
            <?php if(isset($_POST['selectYourTemplate']) && isset($_POST['choiceTemplate']) && !empty($_POST['choiceTemplate'])) {

                //on recupère l'id et le nom du template dans tableau
                $choice = explode('_',$_POST['choiceTemplate']);
              
                //d'abord on affecte tout les status a false
                  $wpdb->query($wpdb->prepare("UPDATE `".TMP_TABLE_NAME."` SET `tmp_status`=%d where 1",0));

                // ensuite on affecte le template choisie à true
                $wpdb->query($wpdb->prepare("UPDATE `".TMP_TABLE_NAME."` SET `tmp_status`=%d where `id_template`=%d ",1 ,$choice[0]));
                ?>

                <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
                     <symbol id="info-fill" fill="currentColor" viewBox="0 0 16 16">
                     <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                    </symbol>
                </svg>                    
                <div class="alert alert-primary alert-dismissible fade show" role="alert">
                    <svg class="bi flex-shrink-0 me-2" width="24" height="24"><use xlink:href="#info-fill"/></svg>
                       <?php echo __('Template selected : ','form-pdf'); echo $choice[1] ?>  
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
           
           <?php } ?>
            <form method="post">
                <section class="py-5 text-center container">
                    <div class="row py-lg-5">
                        <div class="col-lg-6 col-md-8 mx-auto">
                            <h1 class="fw-light"><?php echo __('Sample templates ','form-pdf')?></h1>
                            <p class="lead text-muted"><?php echo __('Here you can add your templates. Or simply modify them as you wish.','form-pdf')?></p>
                            <p>
                                <a href="#" class="btn btn-primary my-2" onclick="launchModalAddTemplate()"><?php echo __('Add New','form-pdf')?></a>                                
                                <button type="submit" name="selectYourTemplate" value="" class="btn btn-secondary my-2"><?php echo __('Select your template','form-pdf')?></button>
                            </p>
                        </div>
                    </div>
                </section>
                <?php if($find_template){ ?>          
            
                <div class="album py-5 bg-light">
                        <div class="container">
                            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
                                <!-- afficher les données pour chaque template -->
                                <?php foreach($result_tmp as $key => $tmp){?>
                                    <div class="col">
                                        <div class="card shadow-sm text-center">
                                            <!-- afficher l'image du template 1 -->
                                            <?php  
                                               // display image when is selected in template 
                                                $sql_picture= $wpdb->prepare("SELECT `id_img`, `img_blob`, `img_type`,`img_title` FROM `".IMG_TABLE_NAME."` WHERE `id_img`=%d",$tmp->img_id);
                                                $resultPicture = $wpdb->get_results($sql_picture);

                                            if(!empty($resultPicture)){
                                                foreach($resultPicture as $key => $img){                                          
                                                    echo '<img src="data:image/'.$img->img_type.';base64,'.$img->img_blob.'" class="card-img-top" alt="'.__('Screen-shot','form-pdf').'" height="150px" width="18rem">';
                                                }
                                            }
                                            // display an example of picture 
                                             else{
                                                echo '<svg class="bd-placeholder-img card-img-top" width="100%" height="150" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: Thumbnail" preserveAspectRatio="xMidYMid slice" focusable="false"><title>Placeholder</title><rect width="100%" height="100%" fill="#55595c"/><text x="50%" y="50%" fill="#eceeef" dy=".3em"></text></svg>';
                                             }         
                                            ?>
                                            <div class="card-body">
                                                <?php echo '<h5 class="card-title">'.ucfirst($tmp->title_pdf).'</h5>';?>
                                                <ul class="list-group list-group-flush">
                                                <?php
                                                echo '<li class="list-group-item"> '.__('Size paper','form-pdf').' : '.ucfirst($tmp->size_paper).'</li>
                                                      <li class="list-group-item">'.__('Font','form-pdf').' : '.ucfirst($tmp->tmp_font).'</li>'; ?>
                                                </ul>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="btn-group">
                                                        <button type="submit" name="moveToTrashTemplate" value="<?= $tmp->id_template; ?>" class="btn btn-sm btn-outline-secondary"><?php echo __('Delete','form-pdf')?></button>
                                                        <button type="button" name="editTemplate" data-id='<?= $tmp->id_template; ?>' value="" onclick="launchModalEditTemplate(this);" class="btn btn-sm btn-outline-secondary"><?php echo __('Edit','form-pdf')?></button>
                                                    </div>
                                                    <div class="form-check">
                                                        <label class="form-check-label" for="exampleRadios1">
                                                            <?php echo __('Use','form-pdf')?>
                                                        </label>
                                                        <input class="form-check-input" type="radio" name="choiceTemplate" <?php if($tmp->tmp_status==true){echo "checked";} ?>  <?php if(isset($_POST['choiceTemplate'])&& $_POST['choiceTemplate']==$tmp->id_template.'_'.$tmp->title_pdf) {print "checked";} ?> value="<?php echo $tmp->id_template.'_'.$tmp->title_pdf; ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>                                   
                                <?php } ?>
                            </div>
                        </div>
                </div>
                <?php }else { ?>
                            <div class="card">
                                <div class="card-body">
                                    <?php echo __('No Templates Found','form-pdf'); 
                                    echo '<a href="#" class="alert-link" onclick="launchModalAddTemplate()"> '.__('Add New','form-pdf').'</a>';
                                    ?>   
                                </div>
                            </div>
                        <?php } ?>

            </form>
    </main>
    <?php 
}

function forms_to_pdf_import_submenu_cb(){
    if(isset($_POST['import'])){  
         //Define site global variables      
         global $wpdb, $df2p_img_upload_error;     

        // Set Error object for get error during import process
        $df2p_img_upload_error = new WP_Error;
        
        // File upload path
        $targetDir = plugin_dir_path( FORM_TO_PDF_FILE )."uploads/";
        $fileName = basename($_FILES["fileUpload"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath,PATHINFO_EXTENSION);
        $file_contents= file_get_contents ($_FILES['fileUpload']['tmp_name']);
        $file_hex="";

        $handle = @fopen($_FILES['fileUpload']['tmp_name'], "rb"); 
        if ($handle) {        
            $file_base64= base64_encode(fread ($handle , filesize($_FILES['fileUpload']['tmp_name']) ));  
            fclose($handle);  
        } 

        if( !empty($_FILES["fileUpload"]["name"])){
            // Allow certain file formats
            $allowTypes = array('jpg','png','jpeg','gif');
            if(in_array($fileType, $allowTypes)){
                // Upload file to server
                if(move_uploaded_file($_FILES["fileUpload"]["tmp_name"], $targetFilePath)){
                    // Insert image file name into database
                    $type = substr($fileName, strpos($fileName, ".")+1);
                    $description = isset($_POST['description']) ? $_POST['description'] : "";

                    $insert = $wpdb->query($wpdb->prepare("INSERT INTO ".IMG_TABLE_NAME." (`img_title`, `img_type`, `img_desc`, `img_blob`) VALUES (%s,%s,%s,%s)", $fileName,  $type, $description,$file_base64));

                    if($insert){
                        unlink(plugin_dir_path( FORM_TO_PDF_FILE )."uploads/".$fileName); // delete le fichier
                        // $statusMsg = "The file ".$fileName." has been uploaded successfully.";
                        echo '<script>window.alert("Le fichier '.$fileName.' a été envoyé avec succés.");</script>';
                    }else{
                        $df2p_img_upload_error->add('Send_img_fail','Sending file fails, try again in a few moments.');
                    } 
                }else{
                    $df2p_img_upload_error->add('sending error','An error occurred while sending the file');
                }
            }else{
                $df2p_img_upload_error->add('only_images','Only JPG, JPEG, PNG and PDF images can be sent');
                // echo '<script>window.alert("Only JPG, JPEG, PNG and PDF images can be sent");</script>';
            }
        }else{
            echo '<script>window.alert("Veuillez choisir un fichier à envoyer");</script>';
        }
    }
    ?>
    <!-- Navs of imports begin -->
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true"><?php echo __('Import CSV','form-pdf'); ?></button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false"><?php echo __('Import picture','form-pdf'); ?></button>
        </li>
        
       
    </ul>
    <div class="tab-content" id="myTabContent">  
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">            
            <?php      
            $display_character = (int) apply_filters('vsz_display_character_count',30);
            $url="/edit.php?post_type=formstopdf_db&page=forms_to_pdf_import";
                echo '<div class="container">';
                    if ($posts = get_posts('post_type=formstopdf_db&posts_per_page=-1')) {
                        $forms = array();
                                            
                        foreach ($posts as $post) {
                            if ($data = get_post_meta($post->ID, 'forms_to_pdf', true)) {           
                                $forms[$data['extra']['submitted_on']] = $data['extra']['submitted_on'];                          
                            }
                        }             
                        echo '<form method="" name="f2p_name" id="f2p_name" action="'.admin_url(esc_url($url)).'">';   
                            echo '<div class="row">';                 
                                echo '<div class="col-12">';
                                    echo' <div class="card">
                                        <div class="card-body">';
                                        echo '<h5 class="card-subtitle mb-2 text-muted">'. __('Select form name :', 'form-pdf') .'</h5>';
                                            echo '<select class="form-select" id="form-name" onchange="select_f2p_import_csv()" name="form-name">';
                                                echo '<option value="1">'. __('Select form name', 'form-pdf') .'</option>';
                                                $alpha_forms = array();
                                                foreach ($forms as $form) {
                                                    $alpha_forms[$form] = $form;
                                                }
                                                // form sorting
                                                ksort($alpha_forms);
                                                foreach ($alpha_forms as $form) {   
                                                    echo '<option  value="' . $form . '" ' . (isset($_REQUEST['form-name']) && $_REQUEST['form-name'] == $form ? 'selected="selected"' : '') . '>' . $form . '</option>';
                                                }
                                            echo '</select>';
                                    echo '</div></div>';
                                echo '</div>';
                            // echo '<input type="submit" name="" class="button-primary" value="'. __('View Form', 'form-pdf').'" />';
                        echo '</form>'; // fin de form  
                        echo '</div>';
                    } 

                    if(isset($_REQUEST['form-name'])&&$_REQUEST['form-name']){
                    ?>
                        <!-- table match csv -->
                    <form method="post" enctype="multipart/form-data">                                  
                        <table class="table table-success table-striped">
                                <thead>
                                    <tr class="form-field form-required">
                                        <th><?php echo __('Field name','form-test'); ?></th>
                                        <th><?php echo __('Match CSV Column','form-test'); ?></th>
                                        <th><?php echo __('Type','form-test'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                if ($posts = get_posts('post_type=formstopdf_db&posts_per_page=-1')) {  
                                    foreach ($posts as $post) { 
                                        if ($data = get_post_meta($post->ID, 'forms_to_pdf', true)){
                                            if ($data['extra']['submitted_on'] == $_REQUEST['form-name']){
                                                foreach($data['data'] as $value){
                                                    echo '<tr class="form-field form-required"><td> '.__($value['original_name'],'form-test').'</td><td><input class="" type="text" name="form_match_key['.$value['original_name'].']" value="'.$value['original_name'].'"></td><td>'.__($value['type'],"form-test").'</td></tr>';
                                                }break;
                                            
                                            }
                                        }                           
                                    }
                                }
                                ?>
                            </tbody>
                        </table> 
                                
                        <table class="table table-borderless">
                            <tbody><tr>
                                    <th><h3 class=""><?php echo __('Import CSV','form-test'); ?></h3></th>
                                    <td>	
                                    </td>	
                                </tr>
                                <tr class="form-field form-required">
                                    <th><label for="importFormList"><?php echo __('Upload CSV :','form-test'); ?></label></th>
                                    <td>
                                        <input type="file" name="importformlist" id="importformlist" onchange="checkfile(this);">
                                    </td>
                                </tr>
                                <tr class="form-field form-required">
                                    <th></th>
                                    <td>
                                        <input type="submit" id="import_csv" name="submit_csv" value="Import Data" class="button button-primary">
                                    </td>
                                </tr>
                            </tbody></table>
                    </form>
                    <?php
        
                }

                if(isset($_POST['submit_csv'])&& isset($_FILES['importformlist'])&& !empty($_FILES['importformlist'])){
                
                    // File upload path
                    $targetDir = plugin_dir_path( FORM_TO_PDF_FILE )."uploads/";
                    $fileName = sanitize_text_field($_FILES['importformlist']["name"]);
                    $targetFilePath = $targetDir . $fileName;
                    $fileType = pathinfo($targetFilePath,PATHINFO_EXTENSION);
                    $file_contents= file_get_contents ($_FILES['importformlist']['tmp_name']);
                    $file_hex="";
                    $header = NULL;
                    $delimiter = ",";
                    $data_csv_import = array();
                    $handle = @fopen($_FILES['importformlist']['tmp_name'], "r"); 
                    if ($handle) {        
                        // fgetcsv — Obtient une ligne depuis un pointeur de fichier et l'analyse pour des champs CSV
                        while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {

                            //is_null — Indique si une variable vaut NULL
                            if (is_null($header)) {
                                foreach ($row as $key => $value) {
                                    $header[] = CharacterCleaner($value);
                                }
                            } else {
                                // array_combine — Crée un tableau à partir de deux autres tableaux
                                $data_csv_import[(isset($row[0]) ? $row[0] : '')] = array_combine($header, $row);
                            }
                            
                        }   fclose($handle);
                    } 
                    
                 
                    // print_r($data_csv_import);
                    // wp_die();

                //    foreach($data_csv_import as $key => $value){
                //        if($key == "name"){

                //        }
                //    }

                //    $db_ins = array(
                //     'post_title'  => date('Y-m-d H:i:s'),
                //     'post_status' => 'publish',
                //     'post_type'   => 'formstopdf_db',
                // );
    
                // // Insert the post into the database
                // if ($post_id = wp_insert_post($db_ins)) {
                //     update_post_meta(
                //             $post_id, 
                //             'forms_to_pdf', 
                //             array(
                //                     'data'            => $data,
                //                     'extra'           => $extra,
                //                     'fields_original' => $fields_data_array,
                //                     'post'            => $_POST,

                //                 )
                //     );

                // }
        } 
            ?>
         </div>                  
        </div>
        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            <div class="container"> 
                <div class="row">   
                    <div class="col-md-12">
                        <div class="card mb-3 border-primary text-center" style="max-width: 900px;">
                            <div class="card-body">              
                                <form action="" method="post" enctype="multipart/form-data" class="mb-3" id="form_envoi" needs-validation>
                                    <h3 class="text-center mb-3"><?php echo __('Import a picture','form-pdf');?></h3>

                                    <div class="user-image mb-4 text-center">
                                        <div style="width: 150px; height: 90px; overflow: hidden; background: #cccccc; margin: 0 auto" class="border border-success rounded">
                                            <img src="<?php echo plugins_url('/img/tenor.gif', FORM_TO_PDF_FILE) ?>" class="figure-img img-fluid" id="imgPlaceholder" alt="">
                                        </div>
                                    </div>
                                    <span id="">  <?php echo __('Only pictures of type ','form-pdf');?>: jpg, png et jpeg</span>
                                    <!-- <div class="input-group mb-3">                              
                                        <input type="file" name="fileUpload" id="chooseFile" class="form-control" placeholder="" aria-label="Username" aria-describedby="basic-addon1" required>
                                    </div> -->
                                    <div class="input-group mb-3">
                                        <input type="file" class="form-control" name="fileUpload" id="chooseFile" placeholder="" aria-label="" required>
                                        <span class="input-group-text" id="basic-addon1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-arrow-up" viewBox="0 0 16 16">
                                                <path d="M8 11a.5.5 0 0 0 .5-.5V6.707l1.146 1.147a.5.5 0 0 0 .708-.708l-2-2a.5.5 0 0 0-.708 0l-2 2a.5.5 0 1 0 .708.708L7.5 6.707V10.5a.5.5 0 0 0 .5.5z"></path>
                                                <path d="M4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H4zm0 1h8a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1z"></path>
                                            </svg>
                                        </span>
                                        <input type="text" name="description" class="form-control" placeholder="Description" aria-label="Server">
                                    </div>
                                    <button type="submit" name="import" class="btn btn-primary mt-4">
                                        <?php echo __('Import','form-pdf');?>
                                    </button>
                                    <!-- echo '<script type="text/javascript">window.alert("'.$documents.'");</script>'; -->
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="card mb-3 border-success text-center" style="max-width: 900px;">
                            <div class="card-body">   

                                <?php
                                global $wpdb;
                            // Declaring $wpdb as global and using it to execute an SQL query statement that returns a PHP object
                                $res = $wpdb->get_results("SELECT `id_img`, `img_title`, `img_blob`, `img_type` FROM ".IMG_TABLE_NAME." WHERE 1");
                                
                                    ?>
                                    <form method="post">
                                        <?php  foreach($res as $key => $result){ ?>
                                            <div class="card flip-card">
                                                <div class="flip-card-inner">
                                                    <div class="flip-card-front">
                                                        <?php  echo '<img src="data:image/'.$result->img_blob.';base64,'.$result->img_blob.'" class="card-img-top" alt="Avatar">'; ?>
                                                    </div>
                                                    <div class="flip-card-back">  
                                                        <div class="d-grid gap-2"> 
                                                        <?php   echo '<span class="badge bg-info" id="">Type : '.$result->img_type.'</span>';
                                                                echo '<button type="submit" class="btn btn-success"   name="download_img" value="'.$result->id_img.'">'.__('Download ','form-pdf').'</button>';
                                                                echo '<button type="submit" class="btn btn-danger"    name="delete_img" value="'.$result->id_img.'">'.__('Delete','form-pdf').'</button>'; ?>
                                                        </div>
                                                    </div> 
                                                </div>
                                            </div>  
                                        <?php } ?>                     
                                    </form>                                               
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
    
<?php

}
   

function download_or_delete_img_f2p(){
    global $wpdb;
    if(isset($_POST['download_img']) && $_POST['download_img']){

        $id = $_POST['download_img'];

        if(!empty($id)){
                    // selectionner une image dans la BDD           
                    $sql = $wpdb->prepare("SELECT  `id_img`, `img_title`, `img_blob`, `img_type` FROM ".IMG_TABLE_NAME." WHERE `id_img`= %d",$id);
                    $resultById = $wpdb->get_results($sql);
        
                
                foreach($resultById as $key => $resultat){
                    $base64 = $resultat->img_blob;
                    $type   = $resultat->img_type;
                    $title  = $resultat->img_title;
                }
        
                $titre = str_replace(' ', '_',$title);

                $base64 = str_replace('data:image/'.$type.';base64,', '', $base64);
                $base64 = str_replace(' ', '+', $base64);
                $data = base64_decode($base64);   

                header('Content-Description: File Transfer');
                header('Content-Type: image/'.$type); 
                header('Content-Disposition: attachment; filename="'.$titre.'"');

                header('Connection: Keep-Alive');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
                header('Content-Length: ' . strlen($data));
                flush(); // Flush system output buffer
                echo $data;
                wp_die();
        }
    }

        if(isset($_POST['use_pdf']) && $_POST['use_pdf']){
            

        }

            //supprimer une image depuis la BDD
        if(isset($_POST['delete_img']) && $_POST['delete_img']){
         
            
            $sql = $wpdb->prepare("DELETE FROM ".IMG_TABLE_NAME." WHERE `id_img`= %d",$_POST['delete_img']);
            $resultById = $wpdb->get_results($sql);
            
            if($resultById !==false){
                ?>
                <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
                    <symbol id="check-circle-fill" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                    </symbol>
                </svg>
                    
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <svg class="bi flex-shrink-0 me-2" width="24" height="24"><use xlink:href="#check-circle-fill"/></svg>
                        <?php echo __('Picture delete successfully','form-pdf') ; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php 
            }
                    
        }       
    
}

// suuprimer une entrée d'un formaulaire
function delete_in_database_f2p(){   
    if (isset($_REQUEST['btnaction']) && isset($_REQUEST['export_id'])&& !empty($_REQUEST['export_id'])){   
        global $wpdb;  
        if ($posts = get_posts('post_type=formstopdf_db&posts_per_page=-1')) {  
            if(isset($_REQUEST['action_selector']) && $_REQUEST['action_selector']=="delete"){               
                foreach($_REQUEST['export_id'] as $key=> $ids){                          
                    if ($data = get_post_meta($ids, 'forms_to_pdf', true)) {  
                            if ($data['extra']['submitted_on'] == $_REQUEST['form-name']) {                                 
                                foreach ($posts as $k => $post ) {
                                    if($ids==$post->ID){                                       
                                       $wpdb->query("DELETE FROM $wpdb->posts WHERE ID IN($ids)");
                                       $wpdb->query("DELETE FROM $wpdb->postmeta WHERE post_id IN($ids)");
                                         // echo '<script>alert("delete")</script>';                   
                                    }
                                }                                
                            }
                    }
                }
            }
        }
    }    
}


function update_in_database_f2p(){
    if(isset($_POST['update_data']) && !empty($_POST['update_data'])){       
 
        if ($data = get_post_meta($_POST['update_data'], 'forms_to_pdf', true)) {  
            
            

            foreach($data['data'] as $key => $value){
                $values_copy[] = $value['value'];
            }             
            $extra = $data['extra'];
            $fields_data_array = $data['fields_original'];
            $post= $data['post'];
            $fields_table = $data['fields_table'];
        }

  

        $name1   = htmlspecialchars(stripslashes(sanitize_text_field($_POST['field_value_0_'.$_POST['update_data']])));
        $name2   = htmlspecialchars(stripslashes(sanitize_text_field($_POST['field_value_1_'.$_POST['update_data']])));
        $email   = htmlspecialchars(stripslashes(sanitize_text_field($_POST['field_value_2_'.$_POST['update_data']])));
        $message = htmlspecialchars(stripslashes(sanitize_text_field($_POST['field_value_3_'.$_POST['update_data']])));
        $updateData = [$name1,$name2,$email,$message];

      
    

        if ($data = get_post_meta($_POST['update_data'], 'forms_to_pdf', true)) {   
            $data_update = [];
            foreach($data["data"] as $key => $value){
                $data_update[] = [
                    "label" => $data["data"][$key]["label"],
                    "original_name" => $data["data"][$key]["original_name"],
                    "value" => $updateData[$key],
                    "type" => $data["data"][$key]["type"],
                ];
            }
        }
    
        // Insert the post into the database
        update_post_meta(
            $_POST['update_data'], 
                'forms_to_pdf', 
                array(
                    'data'            => $data_update,
                    'extra'           => $extra,
                    'fields_original' => $fields_data_array,
                    'post'            => $post,
                    'fields_table'    => $fields_table
                )
        );       
    } 
    

    if(isset($_POST['save_field_settings'])){

        if ($posts = get_posts('post_type=formstopdf_db&posts_per_page=-1')) {
            
            foreach($posts as $post){
                if ($data = get_post_meta($post->ID, 'forms_to_pdf', true)) {                   
                
                    if ($data['extra']['submitted_on'] == $_REQUEST['form-name']) {
                          //we get all the ids
                        $post_id_find[] = $post->ID;
                    }                
                }
            }
        }
        //elimination of duplicates
        $post_id_find_unique = array_unique($post_id_find);

        foreach($post_id_find_unique as $id_update){
            if ($data = get_post_meta($id_update, 'forms_to_pdf', true)) {
                foreach($data['data'] as $key => $value){
                    $data_copy=$value;
                }
                $extra = $data['extra'];
                $fields_data_array = $data['fields_original'];
                $post= $data['post'];
                $fields_table = $data['fields_table'];
            }    
        }

        $name_field    = isset($_POST['name'])        ? htmlspecialchars(stripslashes(sanitize_text_field($_POST['name'])))        : "";
        $name_2_field   = isset($_POST['name_2'])      ? htmlspecialchars(stripslashes(sanitize_text_field($_POST['name_2'])))      : "";
        $email_field = isset($_POST['email'])       ? htmlspecialchars(stripslashes(sanitize_text_field($_POST['email'])))       : "";
        $message_field = isset($_POST['message'])     ? htmlspecialchars(stripslashes(sanitize_text_field($_POST['message'])))     : "";
        $rename_fields_tmp =[ $name_field,$name_2_field , $email_field,$message_field ];
        $rename_fields=[];

        // an assignment only if there are no empty fields 
        foreach($rename_fields_tmp as $value){
            if($value!=""){
                $rename_fields[] = $value;
            }
        }
       
        if ($posts = get_posts('post_type=formstopdf_db&posts_per_page=-1')) {
            foreach($posts as $post){
                if ($data = get_post_meta($post->ID, 'forms_to_pdf', true)) {
                    if ($data['extra']['submitted_on'] == $_REQUEST['form-name']) {  
                        $data_update = [];
                        foreach($data["data"] as $key => $value){
                            $data_update[] = [
                                "label" => $data["data"][$key]["label"],
                                "original_name" => $rename_fields[$key],
                                "value" => $data["data"][$key]["value"],
                                "type" => $data["data"][$key]["type"],
                            ];
                        }
                    }
                } 
            }      
        }

        foreach($post_id_find_unique as $id_update){
            update_post_meta(
                $id_update, 
                    'forms_to_pdf', 
                    array(
                        'data'            => $data_update,
                        'extra'           => $extra,
                        'fields_original' => $fields_data_array,
                        'post'            => $post,
                        'fields_table'    => $fields_table
                    )
            );   
        }          
    }    
}


function forms_to_pdf_download_csv() {

    if(isset($_REQUEST['export_form']) && $_REQUEST['export_form']=="csv" && isset($_POST['download'])){
        if ($posts = get_posts('post_type=formstopdf_db&posts_per_page=-1')) {
            foreach($posts as $post){
              $id_download_csv[]= $post->ID;                
            }
        }

        if (isset($_REQUEST['form-name'])&& isset($_POST['export_id']) && !empty($_POST['export_id'])) {
            if ($rows = forms_to_pdf_get_export_rows($_POST['export_id'],$_REQUEST['form-name'])) {
                header('Content-Type: application/csv');
                header('Content-Disposition: attachment; filename=' . sanitize_title($_REQUEST['form-name']) . '.csv');
                header('Pragma: no-cache');
                echo implode("\n", $rows);
               die;
            }
        }
        else if(isset($_REQUEST['form-name']) && !empty($_POST['export_id'])){
            if ($rows = forms_to_pdf_get_export_rows($id_download_csv,$_REQUEST['form-name'])) {

                header('Content-Type: application/csv');
                header('Content-Disposition: attachment; filename=' . sanitize_title($_REQUEST['form-name']) . '.csv');
                header('Pragma: no-cache');
                echo implode("\n", $rows);
               die;
            }
        }
    }
}

function forms_to_pdf_get_export_rows($id_post,$form_name) {
    $rows = array();

    if ($posts = get_posts('post_type=formstopdf_db&posts_per_page=-1')) {
        $row = '';
        $row .= '"' . __('Date', 'form-pdf') . '","' . __('Submitted On', 'form-pdf') . '","' . __('Submitted By', 'form-pdf') . '",';

        foreach ($posts as $post) {
            if ($data = get_post_meta($post->ID, 'forms_to_pdf', true)) {
                if ($data['extra']['submitted_on'] == $form_name) {
                    foreach ($data['data'] as $field) {
                        $row .= '"' . $field['original_name'] . '",';
                    }
                    break; //looking for the first instance of this form.
                }
            }
        }

        $rows[] = rtrim($row, ',');

        if (isset($id_post)&& !empty($id_post)){
            foreach($id_post as $key=> $ids){ 
                if ($data = get_post_meta($ids, 'forms_to_pdf', true)) {
                    if ($data['extra']['submitted_on'] == $form_name) {
                        foreach ($posts as $k => $post ) {
                            if($ids==$post->ID){
                                $row = '';
                                $row .= '"' . $post->post_date . '","' . $data['extra']['submitted_on'] . '","' . $data['extra']['submitted_by'] . '",';
                                foreach ($data['data'] as $field) {
                                    $row .= '"' . addslashes($field['value']) . '",';
                                }
                                $rows[] = rtrim($row, ',');
                            }
                        }
                    }
                }
            }        
        }else {
            foreach ($posts as $post) {
                        if ($data = get_post_meta($post->ID, 'forms_to_pdf', true)) {
                            if ($data['extra']['submitted_on'] == $form_name) {
                                $row = '';
                                $row .= '"' . $post->post_date . '","' . $data['extra']['submitted_on'] . '","' . $data['extra']['submitted_by'] . '",';
                                    foreach ($data['data'] as $field) {
                                        $row .= '"' . addslashes($field['value']) . '",';
                                    }
                                $rows[] = rtrim($row, ',');
                            }
                        }
                    }
            }
    }
    return $rows;
}

//Generate pdf file here

function forms_to_pdf_download_pdf() {
    global $wpdb;      

    if(isset($_REQUEST['export_form']) && $_REQUEST['export_form']=="pdf" && isset($_POST['download']) && $_POST['download']){    
        if (isset($_REQUEST['form-name'])) {    

                // recuperation de toute template
            $sql_tmp = $wpdb->prepare("SELECT  `id_template`,`title_pdf`, `width_pdf`, `height_pdf`, `size_paper`, `tmp_font`, `size_font`, `line_height`, `paper_orientation`, `media_type`,`tmp_status`,`img_id`  FROM `".TMP_TABLE_NAME."` ORDER BY `title_pdf` ASC");
            $templates = $wpdb->get_results($sql_tmp);

            //Check that the class exists before trying to use it
            if(!class_exists('MYPDF')){
                //Include pdf class file
                require_once(dirname(FORM_TO_PDF_FILE).'/admin/pdfgenerate/dompdf/autoload.inc.php');  

                foreach($templates as $key => $template){
                    if($template->tmp_status == true){
            
                        $pdf = new Dompdf\Dompdf();
                        $options = $pdf->getOptions();
                        $pdf->getOptions()->set('defaultFont', $template->tmp_font); 
                        $options->set('isRemoteEnabled', true);                
                        $pdf->setOptions($options);
                        $pdf->setPaper($template->size_paper, $template->paper_orientation);                        
                        $titlePdf = $template->title_pdf;             
                                            
                        // display image when is selected in template 
                        $sql_logo= $wpdb->prepare("SELECT `id_img`, `img_blob`, `img_type`,`img_title` FROM `".IMG_TABLE_NAME."` WHERE `id_img`=%d",$template->img_id);
                        $resultLogo = $wpdb->get_results($sql_logo);
                    }
                }               

                $docName ="";
                $timeStamp = date('Ymdhis');
                $form_title = preg_replace('/\s+/', '_', $titlePdf);
                $docName = $form_title."-".$timeStamp;

                // pdf html content
                $content = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
                            "http://www.w3.org/TR/html4/loose.dtd">
                            <html>
                            <head>
                                <meta http-equiv="Content-Type" content="text/html;" />
                                <style>table, th, td {
                                        border: 1px solid #ddd;
                                        border-collapse: collapse;
                                        table-layout:fixed;
                                        width:100%;
                                        white-space: normal;
                                        word-wrap: break-word; 
                                    }
                                    td {padding:5px;}
                                    tr {border-color:#0d6efd;}
                                </style>
                                </head>
                            <body>';
                // add a picture if selected in template
                if(!empty($resultLogo)){
                    foreach($resultLogo as $img){                                          
                        $content.='<img src="data:image/'.$img->img_type.';base64,'.$img->img_blob.'" class="card-img-top" alt="'.__('Lgo','form-pdf').'" height="100px" width="100px">';
                    }
                }
                $content.= '<div class="container"><div style="text-align:center;font-size:18px;margin:20px;line-height:30px;">'.$form_title.'</div>';		
    
                $content.= '<table border="0" cellpadding="0" cellspacing="0" style="margin-top:0;margin-left:auto;margin-right:auto;margin-bottom:10px;width:100%;">';
                $content.='<thead><tr><th>Nom du champ</th><th>Valeur</th></tr></thead>';

                if ($posts = get_posts('post_type=formstopdf_db&posts_per_page=-1')) {
    
                    $content .= '<tr><td style="font-weight:bold;font-size:14px;color:#000;padding:5px;line-height:20px;" CELLSPACING=10>' . __('Date', 'form-pdf') . '</td></tr> <tr><td style="font-weight:bold;font-size:14px;color:#000;padding:5px;line-height:20px;" CELLSPACING=10>' . __('Submitted On', 'form-pdf') . '</td>';
    
                    foreach ($posts as $post) {
                        if ($data = get_post_meta($post->ID, 'forms_to_pdf', true)) {
                            if ($data['extra']['submitted_on'] == $_REQUEST['form-name']) {                        
                                foreach ($data['data'] as $field) {
                                    $content .= '<td style="font-weight:bold;font-size:14px;color:#000;padding:5px;line-height:20px;" CELLSPACING=10>' . $field['original_name'] . '</td>';
                                }
                                break;
                                $content .= '</tr>';                            
                            }
                        }
                    }                      
                    if (isset($_REQUEST['export_id'])&& !empty($_REQUEST['export_id'])){                      
                            foreach($_REQUEST['export_id'] as $key=> $ids){                          
                                if ($data = get_post_meta($ids, 'forms_to_pdf', true)){  
                                        if ($data['extra']['submitted_on'] == $_REQUEST['form-name']){                                 
                                            foreach ($posts as $k => $post ) {
                                                if($ids==$post->ID){                                          
                                                        $postDate=$post->post_date;
                                                        $FormatFrDate=date('d-m-Y H:i:s', strtotime($postDate));
                                                        // $FormatFrDate="test";
                                                        $content .= '<tr>';
                                                        $content .= '<td style="padding:5px;line-height:20px;" CELLSPACING=10>' .  $FormatFrDate. '</td><td style="padding:5px;line-height:20px;" CELLSPACING=10>' . $data['extra']['submitted_on'] . '</td>';
                                                            foreach ($data['data'] as $field) {
                                                                $content .= '<td style="padding:5px;line-height:20px" CELLSPACING=10>' . $field['value']. '</td>';
                                                                    }
                                                                $content .= '</tr>';                                    
                                                }
                                            }                                
                                        }
                                }
                            }
                    } else {
                            foreach ($posts as $post) {                            
                                if ($data = get_post_meta($post->ID, 'forms_to_pdf', true)) {
                                    if ($data['extra']['submitted_on'] == $_REQUEST['form-name']) {
                                        $postDate=$post->post_date;
                                        $FormatFrDate=date('d-m-Y H:i:s', strtotime($postDate));
                                        $content .= '<tr><td style="padding:5px;line-height:20px;" CELLSPACING=10>' .  $FormatFrDate. '</td><td style="padding:5px;line-height:20px;" CELLSPACING=10>' . $data['extra']['submitted_on'] . '</td>';
                                            foreach ($data['data'] as $field) {
                                                $content .= '<td style="padding:5px;line-height:20px" CELLSPACING=10>' . $field['value']. '</td>';
                                                }
                                        $content .= '</tr>';
                                    }
                                }
                            }
                        }
                }
                     $content.='</table></div></body></html>';
    
                    // *******************************************************************
                    //Close and output PDF document
    
                    $upload_dir = wp_upload_dir();
    
                    $folderPath = $upload_dir['path']."/";
    
                    $fileFullName = $docName.'.pdf';
                    $readFilePath = $folderPath.$fileFullName;
    
                    //$filePath = $folderPath;
                    $pdf->loadHtml($content);
                    $pdf->render();
                    $pdf->stream($readFilePath);
                    // $pdf->Output($folderPath . $docName . '.pdf', 'F');
                        if(file_exists($readFilePath)){
                            header('Content-Type: application/pdf');
                            header('Content-Disposition: attachment; filename="'.$docName.'.pdf"' );
                            readfile($readFilePath);
                            // delete pdf file
                            unlink($readFilePath);
                            exit;
                        }
            }
        } 
    }
        
}
   
function forms_to_pdf_admin_head() {

    // Hide link on listing page
    if ((isset($_GET['post_type']) && $_GET['post_type'] == 'formstopdf_db') || (isset($_GET['post']) && get_post_type($_GET['post']) == 'formstopdf_db') || (isset($_GET['page']) && $_GET['page'] == 'forms_to_pdf_home')) {
        echo '<style> #bulk-export, #data-filter, #display_setup { display:none; }</style>';
        echo '<style> #menu-posts-formstopdf_db > ul > li.wp-first-item > a { display:none; }</style>';
    }
}


function forms_to_pdf_pt_init() {
    $labels = array(
        'name'               => _x('Divi Form2PDF - Contact form submissions', 'post type general name', 'form-pdf'),
        'singular_name'      => _x('Divi Form2PDF', 'post type singular name', 'form-pdf'),
        'menu_name'          => _x('Divi Form2PDF', 'admin menu', 'form-pdf'),
        'name_admin_bar'     => _x('Divi Form2PDF', 'add new on admin bar', 'form-pdf'),
        'add_new'            => __('Add New', 'Divi Form2PDF', 'form-pdf'),
        'add_new_item'       => __('Add New Divi Form2PDF', 'form-pdf'),
        'new_item'           => __('New Divi Form2PDF', 'form-pdf'),
        'edit_item'          => __('Edit Divi Form2PDF', 'form-pdf'),
        'view_item'          => __('View Divi Form2PDF', 'form-pdf'),
        'search_items'       => __('Search Divi Form2PDF', 'form-pdf'),
        'parent_item_colon'  => __('Parent Divi Form2PDF:', 'form-pdf'),
        'not_found'          => __('No contact form submissions found.', 'form-pdf'),
        'not_found_in_trash' => __('No contact form submissions found in Trash.', 'form-pdf')
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __('Retrieve the data in order to download it as a PDF.', 'form-pdf'),
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => false,
        'capability_type'    => 'get',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon'          => 'dashicons-pdf',
        'supports'           => array('title')
    );

    register_post_type('formstopdf_db', $args);
}


function forms_to_pdf_et_contact_page_headers($headers, $contact_name, $contact_email) {
    global $current_user;

    for ($i = 0; $i <= apply_filters('divi_db_max_forms', 25); $i++) {
        // all inputs from the original Divi inputs
        $current_form_fields = isset($_POST['et_pb_contact_email_fields_' . $i]) ? $_POST['et_pb_contact_email_fields_' . $i] : '';
        
        if ($current_form_fields) {
            $data = array();
            $fields_data_json = str_replace('\\', '', $current_form_fields);
            $fields_data_array = json_decode($fields_data_json, true);
            $email = false;
            // browse the table to separate the values, labels and type of each field from the original Divi inputs
            foreach ($fields_data_array as $index => $value) {
                //get the values by the post method
                $values = isset($_POST[$value['field_id']]) ? $_POST[$value['field_id']] : '-';                
                $values = trim(ucfirst(strtolower($values)));
                //labels
                $label  = trim(ucfirst(strtolower($value['field_label'])));
                $original_name = $value['original_id'];
                //type
                $type   = $value['field_type']; 
                if($value['field_type']=="input"){
                    $type   = "text"; 
                }
                if($value['field_type']=="text"){
                    $type   = "textarea"; 
                }
            
                $data[] = array('label' => $label, 'original_name' =>  $original_name,'value' => $values, 'type' => $type);

                if ($value['field_type'] == 'email') {
                    $email = trim(strtolower($values));
                }
            }

            $this_page = get_post(get_the_ID());
            $this_user = false;

            if ($this_user_id = (isset($current_user->ID) ? $current_user->ID : 0)) {
                if ($this_user = get_userdata($this_user_id)) {
                    $this_user = $this_user->display_name;
                }
            }

            $extra = array(
                'submitted_on'    => $this_page->post_title,
                'submitted_on_id' => $this_page->ID,
                'submitted_by'    => $this_user,
                'submitted_by_id' => $this_user_id
            );

            $THEAD_TFOOT_FIELD = array(
                'et_pb_contact_name_0'      => 'Name',
                'et_pb_contact_email_0'     => 'Email',
                'et_pb_contact_message_0'   => 'Message',
                'post_date'                 => 'Submit date'
            );  

            $db_ins = array(
                'post_title'  => date('Y-m-d H:i:s'),
                'post_status' => 'publish',
                'post_type'   => 'formstopdf_db',
            );

            // Insert the post into the database
            if ($post_id = wp_insert_post($db_ins)) {
                update_post_meta(
                        $post_id, 
                        'forms_to_pdf', 
                        array(
                                'data'            => $data,
                                'extra'           => $extra,
                                'fields_original' => $fields_data_array,
                                'post'            => $_POST,
                                'fields_table'    =>  $THEAD_TFOOT_FIELD
                               // 'server'          => $_SERVER
                            )
                );

                if ($this_user_id) {
                    update_post_meta($post_id, 'forms_to_pdf_submitted_by', $this_user_id);
                }

                update_post_meta($post_id, 'forms_to_pdf_read', 0);
                update_post_meta($post_id, 'forms_to_pdf_email', $email);
            }

        }
    }
    return $headers;
}

function create_table_df2p_entry_add_template(){
	global $wpdb;
	$table_name = $wpdb->prefix .'df2p_templates';
	$charset_collate = $wpdb->get_charset_collate();
	if( $wpdb->get_var( "show tables like '{$table_name}'" ) != $table_name ) {
        $sql = "CREATE TABLE " . $table_name . " (
				`id_template` int(11) NOT NULL AUTO_INCREMENT,
				`img_id` int(11),
				`title_pdf` text NOT NULL,
                `width_pdf` int(11) NOT NULL,
                `height_pdf` int(11) NOT NULL,
				`size_paper` varchar(20) NOT NULL,
                `tmp_font` varchar(50) NOT NULL,
                `size_font` int(11) NOT NULL,
                `line_height` int(11) NOT NULL,
                `paper_orientation` varchar(50) NOT NULL,
                `media_type` varchar(50) NOT NULL,
				`tmp_status` tinyint(1),
				UNIQUE KEY id_template (id_template)
		)$charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

}

