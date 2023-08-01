/**
 * Insertar Variables Estatus
 * Elimando el coloreo porque ten?a problemas
 *
 * Based on Placeholders Plugin
 * @author RosSoft
 */


// Register the related command.
FCKCommands.RegisterCommand( 'Placeholder', new FCKDialogCommand( 'Placeholder', FCKLang.PlaceholderDlgTitle, FCKPlugins.Items['placeholder'].Path + 'fck_placeholder.html', 340, 170 ) ) ;

// Create the "Plaholder" toolbar button.
var oPlaceholderItem = new FCKToolbarButton( 'Placeholder', FCKLang.PlaceholderBtn ) ;
oPlaceholderItem.IconPath = FCKPlugins.Items['placeholder'].Path + 'estatus_variables.gif' ;
FCKToolbarItems.RegisterItem( 'Placeholder', oPlaceholderItem ) ;


// The object used for all Placeholder operations.
var FCKPlaceholders = new Object() ;

// Add a new placeholder at the actual selection.
FCKPlaceholders.Add = function( name )
{
	var oSpan = FCK.CreateElement( 'SPAN' ) ;
	this.SetupSpan( oSpan, name ) ;
}

FCKPlaceholders.EstatusListaVariables = function ()
{
	//var	variables = new Array();
	FCKConfig.ProcessHiddenField();
	var variables=FCKConfig.PageConfig['placeholder_list'];
	eval("variables=" + variables);
	return variables;
}

/**
 * Retorna el fondo que debe tener una variable.
 * Si la variable existe en la lista de variables, amarillo.
 * De lo contrario rojo.
 * @param string nombre Nombre de variable
 * @return string Background
 */
FCKPlaceholders.FondoVariable=function (nombre)
{
	if (this.EstatusListaVariables().indexOf(nombre)!=-1)
	{
		return '#ffff00';
	}
	else
	{
		return '#ffAAAA';
	}
}

// Override the actual configuration values with the values passed throw the
// hidden field "<InstanceName>___Config".
// Modified from _source/internals/fckconfig.js
FCKConfig.ProcessHiddenField = function()
{
	this.PageConfig = new Object() ;

	// Get the hidden field.
	var id=FCK.Name + '___Config' ;
	id=id.replace(']','_');
	id=id.replace(']','_');
	id=id.replace('[','_');
	id=id.replace('[','_');

	var oConfigField = window.parent.document.getElementById(id ) ;
	// Do nothing if the config field was not defined.
	if ( ! oConfigField ) return ;

	var aCouples = oConfigField.value.split('&') ;
	console.log(oConfigField.value);


	for ( var i = 0 ; i < aCouples.length ; i++ )
	{
		if ( aCouples[i].length == 0 )
			continue ;

		var aConfig = aCouples[i].split( '=' ) ;
		var sKey = unescape( aConfig[0] ) ;
		var sVal = unescape( aConfig[1] ) ;

		if ( sKey == 'CustomConfigurationsPath' )	// The Custom Config File path must be loaded immediately.
			FCKConfig[ sKey ] = sVal ;

		else if ( sVal.toLowerCase() == "true" )	// If it is a boolean TRUE.
			this.PageConfig[ sKey ] = true ;

		else if ( sVal.toLowerCase() == "false" )	// If it is a boolean FALSE.
			this.PageConfig[ sKey ] = false ;

		else if ( ! isNaN( sVal ) )					// If it is a number.
			this.PageConfig[ sKey ] = parseInt( sVal ) ;

		else										// In any other case it is a string.
			this.PageConfig[ sKey ] = sVal ;
	}
}


FCKPlaceholders.SetupSpan = function( span, name )
{
	span.innerHTML = '$' + name + '$' ;

	span.style.backgroundColor = this.FondoVariable(name);
	span.style.color = '#000000' ;

	if ( FCKBrowserInfo.IsGecko )
		span.style.cursor = 'default' ;

	span._fckplaceholder = name ;
	span.contentEditable = true ;

	// To avoid it to be resized.
	span.onresizestart = function()
	{
		FCK.EditorWindow.event.returnValue = false ;
		return false ;
	}
}

// On Gecko we must do this trick so the user select all the SPAN when clicking on it.
FCKPlaceholders._SetupClickListener = function()
{
	FCKPlaceholders._ClickListener = function( e )
	{
		if ( e.target.tagName == 'SPAN' && e.target._fckplaceholder )
			FCKSelection.SelectNode( e.target ) ;
	}

	FCK.EditorDocument.addEventListener( 'click', FCKPlaceholders._ClickListener, true ) ;
}

