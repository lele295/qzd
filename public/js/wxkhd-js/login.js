window.onload = function(){
	$(".nextBtn").tap(function(){
		$(".loginSuccess").show();
	})
	$(".del").tap(function(){
		$(".loginSuccess").hide();
	})
}
