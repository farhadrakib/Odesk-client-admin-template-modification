<?php
/*
 * HeatMapTracker
 * (c) 2013. HeatMapTracker
 * http://HeatMapTracker.com
 */

if ( ! defined( 'HMT_STARTED' ) || ! isset( $this->PLUGIN_PATH ) ) {
	die( 'Can`t be called directly' );
}
//detect user info
$broosArr = HMTrackerFN::browser_detection( "full" );
$broos    = HMTrackerFN::browser_detection( "os" ) . " " . HMTrackerFN::browser_detection( "os_number" );

if ( HMTrackerFN::browser_detection( "browser" ) != "ie" ) {
	$broarr = HMTrackerFN::browser_detection( HMTrackerFN::browser_detection( "browser" ) . "_version" );
	$broos .= "; " . $broarr[0] . " " . $broarr[1];
} else {
	$broos .= "; ie " . HMTrackerFN::browser_detection( "number" );
}

//get user real IP
$uip = HMTrackerFN::getRealIp();

$reguser = "guest";

//send valid file type
header( "Content-type: application/javascript" );

//secure check $_GET variables
$_GET['hmtrackerjs'] = str_replace( "~", "%20", $_GET["hmtrackerjs"] );
$_GET                = array_map( array( 'HMTrackerFN', 'hmtracker_secure' ), $_GET );

//fetch user
$user = get_user_by( 'user_key', HMTrackerFN::hmtracker_secure( $_GET['uid'] ) );

//check user
if ( empty( $user ) ) {
	die( '//cant detect user' );
}
$status_code = detect_user_status( $user );
if ( ! validate_user_status( $status_code ) ) {
	die( '//' . user_status_name( $status_code ) );
}
//fetch project settings and check if we can track domain
$general_opts       = array( $this->PROJECTS_NAME . $user->user_key, $this->USER_DOMAINS_NAME . $user->user_key );
$opts               = get_options( $general_opts );
$this->PROJECTS     = $opts[ $this->PROJECTS_NAME . $user->user_key ];
$this->USER_DOMAINS = $opts[ $this->USER_DOMAINS_NAME . $user->user_key ];
$domains            = &$this->USER_DOMAINS['opt_tracking_domains'];

$domain = parse_url( str_replace( "~", ".", $_GET['purl'] ) );
$domain = $domain['host'];
if ( ! in_array( $domain, $domains ) && $status_code != 6 && $status_code != 8 ) {
	//insert a free slot
	if ( ! empty( $this->USER_DOMAINS['opt_tracking_autofill'] ) &&
	     $this->USER_DOMAINS['opt_max_tracking_domains'] > count( $domains )
	) {
		$domains[] = $domain;
		update_option( $this->USER_DOMAINS_NAME . $user->user_key, $this->USER_DOMAINS );
	} else {
		//think how to report overflow max count issue
		die( '//slot overflow' );
	}
}

$option = $this->PROJECTS[ $_GET['hmtrackerjs'] ]['settings'];
if ( ! $option ) {
	$option = $this->PROJECTS[ rawurlencode( $_GET['hmtrackerjs'] ) ]['settings'];
}
//check page we want to record
// print_r($_GET);
// echo "<hr />";
// echo rawurlencode($_GET['hmtrackerjs']);
// echo "<hr />";
// var_dump($option);
if ( ! $option['opt_record_status'] ) {
	die( '//recording disabled' );
}
if ( in_array( $uip, $option['opt_black_ips'] ) ) {
	die( '//IP is blocked' );
}
if ( ( $option["opt_record_all"] == "false" && ! ( in_array( $_SERVER['HTTP_REFERER'], $option['opt_record_special'] ) ) ) ) {
	die( '//Referrer mismatch' );
}
?>
/*
<script>*/
hmtracker = "initialised";
window.onerror = function () {
	return true;
}

var JSONP = function (global) {
	function JSONP(uri, callback) {
		function JSONPResponse() {
			try {
				delete global[src]
			} catch (e) {
				// kinda forgot < IE9 existed
				// thanks @jdalton for the catch
				global[src] = null
			}
			documentElement.removeChild(script);
			callback.apply(this, arguments);
		}

		var
			src = prefix + id++,
			script = document.createElement("script")
			;
		global[src] = JSONPResponse;
		documentElement.insertBefore(
			script,
			documentElement.lastChild
		).src = uri + "=" + src;
	}

	var
		id = 0,
		prefix = "__JSONP__",
		document = global.document,
		documentElement = document.documentElement
		;
	return JSONP;
}(this);

function getUrlVars() {
	var vars = {};
	var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function (m, key, value) {
		vars[key] = value;
	});
	return vars;
}

var hmtracker_cookie_name = "hmtracker";

