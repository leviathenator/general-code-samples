
	
	$.fn.setCurrency = function() {
		
		var elem = $(this);
		
		return elem.each(function(){
			
			var field = $(this);
			
			field.frmtNumberTypes({
				comma:true,
				symbol:'$',
				type: 'currency'
			});
			
			if(field.is('input')){
				
				if(field.data('focus-setCurrency') != 1){
					field.data('focus-setCurrency', 1);
					field.bind('focus', function() {
						var fVal = $(this).val();
						field.val(stripCurrency(fVal));
						field.select();
					});
				}
				
				if(field.data('blur-setCurrency') != 1){
					field.data('blur-setCurrency', 1);
					field.bind('blur', function() {
						//var fVal = $(this).val();
						$(this).frmtNumberTypes({
							comma:true,
							symbol:'$',
							type: 'currency'
						});
					});
				}
	
			}
		});
	};

	$.fn.setPercentage = function(limit) {
		
		var elem = $(this);
		
		return elem.each(function(){
			
			var field = $(this);
			
			field.frmtNumberTypes({
				comma:false,
				symbol:'%',
				type: 'percent'
			});
			
			if(field.is('input')){
				
				if(field.data('focus-setPercentage') != 1){
					field.data('focus-setPercentage', 1);
					field.bind('focus', function() {
						var fVal = $(this).val();
						field.val(stripPercent(fVal));
						field.select();
					});
				}
				
				if(field.data('blur-setPercentage') != 1){
					field.data('blur-setPercentage', 1);
					field.bind('blur', function() {
						//var fVal = $(this).val();
						$(this).frmtNumberTypes({
							comma:true,
							symbol:'%',
							type: 'percent'
						});
						///field.val(fVal.frmtCurrency());
					});
				}
	
			}
		});
	};
	
	$.fn.setDates = function(limit) {
		
		var elem = $(this);
		
		return elem.each(function(){
			
			var field = $(this);
			
			if(field.is('input')){
				
				if(field.data('focus-setDate') != 1){
					field.data('focus-setDate', 1);
					field.bind('focus', function() {
						clean_input_error(field);
					});
				}
				
				if(field.data('blur-setDate') != 1){
					field.data('blur-setDate', 1);
					field.bind('blur', function() {
						if(!isChrome){
							$(this).frmtDate();
						}
					});
				}
	
			}
			else{
				field.frmtDate();
			}
		});
	};
	
	$.fn.frmtDate = function() {

		var 	$this = this,
				err = false,
				isNull = false;
		
		var 	date = ($this.is('input') ? $this.val() : $this.html());
		
		isNull = (date == '');
	
		newdate = moment(date, ['MM/DD/YYYY', 'YYYY-MM-DD'], false).format('MM/DD/YYYY');
		
		var err = (newdate == 'Invalid date');
		if($this.is('input')){

			if(err){
				$this.val('');
			}else{
				
				$this.val(newdate);
			}
	 		
	 	}else{
		 	if(!err){
		 		$this.html(newdate);
		 	}
	 	}
		
		return newdate;
	}	


	var stripCurrency = function(num){
		if(typeof num != 'undefined'){
			num = num.toString();
			return num.replace(/\$|,/g, "");
		}else{
			return false;
		}
	};
	
	var stripPercent = function(num){
		if(typeof num != 'undefined'){
			num = num.toString();
			return num.replace(/\%|,/g, "");
		}else{
			return false;
		}
	};
	
	var stripInteger = function(num){
		if(typeof num != 'undefined'){
			num = num.toString();
			return num.replace(/[^0-9.-]/g, "")
		}else{
			return false;
		}
	};
	
	var stripPhone = function(num){
		if(typeof num != 'undefined'){
			num = num.toString();
			return num.replace(/[^0-9]/g, "")
		}else{
			return false;
		}
	};
	
	$.fn.frmtNumberTypes = function(number, format) {

	  	var $this = this;
	
	  	if (typeof(number) === 'object') {
	  		//incase just parameters are entered and not a number
	  		var format = number;
	  		number = ($this.is('input') ? $this.val() : $this.html());
	  	}
	  	
	  	if(typeof format.comma == 'undefined'){
		  	format.comma = (typeof format.commas != 'undefined' ? format.commas : false);
	  	}
	  	
	  	var isnegative = false;
	  	
	  	
		  	var format = format || {},
		  		comma = format.comma,
		  		symbol = format.symbol || "$",
		  		type	 = format.type   || 'number';
		  		
		  	switch(type){
				 	
			 	case 'currency' :
			 		number = stripCurrency(number);
			 		break;
			 	
			 	case 'percent' :
				 	number = (val_percent(number) ? stripPercent(number) : 0);
				 	break;
			 	
			 	case 'number' :
			 	default:
			 		number = stripInteger(number);
		 	}
		 		
		 	number = (number != '' ? parseFloat(number, 10).toFixed(2) : parseFloat(0, 10).toFixed(2));
		 	
		 	if (comma) {
		 		
		 		
		 		var count = 0;
		 		if(number < 0){
				 	isnegative = true;
				 	number = number.toString().replace('-','');
			 	}
			 	
		 		var numArr = number.toString().split("");
		
		 		var len = numArr.length - 6; 
		
		 		for (var i = len; i > 0; i= i - 3) {
		 			numArr.splice(i,0,",");
		
		 		}
		
		 		number = numArr.join("");
		 		
		 		if(isnegative){
			 		number = '-'+number;
		 		}
		 		
		 	}
		 	
		 	
		 	
		 	if (typeof symbol === 'string') {
			 	
			 	switch(type){
				 	
				 	case 'currency' :
				 		number = symbol + number;
				 		break;
				 	
				 	case 'percent' :
					 	number = number + symbol;
					 	break;
				 	
				 	case 'number' :
				 	default:
				 		number = number;
			 	}
		 	}
		 	
		 	if($this.is('input')){
		 		$this.val(number);
		 	}else{
			 	$this.html(number);
		 	}
		 	
		 	return number;
	 
	  };
	
	var frmtPhone = function(val){
		if(val.length > 0){
			var strip = val.replace(/\D/g,'');
			if(strip.length == 10){
				return val.replace(/(\d{3})(\d{3})(\d{4})/, "($1) $2-$3");
			}else{
				return null;
			}
			
		}else{
			return null;
		}
	};
	
	
	
	$.fn.setPhone = function(limit) {
		
		var elem = $(this);
		
		return elem.each(function(){
			
			var field = $(this);
			var val;
			
			if(field.is('input')){

				
				if(field.data('focus-setPhone') != 1){
					field.data('focus-setPhone', 1);
					field.bind('focus', function() {
						vval = stripPhone($(this).val());
						$this.val(vval);
					});
				}
				
				if(field.data('blur-setPhone') != 1){
					field.data('blur-setPhone', 1);
					field.bind('blur', function() {
						$this = $(this);
						set_val($this);						
					});
				}
				
				function set_val(fld){
					
					var newVal = fld.val();
					if(typeof newVal != 'undefined'){
						
						var fVal = stripPhone(newVal);
						
						if(val_phone(fVal)){
							var frmtd = frmtPhone(fVal);
							fld.val(frmtd);
						}else{
							throw_input_error(fld);
						}
					}
				}
				
				set_val(field);
				
			}
			
			if(field.is('div') || elem.is('span') || elem.is('i') || elem.is('b') || elem.is('strong') || elem.is('li') || elem.is('option')){
				
				val = stripPhone(field.text());
				if(val_phone(val)){
					var txt_frmtd = frmtPhone(val);
					field.text(txt_frmtd);
				}
			}
		});
	};
	
	// | VALIDATE PHONE NUMBER 
	var val_phone = function(obj){
		
		var strip = stripPhone(obj);
		
		if(strip.length == 10){
			var regex = /^[0-9]{1,10}$/;
			return (!regex.test(obj) ? false : true);
		}else{
			return false;
		}
		
	};

	var val_percent = function(obj){
		obj = stripPercent(obj);
		var regex = /^[0-9]{1,3}([\.][0-9]{1,3})?$/;
		return (!regex.test(obj) ? false : true);
	};
	
	var val_email = function(obj){
		var regex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		return (!regex.test(obj) ? false : true);
		
	};
	
	var val_url = function(obj){
		var regex = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
		return (!regex.test(obj) ? false : true);
		
	};
	
	// | VALIDATE PASSWORD 
	// | Password must be between 6 & 20 Characters long. 
	var val_pass = function(obj){
		var regex = /^[A-Za-z0-9!@#$%^&*()_]{6,20}$/;
		return (!regex.test(obj) ? false : true);
		
	};
	
	// | VALIDATE USERNAME 
	// | Username must be between 6 & 20 Characters long
	// | and only contain alph-numeric or numeric characters
	var val_username = function(obj){
		var regex = /^[A-Za-z0-9_]{1,20}$/;
		return (!regex.test(obj) ? false : true);
		
	};
	
	// | VALIDATE PHONE NUMBER 
	var val_phone = function(obj){
		var regex = /^\(?(\d{3})\)?[- ]?(\d{3})[- ]?(\d{4})$/;
		return (!regex.test(obj) ? false : true);
		
	};
	
	// | VALIDATE INTEGER ONLY 
    // | Must be only Integers. And less that 20 characters.  
    var val_integer = function(obj){
        var regex = /^[0-9]{1,20}$/;
        return (!regex.test(obj) ? false : true);
        
    };
    
    // | VALIDATE ALPHA CHARACTERS ONLY 
    // | Must be only Alpha Characters.  
    var val_alpha = function(obj){
        var regex = /^[A-Za-z]{1,20}$/;
        return (!regex.test(obj) ? false : true);
        
    };
    
    // | VALIDATE ALPHA CHARACTERS ONLY 
    // | Must be only Alpha Characters.  
    var val_alpha_num = function(obj){
        var regex = /^[A-Za-z0-9 ]{1,500}$/;
        return (!regex.test(obj) ? false : true);
	};
	
	function flip_readonly_from_input(obj){
		var str = $(obj.data('app-rel'));
		obj.text(str.val());
	}
	
	var handleTemplate = function(name){
		var template = $(name);
		return template.html();
	};
	


	var throw_input_error = function(inpt){
		var obj = inpt.parent();
		if(obj.hasClass('has-success')) obj.removeClass('has-success');
		if(!obj.hasClass('has-error')) obj.addClass('has-error');
	};
	
	var throw_input_success = function(inpt){
		var obj = inpt.parent();
		if(obj.hasClass('has-error')) obj.removeClass('has-error');
		if(!obj.hasClass('has-success')) obj.addClass('has-success');
	};
	
	var clean_input_error = function(inpt){
		var obj = inpt.parent();
		if(obj.hasClass('has-error')) obj.removeClass('has-error');
		if(obj.hasClass('has-success')) obj.removeClass('has-success');
	};
	

	
	
	
	
	
	
	
	