<?php
// Heading
$_['heading_title']                         = 'Export / Import';

// Text
$_['text_success']                          = 'Success: You have successfully imported your data!';
$_['text_success_settings']                 = 'Success: You have successfully updated the settings for the Export/Import tool!';
$_['text_export_type_category']             = 'Categories (including category data and filters)';
$_['text_export_type_category_old']         = 'Categories';
$_['text_export_type_product']              = 'Products (including product data, options, specials, discounts, rewards, attributes and filters)';
$_['text_export_type_product_old']          = 'Products (including product data, options, specials, discounts, rewards and attributes)';
$_['text_export_type_option']               = 'Option definitions';
$_['text_export_type_attribute']            = 'Attribute definitions';
$_['text_export_type_filter']               = 'Filter definitions';
$_['text_export_type_customer']             = 'Customers';
$_['text_yes']                              = 'Yes';
$_['text_no']                               = 'No';
$_['text_nochange']                         = 'No server data has been changed.';
$_['text_log_details']                      = 'See also \'System &gt; Error Logs\' for more details.';
$_['text_log_details_2_0_x']                = 'See also \'Tools &gt; Error Logs\' for more details.';
$_['text_log_details_2_1_x']                = 'See also \'System &gt; Tools &gt; Error Logs\' for more details.';
$_['text_log_details_3_x']                  = 'See also the <a href="%1">System &gt; Maintenance &gt; Error Logs</a> for more details.';
$_['text_loading_notifications']            = 'Getting messages';
$_['text_retry']                            = 'Retry';
$_['text_used_category_ids']                = 'Currently used category IDs are between %1 and %2.';
$_['text_used_product_ids']                 = 'Currently used product IDs are between %1 and %2.';
$_['text_welcome']                          = 'Welcome to the Export/Import Tool (V%1) for OpenCart. If you need a customized version, <a href="https://www.mhccorp.com/index.php?route=information/contact">let us know</a> and we can create one for a charge.';
$_['text_license']                          = '"Commons Clause" License Condition v1.0';
$_['text_license']                         .= '<br /><br />';
$_['text_license']                         .= 'The Software is provided to you by the Licensor under the License, as defined below, subject to the following condition.';
$_['text_license']                         .= '<br /><br />';
$_['text_license']                         .= 'Without limiting other conditions in the License, the grant of rights under the License will not include, and the License does not grant to you, the right to Sell the Software.';
$_['text_license']                         .= '<br /><br />';
$_['text_license']                         .= 'For purposes of the foregoing, "Sell" means practicing any or all of the rights granted to you under the License to provide to third parties, for a fee or other consideration (including without limitation fees for hosting or consulting/ support services related to the Software), a product or service whose value derives, entirely or substantially, from the functionality of the Software. Any license notice or attribution required by the License must also include this Commons Clause License Condition notice.';
$_['text_license']                         .= '<br /><br />';
$_['text_license']                         .= 'Software: Export/Import Tool for OpenCart';
$_['text_license']                         .= '<br /><br />';
$_['text_license']                         .= 'License: <a href="https://www.gnu.org/licenses/gpl-3.0.en.html">GPLv3</a>';
$_['text_license']                         .= '<br /><br />';
$_['text_license']                         .= 'Licensor: <a href="https://www.mhccorp.com">MHCCORP.COM</a>';

