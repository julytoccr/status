/**
-----------------------------------------------------------------------------
	dTree 2.0  |  www.destroydrop.com/javascript/tree/
-----------------------------------------------------------------------------
	Copyright (c) 2002 Geir Landr?

	This script can be used freely as long as all copyright messages are
	intact.
-----------------------------------------------------------------------------
* Modified for e-status
*/
// Node object
function Node(type, id, pid, name, url, title, target, isopen, img, imgopen, ischeckbox, classname)
{
	this . type = type;
	this . id = id;
	this . pid = pid;
	this . name = name;
	this . url = url;
	this . title = title;
	this . target = target;
	this . img = img;
	this . imgopen = imgopen;
	this . ischeckbox = ischeckbox;
	this . classname = classname;
	this . _io = isopen || false;
	this . _ls = false;
	this . _hc = false;
	this . _is = false;
}
// Tree object
function dTree(objName)
{
	// Variables
	// ----------------------------------------------------------------------------
	this . arrNodes =[];
	this . arrRecursed =[];
	this . arrIcons =[];
	this . rootNode = -1;
	this . strOutput = '';
	this . selectedNode = null;
	this . instanceName = objName;
	this . imgFolder = 'img/';
	this . target = null;
	this . hasLines = true;
	this . clickSelect = false;
	this . folderLinks = false;
	this . useCookies = true;
	// Functions
	// ----------------------------------------------------------------------------
	// Adds a new node to the node array
	this . add = function (type, id, pid, name, url, title, target, isopen, img, imgopen, ischeckbox, classname)
	{
		this . arrNodes[this . arrNodes . length] = new Node(type, id, pid, name, url, title, target, isopen, img, imgopen, ischeckbox, classname);
	}
	// Outputs the tree to the page
	this . draw = function ()
	{
		if (document . getElementById)
		{
			this . preloadIcons();
			if (this . useCookies)
				this . selectedNode = this . getSelected();
			this . addNode(this . rootNode);
			//alert(this.strOutput);
			document . writeln(this . strOutput);
		}
		else
		{
			document . writeln('Browser not supported.');
		}
	}
	this . openAll = function ()
	{
		this . oAll(true);
	}
	this . closeAll = function ()
	{
		this . oAll(false);
	}
/*	this . mostrarNodoSeleccionado = function ()
	{
		alert("NodoSeleccionado = " + this . selectedNode);
		var valores = document . getElementsByName("CheckTree");
		if (valores . length > 1)
		{
			alert("Solo se puede seleccionar un nodo destino");
		}
		else
		{
			for (i = 0; i < valores . length; i++)
				if (valores(i) . checked)
				{
					alert(valores(i) . value);
					var elNodo = this . obtenerNodo(valores(i) . value);
					if (elNodo != null)
						alert(elNodo . name);
				}
		}
	}
*/

	/**
	 *  Obtiene un nodo a partir de un id
	 */
	this . obtenerNodo = function (id)
	{
		var n=this.obtenerIndiceNodo(id);
		if (n!=null)
		{
			return this.arrNodes[n];
		}
		else
		{
			return null;
		}
	}

	/**
	 * Obtiene el ?ndice de un nodo
	 * @access private
	 */
	this . obtenerIndiceNodo = function (id)
	{
		for (var n = 0; n < this . arrNodes . length; n++)
			if (this . arrNodes[n] . id == id)
				return n;
		return null;
	}

	/**
	 * Abre un nodo a partir de su id.
	 * Abre todos los padres necesarios para su visi?n
	 */
	this . abrirNodo = function (id)
	{
		var n = this.obtenerIndiceNodo(id);
		if ( n !=null)	this. abrirNodoAux(n);
	}

	/**
	 * Abre un nodo a partir de su ?ndice.
	 * Abre todos los padres necesarios para su visi?n
	 *
	 * @access private
	 */
	this . abrirNodoAux = function (n)
	{
		var cn = this.arrNodes[n];
		var pid = cn.pid;
		if (pid != -1) this.abrirNodo(pid);

		if (! cn._io)
		{
			this . nodeOpen(n, cn . _ls);
			cn . _io = true;
			if (this . useCookies)
				this . updateCookie();
		}
	}

	// Obtiene un nodo a partir de un id
	/*
	this . cambiarEstado = function (check)
	{
				var n = 0;
		alert("CambiandoEstado de nodo=" + check.value + " a=" + check.checked);
				for (n=0; n<this.arrNodes.length; n++){
		                      if (this.arrNodes[n].pid == check.value){
		alert("Hijo=" + this.arrNodes[n].name + " con id=" + this.arrNodes[n].id );
			       		  if (document.all("chk_"+ this.arrNodes[n].id)!= null){
		alert(" ANTES   id=" + this.arrNodes[n].id + " check.id=" + document.all("chk_"+ this.arrNodes[n].id).id + " check.checked=" + document.all("chk_"+ this.arrNodes[n].id).checked);
		 	       		     document.all("chk_"+ this.arrNodes[n].id).checked = check.checked;
		alert(" DESPUES id=" + this.arrNodes[n].id + " check.id=" + document.all("chk_"+ this.arrNodes[n].id).id + "check.checked=" + document.all("chk_"+ this.arrNodes[n].id).checked);
		//			     this.cambiarEstadoHijos(this.arrNodes[n],check.checked);

			       		  }
		                      }
				}

	}
	*/

	/*
	this . cambiarEstadoHijos = function (nodo, estado)
	{
		var nn = 0;
		for (nn = 0; nn < this . arrNodes . length; nn++)
		{
			if (this . arrNodes[nn] . pid == nodo . id)
			{
				//alert("Encontrado el hijo=" + this.arrNodes[nn].name + " con id=" + this.arrNodes[nn].id);
				if (document . all("chk_" + this . arrNodes[nn] . id) != null)
				{
					//alert(" ANTES   id=" + this.arrNodes[nn].id + " check.id=" + document.all("chk_"+ this.arrNodes[nn].id).id + " check.checked=" + document.all("chk_"+ this.arrNodes[nn].id).checked);
					document . all("chk_" + this . arrNodes[nn] . id) . checked = estado;
					//alert(" DESPUES id=" + this.arrNodes[nn].id + " check.id=" + document.all("chk_"+ this.arrNodes[nn].id).id + "check.checked=" + document.all("chk_"+ this.arrNodes[nn].id).checked);
					this . cambiarEstadoHijos(this . arrNodes[nn], estado);
				}
			}
		}
	}
	*/


	// Prealoads images that are used in the tree
	this . preloadIcons = function ()
	{
		if (this . hasLines)
		{
			this . arrIcons[0] = new Image();
			this . arrIcons[0] . src = this . imgFolder + 'plus.gif';
			this . arrIcons[1] = new Image();
			this . arrIcons[1] . src = this . imgFolder + 'plusbottom.gif';
			this . arrIcons[2] = new Image();
			this . arrIcons[2] . src = this . imgFolder + 'minus.gif';
			this . arrIcons[3] = new Image();
			this . arrIcons[3] . src = this . imgFolder + 'minusbottom.gif';
		}
		else
		{
			this . arrIcons[0] = new Image();
			this . arrIcons[0] . src = this . imgFolder + 'nolines_plus.gif';
			this . arrIcons[1] = new Image();
			this . arrIcons[1] . src = this . imgFolder + 'nolines_plus.gif';
			this . arrIcons[2] = new Image();
			this . arrIcons[2] . src = this . imgFolder + 'nolines_minus.gif';
			this . arrIcons[3] = new Image();
			this . arrIcons[3] . src = this . imgFolder + 'nolines_minus.gif';
		}
		this . arrIcons[4] = new Image();
		this . arrIcons[4] . src = this . imgFolder + 'folder.gif';
		this . arrIcons[5] = new Image();
		this . arrIcons[5] . src = this . imgFolder + 'folderopen.gif';
	}
	// Recursive function that creates the tree structure
	this . addNode = function (pNode)
	{
		for (var n = 0; n < this . arrNodes . length; n++)
		{
			if (this . arrNodes[n] . pid == pNode)
			{
				var cn = this . arrNodes[n];
				cn . _hc = this . hasChildren(cn);
				cn . _ls = (this . hasLines) ? this . lastSibling(cn) : false;
				if (cn . _hc && !cn . _io && this . useCookies)
					cn . _io = this . isOpen(cn . id);
				if (this . clickSelect && cn . id == this . selectedNode)
				{
					cn . _is = true;
					this . selectedNode = n;
				}
				if (!this . folderLinks && cn . _hc)
					cn . url = null;
				// If the current node is not the root
				if (this . rootNode != cn . pid)
				{
					// Write out line & empty icons
					for (r = 0; r < this . arrRecursed . length; r++)
						this . strOutput += '<img src="' +this . imgFolder + ((this . arrRecursed[r] == 1 && this . hasLines) ? 'line' : 'empty') + '.gif" alt="" border="0" />';
					// Line & empty icons
					 (cn . _ls) ? this . arrRecursed . push(0) : this . arrRecursed . push(1);
					// Write out join icons
					if (cn . _hc)
					{
						this . strOutput += '<a href="javascript: ' +this . instanceName + '.o(' +n + ');">' + '<img id="j' +this . instanceName + n + '" src="' +this . imgFolder;
						if (!this . hasLines)
							this . strOutput += 'nolines_';
						this . strOutput += ((cn . _io) ? ((cn . _ls && this . hasLines) ? 'minusbottom' : 'minus') : ((cn . _ls && this . hasLines) ? 'plusbottom' : 'plus')) + '.gif" alt="" /></a>';
					}
					else
						this . strOutput += '<img src="' +this . imgFolder + ((this . hasLines) ? ((cn . _ls) ? 'joinbottom' : 'join') : 'empty') + '.gif" alt="" border="0" />';
				}
				this . strOutput += '<span id="item_' + cn.id + '">';
				if (cn . ischeckbox)
				{
					this . strOutput += '<input type="checkbox" name="' + cn.id + '" id="chk_' +cn . id + '" value="1" /> ';
				}
				// Start the node link
				if (cn . url)
				{
					this . strOutput += '<a href="' +cn . url + '"';
					if (cn . title)
						this . strOutput += ' title="' +cn . title + '"';
					if (cn . target)
						this . strOutput += ' target="' +cn . target + '"';
					if (this . target && !cn . target)
						this . strOutput += ' target="' +this . target + '"';
					// If hightlight link is on
					if (this . clickSelect)
					{
						if (cn . _hc)
						{
							if (this . folderLinks)
								this . strOutput += ' onclick="javascript: ' +this . instanceName + '.s(' +n + ');"';
						}
						else
						{
							this . strOutput += ' onclick="javascript: ' +this . instanceName + '.s(' +n + ');"';
						}
					}
					this . strOutput += '>';
				}
				if ((!this . folderLinks || !cn . url) && cn . _hc && cn . pid != this . rootNode)
				{
					this . strOutput += '<a href="javascript: ' +this . instanceName + '.o(' +n + ');">';
				}
				// Write out folder & page icons
				this . strOutput += ' <img id="i' +this . instanceName + n + '" src="' +this . imgFolder;
				this . strOutput += (cn . img) ? ((cn . _io) ? cn . imgopen : cn . img) : ((this . rootNode == cn . pid) ? 'base' : (cn . type == 'P') ? 'page' : ((cn . _io) ? 'folderopen' : 'folder')) + '.gif';
				this . strOutput += '" alt=""  border="0" />';
				if (cn. url && ! (!this . folderLinks && cn . _hc))
					this . strOutput += '</a>';

				// Write out span
				if ( cn . classname )
				{
					classname=' ' + cn . classname;
				}
				else
				{
					classname='';
				}
				this . strOutput += '<span id="' + cn.id + '" class="' + ((this . clickSelect) ? ((cn . _is ? 'nodeSel' : 'node')) : 'node') + classname +  '">';
				// Write out node name
				this . strOutput += cn . name;
				this . strOutput += '</span>';
				// Close the link
				if (! cn.url && (!this . folderLinks && cn . _hc))
					this . strOutput += '</a>';

				this . strOutput += '</span>\n'
				this . strOutput += '<br />\n';
				// If node has children write out divs and go deeper
				if (cn . _hc)
				{
					this . strOutput += '<div id="d' +this . instanceName + n + '" style="display:' + ((this . rootNode == cn . pid || cn . _io) ? 'block' : 'none') + ';">\n';
					this . addNode(cn . id);
					this . strOutput += '</div>\n';
				}
				this . arrRecursed . pop();
			}
		}
	}
	// Checks if a node has any children
	this . hasChildren = function (node)
	{
		for (n = 0; n < this . arrNodes . length; n++)
			if (this . arrNodes[n] . pid == node . id)
				return true;
		return false;
	}
	// Checks if a node is the last sibling
	this . lastSibling = function (node)
	{
		var lastId;
		for (n = 0; n < this . arrNodes . length; n++)
			if (this . arrNodes[n] . pid == node . pid)
				lastId = this . arrNodes[n] . id;
		if (lastId == node . id)
			return true;
		return false;
	}
	// Checks if a node id is in a cookie
	this . isOpen = function (id)
	{
		openNodes = this . getCookie('co' +this . instanceName) . split('.');
		for (n = 0; n < openNodes . length; n++)
			if (openNodes[n] == id)
				return true;
		return false;
	}
	// Checks if the node is selected
	this . isSelected = function (id)
	{
		selectedNode = this . getCookie('cs' +this . instanceName);
		if (selectedNode)
		{
			if (id == selectedNode)
			{
				this . selectedNode = id;
				return true }
		}
		return false;
	}
	// Returns the selected node
	this . getSelected = function ()
	{
		selectedNode = this . getCookie('cs' +this . instanceName);
		if (selectedNode)
			return selectedNode;
		return null;
	}
	// Highlights the selected node
	this . s = function (id)
	{
		cn = this . arrNodes[id];
		if (this . selectedNode != id)
		{
			if (this . selectedNode)
			{
				eOldSpan = document . getElementById("s" + this . instanceName + this . selectedNode);
				eOldSpan . className = "node";
			}
			eNewSpan = document . getElementById("s" + this . instanceName + id);
			eNewSpan . className = "nodeSel";
			this . selectedNode = id;
			if (this . useCookies)
				this . setCookie('cs' +this . instanceName, cn . id);
		}
	}
	// Toggle Open or close
	this . o = function (id)
	{
		//alert(this.instanceName);
		//alert(this.arrNodes.length);
		cn = this . arrNodes[id];
		(cn . _io) ? this . nodeClose(id, cn . _ls) : this . nodeOpen(id, cn . _ls);
		cn . _io = !cn . _io;
		if (this . useCookies)
			this . updateCookie();
	}
	// Open or close all nodes
	this . oAll = function (open)
	{
		for (n = 0; n < this . arrNodes . length; n++)
		{
			if (this . arrNodes[n] . _hc && this . arrNodes[n] . pid != this . rootNode)
			{
				if (open)
				{
					this . nodeOpen(n, this . arrNodes[n] . _ls);
					this . arrNodes[n] . _io = true;
				}
				else
				{
					this . nodeClose(n, this . arrNodes[n] . _ls);
					this . arrNodes[n] . _io = false;
				}
			}
		}
		if (this . useCookies)
			this . updateCookie();
	}
	// Open a node
	this . nodeOpen = function (id, bottom)
	{
		eDiv = document . getElementById('d' +this . instanceName + id);
		eJoin = document . getElementById('j' +this . instanceName + id);
		eIcon = document . getElementById('i' +this . instanceName + id);
		eJoin . src = (bottom) ? this . arrIcons[3] . src : this . arrIcons[2] . src;
		if (!this . arrNodes[id] . img)
			eIcon . src = this . arrIcons[5] . src;
		eDiv . style . display = 'block';
	}
	// Close a node
	this . nodeClose = function (id, bottom)
	{
		eDiv = document . getElementById('d' +this . instanceName + id);
		eJoin = document . getElementById('j' +this . instanceName + id);
		eIcon = document . getElementById('i' +this . instanceName + id);
		eJoin . src = (bottom) ? this . arrIcons[1] . src : this . arrIcons[0] . src;
		if (!this . arrNodes[id] . img)
			eIcon . src = this . arrIcons[4] . src;
		eDiv . style . display = 'none';
	}
	// Clears a cookie
	this . clearCookie = function ()
	{
		var now = new Date();
		var yesterday = new Date(now . getTime() - 1000 * 60 * 60 * 24);
		this . setCookie('co' +this . instanceName, 'cookieValue', yesterday);
		this . setCookie('cs' +this . instanceName, 'cookieValue', yesterday);
	}
	// Sets value in a cookie
	this . setCookie = function (cookieName, cookieValue, expires, path, domain, secure)
	{
		document . cookie = escape(cookieName) + '=' +escape(cookieValue) + (expires ? '; expires=' +expires . toGMTString() : '') + (path ? '; path=' +path : '') + (domain ? '; domain=' +domain : '') + (secure ? '; secure' : '');
	}
	// Gets a value from a cookie
	this . getCookie = function (cookieName)
	{
		var cookieValue = '';
		var posName = document . cookie . indexOf(escape(cookieName) + '=');
		if (posName != -1)
		{
			var posValue = posName + (escape(cookieName) + '=') . length;
			var endPos = document . cookie . indexOf(';', posValue);
			if (endPos != -1)
				cookieValue = unescape(document . cookie . substring(posValue, endPos));
			else
				cookieValue = unescape(document . cookie . substring(posValue));
		}
		return (cookieValue);
	}
	// Returns ids of open nodes as a string
	this . updateCookie = function ()
	{
		sReturn = '';
		for (n = 0; n < this . arrNodes . length; n++)
		{
			if (this . arrNodes[n] . _io && this . arrNodes[n] . pid != this . rootNode)
			{
				if (sReturn)
					sReturn += '.';
				sReturn += this . arrNodes[n] . id;
			}
		}
		this . setCookie('co' +this . instanceName, sReturn);
	}
	// tree object ends
}
// Functions used by the dTree object but are not really a part of it
// ------------------------------------------------------------------------------------------------
// Push and pop for arrays is not implemented in Internet Explorer
if (!Array . prototype . push)
{
	Array . prototype . push = function array_push()
	{
		for (var i = 0; i < arguments . length; i++)
			this[this . length] = arguments[i];
		return this . length;
	}
}
if (!Array . prototype . pop)
{
	Array . prototype . pop = function array_pop()
	{
		lastElement = this[this . length - 1];
		this . length = Math . max(this . length - 1, 0);
		return lastElement;
	}
}