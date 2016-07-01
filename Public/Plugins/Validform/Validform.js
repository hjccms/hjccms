/* 
 * 统一的表单验证方法
 * 加载此文件之前 请先加载 Validform,layer 框架
 */

$(function() {
    $(".validform").Validform({
        
        tiptype:function(msg,o,cssctl){
            if(!o.obj.is("form")){
                    var objtip=o.obj.siblings(".Validform_checktip");
                    cssctl(objtip,o.type);
                    objtip.text(msg);
                    
            }
        },
        datatype:{
                "*6-20": /^[^\s]{6,20}$/,
                "z2-4" : /^[\u4E00-\u9FA5\uf900-\ufa2d]{2,4}$/,
                "y3-10": /\w{3,10}/i,
                "y1-20": /^[a-zA-Z_\/ ]{1,20}$/,
        },
        ajaxPost:true,
        beforeSubmit:function(){
            
            layload();
        },
        callback:function(ret){
            
            layclose();
            if(ret.status==1)
            {
                lalertFun(ret);
            }
            else
            {
                lalert(ret.info,1);
            }
        }
    });
    
});
