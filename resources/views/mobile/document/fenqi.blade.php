<input type="hidden" id="loan_id" name="loan_id" value=""/>
<tr>
    <td colspan="1">合同号：
        @if (!empty($contract_data))
            {{ $contract_data->contract_no }}
        @endif</td>
</tr>
<tr>
    <td colspan="1">申请日期：
        @if (!empty($order_data))
            {{ date('Y-m-d',$order_data->order_create_time) }}
        @endif</td>
</tr>
<tr>
    <td colspan="1">销售点代码：
        @if (!empty($store_data))
            {{ $store_data->SNO }}
        @endif</td>
</tr>
<tr>
    <td colspan="1">销售点名称：
        @if (!empty($store_data))
            {{ $store_data->SNAME }}
        @endif</td>
    </td>
</tr>
<tr>
    <td colspan="1">销售顾问代码：
        @if (!empty($store_data))
            {{ $store_data->SALESMANNO }}
        @endif</td>
    </td>
</tr>
<tr>
    <td colspan="1">申请类别：消费贷</td>
</tr>
<tr>
    <td colspan="1">销售点地址：
        @if (!empty($store_data) )
            {{ $store_data->ADDRESS }}
        @endif</td>
    </td>
</tr>
<tr>
    <td colspan="1">产品代码：
        @if (!empty($rate_data))
            {{ $rate_data->BUSTYPEID }}
        @endif
    </td>
</tr>
<tr>
    <td colspan="1">过去在本公司有过几次贷款申请：</td>
</tr>
<tr>
    <td colspan="1" style="text-align: center">个人资料</td>
</tr>
<tr>
    <td colspan="1">1.姓名：
        @if (!empty($order_data))
            {{ $order_data->applicant_name }}
        @endif

    </td>
</tr>
<tr>
    <td colspan="1">2.性别：</td>
</tr>
<tr>
    <td colspan="1">3.身份证号：
        @if (!empty($order_data))
            {{ $order_data->applicant_id_card }}
        @endif

    </td>
</tr>
<tr>
    <td colspan="1">4.发证机关：</td>
</tr>
<tr>
    <td colspan="1">5.身份证有效期至：</td>
</tr>
<tr>
    <td colspan="1">6.社保号码/学生号码：</td>
</tr>
<tr>
    <td colspan="1">7.教育程度：</td>
</tr>
<tr>
    <td colspan="1">8.住宅/宿舍电话：</td>
</tr>
<tr>
    <td colspan="1">9.住宅电话登记人：</td>
</tr>
<tr>
    <td colspan="1">10.手机：
        @if (!empty($order_data))
            {{ $order_data->mobile }}
        @endif

    </td>
</tr>


<tr>
    <td  colspan="1">11.电子邮箱：
        @if (!empty($work_data))
            {{ $work_data->qq_email }}
        @endif
    </td>
</tr>
<tr>
    <td  colspan="1">12.QQ号码：</td>
</tr>
<tr>
    <td colspan="1">13.婚姻状况：</td>
</tr>
<tr>
    <td colspan="1">14.子女数目：</td>
</tr>
<tr>
    <td colspan="1">15.住房状况：</td>
</tr>
<tr>
    <td colspan="1">16.户籍地址：</td>
</tr>
<tr>
    <td colspan="1">17.镇/乡：</td>
</tr>
<tr>
    <td colspan="1">18.街道/村：</td>
</tr>
<tr>
    <td colspan="1">19.小区/楼盘：</td>
</tr>
<tr>
    <td colspan="1">20.栋/单元/房间号：</td>
</tr>
<tr>
    <td colspan="1">21.现居住地址：</td>
</tr>
<tr>
    <td colspan="1">22.镇/乡：</td>
</tr>
<tr>
    <td colspan="1">23.街道/村：</td>
</tr>
<tr>
    <td colspan="1">24.小区/楼盘：</td>
</tr>
<tr>
    <td colspan="1">25.栋/单元/房间号：</td>
</tr>
<tr>
    <td colspan="1">26.现居住地址是否与户籍地址相同：</td>
</tr>
<tr><td colspan="1"  style="text-align: center">分期购服务内容</td></tr>
<tr>
    <td colspan="1">27.商品类型 ：
        @if (!empty($product_data))
            {{ $product_data->service_type }}
        @endif
    </td>
