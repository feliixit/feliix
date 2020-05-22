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
})