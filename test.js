setInterval(function() {
    var limit = 225.5;
    var first = $('.scores > div.first_team').text();
    var second = $('.scores > div.second_team').text();
    var toplam = Number(first) + Number(second);
    var kalan = limit - toplam;

    console.log('Toplam: ' + toplam + ' Kalan sayı: ' + kalan);
}, 5000);

