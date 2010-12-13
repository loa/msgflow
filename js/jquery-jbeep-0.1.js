/* Made by Loa */
(function($) {
	$.jBeep = {
		defaults: {
			path: "/extensions/msgflow/sound/beep_mult.ogg",
			audioElement: false,
			active: true
		}
	};

	$.fn.extend({
		jBeep:function(config) {
			var config = $.extend({}, $.jBeep.defaults, config);
			
			var audioElement = document.createElement('audio');
			
			// Check if browser supports audio tag
			if(typeof audioElement.load == 'function') {
				// Load audo and play
				//console.debug("JBeep: Beep.");
				audioElement.setAttribute('src', config.path);
				audioElement.load();
				audioElement.play();
			} else {
				// Browser doesn't support audio, deactivate the plugin
				//console.error("JBeep: This browser do not support audio tag.");
			}

			//return the jquery object for chaining
			return this;
		}
	});

})(jQuery);