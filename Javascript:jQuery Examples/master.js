var 	$thisPage,
	$bodyWrap;

var 	$rellist, // Group of <ul> obljects that are sortable
	$rellist_tabs, // group of tabs under each sortable object.
	$open_popup_lnks,
	$returnpopup,
	$activate_drawer_links,
	winHt,
	$scrollWin,
	$scrollWinOffset,
	$scrollWinHt,
	$scrollWinBottom,
	setScrollWinHt,
	active_relat_id,
	stop_popup_relatoverwrite = false,
	popup_saveform = false; // AutoSave stop variable
	 // AutoSave stop variable
	
var 	toolbox_panel,
	toolbox_loader,
	toolbox_content,
	body_wrap;
	
//**** Check browser if Chrome  *****//

var 	isChrome 	= false,
	isChromium 	= window.chrome,
    	vendorName 	= window.navigator.vendor,
    	isOpera 	= window.navigator.userAgent.indexOf("OPR") > -1,
    	isIEedge 	= window.navigator.userAgent.indexOf("Edge") > -1;

if(isChromium !== null && isChromium !== undefined && vendorName === "Google Inc." && isOpera == false && isIEedge == false) {
   isChrome = true; 
}
	
$(document).ready(function(){
	
	
	$thisPage = $('body'),
	$bodyWrap = $('#body-wrap');
	
	toolbox_panel = $('#toolbox-panel'),
	toolbox_loader = $('#toolbox-loader')
	toolbox_content = $('#toolbox-content'),
	body_wrap = $('#body-wrap');
	
	var interval = setInterval(function() {
	    if(document.readyState === 'complete') {
	        clearInterval(interval);
	        pageInit();
	    }    
	}, 100);
	
	var pageInit = function(){		
		
		// Global settings for all Popovers
		var setPopStatus = {
			//placement: 'right',
			container: 'body',
			html: true,
			selector: '[rel="popovers"]',
			trigger:'click',
			content: function () {
				
				var 	btn = $(this),
					id = btn.data('app-id'),
					href = btn.data('app-href');
					tmplat = handleTemplate('#'+$(this).data('app-template'));
					tmplat = $(tmplat);
				
				var parsefunc = tmplat.data('app-func');
				
				if(typeof parsefunc != 'undefined'){
					var func = window[parsefunc];
					if(typeof func == 'function'){
						func(tmplat, id, href);
					}
				}else{
					tmplat.append(btn.data('content'));
				}

				return $(tmplat);
			}
		}
		$thisPage.popover(setPopStatus);
		
		// Set WSIYWIG textareas using Cazary
		var 	cazary_fields_parent 	= $thisPage.find('[id="exec-summary-cazary"] a[data-toggle="tab"]'),
			cazary_fields 		= $thisPage.find('.cazary-textarea textarea');
		
		if(cazary_fields_parent.length > 0){
			
			cazary_fields_parent.on('click', function(){
				cazary_fields.cazary({
					 mode: 'rte',
					 commands: 'MINIMAL'
				 });
			});
		}
		
		// Set Dyanamic AJAX editable fields
		var 	ajx_onfld 	= $thisPage.find('span[data-app-mode="submit-ajax-onefld-form"]');
		
		if(ajx_onfld.length > 0) { 
			ajx_onfld.ajx_onefld_submit(); 
		}
		
		// Set Dynamic Modal links
		open_dyn_popup($thisPage.find('[data-app-mode="open_dyn_popup"]'));
		
		// Set Dynamic AJAX lists
		var ajx_lists	= $thisPage.find('[data-app-mode="add-to-list"]');
		
		if(ajx_lists.length > 0){
			ajx_lists.addToList();
		}

		$thisPage.find('[data-app-mode="delete-file-box"]').del_box_form('click');
		
		// Relationship Board Settings
		$thisPage.find('[data-app-mode="board-collapse-rel"]').collapse({ parent: '#relat-list', toggle:false });
		$thisPage.find('[data-app-mode="toggle-relationship"]').setToggleRows('rel');
		$thisPage.find('[data-app-mode="toggle-borrower"]').setToggleRows('bwr');
		$thisPage.find('[data-app-mode="setreviewer"]').setToggleReviewer();
		
		// Set Toolbox links
		$thisPage.find('[data-app-mode="open-toolbox"]').setToolboxOpenLinks();
		$thisPage.find('*[data-app-mode="close-toolbox"]').setToolboxCloseLinks();
		
		// Set Panel get events
		$thisPage.find('[id^="rel-tabgroup-"] a[data-toggle="tab"]').setTabPanelShowHide();

		format_input_types($('body'));
		
		$('#screen-onload').fadeOut(300);
		
	};

});