function setHMTrackerData(e, t, n) {
	localStorage.setItem(e, t);
}

function getHMTrackerData(e) {
	return localStorage[e];
}

function getByteSize(s) {
	return encodeURIComponent('<q></q>' + s).length;
}

JSONstringify = function (obj) {
	var t = typeof (obj);
	if (t != "object" || obj === null) {
		if (t == "string") obj = '"' + obj + '"';
		return String(obj);
	}
	else {
		var n, v, json = [], arr = (obj && obj.constructor == Array);
		for (n in obj) {
			v = obj[n];
			t = typeof(v);
			if (t == "string") v = '"' + v + '"';
			else if (t == "object" && v !== null) v = JSONstringify(v);
			json.push((arr ? "" : '"' + n + '":') + String(v));
		}
		return (arr ? "[" : "{") + String(json) + (arr ? "]" : "}");
	}
};

JSONparse = function (str) {
	if (str === "") str = '""';
	eval("var p=" + str + ";");
	return p;
};

var END_OF_INPUT = -1;
var base64Chars = new Array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '+', '/');
var reverseBase64Chars = new Array();
for (var i = 0; i < base64Chars.length; i++) {
	reverseBase64Chars[base64Chars[i]] = i;
}
var base64Str;
var base64Count;
function setBase64Str(str) {
	base64Str = str;
	base64Count = 0;
}
function readBase64() {
	if (!base64Str) return END_OF_INPUT;
	if (base64Count >= base64Str.length) return END_OF_INPUT;
	var c = base64Str.charCodeAt(base64Count) & 0xff;
	base64Count++;
	return c;
}
function encodeBase64(str) {
	setBase64Str(str);
	var result = '';
	var inBuffer = new Array(3);
	var lineCount = 0;
	var done = false;
	while (!done && (inBuffer[0] = readBase64()) != END_OF_INPUT) {
		inBuffer[1] = readBase64();
		inBuffer[2] = readBase64();
		result += (base64Chars[inBuffer[0] >> 2]);
		if (inBuffer[1] != END_OF_INPUT) {
			result += (base64Chars [(( inBuffer[0] << 4 ) & 0x30) | (inBuffer[1] >> 4)]);
			if (inBuffer[2] != END_OF_INPUT) {
				result += (base64Chars [((inBuffer[1] << 2) & 0x3c) | (inBuffer[2] >> 6)]);
				result += (base64Chars [inBuffer[2] & 0x3F]);
			} else {
				result += (base64Chars [((inBuffer[1] << 2) & 0x3c)]);
				result += ('=');
				done = true;
			}
		} else {
			result += (base64Chars [(( inBuffer[0] << 4 ) & 0x30)]);
			result += ('=');
			result += ('=');
			done = true;
		}
		lineCount += 4;
		if (lineCount >= 76) {
			result += ('\n');
			lineCount = 0;
		}
	}
	return result;
}

function hmtracker_serialize(arr) {
//	console.log("SERIALIZE BEFORE", arr);
//	console.log(typeof arr == 'string' ? "YES" : "NO");
	var _srz = JSON.stringify(arr);
//	console.log("SERIALIZE AFTER", _srz);
//	console.log(typeof _srz == 'string' ? "YES" : "NO");
	return _srz;
}
function hmtracker_unserialize(e) {
//	console.log("UNSERIALIZE BEFORE", e);
	var unserialized = JSON.parse(e);
//	if( typeof unserialized == "string" ) {
//		unserialized = JSON.parse(unserialized);
//	}
//	console.log("UNSERIALIZE AFTER", unserialized);
	return unserialized;
}

function isiOS() {
	return (
	(navigator.platform.indexOf("iPhone") != -1) ||
	(navigator.platform.indexOf("iPod") != -1) ||
	(navigator.platform.indexOf("iPad") != -1)
	);
}

function getBuff(sess, location, name) {
	var src_buff = getHMTrackerData(hmtracker_cookie_name + "_buff");
	if (src_buff != null) {
		buff = hmtracker_unserialize(src_buff);
	}
	else buff = {};

	if (buff[sess] == undefined)
		buff[sess] = {};

	if (buff[sess][location] == undefined)
		buff[sess][location] = {};

	if (buff[sess][location][name] == undefined)
		buff[sess][location][name] = [];

	return buff;
}

isActive = true;


var latest_update;

