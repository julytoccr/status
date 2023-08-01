$(document).ready(function() {
	var originalEmailRule = $.fn.form.settings.rules.email;
	$.fn.form.settings.rules.email = function(value, optional) {
	    if (typeof optional !== 'undefined' && optional == 'optional' && value == '') {
		    return true;
		}
		
		return originalEmailRule(value);
	};
	
	$.fn.form.settings.rules.semioptional = function(value, args) {
		if (value != '') {
		    return true;
		}
	
	    var args = args.split(',');
		var formId = args[0];
		var $form = $('.form#' + formId);
		for (var i = 1; i < args.length; ++i) {
		    var value = $form.form('get value', args[i]);
			
			if (value != '') {
			    return true;
			}
		}
		
		return false;
	};
	
	/**
	 * jQuery.browser.mobile (http://detectmobilebrowser.com/)
	 * jQuery.browser.mobile will be true if the browser is a mobile device
	 **/
	(function(a){(jQuery.browser=jQuery.browser||{}).mobile=/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))})(navigator.userAgent||navigator.vendor||window.opera);
});

$(document).ready(function() {
    var $loginForm = $('.form#login');
    var $resetForm = $('.form#password_reset');
	var $resetMessage = $loginForm.find('#reset_message');
	
	var $usernameInput = $resetForm.find('input[name=username]');
	var $emailInput = $resetForm.find('input[name=email]');
	
	$resetMessage.nag();
	$resetMessage.nag('hide').css('display', 'none');
  
	triggerForm = function() {
		$loginForm.transition('slide down', 500);
	}

    $('img.transition').transition('fade in', 2000);
	setTimeout(triggerForm, 0);
	
	var animating = false;
	$('.button#launch_password_reset').click(function() {
	  if (animating) {
	    return;
	  }
	  animating = true;
	  
	  var username = $loginForm.form('get value', 'username');
	  if (username != '') {
		  $usernameInput.val(username); // using $loginForm.form('set value', 'username', username) will trigger validation and fire onSuccess
	  }
			  
	  $loginForm.transition('fly right', 700);
	  setTimeout(function() {
		  $resetForm.transition('fly left', 700, function() {
		      animating = false;
	      });
	  }, 650);
	});
	
	var returnToLogin = function() {
	  if (animating) {
	    return;
	  }
	  animating = true;
	  
	  var username = $loginForm.form('get value', 'username');
	  var password = $loginForm.form('get value', 'password');
	  if (username == '' && password == '') {
	      $loginForm.form('reset');
	  }
	  
	  $resetForm.form('reset');
	  $resetForm.transition('fly left', 700);
	  setTimeout(function() {
		  $loginForm.transition('fly right', 700, function() {
		      animating = false;
	      });
	  }, 650);
	};
	$('.button#launch_login').click(returnToLogin);

	$loginForm.form({
		username: {
		  identifier : 'username',
		  rules: [
			{
			  type   : 'empty',
			  prompt : 'Please enter your username'
			}
		  ]
		},
		password: {
		  identifier : 'password',
		  rules: [
			{
			  type   : 'empty',
			  prompt : 'Please enter your password'
			},
			{
			  type   : 'length[8]',
			  prompt : 'At least 8 characters long'
			}
		  ]
		}
	  },
	  {
	    inline: true,
		revalidate: ((jQuery.browser.mobile) ? false : true), // On phones, it closes the keyboard when it still doesn't pass the test
		onFailure: function() {
		    $resetMessage.nag('hide');
			return false;
		},
		onSuccess: function() {
		    $resetMessage.nag('hide');
			
			alert('You have logged in!');
			
			return true;
		}
	  }
	);
	
	var semiChecking = false;
	var semioptionalOnChangeCheck = function(fieldName) {
		if (semiChecking == true) {
		    return;
		}
		semiChecking = true;
		
		$resetForm.form('validate form');
		
		semiChecking = false;
	};
	
	$resetForm.form({
		username: {
		  identifier : 'username',
		  rules: [
			{
			  type   : 'semioptional[password_reset,email]',
			  prompt : 'Username must be specified if email is not specified'
			}
		  ]
		}, 
		email: {
		  identifier : 'email',
		  rules: [
			{
			  type   : 'email[optional]',
			  prompt : 'Not a valid email address'
			},
			{
			  type   : 'semioptional[password_reset,username]',
			  prompt : 'Email must be specified if username is not specified'
			}
		  ]
		}
	  },
	  {
	    inline: true,
		revalidate: ((jQuery.browser.mobile) ? false : true), // On phones, it closes the keyboard when it still doesn't pass the test
		onInvalid: semioptionalOnChangeCheck,
		onValid: semioptionalOnChangeCheck,
		onSuccess: function() {
		    if (semiChecking) {
			   return false;
			}
			
		    $resetMessage.nag('show');
		    returnToLogin();
			return false;
		}
	  }
	);
});