/****************************************************/
/****************** INIT FUNCTIONS ******************/
/****************************************************/

function popstatus(tmplat, id, href){
	
	var lnk = tmplat.find('a');
	
	$('.body').one('click', function(){
		$('#popstatus'+id).popover('hide').popover('destroy');	
	});
	
	lnk.each(function(){
		
		$(this).on('click', function(){
			
			$.get($(this).data('app-href')+id, function(arr){
				
				$('#popstatus'+id).popover('hide').popover('destroy');
				refresh_relate_row(id);
			});
			
		});
	});
}

/**
 * Low-level init formatting function called inside Panels, Dialog, Toolbox & Pages.
 * Calls setter functions for Formatting Phone Number, Currency, Percentages.
 * @param {Element/Object} parent	- Parent object of the Panel, Dialog, Toolbox or Page
 * @return {Function} 	 
 */
function format_input_types(parent){
	
	parent.find('input[type="tel"], .tel').setPhone();
	parent.find('input[type="dollar"], .dollar').setCurrency();
	parent.find('input.percent, .percent').setPercentage();
}

// low-level init function for all Toolboxs
// Calls setters for  Closing Toolbox.
function init_toolbox_func(){
	
	// @toolbox_content is a Global Variable.
	var obj = toolbox_content.find('[data-app-mode="toolbox-close"]');
	obj.setToolboxCloseLinks();
	
	open_dyn_popup(toolbox_content.find('[data-app-mode="open_dyn_popup"]'));
	toolbox_content.setMasterSlaveFields();
	
	var if_save_btn = parent.find('#save-panel-form');
	if(if_save_btn.length > 0){
		
		if_save_btn.togglePanelSaveButton();
		
		toolbox_content.find('input:not(input[type="submit"]), textarea, select').on('change', function(){
			if(!popup_saveform){
				popup_saveform = true;
				toolbox_content.find('#save-panel-form').togglePanelSaveButton();
			}
			
		});
	}
	
	toolbox_content.find('*[data-app-mode="datepicker"], input[type="date"]').setDates();
	toolbox_content.find('input[type="tel"], *.phone').setPhone();
	toolbox_content.find('input[type="dollar"], *.currency, *.dollar').setCurrency();
	toolbox_content.find('*.percent').setPercentage();
	
}

/**
 * Low-level init formatting function called inside Panels, Dialog, Toolbox & Pages.
 * Calls Setters for Date Picker, File Browser and Formatting functions.
 * @param {Element/Object} parent	- Parent object of the Panel, Dialog, Toolbox or Page
 * @return {Function} 	 
 */
