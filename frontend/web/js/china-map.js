document.addEventListener('DOMContentLoaded', function () {
    console.log('[ChinaMap] 极简交互版 - 已加载');

    // var mapObj = document.getElementById('china-map-object');
    // if (mapObj) {
    //     mapObj.style.height = '900px';
    // }

    var baseUrl = window._EVENT_INDEX_URL || '/event/index';

    
    // 获取图片基础路径
    var imageBasePath = (function() {
        var scripts = document.getElementsByTagName('script');
        for (var i = 0; i < scripts.length; i++) {
            var src = scripts[i].src;
            if (src && src.indexOf('/js/china-map.js') > -1) {
                return src.substring(0, src.indexOf('/js/china-map.js'));
            }
        }
        return '';
    })();

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

    // 省份ID到中文名称的映射
    var provinceIdMap = {
        'CNSN': '陕西省', 'CNSH': '上海市', 'CNCQ': '重庆市', 'CNZJ': '浙江省',
        'CNJX': '江西省', 'CNSC': '四川省', 'CNHB': '湖北省', 'CNHN': '湖南省',
        'CNGD': '广东省', 'CNFJ': '福建省', 'CNAH': '安徽省', 'CNJS': '江苏省',
        'CNSD': '山东省', 'CNHE': '河北省', 'CNHA': '河南省', 'CNSX': '山西省',
        'CNLN': '辽宁省', 'CNJL': '吉林省', 'CNHL': '黑龙江省', 'CNGS': '甘肃省',
        'CNQH': '青海省', 'CNYN': '云南省', 'CNGZ': '贵州省', 'CNGX': '广西壮族自治区',
        'CNXJ': '新疆维吾尔自治区', 'CNXZ': '西藏自治区', 'CNBJ': '北京市',
        'CNTJ': '天津市', 'CNNM': '内蒙古自治区', 'CNHI': '海南省', 'CNNX': '宁夏回族自治区',
        'CNTW': '台湾省', 'CNHK': '香港特别行政区', 'CNMO': '澳门特别行政区'
    };

    var provinceCenters = {};
    var provinceLabel = null;

    function getEventsForMapName(mapName, data) {
        for (var dbName in data) {
            if (dbName === mapName || mapName.indexOf(dbName) > -1 || dbName.indexOf(mapName) > -1) {
                return data[dbName];
            }
        }
        return null;
    }

    // --- 绘制三层同心圆点 (带交互效果) ---
    function drawCircleMarker(svgDoc, x, y, onClickCallback) {
        var g = document.createElementNS('http://www.w3.org/2000/svg', 'g');
        g.setAttribute('transform', `translate(${x}, ${y})`);
        g.style.cursor = 'pointer'; // 允许点击，鼠标变手型
        
        // 定义过渡动画样式
        var transitionStyle = 'r 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275), fill 0.3s ease, opacity 0.3s ease';

        // 最外层圆：半径最大，透明度最高
        var outerCircle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
        outerCircle.setAttribute('cx', '0');
        outerCircle.setAttribute('cy', '0');
        outerCircle.setAttribute('r', '12'); // 基础半径的4倍
        outerCircle.setAttribute('fill', '#FFD700');
        outerCircle.setAttribute('opacity', '0.25'); // 最透明但不是百分百
        outerCircle.style.filter = 'blur(1px)';
        outerCircle.style.transition = transitionStyle;
        
        // 中间层圆：半径中等，中等透明度
        var middleCircle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
        middleCircle.setAttribute('cx', '0');
        middleCircle.setAttribute('cy', '0');
        middleCircle.setAttribute('r', '6'); // 基础半径的2倍
        middleCircle.setAttribute('fill', '#FFD700');
        middleCircle.setAttribute('opacity', '0.5'); // 中等透明度
        middleCircle.style.transition = transitionStyle;
        
        // 最内层圆：半径最小，透明度最低（即最不透明）
        var innerCircle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
        innerCircle.setAttribute('cx', '0');
        innerCircle.setAttribute('cy', '0');
        innerCircle.setAttribute('r', '3'); // 基础半径
        innerCircle.setAttribute('fill', '#FFD700');
        innerCircle.setAttribute('opacity', '1'); // 完全不透明
        innerCircle.style.filter = 'drop-shadow(0 0 3px rgba(255, 215, 0, 0.8))';
        innerCircle.style.transition = transitionStyle;
        
        // 添加圆点（从外到内）
        g.appendChild(outerCircle);
        g.appendChild(middleCircle);
        g.appendChild(innerCircle);
        
        // --- 交互事件监听 ---
        g.addEventListener('mouseenter', function() {
            // 悬浮：变大，中心变红
            innerCircle.setAttribute('r', '6');
            innerCircle.setAttribute('fill', '#FF3333'); 
            middleCircle.setAttribute('r', '10');
            middleCircle.setAttribute('opacity', '0.7');
            outerCircle.setAttribute('r', '16');
            outerCircle.setAttribute('opacity', '0.4');
        });

        g.addEventListener('mouseleave', function() {
            // 恢复原状
            innerCircle.setAttribute('r', '3');
            innerCircle.setAttribute('fill', '#FFD700');
            middleCircle.setAttribute('r', '6');
            middleCircle.setAttribute('opacity', '0.5');
            outerCircle.setAttribute('r', '12');
            outerCircle.setAttribute('opacity', '0.25');
        });

        g.addEventListener('click', function(e) {
            e.stopPropagation();
            // 点击反馈动画 (轻微收缩再恢复)
            innerCircle.setAttribute('r', '4'); 
            setTimeout(() => {
                innerCircle.setAttribute('r', '6'); // 回到悬浮状态
            }, 100);

            if (onClickCallback) onClickCallback(e);
        });

        svgDoc.documentElement.appendChild(g);
    }

    // --- 新增：绘制事件连接线和矩形 ---
    function clearConnectors(svgDoc) {
        var layer = svgDoc.getElementById('connector-layer');
        if (layer) layer.remove();
    }

    function drawEventConnectors(svgDoc, centerX, centerY, events) {
        clearConnectors(svgDoc);
        
        var gLayer = svgDoc.createElementNS('http://www.w3.org/2000/svg', 'g');
        gLayer.setAttribute('id', 'connector-layer');
        
        // 1. 获取SVG视口边界
        var svgRoot = svgDoc.documentElement;
        var svgBounds = {
            minX: 0,
            minY: 0,
            maxX: 1000,
            maxY: 800
        };
        
        // 从 viewBox 获取实际边界
        if (svgRoot.viewBox && svgRoot.viewBox.baseVal && svgRoot.viewBox.baseVal.width > 0) {
            var vb = svgRoot.viewBox.baseVal;
            svgBounds.minX = vb.x;
            svgBounds.minY = vb.y;
            svgBounds.maxX = vb.x + vb.width;
            svgBounds.maxY = vb.y + vb.height;
        } else {
            // 从 width/height 属性获取
            var w = parseFloat(svgRoot.getAttribute('width'));
            var h = parseFloat(svgRoot.getAttribute('height'));
            if (!isNaN(w) && !isNaN(h)) {
                svgBounds.maxX = w;
                svgBounds.maxY = h;
            }
        }
        
        // 添加安全边距
        var padding = 25;
        svgBounds.minX += padding;
        svgBounds.minY += padding;
        svgBounds.maxX -= padding;
        svgBounds.maxY -= padding;
        
        // 计算地图中心
        var mapCX = (svgBounds.minX + svgBounds.maxX) / 2;
        var mapCY = (svgBounds.minY + svgBounds.maxY) / 2;

        // 计算从地图中心指向省份中心的向量角度
        var vecX = centerX - mapCX;
        var vecY = centerY - mapCY;
        var baseAngle = Math.atan2(vecY, vecX);

        // 2. 配置参数
        var baseRadius = 120;
        var fanAngle = Math.PI / 2.5; 
        if (events.length > 5) fanAngle = Math.PI / 1.8;
        if (events.length > 10) fanAngle = Math.PI / 1.2;
        
        var placedRects = [];

        events.forEach(function(ev, index) {
            var angle;
            if (events.length === 1) {
                angle = baseAngle;
            } else {
                var step = fanAngle / (events.length - 1);
                var start = baseAngle - (fanAngle / 2);
                angle = start + step * index;
            }
            
            var fontSize = 16;
            var textStr = ev.title || '未命名事件';
            var textLen = 0;
            for(var i=0; i<textStr.length; i++) {
                textLen += (textStr.charCodeAt(i) > 255 ? 1 : 0.6);
            }
            var rectWidth = textLen * fontSize + 20; 
            var rectHeight = 30;

            var currentRadius = baseRadius;
            var endX, endY, rectX, rectY;
            var isRightSide = Math.cos(angle) >= 0;
            var maxAttempts = 8;
            
            for (var attempt = 0; attempt < maxAttempts; attempt++) {
                endX = centerX + currentRadius * Math.cos(angle);
                endY = centerY + currentRadius * Math.sin(angle);
                
                rectX = isRightSide ? endX : (endX - rectWidth);
                rectY = endY - rectHeight;

                // **边界检测和修正**
                var needsAdjustment = false;
                
                // 水平边界检测
                if (rectX < svgBounds.minX) {
                    rectX = svgBounds.minX;
                    needsAdjustment = true;
                } else if (rectX + rectWidth > svgBounds.maxX) {
                    rectX = svgBounds.maxX - rectWidth;
                    needsAdjustment = true;
                }
                
                // 垂直边界检测
                if (rectY < svgBounds.minY) {
                    rectY = svgBounds.minY;
                    needsAdjustment = true;
                } else if (rectY + rectHeight > svgBounds.maxY) {
                    rectY = svgBounds.maxY - rectHeight;
                    needsAdjustment = true;
                }

                // 碰撞检测
                var collision = false;
                var collisionPadding = 4;
                
                for (var k = 0; k < placedRects.length; k++) {
                    var p = placedRects[k];
                    if (rectX < p.x + p.w + collisionPadding &&
                        rectX + rectWidth + collisionPadding > p.x &&
                        rectY < p.y + p.h + collisionPadding &&
                        rectY + rectHeight + collisionPadding > p.y) {
                        collision = true;
                        break;
                    }
                }

                if (!collision) {
                    break;
                } else {
                    currentRadius += (rectHeight + 10);
                }
            }
            
            // 如果修正后的位置仍然有问题，尝试智能调整文字方向
            if (rectX + rectWidth > svgBounds.maxX || rectX < svgBounds.minX) {
                isRightSide = !isRightSide;
                rectX = isRightSide ? endX : (endX - rectWidth);
                // 再次边界修正
                if (rectX < svgBounds.minX) rectX = svgBounds.minX;
                if (rectX + rectWidth > svgBounds.maxX) rectX = svgBounds.maxX - rectWidth;
            }
            
            placedRects.push({x: rectX, y: rectY, w: rectWidth, h: rectHeight});
            
            // 创建单个事件组
            var itemG = svgDoc.createElementNS('http://www.w3.org/2000/svg', 'g');
            itemG.style.cursor = 'pointer';
            itemG.style.transition = 'opacity 0.3s';
            
            // 点击事件：打开详情弹窗
            itemG.onclick = function(e) {
                e.stopPropagation(); // 阻止冒泡，防止触发地图点击清除
                showEventModal(ev);
            };
            
            // 1. 金黄色直线
            var line = svgDoc.createElementNS('http://www.w3.org/2000/svg', 'line');
            line.setAttribute('x1', centerX);
            line.setAttribute('y1', centerY);
            line.setAttribute('x2', endX);
            line.setAttribute('y2', endY);
            line.setAttribute('stroke', '#FFD700');
            line.setAttribute('stroke-width', '2');
            line.setAttribute('stroke-linecap', 'round');
            
            // 2. 连接处的圆形
            var circle = svgDoc.createElementNS('http://www.w3.org/2000/svg', 'circle');
            circle.setAttribute('cx', endX);
            circle.setAttribute('cy', endY);
            circle.setAttribute('r', '4');
            circle.setAttribute('fill', '#FFD700');
            
            // 3. 矩形和文字 - 使用修正后的位置
            var textX = rectX + 10; // 统一使用左对齐，避免文字方向问题
            var textY = rectY + (rectHeight / 2) + (fontSize / 3); // 垂直居中
            
            var rect = svgDoc.createElementNS('http://www.w3.org/2000/svg', 'rect');
            rect.setAttribute('x', rectX);
            rect.setAttribute('y', rectY); 
            rect.setAttribute('width', rectWidth);
            rect.setAttribute('height', rectHeight);
            rect.setAttribute('fill', '#FFD700');
            rect.setAttribute('rx', '4');
            rect.style.filter = 'drop-shadow(2px 2px 3px rgba(0,0,0,0.3))';
            
            var text = svgDoc.createElementNS('http://www.w3.org/2000/svg', 'text');
            text.setAttribute('x', textX);
            text.setAttribute('y', textY); 
            text.setAttribute('fill', '#FF0000');
            text.setAttribute('font-size', fontSize + 'px');
            text.setAttribute('font-weight', 'bold');
            text.setAttribute('font-family', '"Microsoft YaHei", sans-serif');
            text.style.pointerEvents = 'none';
            text.textContent = textStr;
            
            // 组装
            itemG.appendChild(line);
            itemG.appendChild(rect);
            itemG.appendChild(circle);
            itemG.appendChild(text);
            
            // 悬停效果
            itemG.addEventListener('mouseenter', function() {
                rect.setAttribute('fill', '#FFF'); // 变白高亮
                line.setAttribute('stroke', '#FFF');
                circle.setAttribute('fill', '#FFF');
                // 悬停时将元素移到最上层
                gLayer.appendChild(itemG);
            });
            itemG.addEventListener('mouseleave', function() {
                rect.setAttribute('fill', '#FFD700'); // 恢复金黄
                line.setAttribute('stroke', '#FFD700');
                circle.setAttribute('fill', '#FFD700');
            });

            gLayer.appendChild(itemG);
        });
        
        // 添加入场动画
        gLayer.style.opacity = '0';
        svgDoc.documentElement.appendChild(gLayer);
        // 强制重绘
        requestAnimationFrame(function() {
            gLayer.style.transition = 'opacity 0.3s ease';
            gLayer.style.opacity = '1';
        });
    }

    // --- 省份标签逻辑 ---
    function createProvinceLabel(svgDoc) {
        provinceLabel = svgDoc.getElementById('province-label');
        if (!provinceLabel) {
            provinceLabel = svgDoc.createElementNS('http://www.w3.org/2000/svg', 'text');
            provinceLabel.setAttribute('id', 'province-label');
            provinceLabel.style.fontSize = '24px';
            provinceLabel.style.fontFamily = '"SimHei", "Microsoft YaHei", sans-serif';
            provinceLabel.style.fontWeight = 'bold';
            provinceLabel.style.fill = '#FFD700';
            provinceLabel.style.stroke = '#000000';
            provinceLabel.style.strokeWidth = '1.5px';
            provinceLabel.style.paintOrder = 'stroke fill';
            provinceLabel.style.pointerEvents = 'none';
            provinceLabel.style.opacity = '0';
            provinceLabel.style.transition = 'opacity 0.3s ease';
            provinceLabel.style.textAnchor = 'middle';
            svgDoc.documentElement.appendChild(provinceLabel);
        }
        return provinceLabel;
    }

    function getProvinceLabelPosition(svgDoc, provinceId) {
        var labelPoint = svgDoc.querySelector('#label_points circle[id="' + provinceId + '"]');
        if (labelPoint) {
            return {
                x: parseFloat(labelPoint.getAttribute('cx')),
                y: parseFloat(labelPoint.getAttribute('cy'))
            };
        }
        return null;
    }

    function showProvinceLabel(svgDoc, provinceId) {
        var provinceName = provinceIdMap[provinceId];
        if (!provinceName) return;
        var position = getProvinceLabelPosition(svgDoc, provinceId);
        if (!position) return;
        var label = createProvinceLabel(svgDoc);
        label.textContent = provinceName;
        label.setAttribute('x', position.x);
        label.setAttribute('y', position.y - 10);
        label.style.opacity = '1';
    }

    function hideProvinceLabel() {
        if (provinceLabel) provinceLabel.style.opacity = '0';
    }

    // --- 修改：显示单个事件详情弹窗 (移除 Swiper) ---
    function showEventModal(event) {
        var modal = document.getElementById('event-detail-modal');
        
        // 1. 处理图片路径
        var imageUrl = '';
        if (event.image_path) {
            if (event.image_path.indexOf('http') === 0) {
                imageUrl = event.image_path;
            } else {
                var base = window._BASE_WEB_URL || '';
                if (base.slice(-1) === '/') base = base.slice(0, -1);
                var path = event.image_path.replace(/\\/g, '/');
                if (path.indexOf('/') !== 0) {
                    path = (path.indexOf('uploads') === -1) ? '/uploads/' + path : '/' + path;
                }
                imageUrl = base + path;
            }
        } else {
            // 纯色占位图
            imageUrl = 'data:image/svg+xml;base64,' + btoa(`
                <svg xmlns="http://www.w3.org/2000/svg" width="300" height="210" viewBox="0 0 300 210">
                    <rect width="300" height="210" fill="#4a5568"/>
                    <text x="50%" y="45%" fill="#e2e8f0" font-size="16" font-family="Arial" text-anchor="middle" dominant-baseline="middle">暂无图片</text>
                    <text x="50%" y="60%" fill="#cbd5e0" font-size="12" font-family="Arial" text-anchor="middle" dominant-baseline="middle">${event.title || '未知事件'}</text>
                </svg>
            `);
        }

        // 2. 更新 DOM
        var imgElem = document.getElementById('modal-image');
        if (imgElem) imgElem.src = imageUrl;
        
        var imgTitleElem = document.getElementById('modal-image-title');
        if (imgTitleElem) imgTitleElem.textContent = event.title || '';

        updateModalInfo(event);
        modal.classList.add('show');
    }

    function updateModalInfo(event) {
        var titleElem = document.getElementById('modal-title');
        titleElem.textContent = event.title || '未知事件';
        titleElem.onclick = function() {
            var url = window._EVENT_INDEX_URL || '/event/index';
            if (url.indexOf('event%2Findex') > -1) {
                url = url.replace('event%2Findex', 'timeline%2Fview') + '&id=' + event.id;
            } else if (url.indexOf('event/index') > -1) {
                url = url.replace('event/index', 'timeline/view');
                url += (url.indexOf('?') > -1 ? '&' : '?') + 'id=' + event.id;
            } else {
                url += (url.indexOf('?') > -1 ? '&' : '?') + 'id=' + event.id;
            }
            window.open(url, '_blank');
        };

        var dateStr = event.event_date || '未知';
        if (dateStr && dateStr.length > 10) dateStr = dateStr.substring(0, 10);
        document.getElementById('modal-date').textContent = dateStr;
        document.getElementById('modal-location').textContent = event.location || '未知';
        document.getElementById('modal-summary').textContent = event.summary || '暂无摘要';
    }

    // --- 初始化地图 ---
    function initMap(svgDoc) {
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

        addMapStyling(svgDoc);

        var paths = svgDoc.querySelectorAll('#features path');
        
        paths.forEach(function(path) {
            var originalFill = path.getAttribute('fill') || '';
            var provinceId = path.getAttribute('id');
            
            path.style.transition = 'fill 0.3s ease, stroke 0.3s ease, opacity 0.3s ease, transform 0.3s ease, filter 0.3s ease';
            
            path.addEventListener('mouseenter', function () {
                this.style.fill = '#d9534f';
                this.style.stroke = '#FFA500';
                this.style.strokeWidth = '2';
                this.style.opacity = '0.9';
                this.style.transform = 'translateY(-3px)';
                this.style.filter = 'drop-shadow(0 5px 10px rgba(0,0,0,0.5))';
                if (provinceId) showProvinceLabel(svgDoc, provinceId);
            });

            path.addEventListener('mouseleave', function () {
                this.style.fill = originalFill;
                this.style.stroke = '#FFD700';
                this.style.strokeWidth = '1.5';
                this.style.opacity = '1';
                this.style.transform = 'translateY(0)';
                this.style.filter = 'none';
                hideProvinceLabel();
            });
        });
        
        // 全局点击监听：点击空白处清除连接线
        svgDoc.addEventListener('click', function(e) {
            var target = e.target;
            // 检查点击是否在连接线层内部
            var isConnector = false;
            var parent = target;
            while(parent && parent !== svgDoc) {
                if (parent.id === 'connector-layer' || (parent.getAttribute && parent.getAttribute('id') === 'connector-layer')) {
                    isConnector = true;
                    break;
                }
                parent = parent.parentNode;
            }
            
            // 如果点击的不是省份块，也不是连接线上的元素，则清除连接线
            if (target.tagName !== 'path' && !isConnector) {
                clearConnectors(svgDoc);
            }
        });

        var sep = baseUrl.indexOf('?') === -1 ? '?' : '&';
        var fetchUrl = baseUrl + sep + 'action=get-active-locations';

        fetch(fetchUrl)
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(activeData => {
                if (!activeData) activeData = {};
                console.log('[ChinaMap] 数据就绪', activeData);

                paths.forEach(function (path) {
                    var mapEngName = path.getAttribute('name');
                    if (!mapEngName) return;
                    var mapChineseName = provinceMap[mapEngName];
                    if (!mapChineseName) return;

                    var events = getEventsForMapName(mapChineseName, activeData);

                    if (events && events.length > 0) {
                        path.style.cursor = 'pointer';

                        var center = provinceCenters[mapEngName] || provinceCenters[mapChineseName];
                        if (!center) {
                            var bbox = path.getBBox();
                            center = { x: bbox.x + bbox.width / 2, y: bbox.y + bbox.height / 2 };
                            provinceCenters[mapChineseName] = center; 
                        }

                        // 传递点击回调，使圆点点击也能触发连接线
                        drawCircleMarker(svgDoc, center.x, center.y, function() {
                            console.log('点击圆点:', mapChineseName);
                            drawEventConnectors(svgDoc, center.x, center.y, events);
                        });

                        // 修改点击逻辑：不再直接弹窗，而是绘制连接线
                        path.addEventListener('click', function (e) {
                            e.stopPropagation();
                            console.log('点击:', mapChineseName, '事件数:', events.length);
                            // 绘制连接线
                            drawEventConnectors(svgDoc, center.x, center.y, events);
                        });
                    }
                });
            })
            .catch(err => {
                console.error('[ChinaMap] 数据获取失败:', err);
            });
    }

    function addMapStyling(svgDoc) {
        var svg = svgDoc.documentElement;
        var defs = svgDoc.querySelector('defs') || svgDoc.createElementNS('http://www.w3.org/2000/svg', 'defs');
        if (!svgDoc.querySelector('defs')) svg.insertBefore(defs, svg.firstChild);

        var style = svgDoc.createElementNS('http://www.w3.org/2000/svg', 'style');
        style.textContent = `
            #features { filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.3)); }
            #features path { stroke: #FFD700; stroke-width: 1.5; stroke-linejoin: round; stroke-linecap: round; }
            #label_points circle { fill: none; stroke: none; }
            #points circle { fill: #d9534f; stroke: #fff; stroke-width: 2; }
        `;
        defs.appendChild(style);
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
        if (inlineSvg) initMap(document);
    }
});