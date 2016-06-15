$(function(){
    
    $(".sub").click(function(){
        var name = $.trim($("input[name=name]").val());
        var mobile = $.trim($("input[name=mobile]").val());
        if(!name){
            alert('英文名不能为空哦！');
            return false;
        }else{
            if(!checkEnName(name)){
                alert('英文名格式不对哦！');
                return false;
            }
        }
        if(!mobile){
            alert('手机号不能为空哦！');
            return false;
        }else{
            if(!checkMobile(mobile)){
                alert('手机号码格式不对！');
                return false;
            }
        }
        $.ajax({
            type:'post',
            url:'/LevelTest/testLogin',
            data:'name='+name+'&mobile='+mobile,
            dataType:'json',
            success:function(e){
                if(e.status == '1'){
                    location.href = '/Index/checkLevel';
                }
            }
        });
    });
    
    if(typeof(end_key) != "undefined"){
        if(end_key>0){
            change(end_key,'next');
        }
    }
    var media = $('#media')[0]; 
    var timer = null; 
    $('.stop').hide();
    $('.bofang').click(function() {  
        media.play(); 
        $('.bofang').hide(); 
        $('.stop').show();
        time();
    });   
    
    $('.stop').click(function() {  
        media.pause();
        $('.stop').hide(); 
        $('.bofang').show(); 
        clearInterval(timer);
    });
    
    function time(){
        timer = setInterval(function(){
            if(media.paused){
                $('.stop').hide(); 
                $('.bofang').show();
                clearInterval(timer);
            }
        },1);
    }
    
    var num = $(".num").text();
    if(num == '1'){
        answer(num);
    }
    $(".againbutton").click(function(){
        if(confirm("之前测试的成绩会被覆盖哦，确定要重新测试吗？")){
            location.href = '/Index/checkLevel/type/again';
        }
    })
    
    $(".classbutton").click(function(){
        var user_id = $("#user_id").val();
        var user_url = $("#user_url").val();
        if(user_id){
            location.href = user_url;
        }else{
            login();
        }
    })
    
    $(".picslist").click(function(){
        var num = $(this).attr('num');
        $(".picslist").removeClass("testpics2");
        $(this).addClass("testpics2");
        $(".answer").val(num);
    })
    $(".changetext").click(function(){
        var num = $(".num").html();
        var ch = $(this).attr("ch");
        var answer = $("input[name=answer]").val();
        if(ch=='next' && (answer==0 || answer=='' || answer=='underfind')){
            alert('请先选择一个答案！');
            return false;
        }
        if(answer && ch=='next'){
            $.ajax({
                type: "POST",
                url: "/LevelTest/addAnswer",
                data: 'num='+num+'&answer='+answer,
                async: false,
                dataType: "json",
                success: function(e){
                    if(e.status == '0'){
                        location.href = '/Index/checkLevel';
                    }
                }
            });
        }
        change(num,ch);
    })
    
    
    function change(num,ch){
        num = ch=='pre'?parseInt(num)-1:parseInt(num)+1;
        answer(num);
        if(num<1)
        {
            alert('这是第一道题~_~！');
            return false;
        }
        if(num>25)
        {
            location.href = '/Index/testResult';
            return false;
        }
        
        if(num<12)
        {
            $(".picslist").each(function(){
                var n = $(this).attr("num");
                var x = 0;
                x = parseInt(x);
                
                if(num%2==0){ x=600; };
                x = 200*(n-1)+x;
                
                var i = num;
                i = parseInt(i);
                if(num%2==1){ i = num+1;};
                var y = (i/2-1)*200;
                $(this).attr("style","background-position:-"+x+"px -"+y+"px;");
            });
        }
        if(num>11)
        {
            $(".picslist").each(function(){
                var n = $(this).attr("num");
                var x = 0;
                x = parseInt(x);
                
                if(num%2==1){ x=600; };
                x = 200*(n-1)+x;
                
                var i = num;
                i = parseInt(i);
                if(num%2==1){ i = num-1;};
                var y = (i/2)*200;
                $(this).attr("style","background-position:-"+x+"px -"+y+"px;");
            });
        }
        $(".num").html(num);
        $('.stop').hide(); 
        $('.bofang').show();
        $("#media").attr("src","/Public/Style/Home/audio/level_test/"+num+".m4a");
    }
    
    function answer(num){
        if(!num){
            return false;
        }
        var answer = null;
        $.ajax({
            type: "POST",
            url: "/LevelTest/getAnswer",
            data: 'num='+num,
            async: false,
            dataType: "json",
            success: function(e){
                if(e.status == '1'){
                    answer = e.data;
                }
            }
        });
        $(".picslist").removeClass("testpics2");
        if(answer){
            $(".picslist").eq(answer-1).addClass("testpics2");
            $(".answer").val(answer);
        }else{
            $(".answer").val('');
        }
    }
})