// Calls setters for Date Picker, File Browser and Formatting functions.
function init_panel_func(parent){
	
	if(typeof parent != 'undefined'){
		
		open_dyn_popup(parent.find('[data-app-mode="open_dyn_popup"]'));
		
		parent.setMasterSlaveFields();
		
		if(parent.find('#save-panel-form').length > 0){
			
			parent.find('#save-panel-form').togglePanelSaveButton();
			
			parent.find('input:not(input[type="submit"]), textarea, select').on('change', function(){
				if(!popup_saveform){
					popup_saveform = true;
					parent.find('#save-panel-form').togglePanelSaveButton();
				}
				
			});
		}
		parent.find('span[data-app-mode="submit-ajax-onefld-form"]').ajx_onefld_submit();
		
		parent.setFileBrowserLnks();
		parent.find('*[data-app-mode="datepicker"], input[type="date"]').setDates();
		parent.find('input[type="tel"], *.phone').setPhone();
		parent.find('input[type="dollar"], *.currency, *.dollar').setCurrency();
		parent.find('*.percent').setPercentage();
	}else{
		show_fixed_alert('Error: Loading panel setter failed. No parent variable set', 10000, 'danger');
	}
}


/***************************************************/
/****************** FIELD SETTERS ******************/
/***************************************************/

/**
 * Setter for the Reviewer Claim buttons.  
 * @return {Clickable Element} 	 
 */
$.fn.setToggleReviewer = function(){
	
	return $(this).each(function(){
		
		var $this = $(this);
		
		$this.on('click', function(){
			
			toggle_reviewer($this);
			
		});
	});
};

$.fn.setToggleRows = function(type){
	var $this = $(this);
	
	return $this.on('click', function(){
		var 	handle = $(this), 
			row = $('#board-collapse-'+type+'-'+handle.data('app-id'));
		togglerows(row, handle);
		
	});
};

$.fn.format_force_negative = function(){
	var $this = $(this);
	return $this.each(function(){
		$(this).on('keyup', function(){
			var valcheck = parseFloat($(this).val());
			if(valcheck > 0){
				valcheck = valcheck-(valcheck*2);
				$(this).val(valcheck);
			}
		});
	});
};


/**
 * Setter for all Master/Slave Fields.
 *
 * Element example:
 * <tr>
 *	<td>Field Name</td>
 *	<td data-app-mode="dependant-master">Master field</td>	
 *</tr>
 *<tr class="mod_tx-dependant" aria-expanded="false" data-app-mode="dependant-slave" data-app-master="%master_field_name%">
 *	<td>Slave Field Name</td>
 *	<td>Slave Field</td>	
 *</tr>
 *
 * @param {Element/Object} .Object	- Opens Slave <tr> tags and sets fields based on boolean value of Master.
 * @return {Clickable Element} 	 
 */
$.fn.setMasterSlaveFields = function(){
	
	var parent = $(this);
	var masters = parent.find('[data-app-mode="dependant-master"]');
	
	return masters.each(function(){
		
		var 	wrapper = $(this),
			master = wrapper.find('select, input[type="checkbox"]'),
			name = master.attr('name'),
			fields = parent.find('[data-app-mode="dependant-slave"][data-app-master="'+name+'"]');
			type = (master.is(':checkbox') ? 'checkbox' : 'select'),
			initDefaultChoice = (typeof wrapper.data('app-default') !== 'undefined' ? wrapper.data('app-default') : 'N');
		
		if(type == 'select' && master.find('option:selected').attr('value') == initDefaultChoice){
			change_slaves(fields, true);
		}else if(type =='checkbox' && master.is(':checked')){
			change_slaves(fields);
		}
		
		master.on('change', function(){
			
			var 	$this = $(this),
				val = $this.val(),
				defaultChoice = (typeof wrapper.data('app-default') !== 'undefined' ? wrapper.data('app-default') : 'N');
			console.log(defaultChoice);
			if(type == 'select'){
				val = (val == defaultChoice ? true : false);
			}else if(type =='checkbox'){
				val = ($this.is(':checked') ? true : false);
			}

			change_slaves(fields, val);
			
		});
		
		function change_slaves(items, val){
			
			items.each(function(){
				var item = $(this);
				var field = item.find('input, select, textarea');

				if(val){
					if(!field.prop('required')){
						field.prop('required',true);
					}
					if(item.is(':hidden')){
						item.fadeIn(300);
					}
				}else{
					if(field.prop('required')){
						field.prop('required',false);
						
					}
					if(item.is(':visible')){
						item.fadeOut(300);
						field.val('');
					}
				}
			});
		}
	});
};

