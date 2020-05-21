import axios from 'axios';
var headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
//动态修改页面背景高度
function bgHeight(){
    let vm=this;
    let height=$(".bgWidth").height()+1;
    if(height<800){
        $(".bgWidth").height(800);
    }else if(height>800){
        $(".bgWidth").height("auto");
    }
};
// 月级日期格式转变为字符串格式
function monthToStr(datetime){ 
    var year = datetime.getFullYear();
    var month = datetime.getMonth()+1;//js从0开始取 
    if(month<10){
    month = "0" + month;
    }
    var time = year+"-"+month;
    return time;
};
// 日期格式转变为字符串格式
function dateToStr(datetime){ 
    var year = datetime.getFullYear();
    var month = datetime.getMonth()+1;//js从0开始取 
    var date = datetime.getDate(); 
    var hour = datetime.getHours(); 
    var minutes = datetime.getMinutes(); 
    var second = datetime.getSeconds();
    if(month<10){
    month = "0" + month;
    }
    if(date<10){
    date = "0" + date;
    }
    var time = year+"-"+month+"-"+date;
    return time;
};
//时间格式转变为字符串格式
function timeToStr(datetime){ 
    var year = datetime.getFullYear();
    var month = datetime.getMonth()+1;//js从0开始取 
    var date = datetime.getDate(); 
    var hour = datetime.getHours(); 
    var minutes = datetime.getMinutes(); 
    var second = datetime.getSeconds();
    if(hour <10){
    hour = "0" + hour;
    }
    if(minutes <10){
    minutes = "0" + minutes;
    }
    if(second <10){
    second = "0" + second ;
    }
    var time = hour+":"+minutes+":"+second;
    return time;
};
// //返回年
// function getYear(datetime){
//     var year = datetime.getFullYear();
//     return year; 
// }
// //返回月
// function getMonth(datetime){

// }
//获取不需要参数的页面数据
function getDataList(url,commentUrl){
    let vm=this;
    axios.get(url+commentUrl,
        {
            headers:headersStr
        }
    ).then(function(res){
    }).catch(function (error) {
        if(error.response.status=="401"){
        vm.$message('登录过期,请重新登录!');
        sessionStorage.setItem("token","");
        vm.$router.push('/');
        }
    });
};
//检查开始时间不能早于当天凌晨十二点
function startDate(selectTime){
    let vm = this;
    var timeStamp = new Date(new Date().setHours(0,0,0,0))
    var date = new Date(dateToStr(timeStamp));
    // var time = date.getTime()+86400;//明天凌晨零点 
    var time = date.getTime()-86400000;//今天凌晨零点
    var dateTwo = new Date(selectTime);
    var timeTwo = dateTwo.getTime();
    if(selectTime!=''){
        if(timeTwo<time){
            vm.entrustTime='';
            vm.$message('开始日期必须大于等于当前日期！');
        }
    }
};
//切换成loading图标
function switchLoading(event){
    $(event.target).addClass("el-icon-loading");
    $(event.target).removeClass("el-icon-view");
};
//切换成原来图标
function switchoRiginally(event){
    $(event.target).addClass("el-icon-view");
    $(event.target).removeClass("el-icon-loading");
};
//数组去重
function uniq(array){
    var temp = {}, r = [], len = array.length, val, type;
    for (var i = 0; i < len; i++) {
        val = array[i];
        type = typeof val;
        if (!temp[val]) {
            temp[val] = [type];
            r.push(val);
        } else if (temp[val].indexOf(type) < 0) {
            temp[val].push(type);
            r.push(val);
        }
    }
    return r;
}
//数组排序
function arrMinNum(arr){
    var minNum = Infinity, index = -1,minVul = "";
    for (var i = 0; i < arr.length; i++) {
        if (typeof(arr[i]) == "string") {
            if (arr[i].charCodeAt()<minNum) {
                minNum = arr[i].charCodeAt();
                minVul = arr[i];
                index = i;
            }
        }else {
            if (arr[i]<minNum) {
                minNum = arr[i];
                minVul = arr[i]
                index = i;
            }
        }
    };
    return {"minNum":minVul,"index":index};
}
function arrSortMinToMax(arr){
    var arrNew = [];
    var arrOld = arr.concat();
    for (var i = 0; i < arr.length; i++) {
        arrNew.push(arrMinNum(arrOld).minNum);
        arrOld.splice(arrMinNum(arrOld).index,1)
    };
    return (arrNew);
}
function arrMaxNum(arr){
    var maxNum = -Infinity, index = -1,maxVul = "";
    for (var i = 0; i < arr.length; i++) {
        if (typeof(arr[i]) == "string") {
            if (arr[i].charCodeAt()>maxNum) {
                maxNum = arr[i].charCodeAt();
                maxVul = arr[i];
                index = i;
            }
        }else {
            if (arr[i]>maxNum) {
                maxNum = arr[i];
                maxVul = arr[i];
                index = i;
            }
        }
    };
    return {"maxNum":maxVul,"index":index};
}
function arrSortMaxToMin(arr){
    var arrNew = [];
    var arrOld = arr.slice(0);
    for (var i = 0; i < arr.length; i++) {
        arrNew.push(arrMaxNum(arrOld).maxNum);
        arrOld.splice(arrMaxNum(arrOld).index,1);
    };
    return (arrNew);
}
//根据表格数据多少修改表格展示样式
function tableStyleByDataLength(goodsNum,letgth){
    if(parseInt(goodsNum)>letgth){
        $(".cc").css({
            "height":"610px",
            "width":"100.6%",
            })
    }else{
        $(".cc").css({
            "height":"auto",
            "width":"100%",
            })
    }
}
// 返回后台数据中某个数据个数最多的一个
function someDataLongByJson(tableData,str){
    var recLength=[];
    if(tableData!=''){
        tableData.forEach(element=>{
            recLength.push(element[str].length);
        })
    }      
    var j=recLength[0];
    for(var i=0;i<=recLength.length;i++){
        if(recLength[i+1]>=j){
            j=recLength[i+1]
        }
    } 
    if(j==0){
        return 2;
    }
    return j;
}
//选择要展示的表头
function selectTitle(){
    let vm = this;
    return function(str){
        let judgeSelection=this.$store.state.select.find(function(e){
            return e==str;
        });
        if(judgeSelection){
            return true;
        }else{
            return false;
        }
    }
}
//表格宽度计算
function tableWidth(select){
    let vm=this;
    let judgeRate=select.find(function(e){
            return e=='商品名称';
    });
    let cankaoma=select.find(function(e){
            return e=='商品参考码';
    });
    var widthLength=select.length*130;
    if(judgeRate){
        widthLength=widthLength+200;
    }
    if(cankaoma){
        widthLength=widthLength+160;
    }
    let title = $(".title").width();
    if(widthLength<title){
        widthLength=title;
    }
    return "width:"+widthLength+"px"
}
function exportsa(params,tableName){
    let headersToken=sessionStorage.getItem("token");
    var xhr = new XMLHttpRequest();//创建新的XHR对象
    xhr.open('post', params);//指定获取数据的方式和url地址
    xhr.setRequestHeader('Authorization','Bearer ' + sessionStorage.getItem("token"),'Accept','application/vnd.jmsapi.v1+json','Content-Type', 'application/json;')
    xhr.responseType = 'blob';//以blob的形式接收数据，一般文件内容比较大
    xhr.onload = function(e) {
        var blob = this.response;//Blob数据
        if (this.status == 200) {
            if (blob && blob.size > 0) {
                saveAs(blob, ''+tableName+'.xls');//处理二进制数据，让浏览器认识它
                $(".queding").show()
                $(".yincang").hide()
            } 
        } 
    };
    // var aaa = {"diff_goods_info:":parameter};
    xhr.send() //post请求传的参数
}

