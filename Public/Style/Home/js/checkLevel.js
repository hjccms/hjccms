/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$(function(){
    $(".picslist").click(function(){
        var num = $(this).attr('num');
        $(".picslist").removeClass("testpics2");
        $(this).addClass("testpics2");
        $(".answer").val(num);
    })
    $(".changetext").click(function(){
        $(".picslist").removeClass("testpics2");
        var num = $(".num").html();
        var ch = $(this).attr("ch");
        var answer = $(".answer").val();
        
        if(ch=='pre')
        {
            num = parseInt(num)-1;
        }
        else
        {
            num = parseInt(num)+1;
            if(answer==0 || answer=='' || answer=='underfind')
            {
                alert('请先选择一个答案！');
                return false;
            }
        }
        if(num<1)
        {
            alert('这是第一道题~_~！');
            return false;
        }
        if(num>25)
        {
            alert('这是最后一道题~_~！');
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
        $(".answer").val('0');
    })
})

