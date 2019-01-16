var AWSM_Timeline = (function($) {
    'use strict';
		var AwsmtimelineBlocks = $('.awsm-timeline-block'),
		    AwsmAnimateBlocks = AwsmtimelineBlocks.find('.awsm-timeline-block'),
		    Window = $(window),
		    offset = 0.8,
	    init = function() {
	   		AwsmhideBlocks(AwsmtimelineBlocks, offset);
	   		//on window load show/animate timeline blocks visible in viewport
			AwsmshowTopBlocks(AwsmtimelineBlocks, offset);
	   		//on scolling, show/animate timeline blocks when enter the viewport
			Window.on('scroll', OnScroll);
		},
		AwsmhideBlocks = function (blocks, offset) {
			blocks.each(function(){
				var el = $(this),
					animateBlock = el.find('.awsm-timeline-content'),
					animation = el.data('animation');
				if(animation){
					animateBlock.addClass('is-hidden');
				}
			});
		},
		AwsmshowBlocks = function (blocks, offset) {
			blocks.each(function(){
				var el = $(this),
					animateBlock = el.find('.awsm-timeline-content'),
					animation = el.data('animation');
				if(animation){
					( el.offset().top <= Window.scrollTop()+Window.height()*offset && animateBlock.hasClass('is-hidden') ) && animateBlock.removeClass('is-hidden').addClass('animated '+animation);
				}
			});
		},
		AwsmshowTopBlocks = function (blocks, offset) {
			var i = 0;
			blocks.each(function(){
					var el = $(this),
					animateBlock = el.find('.awsm-timeline-content'),
					animation = el.data('animation');
					if(animation){
						if( Window.scrollTop()+Window.height()*offset > el.offset().top && animateBlock.hasClass('is-hidden') ){
							setTimeout(function(){
								animateBlock.removeClass('is-hidden').addClass('animated '+animation);
							}, i);
						} 
					}
				i+= 200;
			});
		},
		OnScroll = function (){
			(!window.requestAnimationFrame) 
				? setTimeout(function(){ AwsmshowBlocks(AwsmtimelineBlocks, offset); }, 100)
				: window.requestAnimationFrame(function(){ AwsmshowBlocks(AwsmtimelineBlocks, offset); });
		}
		return {
        	init: init
    	};
})(jQuery);

jQuery(AWSM_Timeline.init);