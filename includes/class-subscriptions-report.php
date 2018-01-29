<?php

if (!class_exists('CV_Subscriptions_List_Table')) {
    include_once( 'class-subscriptions-list-table.php' );
}

class CV_Subscriptions_Report {
            
    function __construct() {
        
    }
    
    function get_data() {
    
        $all_data = CV_Subscriptions_List_Table::get_data();        
        
        $report_data = [];
        if(is_array($all_data) && $all_data != []){
            foreach($all_data as $data_row){
                
                if(!isset($report_data[$data_row['subscriptions_status']])){
                    $report_data[$data_row['subscriptions_status']] = [];
                }
                
                if(!isset($report_data[$data_row['subscriptions_status']][$data_row['product_id']])){
                    $report_data[$data_row['subscriptions_status']][$data_row['product_id']] = [                            
                        'product_name'    =>      $data_row['product_name'],
                        'sum_qty'    =>      $data_row['qty'],
                    ];
                }else{
                    $report_data[$data_row['subscriptions_status']][$data_row['product_id']]['sum_qty'] = $report_data[$data_row['subscriptions_status']][$data_row['product_id']]['sum_qty'] + $data_row['qty'];
                }                
            }            
        }
        
        return $report_data;
    }
    
    function get_data_csv() {
        
        $report_data_csv = []; 
        $all_data = $this->get_data(); 
        foreach ($all_data as $key=>$row){            
            foreach ($row as $row_s){            
                $report_data_csv[] = [
                    'subscriptions_status' => $key,
                    'product_name' => $row_s['product_name'],
                    'sum_qty' => $row_s['sum_qty'],
                ];
            }
        }
        
        return $report_data_csv;        
    }

}
