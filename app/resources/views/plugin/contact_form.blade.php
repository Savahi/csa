		<div class="row">
        	<div class="col-sm-4 form-group">
				<input class="form-control" id="myContactFormMessageName" placeholder="Ваше имя (обязательно)" type="text" required>
			</div>
	        <div class="col-sm-4 form-group">
				<input class="form-control" id="myContactFormMessageEmail" placeholder="Ваш email (обязательно)" type="email" required>
        	</div>
	        <div class="col-sm-4 form-group">
				<input class="form-control" id="myContactFormMessagePhone" placeholder="Ваш телефон (не обязательно)" type="phone" required>
        	</div>
		</div>
		<textarea class="form-control" id="myContactFormMessageText" name="myContactFormMessageText" placeholder="Your message" rows="5"></textarea>
		<br>
		<div class="row" id='myContactFormCaptcha' style='display:none;'>
			<div class="col-sm-6" style='text-align:right;'>
				<canvas id='myContactFormCaptchaCanvas' width=200 height=40>
			</div>
		    <div class="col-sm-6" style='text-align:left;'>
				<input class="form-control" id="myContactFormCaptchaEnter" name="myContactFormCaptchaEnter" placeholder="The Captcha" type="text" required>
	    	</div>
		</div>
		<div class="row">
			<div class="col-sm-8 form-group">
				<div id='myContactFormMessageStatus'></div>				
			</div>
			<div class="col-sm-4 form-group">
				<button class="btn btn-primary" style='width:100%;' onclick='myContactFormCaptcha();' id='myContactFormCaptchaSend'>
					Отправить (+captcha)</button>
			</div>
		</div>

<script>

var _myContactFormCaptchaInt=null;

function myContactFormCaptcha() {
	let enter = document.getElementById('myContactFormCaptchaEnter');
	let container = document.getElementById('myContactFormCaptcha');
	let elName = document.getElementById('myContactFormMessageName');
	let elEmail = document.getElementById('myContactFormMessageEmail');
	let elPhone = document.getElementById('myContactFormMessagePhone');
	let elText = document.getElementById('myContactFormMessageText');
	let elCaptchaEnter = document.getElementById('myContactFormCaptchaEnter');
	let elStatus = document.getElementById('myContactFormMessageStatus');
	let button = document.getElementById('myContactFormCaptchaSend');
	
	if( container.style.display === 'none' ) {
		_myContactFormCaptchaInt = Math.floor( Math.random() * 10000 );
		myContactFormCaptchaDisplay( _myContactFormCaptchaInt );
		//fetch('/sendmessage/contact_form_captcha.php').then(data=>data.json()).then( function(data) { myContactFormCaptchaDisplay(data.captcha) } ).
		//		catch( function(e) { document.getElementById('myContactFormCaptcha').innerHTML = `Failed to load captcha. Please reload the page`; } );

		container.style.display = 'block';
		button.innerHTML = 'Send';
	} else {
		let formData = new FormData();
		let captchaOk = false;
		try {
			let captchaInt = parseInt( document.getElementById('myContactFormCaptchaEnter').value );
			if( captchaInt == _myContactFormCaptchaInt ) {
				captchaOk = true;
			}
		} catch(e) {
		}
		if(!captchaOk) {
			alert('The captcha you have entered is invalid. Please try again!');
			return;
		} 
		
		formData.append("name", elName.value );
		formData.append("email", elEmail.value );
		formData.append("phone", elPhone.value );
		formData.append("text", elText.value );
		formData.append("captcha", elCaptchaEnter.value );

		fetch("/scripts/contact_form.php", { body: formData, method: "post" }).then(
			data=>data.json()
		).then(
			function(data) { 
				if( data.status === 'ok' ) {
					elName.value = '';
					elEmail.value = '';
					elPhone.value = '';
					elText.value = '';
					elCaptchaEnter.value = '';
					container.style.display = 'none';
					elStatus.innerHTML = 'Your message has been sent...';
					button.innerHTML = "Отправить (+ captcha)";
				} else {
					elStatus.innerHTML = 'An error occured: ' + data.error_message;
				}

			}
		).catch( 
			function(e) { 
				elStatus.innerHTML = 'An error occured. Please, try again...'; 
			} 
		);
	}
}


function myContactFormCaptchaDisplay(captcha_value) {
	let parent = document.getElementById("myContactFormCaptcha");
	let canvas = document.getElementById("myContactFormCaptchaCanvas");	

	let context = canvas.getContext("2d");
	//context.fillStyle = "#efefef";
	//context.fillRect(0, 0, canvas.width, canvas.height);
	context.textAlign = "end";
	context.textBaseline = "top";
	context.font = "18px Arial";
	captcha_int = parseInt(captcha_value);
	context.fillText('Please, enter: ' + captcha_int, 180, 8);
	_myContactFormCaptchaInt = captcha_int;
}

</script>

