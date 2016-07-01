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
                    layclose();
                    layload();
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
                        lalert(msg.info, 1); //风格一
                    }
                }
             });
            
          
        });
    });
    
});

function dataDel(modelId,id,del,table_name)
{
    layer.confirm('您确定要删除这条数据吗？',function(index){
            $.ajax({
                type: "POST",
                url: "/Admin/Model/dataDel/modelId/"+modelId+"/del/"+del+"/table_name/"+table_name,
                data: "id="+id,
                dataType: "json",
                beforeSend:function()
                {
                    layclose();
                    layload();
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
                        lalert(msg.info, 1); //风格一
                    }
                }
             });
            
          
        });
}
//排序函数
function dataSort(modelId,table_name)
{
    var data = '';
    $(".idsort").each(function(){
        var val = $(this).val();
        data += "&"+$(this).attr('name')+"="+val;
    });
    
    $.ajax({
        type: "POST",
        url: "/Admin/Model/dataSort",
        data: "modelId="+modelId+"&table_name="+table_name+data,
        dataType: "json",
        beforeSend:function()
        {
            layload();
        },
        success: function(msg)
        {
            if(msg.status==1)
            {
                location.reload() ;
            }
            else
            {
                lalert(msg.info, 1); 
            }
        }
    });
            
          
      
}
//显示自定义规则输入框
function changeGuize(obj)
{
    var val = obj.val();
    if(val=='other')
    {
        $('.guize').show();
    }
    else
    {
        $('.guize').hide();
        $('.guize').children("input").val('');
    }
}