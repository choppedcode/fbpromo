<?php
function fbpromo_update_addon($setting,$value) {
	if (fbpromo_get_addon($setting)!==false) {
		$sql = "update `tbladdonmodules` set `value`='".$value."' where `module`='fbpromo' and `setting`='".$setting."'";
		$rs = mysql_query($sql);
	} else {
		$sql = "insert into `tbladdonmodules` (`value`,`module`,`setting`) values ('".$value."','fbpromo','".$setting."')";
		$rs = mysql_query($sql);
	}
}

function fbpromo_get_addon($setting) {
	$sql="SELECT `value` FROM `tbladdonmodules` WHERE `module`='fbpromo' and `setting`='".$setting."'";
	$result = full_query($sql);
	if ($data = mysql_fetch_array($result)) {
		if (count($data)>0) return $data["value"];
		else return false;
	}
	return false;
}

