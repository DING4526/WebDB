document.addEventListener('DOMContentLoaded', function () {
    console.log('[ChinaMap] 脚本已加载 - 区块交互版');

    // ---------------------------------------------------------
    // 修复：强制调整地图容器高度
    // ---------------------------------------------------------
    var mapObj = document.getElementById('china-map-object');
    if (mapObj) {
        mapObj.style.height = '900px'; 
    }
    // ---------------------------------------------------------

    var baseUrl = window._EVENT_INDEX_URL || '/event/index';
    
    // 创建悬浮框 (Tooltip) - 初始隐藏
    var tooltip = document.createElement('div');
    tooltip.id = 'map-tooltip';
    // 增加关闭按钮样式，调整 z-index
    tooltip.style.cssText = 'position:absolute; display:none; background:rgba(255,255,255,0.98); border:1px solid #ccc; padding:15px; border-radius:4px; box-shadow:0 4px 20px rgba(0,0,0,0.3); z-index:10000; min-width:220px; pointer-events:auto; font-size:14px; line-height:1.6; color:#333; text-align:left;';
    document.body.appendChild(tooltip);

    // 点击页面其他地方关闭弹窗
    document.addEventListener('click', function(e) {
        // 如果点击的不是地图对象，也不是弹窗本身，则关闭弹窗
        if (e.target !== mapObj && !tooltip.contains(e.target)) {
            tooltip.style.display = 'none';
        }
    });

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
        for (var dbName in data) {
            if (dbName === mapName || mapName.indexOf(dbName) > -1 || dbName.indexOf(mapName) > -1) {
                return data[dbName];
            }
        }
        return null;
    }

    function initMap(svgDoc) {
        // 1. 隐藏原有的圆点 (根据需求，不再展示圆点)
        var labelPoints = svgDoc.getElementById('label_points');
        if (labelPoints) labelPoints.style.display = 'none';
        
        var points = svgDoc.getElementById('points');
        if (points) points.style.display = 'none';

        // 2. 获取所有省份区块 (Path)
        // 根据你提供的 SVG 结构，省份都在 #features 下
        var paths = svgDoc.querySelectorAll('#features path');

        if (!paths.length) {
            console.warn('[ChinaMap] 警告: 未在 #features 下找到 path 元素。');
            return;
        }

        // 3. 初始化区块样式 & 绑定基础悬浮效果
        paths.forEach(function(path) {
            // 设置基础样式，确保可以响应鼠标
            path.style.cursor = 'default'; // 默认普通指针
            path.style.transition = 'fill 0.3s ease, opacity 0.3s ease';
            
            // 保存原始颜色 (SVG 中定义的 fill)
            // 如果 SVG 标签上有 fill 属性，path 可能没有内联 fill，这里取 computed style 或置空
            var originalFill = path.getAttribute('fill') || ''; 

            // --- 悬浮高亮逻辑 (所有区块生效) ---
            path.addEventListener('mouseenter', function () {
                // 高亮颜色：比原色 #9c6f6fff 更亮或更红
                this.style.fill = '#d9534f'; 
                this.style.opacity = '0.9';
            });

            path.addEventListener('mouseleave', function () {
                this.style.fill = originalFill; // 恢复原色
                this.style.opacity = '1';
            });
        });

        // 4. 请求后端数据
        var sep = baseUrl.indexOf('?') === -1 ? '?' : '&';
        var fetchUrl = baseUrl + sep + 'action=get-active-locations';
        
        fetch(fetchUrl)
            .then(response => response.json())
            .then(activeData => {
                console.log('[ChinaMap] 事件数据:', activeData);

                if (!activeData) activeData = {};

                paths.forEach(function (path) {
                    // 获取省份名称 (SVG 中的 name 属性)
                    var mapEngName = path.getAttribute('name');
                    if (!mapEngName) return;
                    
                    var mapChineseName = provinceMap[mapEngName];
                    if (!mapChineseName) return;

                    // 获取该省份的事件列表
                    var events = getEventsForMapName(mapChineseName, activeData);
                    var hasEvents = events && events.length > 0;

                    if (hasEvents) {
                        // 如果有事件，鼠标变手型
                        path.style.cursor = 'pointer';

                        // --- 点击逻辑 (仅有事件时触发) ---
                        path.addEventListener('click', function (e) {
                            e.stopPropagation(); // 阻止冒泡，防止触发 document 的关闭逻辑

                            // 1. 关闭已打开的弹窗 (可选，如果希望同时只显示一个)
                            // tooltip.style.display = 'none';

                            // 2. 构建弹窗内容
                            var html = '<div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #eee; padding-bottom:5px; margin-bottom:8px;">';
                            html += '<span style="font-weight:bold; font-size:16px;">' + mapChineseName + '</span>';
                            html += '<span style="cursor:pointer; color:#999; font-size:18px;" onclick="document.getElementById(\'map-tooltip\').style.display=\'none\'">×</span>';
                            html += '</div>';
                            
                            html += '<ul style="margin:0; padding-left:20px; max-height:250px; overflow-y:auto;">';
                            events.forEach(function(ev) {
                                var url = baseUrl;
                                if (url.indexOf('event%2Findex') > -1) {
                                    url = url.replace('event%2Findex', 'timeline%2Fview') + '&id=' + ev.id;
                                } else if (url.indexOf('event/index') > -1) {
                                    url = url.replace('event/index', 'timeline/view');
                                    url += (url.indexOf('?') > -1 ? '&' : '?') + 'id=' + ev.id;
                                } else {
                                    url += (url.indexOf('?') > -1 ? '&' : '?') + 'id=' + ev.id;
                                }
                                html += '<li style="margin-bottom:5px;"><a href="' + url + '" target="_blank" style="text-decoration:none; color:#0056b3; display:block;">' + ev.title + '</a></li>';
                            });
                            html += '</ul>';

                            tooltip.innerHTML = html;
                            tooltip.style.display = 'block';

                            // 3. 定位弹窗 (跟随地区位置)
                            // 获取当前点击的 path 元素的边界矩形 (相对于 SVG/Object 视口)
                            var pathRect = this.getBoundingClientRect();
                            // 获取地图容器的边界矩形 (相对于浏览器视口)
                            var mapRect = mapObj.getBoundingClientRect();
                            
                            var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                            var scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;

                            // 计算中心点坐标 (相对于文档)
                            // pathRect.left/top 是相对于 object 左上角的
                            var centerX = mapRect.left + scrollLeft + pathRect.left + (pathRect.width / 2);
                            var centerY = mapRect.top + scrollTop + pathRect.top + (pathRect.height / 2);

                            // 默认显示在中心点右侧一点
                            var left = centerX + 20;
                            var top = centerY - 50; // 稍微向上提一点，避免遮挡中心

                            // 边界检查
                            if (left + 240 > document.documentElement.clientWidth) {
                                // 如果右侧溢出，显示在左侧
                                left = centerX - 260;
                            }

                            tooltip.style.left = left + 'px';
                            tooltip.style.top = top + 'px';
                        });
                    }
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



