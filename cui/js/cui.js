$(function()
{
	if( $('.cui-css-element-layer input.cui-focusable:focus').length === 0 )
	{
		$('.cui-css-element-layer input.cui-focusable:first').focus()
	}

	$('.cui-css-element-layer input').on("keydown", function(e)
	{
		let c = $('.cui-css-element-layer input.cui-focusable');
		let n = c.length;

		if (e.keyCode === 13)
		{
			e.preventDefault();
			let idx = c.index(this);
			if (idx < n - 1 ) c[idx+1].focus();
			else if( n > 0  ) c[0].focus();
		}
	})
})

$(document).on('keydown',function(event)
{
	let fn = {
		112: 'FN01', 113: 'FN02', 114: 'FN03', 115: 'FN04',
		116: 'FN05', 117: 'FN06', 118: 'FN07', 119: 'FN08',
		120: 'FN09', 121: 'FN10', 122: 'FN11', 123: 'FN12'
	};
	if( fn[event.keyCode] )
	{
		$('button[name=' + fn[event.keyCode]).focus().trigger('click');
		return false;
	}
});

function a_auto_effect()
{
	$('[data-error]').each(function()
	{
		if( $(this).attr('data-error') !== "" )
		{
			$(this).validationEngine('showPrompt', $(this).attr('data-error'), 'error', 'rightBottom:0', true);
			$(this).addClass('error');
		}
	});
}

function a_reload(sel)
{
	let t = $(sel).closest('.wg-form');
	$('.wg-form').each(function()
	{
		if( $(this).attr('id') !== t.attr('id') ) WG6.get('#'+$(this).attr('id'), $(this).attr('data-wg-url'));
	});
}

function a_update(sel)
{
	$(sel).each(function()
	{
		$(this).find('.wg-form').each(function()
		{
			if( $(this).attr('id') !== t.attr('id') ) WG6.get('#'+$(this).attr('id'), $(this).attr('data-wg-url'));
		});
	});
}

function a_start_progress(sel)
{
	$(sel).each(function()
	{
		let ovl = $('<div>').addClass('k-overlay');
		ovl.width($(this).width());
		ovl.height($(this).height());

		let anc = $('<div>').addClass('k-overlay-anchor').prepend(ovl);
		$(this).prepend(anc);
	});
}

WG6.beforeLoad = function(jqs)
{
	let w = $(jqs).width() + 'px', h = $(jqs).height() + 'px';
	let o = $('<div>').css({position:'absolute',width:w,height:h,backgroundColor:'#fff',zIndex:1000,opacity:0.0}).animate({opacity:0.7},700,'easeOutQuint').addClass('k-overlay');
	let c = $('<div>').css({position:'absolute',width:100,height:100,zIndex:1001}).html(
		'<div align="center" class="cssload-fond">' +
		'<div class="cssload-container-general">' +
		'<div class="cssload-internal"><div class="cssload-ballcolor cssload-ball_1"></div></div>' +
		'<div class="cssload-internal"><div class="cssload-ballcolor cssload-ball_2"></div></div>' +
		'<div class="cssload-internal"><div class="cssload-ballcolor cssload-ball_3"></div></div>' +
		'<div class="cssload-internal"><div class="cssload-ballcolor cssload-ball_4"></div></div>' +
		'</div></div>'
	).addClass('k-overlay');

	try
	{
		let top  = $(window).scrollTop() - $(jqs).offset().top + window.innerHeight / 2.0;
	}
	catch (e) {
		return;
	}

	c.css({top:top,left:'50%',transform:'translate(-50%,-50%)'});

	$(jqs).prepend(o);
	$(jqs).prepend(c);
};

WG6.afterLoad = function(jqs)
{
	$(jqs).find('.k-overlay').remove();
};
