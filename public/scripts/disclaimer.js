(function($){
  $(() => {
    /*
    * Show country disclaimer text on select field change
    */
    const $country = $('.disclaimer__form #countries');

    $country.on('change', function(){
      let countryCodeValue = $('option:selected').val();
      let countryContent = $('.country-content');
      let allowedCountries = $('.allowed-countries');
      let userTypes = $('#user-types');
      let userTypesOptions = userTypes.find('option');
      
      $(this).find('option:selected').each(function(){
        // Reset select user types
        userTypes.prop('selectedIndex', 0);

        if(countryCodeValue){
          // Show or hide country disclaimer text based on country selection.
          if(countryContent.hasClass(countryCodeValue)) {
            countryContent.not('.'+countryCodeValue).hide();
            $('.'+countryCodeValue).show();
          } else {
            countryContent.not('.'+countryCodeValue).hide();
          }

          // Set accept button to decline if not in country allow list
          if(!allowedCountries.hasClass(countryCodeValue)) {
            $('.disclaimer-modal-footer button[name="accept"]').hide();
            $('.disclaimer-modal-footer button[name="not-allowed"]').show();
            if(typeof $('.disclaimer-disclaimer__form #user-types option:selected').data('user-allowed') !== 'undefined') {
              if($('.disclaimer-disclaimer__form #user-types option:selected').data('user-allowed') === 0 || !allowedCountries.hasClass(countryCodeValue)) {
                $('.disclaimer-modal-footer button[name="accept"]').hide();
                $('.disclaimer-modal-footer button[name="not-allowed"]').show();
              } else {
                $('.disclaimer-modal-footer button[name="not-allowed"]').hide();
                $('.disclaimer-modal-footer button[name="accept"]').show();
              }
            }
          } else {
            $('.disclaimer-modal-footer button[name="not-allowed"]').hide();
            $('.disclaimer-modal-footer button[name="accept"]').show();
            if(typeof $('.disclaimer__form #user-types option:selected').data('user-allowed') !== 'undefined') {
              if($('.disclaimer__form #user-types option:selected').data('user-allowed') !== 0 || allowedCountries.hasClass(countryCodeValue)) {
                $('.disclaimer-modal-footer button[name="not-allowed"]').hide();
                $('.disclaimer-modal-footer button[name="accept"]').show();
              } else {
                $('.disclaimer-modal-footer button[name="accept"]').hide();
                $('.disclaimer-modal-footer button[name="not-allowed"]').show();
              }
            }
          }
        }

        // Hide certain user types based on selected countries
        userTypesOptions.each(function() {
          if(typeof $(this).data('selected-countries') !== 'undefined') {
            const explodeSelectedCountries = $(this).attr('data-selected-countries').split('|');
            const selectCountries = $(this).attr('data-selected-countries');
            if (explodeSelectedCountries.includes(countryCodeValue)) {
              userTypes.show().attr('required', true);
              userTypes.find('option[data-selected-countries="'+ selectCountries +'"]').show();
            } else {
              if(selectCountries !== 'all') {
                userTypes.hide().removeAttr('required');
                userTypes.find('option[data-selected-countries="'+ selectCountries +'"]').hide();
              }else {
                userTypes.show().attr('required', true);
                userTypes.find('option[data-selected-countries="'+ selectCountries +'"]').show();
              }
            }
          }
        });
        
      });
    });

    $('.disclaimer__form #user-types').on('change', function(){
      let allowedCountries = $('.allowed-countries');
      $(this).find('option:selected').each(function(){
        if(typeof $(this).data('user-allowed') !== 'undefined') {
          if($(this).data('user-allowed') === 0) {
            $('.disclaimer-modal-footer button[name="accept"]').hide();
            $('.disclaimer-modal-footer button[name="not-allowed"]').show();
            if(!allowedCountries.hasClass($('.disclaimer__form #countries option:selected').val()) || $(this).data('user-allowed') === 0) {
              $('.disclaimer-modal-footer button[name="accept"]').hide();
              $('.disclaimer-modal-footer button[name="not-allowed"]').show();
            } else {
              $('.disclaimer-modal-footer button[name="not-allowed"]').hide();
              $('.disclaimer-modal-footer button[name="accept"]').show();
            }
          } else {
            $('.disclaimer-modal-footer button[name="not-allowed"]').hide();
            $('.disclaimer-modal-footer button[name="accept"]').show();
            if(!$('.disclaimer-disclaimer__form #countries option:selected').hasClass('default')) {
              if(allowedCountries.hasClass($('.disclaimer__form #countries option:selected').val()) || $(this).data('user-allowed') === 0) {
                $('.disclaimer-modal-footer button[name="not-allowed"]').hide();
                $('.disclaimer-modal-footer button[name="accept"]').show();
              } else {
                $('.disclaimer-modal-footer button[name="accept"]').hide();
                $('.disclaimer-modal-footer button[name="not-allowed"]').show();
              }
            }
          }
        }
      });
    });

    /*
    * Set disclaimer cookie to declined and redirect user
    * to disclaimer denied page if disclaimer declined,
    * or a country is selected that is not allowed.
    */
    const $declineButton = $('.disclaimer__form button[name="decline"]');
    const $notAllowedButton = $('.disclaimer__form button[name="not-allowed"]');

    $declineButton.on('click', function() {
      setDisclaimerCookie('disclaimer', 'declined', 1);
      window.location = $('.deny-url').data('deny-page');
    });

    $notAllowedButton.on('click', function(event) {
      if ($('.disclaimer__form')[0].checkValidity() === false) {
        event.preventDefault();
        event.stopPropagation();
      } else {
        setDisclaimerCookie('disclaimer', 'declined', 1);
        window.location = $('.deny-url').data('deny-page');
      }
      $('.disclaimer__form').addClass('was-validated');
    });

    /*
    * Show disclaimer only if cookie is not set or is declined.
    * Add classes to <body> based on cookie values set.
    */
    const disclaimerCookie = getCookie('disclaimer');
    const countryCodeCookie = getCookie('countryCode');
    let countryClassName = 'disclaimer-country-'+countryCodeCookie;

    if (disclaimerCookie === ''){
      $('#disclaimerModal').addClass('show');
      $('.disclaimer').addClass('show');
      $('body').removeClass('disclaimer-accepted disclaimer-declined '+countryClassName);
    } else if (disclaimerCookie === 'accepted') {
      $('.disclaimer').addClass('hide');
      $('body').addClass('disclaimer-accepted '+countryClassName).removeClass('disclaimer-declined');
    } else if (disclaimerCookie === 'declined') {
      $('#disclaimerModal').addClass('show');
      $('.disclaimer').addClass('show');
      $('body').addClass('disclaimer-declined').removeClass('disclaimer-accepted '+countryClassName);
    }
  });

  /*
  * Disclaimer form validation
  */
  window.addEventListener('load', function() {
    // Get disclaimer form
    const disclaimerForm = document.getElementsByClassName('disclaimer__form');

    // Loop over form and prevent submission
    const validation = Array.prototype.filter.call(disclaimerForm, function(form) {
      form.addEventListener('submit', function(event) {
        /*
        * Check if form submitted and set disclaimer cookies
        * based on accept or decline buttons
        */
        if (form.checkValidity() === false) {
          event.preventDefault();
          event.stopPropagation();
          $('.disclaimer-modal-footer p').show();
        } else if ($('button[name="accept"]').on('click') && form.checkValidity() === true) {
          setDisclaimerCookie('disclaimer', 'accepted', 1);
          setCountryCodeCookie('countryCode', $('#countries').val(), 1);
          setUserTypeCookie('userType', $('#user-types').val(), 1);
        } else if ($('button[name="decline"]').on('click') && form.checkValidity() === true) {
          setDisclaimerCookie('disclaimer', 'declined', 1);
          window.location = $('.deny-url').data('deny-page');
        }

        form.classList.add('was-validated');
      }, false);
    });
  }, false);


  /*
  * Set cookie function for disclaimer acceptance
  */
  function setDisclaimerCookie(name, value, days) {
    let expires = '';

    if (days) {
      const date = new Date();
      date.setTime(date.getTime() + (days*24*60*60*1000));
      expires = '; expires=' + date.toUTCString();
    }
    document.cookie = name + '=' + (value || '')  + expires + '; path=/; samesite=strict;';
  }

  /*
  * Set cookie function for disclaimer country
  */
  function setCountryCodeCookie(name, value, days){
    let expires = '';
    const date = new Date();
    date.setTime(date.getTime() + (days*24*60*60*1000));
    expires = '; expires=' + date.toUTCString();
    document.cookie = name + '=' + (value || '')  + expires + '; path=/; samesite=strict;';
  }

  /*
  * Set cookie function for user role types
  */
  function setUserTypeCookie(name, value, days){
    let expires = '';
    const date = new Date();
    date.setTime(date.getTime() + (days*24*60*60*1000));
    expires = '; expires=' + date.toUTCString();
    document.cookie = name + '=' + (value || '')  + expires + '; path=/; samesite=strict;';
  }

  /*
  * Return the value of a specified cookie
  */
  function getCookie(cname) {
    let name = cname + '=';
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');

    for(var i = 0; i < ca.length; i++) {
      let c = ca[i];

      while (c.charAt(0) === ' ') {
        c = c.substring(1);
      }

      if (c.indexOf(name) === 0) {
        return c.substring(name.length, c.length);
      }
    }

    return '';
  }
})( jQuery );