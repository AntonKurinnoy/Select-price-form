function Set_Cookie( name, value ,days){
    if (days==undefined)
        days = 1;
    var todays_date = new Date();
    var expires_date = new Date(todays_date.getTime() + days*24*60*60*1000);
    if ( Get_Cookie( name ) ) document.cookie = name + '=' +
        ';expires=Thu, 01-Jan-1970 00:00:01 GMT';
    document.cookie = name + '=' + escape( value ) + ';expires=' + expires_date.toUTCString()+'; path=/';
}

function Get_Cookie( name ) {

    var start = document.cookie.indexOf( name + '=' );
    var len = start + name.length + 1;
    if ( ( !start ) && ( name != document.cookie.substring( 0, name.length ) ) ){
        return null;
    }
    if ( start == -1 ) return null;
    var end = document.cookie.indexOf( ';', len );
    if ( end == -1 ) end = document.cookie.length;
    try {
        res = decodeURI(unescape( document.cookie.substring( len, end ) ));
    }
    catch(err) {
        return null;
    }
    if (res==undefined)
        res = null;
    return res;
}

/***
 * функция для установки цены
 *
 * по ajax'у отправляем id продукта, которому определяем цену, а также id скидки
 * после обработки информация о цене (новая цена и период её действия) отображаются в таблице
 */
$('select[name="priceSelect"]').on('change',function () {
    var discountID = $('select[name="priceSelect"]').val();
    var prodID = $('div[name="setPrice"] table td[data="id"]').html();

    $.ajax({
        type: 'POST',
        url: '/',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        data: {discountID,prodID},
        success: function(result){
            if (result['discount'] !== "default"){
                $('div[name="setPrice"] table tr[data='+prodID+'] td[data="date_start"]').html(result['discount']['date_start']);
                $('div[name="setPrice"] table tr[data='+prodID+'] td[data="date_end"]').html(result['discount']['date_end']);
            } else {
                $('div[name="setPrice"] table tr[data='+prodID+'] td[data="date_start"]').html("");
                $('div[name="setPrice"] table tr[data='+prodID+'] td[data="date_end"]').html("");
            }
        }

    });

});

/***
 * выбираем способ определения цены
 */
$('select[name="priceType"]').on('change',function () {
    var type = $('select[name="priceType"]').val();
    Set_Cookie("setPriceWay",type);
    location.reload();
});

