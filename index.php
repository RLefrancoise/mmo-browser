<!DOCTYPE html>
<?php
    require_once (__DIR__ . '/server/vendor/autoload.php');
    require_once (__DIR__ . '/server/bootstrap.php');
?>

<html>
	<head>
		<meta charset="utf-8" />
		<title>MMO Browser</title>
		<link rel="stylesheet" type="text/css" href="public/css/style.css" />

		<script>
			var socket = undefined;
		</script>
		<script type="text/javascript" src="public/js/jquery-1.11.1.js"></script>
		<script type="text/javascript" src="public/js/prototype.js"></script>

        <script type="text/javascript" src="public/js/client/Packets.js.php"></script>

		<script type="text/javascript" src="public/js/Key.js"></script>
		<script type="text/javascript" src="public/js/classes/MapEntity.js"></script>

		<script type="text/javascript" src="public/js/classes/Monster.js"></script>
		<script type="text/javascript" src="public/js/classes/Character2.js"></script>
		<script type="text/javascript" src="public/js/classes/Map3.js"></script>
		<!--script type="text/javascript" src="public/js/map.js"></script-->
		<script type="text/javascript" src="public/js/classes/Scene.js"></script>
		<script type="text/javascript" src="public/js/classes/Screen.js"></script>
		<!--script type="text/javascript" src="/socket.io/socket.io.js"></script-->
	</head>
	<body>
		<div id="main_div">
			<div id="login_div" style="float:left">
                <label for="login_field">Login</label>
                <input id="login_field" name="login_field" type="text">
            </div>
			<div id="login_button_block" style="float:right">
                <input id="login_button" type="submit" value="Log in">
            </div>
			<div id="logout_button_block" style="float:right;display:none;">
                <input id="logout_button" type="submit" value="Logout">
            </div>
			<div id="password_div">
                <label for="password_field">Password</label>
                <input id="password_field" name="password_field" type="password">
            </div>

			<canvas id="canvas" width="640" height="480">Votre navigateur ne supporte pas HTML5, veuillez le mettre à jour pour jouer.</canvas>
			<div id="msgbox" name="msgbox"></div>
			<form>
				<input id="chatbox_input" type="text">
				<input id="chatbox_send_button" type="submit" value="Send" style="float:right">
			</form>
		</div>

		<script type="text/javascript">
            var screen = new Screen();

			jQuery('#login_button').click(function(event) {
				event.preventDefault();

				var login = jQuery('#login_field').val();
				var password = jQuery('#password_field').val();

				if(!login || !password) {
					return;
				} else{
                    new CM_LOGIN_REQUEST({
                        login: login,
                        password: password,
                    }, socket).send();

					jQuery('#login_field').val('');
					jQuery('#password_field').val('');
					jQuery('#login_div').css('display', 'none');
					jQuery('#password_div').css('display', 'none');
					jQuery('#login_button_block').css('display', 'none');
					jQuery('#logout_button_block').css('display', 'block');
				}
			});

			jQuery('#logout_button').click(function(event) {
				event.preventDefault();
                socket.close();

                writeMessage('You have logged out.', 'rgb(255,128,0)');

                jQuery('#login_div').css('display', 'block');
                jQuery('#password_div').css('display', 'block');
                jQuery('#login_button_block').css('display', 'block');
                jQuery('#logout_button_block').css('display', 'none');

                screen.scene.destroy();

                initSocket();
			});

			jQuery('#chatbox_send_button').click(function(event) {
				sendMessage();
				event.preventDefault();
			});

			//bind enter key to chatbox input to send message
			jQuery('#chatbox_input').keydown(function(event) {
				if(event.which == Key.Enter) {
					sendMessage();
					event.preventDefault();
				}
			});

			jQuery('#chatbox_input').focus(function(event) {
				canvas.receiveInput = false;
			});

			jQuery('#chatbox_input').blur(function(event) {
				canvas.receiveInput = true;
			});

			function sendMessage() {
				var msg = jQuery('#chatbox_input').val();
				console.log('send message : ' + msg);
				jQuery('#chatbox_input').val('');

                new CM_CHAT_MESSAGE({
                    msg: msg,
                }, socket).send();
			}

			function writeMessage(msg, color) {
				if(color == undefined) color = 'rgb(255,255,255)';

				var box = document.getElementById('msgbox');
				var text = '<span style="color:' + color + '">' + msg + '</span><br>';
				box.innerHTML += text;
				//box.scrollTop += 9999;
			}

            function initSocket() {
                var serverBaseUrl = document.domain;

                socket = new WebSocket('ws://' + serverBaseUrl + ':8080');
                socket.onopen = function(ev) {
                    initProtocol();
                };

                socket.onerror = function(ev) {
                    console.log(JSON.stringify(ev, null, 4));
                }

                socket.onclose = function(ev) {
                    console.log(JSON.stringify(ev, null, 4));
                }
            }

            function initProtocol() {
                socket.onmessage = function(ev) {
                    var data = JSON.parse(ev.data);
                    console.log(data);
                    var packet = Packet.createFromType(data, socket);
                    packet.doAction();
                }
            }

			function init() {
				initSocket();
			}

			jQuery(document).on('ready', init);
		</script>
	</body>
</html>
