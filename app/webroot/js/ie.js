/** Fix CSS Layout. Only for IE < 7 */

function fix_css_layout_ie6()
{

	t=$('header').offsetHeight;
	l=$('side-bar').offsetWidth;
	w=$('header').offsetWidth - l;

	$$('.main').each(function(_elem)
	{
		if (_elem.offsetWidth>w)
		{
			Element.setStyle(_elem,
					{
						position: 'absolute',
						top: t+'px',
						left: l+'px',
						width: w+'px',
						margin: '0px'
					});
		}
		t+=_elem.offsetHeight;
	});
	t2=$('side-bar').offsetHeight + $('header').offsetHeight;
	if (t<t2) t=t2;

	Element.setStyle('footer',
	{
		position: 'absolute',
		top: t+'px',
		width: $('header').offsetWidth - 15 + 'px'
	});
	setTimeout(fix_css_layout_ie6,2000);
}

Event.observe(window,"load", function(event) { fix_css_layout_ie6();});
