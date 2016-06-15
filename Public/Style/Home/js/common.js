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