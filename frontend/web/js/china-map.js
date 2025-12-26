document.addEventListener('DOMContentLoaded', function () {
    console.log('[ChinaMap] 优化交互版 - 已加载');

    // === 配色变量 (与CSS保持一致) ===
    var COLORS = {
        goldPrimary: '#C9A227',    // 古铜金 - 主色
        goldLight: '#D4AF37',      // 浅古铜金 - 高亮
        goldMuted: '#A88B2A',      // 暗金 - 次要
        redPrimary: '#8B1A1A',     // 暗红 - 地图填充
        redHover: '#A52A2A',       // 棕红 - 悬停
        textLight: '#F5E6C8',      // 温暖的浅色文字
        textDark: '#1A1A1A'
    };

    // === 动画时长常量 ===
    var TIMING = {
        fast: 200,
        normal: 350,
        slow: 500,
        easeOutQuart: 'cubic-bezier(0.25, 1, 0.5, 1)',
        easeInOut: 'cubic-bezier(0.4, 0, 0.2, 1)'
    };

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

    // --- 绘制三层同心圆点 (带呼吸动画效果) ---
    function drawCircleMarker(svgDoc, x, y, onClickCallback) {
        var g = document.createElementNS('http://www.w3.org/2000/svg', 'g');
        g.setAttribute('transform', `translate(${x}, ${y})`);
        g.style.cursor = 'pointer';
        
        // 统一过渡动画 - 使用更自然的缓动
        var transitionStyle = `r ${TIMING.normal}ms ${TIMING.easeOutQuart}, fill ${TIMING.normal}ms ease, opacity ${TIMING.normal}ms ease`;

        // 最外层圆：呼吸光晕
        var outerCircle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
        outerCircle.setAttribute('cx', '0');
        outerCircle.setAttribute('cy', '0');
        outerCircle.setAttribute('r', '10');
        outerCircle.setAttribute('fill', COLORS.goldMuted);
        outerCircle.setAttribute('opacity', '0.2');
        outerCircle.style.filter = 'blur(2px)';
        outerCircle.style.transition = transitionStyle;
        
        // 中间层圆：主体光环
        var middleCircle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
        middleCircle.setAttribute('cx', '0');
        middleCircle.setAttribute('cy', '0');
        middleCircle.setAttribute('r', '5');
        middleCircle.setAttribute('fill', COLORS.goldPrimary);
        middleCircle.setAttribute('opacity', '0.45');
        middleCircle.style.transition = transitionStyle;
        
        // 最内层圆：核心亮点
        var innerCircle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
        innerCircle.setAttribute('cx', '0');
        innerCircle.setAttribute('cy', '0');
        innerCircle.setAttribute('r', '2.5');
        innerCircle.setAttribute('fill', COLORS.goldLight);
        innerCircle.setAttribute('opacity', '0.9');
        innerCircle.style.filter = 'drop-shadow(0 0 2px rgba(201, 162, 39, 0.6))';
        innerCircle.style.transition = transitionStyle;
        
        // 添加柔和的呼吸动画 (检查 svgDoc 有效性)
        if (svgDoc && svgDoc.createElementNS) {
            var breatheAnimation = svgDoc.createElementNS('http://www.w3.org/2000/svg', 'animate');
            breatheAnimation.setAttribute('attributeName', 'opacity');
            breatheAnimation.setAttribute('values', '0.15;0.3;0.15');
            breatheAnimation.setAttribute('dur', '3s');
            breatheAnimation.setAttribute('repeatCount', 'indefinite');
            outerCircle.appendChild(breatheAnimation);
        }
        
        // 添加圆点（从外到内）
        g.appendChild(outerCircle);
        g.appendChild(middleCircle);
        g.appendChild(innerCircle);
        
        // --- 交互事件监听 - 使用更柔和的变化 ---
        g.addEventListener('mouseenter', function() {
            // 悬浮：温和放大，颜色变亮
            innerCircle.setAttribute('r', '4');
            innerCircle.setAttribute('fill', COLORS.goldLight);
            innerCircle.setAttribute('opacity', '1');
            middleCircle.setAttribute('r', '8');
            middleCircle.setAttribute('opacity', '0.6');
            outerCircle.setAttribute('r', '14');
            outerCircle.setAttribute('opacity', '0.35');
        });

        g.addEventListener('mouseleave', function() {
            // 恢复原状
            innerCircle.setAttribute('r', '2.5');
            innerCircle.setAttribute('fill', COLORS.goldLight);
            innerCircle.setAttribute('opacity', '0.9');
            middleCircle.setAttribute('r', '5');
            middleCircle.setAttribute('opacity', '0.45');
            outerCircle.setAttribute('r', '10');
            outerCircle.setAttribute('opacity', '0.2');
        });

        g.addEventListener('click', function(e) {
            e.stopPropagation();
            // 点击反馈 - 短暂脉冲效果
            innerCircle.setAttribute('r', '6');
            innerCircle.style.filter = 'drop-shadow(0 0 8px rgba(201, 162, 39, 0.9))';
            setTimeout(function() {
                innerCircle.setAttribute('r', '4');
                innerCircle.style.filter = 'drop-shadow(0 0 2px rgba(201, 162, 39, 0.6))';
            }, 150);

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
            
            var fontSize = 14;
            var textStr = ev.title || '未命名事件';
            var textLen = 0;
            for(var i=0; i<textStr.length; i++) {
                textLen += (textStr.charCodeAt(i) > 255 ? 1 : 0.6);
            }
            var rectWidth = textLen * fontSize + 18; 
            var rectHeight = 28;

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
            
            // 创建单个事件组 - 带入场动画
            var itemG = svgDoc.createElementNS('http://www.w3.org/2000/svg', 'g');
            itemG.style.cursor = 'pointer';
            itemG.style.opacity = '0';
            itemG.style.transition = `opacity ${TIMING.normal}ms ${TIMING.easeOutQuart}`;
            
            // 延迟入场动画 - 依次展开
            setTimeout(function() { itemG.style.opacity = '1'; }, index * 60);
            
            // 点击事件：打开详情弹窗
            itemG.onclick = function(e) {
                e.stopPropagation();
                showEventModal(ev);
            };
            
            // 1. 连接线 - 使用古铜金色
            var line = svgDoc.createElementNS('http://www.w3.org/2000/svg', 'line');
            line.setAttribute('x1', centerX);
            line.setAttribute('y1', centerY);
            line.setAttribute('x2', endX);
            line.setAttribute('y2', endY);
            line.setAttribute('stroke', COLORS.goldMuted);
            line.setAttribute('stroke-width', '1.5');
            line.setAttribute('stroke-linecap', 'round');
            line.setAttribute('stroke-opacity', '0.7');
            line.style.transition = `stroke ${TIMING.fast}ms ease, stroke-opacity ${TIMING.fast}ms ease`;
            
            // 2. 连接处的圆形
            var circle = svgDoc.createElementNS('http://www.w3.org/2000/svg', 'circle');
            circle.setAttribute('cx', endX);
            circle.setAttribute('cy', endY);
            circle.setAttribute('r', '3');
            circle.setAttribute('fill', COLORS.goldPrimary);
            circle.style.transition = `fill ${TIMING.fast}ms ease, r ${TIMING.fast}ms ease`;
            
            // 3. 矩形标签 - 使用深色背景
            var rect = svgDoc.createElementNS('http://www.w3.org/2000/svg', 'rect');
            rect.setAttribute('x', rectX);
            rect.setAttribute('y', rectY); 
            rect.setAttribute('width', rectWidth);
            rect.setAttribute('height', rectHeight);
            rect.setAttribute('fill', 'rgba(30, 25, 20, 0.9)');
            rect.setAttribute('stroke', COLORS.goldMuted);
            rect.setAttribute('stroke-width', '1');
            rect.setAttribute('rx', '4');
            rect.style.filter = 'drop-shadow(1px 2px 3px rgba(0,0,0,0.3))';
            rect.style.transition = `fill ${TIMING.fast}ms ease, stroke ${TIMING.fast}ms ease`;
            
            // 4. 文字 - 使用温暖的浅色
            var textX = rectX + 9;
            var textY = rectY + (rectHeight / 2) + (fontSize / 3);
            
            var text = svgDoc.createElementNS('http://www.w3.org/2000/svg', 'text');
            text.setAttribute('x', textX);
            text.setAttribute('y', textY); 
            text.setAttribute('fill', COLORS.textLight);
            text.setAttribute('font-size', fontSize + 'px');
            text.setAttribute('font-weight', '500');
            text.setAttribute('font-family', '"Microsoft YaHei", sans-serif');
            text.style.pointerEvents = 'none';
            text.style.transition = `fill ${TIMING.fast}ms ease`;
            text.textContent = textStr;
            
            // 组装
            itemG.appendChild(line);
            itemG.appendChild(rect);
            itemG.appendChild(circle);
            itemG.appendChild(text);
            
            // 悬停效果 - 更柔和的变化
            itemG.addEventListener('mouseenter', function() {
                rect.setAttribute('fill', 'rgba(50, 40, 30, 0.95)');
                rect.setAttribute('stroke', COLORS.goldLight);
                line.setAttribute('stroke', COLORS.goldLight);
                line.setAttribute('stroke-opacity', '1');
                circle.setAttribute('fill', COLORS.goldLight);
                circle.setAttribute('r', '4');
                text.setAttribute('fill', '#FFFFFF');
                // 悬停时将元素移到最上层
                gLayer.appendChild(itemG);
            });
            itemG.addEventListener('mouseleave', function() {
                rect.setAttribute('fill', 'rgba(30, 25, 20, 0.9)');
                rect.setAttribute('stroke', COLORS.goldMuted);
                line.setAttribute('stroke', COLORS.goldMuted);
                line.setAttribute('stroke-opacity', '0.7');
                circle.setAttribute('fill', COLORS.goldPrimary);
                circle.setAttribute('r', '3');
                text.setAttribute('fill', COLORS.textLight);
            });

            gLayer.appendChild(itemG);
        });
        
        // 整体入场动画
        gLayer.style.opacity = '0';
        svgDoc.documentElement.appendChild(gLayer);
        requestAnimationFrame(function() {
            gLayer.style.transition = `opacity ${TIMING.normal}ms ${TIMING.easeOutQuart}`;
            gLayer.style.opacity = '1';
        });
    }

    // --- 省份标签逻辑 ---
    function createProvinceLabel(svgDoc) {
        provinceLabel = svgDoc.getElementById('province-label');
        if (!provinceLabel) {
            provinceLabel = svgDoc.createElementNS('http://www.w3.org/2000/svg', 'text');
            provinceLabel.setAttribute('id', 'province-label');
            provinceLabel.style.fontSize = '20px';
            provinceLabel.style.fontFamily = '"SimHei", "Microsoft YaHei", sans-serif';
            provinceLabel.style.fontWeight = '600';
            provinceLabel.style.fill = COLORS.goldLight;
            provinceLabel.style.stroke = 'rgba(0, 0, 0, 0.6)';
            provinceLabel.style.strokeWidth = '3px';
            provinceLabel.style.paintOrder = 'stroke fill';
            provinceLabel.style.pointerEvents = 'none';
            provinceLabel.style.opacity = '0';
            provinceLabel.style.transition = `opacity ${TIMING.normal}ms ${TIMING.easeOutQuart}`;
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

    // --- 修改：显示单个事件详情弹窗 ---
    function showEventModal(event) {
        var modal = document.getElementById('event-detail-modal');
        var backdrop = document.getElementById('modal-backdrop');
        
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
            // 纯色占位图 - 使用新配色
            imageUrl = 'data:image/svg+xml;base64,' + btoa(`
                <svg xmlns="http://www.w3.org/2000/svg" width="280" height="200" viewBox="0 0 280 200">
                    <rect width="280" height="200" fill="#2A2520"/>
                    <text x="50%" y="42%" fill="#C9A227" font-size="14" font-family="Arial" text-anchor="middle" dominant-baseline="middle">暂无图片</text>
                    <text x="50%" y="58%" fill="#A88B2A" font-size="11" font-family="Arial" text-anchor="middle" dominant-baseline="middle">${event.title || '未知事件'}</text>
                </svg>
            `);
        }

        // 2. 更新 DOM
        var imgElem = document.getElementById('modal-image');
        if (imgElem) imgElem.src = imageUrl;
        
        var imgTitleElem = document.getElementById('modal-image-title');
        if (imgTitleElem) imgTitleElem.textContent = event.title || '';

        updateModalInfo(event);
        
        // 显示背景遮罩和弹窗
        if (backdrop) backdrop.classList.add('show');
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
            
            // 统一过渡动画
            path.style.transition = `fill ${TIMING.normal}ms ${TIMING.easeInOut}, stroke ${TIMING.normal}ms ${TIMING.easeInOut}, opacity ${TIMING.normal}ms ease, filter ${TIMING.normal}ms ease`;
            
            path.addEventListener('mouseenter', function () {
                this.style.fill = COLORS.redHover;
                this.style.stroke = COLORS.goldLight;
                this.style.strokeWidth = '2';
                this.style.opacity = '0.95';
                this.style.filter = 'drop-shadow(0 3px 8px rgba(0,0,0,0.4))';
                if (provinceId) showProvinceLabel(svgDoc, provinceId);
            });

            path.addEventListener('mouseleave', function () {
                this.style.fill = originalFill;
                this.style.stroke = COLORS.goldMuted;
                this.style.strokeWidth = '1.2';
                this.style.opacity = '1';
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
            #features { filter: drop-shadow(0 3px 6px rgba(0, 0, 0, 0.25)); }
            #features path { 
                stroke: ${COLORS.goldMuted}; 
                stroke-width: 1.2; 
                stroke-linejoin: round; 
                stroke-linecap: round; 
            }
            #label_points circle { fill: none; stroke: none; }
            #points circle { fill: ${COLORS.redPrimary}; stroke: ${COLORS.textLight}; stroke-width: 1.5; }
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