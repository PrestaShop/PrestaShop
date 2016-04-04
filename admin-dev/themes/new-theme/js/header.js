import $ from 'jquery';

export default class Header {
  constructor() {
    $(() => {
      this.initQuickAccess();
    });
  }
  initQuickAccess() {
    $('.js-quick-link').on('click', (e) => {
      e.preventDefault();

      let method = $(e.target).data('method');
      let name = null;

      if (method === 'add') {
        let text = $(e.target).data('prompt-text');
        let link = $(e.target).data('link');

        name = prompt(text, link);
      }
      if (method === 'add' && name || method === 'remove') {
        let postLink = $(e.target).data('post-link');
        let quickLinkId = $(e.target).data('quicklink-id');
        let rand = $(e.target).data('rand');
        let url = $(e.target).data('url');
        let icon = $(e.target).data('icon');
        console.log(url)
        $.ajax({
          type: 'POST',
          headers: {
            "cache-control": "no-cache"
          },
          async: true,
          url: `${postLink}&action=GetUrl&rand=${rand}&ajax=1&method=${method}&id_quick_access=${quickLinkId}`,
          data: {
            "url": url,
            "name": name,
            "icon": icon
          },
          dataType: "json",
          success: (data) => {
            var quicklink_list = '';
            $.each(data, (index) => {
              if (typeof data[index]['name'] !== 'undefined')
                quicklink_list += '<li><a href="' + data[index]['link'] + '&token=' + data[index]['token'] + '"><i class="icon-chevron-right"></i> ' + data[index]['name'] + '</a></li>';
            });

            if (typeof data['has_errors'] !== 'undefined' && data['has_errors'])
              $.each(data, (index) => {
                if (typeof data[index] === 'string')
                  $.growl.error({
                    title: "",
                    message: data[index]
                  });
              });
            else if (quicklink_list) {
              console.log(quicklink_list)
              $("#header_quick ul.dropdown-menu").html(quicklink_list);
              window.showSuccessMessage(window.update_success_msg);
            }
          }
        });
      }
    });
  }
}
