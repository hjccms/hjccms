$(function(){
    $(".ckbox").click(function(){
        var li = this.parentElement;
        var arr = li.getElementsByTagName('input')
        for(var i=0; i<arr.length; i++){
            var input = arr[i];
            if(input.type == 'checkbox'){
                input.checked = this.checked;
            }
        }
        var li = li.parentElement.parentElement;
        while(li.tagName.toLowerCase() == 'li'){
            var input = li.childNodes[1];
            if(input.tagName.toLowerCase() == 'input'){
                if(this.checked == true){
                    input.checked = this.checked;
                }
            }
            li = li.parentElement.parentElement;
        }
    })
})