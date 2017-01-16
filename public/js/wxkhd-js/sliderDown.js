$(".column").click(function () {
    var obj = $(this).next()
    if (obj.css("display") == "none") {
        $(this).children(".right").children(".icon").html("&#xe601;")
        obj.show();
    } else {
        obj.hide();
        $(this).children(".right").children(".icon").html("&#xe600;")
    }
})

/*点击放大图片*/
$('.img img').click(function(){
    var _class = $(this).attr('class');
    if(_class == 'active'){
        $(this).removeClass('active');
    }else {
        $(this).addClass('active');
    }
})

