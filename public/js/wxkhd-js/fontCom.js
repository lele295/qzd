function loaded(){
	var scale;
	size();
	function size(){
		$screenWid = $(window).width();
		scale = $screenWid/640;
		$('html').css("font-size",scale*100);
	}	
}