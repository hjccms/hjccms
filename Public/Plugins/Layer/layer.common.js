/*
 * 利用layer重写 alert
 * 以后可以更换样式
 */
function lalert(content,isflase)
{
    var iconnum = 6;
    if(isflase==1) iconnum = 5;
    layer.alert(content, {icon: iconnum});
}
/*
 * 利用layer重写  带回调方法的alert
 * 以后可以更换样式
 */
function lalertFun(ret)
{
    var iconnum = 6;
    if(ret.isflase==1) iconnum = 5;
    layer.alert(ret.info, {icon: iconnum},function(){ location.href=ret.data.url;});
}
/*
 * layerlaod加载层
 * 以后可以更换样式
 */
function layload()
{
    layer.load(2);
}
/*
 * 关闭所有弹出层 
 */
function layclose()
{
    layer.closeAll();
}