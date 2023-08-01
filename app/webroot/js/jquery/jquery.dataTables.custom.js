$(function() {

if (this == null) return;
$.fn.customDataTable = function(object) {

	var allowEditColumn = object.allowEditColumn;
	var functionButton = object.functionButton == "true";
	var functionURL = object.functionURL;
	var functionClass = object.functionClass;
	var functionOpenAjax = object.functionOpenAjax;
	var functionRefreshOnTrue = object.functionRefreshOnTrue;
	var editable = object.editable == "true";
	var fieldNames = object.fieldNames;
	var fieldTypes = object.fieldTypes;
	var addURL = object.addURL;
	var editURL = object.editURL;
	var deleteURL = object.deleteURL;
	var etiq_error = null;
	var etiq_ajax_error_unexpectedanswer = null;
	var etiq_ajax_error_peticion = null;
	var etiq_ajax_error_filanoborrable = null;
	var etiq_confirm_remove = null;
	var etiq_tooltip_function = null
	var etiq_tooltip_remove = null;
	var etiq_tooltip_edit = null;
	var etiq_tooltip_confirm = null;
	var etiq_tooltip_cancel = null;
	var etiq_tooltip_columns = null;
	var etiq_tooltip_add = null
	if (object.addedTexts) {
		etiq_error = object.addedTexts.error;
		etiq_ajax_error_unexpectedanswer = object.addedTexts.error_ajax_unexpected;
		etiq_ajax_error_peticion = object.addedTexts.error_ajax_petition;
		etiq_ajax_error_filanoborrable = object.addedTexts.error_ajax_nonErasableRow;
		etiq_confirm_remove = object.addedTexts.etiq_confirm_remove;
		if (object.addedTexts.etiq_tooltip_function)
			etiq_tooltip_function = object.addedTexts.etiq_tooltip_function;
		etiq_tooltip_remove = object.addedTexts.etiq_tooltip_remove;
		etiq_tooltip_edit = object.addedTexts.etiq_tooltip_edit;
		etiq_tooltip_confirm = object.addedTexts.etiq_tooltip_confirm;
		etiq_tooltip_cancel = object.addedTexts.etiq_tooltip_cancel;
		etiq_tooltip_columns = object.addedTexts.etiq_tooltip_columns;
		etiq_tooltip_add = object.addedTexts.etiq_tooltip_add;
	}
	var oLanguage = object.tableTexts;
	var aaSorting = object.sortColumns;
	var aoColumnDefs = object.columnProperties;
	
	var oTable = null;
	jQuery.fn.dataTableExt.oPagination.iFullNumbersShowPages = 9;
	
	// funcio que aplica els metodes de cancelar i acceptar canvis, aixi com esborrar fila, cridables externament
	// @rows: el cjt de files a les que aplicar els metodes
    function _applyFunctions(rows) {
    	rows.each(function() {
			// metode per activar la funcio extra
			this.functionButton = function() {
		    	var row = this;
				
				// treiem els botons de confirmacio i cancelacio i posem una imatge de loading
				var firstCell = $(row).find("td:first-child");
				$(firstCell).parent().prepend("<td><div class=\"loading\">&nbsp;</div></td>");
				var restore = $(firstCell).detach();
				firstCell = $(row).find("td:first-child");
		
				var rowid = row.id.substring(3);
				// cridem a funcio per enviar els valors dels camps
				_performAction("POST",
							   functionURL,
							   rowid,
							   function(data_) {
								   // convertim les dades de retorn a objecte JSON
								   data = $.parseJSON(data_);
									
								   var counter = 0;
								   
								   // hi ha hagut algun error -> la funcio acaba aqui
								   if (data.result != 0) {
									   delete data.result;
										
									   // mostrem les dades que ha retornat la vista
									   $.each(data, function(i,item){
										   alert(etiq_error + " -- " + i + ": " + item);
										   ++counter;
									   });
									   
									   // si la vista no ha tornat dades, llencem excepcio
									   if (counter == 0) throw "error";

									   $(firstCell).parent().prepend(restore);
									   $(firstCell).detach();

									   return;
								   }
								   
								   delete data.result;
								   $.each(data, function(i,item){
									   alert(i + ": " + item);
									   ++counter;
								   });
								   
								   if (counter == 0) alert("Done");
								   
								   $(firstCell).parent().prepend(restore);
								   $(firstCell).detach();
								   
								   location.reload();
							   },
							   function() {
								   // ha passat algun error

								   $(firstCell).parent().prepend(restore);
								   $(firstCell).detach();
							   },
							   null);
		    };
			
    		// metode per convertir les cel·les de la fila en camps editables
    		this.editRow = function(newRow) {
    			if (oTable.fnIsOpen(this)) {
			        oTable.fnClose(this);
			    }
    		
		    	var row = this;
		
			    var jqTds = $('>td:not(:first-child)', row);
			    
				var counter = 0;
				jqTds.each(function() {
					if (typeof allowEditColumn == 'undefined' || allowEditColumn == null || allowEditColumn[counter]) {
						$(this).css("padding", "2px 18px 2px 10px");
						var html = $(this).html();
						if (fieldTypes != null) {
							if (typeof fieldTypes[counter] == 'object') {
								var selectInput = "<select>";
								var val = _getFinalVal(this);
								
								$.each(fieldTypes[counter], function(i,item){
									if (item[0] == val || item[1] == val) {
										selectInput += "<option value=\"" + item[0] + "\" selected>" + item[1] + "</option>";
									}
									else {
										selectInput += "<option value=\"" + item[0] + "\">" + item[1] + "</option>";
									}
							    });
								selectInput += "</select>";
								
								$(this).html(selectInput);
							}
							else if (fieldTypes[counter] == "boolean") {
								var val = _getFinalVal(this);
								if (val == 1 || val == "1") {
									$(this).html("<input type=\"checkbox\" checked />");
								}
								else {
									$(this).html("<input type=\"checkbox\" />");
								}
							}
							else if (fieldTypes[counter] == "text") {
								$(this).html("<input type=\"text\" value=\"" + _getFinalVal(this) + "\" style=\"font-weight: normal; width: 100%;\" />");
							}
						}
						
						$(this).attr("data-original", html);
						$(this).keyup(function(e) {
							if(e.which == 13) {
								row.confirmChanges(newRow);
							}
						});
						$(this).keydown(function(e) {
							if (e.which == 27) {
								if (newRow) row.deleteRow(false);
								else row.cancelChanges();
							}
						});
					}
					++counter;
				});
			    
			    var firstCell = $(row).find("td:first-child");
		        $(firstCell).html("<div class=\"confirm\" title=\"" + etiq_tooltip_confirm + "\">&nbsp;</div><div class=\"cancel\" title=\"" + etiq_tooltip_cancel + "\">&nbsp;</div>")
		                                      .find(".confirm").click(function() { row.confirmChanges(newRow); })
		                                      .parent().find(".cancel").click(function() {
		                                    	  if (newRow) {
		                                    	  	  row.deleteRow(false);
		                                    	  }
		                                    	  else {
		                                    	  	  row.cancelChanges();
		                                    	  }
		                                      });
		                                      
		        $(firstCell).find("div").tipTip({ "delay": 100, "defaultPosition": "top", "fadeIn": 100, "fadeOut": 100 });
		    };
    	
    		// metode que cancela els canvis de la fila
    		this.cancelChanges = function() {
	    		var row = this;
	    		var cells = $('>td:not(:first-child)', row);
	    	
	    		// a cada cel·la li restaurem el contingut original
				var counter = 0;
				cells.each(function() {
					var cell = this;
					
					// restaurem el contingut original sempre que la cel·la fos editable
					if (typeof allowEditColumn == 'undefined' || allowEditColumn == null || allowEditColumn[counter]) {
						$(cell).html($(cell).attr("data-original"));
						
						// retirem listeners de teclat i restaurem estil original
						$(cell).attr("data-original", "")
						       .unbind("keyup")
						       .unbind("keydown")
						       .css("padding", "3px 18px 3px 10px");
					}
					++counter;
				});
				
				$("#tiptip_holder").css("display", "none");
				
				// coloquem les icones a la primera cel·la
				_initializeRowControls(row);
			};
			
			// metode que accepta els canvis d'una fila existent o nova
			// @newRow: boolea que indica si es tracta d'una insercio (true) o una modificacio (false)
			this.confirmChanges = function(newRow) {
				var row = this;
				var cells = $('>td:not(:first-child)', row);
				
				// agafem el id de la fila si es una edicio, o deixem id buit si es nova fila
			    var rowid = ((newRow) ? null : row.id.substring(3));
			    // determinem la URL a la que enviar les dades
			    var url = ((newRow) ? addURL : editURL);
			    
			    // agafem el valor de cada camp
				var data = "{ \"" + fieldNames[0] + "\": \"" + rowid + "\"";
				var counter = 1;
				cells.each(function() {
					var cell = this;
				
					// recollim el valor i el guardem
					var input = $($(cell).find(":first-child").get(0));
					if (typeof fieldTypes[counter - 1] == 'object' || fieldTypes[counter - 1] == "text") {
						data = data + ", \"" + fieldNames[counter] + "\": \"" + input.val() + "\"";
					}
					else if (fieldTypes[counter - 1] == "boolean") {
						var val = input.is(':checked');
						data = data + ", \"" + fieldNames[counter] + "\": \"" + ((val == true) ? "1" : "0") + "\"";
					}
					
					// deshabilitem el camp
					$(input).prop('disabled', true);
					
					++counter;
				});
				data = data + "}";
				
				$("#tiptip_holder").css("display", "none");
				
				// treiem els botons de confirmacio i cancelacio i posem una imatge de loading
				var firstCell = $(row).find("td:first-child");
				$(firstCell).parent().prepend("<td><div class=\"loading\">&nbsp;</div></td>");
				var restore = $(firstCell).detach();
				firstCell = $(row).find("td:first-child");
				
				// convertim els valors dels camps a un objecte JSON
				var jsondata = $.parseJSON(data);
				
				// cridem a funcio per enviar els valors dels camps
				_performAction("POST",
							   url,
							   rowid,
							   function(data_) {
								   // convertim les dades de retorn a objecte JSON
								   data = $.parseJSON(data_);
									
								   // hi ha hagut algun error -> la funcio acaba aqui
								   if (data.result != 0) {
									   delete data.result;
										
									   // mostrem les dades que ha retornat la vista
									   var counter = 0;
									   $.each(data, function(i,item){
										   alert(etiq_error + " -- " + i + ": " + item);
										   ++counter;
									   });
									   
									   // si la vista no ha tornat dades, llencem excepcio
									   if (counter == 0) throw "error";
									   
									   // restaurem els camps d'edicio
									   _restoreInputs(cells, firstCell, restore);
									   return;
								   }
									
								   // a partir d'aqui, la modificacio o insercio ha tingut exit
																		
								   // la fila era insercio, no edicio
								   if (newRow) {
									   rowid = data.id;
										
									   // afegim el id rebut de la vista a la fila
									   $(row).attr("id", "row" + rowid);
								   }
									
								   // restauracio del contingut original, amb els nous valors
								   counter = 0;
								   cells.each(function() {
									   var cell = this;
								   
									   // nomes si la cel·la era editable
									   if (typeof allowEditColumn == 'undefined' || allowEditColumn == null || allowEditColumn[counter]) {
										   // agafem el nou valor
										   var input = $($(cell).find(":first-child").get(0));
											
										   // agafem el contingut original
										   $(cell).html($(cell).attr("data-original"));
											
										   // substituim el valor anterior pel nou
										   if (typeof fieldTypes[counter] == 'object' || fieldTypes[counter] == "text") {
											   _setFinalVal($(cell), input.val());
											   
											   // actualitzem el motor de la taula perque ordeni correctament
											   oTable.fnUpdate( input.val(), row, counter + 1, false );
										   }
										   else if (fieldTypes[counter] == "boolean") {
											   var val = input.is(':checked');
											   val = (val == true) ? "1" : "0";
											   _setFinalVal($(cell), val);
											   
											   // actualitzem el motor de la taula perque ordeni correctament
											   oTable.fnUpdate( val, row, counter + 1, false );
										   }
											
										   // retirem listeners de teclat i restaurem estil original
										   $(cell).attr("data-original", "")
												  .unbind("keyup")
												  .unbind("keydown")
												  .css("padding", "3px 18px 3px 10px");
									   }
									   ++counter;
								   });
									
								   // posem les icones d'edicio corresponents a la primera cel·la
								   _initializeRowControls(row);
									
								   // redibuixem la taula, per si cal reordenar amb els nous valors
								   oTable.fnDraw();
							   },
							   function() {
							       // ha passat algun error, restaurem les cel·les
								   _restoreInputs(cells, firstCell, restore);
							   },
							   jsondata);
			}
			
			// metode per esborrar la fila
			this.deleteRow = function(send) {
				var row = this;
				
				if (typeof send !== 'undefined' && !send) {
					oTable.fnDeleteRow(row);
				}
			    else if (oTable.fnIsOpen(row)) {
			        oTable.fnClose(row);
			    }
			    else {
			        oTable.fnOpen(row, etiq_confirm_remove, "confirmRemove");
			        
			        $(row).next().click(function() {
			        	oTable.fnClose(row);
			        	
			        	var firstCell = $(row).find("td:first-child");
						$(firstCell).parent().prepend("<td><div class=\"loading\">&nbsp;</div></td>");
						var restore = $(firstCell).detach();
						firstCell = $(row).find("td:first-child");
			        	

						// es guardara la destruccio de la fila
						_performAction("GET", deleteURL, row.id.substring(3), function(data_) {
							data = $.parseJSON(data_);
						
							// hi ha hagut algun error -> la funcio acaba aqui
							if (data.result != 0) {
								delete data.result;
								
								alert(etiq_ajax_error_filanoborrable);
								
								$.each(data, function(i,item){
									alert(i + ": " + item);
									++counter;
								});
								
								$(firstCell).parent().prepend(restore);
								$(firstCell).detach();
								
								return;
							}
							
							// a partir d'aqui, la modificacio o insercio ha tingut exit
							
							// esborrem la fila de la taula		    
							oTable.fnDeleteRow(row);
						},
						function() {
							$(firstCell).parent().prepend(restore);
							$(firstCell).detach();
						});
			        });
			    }
		    }
    	});
    }
    // adjunta les funcions de les files cridables externament a cada fila
	_applyFunctions($(this).find("tbody tr"));
	
	// metode per afegir una fila, cridable externament
	$.fn.dataTableAddRow = function() {
		// agafem una fila qualsevol
	    var jqTds = $(this).find("thead:first-child td");
	    
	    // creem un array buit amb tantes posicions com cel·les
		var counter = 0;
		var array = new Array();
		array[0] = "";
		jqTds.each(function() {
			array[counter + 1] = "";
			++counter;
		});
    
    	// afegim la fila nova a la taula
	    var aiNew = oTable.fnAddData( array );
	    
	    // recollim la fila recent creada a la taula
	    var row = oTable.fnGetNodes(aiNew[0]);
	    
	    // li apliquem les funcions cridables externament
	    _applyFunctions($(row));
	    
	    row.editRow(true);
    }
	
	// definim la funcio que envia les dades
    function _performAction(method, url, id, success, error, data) {
    	var url = ((id != null) ? url + "/" + id : url);
		$.ajax({ url: url,
				 type: method,
				 data: data,
				 success: function(data) {
				     try {
					  	 success(data);
					 }
					 catch (e) {
					     alert(etiq_ajax_error_unexpectedanswer + ": " + data);
					      
					     if (typeof error !== 'undefined' && error != null) error();
					 }
				 },
				 error: function() {
				  	 alert(etiq_ajax_error_peticion);
				  	  
				  	 if (typeof error !== 'undefined' && error != null) error();
				 }
		});
    }
    
    // restaura les caselles per modificar el valor de les cel·les i coloca els controls a la primera casella
    function _restoreInputs(rows, firstCell, controls) {
    	rows.each(function() {
			var input = $($(this).find(":first-child").get(0));
			$(input).prop('disabled', false);
		});
		$(firstCell).parent().prepend(controls);
		$(firstCell).detach();
	}

	// coloca els controls de les files a la primera cel·la de cada fila
	function _initializeRowControls(rows) {
		$(rows).each(function() {
			var row = this;
			
			// troba la primera cel·la de la fila
			firstCell = $(row).find("td:first-child");
		
			var html = "";
			if (functionButton) {
				html = "<div class=\"" + functionClass + "\" title=\"" + etiq_tooltip_function + "\">&nbsp;</div>";
			}
			if (editable) {
				// crea els botons de edit i delete
				if (functionButton) {
					html += "&nbsp;";
				}
				html += "<div class=\"delete\" title=\"" + etiq_tooltip_remove + "\">&nbsp;</div><div class=\"edit\" title=\"" + etiq_tooltip_edit + "\">&nbsp;</div>";
			}
			$(firstCell).html(html);
			
			$(firstCell).find("div").tipTip({ "delay": 100, "defaultPosition": "top", "fadeIn": 100, "fadeOut": 100 });
			
			// especifica les accions en fer click a edit i delete
			$(firstCell).find("." + functionClass).click(function(e) {
				e.preventDefault();
				
				$("#tiptip_holder").css("display", "none");
				
				// troba la fila sencera
				var row = $(this).parent().parent().get(0);
				
				if (functionOpenAjax) {
					//crida la funcio d'edicio
					row.functionButton();
				}
				else {
					var id = row.id.substring(3);
					document.location.href = functionURL + ((id != null) ? "/" + id : "");
				}
			});
			$(firstCell).find(".edit").click(function(e) {
				e.preventDefault();
				
				$("#tiptip_holder").css("display", "none");

				// troba la fila sencera i crida la funcio d'edicio
				var row = $(this).parent().parent().get(0);
				row.editRow(false);
			});
			$(firstCell).find(".delete").click(function(e) {
				e.preventDefault();

				// troba la fila sencera i crida la funcio d'esborrat
				var row = $(this).parent().parent().get(0);
				row.deleteRow(true);
			});
		});
	};
	// si la taula es editable, es coloquen els controls a totes les files
	if (editable || functionButton) _initializeRowControls($(this).find("tbody tr"));
	
	// funcio auxiliar, va recursivament buscant per tots els primers fills fins que troba un valor final i el retorna
	function _getFinalVal(element) {
		if ($(element).children().size() == 0) return $(element).html();
		else return _getFinalVal($(element).children().get(0));
	}
	
	// funcio auxiliar, va recursivament buscant per tots els primers fills fins que troba un valor final i el substitueix
	function _setFinalVal(element, val) {
		if ($(element).children().size() == 0) $(element).html(val);
		else _setFinalVal($(element).children().get(0), val);
	}
	
	oTable = $(".datatable").dataTable( { "sDom": "RC<\"clear\">lfrtip",
					      "oColVis": { "aiExclude": [ "0" ] },
					      "bSortCellsTop": "true",
					      "bProcessing": "true",
					      "aLengthMenu": [["5", "10", "15", "20", "40", "50", "75", "100", "-1"], ["5", "10", "15", "20", "40", "50", "75", "100", "-"]],
					      "iDisplayLength": 15,
					      "sPaginationType": 'full_numbers',
					      "oLanguage": oLanguage,
					      "aaSorting": aaSorting,
					      "aoColumnDefs": aoColumnDefs
					      } );
	
    $(this).find("thead input").keyup(function () {
        // Filter on the column (the index) of this element 
        oTable.fnFilter( this.value, $("thead input").index(this) );
    });
     
    // Support functions to provide a little bit of 'user friendlyness' to the textboxes in the footer
    
    // recorda el text inicials de les caselles de cerca de cada columna
    var asInitVals = new Array();
    $(this).find("thead input").each(function (i) {
        asInitVals[i] = this.value;
        $(this).data("search_index", i);
    });
    
    // inicialitza les caselles de cerca de cada columna
    $(this).find("thead input").focus(function () {
        if (this.className == "search_init") {
            this.className = "";
            this.value = "";
        }
    });
    
    // restaura el text inicial de les caselles de cerca de cada columna, en cas que hagin buidat la cerca
    $(this).find("thead input").blur(function (i) {
        if (this.value == "") {
            this.className = "search_init";
            this.value = asInitVals[$(this).data("search_index")];
        }
    });

	// recoloca el botó per mostrar/amagar columnes
	var column_edit = $(this).parent().find(".TableTools");
	if (editable || functionButton) {
		// si la taula es editable, es posa a la segona fila de la capsalera, primera cel·la
		column_edit.detach();
		if (editable) {
			var addrow = $(this).find(".addrow");
		
			$(addrow).attr("title", etiq_tooltip_add).tipTip({ "delay": 100, "defaultPosition": "top", "fadeIn": 100, "fadeOut": 100 });
		
			$(addrow).html("&nbsp;").click(function(e) {
				e.preventDefault();
				
				oTable.dataTableAddRow();
			});
		}
		$(this).find(".editcolumns").html(column_edit).append("<input type=\"hidden\" class=\"search_init\" name=\"x\" value=\"x\" />");
		column_edit.find("span").html("&nbsp;");
	}
	else {
		// si la taula no es editable, es posa sobre la taula, a l'esquerra de tot
		column_edit.detach();
		column_edit.insertBefore($(this).parent().find(".dataTables_length"));
		column_edit.find("span").html("&nbsp");
	}
	$(column_edit).attr("title", etiq_tooltip_columns).tipTip({ "delay": 100, "defaultPosition": "top", "fadeIn": 100, "fadeOut": 100 });
	
	// Canvia el tipus de l'element de span a div
	var attrs = { };
	$.each(column_edit.find("span")[0].attributes, function(idx, attr) {
	    attrs[attr.nodeName] = attr.nodeValue;
	});
	column_edit.find("span").replaceWith(function () {
	    return $("<div>", attrs).append($(this).contents());
	});
}

});