$.fn.togglePanelSaveButton = function(){
	
	var $this = $(this);
	if(popup_saveform){
		$this.prop('disabled', false);
	}else{
		$this.prop('disabled', true);
		popup_saveform = false;
	}
	
};


/**
 * Setter for all File Browser Fields.
 *
 * Element example:
 * <div class="file-upload-control" data-app-type="file-upload-control">
 * 	<div class="input-group">
 * 		<label class="input-group-btn">
 * 			<span class="btn btn-primary">
 * 				Browse&hellip; <input type="file" name="" aria-controls="#ID" accept="file formats" style="display: none;">
 * 			</span>
 * 		</label>
 * 		<input type="text" class="form-control" data-app-id="#ID" accept="file formats" placeholder="" readonly>
 * 	</div>
 * 	<span class="help-block">
 * 		Help text
 * 	</span>
 * </div>
 *
 * @param {Element/Object} .Object	- Adds click function to button
 * @return {Clickable Element} 	 
 */
$.fn.setFileBrowserLnks = function(){
	var parent = $(this);
	parent.on('change', ':file', function() {
		var 	input = $(this),
			numFiles = input.get(0).files ? input.get(0).files.length : 1,
			label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
			
		input.trigger('fileselect', [numFiles, label]);
	});
		
	parent.find(':file').on('fileselect', function(event, numFiles, label) {
		var wrap = $(this).closest('div[data-app-type="file-upload-control"]');
		var dummy = wrap.find('input[data-app-id="file-chooser-dummy"]').attr('placeholder', label);
	});
};

/*********************************************/
/****************** TOOLBOX ******************/
/*********************************************/

/**
 * Setter for the Toolbox 'Open' button (X).
 * Element example <button id="" data-app-mode="open-toolbox" data-app-href=""></button>
 * @param {Element/Object} .Object	- Adds click function to button
 * @return {Clickable Element} 	 
 */
$.fn.setToolboxOpenLinks = function(){

	return $(this).each(function(){
		
		var $this = $(this);
		
		$this.on('click', function(){
			
			refresh_toolbox($this);
		});
	});
	
};

$.fn.refreshToolbox = function() {
	refresh_toolbox($(this));
};

function refresh_toolbox(elm) {
	var lnk = elm.data('app-href');

	if(toolbox_panel.hasClass('open') || toolbox_content.is(':visible')){
		toolbox_close( function(){
			if(!toolbox_panel.data('opening')){
				getcontent();
			}
		} , true );
	}else{

		if(!toolbox_panel.data('opening')){
			getcontent();
		}
	}

	function getcontent(){

		toolbox_panel.data('opening', true);

		$.get(lnk, function(arr){

			if(!arr.err){

				$(arr.data).appendTo(toolbox_content);

				toolbox_open( init_toolbox_func() );


			}else{
				setTimeout(function() {
					show_fixed_alert(arr.msg, 2000);
				}, 700 );

				toolbox_close();
			}

		}, 'json');
	}
};

/**
 * Setter for the Toolbox 'Close' button (X).  
 * @return {Clickable Element} 	 
 */
$.fn.setToolboxCloseLinks = function(){
	
	return $(this).each(function(){
		
		var $this = $(this);
		
		$this.on('click', function(){
			
			toolbox_close();
			
		});
		
	});
	
};



/********************************************/
/****************** ALERTS ******************/
/********************************************/

