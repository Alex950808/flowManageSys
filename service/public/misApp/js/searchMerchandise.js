var offsetWid = document.documentElement.clientWidth;
var offsetHei = document.documentElement.clientHeight;
$(".bg").height(offsetHei);
$(".bg").width(offsetWid);
// $(".notData").height(offsetHei);
// $(".notData").width(offsetWid);

// getData() 
function search(){
    let querySn = $(".Inp").val();
    if(querySn===''){

    }
    let headersToken=sessionStorage.getItem("token");
    $.ajax({
        url: 'http://192.168.0.39:9999/api/Goods/getGoodsFinalDiscount',
        type: "POST",
        async: true,
        cache: false,
        headers:{'Authorization': 'Bearer '+headersToken,'Accept':'application/vnd.jmsapi.v1+json'},
        data: {
            "query_sn":querySn,
        }, 
        success: function(res) {
            $(".bg").height("auto");
            $(".content_b").html('');
            var conStr='';
           for(var i = 0;i<res.data.length;i++){
               conStr+='<div class="content"><div>'+
                            '<span class="con_L D_I_B" style="width: 23%;float: left;">名称</span>'+
                            '<span class="con_R D_I_B" style="width: 75%">'+res.data[i].goods_name+'</span>'+
                        '</div>'+
                        '<div>'+
                            '<span class="con_L D_I_B" style="width: 23%;float: left;">商品规格码</span>'+
                            '<span class="con_R D_I_B" style="width: 75%">'+res.data[i].spec_sn+'</span>'+
                        '</div>'+
                        '<div>'+
                            '<span class="con_L D_I_B" style="width: 23%;float: left;">商家编码</span>'+
                            '<span class="con_R D_I_B" style="width: 75%">'+res.data[i].erp_merchant_no+'</span>'+
                        '</div>'+
                        '<div>'+
                            '<span class="con_L D_I_B" style="width: 23%">商品代码</span>'+
                            '<span class="con_R D_I_B" style="width: 75%">'+res.data[i].erp_prd_no+'</span>'+
                        '</div>'+
                        '<div>'+
                            '<span class="con_L D_I_B" style="width: 23%">参考码</span>'+
                            '<span class="con_R D_I_B" style="width: 75%">'+res.data[i].erp_ref_no+'</span>'+
                        '</div>'+
                        '<div>'+
                            '<span class="con_L D_I_B" style="width: 23%">美金原价</span>'+
                            '<span class="con_R D_I_B" style="width: 75%">'+res.data[i].spec_price+'</span>'+
                        '</div>'+
                        '<div>'+
                        '<span class="con_L D_I_B" style="width: 23%;float: left;">折扣</span>'+
                        '<span class="con_R D_I_B" style="width: 75%">'+
                            '<table cellpadding="0" cellspacing="0" border="0" style="width: 100%;">'+
                                '<tr>'+
                                    '<td>排序</td>'+
                                    '<td>渠道名称</td>'+
                                    '<td>成本折扣</td>'+
                                    '<td>最终折扣</td>'+
                                '</tr>'
                for(var j = 0;j<res.data[i].channels_info.length;j++){
                    conStr+='<tr>'+
                                '<td>'+(j+1)+'</td>'+
                                '<td>'+res.data[i].channels_info[j].channels_name+'</td>'+
                                '<td>'+res.data[i].channels_info[j].cost_discount+'</td>'+
                                '<td>'+res.data[i].channels_info[j].brand_discount+'</td>'+
                            '</tr>'
                }
                conStr+='</table>'+
                            '</span>'+
                        '</div></div>'
           }
           $(".content_b").append(conStr)
        }
    })
}
