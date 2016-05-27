$(function(){
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
        var all_answer = $("input[name=all_answer]").val();
        if(ch=='next' && (answer==0 || answer=='' || answer=='underfind')){
            alert('请先选择一个答案！');
            return false;
        }
        if(answer){
            $.ajax({
                type: "POST",
                url: "/LevelTest/addAnswer",
                data: 'num='+num+'&answer='+answer+'&all_answer='+all_answer,
                async: false,
                dataType: "json",
                success: function(e){
                    if(e.status == '1'){
                        $("input[name=all_answer]").val(e.data);
                    }
                }
            });
        }
        change(num,ch);
    })
    
    
    function change(num,ch){
        num = ch=='pre'?parseInt(num)-1:parseInt(num)+1;
        var answer = null;
        if(num>0){
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
        } 
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
        $(".picslist").removeClass("testpics2");
        if(answer){
            $(".picslist").eq(answer-1).addClass("testpics2");
            $(".answer").val(answer);
        }else{
            $(".answer").val('');
        }
        $('.stop').hide(); 
        $('.bofang').show();
        $("#media").attr("src","/Public/Style/Home/audio/level_test/"+num+".m4a");
    }
})