/**
 *** Displays a Bootstrap alert window with a close button for the specified amount of time. 
 *** Alert window is dropped after a specified element. 
 * @param {htmlString/Element} alert_after	Defined element or "Line" that the alert will be inserted AFTER.
 * @param {String} msg					Error message that will be inserted into the alert window.
 * @param {Number} time					The amount of time in MS that they alert will stay visible. Default is 0 and is defined as infinite. 
 * @param {String} priority				Defines the alert class color of the alert box. 
 * 								Default is 'info'. Other options are: default (grey), primary (blue), success (green), warning (yellow), danger (red)
 * @return {Event/Alert Window} 	 
 */
 
function show_line_alert(alert_after, msg, time, priority){
	
	var 	template = handleTemplate('#template-alert-inline');
		
	$(template).insertAfter($(alert_after));
		
	var 	alert = $('#alert-well-inline'),
		alert_class = (typeof priority != 'undefined' ? priority : 'info');
		
		alert.addClass('alert-'+alert_class);
		alert.find('#alert-dialog').text(msg);
		if(time){
			alert.fadeIn(400).delay(time).fadeOut(400, function(){
				alert.remove();
			});
		}else{
			alert.fadeIn(400);
		}
		
};

/**
 *** Displays a Bootstrap alert window that is displayed on top of allelement son the page. 
 *** Alert window is removed after a specified time limit defined by (time). 
 * @param {String} msg					Error message that will be inserted into the alert window.
 * @param {Number} time					The amount of time in MS that they alert will stay visible. Default is 6000ms 
 * @param {String} priority				Defines the alert class color of the alert box. 
 * 								Default is 'info'. Other options are: default (grey), primary (blue), success (green), warning (yellow), danger (red)
 * @return {Event/Alert Window} 	 
 */
 
function show_fixed_alert(msg, time, priority){
	
	var 	template = handleTemplate('#template-alert-fixed');
	
	$('body').append(template);
		
	var 	alert = $('#alert-well-fixed'),
		alert_class = (typeof priority != 'undefined' ? priority : 'info'),
		alert_time = (typeof time != 'undefined' ? time : 6000);
		
		alert.addClass('alert-'+alert_class);
		alert.find('#alert-dialog').text(msg);
		alert.fadeIn(400).delay(alert_time).fadeOut(400, function(){
			alert.remove();
		});
	

}

function submit_form_push(frm, callback){
	var $frm = $(frm);

	$frm.on('submit', function(event){
		
		event.preventDefault();
		
		if($frm[0].checkValidity()){
			
			
			function showResponse(responseText, statusText, xhr, $form) { 

				var arr = jQuery.parseJSON(responseText);
				
				if (!arr.err) {	

					if(typeof callback == 'function'){

						setTimeout(function() {
							
							// 8-7-15 Changed from sending arr.data to sending entire return array
							callback(arr);
							if(typeof arr.msg != 'undefined' && arr.msg.length > 0){
								show_fixed_alert(arr.msg, 6000, 'warning');
							}
							
						}, 200 );
						
					}else{

						if(typeof arr.msg != 'undefined' && arr.msg.length > 0){
							show_fixed_alert(arr.msg, 6000, 'warning');
						}
						
					}
				}else{
					var err_str = 'There was an error posting your data.';
					show_fixed_alert(err_str, 10000, 'danger');
				}
			}
			
			var options = {
				type: 'POST',
				success : showResponse
			};
			
			var stripnums = $frm.find('input[type="dollar"], input.percent, input.dollar, input.dollar[type="hidden"], input.percent[type="hidden"]');

			
			stripnums.each(function(){
				
				var num = $(this).frmtNumberTypes({
					comma:false,
					type: 'number'
				});
				
			});
			
			$frm.ajaxSubmit(options);
		
		}
	});
	
	
	
}

function showResponse(responseText){
	var arr = jQuery.parseJSON(responseText);

	if (!arr.err) {
		if(typeof arr.msg != 'undefined' && arr.msg.length > 0){
			show_fixed_alert(arr.msg, 6000, 'warning');
		}

	}else{
		var err_str = 'There was an error posting your data.';
		show_fixed_alert(err_str, 10000, 'danger');
	}
}