function hmt_tracking_init() {

	window.onfocus = function () {
		isActive = true;
	}
	window.onblur = function () {
		isActive = false;
	}
	if (top !== self) return false;
	var myVar = "<?php echo $uip."~".$broos."~".$reguser; ?>";
	var session_data = getHMTrackerData(hmtracker_cookie_name + "_session");
	if (session_data == undefined) {
		time = 0;
		session = [Math.floor((Math.random() * 1000000000) + 1), Math.floor((new Date()).getTime() / 1000), time, document.location.href];
		setHMTrackerData(hmtracker_cookie_name + "_session", hmtracker_serialize(session), 365);
	} else {
		var now = Math.floor((new Date()).getTime() / 1000);
		session = hmtracker_unserialize(session_data);
		if ((now - session[1]) > <?php print $option['opt_record_kill_session']; ?>) {
			time = 0;
			session = [Math.floor((Math.random() * 1000000000) + 1), Math.floor((new Date()).getTime() / 1000), time, document.location.href];
			setHMTrackerData(hmtracker_cookie_name + "_session", hmtracker_serialize(session), 365);
		} else {
			if (session[3] != document.location.href)
				time = 0;
			else
				time = session[2];
			session = [session[0], Math.floor((new Date()).getTime() / 1000), session[2], document.location.href];
			setHMTrackerData(hmtracker_cookie_name + "_session", hmtracker_serialize(session), 365);
		}
	}
	var hmtracker_lastmousex = 0, hmtracker_lastmousey = 0, hmtracker_lastscrollv = 0, hmtracker_lastscrollh = 0, lastwinh = 0, lastwinw = 0;
	var hmtracker_prevmousex = 0, hmtracker_prevmousey = 0, prevscrollv = 0, prevscrollh = 0, prevwinh = 0, prevwinw = 0;
	var mouse_move, mouse_click, page_scroll;
	var sendwhen =
	<?php print $option['opt_record_interval']; ?>*
	1000;
	var interval = 100;
	var sending = false;
	var location = document.location.href;

	function sendData() {
		now = Math.floor((new Date()).getTime() / 1000);
		sending = true;

		var send_buff = getHMTrackerData(hmtracker_cookie_name + "_buff") || "";

		var send_obj = hmtracker_unserialize(send_buff);

		for (var key in send_obj) {
			for (var kkey in send_obj[key]) {
				if (send_obj[key][kkey]["window_size"] == undefined)
					send_obj[key][kkey]["window_size"] = [
						[0.8, window.innerHeight, window.innerWidth]
					];
			}
		}
		if (!(send_buff.length < 5) && isActive) {
//			console.log('DATA:', send_obj);
//			console.log('BUFF:', send_buff);
//			var body = document.body,
//				html = document.documentElement;
//
//			var height = Math.max( body.scrollHeight, body.offsetHeight,
//				html.clientHeight, html.scrollHeight, html.offsetHeight );
//			console.log(height);
			JSONP("<?php echo home_url(); ?>?hmtrackerdata=<?php echo $_GET['hmtrackerjs'] ?>&uid=<?php echo $_GET['uid'] ?>" + "&user=" + myVar + "&data=" + encodeBase64(hmtracker_serialize(send_obj)) + "&callback", function (a, b, c) {
			});
			buff = {};
			setHMTrackerData(hmtracker_cookie_name + "_buff", hmtracker_serialize(buff));
			sending = false;
			latest_update = Math.floor((new Date()).getTime() / 1000);
		} else {
			var session_data = getHMTrackerData(hmtracker_cookie_name + "_session");
			if (session_data == undefined) {
				time = 0;
				session = [Math.floor((Math.random() * 1000000000) + 1), Math.floor((new Date()).getTime() / 1000), time, document.location.href];
				setHMTrackerData(hmtracker_cookie_name + "_session", hmtracker_serialize(session), 365);
			} else {
				var now = Math.floor((new Date()).getTime() / 1000);
				session = hmtracker_unserialize(session_data);
				if ((now - session[1]) > <?php print $option['opt_record_kill_session']; ?>) {
					time = 0;
					session = [Math.floor((Math.random() * 1000000000) + 1), Math.floor((new Date()).getTime() / 1000), time, document.location.href];
					setHMTrackerData(hmtracker_cookie_name + "_session", hmtracker_serialize(session), 365);
				} else {
					session = [session[0], latest_update, session[2], document.location.href];
					setHMTrackerData(hmtracker_cookie_name + "_session", hmtracker_serialize(session));
				}
			}
			sending = false;
		}

	}

	setInterval(function () {
		sendData();
	}, sendwhen)
	prevwinw = prevwinh = 0;
	lastwinh = window.innerHeight;
	lastwinw = window.innerWidth;
	setInterval(function () {
		var cur_sess_data = getHMTrackerData(hmtracker_cookie_name + "_session");
		var cur_sess = hmtracker_unserialize(cur_sess_data);

		var mmove_iterate = 0;
		if ((hmtracker_prevmousex != hmtracker_lastmousex || hmtracker_prevmousey != hmtracker_lastmousey) && !sending && <?php print ($option['opt_record_mousemove'])?'true':'false'; ?>) {


			var buff = getBuff(cur_sess[0], location, "mouse_move");
			if (mmove_iterate == 0) {
				mmove_iterate = 0;
				buff[cur_sess[0]][location]["mouse_move"].push([parseFloat(time.toFixed(1)), hmtracker_lastmousex, hmtracker_lastmousey, window.innerWidth]);
//				console.log(1, buff);
				setHMTrackerData(hmtracker_cookie_name + "_buff", hmtracker_serialize(buff));
			} else mmove_iterate--;

			hmtracker_prevmousex = hmtracker_lastmousex;
			hmtracker_prevmousey = hmtracker_lastmousey;
		}

		if ((prevscrollv != hmtracker_lastscrollv || prevscrollh != hmtracker_lastscrollh) && !sending && <?php print ($option['opt_record_pagescroll'])?'true':'false'; ?>) {


			var buff = getBuff(cur_sess[0], location, "page_scroll");
			buff[cur_sess[0]][location]["page_scroll"].push([parseFloat(time.toFixed(1)), hmtracker_lastscrollv, hmtracker_lastscrollh]);
//			console.log(2, buff);
			setHMTrackerData(hmtracker_cookie_name + "_buff", hmtracker_serialize(buff));

			prevscrollv = hmtracker_lastscrollv;
			prevscrollh = hmtracker_lastscrollh;
		}

		if ((prevwinw != lastwinw || prevwinh != lastwinh) && !sending) {

			var buff = getBuff(cur_sess[0], location, "window_size");
			buff[cur_sess[0]][location]["window_size"].push([parseFloat(time.toFixed(1)), lastwinh, lastwinw]);
//			console.log(3, buff);
			setHMTrackerData(hmtracker_cookie_name + "_buff", hmtracker_serialize(buff));

			prevwinw = lastwinw;
			prevwinh = lastwinh;
		}
		time += (interval / 1000);
		cur_sess[2] = time;
		setHMTrackerData(hmtracker_cookie_name + "_session", hmtracker_serialize(cur_sess));
	}, interval)

	window.onmousemove = function (e) {
		hmtracker_lastmousex = e.pageX;
		hmtracker_lastmousey = e.pageY;
	}

	window.onscroll = function (e) {
		hmtracker_lastscrollv = window.pageYOffset;
		hmtracker_lastscrollh = window.pageXOffset;
	}

	window.onresize = function () {
		lastwinh = window.innerHeight;
		lastwinw = window.innerWidth;
	}


	if (!isiOS())
		window.onmousedown = function (event) {
			if (!sending) {
				var cur_sess_data = getHMTrackerData(hmtracker_cookie_name + "_session");
				var cur_sess = hmtracker_unserialize(cur_sess_data);

				var buff = getBuff(cur_sess[0], location, "mouse_click");

				buff[cur_sess[0]][location]["mouse_click"].push([parseFloat(time.toFixed(1)), event.which, hmtracker_lastmousex, hmtracker_lastmousey, hmtracker_lastscrollv, hmtracker_lastscrollh, window.innerWidth]);

//				console.log(4, buff);
				setHMTrackerData(hmtracker_cookie_name + "_buff", hmtracker_serialize(buff));
			}
		}
	if (isiOS())
		window.ontouchstart = function (event) {
			if (!sending) {
				var cur_sess_data = getHMTrackerData(hmtracker_cookie_name + "_session");
				var cur_sess = hmtracker_unserialize(cur_sess_data);

				var buff = getBuff(cur_sess[0], location, "mouse_click");

				buff[cur_sess[0]][location]["mouse_click"].push([parseFloat(time.toFixed(1)), event.which, e.touches[0].pageX, e.touches[0].pageY, hmtracker_lastscrollv, hmtracker_lastscrollh, window.innerWidth]);

//				console.log(5, buff);
				setHMTrackerData(hmtracker_cookie_name + "_buff", hmtracker_serialize(buff));
			}
		}
}

hmtrackerreadyList = []


var funcDomReady = '';
function onDomReady(func) {
	var oldonload = funcDomReady;
	if (typeof funcDomReady != 'function')
		funcDomReady = func;
	else {
		funcDomReady = function () {
			oldonload();
			func();
		}
	}
}
onDomReady(hmt_tracking_init());
function init() {
	if (arguments.callee.done) return;
	arguments.callee.done = true;
	if (funcDomReady)funcDomReady();
}
;
if (document.addEventListener)
	document.addEventListener("DOMContentLoaded", init, false);
if (/WebKit/i.test(navigator.userAgent)) {
	var _timer = setInterval(function () {
		if (/loaded|complete/.test(document.readyState)) {
			clearInterval(_timer);
			init();
		}
	}, 10);
}
window.onload = init;