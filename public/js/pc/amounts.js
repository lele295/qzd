//试算
function count_res(month_pay, amount_obj, period_obj, amount_json){
    this.amount_obj = amount_obj;
    this.period_obj = period_obj;
    this.amount_json = amount_json;
    this.month_pay = month_pay;
}
count_res.prototype.touchcount = function(){
    var self = this;

    function count_money(){
        var amount_val = self.amount_obj.val();
        if(!amount_val){
            return false;
        }
        var period_json = self.amount_json[amount_val];
        var period_text = "";
        $.each(period_json,function(i,val){
            period_text = "<option>"+i+"</option>"+period_text;
        })
        self.period_obj.html(period_text);
        count_period();
    }
    function count_period(){
        var amount_val = self.amount_obj.val();
        var period_val = self.period_obj.val();
        if(!amount_val || !period_val){
            return false;
        }
        var period_json = self.amount_json[amount_val];
        if(self.month_pay.attr("type")) {
            self.month_pay.val(period_json[period_val].toFixed(2));
        }else{
            self.month_pay.text(period_json[period_val].toFixed(2));
        }
    }
    self.amount_obj.change(function(){
        count_money();
    })
    self.period_obj.change(function(){
        count_period();
    })
    count_money();
}

//银行卡
function bank_warn(obj, obj_txt){
    this.obj = obj;
    this.obj_txt = obj_txt;
}
bank_warn.prototype.trigg = function(){
    var self= this;
    this.obj.keyup(function() {
        var str_val = $(this).val().replace(/\s/g, "");

        var re_val = "";
        if (str_val.length > 4){
            for (var i = 0; i < str_val.length; i += 4) {
                if(i%4==0 && i!=0) {
                    re_val += " " + str_val.substr(i, 4);
                }else{
                    re_val += str_val.substr(i, 4);
                }
            }
        }else{
            re_val = str_val;
        }

        $(this).val(re_val);
        //和试算银行卡号不一致提示
        if(str_val.length >15) {
            var bank_str = "您的<span class='step2_p_span'>所有贷款</span>将使用以上账户<span class='step2_p_span'>代扣还款</span>";
            if(fore_bank==str_val)
            {
                bank_str="";
            }
            self.obj_txt.show();
            self.obj_txt.html(bank_str);
        }else{
            self.obj_txt.hide();
        }
    });

};

//选择中后显示
function select_res_show(obj, val, obj_con){
    this.obj = obj;
    this.val = val;
    this.obj_con = obj_con;
}
select_res_show.prototype.start = function(){
    var self = this;
    self.obj.change(function(){
        var val = self.obj.val();
        console.log(val);
        if(val == self.val){
            self.obj_con.show();
        }else{
            self.obj_con.hide();
        }
    })
    self.obj.trigger("change");
}