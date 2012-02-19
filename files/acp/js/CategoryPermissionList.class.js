/** 
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
var CategoryPermissionList = Class.create({
	/**
	 * Inits PermissionList.
	 */
	initialize: function(key, data, settings) {
		this.key = key;
		this.type = 'category';
		this.data = data;
		this.settings = settings;
		this.selectedIndex = -1;
		this.inputHasFocus = false;
		
		// add input listener
		var input = $(this.key+'AddInput');
		if (input) {
			input.observe('focus', function() { this.inputHasFocus = true; }.bind(this));
			input.observe('blur', function() { this.inputHasFocus = false; }.bind(this));
			input.observe('keydown', function(event) {
				var keyCode = event.keyCode;
				if (keyCode == Event.KEY_RETURN) {
					this.addPermission();
				}
			}.bind(this));
		}
		
		// add button listener
		var button = $(this.key+'AddButton');
		if (button) {
			button.observe('click', this.addPermission.bind(this));
		}
		
		// refresh permissions
		this.refreshPermissions();
	},
	
	/**
	 * Refreshes the permission list.
	 */
	refreshPermissions: function() {
		var dataDiv = $(this.key);
		if (dataDiv) {
			// remove old content
			dataDiv.update();
			
			// create list
			var indexArray = this.data.keys();
			if (indexArray.length > 0) {
				var ul = new Element('ul');
				for (var i = 0; i < indexArray.length; i++) {
					var index = indexArray[i];
					var permission = this.data.get(index);	
					
					// create li
					var removeImage = new Element('img', { src: RELATIVE_WCF_DIR+'icon/deleteS.png' });
					var removeLink = new Element('a', { className: 'remove' }).observe('click', function(name) { this.removePermission(parseInt(name)); }.bind(this, index)).insert(removeImage);
					var img = new Element('img', { src: RELATIVE_WCF_DIR+'icon/'+permission.type+'S.png' });
					var a = new Element('a').observe('click', function(name) { this.selectPermission(parseInt(name)); }.bind(this, i)).insert(img).insert(permission.name);
					var li = new Element('li', { id: this.key+i, className: (i == this.selectedIndex ? 'selected' : '') }).insert(removeLink).insert(a);
					
					// insert li
					ul.insert(li);
				}
				dataDiv.insert(ul);
			}
		}
	},
	
	/**
	 * Selects the permission with the given index.
	 */
	selectPermission: function(index) {
		var permission = this.data.get(index);

		// disable selected item
		if (this.selectedIndex != -1) {
			var li = $(this.key+this.selectedIndex);
			if (li) li.removeClassName('selected');
		}
		
		// select item
		this.selectedIndex = index;
		if (this.selectedIndex == -1) {
			this.hideSettings();
		}
		else {
			$(this.key+this.selectedIndex).addClassName('selected');
			
			// update title
			var h3 = $(this.key+'SettingsTitle');
			if (h3) {
				h3.update(language['wsif.acp.'+this.type+'.permissions.permissionsFor'].replace(/\{\$name\}/, permission.name));
			}
			
			// refresh settings
			this.refreshSettings();
			
			// show settings
			this.showSettings();
		}
	},
	
	/**
	 * Adds a new permission to the list.
	 */
	addPermission: function() {
		var query = $(this.key+'AddInput').getValue().strip();
		if (query) {
			this.ajaxRequest = new AjaxRequest();
			new Ajax.Request('index.php?page=CategoryPermissionsObjects'+SID_ARG_2ND, {
				method: 'post',
				parameters: {
					'query': query
				},
				onSuccess: function(response) {
					var objects = response.responseXML.getElementsByTagName('objects');
					if (objects.length > 0) {
						var firstNewKey = -1;
						for (var i = 0; i < objects[0].childNodes.length; i++) {
							// get name
							var name = objects[0].childNodes[i].childNodes[0].childNodes[0].nodeValue;
							var type = objects[0].childNodes[i].childNodes[1].childNodes[0].nodeValue;
							var id = objects[0].childNodes[i].childNodes[2].childNodes[0].nodeValue;  
							
							var doBreak = false;
							for (var j = 0; j < this.data.keys().length; j++) {
								if (this.data.get(j).id == id && this.data.get(j).type == type) doBreak = true;
							}
							if (doBreak) continue;

							var key = this.data.keys().length;
							if (firstNewKey == -1) firstNewKey = key;
							var settings = new Hash();
							settings.set('fullControl', -1);
							for (var j = 0; j < this.settings.length; j++) {
								settings.set(this.settings[j], -1);
							}
							this.data.set(key, { 'name': name, 'type': type, 'id': id, 'settings': settings });
						}
						
						$(this.key+'AddInput').value = '';
						this.refreshPermissions();
						
						// select new permission
						if (firstNewKey != -1) {
							this.selectPermission(firstNewKey);
						}
					}
				}.bind(this)
			});
		}
	},
	
	/**
	 * Removes a permission from the list.
	 */
	removePermission: function(index) {
		this.data.unset(index);
		this.refreshPermissions();
		
		if (this.selectedIndex == index) this.selectPermission(-1);
	},
	
	/**
	 * Refreshes the settings.
	 */
	refreshSettings: function() {
		permission = this.data.get(this.selectedIndex);
		
		var settingsDiv = $(this.key+'Settings');
		if (settingsDiv) {
			// create ul
			var ul = new Element('ul');
			
			var settingIndexes = permission.settings.keys();
			for (var i = 0; i < settingIndexes.length; i++) {
				var setting = settingIndexes[i];
				var settingValue = permission.settings.get(setting);
				
				// deny
				// checkbox
				var checkboxDeny = new Element('input', { 'type': 'checkbox', 'id': this.key+'Setting'+setting+'Deny', 'name': setting, 'checked': (settingValue == 0 ? true : false) });
				checkboxDeny.observe('click', function(name, checkbox) { this.denySetting(name, checkbox.checked); }.bind(this, setting, checkboxDeny));
				// label
				var labelDeny = new Element('label', { 'className': 'deny' }).insert(checkboxDeny);
				
				// allow
				// checkbox
				var checkboxAllow = new Element('input', { 'type': 'checkbox', 'id': this.key+'Setting'+setting+'Allow', 'name': setting, 'checked': (settingValue == 1 ? true : false) });
				checkboxAllow.observe('click', function(name, checkbox) { this.allowSetting(name, checkbox.checked); }.bind(this, setting, checkboxAllow));
				// label
				var labelAllow = new Element('label', { 'className': 'allow' }).insert(checkboxAllow);
				
				// create span
				var span = new Element('span').observe('mouseup', function(name) { $(name).focus(); }.bind(this, this.key+'Setting'+setting+'Allow')).insert(language['wsif.acp.'+this.type+'.permissions.'+setting]);
				
				// create a
				var a = new Element('a').insert(labelDeny).insert(labelAllow).insert(span);
				
				// create li
				var li = new Element('li').insert(a);
				
				// insert li
				ul.insert(li);
			}
			
			// insert new content
			settingsDiv.update(ul);
			
			// check full control
			this.checkFullControl();
		}
	},
	
	/**
	 * Shows the settings.
	 */
	showSettings: function() {
		var settingsContainerDiv = $(this.key+'Settings').parentNode.parentNode;
		if (!settingsContainerDiv.visible()) {
			new Effect.Parallel([
				new Effect.BlindDown(settingsContainerDiv),
				new Effect.Appear(settingsContainerDiv)
			], { duration: 0.3 });
		}
	},
	
	/**
	 * Hides the settings.
	 */
	hideSettings: function() {
		var settingsContainerDiv = $(this.key+'Settings').parentNode.parentNode;
		if (settingsContainerDiv.visible()) {
			new Effect.Parallel([
				new Effect.BlindUp(settingsContainerDiv),
				new Effect.Fade(settingsContainerDiv)
			], { duration: 0.3 });
		}
	},
	
	/**
	 * Checks or unchecks an allow setting.
	 */
	allowSetting: function(setting, checked) {
		if (setting == 'fullControl') this.allowSettingFullControl(checked);
		else this.data.get(this.selectedIndex).settings.set(setting, (checked ? 1 : -1));
		
		this.refreshSettings();
	},
	
	/**
	 * Checks or unchecks all allow settings.
	 */
	allowSettingFullControl: function(checked) {
		var settingIndexes = this.data.get(this.selectedIndex).settings.keys();
		for (var i = 0; i < settingIndexes.length; i++) {
			var setting = settingIndexes[i];
			if (setting == 'fullControl') continue;
			this.data.get(this.selectedIndex).settings.set(setting, (checked ? 1 : -1));
		}
	},
	
	/**
	 * Checks or unchecks a deny setting.
	 */
	denySetting: function(setting, checked) {
		if (setting == 'fullControl') this.denySettingFullControl(checked);
		else this.data.get(this.selectedIndex).settings.set(setting, (checked ? 0 : -1));
		
		this.refreshSettings();
	},
	
	/**
	 * Checks or unchecks all deny settings.
	 */
	denySettingFullControl: function(checked) {
		var settingIndexes = this.data.get(this.selectedIndex).settings.keys();
		for (var i = 0; i < settingIndexes.length; i++) {
			var setting = settingIndexes[i];
			if (setting == 'fullControl') continue;
			this.data.get(this.selectedIndex).settings.set(setting, (checked ? 0 : -1));
		}
	},
	
	/**
	 * Checks or unchecks the allow and deny full control setting.
	 */
	checkFullControl: function() {
		var value = undefined;
		
		var settingIndexes = this.data.get(this.selectedIndex).settings.keys();
		for (var i = 0; i < settingIndexes.length; i++) {
			var setting = settingIndexes[i];
			if (setting == 'fullControl') continue;
			if (value == undefined) value = this.data.get(this.selectedIndex).settings.get(setting);
			else {
				if (value != this.data.get(this.selectedIndex).settings.get(setting)) {
					value = -1; break;
				}
			}
		}
		
		$(this.key+'SettingfullControlAllow').checked = (value == 1);
		$(this.key+'SettingfullControlDeny').checked = (value == 0);
	},
	
	/**
	 * Stores the values in hidden fields.
	 */
	submit: function(form) {
		var indexArray = this.data.keys();
		for (var i = 0; i < indexArray.length; i++) {
			var index = indexArray[i];
			var permission = this.data.get(index);	
			
			// create fields
			var typeField = new Element('input', { 'type': 'hidden', 'name': this.key+'['+i+'][type]', 'value': permission.type });
			var idField = new Element('input', { 'type': 'hidden', 'name': this.key+'['+i+'][id]', 'value': permission.id });
			var nameField = new Element('input', { 'type': 'hidden', 'name': this.key+'['+i+'][name]', 'value': permission.name });
			
			// insert fields
			form.insert(typeField).insert(idField).insert(nameField);
			
			// settings
			var settingIndexArray = permission.settings.keys();
			for (var j = 0; j < settingIndexArray.length; j++) {
				var setting = settingIndexArray[j];
				if (setting == 'fullControl') continue;
				var settingField = new Element('input', { 'type': 'hidden', 'name': this.key+'['+i+'][settings]['+setting+']', 'value': permission.settings.get(setting) });
				form.insert(settingField);
			}
		}
	}
});