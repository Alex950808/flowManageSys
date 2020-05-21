var offsetWid = document.documentElement.clientWidth;
var offsetHei = document.documentElement.clientHeight;
$(".bg").height(offsetHei);
$(".bg").width(offsetWid);
let headersToken=sessionStorage.getItem("token");
// $(".notData").height(offsetHei);
// $(".notData").width(offsetWid); 

var url = location.search; //获取url中"?"符后的字串 
var params = url.split("&")
var sn;
var cut_other_value;
var usdCnyRate;
var freight;
var marginRate;
var freightVal;
var timeVal;
for(var i=0;i<params.length;i++){
    if(params[i].indexOf("sn")>-1){
        sn = params[i].split("=")[1];
    }
    if(params[i].indexOf("cut_other_value")>-1){
        cut_other_value = params[i].split("=")[1];
    }
    if(params[i].indexOf("usdCnyRate")>-1){
        usdCnyRate = params[i].split("=")[1];
    }
    if(params[i].indexOf("freight")>-1){
        freight = params[i].split("=")[1];
    }
    if(params[i].indexOf("marginRate")>-1){
        marginRate = params[i].split("=")[1];
    }
    if(params[i].indexOf("freig_Val")>-1){
        freightVal = params[i].split("=")[1];
    }
    if(params[i].indexOf("timeVal")>-1){
        timeVal = params[i].split("=")[1];
    }
}
//记录输入修改折扣的值
let editInpVal = '';
getData() 
function getData(){
    $(".content_b").html('');
    $(".promptCon").html('正在加载中!!!');
    $(".notData_b").fadeIn();
    if(freightVal=="undefined"){
        freightVal=''
    }
    $.ajax({
        url: bastURL+'Goods/getGoodsFinalDiscount',
        type: "POST",
        async: true,
        cache: false,
        headers:{'Authorization': 'Bearer '+headersToken,'Accept':'application/vnd.jmsapi.v2+json'},
        data: {
            "query_sn":sn,
            "usd_cny_rate":usdCnyRate,
            "freight":freight,
            "margin_rate":marginRate,
            "freight_type":freightVal,
            "predict_pot_time":timeVal,
            "is_detail":1,
            "cut_other_value":cut_other_value,
        }, 
        success: function(res) {
            if(res.code=='1000'){
                var conStr='';
                for(var i = 0;i<res.data.length;i++){
                    conStr+='<div class="goods_T">'+
                                '<span class="goods_title D_I_B">商品信息</span>'+
                            '</div>'
                    let imgStr;
                    if(res.data[i].spec_img!=''){
                        imgStr='<img src="'+imgURL+res.data[i].spec_img+'"/>';
                    }else{
                        imgStr='<img src="./image/notImg.png"/>';
                    }
                    conStr+='<div class="goodsTit_img">'+
                                '<div class="goodsTit_img_lef">'+imgStr+'</div>'+
                                '<div class="goodsTit_img_rig">'+
                                    '<div class="goodsTit_name">'+res.data[i].goods_name+'</div>'+
                                    '<div class="goodsTit_specPrice">'+
                                        '<span class="goods_left D_I_B" style="width:40%">美金原价</span>'+
                                        '<span class="goods_content D_I_B" style="width:50%">&nbsp;&nbsp;$'+res.data[i].spec_price+'</span>'+
                                        '<span class="goods_right D_I_B" style="width:10%" onclick="seeCharts(\''+res.data[i].erp_prd_no+'\')"><img src="./image/tubiao.png"/></span>'+
                                    '</div>'+
                                    '<div class="goodsTit_specPrice">'+
                                        '<span class="D_I_B redColor" style="font-size: 10px;">'+res.data[i].spec_price_update_info+'</span>'+
                                    '</div>'+
                                    '<div class="goodsTit_specPrice">'+
                                        '<span class="goods_left D_I_B" style="width:40%">商品重量</span>'+
                                        '<span class="goods_content D_I_B">&nbsp;&nbsp;'+res.data[i].spec_weight+'KG</span>'+
                                    '</div>'+
                                '</div>'+
                            '</div>'
                    conStr+='<div class="goods_info" style="background-color: #dcddde;">'+
                                '<span class="goods_left D_I_B">&nbsp;&nbsp;&nbsp;商品规格码</span>'+
                                '<sppan class="goods_content D_I_B">'+res.data[i].spec_sn+'</sppan>'+
                                '<span class="goods_right D_I_B" onclick="Copy(\''+res.data[i].spec_sn+'\')"><img src="./image/audit.png"/></span>'+
                            '</div>'+
                            '<div class="goods_info" style="background-color: #dcddde;">'+
                                '<span class="goods_left D_I_B">&nbsp;&nbsp;&nbsp;商家编码</span>'+
                                '<sppan class="goods_content D_I_B">'+res.data[i].erp_merchant_no+'</sppan>'+
                                '<span class="goods_right D_I_B" onclick="Copy(\''+res.data[i].erp_merchant_no+'\')"><img src="./image/audit.png"/></span>'+
                            '</div>'+
                            '<div class="goods_info" style="background-color: #dcddde;">'+
                                '<span class="goods_left D_I_B">&nbsp;&nbsp;&nbsp;商品代码</span>'+
                                '<sppan class="goods_content D_I_B">'+res.data[i].erp_prd_no+'</sppan>'+
                                '<span class="goods_right D_I_B" onclick="Copy(\''+res.data[i].erp_prd_no+'\')"><img src="./image/audit.png"/></span>'+
                            '</div>'+
                            '<div class="goods_info" style="background-color: #dcddde;">'+
                                '<span class="goods_left D_I_B">&nbsp;&nbsp;&nbsp;参考码</span>'+
                                '<sppan class="goods_content D_I_B">'+res.data[i].erp_ref_no+'</sppan>'+
                                '<span class="goods_right D_I_B" onclick="Copy(\''+res.data[i].erp_ref_no+'\')"><img src="./image/audit.png"/></span>'+
                            '</div>'
                            // '<div class="goods_info">'+
                            //     '<span class="goods_left D_I_B">美金原价</span>'+
                            //     '<sppan class="goods_content D_I_B">$'+res.data[i].spec_price+'</sppan>'+
                            //     '<span class="goods_right D_I_B" onclick="seeCharts(\''+res.data[i].erp_prd_no+'\')"><img src="./image/tubiao.png"/></span>'+
                            // '</div>'+
                            // '<div class="goods_info">'+
                            //     '<span class="goods_left D_I_B">商品重量</span>'+
                            //     '<sppan class="goods_content D_I_B">'+res.data[i].spec_weight+'KG</sppan>'+
                            // '</div>'
                    conStr+='<div class="Goods_D">'+
                                '<span class="goods_title D_I_B">运输信息</span>'+
                                // '<span class="D_I_B export" onclick="seeDetail('+i+',\''+res.data[i].spec_sn+'\')">导出报价图片</span>'+
                            '</div>'
                    conStr+='<div class="goods_info">'+
                                '<span class="goods_left D_I_B">运费</span>'+
                                '<sppan class="goods_content D_I_B">$'+res.data[i].freight+'</sppan>'+
                            '</div>'
                            if(freightVal!=""){
                                let freightStr = '';
                                if(freightVal==='0'){
                                    freightStr='无';
                                }else if(freightVal==='1'){
                                    freightStr='空运'
                                }else if(freightVal==='2'){
                                    freightStr='海运'
                                }
                                conStr+='<div class="goods_info">'+
                                            '<span class="goods_left D_I_B">运输方式</span>'+
                                            '<sppan class="goods_content D_I_B">'+freightStr+'</sppan>'+
                                        '</div>'   
                            }
                            if(timeVal!=''){
                                conStr+='<div class="goods_info">'+
                                            '<span class="goods_left D_I_B">预计到港时间</span>'+
                                            '<sppan class="goods_content D_I_B">'+timeVal+'</sppan>'+
                                        '</div>'   
                            }
                       
                    conStr+='<div class="goods_T">'+
                                '<span class="goods_title D_I_B">ERP库存</span>'+
                            '</div>'
                    if(res.data[i].stock_info!=undefined){
                        conStr+='<table class="kucun" style="margin-top: 20px;">'
                        let k = 0;
                        for(var j=0;j<res.data[i].stock_info.length/4;j++){
                            conStr+='<tr>'
                            if(k<res.data[i].stock_info.length){
                                conStr+='<td>'+res.data[i].stock_info[k].shop_name+'</td>'
                            }
                            if((k+1)<res.data[i].stock_info.length){
                                conStr+='<td>'+res.data[i].stock_info[k+1].shop_name+'</td>'
                            }else{
                                conStr+= '<td></td>'
                            }
                            
                            if((k+2)<res.data[i].stock_info.length){
                                conStr+='<td>'+res.data[i].stock_info[k+2].shop_name+'</td>'
                            }else{
                                conStr+= '<td></td>'
                            }
                            if((k+3)<res.data[i].stock_info.length){
                                conStr+='<td>'+res.data[i].stock_info[k+3].shop_name+'</td>'
                            }else{
                                conStr+= '<td></td>'
                            }
                            conStr+='</tr>'
    
                            conStr+='<tr style="font-weight: bold;">'
                            if(k<res.data[i].stock_info.length){
                                conStr+='<td>'+res.data[i].stock_info[k].stock+'</td>'
                            }
                            if((k+1)<res.data[i].stock_info.length){
                                conStr+='<td>'+res.data[i].stock_info[k+1].stock+'</td>'
                            }else{
                                conStr+= '<td></td>'
                            }
                            
                            if((k+2)<res.data[i].stock_info.length){
                                conStr+='<td>'+res.data[i].stock_info[k+2].stock+'</td>'
                            }else{
                                conStr+= '<td></td>'
                            }
                            if((k+3)<res.data[i].stock_info.length){
                                conStr+='<td>'+res.data[i].stock_info[k+3].stock+'</td>'
                            }else{
                                conStr+= '<td></td>'
                            }
                            conStr+='</tr>'
    
                            conStr+='<tr>'
                            if(k<res.data[i].stock_info.length){
                                conStr+='<td><input name="Fruit" type="checkbox" value="\''+res.data[i].stock_info[k].shop_id+'\'" /></td>'
                            }
                            if((k+1)<res.data[i].stock_info.length){
                                conStr+='<td><input name="Fruit" type="checkbox" value="\''+res.data[i].stock_info[k+1].shop_id+'\'" /></td>'
                            }else{
                                conStr+= '<td></td>'
                            }
                            
                            if((k+2)<res.data[i].stock_info.length){
                                conStr+='<td><input name="Fruit" type="checkbox" value="\''+res.data[i].stock_info[k+2].shop_id+'\'" /></td>'
                            }else{
                                conStr+= '<td></td>'
                            }
                            if((k+3)<res.data[i].stock_info.length){
                                conStr+='<td><input name="Fruit" type="checkbox" value="\''+res.data[i].stock_info[k+3].shop_id+'\'" /></td>'
                            }else{
                                conStr+= '<td></td>'
                            }
                            conStr+='</tr>'
                            k=k+4
                        }
                        conStr+='</table>'; 
                    }
                    
                    let nameStr = '';
                    let daicaiStr = '';
                    let xianhuoStr = '';
                    let qitaStr = '';
                    if(res.data[i].channel_name_info!=undefined){
                        for(var j=0;j<res.data[i].channel_name_info.length;j++){
                            // nameStr+= ' <th>'+res.data[i].channel_name_info[j].channels_name+'</th>'
                            nameStr+= ' <th class="position: relative;">'+res.data[i].channel_name_info[j].channels_name+
                                        '<br/><input onclick="switchImg(4,$(this))" class="Select" name="jichu" type="checkbox" value="\''
                                        +res.data[i].channel_name_info[j].channels_id+'\'" />'+
                                        '<img src="./image/d_delelt.png"/>'+
                                        '</th>'
                            daicaiStr+= ' <th class="position: relative;">'+res.data[i].channel_name_info[j].channels_name+
                                        '<br/><input onclick="switchImg(1,$(this))" class="Select" name="daicai" type="checkbox" value="\''
                                        +res.data[i].channel_name_info[j].channels_id+'\'" />'+
                                        '<img src="./image/d_delelt.png"/>'+
                                        '</th>'
                            xianhuoStr+= ' <th>'+res.data[i].channel_name_info[j].channels_name+
                                        '<br/><input onclick="switchImg(2,$(this))" class="Select" name="xianhuo" type="checkbox" value="\''
                                        +res.data[i].channel_name_info[j].channels_id+'\'" />'+
                                        '<img src="./image/x_delelt.png"/>'+
                                        '</th>'
                            qitaStr+= ' <th>'+res.data[i].channel_name_info[j].channels_name+
                                        '<br/><input onclick="switchImg(3,$(this))" class="Select" name="qita" type="checkbox" value="\''
                                        +res.data[i].channel_name_info[j].channels_id+'\'" />'+
                                        '<img src="./image/x_delelt.png"/>'+
                                        '</th>'
                        }
                    }
                    
                    if(res.data[i].channels_info!=undefined){
                        let waitOne_O = '';
                        let waitOne_T = '';
                        let waitOne_S = '';
                        let waitOne_F = '';

                        let waitTwo_O = '';
                        let waitTwo_T = '';
                        let waitTwo_S = '';
                        let waitTwo_F = '';

                        let waitThree_O = '';
                        let waitThree_T = '';
                        let waitThree_S = '';
                        let waitThree_F = '';

                        let waitFour_O = '';
                        let waitFour_T = '';
                        let waitFour_S = '';
                        let waitFour_F = '';

                        let Basics_O = '';
                        let Basics_T = '';
                        let Basics_S = '';

                        let Other_O = '';
                        let Other_T = '';
                        let Other_S = '';
                        let Other_F = '';
                        for(var j=0;j<res.data[i].channels_info.length;j++){
                            waitOne_O+= '<td>'+res.data[i].channels_info[j].cut_middle_discount+'</td>'
                            waitOne_T+= '<td>￥'+res.data[i].channels_info[j].cut_middle_cny_price+'</td>'
                            waitOne_S+= '<td>$'+res.data[i].channels_info[j].cut_middle_usd_price+'</td>'
                            waitOne_F+=  '<td>$'+res.data[i].channels_info[j].cut_middle_freight+'</td>'

                            waitTwo_O+= '<td>'+res.data[i].channels_info[j].cut_one_discount+'</td>'
                            waitTwo_T+= '<td>￥'+res.data[i].channels_info[j].cut_one_cny_price+'</td>'
                            waitTwo_S+= '<td>$'+res.data[i].channels_info[j].cut_one_usd_price+'</td>'
                            waitTwo_F+= '<td>$'+res.data[i].channels_info[j].cut_one_freight+'</td>'
                            
                            waitThree_O+= '<td>'+res.data[i].channels_info[j].cut_one_middle_discount+'</td>'
                            waitThree_T+= '<td>￥'+res.data[i].channels_info[j].cut_one_middle_cny_price+'</td>'
                            waitThree_S+= '<td>$'+res.data[i].channels_info[j].cut_one_middle_usd_price+'</td>'
                            waitThree_F+= '<td>$'+res.data[i].channels_info[j].cut_one_middle_freight+'</td>'

                            waitFour_O+= '<td>'+res.data[i].channels_info[j].cut_two_discount+'</td>'
                            waitFour_T+= '<td>￥'+res.data[i].channels_info[j].cut_two_cny_price+'</td>'
                            waitFour_S+= '<td>$'+res.data[i].channels_info[j].cut_two_usd_price+'</td>'
                            waitFour_F+= '<td>$'+res.data[i].channels_info[j].cut_two_freight+'</td>'

                            Basics_O+= '<td><img onclick="modifyDiscount(\''+res.data[i].channels_info[j].channels_id+'\')" style="width: 18%;" src="./image/modify.png"/>&nbsp;&nbsp;'+res.data[i].channels_info[j].cost_discount+'</td>'
                            Basics_T+= '<td>￥'+res.data[i].channels_info[j].cost_cny_price+'</td>'
                            Basics_S+= '<td>$'+res.data[i].channels_info[j].cost_usd_price+'</td>'
                            
                            if(cut_other_value!=''){
                                Other_O+= '<td>'+res.data[i].channels_info[j].cut_other_discount+'</td>'
                                Other_T+= '<td>'+res.data[i].channels_info[j].cut_other_cny_price+'</td>'
                                Other_S+= '<td>'+res.data[i].channels_info[j].cut_other_usd_price+'</td>'
                                Other_F+= '<td>$'+res.data[i].channels_info[j].cut_other_freight+'</td>'
                            }
                            
                        }
                        conStr+='<div class="goods_T">'+
                                    '<span class="goods_title D_I_B">报价折扣-大批发</span>'+
                                '</div>'+
                                '<div class="goods_T">'+
                                    '<span class="goods_class D_I_B">基础折扣</span>'+
                                '</div>'
                        conStr+='<div class="table_div_wrap" style="background-color: #fff5e2;">'+
                                    '<div class="inner">'+
                                        '<div class="table_thead">'+
                                            '<table cellpadding="0" cellspacing="0">'+
                                                '<thead>'+
                                                '<tr>'+
                                                    '<th>渠道</th><th>渠道</th>'+nameStr+
                                                '</tr>'+
                                                '</thead>'+
                                            '</table>'+
                                        '</div>'+
                                        '<div class="table_tbody">'+
                                            '<table cellpadding="0" cellspacing="0">'+
                                                '<tbody>'+
                                                '<tr>'+
                                                    '<td>1</td><td>1</td>'+Basics_O+
                                                '</tr>'+
                                                '<tr>'+
                                                    '<td>1</td><td>1</td>'+Basics_T+
                                                '</tr>'+
                                                '<tr>'+
                                                    '<td>1</td><td>1</td>'+Basics_S+
                                                '</tr>'+
                                                '</tbody>'+
                                            '</table>'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="outer" style="background-color: #fff5e2;">'+
                                        '<div class="table_thead">'+
                                            '<table cellpadding="0" cellspacing="0">'+
                                                '<thead>'+
                                                '<tr>'+
                                                    '<th>渠道</th>'+
                                                    '<span class="qudao">渠道</span>'+
                                                '</tr>'+
                                                '</thead>'+
                                            '</table>'+
                                        '</div>'+
                                        '<div class="table_tbody">'+
                                            '<table cellpadding="0" cellspacing="0">'+
                                                '<tbody>'+
                                                '<tr>'+
                                                    '<td style="height: 96px;">exw折扣'+
                                                    '<br/><input onclick="rowSelect($(this))" name="jichuDis" type="checkbox" value="cut_middle"/>'+
                                                    '</td>'+
                                                '</tr>'+
                                            '</table>'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="outer" style="background-color: #fff5e2;left: 71px;">'+
                                        '<div>'+
                                            '<table cellpadding="0" cellspacing="0">'+
                                                '<thead>'+
                                                '<tr>'+
                                                    '<th>渠道</th>'+
                                                '</tr>'+
                                                '</thead>'+
                                            '</table>'+
                                        '</div>'+
                                        '<div>'+
                                            '<table cellpadding="0" cellspacing="0">'+
                                                '<tbody>'+
                                                '<tr>'+
                                                    '<td>折扣</td>'+
                                                '</tr>'+
                                                '<tr>'+
                                                    '<td>人民币</td>'+
                                                '</tr>'+
                                                '<tr>'+
                                                    '<td>美金</td>'+
                                                '</tr>'+
                                            '</table>'+
                                        '</div>'+
                                    '</div>'+
                                '</div>'
                        conStr+='<div class="goods_T">'+
                                    '<span class="goods_class D_I_B">代采折扣</span>'+
                                '</div>'
                        conStr+='<div class="table_div_wrap" style="background-color: #ffdbdb;">'+
                                    '<div class="inner">'+
                                        '<div class="table_thead">'+
                                            '<table cellpadding="0" cellspacing="0">'+
                                                '<thead>'+
                                                '<tr>'+
                                                    '<th>渠道</th><th>渠道</th>'+daicaiStr+
                                                '</tr>'+
                                                '</thead>'+
                                            '</table>'+
                                        '</div>'+
                                        '<div class="table_tbody">'+
                                            '<table cellpadding="0" cellspacing="0">'+
                                                '<tbody>'+
                                                '<tr>'+
                                                    '<td>1</td><td>1</td>'+waitOne_O+
                                                '</tr>'+
                                                '<tr>'+
                                                    '<td>1</td><td>1</td>'+waitOne_F+
                                                '</tr>'+
                                                '<tr>'+
                                                    '<td>1</td><td>1</td>'+waitOne_T+
                                                '</tr>'+
                                                '<tr>'+
                                                    '<td>1</td><td>1</td>'+waitOne_S+
                                                '</tr>'+
                                                '<tr>'+
                                                    '<td>1</td><td>1</td>'+waitTwo_O+
                                                '</tr>'+
                                                '<tr>'+
                                                    '<td>1</td><td>1</td>'+waitTwo_F+
                                                '</tr>'+
                                                '<tr>'+
                                                    '<td>1</td><td>1</td>'+waitTwo_T+
                                                '</tr>'+
                                                '<tr>'+
                                                    '<td>1</td><td>1</td>'+waitTwo_S+
                                                '</tr>'+
                                                '</tbody>'+
                                            '</table>'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="outer" style="background-color: #ffdbdb;">'+
                                        '<div class="table_thead">'+
                                            '<table cellpadding="0" cellspacing="0">'+
                                                '<thead>'+
                                                '<tr>'+
                                                    '<th style="height: 60px;">渠道</th>'+
                                                    '<span class="daicaiqudao">渠道</span>'+
                                                '</tr>'+
                                                '</thead>'+
                                            '</table>'+
                                        '</div>'+
                                        '<div class="table_tbody">'+
                                            '<table cellpadding="0" cellspacing="0">'+
                                                '<tbody>'+
                                                '<tr>'+
                                                    '<td style="height: 127px;">exw折扣+<br/>0.5%'+
                                                    '<br/><input onclick="rowSelect($(this))" name="cut_middle" type="checkbox" value="cut_middle"/>'+
                                                    // '<img src="./image/d_delelt.png"/>'+
                                                    '</td>'+
                                                '</tr>'+
                                                '<tr>'+
                                                    '<td style="height: 127px;">exw折扣+<br/>1%'+
                                                    '<br/><input onclick="rowSelect($(this))" name="cut_one" type="checkbox" value="cut_one"/>'+
                                                    // '<img src="./image/d_delelt.png"/>'+
                                                    '</td>'+
                                                '</tr>'+
                                                '</tbody>'+
                                            '</table>'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="outer" style="background-color: #ffdbdb;left: 71px;">'+
                                        '<div>'+
                                            '<table cellpadding="0" cellspacing="0">'+
                                                '<thead>'+
                                                '<tr>'+
                                                    '<th style="height: 60px;">渠道</th>'+
                                                '</tr>'+
                                                '</thead>'+
                                            '</table>'+
                                        '</div>'+
                                        '<div>'+
                                            '<table cellpadding="0" cellspacing="0">'+
                                                '<tbody>'+
                                                '<tr>'+
                                                    '<td>折扣</td>'+
                                                '</tr>'+
                                                '<tr>'+
                                                    '<td>运费</td>'+
                                                '</tr>'+
                                                '<tr>'+
                                                    '<td>人民币</td>'+
                                                '</tr>'+
                                                '<tr>'+
                                                    '<td>美金</td>'+
                                                '</tr>'+
                                                '<tr>'+
                                                    '<td>折扣</td>'+
                                                '</tr>'+
                                                '<tr>'+
                                                    '<td>运费</td>'+
                                                '</tr>'+
                                                '<tr>'+
                                                    '<td>人民币</td>'+
                                                '</tr>'+
                                                '<tr>'+
                                                    '<td>美金</td>'+
                                                '</tr>'+
                                            '</table>'+
                                        '</div>'+
                                    '</div>'+
                                '</div>'
                        conStr+='<div class="goods_T">'+
                                    '<span class="goods_class D_I_B">全包折扣</span>'+
                                '</div>'
                        conStr+='<div class="table_div_wrap" style="background-color: #e1f4fd;">'+
                                    '<div class="inner">'+
                                        '<div class="table_thead">'+
                                            '<table cellpadding="0" cellspacing="0">'+
                                                '<thead>'+
                                                '<tr>'+
                                                    '<th>渠道</th><th>渠道</th>'+xianhuoStr+
                                                '</tr>'+
                                                '</thead>'+
                                            '</table>'+
                                        '</div>'+
                                        '<div class="table_tbody">'+
                                            '<table cellpadding="0" cellspacing="0">'+
                                                '<tbody>'+
                                                '<tr>'+
                                                    '<td>1</td><td>1</td>'+waitThree_O+
                                                '</tr>'+
                                                '<tr>'+
                                                    '<td>1</td><td>1</td>'+waitThree_F+
                                                '</tr>'+
                                                '<tr>'+
                                                    '<td>1</td><td>1</td>'+waitThree_T+
                                                '</tr>'+
                                                '<tr>'+
                                                    '<td>1</td><td>1</td>'+waitThree_S+
                                                '</tr>'+
                                                '<tr>'+
                                                    '<td>1</td><td>1</td>'+waitFour_O+
                                                '</tr>'+
                                                '<tr>'+
                                                    '<td>1</td><td>1</td>'+waitFour_F+
                                                '</tr>'+
                                                '<tr>'+
                                                    '<td>1</td><td>1</td>'+waitFour_T+
                                                '</tr>'+
                                                '<tr>'+
                                                    '<td>1</td><td>1</td>'+waitFour_S+
                                                '</tr>'+
                                                '</tbody>'+
                                            '</table>'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="outer" style="background-color: #e1f4fd;">'+
                                        '<div class="table_thead">'+
                                            '<table cellpadding="0" cellspacing="0">'+
                                                '<thead>'+
                                                '<tr>'+
                                                    '<th style="height: 61px;">渠道</th>'+
                                                    '<span class="quanbaoqudao">渠道</span>'+
                                                '</tr>'+
                                                '</thead>'+
                                            '</table>'+
                                        '</div>'+
                                        '<div class="table_tbody">'+
                                            '<table cellpadding="0" cellspacing="0">'+
                                                '<tbody>'+
                                                '<tr>'+
                                                    '<td style="height: 127px;">代采+1.5%'+
                                                    '<br/><input onclick="rowSelect($(this))" name="cut_one_middle" type="checkbox" value="cut_middle"/>'+
                                                    // '<img src="./image/x_delelt.png"/>'+
                                                    '</td>'+
                                                '</tr>'+
                                                '<tr>'+
                                                    '<td style="height: 127px;">代采+2%'+
                                                    '<br/><input onclick="rowSelect($(this))" name="cut_two" type="checkbox" value="cut_middle"/>'+
                                                    // '<img src="./image/x_delelt.png"/>'+
                                                    '</td>'+
                                                '</tr>'+
                                                '</tbody>'+
                                            '</table>'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="outer" style="background-color: #e1f4fd;left: 71px;">'+
                                        '<div>'+
                                            '<table cellpadding="0" cellspacing="0">'+
                                                '<thead>'+
                                                '<tr>'+
                                                    '<th style="height: 60px;">渠道</th>'+
                                                '</tr>'+
                                                '</thead>'+
                                            '</table>'+
                                        '</div>'+
                                        '<div>'+
                                            '<table cellpadding="0" cellspacing="0">'+
                                                '<tbody>'+
                                                '<tr>'+
                                                    '<td>折扣</td>'+
                                                '</tr>'+
                                                '<tr>'+
                                                    '<td>运费</td>'+
                                                '</tr>'+
                                                '<tr>'+
                                                    '<td>人民币</td>'+
                                                '</tr>'+
                                                '<tr>'+
                                                    '<td>美金</td>'+
                                                '</tr>'+
                                                '<tr>'+
                                                    '<td>折扣</td>'+
                                                '</tr>'+
                                                '<tr>'+
                                                    '<td>运费</td>'+
                                                '</tr>'+
                                                '<tr>'+
                                                    '<td>人民币</td>'+
                                                '</tr>'+
                                                '<tr>'+
                                                    '<td>美金</td>'+
                                                '</tr>'+
                                            '</table>'+
                                        '</div>'+
                                    '</div>'+
                                '</div>'
                        if(cut_other_value!=''){
                            conStr+='<div class="goods_T">'+
                                        '<span class="goods_class D_I_B">其他折扣</span>'+
                                        '<span style="color:#949494;">&nbsp;&nbsp;&nbsp;选中即导出在图片中</span>'
                                        // '<input class="other_d"/>'+
                                    '</div>'
                            conStr+='<div class="table_div_wrap" style="background-color: #fff5e2;">'+
                                    '<div class="inner">'+
                                        '<div class="table_thead">'+
                                            '<table cellpadding="0" cellspacing="0">'+
                                                '<thead>'+
                                                '<tr>'+
                                                    '<th>渠道</th><th>渠道</th>'+qitaStr+
                                                '</tr>'+
                                                '</thead>'+
                                            '</table>'+
                                        '</div>'+
                                        '<div class="table_tbody">'+
                                            '<table cellpadding="0" cellspacing="0">'+
                                                '<tbody>'+
                                                '<tr>'+
                                                    '<td>1</td><td>1</td>'+Other_O+
                                                '</tr>'+
                                                '<tr>'+
                                                    '<td>1</td><td>1</td>'+Other_F+
                                                '</tr>'+
                                                '<tr>'+
                                                    '<td>1</td><td>1</td>'+Other_T+
                                                '</tr>'+
                                                '<tr>'+
                                                    '<td>1</td><td>1</td>'+Other_S+
                                                '</tr>'+
                                                '</tbody>'+
                                            '</table>'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="outer" style="background-color: #fff5e2;">'+
                                        '<div class="table_thead">'+
                                            '<table cellpadding="0" cellspacing="0">'+
                                                '<thead>'+
                                                '<tr>'+
                                                    '<th style="height: 61px;">渠道</th>'+
                                                    '<span class="qudao">渠道</span>'+
                                                    '</tr>'+
                                                '</thead>'+
                                            '</table>'+
                                        '</div>'+
                                        '<div class="table_tbody">'+
                                            '<table cellpadding="0" cellspacing="0">'+
                                                '<tbody>'+
                                                '<tr>'+
                                                    '<td style="height: 127px;">代采'+cut_other_value+'</td>'+
                                                '</tr>'+
                                            '</table>'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="outer" style="background-color: #fff5e2;left: 71px;">'+
                                        '<div>'+
                                            '<table cellpadding="0" cellspacing="0">'+
                                                '<thead>'+
                                                '<tr>'+
                                                    '<th style="height: 61px;">渠道</th>'+
                                                    '</tr>'+
                                                '</thead>'+
                                            '</table>'+
                                        '</div>'+
                                        '<div>'+
                                            '<table cellpadding="0" cellspacing="0">'+
                                                '<tbody>'+
                                                '<tr>'+
                                                    '<td>折扣</td>'+
                                                '</tr>'+
                                                '<tr>'+
                                                    '<td>运费</td>'+
                                                '</tr>'+
                                                '<tr>'+
                                                    '<td>人民币</td>'+
                                                '</tr>'+
                                                '<tr>'+
                                                    '<td>美金</td>'+
                                                '</tr>'+
                                            '</table>'+
                                        '</div>'+
                                    '</div>'+
                                '</div>'
                        }
                    }

                    
                    conStr+='<div class="T_A_C M_T_10 MB_Thirty search"><span class="D_I_B search" onclick="exportImg()">导出图片</span></div>'
                }
                $(".content_b").append(conStr)
                $(".notData_b").fadeOut();
                //基础折扣的滚动JS
                var table_tbody_T = document.getElementsByClassName("table_tbody")[0];
                var table_thead_T = document.getElementsByClassName("table_thead")[0];
                var table_tbody_outer_T = document.getElementsByClassName("table_tbody")[1].getElementsByTagName('table')[0];
                table_tbody_T.onscroll = function (e) {
                    table_thead_T.style.marginLeft = "-"+this.scrollLeft+"px"
                    table_tbody_outer_T.style.marginTop = "-"+this.scrollTop+'px'
                }
                //代采折扣的滚动JS
                var table_tbody = document.getElementsByClassName("table_tbody")[2];
                var table_thead = document.getElementsByClassName("table_thead")[2];
                var table_tbody_outer = document.getElementsByClassName("table_tbody")[3].getElementsByTagName('table')[0];
                table_tbody.onscroll = function (e) {
                    table_thead.style.marginLeft = "-"+this.scrollLeft+"px"
                    table_tbody_outer.style.marginTop = "-"+this.scrollTop+'px'
                }
                //准现货预定折扣滚动JS
                var table_tbody_S = document.getElementsByClassName("table_tbody")[4];
                var table_thead_S = document.getElementsByClassName("table_thead")[4];
                var table_tbody_outer_S = document.getElementsByClassName("table_tbody")[5].getElementsByTagName('table')[0];
                table_tbody_S.onscroll = function (e) {
                    table_thead_S.style.marginLeft = "-"+this.scrollLeft+"px"
                    table_tbody_outer_S.style.marginTop = "-"+this.scrollTop+'px'
                }
                //其他折扣
                var table_tbody_F = document.getElementsByClassName("table_tbody")[6];
                var table_thead_F = document.getElementsByClassName("table_thead")[6];
                var table_tbody_outer_F = document.getElementsByClassName("table_tbody")[7].getElementsByTagName('table')[0];
                table_tbody_F.onscroll = function (e) {
                    table_thead_F.style.marginLeft = "-"+this.scrollLeft+"px"
                    table_tbody_outer_F.style.marginTop = "-"+this.scrollTop+'px'
                }
            }else{
                $(".promptCon").html(res.msg);
                setTimeout(function(){
                    $(".notData_b").fadeOut();
                },2000)
            }
        }
    })
}

