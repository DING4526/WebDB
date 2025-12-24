document.addEventListener('DOMContentLoaded', function () {
    console.log('[ChinaMap] 极简折线交互版 - 已加载');

    var mapObj = document.getElementById('china-map-object');
    if (mapObj) {
        mapObj.style.height = '900px';
    }

    var baseUrl = window._EVENT_INDEX_URL || '/event/index';
    
    // 省份中英文映射
    var provinceMap = {
        "Shaanxi Province": "陕西省", "Shanghai Municipality": "上海市", "Chongqing Municipality": "重庆市",
        "Zhejiang Province": "浙江省", "Jiangxi Province": "江西省", "Yunnan Province": "云南省",
        "Shandong Province": "山东省", "Liaoning Province": "辽宁省", "Beijing Municipality": "北京市",
        "Tianjin Municipality": "天津市", "Hebei Province": "河北省", "Shanxi Province": "山西省",
        "Inner Mongolia Autonomous Region": "内蒙古自治区", "Jilin Province": "吉林省", "Heilongjiang Province": "黑龙江省",
        "Jiangsu Province": "江苏省", "Anhui Province": "安徽省", "Fujian Province": "福建省",
        "Henan Province": "河南省", "Hubei Province": "湖北省", "Hunan Province": "湖南省",
        "Guangdong Province": "广东省", "Guangxi Zhuang Autonomous Region": "广西壮族自治区", "Hainan Province": "海南省",
        "Sichuan Province": "四川省", "Guizhou Province": "贵州省", "Tibet Autonomous Region": "西藏自治区",
        "Gansu Province": "甘肃省", "Qinghai Province": "青海省", "Ningxia Hui Autonomous Region": "宁夏回族自治区",
        "Xinjiang Uygur Autonomous Region": "新疆维吾尔自治区", "Taiwan Province": "台湾省", "Hong Kong SAR": "香港特别行政区",
        "Macao SAR": "澳门特别行政区"
    };

    // 缓存中心点坐标 { "陕西省": {x: 567.3, y: 439.5}, ... }
    var provinceCenters = {};

    // 辅助函数：根据地图名称获取事件
    function getEventsForMapName(mapName, data) {
        for (var dbName in data) {
            if (dbName === mapName || mapName.indexOf(dbName) > -1 || dbName.indexOf(mapName) > -1) {
                return data[dbName];
            }
        }
        return null;
    }

    // --- 核心功能：绘制动态折线 ---
    function drawEventLines(svgDoc, centerX, centerY, events, mapEngName) {
        // 1. 清理旧的线条层
        var oldLayer = svgDoc.getElementById('interaction-layer');
        if (oldLayer) oldLayer.remove();

        // 2. 创建新的交互层
        var layer = document.createElementNS('http://www.w3.org/2000/svg', 'g');
        layer.id = 'interaction-layer';
        svgDoc.documentElement.appendChild(layer);

        // 3. 计算布局方向 - 修改：改为向地图外画
        // SVG viewBox 是 0~1000 (宽度)，中心点约为 500
        // 如果省份中心在左半边(x<500)，线往左画(direction=-1)；右半边则往右画(direction=1)
        var isRightSide = centerX > 500;
        var direction = isRightSide ? 1 : -1; // 修改：改为向外

        // 4. 遍历事件并绘制
        var baseSpacing = 35; // 每一条线垂直间隔
        var startY = centerY - ((events.length - 1) * baseSpacing) / 2; // 垂直居中分布

        events.forEach(function (ev, index) {
            var currentY = startY + (index * baseSpacing);
            
            // 拐点X坐标：向外延伸 60-80px
            var elbowX = centerX + (direction * (60 + index * 5));
            var elbowY = currentY;

            // 终点X坐标：继续向外延伸
            var endX = elbowX + (direction * 120);
            var endY = elbowY;

            // --- 绘制折线 (Path) ---
            var path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            var d = `M ${centerX} ${centerY} L ${elbowX} ${elbowY} L ${endX} ${endY}`;
            
            path.setAttribute('d', d);
            path.setAttribute('class', 'event-line-path');
            path.setAttribute('fill', 'none');
            path.setAttribute('stroke', '#d9534f');
            path.setAttribute('stroke-width', '1.5');
            
            // 计算路径总长度用于动画
            layer.appendChild(path);
            var pathLength = path.getTotalLength();
            path.style.strokeDasharray = pathLength;
            path.style.strokeDashoffset = pathLength;
            
            // 依次延迟启动动画
            setTimeout(function() {
                path.style.transition = 'stroke-dashoffset 0.8s ease-out';
                path.style.strokeDashoffset = '0';
            }, index * 100);

            // --- 绘制文字 (Text) ---
            var text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
            
            var textX = elbowX + (direction * 60);
            var textY = elbowY - 8;
            
            text.setAttribute('x', textX);
            text.setAttribute('y', textY); 
            text.setAttribute('class', 'event-label-text');
            text.setAttribute('text-anchor', 'middle');
            text.setAttribute('font-family', '"Microsoft YaHei", "SimHei", sans-serif'); // 优先雅黑
            text.setAttribute('font-weight', '900'); // 最粗
            text.setAttribute('font-size', '17'); // 17px
            text.setAttribute('fill', '#000000'); // 纯黑
            text.setAttribute('stroke', '#ffffff'); // 白色描边
            text.setAttribute('stroke-width', '3'); // 3px描边
            text.setAttribute('paint-order', 'stroke fill'); // 描边在下
            
            var displayTitle = ev.title.length > 15 ? ev.title.substring(0, 15) + '...' : ev.title;
            text.textContent = displayTitle;

            // 点击跳转逻辑
            text.style.cursor = 'pointer';
            text.addEventListener('click', function(e) {
                e.stopPropagation();
                var url = baseUrl;
                if (url.indexOf('event%2Findex') > -1) {
                    url = url.replace('event%2Findex', 'timeline%2Fview') + '&id=' + ev.id;
                } else if (url.indexOf('event/index') > -1) {
                    url = url.replace('event/index', 'timeline/view');
                    url += (url.indexOf('?') > -1 ? '&' : '?') + 'id=' + ev.id;
                } else {
                    url += (url.indexOf('?') > -1 ? '&' : '?') + 'id=' + ev.id;
                }
                window.open(url, '_blank');
            });

            // 文字淡入动画
            text.style.opacity = '0';
            text.style.transition = 'opacity 0.4s ease-out';
            setTimeout(function() {
                text.style.opacity = '1';
            }, 800 + index * 100);

            layer.appendChild(text);
        });
    }

    // --- 初始化地图 ---
    function initMap(svgDoc) {
        // 1. 预处理：解析中心点坐标
        var labelPoints = svgDoc.getElementById('label_points');
        if (labelPoints) {
            var circles = labelPoints.querySelectorAll('circle');
            circles.forEach(function(c) {
                var pName = c.getAttribute('class') || c.getAttribute('id');
                var cx = parseFloat(c.getAttribute('cx'));
                var cy = parseFloat(c.getAttribute('cy'));
                
                if (pName && !isNaN(cx)) {
                    pName = pName.trim();
                    provinceCenters[pName] = { x: cx, y: cy };
                    
                    if (provinceMap[pName]) {
                        provinceCenters[provinceMap[pName]] = { x: cx, y: cy };
                    }
                }
            });
            labelPoints.style.display = 'none';
        }
        
        var pointsGroup = svgDoc.getElementById('points');
        if (pointsGroup) pointsGroup.style.display = 'none';

        // 2. 获取省份区块并绑定交互
        var paths = svgDoc.querySelectorAll('#features path');
        
        // --- 修复：悬浮高亮对所有省份生效 ---
        paths.forEach(function(path) {
            // 保存原始颜色
            var originalFill = path.getAttribute('fill') || '';
            path.style.transition = 'fill 0.3s ease, opacity 0.3s ease';
            path.style.cursor = 'default'; // 默认指针

            // 悬浮高亮（所有省份生效）
            path.addEventListener('mouseenter', function () {
                this.style.fill = '#d9534f';
                this.style.opacity = '0.9';
            });

            path.addEventListener('mouseleave', function () {
                this.style.fill = originalFill;
                this.style.opacity = '1';
            });
        });
        
        // 全局点击清除线条
        svgDoc.addEventListener('click', function(e) {
            if (e.target.tagName !== 'path' && e.target.tagName !== 'text') {
                var oldLayer = svgDoc.getElementById('interaction-layer');
                if (oldLayer) oldLayer.remove();
            }
        });

        // 3. 请求数据
        var sep = baseUrl.indexOf('?') === -1 ? '?' : '&';
        var fetchUrl = baseUrl + sep + 'action=get-active-locations';

        fetch(fetchUrl)
            .then(response => response.json())
            .then(activeData => {
                if (!activeData) activeData = {};
                console.log('[ChinaMap] 数据就绪', activeData);

                paths.forEach(function (path) {
                    var mapEngName = path.getAttribute('name');
                    if (!mapEngName) return;
                    var mapChineseName = provinceMap[mapEngName];
                    if (!mapChineseName) return;

                    var events = getEventsForMapName(mapChineseName, activeData);

                    // 只有有事件的省份才能点击
                    if (events && events.length > 0) {
                        path.style.cursor = 'pointer'; // 改为手型

                        // 点击触发折线绘制
                        path.addEventListener('click', function (e) {
                            e.stopPropagation();

                            var center = provinceCenters[mapEngName] || provinceCenters[mapChineseName];
                            
                            if (!center) {
                                var bbox = this.getBBox();
                                center = {
                                    x: bbox.x + bbox.width / 2,
                                    y: bbox.y + bbox.height / 2
                                };
                            }

                            console.log('点击:', mapChineseName, '中心:', center, '事件数:', events.length);

                            drawEventLines(svgDoc, center.x, center.y, events, mapEngName);
                        });
                    }
                });
            })
            .catch(err => console.error(err));
    }

    // 载入逻辑保持不变
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
        if (inlineSvg) initMap(document);
    }
});