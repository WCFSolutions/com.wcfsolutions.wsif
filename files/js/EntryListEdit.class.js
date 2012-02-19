/**
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
var EntryListEdit = Class.create({
	/**
	 * Inits EntryListEdit.
	 */
	initialize: function(data, prefixData, count) {
		this.data = data;
		this.prefixData = prefixData;
		this.count = count;
		this.options = Object.extend({
			page:			'',
			url:			'',
			categoryID:		0,
			entryID:		0,
			enableRecycleBin:	true
		}, arguments[3] || { });
		
		// get parent object
		this.parentObject = new InlineListEdit('entry', this);
	},
	
	/**
	 * Initialises special entry options.
	 */
	initItem: function(id) {
		var entry = this.data.get(id);
		// init subject edit
		if (permissions['canEditEntry']) {
			var entrySubjectDiv = $('entryTitle'+id);
			if (entrySubjectDiv) {
				entrySubjectDiv.ondblclick = function(name, event) {
					if (!event) event = window.event;
					if (Event.element(event).parentNode.getAttribute('id') == 'entryPrefix'+name) return;
					this.startTitleEdit(name);
				}.bind(this, id);
			}
		}
			
		// init prefix edit
		if (permissions['canEditEntry'] && this.prefixData.keys().length > 0) {
			var entryPrefixSpan = $('entryPrefix'+id);
			if (entryPrefixSpan) {
				entryPrefixSpan.ondblclick = function(name) { this.startPrefixEdit(name); }.bind(this, id);
			}
		}
	},	
	
	/**
	 * Show the status of an entry.
	 */
	showStatus: function(id) {
		var entry = this.data.get(id);
		
		// get row
		var row = $('entryRow'+id);
		
		// update css class
		if (row) {
			// remove all classes
			row.removeClassName('marked');			
			row.removeClassName('disabled');
			row.removeClassName('deleted');
			
			// disabled
			if (entry.isDisabled) {
				row.addClassName('disabled');
			}
			
			// deleted
			if (entry.isDeleted) {
				row.addClassName('deleted');
			}
			
			// marked
			if (entry.isMarked) {
				row.addClassName('marked');
			}
		}
		
		// update icon
		var icon = $('entryEdit'+id);
		if (icon && icon.src != undefined) {
			// deleted
			if (entry.isDeleted) {
				icon.src = icon.src.replace(/[a-z0-9-_]*?(?=(?:Options)?(?:S|M|L|XL)\.png$)/i, 'entryTrash');
			}
			else {
				icon.src = icon.src.replace(/entryTrash/i, 'entry');
			}
		}
	},
	
	/**
	 * Saves the marked status.
	 */
	saveMarkedStatus: function(data) {
		new Ajax.Request('index.php?action=EntryMark&t='+SECURITY_TOKEN+SID_ARG_2ND, {
			method: 'post',
			parameters: data
		});
	},
	
	/**
	 * Returns a list of the edit options for the edit menu.
	 */
	getEditOptions: function(id) {
		var options = new Array();
		var i = 0;
		var entry = this.data.get(id);

		// edit title
		if (permissions['canEditEntry']) {
			options[i] = new Object();
			options[i]['function'] = 'entryListEdit.startTitleEdit('+id+');';
			options[i]['text'] = language['wsif.category.entries.editTitle'];
			i++;
		}
				
		// edit prefix
		if (permissions['canEditEntry'] && this.prefixData.keys().length > 0 && $('entryPrefix'+id)) {
			options[i] = new Object();
			options[i]['function'] = 'entryListEdit.startPrefixEdit('+id+');';
			options[i]['text'] = language['wsif.category.entries.editPrefix'];
			i++;
		}
				
		// enable / disable
		if (permissions['canEnableEntry']) {
			if (entry.isDisabled == 1) {
				options[i] = new Object();
				options[i]['function'] = 'entryListEdit.enable('+id+');';
				options[i]['text'] = language['wsif.category.entries.enable'];
				i++;
			}
			else if (entry.isDeleted == 0) {
				options[i] = new Object();
				options[i]['function'] = 'entryListEdit.disable('+id+');';
				options[i]['text'] = language['wsif.category.entries.disable'];
				i++;
			}
		}
			
		// delete
		if (permissions['canDeleteEntry'] && (permissions['canDeleteEntryCompletely'] || (entry.isDeleted == 0 && this.options.enableRecycleBin))) {
			options[i] = new Object();
			options[i]['function'] = 'entryListEdit.remove('+id+');';
			options[i]['text'] = (entry.isDeleted == 0 ? language['wcf.global.button.delete'] : language['wcf.global.button.deleteCompletely']);
			i++;
		}
				
		// recover
		if (entry.isDeleted == 1 && permissions['canDeleteEntryCompletely']) {
			options[i] = new Object();
			options[i]['function'] = 'entryListEdit.recover('+id+');';
			options[i]['text'] = language['wsif.category.entries.recover'];
			i++;
		}
				
		// marked status
		if (permissions['canMarkEntry']) {
			var markedStatus = entry ? entry.isMarked : false;
			options[i] = new Object();
			options[i]['function'] = 'entryListEdit.parentObject.markItem(' + (markedStatus ? 'false' : 'true') + ', '+id+');';
			options[i]['text'] = markedStatus ? language['wcf.global.button.unmark'] : language['wcf.global.button.mark'];
			i++;
		}
				
		return options;
	},

	/**
	 * Returns a list of the edit options for the edit marked menu.
	 */
	getEditMarkedOptions: function() {
		var options = new Array();
		var i = 0;
		
		if (this.options.page == 'category') {
			// move
			if (permissions['canMoveEntry']) {
				options[i] = new Object();
				options[i]['function'] = "entryListEdit.move('move');";
				options[i]['text'] = language['wsif.category.entries.move'];
				i++;
			}
		}
		
		// delete
		if (permissions['canDeleteEntry'] && (permissions['canDeleteEntryCompletely'] || this.options.enableRecycleBin)) {
			options[i] = new Object();
			options[i]['function'] = 'entryListEdit.removeAll();';
			options[i]['text'] = language['wcf.global.button.delete'];
			i++;
		}
		
		// recover
		if (this.options.enableRecycleBin && permissions['canDeleteEntryCompletely']) {
			options[i] = new Object();
			options[i]['function'] = 'entryListEdit.recoverAll();';
			options[i]['text'] = language['wsif.category.entries.recover'];
			i++;
		}
		
		// unmark all
		options[i] = new Object();
		options[i]['function'] = 'entryListEdit.unmarkAll();';
		options[i]['text'] = language['wcf.global.button.unmark'];
		i++;
		
		// show marked
		options[i] = new Object();
		options[i]['function'] = 'document.location.href = fixURL("index.php?page=ModerationMarkedEntries'+SID_ARG_2ND+'")';
		options[i]['text'] = language['wsif.category.entries.showMarked'];
		i++;
		
		return options;
	},
	
	/**
	 * Returns the title of the edit marked menu.
	 */
	getMarkedTitle: function() {
		return eval(language['wsif.category.entries.markedEntries']);
	},
	
	/**
	 * Moves this entry.
	 */
	move: function(action) {
		document.location.href = fixURL('index.php?action=EntryMoveMarked&categoryID='+this.options.categoryID+'&url='+encodeURIComponent(this.options.url)+'&t='+SECURITY_TOKEN+SID_ARG_2ND);
	},
	
	/**
	 * Deletes this entry.
	 */
	remove: function(id) {
		var entry = this.data.get(id);
		if (entry.isDeleted == 0 && this.options.enableRecycleBin) {
			var promptResult = prompt(language['wsif.category.entries.delete.reason']);
			if (typeof(promptResult) != 'object' && typeof(promptResult) != 'undefined') {
				if (permissions['canViewDeletedEntry']) {
					new Ajax.Request('index.php?action=EntryTrash&entryID='+id+'&t='+SECURITY_TOKEN+SID_ARG_2ND, {
						method: 'post',
						parameters: {
							reason: promptResult
						},
						onSuccess: function() {
							entry.isDeleted = 1;
							this.showStatus(id);
							var entryRow = $('entryRow'+id);
							if (entryRow) {
								entryRow.down('.editNote').insert('<p class="deleteNote smallFont">'+promptResult.escapeHTML()+'</p>');
							}
						}.bind(this)
					});
				}
				else {
					document.location.href = fixURL('index.php?action=EntryTrash&entryID='+id+'&reason='+encodeURIComponent(promptResult)+'&url='+encodeURIComponent(this.options.url)+'&t='+SECURITY_TOKEN+SID_ARG_2ND);
				}
			}
		}
		else {
			if (confirm((entry.isDeleted == 0 ? language['wsif.category.entries.delete.sure'] : language['wsif.category.entries.deleteCompletely.sure']))) {
				document.location.href = fixURL('index.php?action=EntryDelete&entryID='+id+'&url='+encodeURIComponent(this.options.url)+'&t='+SECURITY_TOKEN+SID_ARG_2ND);
			}
		}
	},
	
	/**
	 * Deletes all marked entries.
	 */
	removeAll: function() {
		if (this.options.enableRecycleBin) {
			var promptResult = prompt(language['wsif.category.entries.deleteMarked.reason']);
			if (typeof(promptResult) != 'object' && typeof(promptResult) != 'undefined') {
				document.location.href = fixURL('index.php?action=EntryDeleteMarked&reason='+encodeURIComponent(promptResult)+'&url='+encodeURIComponent(this.options.url)+'&t='+SECURITY_TOKEN+SID_ARG_2ND);
			}
		}
		else if (confirm(language['wsif.category.entries.deleteMarked.sure'])) {
			document.location.href = fixURL('index.php?action=EntryDeleteMarked&url='+encodeURIComponent(this.options.url)+'&t='+SECURITY_TOKEN+SID_ARG_2ND);
		}
	},
	
	/**
	 * Recovers all marked entries.
	 */
	recoverAll: function(id) {
		document.location.href = fixURL('index.php?action=EntryRecoverMarked&url='+encodeURIComponent(this.options.url)+'&t='+SECURITY_TOKEN+SID_ARG_2ND);
	},
	
	/**
	 * Unmarkes all marked entries.
	 */
	unmarkAll: function() {
		new Ajax.Request('index.php?action=EntryUnmarkAll&t='+SECURITY_TOKEN+SID_ARG_2ND, {
			method: 'get'
		});
		
		// checkboxes
		this.count = 0;
		var entryIDArray = this.data.keys();
		for (var i = 0; i < entryIDArray.length; i++) {
			var id = entryIDArray[i];
			var entry = this.data.get(id);
		
			entry.isMarked = 0;
			var checkbox = $('entryMark'+id);
			if (checkbox) {
				checkbox.checked = false;
			}
			
			this.showStatus(id);
		}
		
		// mark all checkboxes
		this.parentObject.checkMarkAll(false);
		
		// edit marked menu
		this.parentObject.showMarked();
	},

	/**
	 * Recovers an entry.
	 */
	recover: function(id) {
		var entry = this.data.get(id);
		if (entry.isDeleted == 1) {
			new Ajax.Request('index.php?action=EntryRecover&entryID='+id+'&t='+SECURITY_TOKEN+SID_ARG_2ND, {
				method: 'get',
				onSuccess: function() {
					entry.isDeleted = 0;
					this.showStatus(id);
					var entryRow = $('entryRow'+id);
					if (entryRow) {
						$('entryRow'+id).down('.deleteNote').remove();
					}
				}.bind(this)
			});
		}
	},
	
	/**
	 * Enables an entry.
	 */
	enable: function(id) {
		var entry = this.data.get(id);
		if (entry.isDisabled == 1) {
			new Ajax.Request('index.php?action=EntryEnable&entryID='+id+'&t='+SECURITY_TOKEN+SID_ARG_2ND, {
				method: 'get',
				onSuccess: function() {
					entry.isDisabled = 0;
					this.showStatus(id);
				}.bind(this)
			});
		}
	},

	/**
	 * Disables an entry.
	 */
	disable: function(id) {
		var entry = this.data.get(id);
		if (entry.isDisabled == 0 && entry.isDeleted == 0) {
			new Ajax.Request('index.php?action=EntryDisable&entryID='+id+'&t='+SECURITY_TOKEN+SID_ARG_2ND, {
				method: 'get',
				onSuccess: function() {
					entry.isDisabled = 1;
					this.showStatus(id);
				}.bind(this)
			});
		}
	},
	
	/**
	 * Starts the editing of an entry title.
	 */
	startTitleEdit: function(id) {
		if ($('entryTitleInput'+id)) return;
		var entrySubjectDiv = $('entryTitle'+id);
		if (entrySubjectDiv) {
			// get value and hide title
			var value = '';
			var title = entrySubjectDiv.select('a')[0];
			if (title) {
				title.addClassName('hidden');

				// IE, Opera, Safari, Konqueror
				if (title.innerText) {
					value = title.innerText;
				}
				// Firefox
				else {
					value = title.innerHTML.unescapeHTML();
				}
			}
		
			// show input field
			var inputField = new Element('input', {
				'id': 'entryTitleInput'+id,
				'type': 'text',
				'className': 'inputText',
				'style': ('width: '+title.getWidth()+'px;'),
				'value': value
			});
			entrySubjectDiv.insert(inputField);
			
			// add event listeners
			inputField.onkeydown = function(name, e) { this.doTitleEdit(name, e); }.bind(this, id);
			inputField.onblur = function(name) { this.abortTitleEdit(name); }.bind(this, id);
			
			// set focus
			inputField.focus();
		}
	},
	
	/**
	 * Aborts the editing of an entry title.
	 */
	abortTitleEdit: function(id) {
		// remove input field
		var entrySubjectInputDiv = $('entryTitleInput'+id);
		if (entrySubjectInputDiv) {
			entrySubjectInputDiv.remove();
		}
		
		// show title
		var entrySubjectDiv = $('entryTitle'+id);
		if (entrySubjectDiv) {
			// show first child
			var title = entrySubjectDiv.select('a')[0];
			if (title) {
				title.removeClassName('hidden');
			}
		}
	},
	
	/**
	 * Takes the value of the input-field and creates an ajax-request to save the new title.
	 * enter = save
	 * esc = abort
	 */
	doTitleEdit: function(id, e) {
		if (!e) e = window.event;
		
		// get key code
		var keyCode = 0;
		if (e.which) keyCode = e.which;
		else if (e.keyCode) keyCode = e.keyCode;
	
		// get input field
		var inputField = $('entryTitleInput'+id);
		
		// enter
		if (keyCode == '13' && inputField.value != '') {
			// set new value
			inputField.value = inputField.getValue().strip();
			var entrySubjectDiv = $('entryTitle'+id);
			var title = entrySubjectDiv.select('a')[0];
			if (title) {
				title.update(inputField.getValue().escapeHTML());
			}
			
			// save new value
			new Ajax.Request('index.php?action=EntrySubjectEdit&entryID='+id+'&t='+SECURITY_TOKEN+SID_ARG_2ND, {
				method: 'get',
				parameters: {
					subject: inputField.getValue()
				}
			});
			
			// abort editing
			inputField.blur();
			return false;
		}
		// esc
		else if (keyCode == '27') {
			inputField.blur();
			return false;
		}
	},
	
	/**
	 * Starts the editing of an entry prefix.
	 */
	startPrefixEdit: function(id) {
		if ($('entryPrefixSelect'+id)) return;
		var entry = this.data.get(id);
		var entryPrefixSpan = $('entryPrefix'+id);
		if (entryPrefixSpan) {			
			// hide span
			entryPrefixSpan.firstDescendant().addClassName('hidden');
			var value = this.prefixData.get(entry.prefixID);
			
			// show select field
			var selectField = new Element('select', {
				'id': 'entryPrefixSelect'+id
			});
			entryPrefixSpan.appendChild(selectField);
			
			// add empty option
			var optionField = new Element('option', {
				'value': 0
			});
			selectField.appendChild(optionField);
			
			var idArray = this.prefixData.keys();
			for (var i = 0; i < idArray.length; i++) {
				var prefixID = idArray[i];
				var prefix = this.prefixData.get(prefixID);
				
				var optionField = new Element('option', {
					'value': prefixID,
					'selected': (entry.prefixID == prefixID ? true : false)
				});
				
				selectField.appendChild(optionField);
				optionField.appendChild(document.createTextNode(prefix.prefixName));
			}
			
			// add event listeners
			selectField.onchange = function(name, selectField) { this.doPrefixEdit(name, selectField); }.bind(this, id, selectField);
			selectField.onblur = function(name) { this.abortPrefixEdit(name); }.bind(this, id);
			
			// set focus
			selectField.focus();
		}
	},
	
	/**
	 * Aborts the editing of an entry prefix.
	 */
	abortPrefixEdit: function(id) {
		var entry = this.data.get(id);
		
		var entryPrefixSpan = $('entryPrefix'+id);
		if (entryPrefixSpan) {
			// remove select field			
			var selects = entryPrefixSpan.select('select');
			for (var i = 0; i < selects.length; i++) {
				entryPrefixSpan.removeChild(selects[i]);
			}

			// show span
			entryPrefixSpan.firstDescendant().removeClassName('hidden');
		}
	},
	
	/**
	 * Saves the new value of an entry prefix.
	 */
	doPrefixEdit: function(id, selectField) {
		var entry = this.data.get(id);
		
		// get new value
		var newPrefixID = selectField.options[selectField.selectedIndex].value;
		
		// set new value
		entry.prefixID = newPrefixID;
		var entryPrefixSpan = $('entryPrefix'+id);
		
		var newPrefixValue = (newPrefixID != 0 ? this.prefixData.get(newPrefixID).styledPrefixName : '');
		entryPrefixSpan.firstDescendant().update(newPrefixValue);
		
		// save new value
		new Ajax.Request('index.php?action=EntryPrefixEdit&entryID='+id+'&t='+SECURITY_TOKEN+SID_ARG_2ND, {
			method: 'post',
			parameters: {
				prefixID: newPrefixID
			}
		});
			
		// abort editing
		selectField.blur();
	}
});