// function exportImg(){
//     // $("input name=[Fruit]")
//     let inputList = [];
//     $("input[name='Fruit']").each(function(){
//         // inputList.push($(this).prop("checked"))
//         if($(this).prop("checked")==true){
//             inputList.push($(this).val())
//         }
//     })
//     console.log(inputList)
//     // inputList.forEach(element=>{
//     //     console.log(element)
//     // })
// }

//图片切换
function switchImg(state,onec){
    var thisOnce = onec.prop("checked");
    if(thisOnce){
        $(onec.siblings()[1]).attr("src","./image/Add.png");
        var index = onec.parent().index();
        onec.parent().css("background","#5a5a5a")
        onec.parent().parent().parent().parent().parent().siblings().children().children().children().each(function(){
            $(this).children("td").eq(index).css("background",'#5a5a5a');
        });
    }else{
        var index = onec.parent().index();
        if(state==1){
            onec.parent().css("background","#ffdbdb")
            $(onec.siblings()[1]).attr("src","./image/d_delelt.png")
        }else if(state==2){
            onec.parent().css("background","#e1f4fd")
            $(onec.siblings()[1]).attr("src","./image/x_delelt.png")
        }else if(state==3){
            onec.parent().css("background","#fff5e2")
            $(onec.siblings()[1]).attr("src","./image/d_delelt.png")
        }else if(state==4){
            onec.parent().css("background","#fff5e2")
            $(onec.siblings()[1]).attr("src","./image/d_delelt.png")
        }
        onec.parent().parent().parent().parent().parent().siblings().children().children().children().each(function(){
            if(state==1){
                $(this).children("td").eq(index).css("background",'#ffdbdb');
            }else if(state==2){
                $(this).children("td").eq(index).css("background",'#e1f4fd');
            }else if(state==3){
                $(this).children("td").eq(index).css("background",'#fff5e2');
            }else if(state==4){
                $(this).children("td").eq(index).css("background",'#fff5e2');
            }
        });
    }
}
//选中全行
function rowSelect(onec){
    var thisOnce = onec.prop("checked");
    let index = onec.parent().parent().index()
    if(thisOnce){
        $(onec.siblings()[1]).attr("src","./image/Add.png");
    }else{
        $(onec.siblings()[1]).attr("src","./image/d_delelt.png");
    }
    let thisTr = onec.parent().parent().parent().parent().parent().parent().siblings().children().eq(1).children().children().children()
    console.log(thisTr)
    console.log(index)
    if(index===0){
        onec.parent().parent().removeClass("backGrey")
        onec.parent().parent().siblings().addClass("backGrey");
        onec.parent().parent().siblings().children().children().eq(2).prop("checked",false)
        thisTr.eq(0).removeClass("backGrey")
        thisTr.eq(1).removeClass("backGrey")
        thisTr.eq(2).removeClass("backGrey")
        thisTr.eq(3).removeClass("backGrey")

        thisTr.eq(4).addClass("backGrey")
        thisTr.eq(5).addClass("backGrey")
        thisTr.eq(6).addClass("backGrey")
        thisTr.eq(7).addClass("backGrey")
    }else if(index===1){
        onec.parent().parent().removeClass("backGrey")
        onec.parent().parent().siblings().addClass("backGrey");
        onec.parent().parent().siblings().children().children().eq(2).prop("checked",false)
        thisTr.eq(0).addClass("backGrey")
        thisTr.eq(1).addClass("backGrey")
        thisTr.eq(2).addClass("backGrey")
        thisTr.eq(3).addClass("backGrey")

        thisTr.eq(4).removeClass("backGrey")
        thisTr.eq(5).removeClass("backGrey")
        thisTr.eq(6).removeClass("backGrey")
        thisTr.eq(7).removeClass("backGrey")
    }
    let thisInput = onec.parent().parent().children().children().eq(2).prop("checked");
    console.log(onec.parent().parent().children().children())
    if(!thisOnce&&!thisInput){
        onec.parent().parent().siblings().removeClass("backGrey");
        thisTr.eq(0).removeClass("backGrey")
        thisTr.eq(1).removeClass("backGrey")
        thisTr.eq(2).removeClass("backGrey")
        thisTr.eq(3).removeClass("backGrey")

        thisTr.eq(4).removeClass("backGrey")
        thisTr.eq(5).removeClass("backGrey")
        thisTr.eq(6).removeClass("backGrey")
        thisTr.eq(7).removeClass("backGrey")
    }
}