</tr>
<tr>
    <td colspan="1">28.商品总价（元）：
        @if (!empty($product_data))
            {{ $product_data->loan_money }}
        @endif
    </td>
</tr>
<tr>
    <td colspan="1">29.自付金额（元）：</td>
</tr>
<tr>
    <td colspan="1">30.贷款本金（元）：
        @if (!empty($product_data))
            {{ $product_data->loan_money }}
        @endif

    </td>
</tr>
<tr>
    <td colspan="1">31.分期期数：
        @if (!empty($product_data))
            {{ $product_data->periods }}
        @endif

    </td>
</tr>


<tr>
    <td colspan="1">32.每月还款日：
        @if (!empty($contract_data))
            {{ $contract_data->monthly_repay_date }}
        @endif
    </td>
</tr>
<tr>
    <td colspan="1">33.每月还款额：
        @if (!empty($contract_data))
            {{ $contract_data->monthly_repay_money }}
        @endif
    </td>
</tr>

<tr>
    <td colspan="1">34.首次还款额：
        @if (!empty($contract_data))
            {{ $contract_data->first_monthly_repay_money }}
        @endif
    </td>
</tr>
<tr>
    <td colspan="1">35.首次还款日：
        @if (!empty($contract_data))
            {{ $contract_data->first_monthly_repay_date }}
        @endif
    </td>
</tr>
<tr>
    <td colspan="1">36.月贷款利率：
        @if (!empty($rate_data))
            {{ $rate_data->MONTHLYINTERESTRATE }}
        @endif
        （%）</td>
</tr>
<tr>
    <td colspan="1">37.月客户服务费率：
        @if (!empty($rate_data))
            {{ $rate_data->CUSTOMERSERVICERATES }}
        @endif
        （%）</td>
</tr>
<tr>
    <td colspan="1">38.月财务管理费率：
        @if (!empty($rate_data))
            {{ $rate_data->MANAGEMENTFEESRATE }}
        @endif
        （%）</td>
</tr>
<tr>
    <td colspan="1">39.月增值服务费率：0（%）</td>
</tr>
<tr>
    <td colspan="1">40.每月随心还服务费（元）:0
        &nbsp;&nbsp;是否选择随心还：否</td>
</tr>
<tr>
    <td colspan="1">41.是否申请参加保险：否</td>
</tr>
<tr>
    <td colspan="1">42.保险公司名称：</td>
</tr>

<tr><td colspan="1"  style="text-align: center">还款账户信息</td></tr>
<tr>
    <td colspan="1">43.指定还款账户账号：</td>
</tr>
<tr>
    <td colspan="1">44.开户银行：</td>
</tr>
<tr>
    <td colspan="1">45.户名：</td>
</tr>
<tr>
    <td colspan="1">46.客户银行卡号/账号(用于收款、扣款)：{{ $work_data->work_repayment_account }}</td>
</tr>
<tr>
    <td colspan="1">47.客户开户银行：{{ $work_data->BANKNAME }}</td>
</tr>
<tr>
    <td colspan="1">48.客户银行账户户名：
        @if (!empty($order_data))
            {{ $order_data->applicant_name }}
        @endif</td>
</tr>
<tr>
    <td colspan="1">49.银行代扣还款：是</td>
</tr>
<tr>
    <td colspan="1">
        本人选择银行代扣还款，表明本人同意并授权深圳市佰仟金融服务有限公司可通过银行从本人指定的银行账户（如第45、46、47项所示）将每月还款额（如第32项所示）及其它应还款项转入指定还款账户（如第42、43、44项所示）。本人同意此扣款授权同时适用于之前由深圳市佰仟金融服务有限公司提供服务并已签订的一份或多份贷款合同，即深圳市佰仟金融服务有限公司可通过银行从本人指定的上述银行账户内划扣本人在各合同下应偿还的相关款项。本人同意该账户同时可用于因提前还款等引起的资金往来。
    </td>
</tr>
<tr>
    <td colspan="1" style="text-align: center">单位信息</td>
</tr>
<tr>
    <td colspan="1">50.单位/学校/个体全称：
        @if (!empty($work_data))
            {{ $work_data->work_unit }}
        @endif
    </td>
</tr>
<tr>
    <td colspan="1">51.任职部门/班级：</td>
