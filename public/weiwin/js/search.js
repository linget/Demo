$(function () {
    var $currentPlace;
    var $mainPanel = $('#div_traffic');
    var $cityPanel = $('#CitySelect');
 //   Data.cityInit();

    /*** MainPart.start ***/
    // 顶部tab切换
    $mainPanel.find('.mod-tabs li a').on('click', function () {
        var $this = $(this);
        var key = $this.data('key');

        $this.closest('ul').find('li').removeClass('curr');
        $this.closest('li').addClass('curr');
        $('.mod-search-condition').hide();
        $('.mod-search-condition[data-key="' + key + '"]').show();
    });

    // 切换出发城市和目的城市
    var i = 2;
	var j = 2;
    $mainPanel.find('.change-city').on('click', function () {
		var code=$(this).data('code');
		var $city0 = '';
        var $city1 = '';
		var city0 = '';
        var city1 = '';
        var code0 = '';
        var code1 = '';
		if(code == 'train'){
			i++;
            is = parseInt(i%2);
		    $city0 = $('.select-city').eq(2);
            $city1 = $('.select-city').eq(3);
			city0 = $city0.text();
			city1 = $city1.text();
			code0 = $city0.attr('data-code');
			code1 = $city1.attr('data-code');
			/*  切换代码 */
			if(is == 1){
				$('#train_kai').css("display","");
				$('#train_dao').css("display","none");
				$('#train_kaikai').css("display","none");
				$('#train_daodao').css("display","");
			}else{
				 $('#train_kai').css("display","none");
				 $('#train_dao').css("display","");
				 $('#train_daodao').css("display","none");
				 $('#train_kaikai').css("display",""); 
			}
			 $("#train_kai").html(city1);
			 $("#train_dao").html(city0);
			 $("#train_kaikai").html(city1);
			 $("#train_daodao").html(city0);
			 $("#train_city_kai").val(city1);
			 $("#train_city_dao").val(city0);
			 $city0.html(city1).attr('data-code', code1);
             $city1.html(city0).attr('data-code', code0);
			
		}else{
			j++;
            js = parseInt(j%2);
			$city0 = $('.select-city').eq(0);
            $city1 = $('.select-city').eq(1);
			city0 = $city0.text();
			city1 = $city1.text();
			code0 = $city0.attr('data-code');
			code1 = $city1.attr('data-code');
			/*  切换代码 */
			if(js == 1){
            $('#plane_kai').css("display","");
            $('#plane_dao').css("display","none");
            $('#plane_kaikai').css("display","none");
            $('#plane_daodao').css("display","");
			}else{
				 $('#plane_kai').css("display","none");
				 $('#plane_dao').css("display","");
				 $('#plane_daodao').css("display","none");
				 $('#plane_kaikai').css("display","");
			}
			$("#plane_kai").html(city1);
			$("#plane_dao").html(city0);
			$("#plane_kaikai").html(city1);
			$("#plane_daodao").html(city0);
			$("#plane_city_kai").val(city1);
			$("#plane_city_dao").val(city0);
			$city0.html(city1).attr('data-code', code1);
            $city1.html(city0).attr('data-code', code0);
		} 
    });

    // 出发地和目的地选择
    $mainPanel.find('.select-city').on('click', function () {
        var $this = $(this);
		var kind=$(this).attr('data-kind');
		document.cookie="traffic="+kind;
        $currentPlace = $this;
        $mainPanel.hide();
        $('#CitySelect').show();
		updateList('inland');
    });


    // 搜索按钮的触发
    $mainPanel.delegate('.airline-search', 'click', function () {
        var $this = $(this);
        var $searchWrapper = $this.closest('.search');
        var key = $mainPanel.find('.mod-tabs .item.curr a').data('key');
        var $sectionWrapper = $mainPanel.find('.mod-search-condition[data-key="' + key + '"]');
        var startCityCode = $sectionWrapper.find('.select-city').eq(0).attr('data-code');
        var destCityCode = $sectionWrapper.find('.select-city').eq(1).attr('data-code');
        var options = {};

        if (key == 'city') {
            options.sign = 0;
            options.searchStr = '';
            options.startCity = $sectionWrapper.find('.select-city').eq(0).attr('data-code');
            options.destCity = $sectionWrapper.find('.select-city').eq(1).attr('data-code');
        } else {
            options.sign = 1;
            options.searchStr = $mainPanel.find('#AirlineInput').val();
            options.startCity = '';
            options.destCity = '';
        }
        options.date = $sectionWrapper.find('span.date').text();
        $searchWrapper.find('a').hide();
        $searchWrapper.find('span').show();
        Util.goPage('list', options);
    });

    // 没有搜到结果时的遮罩按钮的回调
    $mainPanel.find('#NoSearchResult .btn-confirm').on('click', function () {
        $mainPanel.find('#NoSearchResult').hide();
    });

    /*** MainPart.end ***/


    /*** CitySelect.start 城市列表选择页面 ***/
    var updateList = function (key, character, searchStr) {
		Data.cityInit();
        var htmlStr;
        var renderData = Data.cityData[key];
        if (character) {
            renderData = renderData.filter(function (item) {
                return item.character == character;
            });
        }
		if(key == 'inland'){
			if (searchStr) {
            renderData.forEach(function (item) {
                item.cities = item.cities.filter(function (cityItem) {
                    return cityItem.str.match(searchStr.toUpperCase()) || cityItem.name.match(searchStr);
                });
            });
        }
            renderData.forEach(function (d) {
            d.hasTitle = d.cities.length > 0;
        });
            htmlStr = Mustache.render($('#CityListTmpl').val(), {data: renderData});
		}else{
			if (searchStr) {
            renderData.forEach(function (item) {
                item.cities_out = item.cities_out.filter(function (cityItem) {
                    return cityItem.str.match(searchStr.toUpperCase()) || cityItem.name.match(searchStr);
                });
            });
        }
            renderData.forEach(function (d) {
            d.hasTitle = d.cities_out.length > 0;
        });
            htmlStr = Mustache.render($('#CityListTmpl_out').val(), {data: renderData});
		}
        
        $('#AllCities').html(htmlStr);
    };

    // 顶部tab切换  国内国外切换
    $cityPanel.find('#in_out').delegate('a', 'click', function () {
        var $this = $(this);
        var key = $this.data('key');
        $this.closest('ul').find('li').removeClass('curr');
        $this.closest('li').addClass('curr');
        updateList(key);
    });

    // 城市选中后的处理
    $cityPanel.find('#AllCities').delegate('a.city-item', 'click', function () {
        var code = $(this).data('code');
        var name = $(this).text();
        $currentPlace.text(name);
		$currentPlace.next().val(name);
        $currentPlace.attr('data-selected', '1');
        $currentPlace.attr('data-code', code);
        $mainPanel.show();
        $cityPanel.hide();
    });

    // 城市右侧快捷选中的处理
    $cityPanel.find('ul.mod-cities-helper li').on('click', function () {
        var key = $cityPanel.find('.mod-tabs li.curr a').data('key');
        var character = $(this).text();
        updateList(key, character);
    });

    // 城市右侧快捷选中的处理
    $cityPanel.find('ul.mod-cities-helper li').on('click', function () {
        var key = $cityPanel.find('.mod-tabs li.curr a').data('key');
        var character = $(this).text();

        character  = character == '全部' ? null : character;
        updateList(key, character);
    });

    // 城市搜索处理
    $cityPanel.find('.mod-search-city input').on('keyup', function () {
        var key = $cityPanel.find('.mod-tabs li.curr a').data('key');
        var searchStr = $(this).val();
		if(searchStr == null || searchStr == undefined || searchStr == ""){
			$('#a_2_z').show();
		}else{
			$('#a_2_z').hide();
		}
		updateList(key, null, searchStr);
    });
    /*** CitySelect.end ***/
});


