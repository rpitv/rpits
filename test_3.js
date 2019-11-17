 ({
	/* background stats card image (without headshot) */
	background: load_asset("BACKGROUND_REPLACEMENT_STRING"),
	/* where to position the background png */
	background_x: 0,
	background_y: 0,
	background2_y_offset: 1030,
	x: 353,
	y: 885,
	fade_back: true,
	fade_bottom: false,
	done: false, //when fade_out has completed
	/*
	 * these are used to keep track of the looping
	 */
	fade_out: false,
	back_opacity: 0,
	/* render one frame of the looped animation */
	render: function() {
	
		
	
		//handle animating the stats card
		if (this.done == false){
		
			if(this.fade_back == true && this.back_opacity <= 235)
			{
				this.back_opacity = this.back_opacity + 20;
			}
			else if(this.fade_back == true)
			{
				this.back_opacity = 255;
				this.fade_bottom = true;
				this.fade_back = false;
			}
			
			if(this.fade_bottom == true && this.background2_y_offset >890)
			{
				this.background2_y_offset  = this.background2_y_offset - 8;
			}
			else if(this.fade_bottom == true && this.background2_y_offset <=890)
			{
				this.fade_bottom = false;
			}
			
			
			if (this.fade_out == true && this.back_opacity <= 17){
				this.back_opacity = 0;
				this.fade_out = false;
				this.done = true;
			}
			else if (this.fade_out == true){
				this.back_opacity = this.back_opacity - 17;
			}
			
			draw(this.background, 0, this.background2_y_offset, this.background_x, this.background_y+885, this.x, this.y, this.back_opacity);
										//  x, y (for image start)          x,y for putting  sizex sizy opacity
		}
	},
	
	command: function(data){
		if(data != 'cut'){
			this.fade_out = true;
		}
		else{
			this.back_opacity = 0;
			this.fade_opacity = 0;
			this.done = true;
		}
	}
});
