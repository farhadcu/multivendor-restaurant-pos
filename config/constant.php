<?php
define('DATE', date('Y-m-d H:i:s'));//database date time format
define('DATE_FORMAT', date('j M, Y h:i:sA'));//showing date time format
define('PREFIX', 1000);//for all code
define('GENDER',json_encode(['','Male','Female']));//for gender check
define('FOLDER_PATH','public/storage/');//default storage folder path
define('LOGO','logo/');//employee photo folder path
define('COMMON_IMG','img/');//employee photo folder path
define('ADMIN_PHOTO','admin-photo/');//admin user photo folder path
define('COMPANY_PHOTO','company-photo/');//compnay user photo folder path
define('PRODUCT_IMAGE','product/');//compnay user photo folder path
define('PURCHASE_DOCUMENT','purchase-document/');//compnay user photo folder path
define('COMPANY_USER_PHOTO','company-user-photo/');//compnay user photo folder path
define('TRANSACTION_DOCUMENT','transaction-document/');//compnay user photo folder path
define('DEFAULT_PHOTO',json_encode(['','male-user.png','female-user.png']));//default user photo
define('ONLINE_STATUS',json_encode(['','logged_in','logged_out']));//for user logged in status
define('SWITCH_STATUS',['1'=>'checked','2'=>'']);//for switch button in table
define('STATUS',['1'=>'Active','2'=>'Deactive']);//for switch button in table

define('PAYMENT_TYPE',['1'=>'Cash','2'=>'Card','3'=>'Cheque','4'=>'Mobile Banking']);
define('ACCOUNT',['1'=>'Asset','2'=>'Liability','3'=>'Equity','4'=>'Revenue','5'=>'Expense']);
define('CUSTOMER_GROUP',['1' => 'General', '2'=> 'Distributor','3' => 'Reseller']);
define('PURCHASE_STATUS_LABEL',['1' => '<a href="javascript:void(0)" class="btn btn-sm btn-label-success btn-bold  mr-2 mb-2">Received</a>', 
'2'=> '<a href="javascript:void(0)" class="btn btn-sm btn-label-danger btn-bold  mr-2 mb-2">Partial</a>']);
define('PAYMENT_STATUS_LABEL',['1' => '<a href="javascript:void(0)" class="btn btn-sm btn-label-success btn-bold  mr-2 mb-2">Paid</a>',
 '2'=> '<a href="javascript:void(0)" class="btn btn-sm btn-label-danger btn-bold  mr-2 mb-2">Due</a>']);

define('PURCHASE_STATUS',['1' => 'Received', '2'=> 'Partial']);
define('ORDER_STATUS',['1' => 'Complete', '2'=> 'Pending', '3'=> 'Cancel']);
define('PAYMENT_STATUS',['1' => 'Paid', '2'=> 'Due']);

define('MAIL_DRIVER',(['smtp','sendmail','mail']));
define('MAIL_PORT',(['25','465','587']));
define('MAIL_ENCRYPTION',(['tls','ssl']));

//table button icons
define('EDIT_ICON','<i class="kt-nav__link-icon flaticon2-contract text-info"></i> <span class="kt-nav__link-text">Edit</span>');
define('VIEW_ICON','<i class="kt-nav__link-icon flaticon2-expand text-success"></i> <span class="kt-nav__link-text">View</span>');
define('DELETE_ICON','<i class="kt-nav__link-icon flaticon2-trash text-danger"></i> <span class="kt-nav__link-text">Delete</span>');

?>
