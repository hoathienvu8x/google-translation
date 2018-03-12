<?php
if (isset($_POST['text']) && !empty($_POST['text'])) {
	$args = array(
		'client' => 'gtx',
		'sl' => 'vi',
		'tl' => 'en',
		'hl' => 'en',
		'dt' => 't',
		'ie' => 'UTF-8',
		'oe' => 'UTF-8',
		'otf' => '1',
		'pc' => '1',
		'ssel' => '0',
		'tsel' => '0',
		'kc' => '4'
	);
	$text = trim($_POST['text']);
	$url_translate = 'https://translate.googleapis.com/translate_a/single?'.http_build_query($args).'&dt=at&dt=bd&dt=ex&dt=ld&dt=md&dt=qca&dt=rw&dt=rm&dt=ss&dt=t&q=%s';
	$url = sprintf($url_translate, urlencode($text));
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36")');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
	curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	curl_setopt($ch, CURLOPT_TIMEOUT, 20);
	$rt = curl_exec($ch);
	$js = json_decode($rt, true);
	// print_r($js);exit;
	$a = array();
	if (isset($js[0][0])) {
		$a[] = array_shift($js[0][0]);
	}
	$b = $js[1][0];
	if (isset($b[0]) && isset($b[1]) && $b[0] == 'interjection') {
		$a[] = $b[1];
	}
	echo implode(',',$a);
	exit;
}
?>
<style> a{text-decoration:none;color:#333;}ul,li {list-style:none;}</style>
<div style="min-width:300px;max-width:450px;margin:20px auto 0;background-color:#eee;padding:10px;">
	<h2 style="padding: 0 0 10px;margin: 0;font-weight: normal;">Wikipedia</h2>
	<div style="width:96%;background-color:#fff;padding:2%;border-bottom:1px solid #eee;min-height:30px;font-size: 16px;font-family: arial,sans-serif;line-height: 25px;"><div id="loading" style="text-align:center;display:none;"><img src="loading.gif" /></div><div id="response"></div></div>
	<input type="text" name="question" id="question" value="" placeholder="Question ?" style="width:100%;padding:2%;border:none;outline:none;outline-color: transparent;font-size: 16px;color: brown;font-family: arial,sans-serif;" />
</div>
<script>
var serialize = function(obj, prefix) {
	var str = [], p;
	for(p in obj) {
		if (obj.hasOwnProperty(p)) {
			var k = prefix ? prefix + "[" + p + "]" : p, v = obj[p];
			str.push((v !== null && typeof v === "object") ? serialize(v, k) : encodeURIComponent(k) + "=" + encodeURIComponent(v));
		}
	}
	return str.join("&");
};
function ajax(url, method, data, success) {
    var params = typeof data == 'string' ? data : serialize(data);
    var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
	if (method.toLowerCase() == 'get') {
		xhr.open('GET', url + (url.indexOf('?') != -1 ? '&' : '?')+params);
		params = null;
	} else {
		xhr.open('POST', url);
	}
    xhr.onreadystatechange = function() {
        if (xhr.readyState>3 && xhr.status==200) { 
			return success(xhr.responseText);
		}
    };
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send(params);
    return xhr;
}
var ip = document.getElementById('question');
if (ip) {
	ip.onkeydown = function(e) {
		var code = e.which || e.keycode
		if (code == 13) {
			var t = this.value;
			document.getElementById('loading').style.display = 'block';
			document.getElementById('response').innerHTML = '';
			ajax('<?php echo basename(__FILE__); ?>','post',{text : t}, function(json) {
				document.getElementById('loading').style.display = 'none';
				document.getElementById('response').innerHTML = json;
				/*try {
					var obj = JSON.parse(json);
					if (obj.status == 'success') {
						document.getElementById('response').innerHTML = obj.data.defined+'<hr /><div>'+obj.data.document+'</div>';
					} else {
						document.getElementById('response').innerHTML = obj.msg;
					}
				} catch(ex) {
					console.log(ex.message);
				}*/
			});
		}
	};
}
</script>