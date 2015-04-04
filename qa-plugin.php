<?php
        
/*              
    Plugin Name: Event Timeline Reports
    Plugin URI: https://github.com/Towhidn/Q2A-timeline
    Plugin Update Check URI:  https://github.com/Towhidn/Q2A-timeline/raw/master/q2a-timeline/qa-plugin.php
    Plugin Description: Timeline visualizion Reports for Q2A Admin
    Plugin Version: 1
    Plugin Date: 2015-04-01
    Plugin Author: Dor Amir
    Plugin Author URI: 
    Plugin License: copy lifted                           
*/                      
                        
    if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
                    header('Location: ../../');
                    exit;   
    }               

	qa_register_plugin_module('page', 'qa-timeline-admin.php', 'qa_timeline_admin', 'timeline Options');
	qa_register_plugin_layer('qa-timeline-layer.php', 'timeline Layer');
	
/*                              
    Omit PHP closing tag to help avoid accidental output
*/
