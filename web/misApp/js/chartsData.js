

let headersToken=sessionStorage.getItem("token");
if(headersToken==null){
    location.href='./login.html';
    // return false;
}
let chartsData = [];

var url = location.search; //获取url中"?"符后的字串 
var params = url.split("&")
var sn;
for(var i=0;i<params.length;i++){
    if(params[i].indexOf("sn")>-1){
        sn = params[i].split("=")[1];
    }
}

getChartsData();
function getChartsData(){
    $(".promptCon").html('正在加载中!!!');
    $(".notData_b").fadeIn();
    $.ajax({
        url: bastURL+'Goods/ltGoodsSpecPriceInfo?erp_prd_no='+sn,
        type:"GET",
        async: true,
        // cache: false,
        headers:{'Authorization': 'Bearer '+headersToken,'Accept':'application/vnd.jmsapi.v2+json'},
        success: function(res) {
            if(res.code=='1000'){
                chartsData=res.data;
                drawLine();
            }else{
                $(".promptCon").html(res.msg);
                $(".notData_b").fadeIn();
                setTimeout(function(){
                    $(".notData_b").fadeOut();
                },2000)
            }
            
        }
    })
}
function seeSomeDate(num,e){
    $(".promptCon").html('正在加载中!!!');
    $(".notData_b").fadeIn();
    //获取三十天前日期
    var myDate = new Date();
    var nowY = myDate.getFullYear();
    var nowM = myDate.getMonth()+1;
    var nowD = myDate.getDate();
    var enddate = nowY+"-"+(nowM<10 ? "0" + nowM : nowM)+"-"+(nowD<10 ? "0"+ nowD : nowD);//当前日期
    var lw = new Date(myDate - 1000 * 60 * 60 * 24 * num);//最后一个数字30可改，30天的意思
    var lastY = lw.getFullYear();
    var lastM = lw.getMonth()+1;
    var lastD = lw.getDate();
    var startdate=lastY+"-"+(lastM<10 ? "0" + lastM : lastM)+"-"+(lastD<10 ? "0"+ lastD : lastD);//三十天之前日期
    $("#main").hide();
    $(e).css({
        "background-color": "#ccc",
    })
    $(e).siblings('span').css({
        "background-color": "#4677c4",
    })
    $.ajax({
        url: bastURL+'Goods/ltGoodsSpecPriceInfo',
        type:"GET",
        async: true,
        // cache: false,
        headers:{'Authorization': 'Bearer '+headersToken,'Accept':'application/vnd.jmsapi.v2+json'},
        data:{
            "start_time":startdate,
            "end_time":enddate,
            "erp_prd_no":sn
        },
        success: function(res) {
            if(res.code=='1000'){
                chartsData=res.data;
                drawLine();
                $("#main").show();
            }else{
                $(".notData_b").fadeOut();
            }
            
        }
    }).catch(function (error) {
        $(".notData_b").fadeOut();
    });
}
function drawLine(){
    let vm = this;
    // 基于准备好的dom，初始化echarts实例
    // $("#myChart").show()
    let myChart = echarts.init(document.getElementById('main'))
    // myChart.clear();
    let dateList = [];
    chartsData.date_arr.forEach(element=>{
        console.log(element.substring(5));
        dateList.push(element.substring(5));
    })
    $(".notData_b").fadeOut();
    // 绘制图表
    myChart.setOption({
        title: { text: chartsData.goods_info.goods_name },
        tooltip: {
            trigger: 'axis'
        },
        // legend: {
        //     data:['渠道积分','渠道余款']
        // },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        toolbox: {
            feature: {
                saveAsImage: {}
            }
        },
        xAxis: {
            type: 'category',
            boundaryGap: false,
            data: dateList,
        },
        yAxis: {
            type: 'value'
        },
        series: [
            {
                name:'美金原价',
                type:'line',
                stack: '总量',
                data: chartsData.spec_price_arr,
            }
            // {
            //     name:'渠道余款',
            //     type:'line',
            //     stack: '总量',
            //     data:vm.money_log
            // },
        ]
    });
}