//数组中查找重复值并标记下标 
function listSearchRepeat(arr){
    var list = [];
    let rowspan = [];
    for (var i = 0; i < arr.length; i++){
        var hasRead = false;
        for (var k = 0; k < list.length; k++){
            if (i == list[k]){
                hasRead = true;
            }
        }
        // if (hasRead) { break;}
        let index = [i];
        var _index = i, haveSame = false;
        for (var j = i + 1; j < arr.length; j++){
            if (arr[i] ==arr[j]){
                list.push(j);
                index.push(j)
                haveSame = true;
            }
        }
        if (haveSame){
            if(arr[i-1]!=arr[i]){ 
                rowspan.push(index.length)
                index.splice(1);
            }else{
                rowspan.push(1);
            }
        }else{
            rowspan.push(1);
            
        }
    }
    return rowspan;
}
function exportExcel(blob,name){
    saveAs(blob.data, name);
}
var saveAs = saveAs || (function(view) {
	"use strict";
	// IE <10 is explicitly unsupported
	if (typeof view === "undefined" || typeof navigator !== "undefined" && /MSIE [1-9]\./.test(navigator.userAgent)) {
		return;
	}
	var
		  doc = view.document
		  // only get URL when necessary in case Blob.js hasn't overridden it yet
		, get_URL = function() {
			return view.URL || view.webkitURL || view;
		}
		, save_link = doc.createElementNS("http://www.w3.org/1999/xhtml", "a")
		, can_use_save_link = "download" in save_link
		, click = function(node) {
			var event = new MouseEvent("click");
			node.dispatchEvent(event);
		}
		, is_safari = /constructor/i.test(view.HTMLElement) || view.safari
		, is_chrome_ios =/CriOS\/[\d]+/.test(navigator.userAgent)
		, setImmediate = view.setImmediate || view.setTimeout
		, throw_outside = function(ex) {
			setImmediate(function() {
				throw ex;
			}, 0);
		}
		, force_saveable_type = "application/octet-stream;"
		// the Blob API is fundamentally broken as there is no "downloadfinished" event to subscribe to
		, arbitrary_revoke_timeout = 1000 * 40 // in ms
		, revoke = function(file) {
			var revoker = function() {
				if (typeof file === "string") { // file is an object URL
					get_URL().revokeObjectURL(file);
				} else { // file is a File
					file.remove();
				}
			};
			setTimeout(revoker, arbitrary_revoke_timeout);
		}
		, dispatch = function(filesaver, event_types, event) {
			event_types = [].concat(event_types);
			var i = event_types.length;
			while (i--) {
				var listener = filesaver["on" + event_types[i]];
				if (typeof listener === "function") {
					try {
						listener.call(filesaver, event || filesaver);
					} catch (ex) {
						throw_outside(ex);
					}
				}
			}
		}
		, auto_bom = function(blob) {
			// prepend BOM for UTF-8 XML and text/* types (including HTML)
			// note: your browser will automatically convert UTF-16 U+FEFF to EF BB BF
			if (/^\s*(?:text\/\S*|application\/xml|\S*\/\S*\+xml)\s*;.*charset\s*=\s*utf-16/i.test(blob.type)) {
				return new Blob([String.fromCharCode(0xFEFF), blob], {type: blob.type});
			}
			return blob;
		}
		, FileSaver = function(blob, name, no_auto_bom) {
			if (!no_auto_bom) {
				blob = auto_bom(blob);
			}
			// First try a.download, then web filesystem, then object URLs
			var
				  filesaver = this
				, type = blob.type
				, force = type === force_saveable_type
				, object_url
				, dispatch_all = function() {
					dispatch(filesaver, "writestart progress write writeend".split(" "));
				}
				// on any filesys errors revert to saving with object URLs
				, fs_error = function() {
					if ((is_chrome_ios || (force && is_safari)) && view.FileReader) {
						// Safari doesn't allow downloading of blob urls
						var reader = new FileReader();
						reader.onloadend = function() {
							var url = is_chrome_ios ? reader.result : reader.result.replace(/^data:[^;]*;/, 'data:attachment/file;');
							var popup = view.open(url, '_blank');
							if(!popup) view.location.href = url;
							url=undefined; // release reference before dispatching
							filesaver.readyState = filesaver.DONE;
							dispatch_all();
						};
						reader.readAsDataURL(blob);
						filesaver.readyState = filesaver.INIT;
						return;
					}
					// don't create more object URLs than needed
					if (!object_url) {
						object_url = get_URL().createObjectURL(blob);
					}
					if (force) {
						view.location.href = object_url;
					} else {
						var opened = view.open(object_url, "_blank");
						if (!opened) {
							// Apple does not allow window.open, see https://developer.apple.com/library/safari/documentation/Tools/Conceptual/SafariExtensionGuide/WorkingwithWindowsandTabs/WorkingwithWindowsandTabs.html
							view.location.href = object_url;
						}
					}
					filesaver.readyState = filesaver.DONE;
					dispatch_all();
					revoke(object_url);
				}
			;
			filesaver.readyState = filesaver.INIT;


			if (can_use_save_link) {
				object_url = get_URL().createObjectURL(blob);
				setImmediate(function() {
					save_link.href = object_url;
					save_link.download = name;
					click(save_link);
					dispatch_all();
					revoke(object_url);
					filesaver.readyState = filesaver.DONE;
				}, 0);
				return;
			}


			fs_error();
		}
		, FS_proto = FileSaver.prototype
		, saveAs = function(blob, name, no_auto_bom) {
			return new FileSaver(blob, name || blob.name || "download", no_auto_bom);
		}
	;


	// IE 10+ (native saveAs)
	if (typeof navigator !== "undefined" && navigator.msSaveOrOpenBlob) {
		return function(blob, name, no_auto_bom) {
			name = name || blob.name || "download";


			if (!no_auto_bom) {
				blob = auto_bom(blob);
			}
			return navigator.msSaveOrOpenBlob(blob, name);
		};
	}


	// todo: detect chrome extensions & packaged apps
	//save_link.target = "_blank";


	FS_proto.abort = function(){};
	FS_proto.readyState = FS_proto.INIT = 0;
	FS_proto.WRITING = 1;
	FS_proto.DONE = 2;


	FS_proto.error =
	FS_proto.onwritestart =
	FS_proto.onprogress =
	FS_proto.onwrite =
	FS_proto.onabort =
	FS_proto.onerror =
	FS_proto.onwriteend =
		null;


	return saveAs;
}(
	   typeof self !== "undefined" && self
	|| typeof window !== "undefined" && window
	|| this
));
export { //很关键,函数抛出 
    bgHeight,
    monthToStr,
    dateToStr,
    timeToStr,
    getDataList,
    startDate,
    switchLoading,
    switchoRiginally,
    uniq,
    arrSortMinToMax,
    arrSortMaxToMin,
    tableStyleByDataLength,
    someDataLongByJson,
    selectTitle,
    tableWidth,
    exportsa,
    listSearchRepeat,
    exportExcel,
}