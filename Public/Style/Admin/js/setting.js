/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/*************后台菜单部分*****************/
$(function() {
    //删除菜单
    $(document).on("click", ".delLink", function(e) {
        var id = $(this).attr('delid');
        layer.confirm('您确定要删除么？',function(index){
            $.ajax({
                type: "POST",
                url: "/Admin/Menu/delMenu",
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
//通用删除方法
function dataDel(id)
{
    
}

