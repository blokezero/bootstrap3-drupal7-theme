(function ($) {
  Drupal.behaviors.wonderpedia = {
    attach: function (context, settings) {
      // $('#front-subs-modal').modal();
      $('#about').popover({
        'title': 'About Wonderpedia',
        'content': "Wonderpedia offers a unique mix of content from the most important moments in history to futuristic technology, from the secrets of the animal kingdom to gripping world events, from the far reaches of outer space to the inner workings of the human mind. Wonderpedia targets a broad consumer base appealing to everyone with a curious nature and a thirst for knowledge with a core target of men aged 25-54 who need to know ‘something about everything and everything about something’.",
        'html': true    
      });
    }
  };
})(jQuery);