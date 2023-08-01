function get_sortable(hidden_id, item_class)
{
	var items = $$('.' + item_class);
	var order = '';
	for(var i=0; i<items.length; i++)
	{
		order+= items[i].id;
		if(i + 1 < items.length) order += ' ';
	}
	//set the value of a hidden field
	$(hidden_id).value=order;
	return order;
}
