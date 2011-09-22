/** 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.php>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
var EntryRating = Class.create({
	/**
	 * Inits Rating.
	 */
	initialize: function(elementName) {
		this.elementName = elementName;
		this.options = Object.extend({
			currentRating:		0,
			iconRating:		'',
			iconNoRating:		''
		}, arguments[1] || { });
		
		// create star elements
		var span = $(this.elementName+'Span');
		if (span) {
			// add stars
			for (var i = 1; i <= 5; i++) {
				var star = new Element('img', {
					'src': this.options.iconNoRating,
					'alt': ''
				});
				star.onmouseover = function(star, name) { star.setStyle({ cursor: 'pointer' }); this.showRating(parseInt(name)); }.bind(this, star, i);
				star.onclick = function(name) { this.submitRating(parseInt(name)); }.bind(this, i);
				
				// append star
				span.appendChild(star);
			}
		
			// add event listener
			span.onmouseout = function() { this.showRating(this.options.currentRating) }.bind(this);
			
			// set visible
			span.removeClassName('hidden');
		}
		
		// show current rating
		if (this.options.currentRating > 0) {
			this.showRating(this.options.currentRating);
		}
	},

	/**
	 * Shows given rating.
	 */	
	showRating: function(rating) {
		var span = $(this.elementName+'Span');
		if (span) {
			for (var i = 1; i <= rating; i++) {
				if (span.childNodes[i-1]) {
					span.childNodes[i-1].src = this.options.iconRating;
				}
			}	
			
			for (var i = rating + 1; i <= 5; i++) {
				if (span.childNodes[i-1]) {
					span.childNodes[i-1].src = this.options.iconNoRating;
				}
			}
		}
	},

	/**
	 * Submits given rating.
	 */	
	submitRating: function(rating) {
		var element = $(this.elementName);
		var select = $(this.elementName+'Select');
		if (element) {
			this.currentRating = rating;
			element.value = rating;
			
			if (select) {
				select.selectedIndex = rating - 1;
			}
			
			element.form.submit();
		}
	}
});