</tr>
<tr>
    <td colspan="1">52.职位：</td>
</tr>
<tr>
    <td colspan="1">53.行业类别：</td>
</tr>
<tr>
    <td colspan="1">54.单位性质：</td>
</tr>
<tr>
    <td colspan="1">55.单位电话：
        @if (!empty($work_data))
            {{ $work_data->work_unit_mobile }}
        @endif
    </td>
</tr>
<tr>
    <td colspan="1">56.单位地址：</td>
</tr>
<tr>
    <td colspan="1">57.镇/乡：</td>
</tr>
<tr>
    <td colspan="1">58.街道/村：</td>
</tr>
<tr>
    <td colspan="1">59.小区/楼盘：</td>
</tr>
<tr>
    <td colspan="1">60.栋/单元/房间号：</td>
</tr>
<tr>
    <td  colspan="1">61.邮寄地址：</td>
</tr>
<tr>
    <td  colspan="1">62.总工作经验/总大学学习时间（年）：</td>
</tr>
<tr>
    <td  colspan="1">63.现单位工作/个体营业时间(月)：</td>
</tr>
<tr>
    <td  colspan="1">64.在现单位是否购买社保：</td>
</tr>
<tr>
    <td  colspan="1" style="text-align: center">配偶及家庭成员信息</td>
</tr>
<tr>
    <td  colspan="1">65.配偶姓名：</td>
</tr>
<tr>
    <td  colspan="1">66.配偶移动电话：</td>
</tr>
<tr>
    <td  colspan="1">67.配偶单位名称：</td>
</tr>
<tr>
    <td  colspan="1">68.配偶单位电话：</td>
</tr>
<tr>
    <td  colspan="1">69.家庭成员名称：
        @if (!empty($work_data))
            {{ $work_data->family_name }}
        @endif

    </td>
</tr>
<tr>
    <td  colspan="1">70.家庭成员类型：</td>
</tr>
<tr>
    <td  colspan="1">71.家庭成员电话：
        @if (!empty($work_data))
            {{ $work_data->family_mobile }}
        @endif

    </td>
</tr>
<tr>
    <td colspan="1">72.家庭成员联系地址：</td>
</tr>
<tr><td  colspan="1" style="text-align: center">其他联系人信息 </td></tr>
<tr>
    <td  colspan="1">73.联系人姓名：</td>
</tr>
<tr>
    <td colspan="1">74.与申请人关系：</td>
</tr>
<tr>
    <td colspan="1">75.联系电话:</td>
</tr>
<tr>
    <td colspan="1">76.月收入总额（元）：</td>
</tr>
<tr>
    <td colspan="1">77.其他收入（元/月）：</td>
</tr>
<tr>
    <td colspan="1">78.家庭月收入（元）：</td>
</tr>
<tr>
    <td colspan="1">79.个人月支出（元/月）：</td>
</tr>
<tr>
    <td  colspan="1" style="text-align: center">其他信息</td>
</tr>
<tr>
    <td  colspan="1">提供的申请材料：客户身份证正面、客户身份证背面、客户现场照片、手术确认书、银行卡正面、征信授权书</td>
</tr>
<tr>
    <td colspan="1">产品附录： </td>
</tr>
<tr>
    <td colspan="1">是否已签署授权书：是</td>
</tr>
<tr>
    <td  colspan="1">
        本人具有完全民事行为能力签署本申请表并承担相应责任。本人已仔细阅读并完全了解《分期购消费贷款三方协议》，并且自愿遵守相关的合同规定。<br/>
        本人保证在此表上填写、确认的内容及向贷款人、佰仟金融提供的所有相关资料全部真实、有效，如本人提供虚假资料，将承担由此引起的一切责任及损失。本人在此同意贷款人和佰仟金融按照相关法律法规的规定，向有权机构报送本人的个人信用信息，包括但不限于本申请表项下的信息，同时，授权贷款人和佰仟金融向任何可能的来源查询本人及配偶的与贷款申请有关的个人信息和个人信用信息，相关查询结果的用途将用于贷款审查和贷款管理。<br/>
        本人同意如因单方面取消申请或不具备借款条件，申请不获批准，本表格及所有已提交的资料无须退还本人，由佰仟金融处理。贷款人有权拒绝本贷款申请而无须给予任何原因解释。
    </td>
</tr>