// Entry
$_['entry_import']                          = 'Import from a XLS, XLSX or ODS spreadsheet file';
$_['entry_export']                          = 'Export requested data to a XLSX spreadsheet file.';
$_['entry_export_type']                     = 'Select what data you want to export:';
$_['entry_range_type']                      = 'Please select the data range you want to export:';
$_['entry_category_filter']                 = 'Limit products export by category:';
$_['entry_category']                        = 'Categories';
$_['entry_start_id']                        = 'Start id:';
$_['entry_start_index']                     = 'Counts per batch:';
$_['entry_end_id']                          = 'End id:';
$_['entry_end_index']                       = 'The batch number:';
$_['entry_incremental']                     = 'Use incremental Import';
$_['entry_upload']                          = 'File to be uploaded';
$_['entry_settings_use_option_id']          = 'Use <b>option_id</b> instead of <b>option</b> name in worksheets \'ProductOptions\' and \'ProductOptionValues\'';
$_['entry_settings_use_option_value_id']    = 'Use <b>option_value_id</b> instead of <b>option_value</b> name in worksheet \'ProductOptionValues\'';
$_['entry_settings_use_attribute_group_id'] = 'Use <b>attribute_group_id</b> instead of <b>attribute_group</b> name in worksheet \'ProductAttributes\'';
$_['entry_settings_use_attribute_id']       = 'Use <b>attribute_id</b> instead of <b>attribute</b> name in worksheet \'ProductAttributes\'';
$_['entry_settings_use_filter_group_id']    = 'Use <b>filter_group_id</b> instead of <b>filter_group</b> name in worksheets \'ProductFilters\' and \'CategoryFilters\'';
$_['entry_settings_use_filter_id']          = 'Use <b>filter_id</b> instead of <b>filter</b> name in worksheets \'ProductFilters\' and \'CategoryFilters\'';
$_['entry_version']                         = 'Version of Export/Import Tool';
$_['entry_oc_version']                      = 'Version of OpenCart';
$_['entry_license']                         = 'License';