//导出图片
function exportImg(){
    let inputList = [];
    let inputDaiCai = [];
    let inputXianHuo = [];
    let inputQiTa = [];
    let inputJiChu = [];
    let kucun = '';
    let daicai = '';
    let xianhuo = '';
    let qita = '';
    //erp库存选中保存
    $("input[name='Fruit']").each(function(){
        if($(this).prop("checked")==true){
            inputList.push($(this).val())
        }
    })
    if(inputList!=''){
        kucun='stock_info:'+inputList.toString().replace(/[']/g,'');
    }
    //基础折扣选中保存
    $("input[name='jichu']").each(function(){
        if($(this).prop("checked")==false){
            inputJiChu.push($(this).val())
        }
    })
    //代采选中保存
    $("input[name='daicai']").each(function(){
        if($(this).prop("checked")==false){
            inputDaiCai.push($(this).val())
        }
    })
    //准现货选中保存
    $("input[name='xianhuo']").each(function(){
        if($(this).prop("checked")==false){
            inputXianHuo.push($(this).val())
        }
    })
    //其他选中保存
    $("input[name='qita']").each(function(){
        if($(this).prop("checked")==false){
            inputQiTa.push($(this).val())
        }
    })
    qita = "cut_other:"+inputQiTa.toString().replace(/[']/g,'');
    $(".promptCon").html('正在导出请稍后!!!');
    $(".notData_b").fadeIn();
        
    let cut_middle;
    let cut_one_middle;
    let cut_one;
    let cut_two;
    let jichu;
    if($("input[name='jichuDis']").prop("checked")==true){
        jichu = inputJiChu.toString().replace(/[']/g,'')
    }
    if($("input[name='cut_middle']").prop("checked")==true){
        cut_middle = inputDaiCai.toString().replace(/[']/g,'')
    }
    if($("input[name='cut_one_middle']").prop("checked")==true){
        cut_one_middle = inputXianHuo.toString().replace(/[']/g,'')
    }
    if($("input[name='cut_one']").prop("checked")==true){
        cut_one = inputDaiCai.toString().replace(/[']/g,'')
    }
    if($("input[name='cut_two']").prop("checked")==true){
        cut_two = inputXianHuo.toString().replace(/[']/g,'')
    }
    let inputVal = '';
    console.log(editInpVal)
    if(editInpVal!=''){
        inputVal = JSON.stringify([{"exw_modify":editInpVal,"channels_id":channelID}])
    }
    if(freight=="undefined"){
        freight='';
    }
    $.ajax({
        url: bastURL+'Goods/getGoodsFinalDiscount',
        type: "POST",
        async: true,
        cache: false,
        headers:{'Authorization': 'Bearer '+headersToken,'Accept':'application/vnd.jmsapi.v2+json'},
        data:{
            "query_sn":sn,
            "is_detail":"1",
            "cut_other_value":cut_other_value,
            "usd_cny_rate":usdCnyRate,
            "freight":freight,
            "margin_rate":marginRate,
            "freight_type":freightVal,
            "predict_pot_time":timeVal,
            "cut_other":inputQiTa.toString().replace(/[']/g,''),
            "stock_info":inputList.toString().replace(/[']/g,''),
            "cut_middle":cut_middle,
            "cut_one_middle":cut_one_middle,
            "cut_one":cut_one,
            "cut_two":cut_two,
            "cost":jichu,
            "exw_modify":inputVal,
        },
        success:function(res){
            setTimeout(function(){
                $(".notData_b").fadeOut();
            },2000)
            var imgsrc = imgURL+res.path;   
            var name = '测试';                         // 获取图片地址
            var a = document.createElement('a');          // 创建一个a节点插入的document
            var event = new MouseEvent('click')           // 模拟鼠标click点击事件
            a.download = '报价图片'                 // 设置a节点的download属性值
            a.href = imgsrc;                                 // 将图片的src赋值给a节点的href
            a.dispatchEvent(event)                        // 触发鼠标点击事件
        }
    })
}

let channelID;
//修改渠道折扣
function modifyDiscount(ID){
    $(".modifyDis_b").fadeIn();
    channelID = ID;
    console.log(ID)
}
//关闭修改渠道折扣弹出框
function paralyse(){
    $(".modifyDis_b").fadeOut();
}
//确定修改渠道折扣
function Determine(){
    editInpVal = $(".editDis").val();
    if(/^(0.\d+|0|1)$/.test(editInpVal)==false){
        $(".promptCon").html('请输入正确的折扣信息!!!');
        $(".modifyDis_b").fadeOut();
        $(".notData_b").fadeIn();
        setTimeout(function(){
            $(".notData_b").fadeOut();
        },2000)
        return false;
    }
    $(".content_b").html('');
    let inputVal = [{"exw_modify":editInpVal,"channels_id":channelID}];
    $(".promptCon").html('正在修改中请稍后!!!');
    $(".notData_b").fadeIn();
    $(".modifyDis_b").fadeOut();
    $.ajax({
        url: bastURL+'Goods/getGoodsFinalDiscount',
        type: "POST",
        async: true,
        cache: false,
        headers:{'Authorization': 'Bearer '+headersToken,'Accept':'application/vnd.jmsapi.v2+json'},
        data: {
            "query_sn":sn,
            "usd_cny_rate":usdCnyRate,
            "freight":freight,
            "margin_rate":marginRate,
            "is_detail":1,
            "cut_other_value":cut_other_value,
            // "exw_modify":[{"exw_modify":inputVal,"channels_id":"86"}]
            "exw_modify":JSON.stringify(inputVal),
        }, 
        success: function(res) {
            console.log(res)
            $(".modifyDis_b").fadeOut();
            if(res.code=='1000'){
                var conStr='';
                for(var i = 0;i<res.data.length;i++){
                    conStr+='<div class="goods_T">'+
                                '<span class="goods_title D_I_B">商品信息</span>'+
                            '</div>'
                    conStr+='<div class="goods_name">'+
                                '<span class="goods_left D_I_B">名称</span>'+
                                '<sppan class="goods_content D_I_B">'+res.data[i].goods_name+'</sppan>'+
                            '</div>'+
                            '<div class="goods_info">'+
                                '<span class="goods_left D_I_B">商品规格码</span>'+
                                '<sppan class="goods_content D_I_B" onclick="Copy(\''+res.data[i].spec_sn+'\')">'+res.data[i].spec_sn+'</sppan>'+
                                '<span class="goods_right D_I_B"><img src="./image/audit.png"/></span>'+
                            '</div>'+
                            '<div class="goods_info">'+
                                '<span class="goods_left D_I_B">商家编码</span>'+
                                '<sppan class="goods_content D_I_B" onclick="Copy(\''+res.data[i].erp_merchant_no+'\')">'+res.data[i].erp_merchant_no+'</sppan>'+
                                '<span class="goods_right D_I_B"><img src="./image/audit.png"/></span>'+
                            '</div>'+
                            '<div class="goods_info">'+
                                '<span class="goods_left D_I_B">商品代码</span>'+
                                '<sppan class="goods_content D_I_B" onclick="Copy(\''+res.data[i].erp_prd_no+'\')">'+res.data[i].erp_prd_no+'</sppan>'+
                                '<span class="goods_right D_I_B"><img src="./image/audit.png"/></span>'+
                            '</div>'+
                            '<div class="goods_info">'+
                                '<span class="goods_left D_I_B">参考码</span>'+
                                '<sppan class="goods_content D_I_B" onclick="Copy(\''+res.data[i].erp_ref_no+'\')">'+res.data[i].erp_ref_no+'</sppan>'+
                                '<span class="goods_right D_I_B"><img src="./image/audit.png"/></span>'+
                            '</div>'+
                            '<div class="goods_info">'+
                                '<span class="goods_left D_I_B">美金原价</span>'+
                                '<sppan class="goods_content D_I_B" onclick="seeCharts(\''+res.data[i].erp_prd_no+'\')">'+res.data[i].spec_price+'</sppan>'+
                                '<span class="goods_right D_I_B"><img src="./image/tubiao.png"/></span>'+
                            '</div>'+
                            '<div class="goods_info">'+
                                '<span class="goods_left D_I_B">商品重量</span>'+
                                '<sppan class="goods_content D_I_B">'+res.data[i].spec_weight+'</sppan>'+
                            '</div>'
                    conStr+='<div class="Goods_D">'+
                                '<span class="goods_title D_I_B">运输信息</span>'+
                                // '<span class="D_I_B export" onclick="seeDetail('+i+',\''+res.data[i].spec_sn+'\')">导出报价图片</span>'+
                            '</div>'
                    conStr+='<div class="goods_info">'+
                                '<span class="goods_left D_I_B">运费</span>'+
                                '<sppan class="goods_content D_I_B">'+res.data[i].freight+'</sppan>'+
                            '</div>'
                            if(freightVal!="undefined"){
                                let freightStr = '';
                                if(freightVal==='0'){
                                    freightStr='无';
                                }else if(freightVal==='1'){
                                    freightStr='空运'
                                }else if(freightVal==='2'){
                                    freightStr='海运'
                                }
                                conStr+='<div class="goods_info">'+
                                            '<span class="goods_left D_I_B">运输方式</span>'+
                                            '<sppan class="goods_content D_I_B">'+freightStr+'</sppan>'+
                                        '</div>'   
                            }
                            if(timeVal!=''){
                                conStr+='<div class="goods_info">'+
                                            '<span class="goods_left D_I_B">预计到港时间</span>'+
                                            '<sppan class="goods_content D_I_B">'+timeVal+'</sppan>'+
                                        '</div>'   
                            }
                       
                    conStr+='<div class="goods_T">'+
                                '<span class="goods_title D_I_B">ERP库存</span>'+
                            '</div>'
                    conStr+='<table class="kucun" style="margin-top: 20px;">'
                    let k = 0;
                    for(var j=0;j<res.data[i].stock_info.length/4;j++){
                        conStr+='<tr>'
                        if(k<res.data[i].stock_info.length){
                            conStr+='<td>'+res.data[i].stock_info[k].shop_name+'</td>'
                        }
                        if((k+1)<res.data[i].stock_info.length){
                            conStr+='<td>'+res.data[i].stock_info[k+1].shop_name+'</td>'
                        }else{
                            conStr+= '<td></td>'
                        }
                        
                        if((k+2)<res.data[i].stock_info.length){
                            conStr+='<td>'+res.data[i].stock_info[k+2].shop_name+'</td>'
                        }else{
                            conStr+= '<td></td>'
                        }
                        if((k+3)<res.data[i].stock_info.length){
                            conStr+='<td>'+res.data[i].stock_info[k+3].shop_name+'</td>'
                        }else{
                            conStr+= '<td></td>'
                        }
                        conStr+='</tr>'

                        conStr+='<tr style="font-weight: bold;">'
                        if(k<res.data[i].stock_info.length){
                            conStr+='<td>'+res.data[i].stock_info[k].stock+'</td>'
                        }
                        if((k+1)<res.data[i].stock_info.length){
                            conStr+='<td>'+res.data[i].stock_info[k+1].stock+'</td>'
                        }else{
                            conStr+= '<td></td>'
                        }
                        
                        if((k+2)<res.data[i].stock_info.length){
                            conStr+='<td>'+res.data[i].stock_info[k+2].stock+'</td>'
                        }else{
                            conStr+= '<td></td>'
                        }
                        if((k+3)<res.data[i].stock_info.length){
                            conStr+='<td>'+res.data[i].stock_info[k+3].stock+'</td>'
                        }else{
                            conStr+= '<td></td>'
                        }
                        conStr+='</tr>'

                        conStr+='<tr>'
                        if(k<res.data[i].stock_info.length){
                            conStr+='<td><input name="Fruit" type="checkbox" value="\''+res.data[i].stock_info[k].shop_id+'\'" /></td>'
                        }
                        if((k+1)<res.data[i].stock_info.length){
                            conStr+='<td><input name="Fruit" type="checkbox" value="\''+res.data[i].stock_info[k+1].shop_id+'\'" /></td>'
                        }else{
                            conStr+= '<td></td>'
                        }
                        
                        if((k+2)<res.data[i].stock_info.length){
                            conStr+='<td><input name="Fruit" type="checkbox" value="\''+res.data[i].stock_info[k+2].shop_id+'\'" /></td>'
                        }else{
                            conStr+= '<td></td>'
                        }
                        if((k+3)<res.data[i].stock_info.length){
                            conStr+='<td><input name="Fruit" type="checkbox" value="\''+res.data[i].stock_info[k+3].shop_id+'\'" /></td>'
                        }else{
                            conStr+= '<td></td>'
                        }
                        conStr+='</tr>'
                        k=k+4
                    }
                    conStr+='</table>';
                    let nameStr = '';
                    let daicaiStr = '';
                    let xianhuoStr = '';
                    let qitaStr = '';
                    for(var j=0;j<res.data[i].channel_name_info.length;j++){
                        // nameStr+= ' <th>'+res.data[i].channel_name_info[j].channels_name+'</th>'
                        nameStr+= ' <th class="position: relative;">'+res.data[i].channel_name_info[j].channels_name+
                                    '<br/><input onclick="switchImg(4,$(this))" class="Select" name="jichu" type="checkbox" value="\''
                                    +res.data[i].channel_name_info[j].channels_id+'\'" />'+
                                    '<img src="./image/d_delelt.png"/>'+
                                    '</th>'
                        daicaiStr+= ' <th class="position: relative;">'+res.data[i].channel_name_info[j].channels_name+
                                    '<br/><input onclick="switchImg(1,$(this))" class="Select" name="daicai" type="checkbox" value="\''
                                    +res.data[i].channel_name_info[j].channels_id+'\'" />'+
                                    '<img src="./image/d_delelt.png"/>'+
                                    '</th>'
                        xianhuoStr+= ' <th>'+res.data[i].channel_name_info[j].channels_name+
                                    '<br/><input onclick="switchImg(2,$(this))" class="Select" name="xianhuo" type="checkbox" value="\''
                                    +res.data[i].channel_name_info[j].channels_id+'\'" />'+
                                    '<img src="./image/x_delelt.png"/>'+
                                    '</th>'
                        qitaStr+= ' <th>'+res.data[i].channel_name_info[j].channels_name+
                                    '<br/><input onclick="switchImg(3,$(this))" class="Select" name="qita" type="checkbox" value="\''
                                    +res.data[i].channel_name_info[j].channels_id+'\'" />'+
                                    '<img src="./image/x_delelt.png"/>'+
                                    '</th>'
                    }

                    let waitOne_O = '';
                    let waitOne_T = '';
                    let waitOne_S = '';
                    let waitOne_F = '';

                    let waitTwo_O = '';
                    let waitTwo_T = '';
                    let waitTwo_S = '';
                    let waitTwo_F = '';

                    let waitThree_O = '';
                    let waitThree_T = '';
                    let waitThree_S = '';
                    let waitThree_F = '';

                    let waitFour_O = '';
                    let waitFour_T = '';
                    let waitFour_S = '';
                    let waitFour_F = '';

                    let Basics_O = '';
                    let Basics_T = '';
                    let Basics_S = '';

                    let Other_O = '';
                    let Other_T = '';
                    let Other_S = '';
                    let Other_F = '';
                    for(var j=0;j<res.data[i].channels_info.length;j++){
                        waitOne_O+= '<td>'+res.data[i].channels_info[j].cut_middle_discount+'</td>'
                        waitOne_T+= '<td>￥'+res.data[i].channels_info[j].cut_middle_cny_price+'</td>'
                        waitOne_S+= '<td>$'+res.data[i].channels_info[j].cut_middle_usd_price+'</td>'
                        waitOne_F+=  '<td>$'+res.data[i].channels_info[j].cut_middle_freight+'</td>'

                        waitTwo_O+= '<td>'+res.data[i].channels_info[j].cut_one_discount+'</td>'
                        waitTwo_T+= '<td>￥'+res.data[i].channels_info[j].cut_one_cny_price+'</td>'
                        waitTwo_S+= '<td>$'+res.data[i].channels_info[j].cut_one_usd_price+'</td>'
                        waitTwo_F+= '<td>$'+res.data[i].channels_info[j].cut_one_freight+'</td>'
                        
                        waitThree_O+= '<td>'+res.data[i].channels_info[j].cut_one_middle_discount+'</td>'
                        waitThree_T+= '<td>￥'+res.data[i].channels_info[j].cut_one_middle_cny_price+'</td>'
                        waitThree_S+= '<td>$'+res.data[i].channels_info[j].cut_one_middle_usd_price+'</td>'
                        waitThree_F+= '<td>$'+res.data[i].channels_info[j].cut_one_middle_freight+'</td>'

                        waitFour_O+= '<td>'+res.data[i].channels_info[j].cut_two_discount+'</td>'
                        waitFour_T+= '<td>￥'+res.data[i].channels_info[j].cut_two_cny_price+'</td>'
                        waitFour_S+= '<td>$'+res.data[i].channels_info[j].cut_two_usd_price+'</td>'
                        waitFour_F+= '<td>$'+res.data[i].channels_info[j].cut_two_freight+'</td>'

                        Basics_O+= '<td><img onclick="modifyDiscount(\''+res.data[i].channels_info[j].channels_id+'\')" style="width: 18%;" src="./image/modify.png"/>&nbsp;&nbsp;'+res.data[i].channels_info[j].cost_discount+'</td>'
                        Basics_T+= '<td>￥'+res.data[i].channels_info[j].cost_cny_price+'</td>'
                        Basics_S+= '<td>$'+res.data[i].channels_info[j].cost_usd_price+'</td>'
                        
                        if(cut_other_value!=''){
                            Other_O+= '<td>'+res.data[i].channels_info[j].cut_other_discount+'</td>'
                            Other_T+= '<td>'+res.data[i].channels_info[j].cut_other_cny_price+'</td>'
                            Other_S+= '<td>'+res.data[i].channels_info[j].cut_other_usd_price+'</td>'
                            Other_F+= '<td>$'+res.data[i].channels_info[j].cut_other_freight+'</td>'
                        }
                        
                    }
                    conStr+='<div class="goods_T">'+
                                '<span class="goods_title D_I_B">报价折扣-大批发</span>'+
                            '</div>'+
                            '<div class="goods_T">'+
                                '<span class="goods_class D_I_B">基础折扣</span>'+
                            '</div>'
                    conStr+='<div class="table_div_wrap" style="background-color: #fff5e2;">'+
                                '<div class="inner">'+
                                    '<div class="table_thead">'+
                                        '<table cellpadding="0" cellspacing="0">'+
                                            '<thead>'+
                                            '<tr>'+
                                                '<th>渠道</th><th>渠道</th>'+nameStr+
                                            '</tr>'+
                                            '</thead>'+
                                        '</table>'+
                                    '</div>'+
                                    '<div class="table_tbody">'+
                                        '<table cellpadding="0" cellspacing="0">'+
                                            '<tbody>'+
                                            '<tr>'+
                                                '<td>1</td><td>1</td>'+Basics_O+
                                            '</tr>'+
                                            '<tr>'+
                                                '<td>1</td><td>1</td>'+Basics_T+
                                            '</tr>'+
                                            '<tr>'+
                                                '<td>1</td><td>1</td>'+Basics_S+
                                            '</tr>'+
                                            '</tbody>'+
                                        '</table>'+
                                    '</div>'+
                                '</div>'+
                                '<div class="outer" style="background-color: #fff5e2;">'+
                                    '<div class="table_thead">'+
                                        '<table cellpadding="0" cellspacing="0">'+
                                            '<thead>'+
                                            '<tr>'+
                                                '<th>渠道</th>'+
                                                '<span class="qudao">渠道</span>'+
                                            '</tr>'+
                                            '</thead>'+
                                        '</table>'+
                                    '</div>'+
                                    '<div class="table_tbody">'+
                                        '<table cellpadding="0" cellspacing="0">'+
                                            '<tbody>'+
                                            '<tr>'+
                                                '<td style="height: 96px;">exw折扣'+
                                                '<br/><input onclick="rowSelect($(this))" name="jichuDis" type="checkbox" value="jichu"/>'+
                                                '</td>'+
                                            '</tr>'+
                                        '</table>'+
                                    '</div>'+
                                '</div>'+
                                '<div class="outer" style="background-color: #fff5e2;left: 71px;">'+
                                    '<div>'+
                                        '<table cellpadding="0" cellspacing="0">'+
                                            '<thead>'+
                                            '<tr>'+
                                                '<th>渠道</th>'+
                                            '</tr>'+
                                            '</thead>'+
                                        '</table>'+
                                    '</div>'+
                                    '<div>'+
                                        '<table cellpadding="0" cellspacing="0">'+
                                            '<tbody>'+
                                            '<tr>'+
                                                '<td>折扣</td>'+
                                            '</tr>'+
                                            '<tr>'+
                                                '<td>人民币</td>'+
                                            '</tr>'+
                                            '<tr>'+
                                                '<td>美金</td>'+
                                            '</tr>'+
                                        '</table>'+
                                    '</div>'+
                                '</div>'+
                            '</div>'
                    conStr+='<div class="goods_T">'+
                                '<span class="goods_class D_I_B">代采折扣</span>'+
                            '</div>'
                    conStr+='<div class="table_div_wrap" style="background-color: #ffdbdb;">'+
                                '<div class="inner">'+
                                    '<div class="table_thead">'+
                                        '<table cellpadding="0" cellspacing="0">'+
                                            '<thead>'+
                                            '<tr>'+
                                                '<th>渠道</th><th>渠道</th>'+daicaiStr+
                                            '</tr>'+
                                            '</thead>'+
                                        '</table>'+
                                    '</div>'+
                                    '<div class="table_tbody">'+
                                        '<table cellpadding="0" cellspacing="0">'+
                                            '<tbody>'+
                                            '<tr>'+
                                                '<td>1</td><td>1</td>'+waitOne_O+
                                            '</tr>'+
                                            '<tr>'+
                                                '<td>1</td><td>1</td>'+waitOne_F+
                                            '</tr>'+
                                            '<tr>'+
                                                '<td>1</td><td>1</td>'+waitOne_T+
                                            '</tr>'+
                                            '<tr>'+
                                                '<td>1</td><td>1</td>'+waitOne_S+
                                            '</tr>'+
                                            '<tr>'+
                                                '<td>1</td><td>1</td>'+waitTwo_O+
                                            '</tr>'+
                                            '<tr>'+
                                                '<td>1</td><td>1</td>'+waitTwo_F+
                                            '</tr>'+
                                            '<tr>'+
                                                '<td>1</td><td>1</td>'+waitTwo_T+
                                            '</tr>'+
                                            '<tr>'+
                                                '<td>1</td><td>1</td>'+waitTwo_S+
                                            '</tr>'+
                                            '</tbody>'+
                                        '</table>'+
                                    '</div>'+
                                '</div>'+
                                '<div class="outer" style="background-color: #ffdbdb;">'+
                                    '<div class="table_thead">'+
                                        '<table cellpadding="0" cellspacing="0">'+
                                            '<thead>'+
                                            '<tr>'+
                                                '<th style="height: 60px;">渠道</th>'+
                                                '<span class="daicaiqudao">渠道</span>'+
                                            '</tr>'+
                                            '</thead>'+
                                        '</table>'+
                                    '</div>'+
                                    '<div class="table_tbody">'+
                                        '<table cellpadding="0" cellspacing="0">'+
                                            '<tbody>'+
                                            '<tr>'+
                                                '<td style="height: 127px;">exw折扣+<br/>0.5%'+
                                                '<br/><input onclick="rowSelect($(this))" name="cut_middle" type="checkbox" value="cut_middle"/>'+
                                                // '<img src="./image/d_delelt.png"/>'+
                                                '</td>'+
                                            '</tr>'+
                                            '<tr>'+
                                                '<td style="height: 127px;">exw折扣+<br/>1%'+
                                                '<br/><input onclick="rowSelect($(this))" name="cut_one" type="checkbox" value="cut_one"/>'+
                                                // '<img src="./image/d_delelt.png"/>'+
                                                '</td>'+
                                            '</tr>'+
                                            '</tbody>'+
                                        '</table>'+
                                    '</div>'+
                                '</div>'+
                                '<div class="outer" style="background-color: #ffdbdb;left: 71px;">'+
                                    '<div>'+
                                        '<table cellpadding="0" cellspacing="0">'+
                                            '<thead>'+
                                            '<tr>'+
                                                '<th style="height: 60px;">渠道</th>'+
                                            '</tr>'+
                                            '</thead>'+
                                        '</table>'+
                                    '</div>'+
                                    '<div>'+
                                        '<table cellpadding="0" cellspacing="0">'+
                                            '<tbody>'+
                                            '<tr>'+
                                                '<td>折扣</td>'+
                                            '</tr>'+
                                            '<tr>'+
                                                '<td>运费</td>'+
                                            '</tr>'+
                                            '<tr>'+
                                                '<td>人民币</td>'+
                                            '</tr>'+
                                            '<tr>'+
                                                '<td>美金</td>'+
                                            '</tr>'+
                                            '<tr>'+
                                                '<td>折扣</td>'+
                                            '</tr>'+
                                            '<tr>'+
                                                '<td>运费</td>'+
                                            '</tr>'+
                                            '<tr>'+
                                                '<td>人民币</td>'+
                                            '</tr>'+
                                            '<tr>'+
                                                '<td>美金</td>'+
                                            '</tr>'+
                                        '</table>'+
                                    '</div>'+
                                '</div>'+
                            '</div>'
                    conStr+='<div class="goods_T">'+
                                '<span class="goods_class D_I_B">全包折扣</span>'+
                            '</div>'
                    conStr+='<div class="table_div_wrap" style="background-color: #e1f4fd;">'+
                                '<div class="inner">'+
                                    '<div class="table_thead">'+
                                        '<table cellpadding="0" cellspacing="0">'+
                                            '<thead>'+
                                            '<tr>'+
                                                '<th>渠道</th><th>渠道</th>'+xianhuoStr+
                                            '</tr>'+
                                            '</thead>'+
                                        '</table>'+
                                    '</div>'+
                                    '<div class="table_tbody">'+
                                        '<table cellpadding="0" cellspacing="0">'+
                                            '<tbody>'+
                                            '<tr>'+
                                                '<td>1</td><td>1</td>'+waitThree_O+
                                            '</tr>'+
                                            '<tr>'+
                                                '<td>1</td><td>1</td>'+waitThree_F+
                                            '</tr>'+
                                            '<tr>'+
                                                '<td>1</td><td>1</td>'+waitThree_T+
                                            '</tr>'+
                                            '<tr>'+
                                                '<td>1</td><td>1</td>'+waitThree_S+
                                            '</tr>'+
                                            '<tr>'+
                                                '<td>1</td><td>1</td>'+waitFour_O+
                                            '</tr>'+
                                            '<tr>'+
                                                '<td>1</td><td>1</td>'+waitFour_F+
                                            '</tr>'+
                                            '<tr>'+
                                                '<td>1</td><td>1</td>'+waitFour_T+
                                            '</tr>'+
                                            '<tr>'+
                                                '<td>1</td><td>1</td>'+waitFour_S+
                                            '</tr>'+
                                            '</tbody>'+
                                        '</table>'+
                                    '</div>'+
                                '</div>'+
                                '<div class="outer" style="background-color: #e1f4fd;">'+
                                    '<div class="table_thead">'+
                                        '<table cellpadding="0" cellspacing="0">'+
                                            '<thead>'+
                                            '<tr>'+
                                                '<th style="height: 61px;">渠道</th>'+
                                                '<span class="quanbaoqudao">渠道</span>'+
                                            '</tr>'+
                                            '</thead>'+
                                        '</table>'+
                                    '</div>'+
                                    '<div class="table_tbody">'+
                                        '<table cellpadding="0" cellspacing="0">'+
                                            '<tbody>'+
                                            '<tr>'+
                                                '<td style="height: 127px;">代采+1.5%'+
                                                '<br/><input onclick="rowSelect($(this))" name="cut_one_middle" type="checkbox" value="cut_middle"/>'+
                                                // '<img src="./image/x_delelt.png"/>'+
                                                '</td>'+
                                            '</tr>'+
                                            '<tr>'+
                                                '<td style="height: 127px;">代采+2%'+
                                                '<br/><input onclick="rowSelect($(this))" name="cut_two" type="checkbox" value="cut_middle"/>'+
                                                // '<img src="./image/x_delelt.png"/>'+
                                                '</td>'+
                                            '</tr>'+
                                            '</tbody>'+
                                        '</table>'+
                                    '</div>'+
                                '</div>'+
                                '<div class="outer" style="background-color: #e1f4fd;left: 71px;">'+
                                    '<div>'+
                                        '<table cellpadding="0" cellspacing="0">'+
                                            '<thead>'+
                                            '<tr>'+
                                                '<th style="height: 60px;">渠道</th>'+
                                            '</tr>'+
                                            '</thead>'+
                                        '</table>'+
                                    '</div>'+
                                    '<div>'+
                                        '<table cellpadding="0" cellspacing="0">'+
                                            '<tbody>'+
                                            '<tr>'+
                                                '<td>折扣</td>'+
                                            '</tr>'+
                                            '<tr>'+
                                                '<td>运费</td>'+
                                            '</tr>'+
                                            '<tr>'+
                                                '<td>人民币</td>'+
                                            '</tr>'+
                                            '<tr>'+
                                                '<td>美金</td>'+
                                            '</tr>'+
                                            '<tr>'+
                                                '<td>折扣</td>'+
                                            '</tr>'+
                                            '<tr>'+
                                                '<td>运费</td>'+
                                            '</tr>'+
                                            '<tr>'+
                                                '<td>人民币</td>'+
                                            '</tr>'+
                                            '<tr>'+
                                                '<td>美金</td>'+
                                            '</tr>'+
                                        '</table>'+
                                    '</div>'+
                                '</div>'+
                            '</div>'
                    if(cut_other_value!=''){
                        conStr+='<div class="goods_T">'+
                                    '<span class="goods_class D_I_B">其他折扣</span>'+
                                    '<span style="color:#949494;">&nbsp;&nbsp;&nbsp;选中即导出在图片中</span>'
                                    // '<input class="other_d"/>'+
                                '</div>'
                        conStr+='<div class="table_div_wrap" style="background-color: #fff5e2;">'+
                                '<div class="inner">'+
                                    '<div class="table_thead">'+
                                        '<table cellpadding="0" cellspacing="0">'+
                                            '<thead>'+
                                            '<tr>'+
                                                '<th>渠道</th>'+qitaStr+
                                            '</tr>'+
                                            '</thead>'+
                                        '</table>'+
                                    '</div>'+
                                    '<div class="table_tbody">'+
                                        '<table cellpadding="0" cellspacing="0">'+
                                            '<tbody>'+
                                            '<tr>'+
                                                '<td>1</td>'+Other_O+
                                            '</tr>'+
                                            '<tr>'+
                                                '<td>1</td>'+Other_F+
                                            '</tr>'+
                                            '<tr>'+
                                                '<td>1</td>'+Other_T+
                                            '</tr>'+
                                            '<tr>'+
                                                '<td>1</td>'+Other_S+
                                            '</tr>'+
                                            '</tbody>'+
                                        '</table>'+
                                    '</div>'+
                                '</div>'+
                                '<div class="outer" style="background-color: #fff5e2;">'+
                                    '<div class="table_thead">'+
                                        '<table cellpadding="0" cellspacing="0">'+
                                            '<thead>'+
                                            '<tr>'+
                                                '<th style="height: 61px;">渠道</th>'+
                                            '</tr>'+
                                            '</thead>'+
                                        '</table>'+
                                    '</div>'+
                                    '<div class="table_tbody">'+
                                        '<table cellpadding="0" cellspacing="0">'+
                                            '<tbody>'+
                                            '<tr>'+
                                                '<td style="height: 127px;">代采'+cut_other_value+'</td>'+
                                            '</tr>'+
                                        '</table>'+
                                    '</div>'+
                                '</div>'+
                            '</div>'
                    }
                    conStr+='<div class="T_A_C M_T_10 MB_Thirty search"><span class="D_I_B search" onclick="exportImg()">导出图片</span></div>'
                }
                $(".content_b").append(conStr)
                $(".notData_b").fadeOut();
                //基础折扣的滚动JS
                var table_tbody_T = document.getElementsByClassName("table_tbody")[0];
                var table_thead_T = document.getElementsByClassName("table_thead")[0];
                var table_tbody_outer_T = document.getElementsByClassName("table_tbody")[1].getElementsByTagName('table')[0];
                table_tbody_T.onscroll = function (e) {
                    table_thead_T.style.marginLeft = "-"+this.scrollLeft+"px"
                    table_tbody_outer_T.style.marginTop = "-"+this.scrollTop+'px'
                }
                //代采折扣的滚动JS
                var table_tbody = document.getElementsByClassName("table_tbody")[2];
                var table_thead = document.getElementsByClassName("table_thead")[2];
                var table_tbody_outer = document.getElementsByClassName("table_tbody")[3].getElementsByTagName('table')[0];
                table_tbody.onscroll = function (e) {
                    table_thead.style.marginLeft = "-"+this.scrollLeft+"px"
                    table_tbody_outer.style.marginTop = "-"+this.scrollTop+'px'
                }
                //准现货预定折扣滚动JS
                var table_tbody_S = document.getElementsByClassName("table_tbody")[4];
                var table_thead_S = document.getElementsByClassName("table_thead")[4];
                var table_tbody_outer_S = document.getElementsByClassName("table_tbody")[5].getElementsByTagName('table')[0];
                table_tbody_S.onscroll = function (e) {
                    table_thead_S.style.marginLeft = "-"+this.scrollLeft+"px"
                    table_tbody_outer_S.style.marginTop = "-"+this.scrollTop+'px'
                }
                //其他折扣
                var table_tbody_F = document.getElementsByClassName("table_tbody")[6];
                var table_thead_F = document.getElementsByClassName("table_thead")[6];
                var table_tbody_outer_F = document.getElementsByClassName("table_tbody")[7].getElementsByTagName('table')[0];
                table_tbody_F.onscroll = function (e) {
                    table_thead_F.style.marginLeft = "-"+this.scrollLeft+"px"
                    table_tbody_outer_F.style.marginTop = "-"+this.scrollTop+'px'
                }
            }else{
                $(".promptCon").html(res.msg);
                setTimeout(function(){
                    $(".notData_b").fadeOut();
                },2000)
            }
        }
    })
    $(".editDis").val('');
}


function closes(once){
    once.parent().css("display"," none")
}


function docopy(text) {
    Clipboard.copy(text);
}
function seeCharts(sn){
    location.href='./chartsData.html?sn='+sn;
}
