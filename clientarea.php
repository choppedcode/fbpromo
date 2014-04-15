<!DOCTYPE html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
<body>
<?php 
if ($user) {
	echo $vars['_lang']['admin7'].' '.$user_profile['first_name'].',<br /><br />';
	$id=$user_profile['id'];
	$hash=$id % 97;
	if ($signed_request = fbpromo_parsePageSignedRequest($facebook->getSignedRequest())) {
		if ($signed_request->page->liked) {
				$i=1;
				$promotions=array();
				echo $vars['_lang']['liked'].'<br />';
				$sql = mysql_query("SELECT * FROM `tblpromotions` where `id` in (".fbpromo_get_addon('promotions').") and (`expirationdate` = '0000-00-00' or `expirationdate` < '".date('Y-m-d')."') order by `code`");
				if (mysql_num_rows($sql)) {
					while ($promotion = mysql_fetch_assoc($sql)) {
						echo '<strong>'.$promotion['code'].'-'.$id.'-'.$hash.'</strong> <sup>('.$i.')</sup>'.'<br />';
						$promotion['code'].='-'.$id.'-'.$hash;
						unset($promotion['id']);
						$promotion['uses']=0;
						$promotion['expirationdate']=date('Y-m-d',time()+60*60*24*30);
						$promotions[]=$promotion;
						$i++;
					}
				}
				echo '<br />'.fbpromo_get_addon('description').'<br />';
				foreach ($promotions as $promotion) {
					$values=$keys='';
					foreach ($promotion as $key => $value) {
						if ($values) $values.=",";
						if ($keys) $keys.=",";
						$values.="'".$value."'";
						$keys.="`".$key."`";
					}
					$sql = mysql_query("SELECT * FROM `tblpromotions` where `code`='".$promotion['code']."'");
					if (!mysql_num_rows($sql)) {
						$query="INSERT INTO `tblpromotions` (".$keys.") VALUES (".$values.")";
						$rs = mysql_query($query);
					}
				}
		} else {
			echo $vars['_lang']['notliked'];
		}
	} else {
		echo $vars['_lang']['parsed_request_error'].': '.$facebook->getSignedRequest();	
		logActivity($vars['_lang']['parsed_request_error'].': '.$_REQUEST['signed_request']);
	}
} else {
	echo $vars['_lang']['login'];
	?>
	<br />
	<br />
	<fb:login-button></fb:login-button>
	<?php } ?>
	<div id="fb-root"></div>
	<script>
      window.fbAsyncInit = function() {
        FB.init({
          appId: '<?php echo $facebook->getAppID() ?>',
          cookie: true,
          xfbml: true,
          oauth: true
        });
        FB.Event.subscribe('auth.login', function(response) {
          window.location.reload();
        });
        FB.Event.subscribe('auth.logout', function(response) {
          window.location.reload();
        });
      };
      (function() {
        var e = document.createElement('script'); e.async = true;
        e.src = document.location.protocol +
          '//connect.facebook.net/en_US/all.js';
        document.getElementById('fb-root').appendChild(e);
      }());
    </script>
</body>
</html>
