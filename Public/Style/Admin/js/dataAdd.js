/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


//删除字段



function selTems(con)
{
    var modelId = $("."+con).val();
    
    $.ajax({
        type: "POST",
        url: "/Admin/Category/selTems/",
        data: "modelId="+modelId,
        dataType: "json",
        beforeSend:function()
        {
            layer.load('正在请求...', 3);
        },
        success: function(msg)
        {
            if(msg.status==1)
            {
                $(".template_index").html(msg.data.index);
                $(".template_list").html(msg.data.list);
                $(".template_show").html(msg.data.show);
            }
            
        }
    });
    
}