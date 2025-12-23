document.addEventListener('DOMContentLoaded', function () {
    console.log('[ChinaMap] 脚本已加载 - 悬浮框版');

    // ---------------------------------------------------------
    // 修复：强制调整地图容器高度，解决底部显示不全的问题
    // ---------------------------------------------------------
    var mapObj = document.getElementById('china-map-object');
    if (mapObj) {
        // 900px 通常足以显示包含南海诸岛的完整中国地图
        mapObj.style.height = '900px'; 
    }
    // ---------------------------------------------------------

    var baseUrl = window._EVENT_INDEX_URL || '/event/index';
    
    // 创建悬浮框 (Tooltip)
    var tooltip = document.createElement('div');
    tooltip.id = 'map-tooltip';
    tooltip.style.cssText = 'position:absolute; display:none; background:rgba(255,255,255,0.95); border:1px solid #ccc; padding:15px; border-radius:4px; box-shadow:0 4px 15px rgba(0,0,0,0.2); z-index:9999; min-width:200px; pointer-events:auto; font-size:14px; line-height:1.6; color:#333; text-align:left;';
    document.body.appendChild(tooltip);

    var hideTimeout; // 用于控制悬浮框消失的延时

    var provinceMap = {
        "Shaanxi Province": "陕西省",
        "Shanghai Municipality": "上海市", 
        "Chongqing Municipality": "重庆市",
        "Zhejiang Province": "浙江省",
        "Jiangxi Province": "江西省",
        "Yunnan Province": "云南省",
        "Shandong Province": "山东省",
        "Liaoning Province": "辽宁省",
        "Beijing Municipality": "北京市",
        "Tianjin Municipality": "天津市",
        "Hebei Province": "河北省",
        "Shanxi Province": "山西省",
        "Inner Mongolia Autonomous Region": "内蒙古自治区",
        "Jilin Province": "吉林省",
        "Heilongjiang Province": "黑龙江省",
        "Jiangsu Province": "江苏省",
        "Anhui Province": "安徽省",
        "Fujian Province": "福建省",
        "Henan Province": "河南省",
        "Hubei Province": "湖北省",
        "Hunan Province": "湖南省",
        "Guangdong Province": "广东省",
        "Guangxi Zhuang Autonomous Region": "广西壮族自治区",
        "Hainan Province": "海南省",
        "Sichuan Province": "四川省",
        "Guizhou Province": "贵州省",
        "Tibet Autonomous Region": "西藏自治区",
        "Gansu Province": "甘肃省",
        "Qinghai Province": "青海省",
        "Ningxia Hui Autonomous Region": "宁夏回族自治区",
        "Xinjiang Uygur Autonomous Region": "新疆维吾尔自治区",
        "Taiwan Province": "台湾省",
        "Hong Kong SAR": "香港特别行政区",
        "Macao SAR": "澳门特别行政区"
    };

    // 辅助函数：根据地图名称获取对应的事件列表
    function getEventsForMapName(mapName, data) {
        // data 结构: { "上海": [...], "陕西": [...] }
        for (var dbName in data) {
            // 模糊匹配：兼容 "上海" 和 "上海市"
            if (dbName === mapName || mapName.indexOf(dbName) > -1 || dbName.indexOf(mapName) > -1) {
                return data[dbName];
            }
        }
        return null;
    }

    function initMap(svgDoc) {
        // 1. 查找圆点
        var circles = svgDoc.querySelectorAll('#label_points circle');
        
        // 兼容性回退
        if (!circles.length) {
             circles = svgDoc.querySelectorAll('circle[class*="Province"], circle[class*="Municipality"]');
        }

        if (!circles.length) {
            console.warn('[ChinaMap] 警告: 未找到任何 circle 元素。');
            return;
        }

        // 初始化样式
        circles.forEach(function(circle) {
            if (!circle.getAttribute('r') || circle.getAttribute('r') == '0') circle.setAttribute('r', 6);
            if (!circle.style.fill && !circle.getAttribute('fill')) circle.style.fill = '#555';
            circle.style.opacity = '1';
            circle.style.transition = 'all 0.3s ease';
        });

        // 3. 请求后端数据
        var sep = baseUrl.indexOf('?') === -1 ? '?' : '&';
        var fetchUrl = baseUrl + sep + 'action=get-active-locations';
        
        fetch(fetchUrl)
            .then(response => response.json())
            .then(activeData => {
                console.log('[ChinaMap] 事件数据:', activeData);

                if (!activeData || Object.keys(activeData).length === 0) return;

                circles.forEach(function (circle) {
                    var className = circle.getAttribute('class');
                    if (!className) return;
                    
                    className = className.trim();
                    var mapChineseName = provinceMap[className];

                    // 获取该省份的事件列表
                    var events = getEventsForMapName(mapChineseName, activeData);

                    if (mapChineseName && events && events.length > 0) {
                        // 激活样式
                        circle.style.cursor = 'pointer';
                        circle.style.fill = '#d9534f';
                        
                        var originalR = circle.getAttribute('r');

                        // 鼠标移入：显示悬浮框
                        circle.addEventListener('mouseenter', function (e) {
                            // 高亮圆点
                            this.style.fill = '#ff0000';
                            this.setAttribute('r', parseFloat(originalR) + 4);

                            // 构建悬浮框内容
                            var html = '<div style="font-weight:bold; margin-bottom:8px; border-bottom:1px solid #eee; padding-bottom:5px; font-size:16px;">' + mapChineseName + '</div>';
                            html += '<ul style="margin:0; padding-left:20px; max-height:250px; overflow-y:auto;">';
                            
                            events.forEach(function(ev) {
                                // 构建跳转链接：替换 event/index 为 timeline/view
                                var url = baseUrl;
                                
                                // 针对 Yii2 默认路由 ?r=event%2Findex 或 ?r=event/index 进行处理
                                if (url.indexOf('event%2Findex') > -1) {
                                    // 情况1: URL编码的路由 (例如 index.php?r=event%2Findex)
                                    url = url.replace('event%2Findex', 'timeline%2Fview');
                                    url += '&id=' + ev.id;
                                } else if (url.indexOf('event/index') > -1) {
                                    // 情况2: 未编码路由或伪静态 (例如 index.php?r=event/index 或 /event/index)
                                    url = url.replace('event/index', 'timeline/view');
                                    // 如果已有参数(如 ?r=...)，用 & 连接，否则用 ?
                                    if (url.indexOf('?') > -1) {
                                        url += '&id=' + ev.id;
                                    } else {
                                        url += '?id=' + ev.id;
                                    }
                                } else {
                                    // 兜底情况
                                    if (url.indexOf('?') > -1) {
                                        url += '&id=' + ev.id;
                                    } else {
                                        url += '?id=' + ev.id;
                                    }
                                }

                                html += '<li style="margin-bottom:5px;"><a href="' + url + '" target="_blank" style="text-decoration:none; color:#0056b3; display:block;">' + ev.title + '</a></li>';
                            });
                            html += '</ul>';

                            tooltip.innerHTML = html;
                            tooltip.style.display = 'block';

                            // 修改：基于圆点元素位置定位，确保悬浮框始终在省份圆点附近
                            var circleRect = this.getBoundingClientRect(); // 圆点相对 SVG 视口的位置
                            var mapRect = mapObj.getBoundingClientRect();  // 地图容器相对浏览器视口的位置
                            
                            var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                            var scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;

                            // 计算绝对坐标 (相对于文档)
                            // 默认显示在圆点右侧：地图X + 滚动X + 圆点X + 圆点宽 + 间距
                            var left = mapRect.left + scrollLeft + circleRect.left + circleRect.width + 10;
                            // 垂直居中稍偏上
                            var top = mapRect.top + scrollTop + circleRect.top + (circleRect.height / 2) - 20;

                            // 防止溢出屏幕右侧 (假设悬浮框宽度约 230px)
                            // 使用 clientWidth 获取可视区域宽度
                            if (mapRect.left + circleRect.left + 250 > document.documentElement.clientWidth) {
                                left = mapRect.left + scrollLeft + circleRect.left - 230; // 改为显示在圆点左侧
                            }

                            tooltip.style.left = left + 'px';
                            tooltip.style.top = top + 'px';

                            clearTimeout(hideTimeout);
                        });

                        // 鼠标移出：延时隐藏
                        circle.addEventListener('mouseleave', function () {
                            this.style.fill = '#d9534f';
                            this.setAttribute('r', originalR);
                            
                            // 修改：将延时从 300ms 增加到 1000ms (1秒)，给用户足够时间移动鼠标进入悬浮框
                            hideTimeout = setTimeout(function() {
                                tooltip.style.display = 'none';
                            }, 1000); 
                        });
                    } else {
                        // 非活跃省份
                        circle.style.cursor = 'default';
                    }
                });

                // 悬浮框交互：鼠标移入悬浮框时取消隐藏
                tooltip.addEventListener('mouseenter', function() {
                    clearTimeout(hideTimeout);
                });
                
                // 鼠标移出悬浮框时隐藏
                tooltip.addEventListener('mouseleave', function() {
                    // 修改：增加 500ms 缓冲时间，防止鼠标意外滑出导致立即消失
                    var _this = this;
                    hideTimeout = setTimeout(function() {
                        _this.style.display = 'none';
                    }, 500);
                });
            })
            .catch(err => console.error('[ChinaMap] 请求失败:', err));
    }

    var obj = document.getElementById('china-map-object');
    if (obj) {
        if (obj.contentDocument && obj.contentDocument.readyState === 'complete' && obj.contentDocument.querySelector('svg')) {
            initMap(obj.contentDocument);
        } else {
            obj.addEventListener('load', function () {
                initMap(obj.contentDocument);
            });
        }
    } else {
        var inlineSvg = document.querySelector('#china-map-wrapper svg');
        if (inlineSvg) {
            initMap(document); 
        }
    }
});



