<?php
/*
  Plugin Name: Subscription Lists for WooCommerce
  Plugin URI: https://github.com/cloudveiltech/wc-subscription-report
  Description: This plugin generate a report of all subscriptions, with quantities, for product X, Y, Z.  It will also show the subscription totals organized by status.
  Version: 1.0.0
  Author: CloudVeil Technology
  Author URI:

  Copyright: CloudVeil Technology
  License: GPLv2 or later
  License URI: URI: https://www.gnu.org/licenses/gpl-2.0.html

  Developers:
 */

defined('ABSPATH') or exit;

// Make sure WooCommerce is active
if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))))
    exit;

add_action('woocommerce_init', 'cvsp_init', 10, 1);

function cvsp_init() {

    define('CVSP_DIR', plugin_dir_path(__FILE__));
    define('CVSP_URL', plugin_dir_url(__FILE__));
    
    add_action('admin_menu', 'menu');

    function menu() {
        add_menu_page('CloudVeil subscription', 'CloudVeil subscription', 'manage_options', 'cloudveil_subscription');
        add_submenu_page('cloudveil_subscription', 'CloudVeil Subscription Total', 'Subscription Total', 'manage_options', 'cloudveil_subscription', 'cvsp_rep');
        add_submenu_page('cloudveil_subscription', 'CloudVeil Subscription List', 'Subscription List', 'manage_options', 'cv_subscriptions', 'cvsp_list');
    }

    wp_enqueue_style('cvsp_style', CVSP_URL . '/css/cvsp_style.css');
    
    include_once( 'includes/class-subscriptions-list-table.php' );
    include_once( 'includes/class-subscriptions-report.php' );
        
    if(isset($_POST['get_data']) && $_POST['get_data'] == 'download_csv'){ 
        include_once( 'includes/class-csv-report.php' );
        $types = array('total','list');
        $type_report = isset($_POST['type_report']) && in_array($_POST['type_report'],$types) ? $_POST['type_report'] : $types[0];                    
        if($type_report == 'list'){
            $v_csv = CV_Subscriptions_List_Table::get_data();        
        }else{            
            $v_csv_obj = new CV_Subscriptions_Report();        
            $v_csv = $v_csv_obj->get_data_csv();                    
        }        
        new CV_Export_CSV($type_report, $v_csv, [],$type_report,';');
    }
    
    function cvsp_list() {

            global $listTable;
            $listTable = new CV_Subscriptions_List_Table();
            $listTable->prepare_items();

            ?>
            <div class="wrap">
                <h2>Subscription products list</h2>
                <?php echo add_csv_auload_btn('list') ?>
                <form method="post">
                    <input type="hidden" name="page" value="ttest_list_table">
                    <?php
                    $listTable->display();
                    ?>
                </form>
            </div>
            <?php        
    }

    function cvsp_rep() {

        $report = new CV_Subscriptions_Report();
        $report_data = $report->get_data();           
        ?>
        <div class="wrap">
            <h2>Subscription Totals</h2>
            <?php echo add_csv_auload_btn('total') ?>
            <table  class="cv-r">
                <thead>
                    <th class="cv-r-col-status">Subscriptions status</th>
                    <th class="cv-r-col-name">Product name</th>
                    <th class="cv-r-col-qty">QTY</th>
                </thead>
                <tbody>
                <?php
                    if($report_data == []){
                ?>
                    <td colspan="3" class="cv-r-error">No subscription found.</td>
                <?php
                    }else{
                        foreach ($report_data as $sub_status=>$sub_row){
                            ?>
                            <tr>
                                <td class="cv-r-status"><?php echo $sub_status ?></td>
                                <td colspan="2">
                                    <table class="cv-r-name">
                                        <?php
                                            foreach ($sub_row as $prod_ID=>$prod_row){
                                        ?>
                                            <tr>
                                                <td class="cv-prod"><?php echo $prod_row['product_name'] ?></td>
                                                <td class="cv-qte"><?php echo $prod_row['sum_qty'] ?></td>
                                            </tr>
                                        <?php
                                            }
                                        ?>
                                    </table>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                ?>    
                </tbody>
            </table>                
        </div>                    
        <?php        
    }
    
    function add_csv_auload_btn($type){        
        ?>
                <div style="margin: 5px 0; text-align: right;">
                    <form method="post">
                        <input type="hidden" name="type_report" value="<?php echo $type ?>">
                        <button class="button button-primary" type="submit" name="get_data" value="download_csv">Get data <b>.csv</b></button>
                    </form>                    
		</div>
        <?php
    }

}   
