define(function(require, exports, module) {

    require('jquery.cycle2');
    var Validator = require('bootstrap.validator');

    exports.run = function() {

    	$('.homepage-feature').cycle({
	        fx:"scrollHorz",
	        slides: "> a, > img",
	        log: "false",
	        pauseOnHover: "true"
    	});

    	var validator = new Validator({
            element: '#login-form'
        });

        validator.addItem({
            element: '[name="email"]',
            required: true,
            rule: 'email email_remote'
        });

        validator.addItem({
            element: '[name="password"]',
            required: true,
            rule: 'minlength{min:5} maxlength{max:20}'
        });

        validator.addItem({
            element: '[name="confirmpassword"]',
            required: true,
            rule: 'confirmation{target:#register_password}'
        });

    };

});