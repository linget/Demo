var Util = {
    dateFormat: function (date, fmt) {
        var o = {
            "M+": date.getMonth() + 1, //月份
            "d+": date.getDate(), //日
            "h+": date.getHours(), //小时
            "m+": date.getMinutes(), //分
            "s+": date.getSeconds(), //秒
            "q+": Math.floor((date.getMonth() + 3) / 3), //季度
            "S": date.getMilliseconds() //毫秒
        };
        if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (date.getFullYear() + "").substr(4 - RegExp.$1.length));
        for (var k in o) {
            if (new RegExp("(" + k + ")").test(fmt)) {
                fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) :
                    (("00" + o[k]).substr(("" + o[k]).length)));
            }
        }

        return fmt;
    },
    dayText: function (date) {
        var Week = ['日', '一', '二', '三', '四', '五', '六'];
        return ' 周' + Week[date.getDay()];
    },
    getTime: function (str) {
        var l = str.split(' ');
        l = l.slice(1);
        if (l.length > 0) {
            return l[0];
        }
        return '';
    },
    getUrlParams: function () {
        var result = {};
        var params = (window.location.search.split('?')[1] || '').split('&');
        for(var param in params) {
            if (params.hasOwnProperty(param)) {
                paramParts = params[param].split('=');
                result[paramParts[0]] = decodeURIComponent(paramParts[1] || "");
            }
        }
        return result;
    },
    goPage: function (key, appendObj) {
        var appendingStr = '';
        var urlArr = location.href.split('/');

        for (var k in appendObj) {
            appendingStr += '&' + k + '=' + appendObj[k];
        }

        urlArr.forEach(function (item, i) {
            if (item.match(/\S+\.html/)) {
                urlArr[i] = item.replace(/\S+\.html/, key + ".html");
            }
        });
        location.href = urlArr.join('/') + appendingStr;
    }
};
