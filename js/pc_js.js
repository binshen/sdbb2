$(function(){
    function hideTips(){
      $('#tips .tip').hide();
    };

    // 阻止冒泡事件
    function stopPropagation(e) {
      var e = window.event || e;
      if(e.stopPropagation){
        e.stopPropagation();
      }else{
        e.cancelBubble = true;
      }
    };

    (function() {
      var $tipUser = $('#tips .user');
      var $tipPw = $('#tips .pw');
      var fuc = {};
      fuc.showTipUser = function(){
        hideTips();
        $tipUser.show();
      }
      fuc.showTipPw = function(){
        hideTips();
        $tipPw.show();
      }
      fuc.showPw = function(){
        hideTips();
        $('#login').hide();
        $('#pw').show();
      }
      //
      var $user = $('#login .user input');
      var $pw = $('#login .pw input');
      var $login = $('#login .login');
      function onInputKeyUp(event){
        if(event.keyCode == 13 || event.keyCode == 108){
          $login.click();
        }
      };
      $user.on('keyup', onInputKeyUp);
      $pw.on('keyup', onInputKeyUp);
      $login.on('click', function(e){
        stopPropagation(e);

        var temp;
        temp = $.trim($user.val());   //console.log(userValue);
        if(temp == ''){
          $user.focus();
          return;
        }
        temp = $.trim($pw.val());   //console.log(userValue);
        if(temp == ''){
          $pw.focus();
          return;
        }
        window['submitFormLogin'](fuc);
      });

      $(window).click(function() {
        hideTips();
      });

      $('#tips .tip').on('click', function(e) {
        stopPropagation(e);
      });

    })();

    (function(){
      var $tipPw1 = $('#tips .pw1');
      var $tipPw2 = $('#tips .pw2');
      var fuc = {};
      fuc.showTipPw1 = function(){
        hideTips();
        $tipPw1.show();
      }
      fuc.showTipPw2 = function(){
        hideTips();
        $tipPw2.show();
      }
      fuc.showGoto = function(){
        hideTips();
        $('#pw').hide();
        $('#goto').show();
      }
      //
      var $pw1 = $('#pw .pw1 input');
      var $pw2 = $('#pw .pw2 input');
      var $save = $('#pw .save');
      function onInputKeyUp(event){
        if(event.keyCode == 13){
          $save.click();
        }
      };
      $pw1.on('keyup', onInputKeyUp);
      $pw2.on('keyup', onInputKeyUp);
      $save.on('click', function(e){
        stopPropagation(e);

        var temp;
        temp = $.trim($pw1.val());   //console.log(userValue);
        if(temp == ''){
          $pw1.focus();
          return;
        }
        var temp2 = $.trim($pw2.val());   //console.log(userValue);
        if(temp2 == ''){
          $pw2.focus();
          return;
        }
        if(temp != temp2){
          fuc.showTipPw2();
          return;
        }
        window['submitFormPw'](fuc);
      });
    })();

    (function() {
      var $pics = $('#pics .pic');
      var index = 0;
      var count = $pics.length;
      $pics.eq(index).fadeIn('slow');
      setInterval(function() {
        $pics.eq(index).fadeOut('slow', function() {
          index ++;
          if(index == count){
            index = 0;
          }
          $pics.eq(index).fadeIn('slow');
        });
      }, 5000);

    })();

    // 使用label代替placeholder
    (function() {
      var $input = $('.input');

      $input.each(function() {
        var $this = $(this);
        if($this.val() != '') {
          $this.siblings('.placeholder').hide();
        }
      }).focus(function() {
        var $this = $(this);
        $this.addClass('input-focus').siblings('.placeholder').hide();

      }).blur(function() {
        var $this = $(this);
        $this.removeClass('input-focus')
        if($this.val() == '') {
          $this.siblings('.placeholder').show();
        }
      });
      
    })();
});


