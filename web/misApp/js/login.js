var offsetWid = document.documentElement.clientWidth;
var offsetHei = document.documentElement.clientHeight;
$(".bg").height(offsetHei);
$(".bg").width(offsetWid);
$(function(){
    
})

// 
function getData(){
    let user = $(".userName input").val();
    let pass = $(".passWord input").val();
    let headersToken=sessionStorage.getItem("token");
    $.ajax({
        url: bastURL+'user/h5Login',
        type: "POST",
        async: true,
        cache: false,
        headers:{'Accept':'application/vnd.jmsapi.v2+json'},
        data: {
            "user_name":user,
            "password":pass,
        }, 
        success: function(res) {
           if(res.code=='1000'){
                // if(localStorage .getItem("versionNum")!=res.data.sys_version_info.web_num){
                    
                // }else{
                //     localStorage .setItem("versionNum",res.data.sys_version_info.web_num);
                //     router.push({path:'/indexPage'});
                // }
            location.href='./searchMerchandise.html'; 
            sessionStorage.setItem("token",res.token);
            sessionStorage.setItem("user_info",JSON.stringify(res.user_info));
           }else{
            $(".notData_b").fadeIn();
            $(".tishi").html("错误提示")
            $(".promptCon").html(res.msg)
            setTimeout(function(){
                $(".notData_b").fadeOut();
            },2000)
           }
        }
    })
}