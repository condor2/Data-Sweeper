<?php
/*
* This script deletes all of the demo products/categories and other information in a default OpenCart setup.
* Tested with: OpenCart 2.3.0.x - 3.0.x.x
* Support/Questions/Bugs: support.craftycoding.com
*/

set_time_limit (0);
error_reporting(E_ALL);
ini_set("display_errors" , 1);
$ERR = array();
$MESS = array();

// Required functions.

function recursiveDelete($str, $base = false){
	if(is_file($str)){
		unlink($str);
	}
	elseif(is_dir($str)){
		$scan = glob(rtrim($str,'/').'/*');
		foreach($scan as $index=>$path){
			if(basename($path) == 'index.html') continue;
			recursiveDelete($path, true);
		}
		if($base){
			rmdir($str);
		}
	} 
}

if(isset($_POST['confirmdelete'])){

	// Getting required information for deleting.
	require_once('config.php');
	require_once(DIR_SYSTEM . 'startup.php');
	$tables_in_db = array();
	
	$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
		

		$result = $db->query('SHOW TABLES FROM `'.DB_DATABASE.'`');
		
		if(!$result){ $ERR[] = "Could not get a list of tables from the database. Please check your settings in config.php"; }
		
		if(!$ERR){
			foreach($result->rows as $table){
				$tables_in_db[] = current($table);
			}
			
			$tables = array(
				'address',
				'affiliate',
				'affiliate_transaction',
				'attribute',
				'attribute_description',
				'attribute_group',
				'attribute_group_description',
				'banner',
				'banner_image',
				'banner_image_description',
				'category',
				'category_description',
				'category_to_layout',
				'category_to_store',
				'category_path',
				'coupon',
				'coupon_history',
				'coupon_product',
				'customer',
				'customer_ip',
				'customer_reward',
				'customer_transaction',
				'download',
				'download_description',
				'manufacturer',
				'manufacturer_to_store',
				'order',
				'order_download',
				'order_history',
				'order_option',
				'order_product',
				'option',
				'option_description',
				'option_value',
				'option_value_description',
				'product',
				'product_attribute',
				'product_description',
				'product_discount',
				'product_image',
				'product_option',
				'product_option_value',
				'product_related',
				'product_reward',
				'product_special',
				'product_to_category',
				'product_to_download',
				'product_to_layout',
				'product_to_store',
				'return',
				'return_history',
				'review',
				'store',
				'url_alias',
				'seo_url',
				'voucher',
				'voucher_history',
				'voucher_theme',
				'voucher_theme_description'
			);
			
			foreach($tables as $table){
				if(in_array(DB_PREFIX . $table, $tables_in_db)){
					$db->query('TRUNCATE TABLE `' . DB_PREFIX . $table . '`');
				}
			}
			
			# Delete all the demo images
			recursiveDelete(DIR_IMAGE . 'catalog/demo');
			
			# Delete all files from the cache directory.
			recursiveDelete(DIR_IMAGE . 'cache');
			recursiveDelete(DIR_CACHE);

			$MESS[] = "Data deleted. Now you can delete this script from your root directory!";
		}
}

?>
<html>
<head>
	<title>OpenCart Data Sweeper</title>
	<style type="text/css">
		body { background-color:#eee; color:#333; }
		body, p, table { font-size:1em; font-family: Calibri, verdana, sans-serif; }
		h1 { font-size:2em; margin-top:0px; }
		#container { width:80%; max-width:1000px; margin-top:5%; margin-left:auto; margin-right:auto; background-color:#fff; padding:40px; }
		#footer { text-align:center; font-size:0.9em; padding:20px; }
		#errors { border:2px solid #ff0000; color:#ff0000; }
		#messages { border:2px solid #00ff00; color:#00ff00; }
		a { color:#666; text-decoration:underline; }
		a:hover { color:#ff0000; }
		p#submit, p#checkbox { text-align:center; }
		fieldset { border:1px solid #ddd; padding:20px; }
		fieldset legend { font-weight:bold; }
		.submit {
			background-color:#fe1a00;
			-webkit-border-top-left-radius:15px;
			-moz-border-radius-topleft:15px;
			border-top-left-radius:15px;
			-webkit-border-top-right-radius:15px;
			-moz-border-radius-topright:15px;
			border-top-right-radius:15px;
			-webkit-border-bottom-right-radius:15px;
			-moz-border-radius-bottomright:15px;
			border-bottom-right-radius:15px;
			-webkit-border-bottom-left-radius:15px;
			-moz-border-radius-bottomleft:15px;
			border-bottom-left-radius:15px;
			text-indent:0;
			display:inline-block;
			color:#ffffff;
			border:0px;
			font-family:Arial Black;
			font-weight:bold;
			font-style:normal;
			font-size:1em;
			height:65px;
			width:131px;
			text-decoration:none;
			text-align:center;
		}
		.submit:hover { background-color:#ce0100; }
		.submit:active { position:relative; top:1px; }
	</style>
</head>
<body>
<div id="container">
<h1>OpenCart Data Sweeper</h1>
<?php
if($ERR){
	echo "<div id='errors'><ul>";
	foreach($ERR as $error){
		echo '<li>' . $error . '</li>';
	}
	echo "</ul></div>";
}

if($MESS){
	echo "<div id='messages'><ul>";
	foreach($MESS as $message){
		echo '<li>' . $message . '</li>';
	}
	echo "</ul></div>";
}
?>
	<p>This script will remove all the product and category data from your OpenCart installation.</p>
	
	<p><strong>IMPORTANT!</strong>
		<ul>
			<li>This is irreversable so be sure to take backups using phpMyAdmin if you're unsure about what you are doing!</li>
			<li>Remember to delete this script after you have used it, otherwise other people might open it and delete everything from your store.</li>
		</ul>
	</p>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<fieldset>	
			<legend>Confirm delete</legend>
			<label>
				<p id="checkbox"><input type="checkbox" name="confirmdelete" value="1" />
				Check to confirm that you want to delete all the data on this OpenCart installation.</p>
			</label>
			<p id="submit"><input type="submit" class="submit" value="Delete Data" /></p>
		</fieldset>
	</form>
</div>
<div id="footer">Coded by: <a href="http://www.craftycoding.com" target="_blank">Crafty Coding</a> | Support/Bugs/Questions: <a href="http://support.craftycoding.com" target="_blank">Support Site</a></div>
</body>
</html>
