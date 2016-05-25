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