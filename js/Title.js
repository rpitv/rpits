(function() {
	RPITS.ui.Title = function(title) {
		for (var prop in title) this[prop] = title[prop];
	};
	RPITS.ui.Title.prototype.getFilename = function() {
		if(this.type == 'general') {
			return this.name + this.id + '.png';
		} else if (this.type == 'player') {
			return this.num + this.first + this.last + '.png';
		}
	};
	
	RPITS.ui.Title.prototype.getDisplayName = function() {
		if(this.type == 'general') {
			return this.name;
		} else {
			return this.num + ' - ' + this.first + ' ' + this.last;
		}
	};

	RPITS.ui.Title.prototype.renderListEl = function() {
		this.listEl = $('<li>');
		this.listEl.append('<img src="thumbs/'+this.getFilename()+'">'+this.getDisplayName());
		this.listEl.data('title',this);

		return this.listEl;
	};

	RPITS.ui.Title.prototype.getEditURL = function() {
		if(this.type == 'general') {
			return "im_edit_title.php?id=" + this.id;
		} else if(this.type == 'player') {
			return "im_edit_ptitle.php?id=" + this.id;
		} else {
			throw new error('Invalid title type');
		}
	}
	RPITS.ui.Title.prototype.getRenderURL = function() {
		if(this.type == 'general') {
			return "im_render_title.php?id=" + this.id +"&eventId="+ui.eventId;
		} else {
			return "im_render_title.php?player=" + this.id;
		}
	}
	
})();