// Error
$_['error_permission']                      = 'Warning: You do not have permission to modify Export/Import!';
$_['error_upload']                          = 'Uploaded spreadsheet file has validation errors!';
$_['error_worksheets']                      = 'Export/Import: Invalid worksheet names';
$_['error_categories_header']               = 'Export/Import: Invalid header in the Categories worksheet';
$_['error_category_filters_header']         = 'Export/Import: Invalid header in the CategoryFilters worksheet';
$_['error_category_seo_keywords_header']    = 'Export/Import: Invalid header in the CategorySEOKeywords worksheet';
$_['error_products_header']                 = 'Export/Import: Invalid header in the Products worksheet';
$_['error_additional_images_header']        = 'Export/Import: Invalid header in the AdditionalImages worksheet';
$_['error_specials_header']                 = 'Export/Import: Invalid header in the Specials worksheet';
$_['error_discounts_header']                = 'Export/Import: Invalid header in the Discounts worksheet';
$_['error_rewards_header']                  = 'Export/Import: Invalid header in the Rewards worksheet';
$_['error_product_options_header']          = 'Export/Import: Invalid header in the ProductOptions worksheet';
$_['error_product_option_values_header']    = 'Export/Import: Invalid header in the ProductOptionValues worksheet';
$_['error_product_attributes_header']       = 'Export/Import: Invalid header in the ProductAttributes worksheet';
$_['error_product_filters_header']          = 'Export/Import: Invalid header in the ProductFilters worksheet';
$_['error_product_seo_keywords_header']     = 'Export/Import: Invalid header in the ProductSEOKeywords worksheet';
$_['error_options_header']                  = 'Export/Import: Invalid header in the Options worksheet';
$_['error_option_values_header']            = 'Export/Import: Invalid header in the OptionValues worksheet';
$_['error_attribute_groups_header']         = 'Export/Import: Invalid header in the AttributeGroups worksheet';
$_['error_attributes_header']               = 'Export/Import: Invalid header in the Attributes worksheet';
$_['error_filter_groups_header']            = 'Export/Import: Invalid header in the FilterGroups worksheet';
$_['error_filters_header']                  = 'Export/Import: Invalid header in the Filters worksheet';
$_['error_customers_header']                = 'Export/Import: Invalid header in the Customers worksheet';
$_['error_addresses_header']                = 'Export/Import: Invalid header in the Addresses worksheet';
$_['error_product_options']                 = 'Export/Import: Missing Products worksheet, or Products worksheet not listed before ProductOptions';
$_['error_product_option_values']           = 'Export/Import: Missing Products worksheet, or Products worksheet not listed before ProductOptionValues';
$_['error_product_option_values_2']         = 'Export/Import: Missing ProductOptions worksheet, or ProductOptions worksheet not listed before ProductOptionValues';
$_['error_product_option_values_3']         = 'Export/Import: ProductOptionValues worksheet also expected after a ProductOptions worksheet';
$_['error_additional_images']               = 'Export/Import: Missing Products worksheet, or Products worksheet not listed before AdditionalImages';
$_['error_specials']                        = 'Export/Import: Missing Products worksheet, or Products worksheet not listed before Specials';
$_['error_discounts']                       = 'Export/Import: Missing Products worksheet, or Products worksheet not listed before Discounts';
$_['error_rewards']                         = 'Export/Import: Missing Products worksheet, or Products worksheet not listed before Rewards';
$_['error_product_attributes']              = 'Export/Import: Missing Products worksheet, or Products worksheet not listed before ProductAttributes';
$_['error_attributes']                      = 'Export/Import: Missing AttributeGroups worksheet, or AttributeGroups worksheet not listed before Attributes';
$_['error_attributes_2']                    = 'Export/Import: Attributes worksheet also expected after an AttributeGroups worksheet';
$_['error_category_filters']                = 'Export/Import: Missing Categories worksheet, or Categories worksheet not listed before CategoryFilters';
$_['error_category_seo_keywords']           = 'Export/Import: Missing Categories worksheet, or Categories worksheet not listed before CategorySEOKeywords';
$_['error_product_filters']                 = 'Export/Import: Missing Products worksheet, or Products worksheet not listed before ProductFilters';
$_['error_product_seo_keywords']            = 'Export/Import: Missing Products worksheet, or Products worksheet not listed before ProductSEOKeywords';
$_['error_filters']                         = 'Export/Import: Missing FilterGroups worksheet, or FilterGroups worksheet not listed before Filters';
$_['error_filters_2']                       = 'Export/Import: Filters worksheet also expected after a FilterGroups worksheet';
$_['error_option_values']                   = 'Export/Import: Missing Options worksheet, or Options worksheet not listed before OptionValues';
$_['error_option_values_2']                 = 'Export/Import: OptionValues worksheet also expected after an Options worksheet';
$_['error_post_max_size']                   = 'File size is greater than %1 (see PHP setting \'post_max_size\')';
$_['error_upload_max_filesize']             = 'File size is greater than %1 (see PHP setting \'upload_max_filesize\')';
$_['error_select_file']                     = 'Please select a file before clicking \'Import\'';
$_['error_id_no_data']                      = 'No data between start-id and end-id.';
$_['error_page_no_data']                    = 'No more data.';
$_['error_param_not_number']                = 'Values for data range must be whole numbers.';
$_['error_upload_name']                     = 'Missing file name for upload';
$_['error_upload_ext']                      = 'Uploaded file has not one of the \'.xls\', \'.xlsx\' or \'.ods\' file name extensions, it might not be a spreadsheet file!';
$_['error_notifications']                   = 'Could not load messages from MHCCORP.COM.';
$_['error_no_news']                         = 'No messages';
$_['error_batch_number']                    = 'Batch number must be greater than 0';
$_['error_min_item_id']                     = 'Start id must be greater than 0';
$_['error_option_name']                     = 'Option \'%1\' is defined multiple times!<br />';
$_['error_option_name']                    .= 'In the Settings-tab please activate the following:<br />';
$_['error_option_name']                    .= "Use <b>option_id</b> instead of <b>option</b> name in worksheets 'ProductOptions' and 'ProductOptionValues'";
$_['error_option_value_name']               = 'Option value \'%1\' is defined multiple times within its option!<br />';
$_['error_option_value_name']              .= 'In the Settings-tab please activate the following:<br />';
$_['error_option_value_name']              .= "Use <b>option_value_id</b> instead of <b>option_value</b> name in worksheet 'ProductOptionValues'";
$_['error_attribute_group_name']            = 'AttributeGroup \'%1\' is defined multiple times!<br />';
$_['error_attribute_group_name']           .= 'In the Settings-tab please activate the following:<br />';
$_['error_attribute_group_name']           .= "Use <b>attribute_group_id</b> instead of <b>attribute_group</b> name in worksheets 'ProductAttributes'";
$_['error_attribute_name']                  = 'Attribute \'%1\' is defined multiple times within its attribute group!<br />';
$_['error_attribute_name']                 .= 'In the Settings-tab please activate the following:<br />';
$_['error_attribute_name']                 .= "Use <b>attribute_id</b> instead of <b>attribute</b> name in worksheet 'ProductAttributes'";
$_['error_filter_group_name']               = 'FilterGroup \'%1\' is defined multiple times!<br />';
$_['error_filter_group_name']              .= 'In the Settings-tab please activate the following:<br />';
$_['error_filter_group_name']              .= "Use <b>filter_group_id</b> instead of <b>filter_group</b> name in worksheets 'ProductFilters'";
$_['error_filter_name']                     = 'Filter \'%1\' is defined multiple times within its filter group!<br />';
$_['error_filter_name']                    .= 'In the Settings-tab please activate the following:<br />';
$_['error_filter_name']                    .= "Use <b>filter_id</b> instead of <b>filter</b> name in worksheet 'ProductFilters'";
$_['error_incremental']                     = "Missing 'incremental' (Yes or No) selection for Import";

