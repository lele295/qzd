function loaded(){
	var scale;
	size();
	function size(){
		$screenWid = $(window).width();
		scale = $screenWid/640;
		$('html').css("font-size",parseInt(scale*100));
	}
    window.addEventListener("load",function(){
        FastClick.attach(document.body);
    },false);
}