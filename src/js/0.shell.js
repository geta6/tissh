(function () {

  var history = [], current = 0;

  $(window).on('click',  function () { $('#cmd').focus() });

  $(window).on('resize', function () { $('#cmd').css({width:parseInt($(this).width()*0.9-$('#pre').width() )+'px'}) });

  $(window).on('keydown', '#cmd', function (e) {
    switch (e.keyCode) {
    case 9 : // TAB
      e.preventDefault();
      $('#cmd').val('補完し.たい.けど.だるいのでやら.ない.jp');
      break;
    case 13 : // RETURN
      $(window).trigger('EXECUTE');
      break;
    case 38 : // UP
      if (0 < current) $('#cmd').val(history[--current]);
      break;
    case 40 : // DOWN
      if (current < history.length) $('#cmd').val(history[++current]);
      break;
    }
  });

  $(window).on('EXECUTE', function () {
    var line = $('#cmd').val(), face;
    $.ajax({
      async: false,
      url: '/init',
      complete: function (res) {
        res  = JSON.parse(res.responseText);
        face = res.user + '@' + res.host + ' $ ';
      }
    });
    $('#last').attr({id:''}).text(face + line);
    if (line) {
      if (history[history.length-1] != line) history.push(line);
      current = history.length;
      $.ajax({
        async: false,
        url: '/exec',
        data: {cmd:line},
        complete: function (res) {
          var json = 'tissh/return' != res.getResponseHeader('Content-Type')
            ? { text : 'tissh: command abort' }
            : JSON.parse(res.responseText);
          if ('undefined' != typeof(json.text)) {
            $('#shell').append($('<pre>').addClass('res').text(json.text));
          }
          if ('undefined' != typeof(json.exec)) {
            eval(json.exec);
          }
          if ('undefined' != typeof(json.html)) {
            $('#shell').append($('<pre>').addClass('res').html(json.html));
          }
        }
      });
    }
    $('#shell').append(
      $('<div>').attr({id:'last'}).addClass('req')
        .append($('<span>').attr({id:'pre'}).html(face))
        .append($('<input>').attr({id:'cmd',type:'text'}))
    );
    $('html, body').animate({scrollTop: $(document).height()}, 0);
    $(window).trigger('resize').trigger('click');
  });

  $(window).trigger('click').trigger('resize').trigger('EXECUTE');

  $(document).on('click', '.exe', function () {
    $('#cmd').val($(this).html());
    $(window).trigger('EXECUTE');
  });

}());
