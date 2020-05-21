
var offsetWid = document.documentElement.clientWidth;
var offsetHei = document.documentElement.clientHeight;
$(".bg").height(offsetHei);
$(".bg").width(offsetWid);
let headersToken=sessionStorage.getItem("token");
if(headersToken==null){
    location.href='./login.html';
    // return false;
}


var page = 1;
var total;


//滚动条到达页面底部自动加载下一页数据
$(window).scroll(function(){
    var scrollTop = $(this).scrollTop();
    var scrollHeight = $(document).height();
    var windowHeight = $(this).height();
    // var titleImgHeight = $(".titleImg").height();
    // if(scrollTop>titleImgHeight){
    //     $(".titleList").addClass("addClass")
    //     $(".aa").removeClass("zhanwei")
    // }else{
    //     $(".titleList").removeClass("addClass")
    //     $(".aa").addClass("zhanwei")
    // }
    if(scrollTop + windowHeight == scrollHeight){
        //当滚动条到底时,这里是触发内容
        //异步请求数据,局部刷新dom 
        page=page+1;
        if(page<=total){
            ajaxRequest()
        }
    }
});
let tabIndex = 0;


function search(){
    $(".content_b").html('');
    $(".promptCon").html('正在加载中!!!');
    $(".notData_b").fadeIn();
    page=1;
    let querySn = $(".goods").val();
    let usdCnyRate = $(".rmber").val();
    let freight;
    if($(".freightEr").val()==''){
        freight = 3;
    }else{
        freight = $(".freightEr").val();
    }
    
    let marginRate = $(".grossProfit").val();
    let freightVal = $("input[name='Fruit']:checked").val()
    let timeVal = $("input[name='user_date']").val()
    $.ajax({
        url: bastURL+'Goods/getGoodsFinalDiscount',
        type: "POST",
        async: true,
        cache: false,
        headers:{'Authorization': 'Bearer '+headersToken,'Accept':'application/vnd.jmsapi.v2+json'},
        data: {
            "query_sn":querySn,
            // "usd_cny_rate":usdCnyRate, 
            // "freight":freight,
            // "margin_rate":marginRate,
            // "freight_type":freightVal,
            // "predict_pot_time":timeVal,
            "is_detail":0,
            "is_page":1,
            "page":page,
            "page_size":5,
        }, 
        success: function(res) {
            if(res.code=='1000'){
                var conStr='';
                total=res.data.total;
                tabIndex=tabIndex+res.data.goods_final_list.data.length;
                // console.log(res.data.data.goods_list);
                for(var i = 0;i<res.data.goods_final_list.data.length;i++){
                    conStr+='<div class="Goods_D">'+
                                '<span class="goods_title D_I_B">商品信息</span>'+
                                '<span class="D_I_B export" onclick="seeDetail('+i+',\''+res.data.goods_final_list.data[i].spec_sn+'\')">导出报价图片</span>'+
                            '</div>'
                    let imgStr;
                    if(res.data.goods_final_list.data[i].spec_img!=''){
                        imgStr='<img src="'+imgURL+res.data.goods_final_list.data[i].spec_img+'"/>';
                    }else{
                        imgStr='<img src="./image/notImg.png"/>';
                    }
                    conStr+='<div class="goodsTit_img">'+
                                '<div class="goodsTit_img_lef">'+imgStr+'</div>'+
                                '<div class="goodsTit_img_rig">'+
                                    '<div class="goodsTit_name">'+res.data.goods_final_list.data[i].goods_name+'</div>'+
                                    '<div class="goodsTit_specPrice">'+
                                        '<span class="goods_left D_I_B" style="width:40%">美金原价</span>'+
                                        '<span class="goods_content D_I_B" style="width:50%">&nbsp;&nbsp;$'+res.data.goods_final_list.data[i].spec_price+'</span>'+
                                        '<span class="goods_right D_I_B" style="width:10%" onclick="seeCharts(\''+res.data.goods_final_list.data[i].erp_prd_no+'\')"><img src="./image/tubiao.png"/></span>'+
                                    '</div>'+
                                    '<div class="goodsTit_specPrice">'+
                                        '<span class="D_I_B redColor" style="font-size: 10px;">'+res.data.goods_final_list.data[i].spec_price_update_info+'</span>'+
                                    '</div>'+
                                    '<div class="goodsTit_specPrice">'+
                                        '<span class="goods_left D_I_B" style="width:40%">商品重量</span>'+
                                        '<span class="goods_content D_I_B">&nbsp;&nbsp;'+res.data.goods_final_list.data[i].spec_weight+'KG</span>'+
                                    '</div>'+
                                '</div>'+
                            '</div>'
                    conStr+='<div class="goods_info" style="background-color: #dcddde;">'+
                                '<span class="goods_left D_I_B">&nbsp;&nbsp;&nbsp;商品规格码</span>'+
                                '<sppan class="goods_content D_I_B">'+res.data.goods_final_list.data[i].spec_sn+'</sppan>'+
                                '<span class="goods_right D_I_B" onclick="Copy(\''+res.data.goods_final_list.data[i].spec_sn+'\')"><img src="./image/audit.png"/></span>'+
                            '</div>'+
                            '<div class="goods_info" style="background-color: #dcddde;">'+
                                '<span class="goods_left D_I_B">&nbsp;&nbsp;&nbsp;商家编码</span>'+
                                '<sppan class="goods_content D_I_B">'+res.data.goods_final_list.data[i].erp_merchant_no+'</sppan>'+
                                '<span class="goods_right D_I_B" onclick="Copy(\''+res.data.goods_final_list.data[i].erp_merchant_no+'\')"><img src="./image/audit.png"/></span>'+
                            '</div>'+
                            '<div class="goods_info" style="background-color: #dcddde;">'+
                                '<span class="goods_left D_I_B">&nbsp;&nbsp;&nbsp;商品代码</span>'+
                                '<sppan class="goods_content D_I_B">'+res.data.goods_final_list.data[i].erp_prd_no+'</sppan>'+
                                '<span class="goods_right D_I_B" onclick="Copy(\''+res.data.goods_final_list.data[i].erp_prd_no+'\')"><img src="./image/audit.png"/></span>'+
                            '</div>'+
                            '<div class="goods_info" style="background-color: #dcddde;">'+
                                '<span class="goods_left D_I_B">&nbsp;&nbsp;&nbsp;参考码</span>'+
                                '<sppan class="goods_content D_I_B">'+res.data.goods_final_list.data[i].erp_ref_no+'</sppan>'+
                                '<span class="goods_right D_I_B" onclick="Copy(\''+res.data.goods_final_list.data[i].erp_ref_no+'\')"><img src="./image/audit.png"/></span>'+
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
                                '<sppan class="goods_content D_I_B">$'+res.data.goods_final_list.data[i].freight+'</sppan>'+
                            '</div>'
                            if(freightVal!=undefined){
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
                    if(res.data.goods_final_list.data[i].stock_info!=undefined){
                        conStr+='<table class="kucun" style="margin-top: 20px;">'
                        let k = 0;
                        for(var j=0;j<res.data.goods_final_list.data[i].stock_info.length/4;j++){
                            conStr+='<tr>'
                            if(k<res.data.goods_final_list.data[i].stock_info.length){
                                conStr+='<td>'+res.data.goods_final_list.data[i].stock_info[k].shop_name+'</td>'
                            }
                            if((k+1)<res.data.goods_final_list.data[i].stock_info.length){
                                conStr+='<td>'+res.data.goods_final_list.data[i].stock_info[k+1].shop_name+'</td>'
                            }else{
                                conStr+= '<td></td>'
                            }
                            
                            if((k+2)<res.data.goods_final_list.data[i].stock_info.length){
                                conStr+='<td>'+res.data.goods_final_list.data[i].stock_info[k+2].shop_name+'</td>'
                            }else{
                                conStr+= '<td></td>'
                            }
                            if((k+3)<res.data.goods_final_list.data[i].stock_info.length){
                                conStr+='<td>'+res.data.goods_final_list.data[i].stock_info[k+3].shop_name+'</td>'
                            }else{
                                conStr+= '<td></td>'
                            }
                            conStr+='</tr>'

                            conStr+='<tr style="font-weight: bold;">'
                            if(k<res.data.goods_final_list.data[i].stock_info.length){
                                conStr+='<td>'+res.data.goods_final_list.data[i].stock_info[k].stock+'</td>'
                            }
                            if((k+1)<res.data.goods_final_list.data[i].stock_info.length){
                                conStr+='<td>'+res.data.goods_final_list.data[i].stock_info[k+1].stock+'</td>'
                            }else{
                                conStr+= '<td></td>'
                            }
                            
                            if((k+2)<res.data.goods_final_list.data[i].stock_info.length){
                                conStr+='<td>'+res.data.goods_final_list.data[i].stock_info[k+2].stock+'</td>'
                            }else{
                                conStr+= '<td></td>'
                            }
                            if((k+3)<res.data.goods_final_list.data[i].stock_info.length){
                                conStr+='<td>'+res.data.goods_final_list.data[i].stock_info[k+3].stock+'</td>'
                            }else{
                                conStr+= '<td></td>'
                            }
                            conStr+='</tr>'
                            k=k+4
                        }
                        conStr+='</table>';
                    }
                    let nameStr = '';
                    if(res.data.goods_final_list.data[i].channel_name_info!=undefined){
                        for(var j=0;j<res.data.goods_final_list.data[i].channel_name_info.length;j++){
                            nameStr+= ' <th>'+res.data.goods_final_list.data[i].channel_name_info[j].channels_name+'</th>'
                        }
                    }
                    
                    if(res.data.goods_final_list.data[i].channels_info!=undefined){
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

                        
                        for(var j=0;j<res.data.goods_final_list.data[i].channels_info.length;j++){
                            waitOne_O+= '<td>'+res.data.goods_final_list.data[i].channels_info[j].cut_middle_discount+'</td>'
                            waitOne_T+= '<td>￥'+res.data.goods_final_list.data[i].channels_info[j].cut_middle_cny_price+'</td>'
                            waitOne_S+= '<td>$'+res.data.goods_final_list.data[i].channels_info[j].cut_middle_usd_price+'</td>'
                            waitOne_F+=  '<td>运'+res.data.goods_final_list.data[i].channels_info[j].cut_middle_freight+'</td>'

                            waitTwo_O+= '<td>'+res.data.goods_final_list.data[i].channels_info[j].cut_one_discount+'</td>'
                            waitTwo_T+= '<td>￥'+res.data.goods_final_list.data[i].channels_info[j].cut_one_cny_price+'</td>'
                            waitTwo_S+= '<td>$'+res.data.goods_final_list.data[i].channels_info[j].cut_one_usd_price+'</td>'
                            waitTwo_F+= '<td>运'+res.data.goods_final_list.data[i].channels_info[j].cut_one_freight+'</td>'
                            
                            waitThree_O+= '<td>'+res.data.goods_final_list.data[i].channels_info[j].cut_one_middle_discount+'</td>'
                            waitThree_T+= '<td>￥'+res.data.goods_final_list.data[i].channels_info[j].cut_one_middle_cny_price+'</td>'
                            waitThree_S+= '<td>$'+res.data.goods_final_list.data[i].channels_info[j].cut_one_middle_usd_price+'</td>'
                            waitThree_F+= '<td>运'+res.data.goods_final_list.data[i].channels_info[j].cut_one_middle_freight+'</td>'

                            waitFour_O+= '<td>'+res.data.goods_final_list.data[i].channels_info[j].cut_two_discount+'</td>'
                            waitFour_T+= '<td>￥'+res.data.goods_final_list.data[i].channels_info[j].cut_two_cny_price+'</td>'
                            waitFour_S+= '<td>$'+res.data.goods_final_list.data[i].channels_info[j].cut_two_usd_price+'</td>'
                            waitFour_F+= '<td>运'+res.data.goods_final_list.data[i].channels_info[j].cut_two_freight+'</td>'

                            Basics_O+= '<td>'+res.data.goods_final_list.data[i].channels_info[j].cost_discount+'</td>'
                            Basics_T+= '<td>￥'+res.data.goods_final_list.data[i].channels_info[j].cost_cny_price+'</td>'
                            Basics_S+= '<td>$'+res.data.goods_final_list.data[i].channels_info[j].cost_usd_price+'</td>'

                            
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
                                                    // '<br/><input onclick="rowSelect($(this))" name="jichu" type="checkbox" value="cut_middle"/>'+
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
                                                    '<th>渠道</th><th>渠道</th>'+nameStr+
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
                                                    '<th>渠道</th>'+
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
                                                    // '<br/><input onclick="rowSelect($(this))" name="cut_middle" type="checkbox" value="cut_middle"/>'+
                                                    // '<img src="./image/d_delelt.png"/>'+
                                                    '</td>'+
                                                '</tr>'+
                                                '<tr>'+
                                                    '<td style="height: 127px;">exw折扣+<br/>1%'+
                                                    // '<br/><input onclick="rowSelect($(this))" name="cut_one" type="checkbox" value="cut_one"/>'+
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
                                                    '<th>渠道</th><th>渠道</th>'+nameStr+
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
                                                    '<th>渠道</th>'+
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
                                                    // '<br/><input onclick="rowSelect($(this))" name="cut_one_middle" type="checkbox" value="cut_middle"/>'+
                                                    // '<img src="./image/x_delelt.png"/>'+
                                                    '</td>'+
                                                '</tr>'+
                                                '<tr>'+
                                                    '<td style="height: 127px;">代采+2%'+
                                                    // '<br/><input onclick="rowSelect($(this))" name="cut_two" type="checkbox" value="cut_middle"/>'+
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
                                    '<span class="goods_class D_I_B">其他折扣</span>&nbsp;&nbsp;'+
                                    '<input class="other_d other_d'+i+'"/>&nbsp;&nbsp;'+
                                    '<span class="sousuo" onclick="discountSearch($(this),\''+res.data.goods_final_list.data[i].spec_sn+'\','+i+')">搜索</span>'+
                                '</div>'
                        conStr+='<div class="table_div_wrap" style="background-color: #fff5e2;display: none">'+
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
                                            '<tr class="aaa'+i+'">'+
                                                // '<td>1</td>'+Other_O+
                                            '</tr>'+
                                            '<tr class="eee'+i+'">'+
                                                // '<td>1</td>'+Other_O+
                                            '</tr>'+
                                            '<tr class="bbb'+i+'">'+
                                                // '<td>1</td>'+Other_O+
                                            '</tr>'+
                                            '<tr class="ccc'+i+'">'+
                                                // '<td>1</td>'+Other_O+
                                            '</tr>'+
                                            '</tbody>'+
                                        '</table>'+
                                    '</div>'+
                                '</div>'+
                                // '<div class="outer" style="background-color: #fff5e2;">'+
                                //     '<div class="table_thead">'+
                                //         '<table cellpadding="0" cellspacing="0">'+
                                //             '<thead>'+
                                //             '<tr>'+
                                //                 '<th>渠道</th>'+
                                //             '</tr>'+
                                //             '</thead>'+
                                //         '</table>'+
                                //     '</div>'+
                                //     '<div class="table_tbody">'+
                                //         '<table cellpadding="0" cellspacing="0">'+
                                //             '<tbody>'+
                                //             '<tr class="ddd'+i+'">'+
                                //                 // '<td style="height: 96px;">代采+</td>'+
                                //             '</tr>'+
                                //         '</table>'+
                                //     '</div>'+
                                // '</div>'+
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
                                            '<tr class="ddd'+i+'">'+
                                                // '<td style="height: 96px;">代采+</td>'+
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
                $(".content_b").append(conStr)
                $(".notData_b").fadeOut();
                if(res.data.goods_final_list.data.length===0){
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
                    var table_tbody = [];
                    var table_thead = [];
                    var table_tbody_outer = []
                    for(var i = 0;i<res.data.goods_final_list.data.length*4;i++){
                        table_tbody.push("table_tbody"+i)
                        table_thead.push("table_thead"+i)
                        table_tbody_outer.push("table_tbody_outer"+i)
                    }
                    for(var i = 0;i<res.data.goods_final_list.data.length*4;i++){
                        if(i==0){
                            table_tbody[i] = document.getElementsByClassName("table_tbody")[0];
                            table_thead[i] = document.getElementsByClassName("table_thead")[0];
                            table_tbody_outer[i] = document.getElementsByClassName("table_tbody")[1].getElementsByTagName('table')[0];
                            let aaa = table_thead[i]
                            table_tbody[i].onscroll = function (e) {
                                aaa.style.marginLeft = "-"+this.scrollLeft+"px"
                                //此处用于控制顶行不动，上下滚动
                                // table_tbody_outer[i].style.marginTop = "-"+this.scrollTop+'px'
                            }
                        }else{
                            //基础折扣的滚动JS
                            table_tbody[i] = document.getElementsByClassName("table_tbody")[i*2];
                            table_thead[i] = document.getElementsByClassName("table_thead")[i*2];
                            table_tbody_outer[i] = document.getElementsByClassName("table_tbody")[i*2+1].getElementsByTagName('table')[0];
                            let bbb = table_thead[i]
                            table_tbody[i].onscroll = function (e) {
                                bbb.style.marginLeft = "-"+this.scrollLeft+"px"
                                //此处用于控制顶行不动，上下滚动
                                // table_tbody_outer[i].style.marginTop = "-"+this.scrollTop+'px'
                            }
                        }
                        
                    }
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

function ajaxRequest(){
    // $(".content_b").html('');
    $(".promptCon").html('正在加载中!!!');
    $(".notData_b").fadeIn();
    let querySn = $(".goods").val();
    let usdCnyRate = $(".rmber").val();
    let freight;
    if($(".freightEr").val()==''){
        freight = 3;
    }else{
        freight = $(".freightEr").val();
    }
    let marginRate = $(".grossProfit").val();
    let freightVal = $("input[name='Fruit']:checked").val()
    let timeVal = $("input[name='user_date']").val()
    $.ajax({
        url: bastURL+'Goods/getGoodsFinalDiscount',
        type: "POST",
        async: true,
        cache: false,
        headers:{'Authorization': 'Bearer '+headersToken,'Accept':'application/vnd.jmsapi.v2+json'},
        data: {
            "query_sn":querySn,
            "usd_cny_rate":usdCnyRate,
            "freight":freight,
            "margin_rate":marginRate,
            "freight_type":freightVal,
            "predict_pot_time":timeVal,
            "is_detail":0,
            "is_page":1,
            "page":page,
            "page_size":5,
        }, 
        success: function(res) {
            if(res.code=='1000'){
                var conStr='';
                total=res.data.total;
                tabIndex=tabIndex+res.data.data.length;
                for(var i = 0;i<res.data.data.length;i++){
                    conStr+='<div class="Goods_D">'+
                                '<span class="goods_title D_I_B">商品信息</span>'+
                                '<span class="D_I_B export" onclick="seeDetail('+i+',\''+res.data.data[i].spec_sn+'\')">导出报价图片</span>'+
                            '</div>'
                    let imgStr;
                    if(res.data.data[i].spec_img!=''){
                        imgStr='<img src="'+imgURL+res.data.data[i].spec_img+'"/>';
                    }else{
                        imgStr='<img src="./image/notImg.png"/>';
                    }
                    conStr+='<div class="goodsTit_img">'+
                                '<div class="goodsTit_img_lef">'+imgStr+'</div>'+
                                '<div class="goodsTit_img_rig">'+
                                    '<div class="goodsTit_name">'+res.data.data[i].goods_name+'</div>'+
                                    '<div class="goodsTit_specPrice">'+
                                        '<span class="goods_left D_I_B" style="width:40%">美金原价</span>'+
                                        '<span class="goods_content D_I_B" style="width:50%">&nbsp;&nbsp;$'+res.data.data[i].spec_price+'</span>'+
                                        '<span class="goods_right D_I_B" style="width:10%" onclick="seeCharts(\''+res.data.data[i].erp_prd_no+'\')"><img src="./image/tubiao.png"/></span>'+
                                    '</div>'+
                                    '<div class="goodsTit_specPrice">'+
                                        '<span class="D_I_B redColor" style="font-size: 10px;">'+res.data.data[i].spec_price_update_info+'</span>'+
                                    '</div>'+
                                    '<div class="goodsTit_specPrice">'+
                                        '<span class="goods_left D_I_B" style="width:40%">商品重量</span>'+
                                        '<span class="goods_content D_I_B">&nbsp;&nbsp;'+res.data.data[i].spec_weight+'KG</span>'+
                                    '</div>'+
                                '</div>'+
                            '</div>'
                    conStr+='<div class="goods_info" style="background-color: #dcddde;">'+
                                '<span class="goods_left D_I_B">&nbsp;&nbsp;&nbsp;商品规格码</span>'+
                                '<sppan class="goods_content D_I_B">'+res.data.data[i].spec_sn+'</sppan>'+
                                '<span class="goods_right D_I_B" onclick="Copy(\''+res.data.data[i].spec_sn+'\')"><img src="./image/audit.png"/></span>'+
                            '</div>'+
                            '<div class="goods_info" style="background-color: #dcddde;">'+
                                '<span class="goods_left D_I_B">&nbsp;&nbsp;&nbsp;商家编码</span>'+
                                '<sppan class="goods_content D_I_B">'+res.data.data[i].erp_merchant_no+'</sppan>'+
                                '<span class="goods_right D_I_B" onclick="Copy(\''+res.data.data[i].erp_merchant_no+'\')"><img src="./image/audit.png"/></span>'+
                            '</div>'+
                            '<div class="goods_info" style="background-color: #dcddde;">'+
                                '<span class="goods_left D_I_B">&nbsp;&nbsp;&nbsp;商品代码</span>'+
                                '<sppan class="goods_content D_I_B">'+res.data.data[i].erp_prd_no+'</sppan>'+
                                '<span class="goods_right D_I_B" onclick="Copy(\''+res.data.data[i].erp_prd_no+'\')"><img src="./image/audit.png"/></span>'+
                            '</div>'+
                            '<div class="goods_info" style="background-color: #dcddde;">'+
                                '<span class="goods_left D_I_B">&nbsp;&nbsp;&nbsp;参考码</span>'+
                                '<sppan class="goods_content D_I_B">'+res.data.data[i].erp_ref_no+'</sppan>'+
                                '<span class="goods_right D_I_B" onclick="Copy(\''+res.data.data[i].erp_ref_no+'\')"><img src="./image/audit.png"/></span>'+
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
                                '<sppan class="goods_content D_I_B">$'+res.data.data[i].freight+'</sppan>'+
                            '</div>'
                            if(freightVal!=undefined){
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
                    if(res.data.data[i].stock_info!=undefined){
                        conStr+='<table class="kucun" style="margin-top: 20px;">'
                        let k = 0;
                        for(var j=0;j<res.data.data[i].stock_info.length/4;j++){
                            conStr+='<tr>'
                            if(k<res.data.data[i].stock_info.length){
                                conStr+='<td>'+res.data.data[i].stock_info[k].shop_name+'</td>'
                            }
                            if((k+1)<res.data.data[i].stock_info.length){
                                conStr+='<td>'+res.data.data[i].stock_info[k+1].shop_name+'</td>'
                            }else{
                                conStr+= '<td></td>'
                            }
                            
                            if((k+2)<res.data.data[i].stock_info.length){
                                conStr+='<td>'+res.data.data[i].stock_info[k+2].shop_name+'</td>'
                            }else{
                                conStr+= '<td></td>'
                            }
                            if((k+3)<res.data.data[i].stock_info.length){
                                conStr+='<td>'+res.data.data[i].stock_info[k+3].shop_name+'</td>'
                            }else{
                                conStr+= '<td></td>'
                            }
                            conStr+='</tr>'

                            conStr+='<tr style="font-weight: bold;">'
                            if(k<res.data.data[i].stock_info.length){
                                conStr+='<td>'+res.data.data[i].stock_info[k].stock+'</td>'
                            }
                            if((k+1)<res.data.data[i].stock_info.length){
                                conStr+='<td>'+res.data.data[i].stock_info[k+1].stock+'</td>'
                            }else{
                                conStr+= '<td></td>'
                            }
                            
                            if((k+2)<res.data.data[i].stock_info.length){
                                conStr+='<td>'+res.data.data[i].stock_info[k+2].stock+'</td>'
                            }else{
                                conStr+= '<td></td>'
                            }
                            if((k+3)<res.data.data[i].stock_info.length){
                                conStr+='<td>'+res.data.data[i].stock_info[k+3].stock+'</td>'
                            }else{
                                conStr+= '<td></td>'
                            }
                            conStr+='</tr>'
                            k=k+4
                        }
                        conStr+='</table>';
                    }
                    
                    let nameStr = '';
                    if(res.data.data[i].channel_name_info!=undefined){
                        for(var j=0;j<res.data.data[i].channel_name_info.length;j++){
                            nameStr+= ' <th>'+res.data.data[i].channel_name_info[j].channels_name+'</th>'
                        }
                    }

                    if(res.data.data[i].channels_info!=undefined){
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

                        
                        for(var j=0;j<res.data.data[i].channels_info.length;j++){
                            waitOne_O+= '<td>'+res.data.data[i].channels_info[j].cut_middle_discount+'</td>'
                            waitOne_T+= '<td>￥'+res.data.data[i].channels_info[j].cut_middle_cny_price+'</td>'
                            waitOne_S+= '<td>$'+res.data.data[i].channels_info[j].cut_middle_usd_price+'</td>'
                            waitOne_F+=  '<td>运'+res.data.data[i].channels_info[j].cut_middle_freight+'</td>'

                            waitTwo_O+= '<td>'+res.data.data[i].channels_info[j].cut_one_discount+'</td>'
                            waitTwo_T+= '<td>￥'+res.data.data[i].channels_info[j].cut_one_cny_price+'</td>'
                            waitTwo_S+= '<td>$'+res.data.data[i].channels_info[j].cut_one_usd_price+'</td>'
                            waitTwo_F+= '<td>运'+res.data.data[i].channels_info[j].cut_one_freight+'</td>'
                            
                            waitThree_O+= '<td>'+res.data.data[i].channels_info[j].cut_one_middle_discount+'</td>'
                            waitThree_T+= '<td>￥'+res.data.data[i].channels_info[j].cut_one_middle_cny_price+'</td>'
                            waitThree_S+= '<td>$'+res.data.data[i].channels_info[j].cut_one_middle_usd_price+'</td>'
                            waitThree_F+= '<td>运'+res.data.data[i].channels_info[j].cut_one_middle_freight+'</td>'

                            waitFour_O+= '<td>'+res.data.data[i].channels_info[j].cut_two_discount+'</td>'
                            waitFour_T+= '<td>￥'+res.data.data[i].channels_info[j].cut_two_cny_price+'</td>'
                            waitFour_S+= '<td>$'+res.data.data[i].channels_info[j].cut_two_usd_price+'</td>'
                            waitFour_F+= '<td>运'+res.data.data[i].channels_info[j].cut_two_freight+'</td>'

                            Basics_O+= '<td>'+res.data.data[i].channels_info[j].cost_discount+'</td>'
                            Basics_T+= '<td>￥'+res.data.data[i].channels_info[j].cost_cny_price+'</td>'
                            Basics_S+= '<td>$'+res.data.data[i].channels_info[j].cost_usd_price+'</td>'

                            
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
                                                    // '<br/><input onclick="rowSelect($(this))" name="jichu" type="checkbox" value="cut_middle"/>'+
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
                                                    '<th>渠道</th><th>渠道</th>'+nameStr+
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
                                                    '<th>渠道</th>'+
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
                                                    // '<br/><input onclick="rowSelect($(this))" name="cut_middle" type="checkbox" value="cut_middle"/>'+
                                                    // '<img src="./image/d_delelt.png"/>'+
                                                    '</td>'+
                                                '</tr>'+
                                                '<tr>'+
                                                    '<td style="height: 127px;">exw折扣+<br/>1%'+
                                                    // '<br/><input onclick="rowSelect($(this))" name="cut_one" type="checkbox" value="cut_one"/>'+
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
                                                    '<th>渠道</th><th>渠道</th>'+nameStr+
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
                                                    '<th>渠道</th>'+
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
                                                    // '<br/><input onclick="rowSelect($(this))" name="cut_one_middle" type="checkbox" value="cut_middle"/>'+
                                                    // '<img src="./image/x_delelt.png"/>'+
                                                    '</td>'+
                                                '</tr>'+
                                                '<tr>'+
                                                    '<td style="height: 127px;">代采+2%'+
                                                    // '<br/><input onclick="rowSelect($(this))" name="cut_two" type="checkbox" value="cut_middle"/>'+
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
                                    '<span class="goods_class D_I_B">其他折扣</span>&nbsp;&nbsp;'+
                                    '<input class="other_d other_d'+i+'"/>&nbsp;&nbsp;'+
                                    '<span class="sousuo" onclick="discountSearch($(this),\''+res.data.data[i].spec_sn+'\','+i+')">搜索</span>'+
                                '</div>'
                        conStr+='<div class="table_div_wrap" style="background-color: #fff5e2;display: none">'+
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
                                            '<tr class="aaa'+i+'">'+
                                                // '<td>1</td>'+Other_O+
                                            '</tr>'+
                                            '<tr class="eee'+i+'">'+
                                                // '<td>1</td>'+Other_O+
                                            '</tr>'+
                                            '<tr class="bbb'+i+'">'+
                                                // '<td>1</td>'+Other_O+
                                            '</tr>'+
                                            '<tr class="ccc'+i+'">'+
                                                // '<td>1</td>'+Other_O+
                                            '</tr>'+
                                            '</tbody>'+
                                        '</table>'+
                                    '</div>'+
                                '</div>'+
                                // '<div class="outer" style="background-color: #fff5e2;">'+
                                //     '<div class="table_thead">'+
                                //         '<table cellpadding="0" cellspacing="0">'+
                                //             '<thead>'+
                                //             '<tr>'+
                                //                 '<th>渠道</th>'+
                                //             '</tr>'+
                                //             '</thead>'+
                                //         '</table>'+
                                //     '</div>'+
                                //     '<div class="table_tbody">'+
                                //         '<table cellpadding="0" cellspacing="0">'+
                                //             '<tbody>'+
                                //             '<tr class="ddd'+i+'">'+
                                //                 // '<td style="height: 96px;">代采+</td>'+
                                //             '</tr>'+
                                //         '</table>'+
                                //     '</div>'+
                                // '</div>'+
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
                                            '<tr class="ddd'+i+'">'+
                                                // '<td style="height: 96px;">代采+</td>'+
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
                $(".content_b").append(conStr)
                $(".notData_b").fadeOut();
                if(res.data.data.length===0){
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
                    var table_tbody = [];
                    var table_thead = [];
                    var table_tbody_outer = []
                    for(var i = 0;i<tabIndex*4;i++){
                        table_tbody.push("table_tbody"+i)
                        table_thead.push("table_thead"+i)
                        table_tbody_outer.push("table_tbody_outer"+i)
                    }
                    for(var i = 0;i<tabIndex*4;i++){
                        if(i==0){
                            table_tbody[i] = document.getElementsByClassName("table_tbody")[0];
                            table_thead[i] = document.getElementsByClassName("table_thead")[0];
                            table_tbody_outer[i] = document.getElementsByClassName("table_tbody")[1].getElementsByTagName('table')[0];
                            let aaa = table_thead[i]
                            table_tbody[i].onscroll = function (e) {
                                aaa.style.marginLeft = "-"+this.scrollLeft+"px"
                                //此处用于控制顶行不动，上下滚动
                                // table_tbody_outer[i].style.marginTop = "-"+this.scrollTop+'px'
                            }
                        }else{
                            //基础折扣的滚动JS
                            table_tbody[i] = document.getElementsByClassName("table_tbody")[i*2];
                            table_thead[i] = document.getElementsByClassName("table_thead")[i*2];
                            table_tbody_outer[i] = document.getElementsByClassName("table_tbody")[i*2+1].getElementsByTagName('table')[0];
                            let bbb = table_thead[i]
                            table_tbody[i].onscroll = function (e) {
                                bbb.style.marginLeft = "-"+this.scrollLeft+"px"
                                //此处用于控制顶行不动，上下滚动
                                // table_tbody_outer[i].style.marginTop = "-"+this.scrollTop+'px'
                            }
                        }
                        
                    }
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

//搜索
function discountSearch(once,sn,index){
    //其他折扣
    let Other_O = '';
    let Other_T = '';
    let Other_S = '';
    let Other_F = '';
    let usdCnyRate = $(".rmber").val();
    let freight;
    if($(".freightEr").val()==''){
        freight = 3;
    }else{
        freight = $(".freightEr").val();
    }
    let marginRate = $(".grossProfit").val();
    let inputVal =  once.siblings('input').val();
    if(once.siblings('input').val()===''){
        $(".promptCon").html('请填写折扣后搜索!!!');
        $(".notData_b").fadeIn();
        setTimeout(function(){
            $(".notData_b").fadeOut();
        },2000)
    }else{
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
                "is_detail":0,
                "cut_other_value":inputVal,
            }, 
            success: function(res) {
                Other_O+='<td>1</td><td>1</td>';
                Other_T+='<td>1</td><td>1</td>';
                Other_S+='<td>1</td><td>1</td>';
                Other_F+='<td>1</td><td>1</td>';
                for(var i = 0;i<res.data.length;i++){
                    for(var j=0;j<res.data[i].channels_info.length;j++){
                        Other_O+= '<td>'+res.data[i].channels_info[j].cut_other_discount+'</td>'
                        Other_T+= '<td>'+res.data[i].channels_info[j].cut_other_cny_price+'</td>'
                        Other_S+= '<td>'+res.data[i].channels_info[j].cut_other_usd_price+'</td>'
                        Other_F+= '<td>运'+res.data[i].channels_info[j].cut_other_freight+'</td>'
                    }
                }
                
                $(".aaa"+index).html(Other_O);
                $(".bbb"+index).html(Other_T);
                $(".ccc"+index).html(Other_S);
                $(".eee"+index).html(Other_F);
                $(".ddd"+index).html('<td style="height: 127px;">代采+'+inputVal+'</td>')
                once.parent().next().css("display","block")
            }
        })
    }
}


//查看详情 
function seeDetail(index,sn){
    let inputVal = $(".other_d"+index+"").val()
    let usdCnyRate = $(".rmber").val();
    let freight;
    if($(".freightEr").val()==''){
        freight = 3;
    }else{
        freight = $(".freightEr").val();
    }
    let marginRate = $(".grossProfit").val();
    let freightVal = $("input[name='Fruit']:checked").val()
    let timeVal = $("input[name='user_date']").val()
    location.href='./goodsDetail.html?sn='+sn+"&cut_other_value="+inputVal+"&usdCnyRate="+usdCnyRate+"&freight="+freight+"&marginRate="
    +marginRate+"&freig_Val="+freightVal+"&timeVal="+timeVal;
}

//判断是否小于0
function ifLessThanZero(){
    let usdCnyRate = $(".rmber").val();
    if(usdCnyRate<0){
        $(".notData_b").fadeIn();
        $(".promptCon").html('汇率不能小于等于0！');
        $(".rmber").val('')
        setTimeout(function(){
            $(".notData_b").fadeOut();
        },2000)
    }
    if(usdCnyRate==='0'){
        $(".notData_b").fadeIn();
        $(".promptCon").html('汇率不能小于等于0！');
        $(".rmber").val('')
        setTimeout(function(){
            $(".notData_b").fadeOut();
        },2000)
    }
}

function docopy(text) {
    Clipboard.copy(text);
}

function seeCharts(sn){
    location.href='./chartsData.html?sn='+sn;
}
