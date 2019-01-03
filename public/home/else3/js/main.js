$(document).ready(function(){

    // Disable href="#" links
    $('a').click(function(){if ($(this).attr('href') == '#') {return false;}});
    $('a').live('click', function(){if ($(this).attr('href') == '#') {return false;}});

    // tooltip
    $("a[rel^='tooltip']").tooltip();



    // 标签库的js
    
    $(".show-labelitem").on("click",function(){
    $(this).hide();
    $(".hide-labelitem").show();
    $("#labelItem").show();
  });
  $(".hide-labelitem").on("click",function(){
    $(this).hide();
    $(".show-labelitem").show();
    $("#labelItem").hide();
  });
  $(".label-item").on("click","li",function(){
    var id = $(this).attr("data");
    var text = $(this).children("span").html();
    var labelHTML = "<li data='"+id+"''>x "+text+"</li>";
    if($(this).hasClass("selected")){
      return false;
    }else if($(".label-selected").children("li").length >= 8){
      layer.msg("最多可以选择8个哦");
      return false;
    }
    $(".label-selected").append(labelHTML);
    val = '';
    for(var i = 0; i < $(".label-selected").children("li").length; i++){
      val += $(".label-selected").children("li").eq(i).attr("data")+',';
    }
    $("input[name='biaoqian_id']").val(val);
    $(this).addClass("selected");
  });
  var val = "";
  $(".label-selected").on("click","li",function(){
    var id = $(this).attr("data");
    val = '';
    $(this).remove();
    for(var i = 0; i < $(".label-selected").children("li").length; i++){
      val += $(".label-selected").children("li").eq(i).attr("data")+',';
    }
    $("input[name='biaoqian_id']").val(val);
    $(".label-item").find("li[data='"+id+"']").removeClass("selected");
  });


  //点击更换标签
  var limit = 0;
  $(".replacelable").on("click",function(){
    layer.load(1);
    limit += 32;
    $.ajax({
      url:window.location.href,
      type:"post",
      datatype:"json",
      data:{
        labellimit:limit
      },
      success:function(data){
        layer.closeAll('loading');
        $(".label-item").find("li").remove();//删除原先所有，本来想让数据表随机搜索的，想着有点mmp，就没搞，用的是limit
        var html = '';
        for(var i in data){
          html += "<li data=\""+data[i].term_id+"\">x<span>"+data[i].name+"</span></li>";//拼接标签
        }
        $(".label-item").append(html);//追加至文档
      },
      error:function(){
        layer.closeAll('loading');
        layer.msg("错误~~~");
      }
    })
  })





    //prettyPhoto
    $('a[data-rel]').each(function() {
        $(this).attr('rel', $(this).data('rel'));
    });
    $("a[rel^='prettyPhoto']").prettyPhoto();

    //Menu
    jQuery('#menu > ul').superfish({ 
        delay:       0,
        animation:   {
            opacity:'show',
            height:'show'
        },
        speed:       'fast',
        autoArrows:  false
    });
    (function() {
		var $menu = $('#menu ul'),
			optionsList = '<option value="" selected>Menu...</option>';

		$menu.find('li').each(function() {
			var $this   = $(this),
				$anchor = $this.children('a'),
				depth   = $this.parents('ul').length - 1,
				indent  = '';

			if( depth ) {
				while( depth > 0 ) {
					indent += ' - ';
					depth--;
				}
			}
			optionsList += '<option value="' + $anchor.attr('href') + '">' + indent + ' ' + $anchor.text() + '</option>';
		}).end().after('<select class="res-menu">' + optionsList + '</select>');

		$('.res-menu').on('change', function() {
			window.location = $(this).val();
		});
		
	})();
    
    //Flickr Widget Footer
    $('#footer .flickr').jflickrfeed({
        limit: 8,
        qstrings: {
            id: '36621592@N06'
        },
        itemTemplate: ''+
            '<li class="thumbnail">'+
                '<a rel="prettyPhoto[flickr]" href="{{image}}" title="{{title}}">' +
                    '<img src="{{image_s}}" alt="{{title}}" />' +
                    '<span class="frame-overlay"></span>' +
                '</a>' +
            '</li>'
    }, function(data) {
        $("a[rel^='prettyPhoto']").prettyPhoto();
    });

	//Flickr Widget Sidebar
    $('#sidebar .sidebar-flickr').jflickrfeed({
		limit: 8,
		qstrings: {
			id: '36621592@N06'
		},
		itemTemplate: ''+
            '<li class="thumbnail">'+
                '<a rel="prettyPhoto[flickr]" href="{{image}}" title="{{title}}">' +
                    '<img src="{{image_s}}" alt="{{title}}" />' +
                    '<span class="frame-overlay"></span>' +
                '</a>' +
            '</li>'
	}, function(data) {
		$("a[rel^='prettyPhoto']").prettyPhoto();
	});

	//Portfolio
	var $portfolioClone = $(".filtrable").clone();
	$("#filtrable a").live('click', function(e){
		
		$("#filtrable li").removeClass("current");	
		
		var $filterClass = $(this).parent().attr("class");
        var $filteredPortfolio = $portfolioClone.find("article");

		if ( $filterClass == "all" ) {
			$filteredPortfolio = $portfolioClone.find("article");
		} else {
			$filteredPortfolio = $portfolioClone.find("article[data-type~=" + $filterClass + "]");
		}
	
		$(".filtrable").quicksand( $filteredPortfolio, { 
			duration: 800, 
			easing: 'easeOutQuint' 
		}, function(){
            $("a[rel^='prettyPhoto']").prettyPhoto();
		});

		$(this).parent().addClass("current");
        
		e.preventDefault();
	});

    // To Top Button
    $(function(){
        $().UItoTop({ easingType: 'easeOutQuart' });
    });
                       
});

$(window).load(function() {
	// Slider
    $("#mainslider").flexslider({
		animation: "slide",
        slideshow: false, // ***
        useCSS: false,
		controlNav: true,
		animationLoop: false,
		smoothHeight: true
	});
    // Accordion settings
    $(function() {
        $('.accordion').on('show', function (e) {
            $(e.target).prev('.accordion-heading').find('i').removeClass('icon-plus');
            $(e.target).prev('.accordion-heading').find('i').addClass('icon-minus');
        });
        $('.accordion').on('hide', function (e) {
            $(e.target).prev('.accordion-heading').find('i').removeClass('icon-minus');
            $(e.target).prev('.accordion-heading').find('i').addClass('icon-plus');
        });
    });
});