// Open the Placeholder dialog on double click.
FCKPlaceholders.OnDoubleClick = function( span )
{
	if ( span.tagName == 'SPAN' && span._fckplaceholder )
		FCKCommands.GetCommand( 'Placeholder' ).Execute() ;
}

//FCK.RegisterDoubleClickHandler( FCKPlaceholders.OnDoubleClick, 'SPAN' ) ;

// Check if a Placholder name is already in use.
FCKPlaceholders.Exist = function( name )
{
	var aSpans = FCK.EditorDocument.getElementsByTagName( 'SPAN' )

	for ( var i = 0 ; i < aSpans.length ; i++ )
	{
		if ( aSpans[i]._fckplaceholder == name )
			return true ;
	}
}

if ( FCKBrowserInfo.IsIE )
{
	FCKPlaceholders.Redraw = function()
	{
		var aPlaholders = FCK.EditorDocument.body.innerText.match( /\$[^$]+\$/g ) ;
		if ( !aPlaholders )
			return ;

		var oRange = FCK.EditorDocument.body.createTextRange() ;

		for ( var i = 0 ; i < aPlaholders.length ; i++ )
		{
			if ( oRange.findText( aPlaholders[i] ) )
			{
				var sName = aPlaholders[i].match( /\$([^$]*?)\$/ )[1] ;
				var background=this.FondoVariable(sName);
				oRange.pasteHTML( '<span style="color: #000000; background-color: ' + background +'" contenteditable="true" _fckplaceholder="' + sName + '">' + aPlaholders[i] + '</span>' ) ;
			}
		}
	}
}
else
{
	FCKPlaceholders.Redraw = function()
	{
		var oInteractor = FCK.EditorDocument.createTreeWalker( FCK.EditorDocument.body, NodeFilter.SHOW_TEXT, FCKPlaceholders._AcceptNode, true ) ;

		var	aNodes = new Array() ;

		while ( oNode = oInteractor.nextNode() )
		{
			aNodes[ aNodes.length ] = oNode ;
		}

		for ( var n = 0 ; n < aNodes.length ; n++ )
		{
			var aPieces = aNodes[n].nodeValue.split( /(\$[^$]+\$)/g ) ;

			for ( var i = 0 ; i < aPieces.length ; i++ )
			{
				if ( aPieces[i].length > 0 )
				{
					if ( aPieces[i].indexOf( '$' ) == 0 )
					{
						var sName = aPieces[i].match( /\$([^$]*?)\$/ )[1] ;

						var oSpan = FCK.EditorDocument.createElement( 'span' ) ;
						FCKPlaceholders.SetupSpan( oSpan, sName ) ;

						aNodes[n].parentNode.insertBefore( oSpan, aNodes[n] ) ;

						var oSpan = FCK.EditorDocument.createElement( 'span' ) ;
						aNodes[n].parentNode.insertBefore( oSpan, aNodes[n] ) ;
					}
					else
						aNodes[n].parentNode.insertBefore( FCK.EditorDocument.createTextNode( aPieces[i] ) , aNodes[n] ) ;
				}
			}

			aNodes[n].parentNode.removeChild( aNodes[n] ) ;
		}

		FCKPlaceholders._SetupClickListener() ;
	}

	FCKPlaceholders._AcceptNode = function( node )
	{
		if ( /\$[^$]+\$/.test( node.nodeValue ) )
			return NodeFilter.FILTER_ACCEPT ;
		else
			return NodeFilter.FILTER_SKIP ;
	}
}

//FCK.Events.AttachEvent( 'OnAfterSetHTML', FCKPlaceholders.Redraw ) ;

// The "Redraw" method must be called on startup.
//FCKPlaceholders.Redraw() ;

// We must process the SPAN tags to replace then with the real resulting value of the placeholder.
/*FCKXHtml.TagProcessors['span'] = function( node, htmlNode )
{
	if ( htmlNode._fckplaceholder )
		node = FCKXHtml.XML.createTextNode( '$' + htmlNode._fckplaceholder + '$ ' ) ;
	else
		FCKXHtml._AppendChildNodes( node, htmlNode, false ) ;

	return node ;
}*/