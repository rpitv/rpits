/*
 * an example of how a title with an animated headshot might be done
 */
 
 function zeroFill( number, width )
{
  width -= number.toString().length;
  if ( width > 0 )
  {
    return new Array( width + (/\./.test( number ) ? 2 : 1) ).join( '0' ) + number;
  }
  return number + ""; // always return a string
}
 
function load_image_sequence(filename, count) {
	/* helper function to load a bunch of sequential pngs */
	var result = Array(count);
	for (var i = 0; i < count; i++) {
		result[i] = load_asset(filename + zeroFill(i, 3) + ".tga");
	}
	
	return result;
}

function load_static_headshot(bg_replacement_string) {
	//handle if we don't have a lookup
	return load_asset(bg_replacement_string.substring(0, bg_replacement_string.length - 15) + '.png');
}
 
({
	/* background stats card image (without headshot) */
	background: load_asset("BACKGROUND_REPLACEMENT_STRING"),
	/* sequence of PNG headshots to use */
	headshots: load_image_sequence("SEQUENCE_REPLACEMENT_STRING", 230),
	static_headshot: load_static_headshot("BACKGROUND_REPLACEMENT_STRING"),
	/* where to position the background png */
	background_x: 0,
	background_y: 0,
	/* where to position the headshot frames */
	//headshot_x: 55,
	//headshot_y: 743,
	headshot_x: 400,
	headshot_y: 783,
	x: 353,
	y: 885,
	x_max: 1575,
	y_max: 1056,
	fade_head: false,
	done: false, //when fade_out has completed
	headshot_opacity: 0,
	/* 
	 * we're going to loop the final frames of the headshot (after the "look-up"). 
	 * This marks the first frame that isn't "look-up".
	 */
	loop_start: 150,
	
	/*
	 * these are used to keep track of the looping
	 */
	frame_counter: 0,
	direction: 0,
	fade_out: false,
	back_opacity: 255,
	/* render one frame of the looped animation */
	render: function() {
	
		if(this.headshots[0] == -1){
			this.background = this.static_headshot;
		}
	
		//handle animating the stats card
		if (this.done == false){
		
			if (this.direction == 0) {
				this.frame_counter++;
				if (this.frame_counter >= this.headshots.length) {
					this.frame_counter -= 2;
					this.direction = 1;
				}
			} else {
				this.frame_counter--;
				if (this.frame_counter <= this.loop_start) {
					this.frame_counter += 2;
					this.direction = 0;
				}
			}
			
			if (this.x < this.x_max + 500){
				this.x = this.x + 65;
			}
			
			if(this.y < this.y_max && this.x > this.x_max + 300){
				this.fade_head = true;
			}
			
			if(this.y < this.y_max && this.x > this.x_max + 50){
				this.y = this.y + 20;
			}
			
			if (this.fade_head == true && this.headshot_opacity <= 235 && this.fade_out == false){
				this.headshot_opacity = this.headshot_opacity + 20;
			}else if (this.fade_head == true && this.headshot_opacity >= 235 && this.fade_out == false){
				this.headshot_opacity = 255;
				this.fade_head = false;
			}
			
			if (this.fade_out == true){
				this.back_opacity = this.back_opacity - 17;
				this.headshot_opacity = this.headshot_opacity - 17;
			}
			
			if (this.fade_out == true && this.back_opacity <= 0){
				this.fade_out = false;
				this.done = true;
			}
			
			draw(this.background, 0, 0, this.background_x, this.background_y, this.x, this.y, this.back_opacity);
			draw(this.headshots[this.frame_counter], 0, 0, this.headshot_x, this.headshot_y, -1, -1, this.headshot_opacity);
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
