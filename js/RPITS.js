(function() {
	RPITS = {};

	RPITS.ui = {};

	RPITS.constants = {
		KEYCODE: {
			SPACEBAR: 32,
			LETTER_C: 67,
			ENTER: 13,
			LETTER_R: 82,
			LETTER_E: 69,
			ARROW_DOWN: 40,
			ARROW_UP: 38,
			TAB: 9		
		}
	};

	RPITS.ui.Monitor = function(options) {
		this.options = options;
		this.el = $('<div id="' + options.id +'"></div>');
		this.label = $('<div class="label">'+options.name+'</div>');
		this.image = $('<div class="image"></div>');
		this.el.append(this.label);
		this.el.append(this.image);
		$('body').append(this.el);
		this.title = null;

		this.on = function(title) {
			this.title = title;
			this.el.addClass('on');
			this.label.text(this.options.name + ' - ' + title.name);
			var image = $('<img src="out/'+title.getFilename() + '?rand=' + Date.now() +'">');
			image.width(this.el.css('width'));
			this.image.html(image);
		};
		this.active = function() {
			return this.title;
		};
		this.off = function() {
			this.title = null;
			this.el.removeClass('on');
			this.label.text(this.options.name);
			this.image.empty();
		};
	};

	RPITS.ui.Console = function() {
		this.el = $('<div id="log"></div>');
		$('body').append(this.el);

		this.log = function(string) {
			console.log(string);
			this.el.append(string);
			this.el.animate({
				scrollTop: this.el.prop("scrollHeight") - this.el.height()
			}, 200);
		};
	};

	RPITS.Keyer = function(options) {
		var DURATION_IN = 15;
		var DURATION_OUT = 15;
		options = options || {};
		this.base = options.base || 'putter.php';
		this.el = $('<div id="loadTarget"></div>');

		this.doXHR = function(url,callback) {
			$.get(url,function(data) {
				console.log(data);
				if(typeof callback === 'function') {
					callback(data);
				}
			});
		};

		this.command = function(command,callback) {
			this.doXHR(this.base + '?command=' + command,callback);
		};

		this.put = function(title,callback) {
			this.doXHR(this.base + '?path=out/' + title.getFilename(),callback);
		};

		this.offProgram = function(duration) {
			this.title = null;
			duration = duration || (this.title && this.title.durationOut) || DURATION_OUT;
			this.command('dissolve_out/' + duration);
		}

		this.onProgram = function(title,duration) {
			this.title = title;
			duration = duration || title.durationIn || DURATION_IN;
			this.put(title,function() {
				this.command('dissolve_in/' + duration);
			}.bind(this));
		};
	};
	window.RPITS = RPITS;
}(window));