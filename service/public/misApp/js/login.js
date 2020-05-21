var offsetWid = document.documentElement.clientWidth;
var offsetHei = document.documentElement.clientHeight;
$(".bg").height(offsetHei);
$(".bg").width(offsetWid);

//
function getData(){
    let user = $(".userName input").val();
    let pass = $(".passWord input").val();
    console.log(user,pass)
    $.ajax({
        url: 'http://192.168.0.39:9999/api/user/h5Login',
        type: "POST",
        async: true,
        cache: false,
        data: {
            "user_name":user,
            "password":pass,
        }, 
        success: function(res) {
           if(res.code=='1000'){
            location.href='./searchMerchandise.html';
            sessionStorage.setItem("token",res.token);
           }else{
            $(".notData_b").fadeIn();
            setTimeout(function(){
                $(".notData_b").fadeOut();
            },2000)
           }
        }
    })
}