

// var bastURL = 'http://120.76.27.42:84/api/';//测试服
var imgURL = 'http://120.76.27.42:84/';//测试服 
// var bastURL = 'http://www.haidaihk.cn/api/';//正式服
// var imgURL = 'http://www.haidaihk.cn/';//正式服
var bastURL  = 'http://192.168.0.3:9999/api/';//宗兴

//复制函数
window.Clipboard = (function(window, document, navigator) {
    var textArea,
        copy;

    // 判断是不是ios端
    function isOS() {
        return navigator.userAgent.match(/ipad|iphone/i);
    }
    //创建文本元素
    function createTextArea(text) {
        textArea = document.createElement('textArea');
        textArea.innerHTML = text;
        textArea.value = text;
        document.body.appendChild(textArea);
    }
    //选择内容 
    function selectText() {
        var range,
            selection;

        if (isOS()) {
            range = document.createRange('input');
            // range.setAttribute('readonly', 'readonly'); // 防止手机上弹出软键盘
            range.selectNodeContents(textArea);
            selection = window.getSelection();
            selection.removeAllRanges();
            selection.addRange(range);
            textArea.setSelectionRange(0, 999999);
            // textArea.select();
        } else {
            textArea.select();
        }
    }

    //复制到剪贴板
    function copyToClipboard() {
        $(".notData_b").fadeIn();
        try{
            // console.log(document.execCommand("Copy"))
            if(document.execCommand("Copy")){
                // console.log(document.execCommand("Copy"))
                $(".promptCon").html('复制成功');
                setTimeout(function(){
                    $(".notData_b").fadeOut();
                },2000)
                // console.log('复制成功') 
                //layer.msg('复制成功');
            }else{
                // alter('复制失败！请手动复制！') 
                $(".promptCon").html('复制失败');
                setTimeout(function(){
                    $(".notData_b").fadeOut();
                },2000)
                //layer.msg('复制失败！请手动复制！');
            }
        }catch(err){
                // console.log('复制错误！请手动复制！') 
                $(".promptCon").html('复制失败');
                setTimeout(function(){
                    $(".notData_b").fadeOut();
                },2000)
                //layer.msg('复制错误！请手动复制！');

        }
        document.body.removeChild(textArea);
    }

    copy = function(text) {
        createTextArea(text);
        selectText();
        copyToClipboard();
    };

    return {
        copy: copy
    };
})(window, document, navigator);



function Copy(text) {
    var input = document.createElement('input');
    input.setAttribute('readonly', 'readonly'); // 防止手机上弹出软键盘
    input.setAttribute('value', text);
    document.body.appendChild(input);
    input.setSelectionRange(0, 9999);
    input.select();
    var res = document.execCommand('copy');
    document.body.removeChild(input);
    $(".notData_b").fadeIn();
    if(res){
        $(".promptCon").html('复制成功');
        setTimeout(function(){
            $(".notData_b").fadeOut();
        },2000)
    }else{
        $(".promptCon").html('复制失败');
        setTimeout(function(){
            $(".notData_b").fadeOut();
        },2000)
    }
    return res;
}