$_['error_missing_customer_group']                      = 'Export/Import: Missing customer_groups in worksheet \'%1\'!';
$_['error_invalid_customer_group']                      = 'Export/Import: Undefined customer_group \'%2\' used in worksheet \'%1\'!';
$_['error_missing_product_id']                          = 'Export/Import: Missing product_ids in worksheet \'%1\'!';
$_['error_missing_option_id']                           = 'Export/Import: Missing option_ids in worksheet \'%1\'!';
$_['error_invalid_option_id']                           = 'Export/Import: Undefined option_id \'%2\' used in worksheet \'%1\'!';
$_['error_missing_option_name']                         = 'Export/Import: Missing option_names in worksheet \'%1\'!';
$_['error_invalid_product_id_option_id']                = 'Export/Import: Option_id \'%3\' not specified for product_id \'%2\' in worksheet \'%4\', but it is used in worksheet \'%1\'!';
$_['error_missing_option_value_id']                     = 'Export/Import: Missing option_value_ids in worksheet \'%1\'!';
$_['error_invalid_option_id_option_value_id']           = 'Export/Import: Undefined option_value_id \'%3\' for option_id \'%2\' used in worksheet \'%1\'!';
$_['error_missing_option_value_name']                   = 'Export/Import: Missing option_value_names in worksheet \'%1\'!';
$_['error_invalid_option_id_option_value_name']         = 'Export/Import: Undefined option_value_name \'%3\' for option_id \'%2\' used in worksheet \'%1\'!'; 
$_['error_invalid_option_name']                         = 'Export/Import: Undefined option_name \'%2\' used in worksheet \'%1\'!';
$_['error_invalid_product_id_option_name']              = 'Export/Import: Option_name \'%3\' not specified for product_id \'%2\' in worksheet \'%4\', but it is used in worksheet \'%1\'!';
$_['error_invalid_option_name_option_value_id']         = 'Export/Import: Undefined option_value_id \'%3\' for option_name \'%2\' used in worksheet \'%1\'!';
$_['error_invalid_option_name_option_value_name']       = 'Export/Import: Undefined option_value_name \'%3\' for option_name \'%2\' used in worksheet \'%1\'!';
$_['error_missing_attribute_group_id']                  = 'Export/Import: Missing attribute_group_ids in worksheet \'%1\'!';
$_['error_invalid_attribute_group_id']                  = 'Export/Import: Undefined attribute_group_id \'%2\' used in worksheet \'%1\'!';
$_['error_missing_attribute_group_name']                = 'Export/Import: Missing attribute_group_names in worksheet \'%1\'!';
$_['error_missing_attribute_id']                        = 'Export/Import: Missing attribute_ids in worksheet \'%1\'!';
$_['error_invalid_attribute_group_id_attribute_id']     = 'Export/Import: Undefined attribute_id \'%3\' for attribute_group_id \'%2\' used in worksheet \'%1\'!';
$_['error_missing_attribute_name']                      = 'Export/Import: Missing attribute_names in worksheet \'%1\'!';
$_['error_invalid_attribute_group_id_attribute_name']   = 'Export/Import: Undefined attribute_name \'%3\' for option_id \'%2\' used in worksheet \'%1\'!'; 
$_['error_invalid_attribute_group_name']                = 'Export/Import: Undefined attribute_group_name \'%2\' used in worksheet \'%1\'!';
$_['error_invalid_attribute_group_name_attribute_id']   = 'Export/Import: Undefined attribute_id \'%3\' for attribute_group_name \'%2\' used in worksheet \'%1\'!';
$_['error_invalid_attribute_group_name_attribute_name'] = 'Export/Import: Undefined attribute_name \'%3\' for attribute_group_name \'%2\' used in worksheet \'%1\'!';
$_['error_missing_filter_group_id']                     = 'Export/Import: Missing filter_group_ids in worksheet \'%1\'!';
$_['error_invalid_filter_group_id']                     = 'Export/Import: Undefined filter_group_id \'%2\' used in worksheet \'%1\'!';
$_['error_missing_filter_group_name']                   = 'Export/Import: Missing filter_group_names in worksheet \'%1\'!';
$_['error_missing_filter_id']                           = 'Export/Import: Missing filter_ids in worksheet \'%1\'!';
$_['error_invalid_filter_group_id_filter_id']           = 'Export/Import: Undefined filter_id \'%3\' for filter_group_id \'%2\' used in worksheet \'%1\'!';
$_['error_missing_filter_name']                         = 'Export/Import: Missing filter_names in worksheet \'%1\'!';
$_['error_invalid_filter_group_id_filter_name']         = 'Export/Import: Undefined filter_name \'%3\' for option_id \'%2\' used in worksheet \'%1\'!'; 
$_['error_invalid_filter_group_name']                   = 'Export/Import: Undefined filter_group_name \'%2\' used in worksheet \'%1\'!';
$_['error_invalid_filter_group_name_filter_id']         = 'Export/Import: Undefined filter_id \'%3\' for filter_group_name \'%2\' used in worksheet \'%1\'!';
$_['error_invalid_filter_group_name_filter_name']       = 'Export/Import: Undefined filter_name \'%3\' for filter_group_name \'%2\' used in worksheet \'%1\'!';
$_['error_invalid_product_id']                          = 'Export/Import: Invalid product_id \'%2\' used in worksheet \'%1\'!';
$_['error_duplicate_product_id']                        = 'Export/Import: Duplicate product_id \'%2\' used in worksheet \'%1\'!';
$_['error_unlisted_product_id']                         = 'Export/Import: Worksheet \'%1\' cannot use product_id \'%2\' because it is not listed in worksheet \'Products\'!';
$_['error_wrong_order_product_id']                      = 'Export/Import: Worksheet \'%1\' uses product_id \'%2\' in the wrong order. Ascending order expected!';
$_['error_filter_not_supported']                        = 'Export/Import: Filters are not supported in your OpenCart version!';
$_['error_seo_keywords_not_supported']                  = 'Export/Import: Worksheet \'%1\' is not supported in your OpenCart version!';
$_['error_missing_category_id']                         = 'Export/Import: Missing category_ids in worksheet \'%1\'!';
$_['error_invalid_category_id']                         = 'Export/Import: Invalid category_id \'%2\' used in worksheet \'%1\'!';
$_['error_duplicate_category_id']                       = 'Export/Import: Duplicate category_id \'%2\' used in worksheet \'%1\'!';
$_['error_wrong_order_category_id']                     = 'Export/Import: Worksheet \'%1\' uses category_id \'%2\' in the wrong order. Ascending order expected!';
$_['error_unlisted_category_id']                        = 'Export/Import: Worksheet \'%1\' cannot use category_id \'%2\' because it is not listed in worksheet \'Categories\'!';
$_['error_addresses']                                   = 'Export/Import: Missing Cutomers worksheet, or Customers worksheet not listed before Addresses!';
$_['error_addresses_2']                                 = 'Export/Import: Addresses worksheet also expected after Customers worksheet';
$_['error_invalid_store_id']                            = 'Export/Import: Invalid store_id=\'%1\' used in worksheet \'%2\'!';
$_['error_missing_customer_id']                         = 'Export/Import: Missing customer_ids in worksheet \'%1\'!';
$_['error_invalid_customer_id']                         = 'Export/Import: Invalid customer_id \'%2\' used in worksheet \'%1\'!';
$_['error_duplicate_customer_id']                       = 'Export/Import: Duplicate customer_id \'%2\' used in worksheet \'%1\'!';
$_['error_wrong_order_customer_id']                     = 'Export/Import: Worksheet \'%1\' uses customer_id \'%2\' in the wrong order. Ascending order expected!';
$_['error_unlisted_customer_id']                        = 'Export/Import: Worksheet \'%1\' cannot use customer_id \'%2\' because it is not listed in worksheet \'Customers\'!';
$_['error_missing_country_col']                         = 'Export/Import: Worksheet \'%1\' has no \'country\' column heading!';
$_['error_missing_zone_col']                            = 'Export/Import: Worksheet \'%1\' has no \'zone\' column heading!';
$_['error_undefined_country']                           = 'Export/Import: Undefined country \'%1\' used in worksheet \'%2\'!';
$_['error_undefined_zone']                              = 'Export/Import: Undefined zone \'%2\' for country \'%1\' used in worksheet \'%3\'!';
$_['error_incremental_only']                            = 'Export/Import: Worksheet \'%1\' can only be imported in incremental mode for the time being!';
$_['error_multiple_category_id_store_id']               = 'Export/Import: Duplicate category_id/store_id \'%1\'/\'%2\' listed in worksheet \'CategorySEOKeywords\'!';
$_['error_multiple_product_id_store_id']                = 'Export/Import: Duplicate product_id/store_id \'%1\'/\'%2\' listed in worksheet \'ProductSEOKeywords\'!';
$_['error_unique_keyword']                              = 'Export/Import: Keyword \'%1\' used more than once for store_id \'%2\' in worksheet \'%3\'!';
$_['error_php_version']                                 = 'Export/Import: PHP Version 7.2 or higher required!';

// Tabs
$_['tab_import']                            = 'Import';
$_['tab_export']                            = 'Export';
$_['tab_settings']                          = 'Settings';
$_['tab_support']                           = 'Version Info';

// Button labels
$_['button_import']                         = 'Import';
$_['button_export']                         = 'Export';
$_['button_settings']                       = 'Update Settings';
$_['button_export_id']                      = 'By id range';
$_['button_export_page']                    = 'By batches';

// Help
$_['help_range_type']                       = '(Optional, leave empty if not needed)';
$_['help_category_filter']                  = '(Optional, leave empty if not needed)';
$_['help_incremental_yes']                  = '(Update and/or add data)';
$_['help_incremental_no']                   = '(Delete all data of the kind specified by worksheet on server before Import)';
$_['help_import']                           = 'Spreadsheet can have categories, products, attribute definitions, option definitions, or filter definitions. ';
$_['help_import_old']                       = 'Spreadsheet can have categories, products, attribute definitions, or option definitions. ';
$_['help_format']                           = 'Do an Export first to see the exact format of the worksheets!';
?>