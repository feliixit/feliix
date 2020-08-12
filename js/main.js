$(function(){
    //主要執行js
    $('.tags a.A').click(function(){
        $('.tags a').removeClass('focus');
        $(this).addClass('focus');
        $('.block').removeClass('focus');
        $('.block.A').addClass('focus');
    })
    $('.tags a.B').click(function(){
        $('.tags a').removeClass('focus');
        $(this).addClass('focus');
        $('.block').removeClass('focus');
        $('.block.B').addClass('focus');
    })
    $('.tags a.C').click(function(){
        $('.tags a').removeClass('focus');
        $(this).addClass('focus');
        $('.block').removeClass('focus');
        $('.block.C').addClass('focus');
    })
    $('.tags a.D').click(function(){
        $('.tags a').removeClass('focus');
        $(this).addClass('focus');
        $('.block').removeClass('focus');
        $('.block.D').addClass('focus');
    })
    //
    
})
// 第二階段新增
function dialogclear(){
        console.log('dialogclear');
        $('.list_function .dialog').removeClass('show');
        $('.list_function a').removeClass('focus');
        $('.list_function.main .block.fn a').removeClass('focus');        
}
function dialogshow($me,$target){
    $me.click(function(){
        if ($me.hasClass('focus')){
            dialogclear();
        } else {
            dialogclear();
            $me.addClass('focus');
            $target.addClass('show');
        }
    })
}
//
console.log('main.js is loaded.');