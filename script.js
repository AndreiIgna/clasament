jQuery(function($) {


  $('#accordion li > a').click(function(e) {
    //
  });


  $('.accordion input[type="checkbox"]').change(function() {
    var ids = [];
    $(this).closest('.accordion').find('input:checked').each(function() {
      ids.push($(this).val());
    });

    url = Rkm.build_url({concursuri: ids.join(',')});

    window.location = url;
  });


});

























Rkm = window['Rkm'] || {};

Rkm.get_param = function(name) {
    var match = RegExp('[?&]' + name + '=([^&]*)').exec(window.location.search);
    return match ? decodeURIComponent(match[1].replace(/\+/g, ' ')) : null;
}

Rkm.parse_str = function(s) {
  var rv = {}, decode = window.decodeURIComponent || window.unescape;
  (s == null ? location.search : s).replace(/^[?#]/, "").replace(/([^=&]*?)((?:\[\])?)(?:=([^&]*))?(?=&|$)/g, function ($, n, arr, v) {
    if (n == "") return;
    n = decode(n);
    v = decode(v);
    if (arr) {
      (typeof rv[n] == "object") ? rv[n].push(v) : rv[n] = [v];
    } else {
      rv[n] = v;
    }
  });
  return rv;
}

Rkm.build_url = function(new_params, keep_old_params, url) {
  new_params = new_params || {};
  if(!jQuery.isPlainObject(new_params)) new_params = Rkm.parse_str(new_params);
  keep_old_params = keep_old_params === false ? false : true;
  url = url || window.location+'';
  url = url.split('#')[0];
  params = {};
  hash = '';

  if(url.indexOf('?') != -1) {
    url = url.split('?');
    if(url[1].indexOf('#') != -1) {
      new_url = url[1].split('#');
      url[1] = new_url[0];
      hash = '#' + new_url[1];
    }
    params = Rkm.parse_str(url[1]);
    url = url[0];
  }

  return url + '?' + jQuery.param(jQuery.extend(params, new_params)) + hash;
}
