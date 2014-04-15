<?php
require_once(dirname(__FILE__).'/functions.php');

function fbpromo_promoCodes($vars) {
	if (strstr($_SERVER['SCRIPT_NAME'],'cart.php') && fbpromo_get_addon('promo_active')) {
		$txt='<div style="text-align:center;background-color:#E7FFDA;border-radius:5px;padding:20px;font-size:16px;margin:20px 40px;">';
		$txt.=html_entity_decode(fbpromo_get_addon('promo_text'));
		$txt.='</div>';
		return $txt;
	}
}
add_hook('ClientAreaHeaderOutput',1,'fbpromo_promoCodes');
