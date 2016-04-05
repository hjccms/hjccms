/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//删除字段
$(function() {
    //删除菜单
    $(document).on("click", ".delField", function(e) {
        var id = $(this).attr('fieldid');
        var modelId = $(this).attr('modelid');
        layer.confirm('您确定要删除么？',function(index){
            $.ajax({
                type: "POST",
                url: "/Admin/Fieldinfo/fieldDel/modelId/"+modelId,
                data: "id="+id,
                dataType: "json",
                beforeSend:function()
                {
                    layer.load('正在请求...', 3);
                },
                success: function(msg)
                {
                    if(msg.status==1)
                    {
                        //layer.alert('删除成功！', 1); //风格一
                        location.reload() ;
                    }
                    else
                    {
                        layer.alert(msg.info, 8); //风格一
                    }
                }
             });
            
          
        });
    });
    
});

function dataDel(modelId,id)
{
    layer.confirm('您确定要删除这条数据吗？',function(index){
            $.ajax({
                type: "POST",
                url: "/Admin/Model/dataDel/modelId/"+modelId,
                data: "id="+id,
                dataType: "json",
                beforeSend:function()
                {
                    layer.load('正在请求...', 3);
                },
                success: function(msg)
                {
                    if(msg.status==1)
                    {
                        //layer.alert('删除成功！', 1); //风格一
                        location.reload() ;
                    }
                    else
                    {
                        layer.alert(msg.info, 8); //风格一
                    }
                }
             });
            
          
        });
}

