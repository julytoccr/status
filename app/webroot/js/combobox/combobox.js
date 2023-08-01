function ComboBox_Init() 
{
    if ( ComboBox_UplevelBrowser() ) {
        for( var i = 0; i < ComboBoxes.length; i++ ) {
            ComboBox_Load( ComboBoxes[i] );
        }
    }
}
function ComboBox_UplevelBrowser() {
    if( typeof( document.getElementById ) == "undefined" ) return false;
    var combo = document.getElementById( ComboBoxes[0] + "_container" );
    if( combo == null || typeof( combo ) == "undefined" ) return false;
    if( typeof( combo.style ) == "undefined" ) return false;
    if( typeof( combo.innerHTML ) == "undefined" ) return false;
    return true;
}
function ComboBox_Load( comboId ) {
    var combo  = document.getElementById( comboId + "_container" );
    var button = document.getElementById( comboId + "_button" );
    var list   = document.getElementById( comboId + "_select");
    var text   = document.getElementById( comboId);
    
    
    combo.List = list;
    combo.Button = button;
    combo.Text = text;
    combo.style.border = "2px inset ButtonFace";
    combo.style.padding = "0px";
    combo.style.margin = "0px";
    
    button.Container = combo;
    button.Toggle = ComboBox_ToggleList;
    button.onclick = button.Toggle;
    button.onmouseover = function(e) { this.Container.List.DisableBlur(e); };
    button.onmouseout = function(e) { this.Container.List.EnableBlur(e); };
    button.style.padding = "0px";
    button.style.margin = "0px";
    button.style.background = "ButtonFace";
    button.style.color = "ButtonText";
    button.style.borderLeft = "1px outset ButtonFace";
    button.style.cursor = "default";
    button.style.fontSize = "8pt";
    button.innerHTML = "\u25BC";
    button.onselectstart = function(e){ return false; };
    
    text.Container = combo;
    text.TypeDown = ComboBox_TextTypeDown;
    text.KeyAccess = ComboBox_TextKeyAccess;
    //ROSSOFT: I don't like this: 
    //text.onkeyup = function(e) { this.KeyAccess(e); this.TypeDown(e); };
    text.style.border = "none";
    text.style.margin = "0px";
    text.style.padding = "0px";
    text.style.width = ( list.offsetWidth ) + "px";
    
    list.Container = combo;
    list.Show = ComboBox_ShowList;
    list.Hide = ComboBox_HideList;
    list.EnableBlur = ComboBox_ListEnableBlur;
    list.DisableBlur = ComboBox_ListDisableBlur;
    list.Select = ComboBox_ListItemSelect;
    list.ClearSelection = ComboBox_ListClearSelection;
    list.KeyAccess = ComboBox_ListKeyAccess;
    list.FireTextChange = ComboBox_ListFireTextChange;
    list.onchange = null;
    list.onclick = function(e){ this.Select(e); this.ClearSelection(); this.FireTextChange(); };
    list.onkeyup = function(e) { this.KeyAccess(e); };
    list.EnableBlur(null);
    list.style.position = "absolute";
    list.style.zIndex = 200;
    list.size = ComboBox_GetListSize( list );
    list.IsShowing = true;
    list.Hide();
	list.selectedIndex=-1;
    
}
function ComboBox_InitEvent( e ) {
    if( typeof( e ) == "undefined" && typeof( window.event ) != "undefined" ) e = window.event;
    if( e == null ) e = new Object();
    return e;
}
function ComboBox_ListClearSelection() {
	if ( typeof( this.Container.Text.createTextRange ) == "undefined" ) return;
    var rNew = this.Container.Text.createTextRange();
    rNew.moveStart('character', this.Container.Text.value.length) ;
    rNew.select();
}
function ComboBox_GetListSize( theList ) {
    ComboBox_EnsureListSize( theList );
    return theList.listSize;
}
function ComboBox_EnsureListSize( theList ) {
    if ( typeof( theList.listSize ) == "undefined" ) {
        if( typeof( theList.getAttribute ) != "undefined" ) {
            if( theList.getAttribute( "listSize" ) != null && theList.getAttribute( "listSize" ) != "" ) {
                theList.listSize = theList.getAttribute( "listSize" );
                return;
            }
        }
        if( theList.options.length > 0 ) {
            theList.listSize = theList.options.length;
            return;
        }
        theList.listSize = 4;
    }
}
function ComboBox_ListKeyAccess(e) { //Make enter/space and escape do the right thing :)
    e = ComboBox_InitEvent( e );
    if( e.keyCode == 13 || e.keyCode == 32 ) {
        this.Select();
        return;
    }
    if( e.keyCode == 27 ) {
        this.Hide();
        this.Container.Text.focus();
        return;
    }
}
function ComboBox_TextKeyAccess(e) { //Make alt+arrow expand the list
	e = ComboBox_InitEvent( e );
	if( e.altKey && (e.keyCode == 38 || e.keyCode == 40) ) {
		this.Container.List.Show();
	}
}
function ComboBox_TextTypeDown(e) { //Make the textbox do a type-down on the list
    e = ComboBox_InitEvent( e );
    var items = this.Container.List.options;
    if( this.value == "" ) return;
    var ctrlKeys = Array( 8, 46, 37, 38, 39, 40, 33, 34, 35, 36, 45, 16, 20 );
    for( var i = 0; i < ctrlKeys.length; i++ ) {
        if( e.keyCode == ctrlKeys[i] ) return;
    }
    for( var i = 0; i < items.length; i++ ) {
        var item = items[i];
        if( item.text.toLowerCase().indexOf( this.value.toLowerCase() ) == 0 ) {
            this.Container.List.selectedIndex = i;
            if ( typeof( this.Container.Text.createTextRange ) != "undefined" ) {
				this.Container.List.Select();
			}
            break;
        }
    }
}
function ComboBox_ListFireTextChange() {
    var textOnChange = this.Container.Text.onchange;
	if ( textOnChange != null && typeof(textOnChange) == "function" ) {
		textOnChange();
	}
}
function ComboBox_ListEnableBlur(e) {
    this.onblur = this.Hide;
}
function ComboBox_ListDisableBlur(e) {
    this.onblur = null;
}
function ComboBox_ListItemSelect(e) {
    if( this.options.length > 0 ) {
        var text = this.Container.Text;
        var oldValue = text.value;
//        var newValue = this.options[ this.selectedIndex ].text;
        var newValue = this.options[ this.selectedIndex ].value;
        //this.selectedIndex=-1;
        text.value = newValue;
        if ( typeof( text.createTextRange ) != "undefined" ) {
            if (newValue != oldValue) {
                var rNew = text.createTextRange();
                rNew.moveStart('character', oldValue.length) ;
                rNew.select();
            }
        }
    }
    this.Hide();
    this.Container.Text.focus();
}
function ComboBox_ToggleList(e) {
    if( this.Container.List.IsShowing == true ) {
        this.Container.List.Hide();
    } else {
        this.Container.List.Show();
    }
}
function ComboBox_ShowList(e) {
    if ( !this.IsShowing && !this.disabled ) {
        this.style.width = ( this.Container.offsetWidth ) + "px";
        this.style.top = ( this.Container.offsetHeight + ComboBox_RecursiveOffsetTop(this.Container,true) ) + "px";
        this.style.left = ( ComboBox_RecursiveOffsetLeft(this.Container,true) ) + "px";
        ComboBox_SetVisibility(this,true);
        this.focus();
        this.IsShowing = true;
    }
}
function ComboBox_HideList(e) {
    if( this.IsShowing ) {
		ComboBox_SetVisibility(this,false);
        this.IsShowing = false;
    }
}
function ComboBox_SetVisibility(theList,isVisible) {
	var isIE = ( typeof( theList.dataSrc ) != "undefined" ); // dataSrc is an IE-only property which is unlikely to be supported elsewhere
	if ( isIE ) {
		if ( isVisible ) {
			theList.style.visibility = "visible";
		} else {
			theList.style.visibility = "hidden";
		}
	} else { 
		if ( isVisible ) {
			theList.style.display = "block";
		} else {
			theList.style.display = "none";
		}
	}
}
function ComboBox_RecursiveOffsetTop(thisObject,isFirst) {
	if(thisObject.offsetParent) {
		if ( thisObject.style.position == "absolute" && !isFirst && typeof(document.designMode) != "undefined" ) {
			return 0;
		}
		return (thisObject.offsetTop + ComboBox_RecursiveOffsetTop(thisObject.offsetParent,false));
	} else {
		return thisObject.offsetTop;
	}
}
function ComboBox_RecursiveOffsetLeft(thisObject,isFirst) {
	if(thisObject.offsetParent) {
		if ( thisObject.style.position == "absolute" && !isFirst && typeof(document.designMode) != "undefined" ) {
			return 0;
		}
		return (thisObject.offsetLeft + ComboBox_RecursiveOffsetLeft(thisObject.offsetParent,false));
	} else {
		return thisObject.offsetLeft;
	}
}
function ComboBox_SimpleAttach(selectElement,textElement) {
	textElement.value = selectElement.options[ selectElement.options.selectedIndex ].text;
	var textOnChange = textElement.onchange;
	if ( textOnChange != null && typeof( textOnChange ) == "function" ) {
		textOnChange();
	}
}