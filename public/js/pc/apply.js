$(function(){
    $(".person_form").validate({
        errorPlacement : function(error, element) {
            error.appendTo(element.parentsUntil("input-con").next("p"));
        },
        rules: {
            Childrentotal:{
                required : true,
                max:10,
                number:true
            },
            SpouseName:{
                required:true,
                isName:true
            },
            SpouseTel:{
                required:true,
                Phonepc:true
            },
            KinshipName:{
                required:true,
                isName:true,
                isSpecial:true
            },
            KinshipTel:{
                required:true,
                Phonepc:true
            },
            MaturityDate:{
                required : true,
                after:true
            },
            Countryside:{
                required:true,
                isSpecial:true
            },
            Villagecenter:{
                required:true,
                isSpecial:true
            },
            Plot:{
                required:true,
                isSpecial:true
            },
            Room:{
                required:true,
                isSpecial:true
            },
            ReplaceAccount:{
                required:true,
                isBankCard:true
            },
            OpenBank:{
                required : true
            },
            OpenBranch:{
                required : true
            },
            RelativeType:{
                required : true
            },
            City:{
                required : true
            },
            Marriage:{
                required : true
            },
            Flag2:{
                required : true
            },
            OtherTelephone:{
                isSpecial:true
            }
        },
        messages: {
            Childrentotal:{
                required : '子女数目不能为空'
            },
            SpouseName:{
                required:"请输入配偶姓名",
                isName:"请输入正确的配偶姓名"
            },
            SpouseTel:{
                required:"请输入配偶电话",
                isPhoneNum:"请输入正确的配偶电话"
            },
            KinshipName:{
                required:"请输入亲人姓名",
                isName:"请输入正确的亲人姓名"
            },
            KinshipTel:{
                required:"请输入亲人电话",
                isPhoneNum:"请输入正确的亲人电话"
            },
            MaturityDate:{
                required : "请输入到期日",
                after: "身份证有效期必须大于当前日期"
            },
            Countryside:{
                required:"请输入区县镇",
                isAddress:"请输入正确的区县镇"
            },
            Villagecenter:{
                required:"请输入街道村",
                isAddress:"请输入正确的街道村"
            },
            Plot:{
                required:"请输入栋/单元/房号",
                isAddress:"请输入正确的栋/单元/房号"
            },
            Room:{
                required:"请输入小区/楼盘",
                isAddress:"请输入正确的小区/楼盘"
            },
            ReplaceAccount:{
                required:"请输入银行卡账号",
                isBankCard:"请输入正确的银行卡账号"
            },
            OpenBranch:{
                required : '请选择支行名称'
            },
            RelativeType:{
                required : '亲属关系不能为空'
            },
            City:{
                required : '请选择邻近开户行省市'
            },
            OpenBank:{
                required : '请选择开户银行'
            },
            Marriage:{
                required : '请选择婚姻状态'
            },
            Flag2:{
                required : '请选择现居住地址'
            }
        }
    });



    $(".firm_form").validate({
        errorPlacement : function(error, element) {
            error.appendTo(element.parentsUntil("input-con").next("p"));
        },
        rules : {
            WorkCorp:{
                required : true,
                isSpecial:true,
                minlength:5
            },
            UnitCountryside:{
                required : true,
                isSpecial:true
            },
            UnitStreet:{
                required : true,
                isSpecial:true
            },
            UnitRoom:{
                required : true,
                isSpecial:true
            },
            UnitNo:{
                required : true,
                isSpecial:true
            },
            WorkTel:{
                required : true,
                minlength:6,
                number:true
            },
            area_code:{
                required : true,
                minlength:3,
                number:true,
                isAreaCode:true
            },
            SelfMonthIncome:{
                required : true,
                min:1000,
                number:true
            },
            OtherContact:{
                required : true,
                isName:true
            },
            ContactTel:{
                required : true,
                Phonepc : true
            },
            Flag8:{
                required : true
            }
        },
        messages : {
            WorkCorp: {
                required: '单位名称不能为空'
            },
            UnitCountryside: {
                required: '区县镇不能为空'
            },
            WorkTel: {
                required: '区号和电话不能为空'
            },
            area_code: {
                required: '区号和电话不能为空'
            },
            UnitStreet: {
                required: '街道村不能为空'
            },
            UnitRoom: {
                required: '小区/楼盘不能为空'
            },
            UnitNo: {
                required: '栋/单元/房号不能为空'
            },
            OtherContact: {
                required: '其他联系人姓名不能为空'
            },
            ContactTel: {
                required: '其他联系人电话不能为空'
            },
            SelfMonthIncome: {
                required: '月收入不能为空',
                min: '请正确填写月收入数额'
            },
            Flag8: {
                required: '请选择邮寄地址'
            }
        }
    });

    $("#img-form").validate({
        errorPlacement : function(error, element) {
            element.parent().find("img").attr("src", "/images/pc/p-attach-img.png");
        },
        rules : {
            card_face:{
                required : true
            },
            card_oppo:{
                required : true
            },
            self_card:{
                required : true
            }
        },
        messages : {
            card_face: {
                required: '请上传身份证正面'
            },
            card_oppo: {
                required: '请上传身份证反面'
            },
            self_card: {
                required: '请上传本人持身份证照片'
            }
        }
    });
})