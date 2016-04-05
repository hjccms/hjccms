
// Cloud Float...
var $main = $cloud = mainwidth = null;
var offset1 = 450;
var offset2 = 0;

var offsetbg = 0;

$(document).ready(
        function () {
            $main = $("#mainBody");
            $body = $("body");
            $cloud1 = $("#cloud1");
            $cloud2 = $("#cloud2");

            mainwidth = $main.outerWidth();

        }
        //账号登陆
       
);

/// 飘动
setInterval(function flutter() {
    if (offset1 >= mainwidth) {
        offset1 = -580;
    }

    if (offset2 >= mainwidth) {
        offset2 = -580;
    }

    offset1 += 1.1;
    offset2 += 1;
    $cloud1.css("background-position", offset1 + "px 100px")

    $cloud2.css("background-position", offset2 + "px 460px")
}, 70);


setInterval(function bg() {
    if (offsetbg >= mainwidth) {
        offsetbg = -580;
    }

    offsetbg += 0.9;
    $body.css("background-position", -offsetbg + "px 0")
}, 90);

function login()
{
    var username = $.trim($('.loginuser').val());
    var password = $.trim($('.loginpwd').val());
    var verify = $.trim($('.verify').val());
    if(username==''||password=='')
    {
        alert('请输入账号和密码！');
        return false;
    }
    if(verify==''||verify=='验证码')
    {
        alert('请输入验证码！');
        return false;
    }
    $.ajax({
        type: "POST",
        url: "/Admin/Login/ajaxLogin",
        data: "username="+username+"&password="+password+"&verify="+verify,
        dataType: "json",
        beforeSend:function(msg){
            $(".loginerror").html('正在登陆...');
            $(".loginerror").show();
	},
        success: function(msg)
        {
            if(msg.status==1)
            {
                $(".loginerror").html('登陆成功！');
                $(".loginerror").show();
                location.href='/Admin/Index';
            }
            else
            {
                $(".loginerror").html(msg.info);
                $(".loginerror").show();
            }
        }
     });
}