(function () {
	// Prepare
	var $, ScrollTo
	$ = window.jQuery || require('jquery')

	// Fix scrolling animations on html/body on safari
	$.propHooks.scrollTop = $.propHooks.scrollLeft = {
		get: function (elem, prop) {
			var result = null
			if ( elem.tagName === 'HTML' || elem.tagName === 'BODY' ) {
				if ( prop === 'scrollLeft' ) {
					result = window.scrollX
				}
				else if ( prop === 'scrollTop' ) {
					result = window.scrollY
				}
			}
			if ( result == null ) {
				result = elem[prop]
			}
			return result
		}
	}
	$.Tween.propHooks.scrollTop = $.Tween.propHooks.scrollLeft = {
		get: function (tween) {
			return $.propHooks.scrollTop.get(tween.elem, tween.prop)
		},
		set: function (tween) {
			// Our safari fix
			if ( tween.elem.tagName === 'HTML' || tween.elem.tagName === 'BODY' ) {
				// Defaults
				tween.options.bodyScrollLeft = (tween.options.bodyScrollLeft || window.scrollX)
				tween.options.bodyScrollTop = (tween.options.bodyScrollTop || window.scrollY)

				// Apply
				if ( tween.prop === 'scrollLeft' ) {
					tween.options.bodyScrollLeft = Math.round(tween.now)
				}
				else if ( tween.prop === 'scrollTop' ) {
					tween.options.bodyScrollTop = Math.round(tween.now)
				}

				// Apply
				window.scrollTo(tween.options.bodyScrollLeft, tween.options.bodyScrollTop)
			}
			// jQuery's IE8 Fix
			else if ( tween.elem.nodeType && tween.elem.parentNode ) {
				tween.elem[tween.prop] = tween.now
			}
		}
	}

	// jQuery ScrollTo
	ScrollTo = {
		// Configuration
		config: {
			duration: 400,
			easing: 'swing',
			callback: null,
			durationMode: 'each',
			offsetTop: 0,
			offsetLeft: 0
		},

		// Set Configuration
		configure: function (options) {
			// Apply Options to Config
			$.extend(ScrollTo.config, options || {})

			// Chain
			return this
		},

		// Perform the Scroll Animation for the Collections
		// We use $inline here, so we can determine the actual offset start for each overflow:scroll item
		// Each collection is for each overflow:scroll item
		scroll: function (collections, config) {
			// Prepare
			var collection, $container, $target, $inline, position,
				containerScrollTop, containerScrollLeft,
				containerScrollTopEnd, containerScrollLeftEnd,
				startOffsetTop, targetOffsetTop, targetOffsetTopAdjusted,
				startOffsetLeft, targetOffsetLeft, targetOffsetLeftAdjusted,
				scrollOptions,
				callback

			// Determine the Scroll
			collection = collections.pop()
			$container = collection.$container
			$target = collection.$target

			// Prepare the Inline Element of the Container
			$inline = $('<span/>').css({
				'position': 'absolute',
				'top': '0px',
				'left': '0px'
			})
			position = $container.css('position')

			// Insert the Inline Element of the Container
			$container.css({position: 'relative'})
			$inline.appendTo($container)

			// Determine the top offset
			startOffsetTop = $inline.offset().top
			targetOffsetTop = $target.offset().top
			targetOffsetTopAdjusted = targetOffsetTop - startOffsetTop - parseInt(config.offsetTop, 10)

			// Determine the left offset
			startOffsetLeft = $inline.offset().left
			targetOffsetLeft = $target.offset().left
			targetOffsetLeftAdjusted = targetOffsetLeft - startOffsetLeft - parseInt(config.offsetLeft, 10)

			// Determine current scroll positions
			containerScrollTop = $container.prop('scrollTop')
			containerScrollLeft = $container.prop('scrollLeft')

			// Reset the Inline Element of the Container
			$inline.remove()
			$container.css({position: position})

			// Prepare the scroll options
			scrollOptions = {}

			// Prepare the callback
			callback = function () {
				// Check
				if ( collections.length === 0 ) {
					// Callback
					if ( typeof config.callback === 'function' ) {
						config.callback()
					}
				}
				else {
					// Recurse
					ScrollTo.scroll(collections, config)
				}
				// Return true
				return true
			}

			// Handle if we only want to scroll if we are outside the viewport
			if ( config.onlyIfOutside ) {
				// Determine current scroll positions
				containerScrollTopEnd = containerScrollTop + $container.height()
				containerScrollLeftEnd = containerScrollLeft + $container.width()

				// Check if we are in the range of the visible area of the container
				if ( containerScrollTop < targetOffsetTopAdjusted && targetOffsetTopAdjusted < containerScrollTopEnd ) {
					targetOffsetTopAdjusted = containerScrollTop
				}
				if ( containerScrollLeft < targetOffsetLeftAdjusted && targetOffsetLeftAdjusted < containerScrollLeftEnd ) {
					targetOffsetLeftAdjusted = containerScrollLeft
				}
			}

			// Determine the scroll options
			if ( targetOffsetTopAdjusted !== containerScrollTop ) {
				scrollOptions.scrollTop = targetOffsetTopAdjusted
			}
			if ( targetOffsetLeftAdjusted !== containerScrollLeft ) {
				scrollOptions.scrollLeft = targetOffsetLeftAdjusted
			}

			// Check to see if the scroll is necessary
			if ( $container.prop('scrollHeight') === $container.height() ) {
				delete scrollOptions.scrollTop
			}
			if ( $container.prop('scrollWidth') === $container.width() ) {
				delete scrollOptions.scrollLeft
			}

			// Perform the scroll
			if ( scrollOptions.scrollTop != null || scrollOptions.scrollLeft != null ) {
				$container.animate(scrollOptions, {
					duration: config.duration,
					easing: config.easing,
					complete: callback
				})
			}
			else {
				callback();
			}

			// Return true
			return true
		},

		// ScrollTo the Element using the Options
		fn: function (options) {
			// Prepare
			var collections, config, $container, container
			collections = []

			// Prepare
			var	$target = $(this)
			if ( $target.length === 0 ) {
				// Chain
				return this
			}

			// Handle Options
			config = $.extend({}, ScrollTo.config, options)

			// Fetch
			$container = $target.parent()
			container = $container.get(0)

			// Cycle through the containers
			while ( ($container.length === 1) && (container !== document.body) && (container !== document) ) {
				// Check Container for scroll differences
				var containerScrollTop, containerScrollLeft
				containerScrollTop = $container.css('overflow-y') !== 'visible' && container.scrollHeight !== container.clientHeight
				containerScrollLeft =  $container.css('overflow-x') !== 'visible' && container.scrollWidth !== container.clientWidth
				if ( containerScrollTop || containerScrollLeft ) {
					// Push the Collection
					collections.push({
						'$container': $container,
						'$target': $target
					})
					// Update the Target
					$target = $container
				}
				// Update the Container
				$container = $container.parent()
				container = $container.get(0)
			}

			// Add the final collection
			collections.push({
				'$container': $('html'),
				// document.body doesn't work in firefox, html works for all
				// internet explorer starts at the beggining
				'$target': $target
			})

			// Adjust the Config
			if ( config.durationMode === 'all' ) {
				config.duration /= collections.length
			}

			// Handle
			ScrollTo.scroll(collections, config)

			// Chain
			return this
		}
	}

	// Apply our extensions to jQuery
	$.ScrollTo = $.ScrollTo || ScrollTo
	$.fn.ScrollTo = $.fn.ScrollTo || ScrollTo.fn

	// Export
	return ScrollTo
}).call(this)