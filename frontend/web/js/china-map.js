document.addEventListener('DOMContentLoaded', function () {
    console.log('[ChinaMap] 脚本已加载 - 修复版 v3');

    // ---------------------------------------------------------
    // 修复：强制调整地图容器高度，解决底部显示不全的问题
    // ---------------------------------------------------------
    var mapObj = document.getElementById('china-map-object');
    if (mapObj) {
        // 900px 通常足以显示包含南海诸岛的完整中国地图
        // 如果仍然显示不全，可以尝试增加到 1000px
        mapObj.style.height = '900px'; 
    }
    // ---------------------------------------------------------

    var baseUrl = window._EVENT_INDEX_URL || '/event/index';
    
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

    function isLocationActive(mapName, activeList) {
        if (!mapName || !activeList) return false;
        return activeList.some(function(dbName) {
            if (dbName === mapName) return true;
            if (mapName.indexOf(dbName) > -1) return true;
            if (dbName.indexOf(mapName) > -1) return true;
            return false;
        });
    }

    function getDbName(mapName, activeList) {
        if (!mapName || !activeList) return null;
        var match = activeList.find(function(dbName) {
            return dbName === mapName || mapName.indexOf(dbName) > -1 || dbName.indexOf(mapName) > -1;
        });
        return match || mapName;
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

        // 2. 【关键修复】强制设置半径和颜色，防止圆点不可见
        circles.forEach(function(circle) {
            // 如果没有半径，设置默认半径
            if (!circle.getAttribute('r') || circle.getAttribute('r') == '0') {
                circle.setAttribute('r', 6);
            }
            // 如果没有填充色，设置默认颜色 (深灰色)
            if (!circle.style.fill && !circle.getAttribute('fill')) {
                circle.style.fill = '#555';
            }
            // 确保透明度正常
            circle.style.opacity = '1';
            circle.style.transition = 'all 0.3s ease';
        });

        // 3. 请求后端数据
        var sep = baseUrl.indexOf('?') === -1 ? '?' : '&';
        var fetchUrl = baseUrl + sep + 'action=get-active-locations';
        
        fetch(fetchUrl)
            .then(response => response.json())
            .then(activeLocations => {
                console.log('[ChinaMap] 活跃省份:', activeLocations);

                if (!Array.isArray(activeLocations)) {
                    activeLocations = [];
                }

                var activatedCount = 0;

                circles.forEach(function (circle) {
                    var className = circle.getAttribute('class');
                    if (!className) return;
                    
                    className = className.trim();
                    var mapChineseName = provinceMap[className];

                    if (mapChineseName && isLocationActive(mapChineseName, activeLocations)) {
                        activatedCount++;
                        var targetDbName = getDbName(mapChineseName, activeLocations);

                        // 激活样式
                        circle.style.cursor = 'pointer';
                        circle.style.fill = '#d9534f'; // 默认给活跃点一个醒目的颜色(红色)
                        
                        // 点击事件
                        circle.addEventListener('click', function (e) {
                            e.preventDefault();
                            var target = baseUrl + (baseUrl.indexOf('?') === -1 ? '?' : '&') + 'location=' + encodeURIComponent(targetDbName);
                            window.location.href = target;
                        });

                        // 悬停事件
                        var originalR = circle.getAttribute('r');
                        circle.addEventListener('mouseenter', function () {
                            this.style.fill = '#ff0000'; // 悬停变亮红
                            this.setAttribute('r', parseFloat(originalR) + 4); // 变大
                            
                            var title = this.querySelector('title');
                            if (!title) {
                                title = document.createElementNS('http://www.w3.org/2000/svg', 'title');
                                this.appendChild(title);
                            }
                            title.textContent = mapChineseName + " (点击查看)";
                        });
                        
                        circle.addEventListener('mouseleave', function () {
                            this.style.fill = '#d9534f'; // 恢复活跃色
                            this.setAttribute('r', originalR);
                        });
                    } else {
                        // 非活跃省份：保持默认样式
                        circle.style.cursor = 'default';
                    }
                });
                console.log('[ChinaMap] 激活节点数:', activatedCount);
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



