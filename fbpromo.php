<?php
if (!defined("WHMCS")) die("This file cannot be accessed directly");

require_once(dirname(__FILE__).'/functions.php');

//error_reporting(E_ALL & ~E_NOTICE);ini_set('display_errors', '1');

function fbpromo_config() {
	$configarray = array(
    "name" => "Facebook Promotions",
    "description" => "This addon allows you to automatically issue promotions to your Facebook fans.",
    "version" => "1.3.0",
    "author" => "Zingiri",
    "language" => "english",
    "fields" => array(
        "promo_text" => array("FriendlyName" => "Promotion text", "Type" => "text", "Size" => "120", "Description" => "Enter HTML text here to be displayed on your ordering pages, advertising your promotions and discounts."),
        "promo_active" => array("FriendlyName" => "Promo active", "Type" => "yesno", "Description" => "Tick to activate the promotion text."),
	));
	return $configarray;
}

function fbpromo_vars() {
	$c=fbpromo_config();
	foreach ($c['fields'] as $field => $data) {
		$vars[$field]=fbpromo_get_addon($field);
	}
	return $vars;
}

function fbpromo_clientarea($vars) {
	global $_LANG;

	require(dirname(__FILE__)."/src/facebook.php");
	$app_id = fbpromo_get_addon('appid');
	$application_secret = fbpromo_get_addon('appsecret');
	$facebook = new Facebook(array(   'appId'  => $app_id,
  		'secret' => $application_secret,
	));


	// Get User ID
	$user = $facebook->getUser();

	// We may or may not have this data based on whether the user is logged in.
	//
	// If we have a $user id here, it means we know the user is logged into
	// Facebook, but we don't know if the access token is valid. An access
	// token is invalid if the user logged out of Facebook.

	if ($user) {
		try {
			// Proceed knowing you have a logged in user who's authenticated.
			$user_profile = $facebook->api('/me');
		} catch (FacebookApiException $e) {
			error_log($e);
			$user = null;
		}
	}
	require(dirname(__FILE__).'/clientarea.php');
	die();
}

function fbpromo_parsePageSignedRequest($signedRequest) {
	if ($signedRequest) {
		$encoded_sig = null;
		$payload = null;
		list($encoded_sig, $payload) = explode('.', $_REQUEST['signed_request'], 2);
		$sig = base64_decode(strtr($encoded_sig, '-_', '+/'));
		$data = json_decode(base64_decode(strtr($payload, '-_', '+/'), true));
		return $data;
	}
	return false;
}

function fbpromo_output($vars) {
	global $CONFIG;

	if (isset($_REQUEST['save-settings'])) {
		if (isset($_REQUEST['promotions'])) {
			$promotions=$_REQUEST['promotions'];
			fbpromo_update_addon('promotions',implode(',',$promotions));
		} else {
			$promotions=array();
			fbpromo_update_addon('promotions','');
		}
		if (isset($_REQUEST['description'])) {
			$description=$_REQUEST['description'];
			fbpromo_update_addon('description',$description);
		} else {
			fbpromo_update_addon('description','');
		}
		if (isset($_REQUEST['appid'])) {
			$appId=$_REQUEST['appid'];
			fbpromo_update_addon('appid',$appId);
		} else {
			fbpromo_update_addon('appid','');
		}
		if (isset($_REQUEST['appsecret'])) {
			$appSecret=$_REQUEST['appsecret'];
			fbpromo_update_addon('appsecret',$appSecret);
		} else {
			fbpromo_update_addon('appsecret','');
		}

		echo '<div class="infobox">'.$vars['_lang']['admin5'].'</div>';

	} else {
		$promotions=explode(',',fbpromo_get_addon('promotions'));
		$description=fbpromo_get_addon('description');
		$appId=fbpromo_get_addon('appid');
		$appSecret=fbpromo_get_addon('appsecret');
	}

	echo '<form method="post" action="addonmodules.php?module=fbpromo">';

	echo '<table class="form" width="100%" cellspacing="2" cellpadding="3" border="0">';
	echo '<tr><td class="fieldlabel">'.$vars['_lang']['admin1'].'</td><td class="fieldarea">'.$CONFIG['SystemURL'].'/?m=fbpromo'.'</td></tr>';
	echo '<tr><td class="fieldlabel">'.$vars['_lang']['admin8'].'</td><td class="fieldarea"><input type="text" size="32" name="appid" value="'.$appId.'" /></td></tr>';
	echo '<tr><td class="fieldlabel">'.$vars['_lang']['admin9'].'</td><td class="fieldarea"><input type="text" size="32" name="appsecret" value="'.$appSecret.'" /></td></tr>';

	$sql = mysql_query("SELECT * FROM `tblpromotions` where `expirationdate` = '0000-00-00' or `expirationdate` < '".date('Y-m-d')."' order by `code`");
	if (mysql_num_rows($sql)) {
		echo '<tr>';
		echo '<td class="fieldlabel">'.$vars['_lang']['admin4'].'</td>';
		echo '<td class="fieldarea">';
		while ($promotion = mysql_fetch_assoc($sql)) {
			$tmp=explode('-',$promotion['code']);
			$hash=$id % 97;
			$c=count($tmp);
			if ($c >= 2) {
				$hash=$tmp[$c-1];
				$id=$tmp[$c-2];
				if ($hash==$id % 97) continue;
			}
			if (in_array($promotion['id'],$promotions)) echo '<input type="checkbox" name="promotions[]" value="'.$promotion['id'].'" checked="checked" />';
			else echo '<input type="checkbox" name="promotions[]" value="'.$promotion['id'].'" />';
			echo $promotion['code'];
			echo '<br />';
		}
		echo '</td>';
		echo '</tr>';
		echo '<tr><td class="fieldlabel">'.$vars['_lang']['admin6'].'</td>';
		echo '<td class="fieldarea"><textarea cols="80" rows="10" name="description">'.$description.'</textarea></td>';
	} else {
		echo '<tr><td class="fieldlabel">'.$vars['_lang']['admin3'].'</td></tr>';
	}
	echo '</table>';
	echo '<p align="center"><input type="submit" name="save-settings" value="'.$vars['_lang']['admin2'].'" /></p>';

	echo '</form>';
	echo '<hr /><p>'.$vars['_lang']['intro'].'</p><p>'.$vars['_lang']['description'].'</p><p>'.$vars['_lang']['documentation'].'</p>';
}

function fbpromo_message($message,$type='debug') {
	global $barEcho;

	if (is_array($message)) $message=print_r($message,true);
	$output=$message;
	if (!isset($barEcho) || $barEcho) echo '<br />'.$output,'<br />';
	logactivity($output);
}
