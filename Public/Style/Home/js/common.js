//正则判断手机号
function checkMobile(val){
    var pattern = /^1[3|4|5|7|8][0-9]\d{8}$/;
    if (pattern.test(val)) {
        return true;
    }else {
        return false;
    }
}

//正则判断英文名
function checkEnName(val){
    var pattern = /^[a-z A-Z]{1,50}$/;
    if (pattern.test(val)) {
        return true;
    }else {
        return false;
    }
}

//登录
function login()
{
    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
    parent.layer.open({
        type: 2,
        area: ['500px', '400px'],
        fix: true, //不固定
        maxmin: true,
        title:'登录',
        content: ['/Home/Register/login','no']
    });
    
    parent.layer.close(index); //再执行关闭 
}

//登录
function register()
{
    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
   
    parent.layer.open({
        type: 2,
        area: ['500px', '480px'],
        fix: true, //不固定
        maxmin: true,
        title:'注册',
        content: ['/Home/Register/reg','no']
    });
     parent.layer.close(index); //再执行关闭    
}
$(function() {
    $(".addUser").click(function(){
        var name = $.trim($("input[name=name]").val());
        var age = $.trim($("input[name=age]").val());
        var mobile = $.trim($("input[name=mobile]").val());
        var area = $.trim($("input[name=area]").val());
        var origin = $.trim($("input[name=origin]").val());
        if(!name || name=='孩子姓名'){
            alert('孩子姓名不能为空哦！');
            return false;
        }
        if(!age || age=='孩子年龄'){
            alert('孩子年龄不能为空哦！');
            return false;
        }
        if(!mobile || mobile=='联系电话'){
            alert('联系电话不能为空哦！');
            return false;
        }
        if(!area || area=='所在地区'){
            alert('所在地址不能为空哦！');
            return false;
        }
        if(!checkMobile(mobile)){
            alert('手机格式不正确哦！');
            return false;
        }
        $.ajax({
            type:'POST',
            url:'/Register/ajaxAddUser',
            data:'name='+name+'&age='+age+'&mobile='+mobile+'&area='+area+'&origin='+origin,
            async:false,
            dataType:'json',
            success:function(){
                
            }
        });
        
    });
});