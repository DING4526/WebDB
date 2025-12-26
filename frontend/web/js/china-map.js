document.addEventListener('DOMContentLoaded', function () {
    console.log('[ChinaMap] 优化交互版 - 已加载');

    // === 配色变量 (与CSS保持一致) ===
    var COLORS = {
        goldPrimary: '#C9A227',    // 古铜金 - 主色
        goldLight: '#D4AF37',      // 浅古铜金 - 高亮
        goldMuted: '#A88B2A',      // 暗金 - 次要
        redPrimary: '#8B1A1A',     // 暗红 - 圆点/强调
        redHover: '#A52A2A',       // 棕红 - 备用悬停
        // 地图填充采用低饱和暗红渐变，避免刺眼
        mapRedTop: '#5A2323',      // 顶部渐变色：低饱和暗红
        mapRedBottom: '#4A1C1D',   // 底部渐变色：更深的暗红
        mapRedHoverTop: '#6B2B2B', // 悬停顶部：略亮但仍克制
        mapRedHoverBottom: '#552323', // 悬停底部：适度加深
        textLight: '#F5E6C8',      // 温暖的浅色文字
        textDark: '#1A1A1A'
    };

    // === 动画时长常量 ===
    var TIMING = {
        fast: 200,
        normal: 350,
        slow: 500,
        easeOutQuart: 'cubic-bezier(0.25, 1, 0.5, 1)',
        easeInOut: 'cubic-bezier(0.4, 0, 0.2, 1)',
        // 历史动画播放相关时长
        animModalDelay: 300,       // 动画时显示弹窗前的延迟
        animModalDuration: 1800,   // 动画时弹窗显示时长
        animEventGap: 100          // 动画时事件间隔
    };

    // === 几何常量 ===
    var GEOMETRY = {
        minArrowDistance: 20,  // 绘制箭头的最小距离
        arrowStartOffset: 15,  // 箭头起点偏移
        arrowEndOffset: 20     // 箭头终点偏移
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
    var currentHighlightedPath = null;  // 跟踪当前高亮的省份
    var currentEventId = null;  // 跟踪当前打开弹窗的事件ID

    // 特殊地名到省份的映射（硬编码）
    var specialLocationMap = {
        '北京': ['北京市'],
        '北平': ['北京市'],
        '宛平': ['北京市'],
        '北京宛平': ['北京市'],
        '卢沟桥': ['北京市'],
        '天津': ['天津市'],
        '北平/天津': ['北京市', '天津市'],
        '西安': ['陕西省'],
        '南京': ['江苏省'],
        '沈阳': ['辽宁省'],
        '太行山区': ['山西省', '河北省', '河南省'],
        '太行山': ['山西省', '河北省', '河南省'],
        '华北': ['北京市', '天津市', '河北省', '山西省', '内蒙古自治区'],

    };

    /**
     * 标准化省份名称，支持模糊匹配
     * 例如："上海" <-> "上海市", "北京" <-> "北京市"
     */
    function normalizeProvinceName(name) {
        if (!name) return name;
        
        // 去除首尾空格
        name = name.trim();
        
        // 省级行政区后缀列表
        var suffixes = ['省', '市', '自治区', '特别行政区'];
        
        // 移除后缀，获取核心名称
        var coreName = name;
        for (var i = 0; i < suffixes.length; i++) {
            if (name.endsWith(suffixes[i])) {
                coreName = name.substring(0, name.length - suffixes[i].length);
                break;
            }
        }
        
        return coreName;
    }

    /**
     * 检查两个省份名称是否匹配（支持模糊匹配）
     * 例如："上海" 匹配 "上海市", "北京" 匹配 "北京市"
     */
    function isProvinceMatch(name1, name2) {
        if (!name1 || !name2) return false;
        
        // 完全相同
        if (name1 === name2) return true;
        
        // 标准化后比较
        var core1 = normalizeProvinceName(name1);
        var core2 = normalizeProvinceName(name2);
        
        return core1 === core2;
    }


    function getEventsForMapName(mapName, data) {

        var result = [];
        var seenEventIds = new Set();   // 去重核心

        // 1. 直接匹配该省已有事件（精确匹配）
        if (data[mapName] && Array.isArray(data[mapName])) {
            data[mapName].forEach(function (ev) {
                var id = ev.id || ev._id || JSON.stringify(ev);
                if (!seenEventIds.has(id)) {
                    seenEventIds.add(id);
                    result.push(ev);
                }
            });
        }


        // 2. 模糊匹配：遍历所有数据键，查找可能的匹配
        // 例如：地图是"上海市"，数据可能是"上海"
        for (var locationKey in data) {
            if (!Array.isArray(data[locationKey])) continue;
            
            // 跳过已经精确匹配过的
            if (locationKey === mapName) continue;
            
            // 使用模糊匹配函数检查
            if (isProvinceMatch(locationKey, mapName)) {
                data[locationKey].forEach(function (ev) {
                    var id = ev.id || ev._id || JSON.stringify(ev);
                    if (!seenEventIds.has(id)) {
                        seenEventIds.add(id);
                        result.push(ev);
                    }
                });
            }
        }

        // 3. 扫描所有事件，检查是否有“特殊地名”应归属到该省
        for (var locationKey in specialLocationMap) {
            var provinces = specialLocationMap[locationKey];
            if (!provinces.includes(mapName)) continue;

            for (var province in data) {
                if (!Array.isArray(data[province])) continue;

                data[province].forEach(function (ev) {
                    if (ev.location && ev.location.indexOf(locationKey) > -1) {
                        var id = ev.id || ev._id || JSON.stringify(ev);
                        if (!seenEventIds.has(id)) {
                            seenEventIds.add(id);
                            result.push(ev);
                        }
                    }
                });
            }
        }

        return result.length > 0 ? result : null;
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
        outerCircle.setAttribute('r', '12');  // 从10增加到12
        outerCircle.setAttribute('fill', COLORS.goldMuted);
        outerCircle.setAttribute('opacity', '0.25');  // 从0.2增加到0.25
        outerCircle.style.filter = 'blur(2px)';
        outerCircle.style.transition = transitionStyle;
        
        // 中间层圆：主体光环
        var middleCircle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
        middleCircle.setAttribute('cx', '0');
        middleCircle.setAttribute('cy', '0');
        middleCircle.setAttribute('r', '6');  // 从5增加到6
        middleCircle.setAttribute('fill', COLORS.goldPrimary);
        middleCircle.setAttribute('opacity', '0.5');  // 从0.45增加到0.5
        middleCircle.style.transition = transitionStyle;
        
        // 最内层圆：核心亮点
        var innerCircle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
        innerCircle.setAttribute('cx', '0');
        innerCircle.setAttribute('cy', '0');
        innerCircle.setAttribute('r', '3');  // 从2.5增加到3
        innerCircle.setAttribute('fill', COLORS.goldLight);
        innerCircle.setAttribute('opacity', '0.95');  // 从0.9增加到0.95
        innerCircle.style.filter = 'drop-shadow(0 0 3px rgba(201, 162, 39, 0.7))';  // 增强阴影
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
        
        // 状态跟踪：记录是否处于 hover 状态
        var isHovering = false;
        
        // --- 交互事件监听 - 增强交互效果 ---
        g.addEventListener('mouseenter', function() {
            isHovering = true;
            // 悬浮：明显放大，颜色变亮，增加光晕
            innerCircle.setAttribute('r', '5');  // 从4增加到5
            innerCircle.setAttribute('fill', '#FFD700');  // 更亮的金色
            innerCircle.setAttribute('opacity', '1');
            innerCircle.style.filter = 'drop-shadow(0 0 8px rgba(255, 215, 0, 0.9))';
            middleCircle.setAttribute('r', '10');  // 从8增加到10
            middleCircle.setAttribute('opacity', '0.7');  // 从0.6增加到0.7
            outerCircle.setAttribute('r', '18');  // 从14增加到18
            outerCircle.setAttribute('opacity', '0.45');  // 从0.35增加到0.45
        });

        g.addEventListener('mouseleave', function() {
            isHovering = false;
            // 恢复原状
            innerCircle.setAttribute('r', '3');
            innerCircle.setAttribute('fill', COLORS.goldLight);
            innerCircle.setAttribute('opacity', '0.95');
            innerCircle.style.filter = 'drop-shadow(0 0 3px rgba(201, 162, 39, 0.7))';
            middleCircle.setAttribute('r', '6');
            middleCircle.setAttribute('opacity', '0.5');
            outerCircle.setAttribute('r', '12');
            outerCircle.setAttribute('opacity', '0.25');
        });

        g.addEventListener('click', function(e) {
            e.stopPropagation();
            // 点击反馈 - 更强烈的脉冲效果
            innerCircle.setAttribute('r', '8');  // 从6增加到8
            innerCircle.style.filter = 'drop-shadow(0 0 15px rgba(255, 215, 0, 1))';
            middleCircle.setAttribute('r', '12');
            outerCircle.setAttribute('r', '20');
            outerCircle.setAttribute('opacity', '0.6');
            
            setTimeout(function() {
                // 恢复时检查是否还在 hover 状态
                if (isHovering) {
                    // 恢复到 hover 状态
                    innerCircle.setAttribute('r', '5');
                    innerCircle.setAttribute('fill', '#FFD700');
                    innerCircle.setAttribute('opacity', '1');
                    innerCircle.style.filter = 'drop-shadow(0 0 8px rgba(255, 215, 0, 0.9))';
                    middleCircle.setAttribute('r', '10');
                    middleCircle.setAttribute('opacity', '0.7');
                    outerCircle.setAttribute('r', '18');
                    outerCircle.setAttribute('opacity', '0.45');
                } else {
                    // 恢复到初始状态
                    innerCircle.setAttribute('r', '3');
                    innerCircle.setAttribute('fill', COLORS.goldLight);
                    innerCircle.setAttribute('opacity', '0.95');
                    innerCircle.style.filter = 'drop-shadow(0 0 3px rgba(201, 162, 39, 0.7))';
                    middleCircle.setAttribute('r', '6');
                    middleCircle.setAttribute('opacity', '0.5');
                    outerCircle.setAttribute('r', '12');
                    outerCircle.setAttribute('opacity', '0.25');
                }
            }, 200);  // 从150增加到200

            if (onClickCallback) onClickCallback(e);
        });

        svgDoc.documentElement.appendChild(g);
    }

    // --- 清除省份高亮状态 ---
    function clearProvinceHighlight() {
        if (currentHighlightedPath) {
            currentHighlightedPath.style.fill = 'url(#mapFillGradient)';
            currentHighlightedPath.style.stroke = COLORS.goldMuted;
            currentHighlightedPath.style.strokeWidth = '1.2';
            currentHighlightedPath.style.filter = 'none';
            currentHighlightedPath = null;
        }
    }

    // --- 设置省份高亮状态 ---
    function setProvinceHighlight(path) {
        clearProvinceHighlight();
        currentHighlightedPath = path;
        path.style.fill = 'url(#mapHoverGradient)';
        path.style.stroke = COLORS.goldLight;
        path.style.strokeWidth = '3';
        path.style.filter = 'drop-shadow(0 4px 12px rgba(201,162,39,0.5)) brightness(1.1)';
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
            
            var fontSize = 16;  // 从18减少到16，减弱强调
            var textStr = ev.title || '未命名事件';
            // 限制文字长度，超过10个字符截断
            if (textStr.length > 12) {
                textStr = textStr.substring(0, 11) + '…';
            }
            var textLen = 0;
            for(var i=0; i<textStr.length; i++) {
                textLen += (textStr.charCodeAt(i) > 255 ? 1 : 0.55);
            }
            // 定义内边距，让文字和矩形更协调
            var paddingX = 10;
            var paddingY = 8;
            var rectWidth = textLen * fontSize + paddingX * 2;
            var rectHeight = fontSize + paddingY * 2;

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
            rect.setAttribute('padding', '8'); 
            rect.setAttribute('width', rectWidth);
            rect.setAttribute('height', rectHeight);
            rect.setAttribute('fill', 'rgba(30, 25, 20, 0.9)');
            rect.setAttribute('stroke', COLORS.goldMuted);
            rect.setAttribute('stroke-width', '1');
            rect.setAttribute('rx', '4');
            rect.style.filter = 'drop-shadow(1px 2px 3px rgba(0,0,0,0.3))';
            rect.style.transition = `fill ${TIMING.fast}ms ease, stroke ${TIMING.fast}ms ease`;
            
            // 4. 文字 - 使用温暖的浅色，位置基于内边距
            var textX = rectX + paddingX;
            var textY = rectY + paddingY + fontSize * 0.75;  // 基线对齐
            
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
            
            // 悬停效果 - 更柔和的视觉反馈
            itemG.addEventListener('mouseenter', function() {
                rect.setAttribute('fill', 'rgba(50, 40, 30, 0.95)');
                rect.setAttribute('stroke', COLORS.goldLight);
                rect.setAttribute('stroke-width', '1.5');
                rect.style.filter = 'drop-shadow(1px 2px 5px rgba(0,0,0,0.4))';
                line.setAttribute('stroke', COLORS.goldLight);
                line.setAttribute('stroke-width', '2');
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
                rect.setAttribute('stroke-width', '1');
                rect.style.filter = 'drop-shadow(1px 2px 3px rgba(0,0,0,0.3))';
                line.setAttribute('stroke', COLORS.goldMuted);
                line.setAttribute('stroke-width', '1.5');
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

    // --- 绘制单个事件的连接线（用于动画播放时） ---
    function drawSingleEventConnector(svgDoc, centerX, centerY, event) {
        clearConnectors(svgDoc);
        
        var gLayer = svgDoc.createElementNS('http://www.w3.org/2000/svg', 'g');
        gLayer.setAttribute('id', 'connector-layer');
        
        // 获取SVG视口边界
        var svgRoot = svgDoc.documentElement;
        var svgBounds = {
            minX: 0, minY: 0, maxX: 1000, maxY: 800
        };
        
        if (svgRoot.viewBox && svgRoot.viewBox.baseVal && svgRoot.viewBox.baseVal.width > 0) {
            var vb = svgRoot.viewBox.baseVal;
            svgBounds.minX = vb.x;
            svgBounds.minY = vb.y;
            svgBounds.maxX = vb.x + vb.width;
            svgBounds.maxY = vb.y + vb.height;
        }
        
        var padding = 25;
        svgBounds.minX += padding;
        svgBounds.minY += padding;
        svgBounds.maxX -= padding;
        svgBounds.maxY -= padding;
        
        // 计算地图中心来决定文字朝向
        var mapCX = (svgBounds.minX + svgBounds.maxX) / 2;
        var mapCY = (svgBounds.minY + svgBounds.maxY) / 2;
        var vecX = centerX - mapCX;
        var vecY = centerY - mapCY;
        var baseAngle = Math.atan2(vecY, vecX);
        
        // 配置参数
        var baseRadius = 100;
        var fontSize = 16;
        var textStr = event.title || '未命名事件';
        if (textStr.length > 14) {
            textStr = textStr.substring(0, 13) + '…';
        }
        
        var textLen = 0;
        for(var i = 0; i < textStr.length; i++) {
            textLen += (textStr.charCodeAt(i) > 255 ? 1 : 0.55);
        }
        
        var paddingX = 12;
        var paddingY = 10;
        var rectWidth = textLen * fontSize + paddingX * 2;
        var rectHeight = fontSize + paddingY * 2;
        
        var endX = centerX + baseRadius * Math.cos(baseAngle);
        var endY = centerY + baseRadius * Math.sin(baseAngle);
        var isRightSide = Math.cos(baseAngle) >= 0;
        var rectX = isRightSide ? endX : (endX - rectWidth);
        var rectY = endY - rectHeight / 2;
        
        // 边界修正
        if (rectX < svgBounds.minX) rectX = svgBounds.minX;
        if (rectX + rectWidth > svgBounds.maxX) rectX = svgBounds.maxX - rectWidth;
        if (rectY < svgBounds.minY) rectY = svgBounds.minY;
        if (rectY + rectHeight > svgBounds.maxY) rectY = svgBounds.maxY - rectHeight;
        
        // 创建单个事件组
        var itemG = svgDoc.createElementNS('http://www.w3.org/2000/svg', 'g');
        itemG.style.pointerEvents = 'none'; // 动画时禁用点击
        
        // 1. 连接线 - 使用与点击时一致的样式
        var line = svgDoc.createElementNS('http://www.w3.org/2000/svg', 'line');
        line.setAttribute('x1', centerX);
        line.setAttribute('y1', centerY);
        line.setAttribute('x2', endX);
        line.setAttribute('y2', endY);
        line.setAttribute('stroke', COLORS.goldMuted);
        line.setAttribute('stroke-width', '1.5');
        line.setAttribute('stroke-linecap', 'round');
        line.setAttribute('stroke-opacity', '0.7');
        
        // 2. 连接处的圆形 - 使用与点击时一致的样式
        var circle = svgDoc.createElementNS('http://www.w3.org/2000/svg', 'circle');
        circle.setAttribute('cx', endX);
        circle.setAttribute('cy', endY);
        circle.setAttribute('r', '3');
        circle.setAttribute('fill', COLORS.goldPrimary);
        
        // 3. 矩形标签 - 使用与点击时一致的样式
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
        
        // 4. 文字 - 使用与点击时一致的样式
        var textX = rectX + paddingX;
        var textY = rectY + paddingY + fontSize * 0.75;
        
        var text = svgDoc.createElementNS('http://www.w3.org/2000/svg', 'text');
        text.setAttribute('x', textX);
        text.setAttribute('y', textY);
        text.setAttribute('fill', COLORS.textLight);
        text.setAttribute('font-size', fontSize + 'px');
        text.setAttribute('font-weight', '500');
        text.setAttribute('font-family', '"Microsoft YaHei", sans-serif');
        text.textContent = textStr;
        
        // 组装
        itemG.appendChild(line);
        itemG.appendChild(rect);
        itemG.appendChild(circle);
        itemG.appendChild(text);
        gLayer.appendChild(itemG);
        
        // 入场动画
        gLayer.style.opacity = '0';
        gLayer.style.transition = 'opacity 0.3s ease';
        svgDoc.documentElement.appendChild(gLayer);
        
        requestAnimationFrame(function() {
            gLayer.style.opacity = '1';
        });
    }

    // --- 省份标签逻辑 ---
    function createProvinceLabel(svgDoc) {
        provinceLabel = svgDoc.getElementById('province-label');
        if (!provinceLabel) {
            provinceLabel = svgDoc.createElementNS('http://www.w3.org/2000/svg', 'text');
            provinceLabel.setAttribute('id', 'province-label');
            provinceLabel.setAttribute('font-size', '26');
            provinceLabel.setAttribute('font-weight', '600');
            provinceLabel.setAttribute('fill', COLORS.goldLight);
            provinceLabel.setAttribute('text-anchor', 'start');
            provinceLabel.setAttribute('font-family', '"STKaiti", "KaiTi", "STSong", "SimSun", serif');
            provinceLabel.setAttribute('letter-spacing', '2');
            provinceLabel.style.pointerEvents = 'none';
            provinceLabel.style.opacity = '0';
            provinceLabel.style.transition = `opacity ${TIMING.normal}ms ${TIMING.easeOutQuart}`;
            // 金字+白边+多层阴影，增强对比度
            provinceLabel.setAttribute('stroke', '#FFFFFF');
            provinceLabel.setAttribute('stroke-width', '1.5');
            provinceLabel.setAttribute('stroke-linejoin', 'round');
            provinceLabel.setAttribute('stroke-linecap', 'round');
            provinceLabel.setAttribute('paint-order', 'stroke fill');
            provinceLabel.style.filter = 'drop-shadow(0 3px 5px rgba(0,0,0,0.9)) drop-shadow(0 0 8px rgba(0,0,0,0.7)) drop-shadow(0 0 15px rgba(201,162,39,0.4))';
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
        // 将标签显示在省份位置左上方偏移
        label.setAttribute('x', position.x - 60);
        label.setAttribute('y', position.y - 15);
        label.style.opacity = '0.92';
    }

    function hideProvinceLabel() {
        if (provinceLabel) provinceLabel.style.opacity = '0';
    }

    // --- 修改：显示单个事件详情弹窗 ---
    function showEventModal(event, skipBackdrop) {
        var modal = document.getElementById('event-detail-modal');
        var backdrop = document.getElementById('modal-backdrop');
        
        // 存储当前事件ID用于跳转
        currentEventId = event.id;
        modal.setAttribute('data-event-id', event.id);
        
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
        
        // 显示弹窗（动画模式时跳过背景遮罩）
        if (!skipBackdrop && backdrop) backdrop.classList.add('show');
        modal.classList.add('show');
    }

    function updateModalInfo(event) {
        var titleElem = document.getElementById('modal-title');
        titleElem.textContent = event.title || '未知事件';
        // 移除标题的单独点击事件，改为整个modal-content可点击

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
            path.style.transition = `fill ${TIMING.normal}ms ${TIMING.easeInOut}, stroke ${TIMING.normal}ms ${TIMING.easeInOut}, opacity ${TIMING.normal}ms ease, filter ${TIMING.normal}ms ease, stroke-width ${TIMING.fast}ms ease`;
            
            path.addEventListener('mouseenter', function () {
                // 如果不是当前高亮的省份，才应用hover效果
                if (this !== currentHighlightedPath) {
                    this.style.fill = 'url(#mapHoverGradient)';
                    this.style.stroke = COLORS.goldLight;
                    this.style.strokeWidth = '2.5';
                    this.style.opacity = '1';
                    this.style.filter = 'drop-shadow(0 3px 10px rgba(201,162,39,0.4)) brightness(1.05)';
                }
            });

            path.addEventListener('mouseleave', function () {
                // 如果不是当前高亮的省份，才恢复默认样式
                if (this !== currentHighlightedPath) {
                    this.style.fill = 'url(#mapFillGradient)';
                    this.style.stroke = COLORS.goldMuted;
                    this.style.strokeWidth = '1.2';
                    this.style.opacity = '1';
                    this.style.filter = 'none';
                }
            });
        });
        
        // 全局点击监听：点击空白处清除连接线和高亮状态
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
            
            // 检查是否点击了圆点标记
            var isMarker = false;
            parent = target;
            while(parent && parent !== svgDoc) {
                if (parent.tagName === 'g' && parent.style && parent.style.cursor === 'pointer') {
                    // 检查是否是圆点组（包含circle元素）
                    var circles = parent.querySelectorAll('circle');
                    if (circles.length >= 2) {
                        isMarker = true;
                        break;
                    }
                }
                parent = parent.parentNode;
            }
            
            // 如果点击的不是省份块、连接线、圆点标记，则清除所有状态
            if (target.tagName !== 'path' && !isConnector && !isMarker) {
                clearConnectors(svgDoc);
                hideProvinceLabel();
                clearProvinceHighlight();
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
                        // 标记有事件的省份，用于样式强调
                        path.classList.add('has-events');

                        var center = provinceCenters[mapEngName] || provinceCenters[mapChineseName];
                        if (!center) {
                            var bbox = path.getBBox();
                            center = { x: bbox.x + bbox.width / 2, y: bbox.y + bbox.height / 2 };
                            provinceCenters[mapChineseName] = center; 
                        }

                        // 传递点击回调，使圆点点击也能触发连接线
                        drawCircleMarker(svgDoc, center.x, center.y, function() {
                            console.log('点击圆点:', mapChineseName);
                            // 获取省份 ID 用于显示省份名
                            var provinceId = path.getAttribute('id');
                            if (provinceId) {
                                showProvinceLabel(svgDoc, provinceId);
                            }
                            // 使用统一的高亮函数
                            setProvinceHighlight(path);
                            
                            drawEventConnectors(svgDoc, center.x, center.y, events);
                        });

                        // 修改点击逻辑：不再直接弹窗，而是绘制连接线
                        path.addEventListener('click', function (e) {
                            e.stopPropagation();
                            console.log('点击:', mapChineseName, '事件数:', events.length);
                            // 显示省份名
                            var provinceId = path.getAttribute('id');
                            if (provinceId) {
                                showProvinceLabel(svgDoc, provinceId);
                            }
                            // 使用统一的高亮函数
                            setProvinceHighlight(this);
                            // 绘制连接线
                            drawEventConnectors(svgDoc, center.x, center.y, events);
                        });
                    }
                });
                
                // 绑定动画控制按钮
                bindAnimationControls(svgDoc, activeData);
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
                fill: url(#mapFillGradient);
            }
            #features path.has-events { stroke-width: 1.6; }
            #features path.has-events:hover { stroke-width: 2.2; }
            #label_points circle { fill: none; stroke: none; }
            #points circle { fill: ${COLORS.redPrimary}; stroke: ${COLORS.textLight}; stroke-width: 1.5; }
        `;
        defs.appendChild(style);

        // 渐变：增强层次，采用低饱和暗红系避免刺眼
        var gradFill = svgDoc.createElementNS('http://www.w3.org/2000/svg', 'linearGradient');
        gradFill.setAttribute('id', 'mapFillGradient');
        gradFill.setAttribute('x1', '0%');
        gradFill.setAttribute('y1', '0%');
        gradFill.setAttribute('x2', '0%');
        gradFill.setAttribute('y2', '100%');

        var stop1 = svgDoc.createElementNS('http://www.w3.org/2000/svg', 'stop');
        stop1.setAttribute('offset', '0%');
        stop1.setAttribute('stop-color', COLORS.mapRedTop);
        stop1.setAttribute('stop-opacity', '0.95');
        gradFill.appendChild(stop1);

        var stop2 = svgDoc.createElementNS('http://www.w3.org/2000/svg', 'stop');
        stop2.setAttribute('offset', '100%');
        stop2.setAttribute('stop-color', COLORS.mapRedBottom);
        stop2.setAttribute('stop-opacity', '0.95');
        gradFill.appendChild(stop2);

        defs.appendChild(gradFill);

        var gradHover = svgDoc.createElementNS('http://www.w3.org/2000/svg', 'linearGradient');
        gradHover.setAttribute('id', 'mapHoverGradient');
        gradHover.setAttribute('x1', '0%');
        gradHover.setAttribute('y1', '0%');
        gradHover.setAttribute('x2', '0%');
        gradHover.setAttribute('y2', '100%');

        var h1 = svgDoc.createElementNS('http://www.w3.org/2000/svg', 'stop');
        h1.setAttribute('offset', '0%');
        h1.setAttribute('stop-color', COLORS.mapRedHoverTop);
        h1.setAttribute('stop-opacity', '0.98');
        gradHover.appendChild(h1);

        var h2 = svgDoc.createElementNS('http://www.w3.org/2000/svg', 'stop');
        h2.setAttribute('offset', '100%');
        h2.setAttribute('stop-color', COLORS.mapRedHoverBottom);
        h2.setAttribute('stop-opacity', '0.98');
        gradHover.appendChild(h2);

        defs.appendChild(gradHover);
    }

    // ======================================
    // === 历史动画播放系统 ===
    // ======================================
    
    var animationState = {
        isPlaying: false,
        isPaused: false,  // 新增：暂停状态
        currentStep: 0,
        allEvents: [],
        timeouts: [],
        svgDoc: null,
        arrowLayer: null,
        previousLocation: null,
        interactionBlocked: false,  // 动画期间阻止其他交互
        activeData: null  // 新增：保存数据用于恢复播放
    };

    // 根据事件的location解析对应的省份名（用于动画时匹配省份路径）
    function resolveProvinceForEvent(event, dataKey) {
        var location = event.location || '';
        
        // 1. 首先检查事件的location是否匹配特殊地名
        for (var locationKey in specialLocationMap) {
            if (location.indexOf(locationKey) > -1) {
                // 返回第一个匹配的省份（对于跨省事件取第一个）
                return specialLocationMap[locationKey][0];
            }
        }
        
        // 2. 如果dataKey本身就是省份名，直接返回
        // 检查是否在provinceMap的值中
        for (var engName in provinceMap) {
            if (provinceMap[engName] === dataKey) {
                return dataKey;
            }
        }
        
        // 3. 如果dataKey是特殊地名，解析为省份
        if (specialLocationMap[dataKey]) {
            return specialLocationMap[dataKey][0];
        }
        
        // 4. 默认返回dataKey（可能是未识别的省份名）
        return dataKey;
    }

    // 获取所有事件并按时间排序
    function getAllEventsSorted(activeData) {
        var allEvents = [];
        var seenIds = new Set();
        
        for (var dataKey in activeData) {
            if (!Array.isArray(activeData[dataKey])) continue;
            activeData[dataKey].forEach(function(event) {
                // Use event id, _id, or a composite key based on title+date+location
                var id = event.id || event._id || 
                    (event.title || '') + '_' + (event.event_date || '') + '_' + (event.location || '');
                if (!seenIds.has(id)) {
                    seenIds.add(id);
                    // 解析事件对应的省份名，确保能匹配到地图路径
                    var resolvedProvince = resolveProvinceForEvent(event, dataKey);
                    allEvents.push({
                        event: event,
                        province: resolvedProvince,
                        date: event.event_date || '1931-01-01'
                    });
                }
            });
        }
        
        // 按日期排序
        allEvents.sort(function(a, b) {
            return new Date(a.date) - new Date(b.date);
        });
        
        return allEvents;
    }

    // 更新时间标签
    function updateTimelineLabel(event, show) {
        var label = document.getElementById('timeline-label');
        if (!label) return;
        
        if (!show) {
            label.classList.remove('show', 'animating');
            return;
        }
        
        var dateStr = event.event_date || '';
        var year = '';
        var fullDate = '';
        
        if (dateStr) {
            var parts = dateStr.split('-');
            year = parts[0] || '';
            if (parts[1] && parts[2]) {
                fullDate = parts[1] + '月' + parts[2] + '日';
            } else if (parts[1]) {
                fullDate = parts[1] + '月';
            }
        }
        
        var yearEl = label.querySelector('.timeline-label-year');
        var dateEl = label.querySelector('.timeline-label-date');
        var eventEl = label.querySelector('.timeline-label-event');
        
        if (yearEl) yearEl.textContent = year;
        if (dateEl) dateEl.textContent = fullDate;
        if (eventEl) eventEl.textContent = event.title || '';
        
        label.classList.add('show', 'animating');
    }

    // 创建箭头图层
    function createArrowLayer(svgDoc) {
        var existing = svgDoc.getElementById('arrow-layer');
        if (existing) existing.remove();
        
        var layer = svgDoc.createElementNS('http://www.w3.org/2000/svg', 'g');
        layer.setAttribute('id', 'arrow-layer');
        svgDoc.documentElement.appendChild(layer);
        return layer;
    }

    // 绘制地点变化箭头
    function drawLocationArrow(svgDoc, fromCenter, toCenter) {
        if (!fromCenter || !toCenter) return null;
        
        var layer = svgDoc.getElementById('arrow-layer');
        if (!layer) layer = createArrowLayer(svgDoc);
        
        // 清除之前的箭头
        layer.innerHTML = '';
        
        // 计算箭头路径
        var dx = toCenter.x - fromCenter.x;
        var dy = toCenter.y - fromCenter.y;
        var distance = Math.sqrt(dx * dx + dy * dy);
        
        if (distance < GEOMETRY.minArrowDistance) return null; // 距离太近不画箭头
        
        // 归一化方向
        var nx = dx / distance;
        var ny = dy / distance;
        
        // 起点和终点偏移（避免覆盖圆点）
        var startX = fromCenter.x + nx * GEOMETRY.arrowStartOffset;
        var startY = fromCenter.y + ny * GEOMETRY.arrowStartOffset;
        var endX = toCenter.x - nx * GEOMETRY.arrowEndOffset;
        var endY = toCenter.y - ny * GEOMETRY.arrowEndOffset;
        
        // 创建贝塞尔曲线控制点（弧形路径）
        var midX = (startX + endX) / 2;
        var midY = (startY + endY) / 2;
        var perpX = -ny * (distance * 0.15); // 垂直方向偏移
        var perpY = nx * (distance * 0.15);
        var ctrlX = midX + perpX;
        var ctrlY = midY + perpY;
        
        // 创建路径
        var path = svgDoc.createElementNS('http://www.w3.org/2000/svg', 'path');
        var pathD = 'M ' + startX + ' ' + startY + ' Q ' + ctrlX + ' ' + ctrlY + ' ' + endX + ' ' + endY;
        path.setAttribute('d', pathD);
        path.setAttribute('fill', 'none');
        path.setAttribute('stroke', COLORS.goldLight);
        path.setAttribute('stroke-width', '2.5');
        path.setAttribute('stroke-linecap', 'round');
        path.setAttribute('stroke-dasharray', '8, 4');
        path.style.filter = 'drop-shadow(0 2px 4px rgba(201, 162, 39, 0.5))';
        path.style.opacity = '0';
        path.style.transition = 'opacity 0.5s ease';
        
        // 创建箭头头部
        var arrowSize = 10;
        // 计算终点处的切线方向
        var t = 0.95; // 接近终点
        var tangentX = 2 * (1 - t) * (ctrlX - startX) + 2 * t * (endX - ctrlX);
        var tangentY = 2 * (1 - t) * (ctrlY - startY) + 2 * t * (endY - ctrlY);
        var tangentLen = Math.sqrt(tangentX * tangentX + tangentY * tangentY);
        tangentX /= tangentLen;
        tangentY /= tangentLen;
        
        var arrowAngle = Math.PI / 6; // 30度
        var ax1 = endX - arrowSize * (tangentX * Math.cos(arrowAngle) - tangentY * Math.sin(arrowAngle));
        var ay1 = endY - arrowSize * (tangentY * Math.cos(arrowAngle) + tangentX * Math.sin(arrowAngle));
        var ax2 = endX - arrowSize * (tangentX * Math.cos(-arrowAngle) - tangentY * Math.sin(-arrowAngle));
        var ay2 = endY - arrowSize * (tangentY * Math.cos(-arrowAngle) + tangentX * Math.sin(-arrowAngle));
        
        var arrowHead = svgDoc.createElementNS('http://www.w3.org/2000/svg', 'polygon');
        arrowHead.setAttribute('points', endX + ',' + endY + ' ' + ax1 + ',' + ay1 + ' ' + ax2 + ',' + ay2);
        arrowHead.setAttribute('fill', COLORS.goldLight);
        arrowHead.style.filter = 'drop-shadow(0 1px 3px rgba(201, 162, 39, 0.6))';
        arrowHead.style.opacity = '0';
        arrowHead.style.transition = 'opacity 0.5s ease';
        
        layer.appendChild(path);
        layer.appendChild(arrowHead);
        
        // 添加虚线流动动画
        var animateOffset = svgDoc.createElementNS('http://www.w3.org/2000/svg', 'animate');
        animateOffset.setAttribute('attributeName', 'stroke-dashoffset');
        animateOffset.setAttribute('from', '24');
        animateOffset.setAttribute('to', '0');
        animateOffset.setAttribute('dur', '0.8s');
        animateOffset.setAttribute('repeatCount', 'indefinite');
        path.appendChild(animateOffset);
        
        // 显示箭头
        requestAnimationFrame(function() {
            path.style.opacity = '0.85';
            arrowHead.style.opacity = '0.9';
        });
        
        return { path: path, arrow: arrowHead };
    }

    // 清除箭头
    function clearArrows(svgDoc) {
        var layer = svgDoc.getElementById('arrow-layer');
        if (layer) layer.innerHTML = '';
    }

    // 播放单个事件的动画
    function playEventAnimation(svgDoc, eventData, index, callback) {
        var event = eventData.event;
        var province = eventData.province;
        
        // 获取省份路径
        var paths = svgDoc.querySelectorAll('#features path');
        var targetPath = null;
        var targetCenter = null;
        
        paths.forEach(function(path) {
            var mapEngName = path.getAttribute('name');
            if (!mapEngName) return;
            var mapChineseName = provinceMap[mapEngName];
            // 使用模糊匹配，支持"上海" <-> "上海市"等情况
            if (isProvinceMatch(mapChineseName, province)) {
                targetPath = path;
                targetCenter = provinceCenters[mapEngName] || provinceCenters[province];
                if (!targetCenter) {
                    var bbox = path.getBBox();
                    targetCenter = { x: bbox.x + bbox.width / 2, y: bbox.y + bbox.height / 2 };
                }
            }
        });
        
        if (!targetPath || !targetCenter) {
            if (callback) callback();
            return;
        }
        
        // 更新时间标签
        updateTimelineLabel(event, true);
        
        // 绘制地点变化箭头
        if (animationState.previousLocation && 
            (animationState.previousLocation.x !== targetCenter.x || 
             animationState.previousLocation.y !== targetCenter.y)) {
            drawLocationArrow(svgDoc, animationState.previousLocation, targetCenter);
        }
        animationState.previousLocation = targetCenter;
        
        // 高亮省份
        setProvinceHighlight(targetPath);
        
        // 显示省份名
        var provinceId = targetPath.getAttribute('id');
        if (provinceId) {
            showProvinceLabel(svgDoc, provinceId);
        }
        
        // 创建临时高亮圆点
        var highlightCircle = createHighlightCircle(svgDoc, targetCenter.x, targetCenter.y);
        
        // 绘制单个事件的连接线+rect（立即显示，无延迟）
        drawSingleEventConnector(svgDoc, targetCenter.x, targetCenter.y, event);
        
        // 显示弹窗（无遮罩，更快显示）
        animationState.timeouts.push(setTimeout(function() {
            showEventModal(event, true); // 跳过背景遮罩
            
            // 弹窗显示后的延迟
            animationState.timeouts.push(setTimeout(function() {
                // 移除高亮圆点
                if (highlightCircle && highlightCircle.parentNode) {
                    highlightCircle.style.opacity = '0';
                    setTimeout(function() {
                        if (highlightCircle.parentNode) {
                            highlightCircle.parentNode.removeChild(highlightCircle);
                        }
                    }, 300);
                }
                
                // 关闭弹窗
                closeEventModal();
                
                // 清除连接线（准备下一个事件）
                clearConnectors(svgDoc);
                
                // 继续下一个
                if (callback) callback();
            }, TIMING.animModalDuration));
        }, TIMING.animModalDelay));
    }

    // 创建高亮圆点效果
    function createHighlightCircle(svgDoc, x, y) {
        var g = svgDoc.createElementNS('http://www.w3.org/2000/svg', 'g');
        g.setAttribute('transform', 'translate(' + x + ', ' + y + ')');
        g.setAttribute('class', 'animation-highlight-marker');
        
        // 外层脉冲圈
        var pulseCircle = svgDoc.createElementNS('http://www.w3.org/2000/svg', 'circle');
        pulseCircle.setAttribute('cx', '0');
        pulseCircle.setAttribute('cy', '0');
        pulseCircle.setAttribute('r', '20');
        pulseCircle.setAttribute('fill', 'none');
        pulseCircle.setAttribute('stroke', COLORS.goldLight);
        pulseCircle.setAttribute('stroke-width', '2');
        pulseCircle.setAttribute('opacity', '0.8');
        
        // 添加脉冲动画
        var animateR = svgDoc.createElementNS('http://www.w3.org/2000/svg', 'animate');
        animateR.setAttribute('attributeName', 'r');
        animateR.setAttribute('from', '10');
        animateR.setAttribute('to', '30');
        animateR.setAttribute('dur', '1.2s');
        animateR.setAttribute('repeatCount', 'indefinite');
        pulseCircle.appendChild(animateR);
        
        var animateOpacity = svgDoc.createElementNS('http://www.w3.org/2000/svg', 'animate');
        animateOpacity.setAttribute('attributeName', 'opacity');
        animateOpacity.setAttribute('from', '0.8');
        animateOpacity.setAttribute('to', '0');
        animateOpacity.setAttribute('dur', '1.2s');
        animateOpacity.setAttribute('repeatCount', 'indefinite');
        pulseCircle.appendChild(animateOpacity);
        
        // 中心亮点
        var centerCircle = svgDoc.createElementNS('http://www.w3.org/2000/svg', 'circle');
        centerCircle.setAttribute('cx', '0');
        centerCircle.setAttribute('cy', '0');
        centerCircle.setAttribute('r', '8');
        centerCircle.setAttribute('fill', COLORS.goldPrimary);
        centerCircle.setAttribute('opacity', '0.9');
        centerCircle.style.filter = 'drop-shadow(0 0 8px rgba(201, 162, 39, 0.8))';
        
        g.appendChild(pulseCircle);
        g.appendChild(centerCircle);
        
        g.style.opacity = '0';
        g.style.transition = 'opacity 0.3s ease';
        
        svgDoc.documentElement.appendChild(g);
        
        requestAnimationFrame(function() {
            g.style.opacity = '1';
        });
        
        return g;
    }

    // 关闭事件弹窗
    function closeEventModal() {
        var modal = document.getElementById('event-detail-modal');
        var backdrop = document.getElementById('modal-backdrop');
        if (modal) modal.classList.remove('show');
        if (backdrop) backdrop.classList.remove('show');
    }

    // 开始播放动画
    function startAnimation(svgDoc, activeData) {
        if (animationState.isPlaying && !animationState.isPaused) return;
        
        animationState.isPlaying = true;
        animationState.isPaused = false;
        animationState.interactionBlocked = true;  // 阻止其他交互
        animationState.svgDoc = svgDoc;
        animationState.activeData = activeData;  // 保存数据
        
        // 如果不是从暂停恢复，重置状态
        if (animationState.currentStep === 0 || !animationState.allEvents || animationState.allEvents.length === 0) {
            animationState.currentStep = 0;
            animationState.previousLocation = null;
            animationState.allEvents = getAllEventsSorted(activeData);
        }
        
        console.log('[Animation] 开始播放，共 ' + animationState.allEvents.length + ' 个事件，当前步骤: ' + animationState.currentStep);
        
        // 更新按钮状态
        var playBtn = document.getElementById('btn-play-animation');
        var skipBtn = document.getElementById('btn-skip-animation');
        if (playBtn) {
            playBtn.classList.add('playing');
            playBtn.innerHTML = '<i class="glyphicon glyphicon-pause"></i><span>暂停</span>';
        }
        if (skipBtn) skipBtn.style.display = 'flex';
        
        // 添加交互阻止遮罩
        addInteractionBlocker();
        
        // 创建箭头图层（如果还没有）
        if (!svgDoc.getElementById('arrow-layer')) {
            createArrowLayer(svgDoc);
        }
        
        // 开始播放
        playNextEvent();
    }

    // 播放下一个事件
    function playNextEvent() {
        if (!animationState.isPlaying || animationState.isPaused) return;
        
        if (animationState.currentStep >= animationState.allEvents.length) {
            // 动画结束
            stopAnimation();
            console.log('[Animation] 播放完成');
            return;
        }
        
        var eventData = animationState.allEvents[animationState.currentStep];
        animationState.currentStep++;
        
        playEventAnimation(animationState.svgDoc, eventData, animationState.currentStep - 1, function() {
            // 短暂延迟后播放下一个
            animationState.timeouts.push(setTimeout(function() {
                playNextEvent();
            }, TIMING.animEventGap));
        });
    }

    // 添加交互阻止遮罩（仅覆盖地图区域，不影响控制按钮）
    function addInteractionBlocker() {
        var existing = document.getElementById('animation-interaction-blocker');
        if (existing) return;
        
        var mapWrapper = document.getElementById('china-map-wrapper');
        if (!mapWrapper) return;
        
        var blocker = document.createElement('div');
        blocker.id = 'animation-interaction-blocker';
        // 使用兼容性更好的 CSS 写法
        blocker.style.cssText = 'position: absolute; top: 0; right: 0; bottom: 0; left: 0; z-index: 50; cursor: not-allowed; background: transparent;';
        
        // 阻止所有交互事件
        var blockEvent = function(e) {
            e.stopPropagation();
            e.preventDefault();
        };
        blocker.addEventListener('click', blockEvent);
        blocker.addEventListener('mousedown', blockEvent);
        blocker.addEventListener('mouseup', blockEvent);
        blocker.addEventListener('touchstart', blockEvent);
        blocker.addEventListener('touchend', blockEvent);
        
        mapWrapper.style.position = 'relative';
        mapWrapper.appendChild(blocker);
    }
    
    // 移除交互阻止遮罩
    function removeInteractionBlocker() {
        var blocker = document.getElementById('animation-interaction-blocker');
        if (blocker && blocker.parentNode) {
            blocker.parentNode.removeChild(blocker);
        }
    }

    // 暂停动画
    function pauseAnimation() {
        if (!animationState.isPlaying || animationState.isPaused) return;
        
        console.log('[Animation] 暂停于步骤 ' + animationState.currentStep);
        animationState.isPaused = true;
        
        // 清除所有定时器
        animationState.timeouts.forEach(function(t) {
            clearTimeout(t);
        });
        animationState.timeouts = [];
        
        // 更新按钮状态
        var playBtn = document.getElementById('btn-play-animation');
        if (playBtn) {
            playBtn.innerHTML = '<i class="glyphicon glyphicon-play"></i><span>继续</span>';
        }
    }
    
    // 恢复动画
    function resumeAnimation() {
        if (!animationState.isPlaying || !animationState.isPaused) return;
        
        console.log('[Animation] 从步骤 ' + animationState.currentStep + ' 继续');
        animationState.isPaused = false;
        
        // 更新按钮状态
        var playBtn = document.getElementById('btn-play-animation');
        if (playBtn) {
            playBtn.innerHTML = '<i class="glyphicon glyphicon-pause"></i><span>暂停</span>';
        }
        
        // 继续播放
        playNextEvent();
    }

    // 停止动画
    function stopAnimation() {
        console.log('[Animation] 停止动画');
        animationState.isPlaying = false;
        animationState.isPaused = false;
        animationState.interactionBlocked = false;  // 恢复交互
        
        // 清除所有定时器
        animationState.timeouts.forEach(function(t) {
            clearTimeout(t);
        });
        animationState.timeouts = [];
        
        // 移除交互阻止遮罩
        removeInteractionBlocker();
        
        // 重置状态
        animationState.currentStep = 0;
        animationState.allEvents = [];
        animationState.activeData = null;
        
        // 恢复UI
        var playBtn = document.getElementById('btn-play-animation');
        var skipBtn = document.getElementById('btn-skip-animation');
        if (playBtn) {
            playBtn.classList.remove('playing');
            playBtn.innerHTML = '<i class="glyphicon glyphicon-play"></i><span>回顾历史</span>';
        }
        if (skipBtn) skipBtn.style.display = 'none';
        
        // 隐藏时间标签
        updateTimelineLabel(null, false);
        
        // 关闭弹窗
        closeEventModal();
        
        // 清除高亮和箭头
        if (animationState.svgDoc) {
            clearProvinceHighlight();
            hideProvinceLabel();
            clearConnectors(animationState.svgDoc);
            clearArrows(animationState.svgDoc);
            
            // 移除高亮标记
            var markers = animationState.svgDoc.querySelectorAll('.animation-highlight-marker');
            markers.forEach(function(m) {
                if (m.parentNode) m.parentNode.removeChild(m);
            });
        }
        
        animationState.previousLocation = null;
    }

    // 绑定按钮事件
    function bindAnimationControls(svgDoc, activeData) {
        var playBtn = document.getElementById('btn-play-animation');
        var skipBtn = document.getElementById('btn-skip-animation');
        
        if (playBtn) {
            playBtn.addEventListener('click', function() {
                if (animationState.isPlaying && !animationState.isPaused) {
                    // 正在播放 -> 暂停
                    pauseAnimation();
                } else if (animationState.isPaused) {
                    // 已暂停 -> 继续
                    resumeAnimation();
                } else {
                    // 未开始 -> 开始播放
                    startAnimation(svgDoc, activeData);
                }
            });
        }
        
        if (skipBtn) {
            skipBtn.addEventListener('click', function() {
                stopAnimation();
            });
        }
        
        // 首次加载时自动启动动画序列
        if (window._AUTO_START_ANIMATION) {
            window._AUTO_START_ANIMATION = false; // 防止重复触发
            runIntroSequence(svgDoc, activeData);
        }
    }

    // 运行首页开场序列
    function runIntroSequence(svgDoc, activeData) {
        var introOverlay = document.getElementById('intro-overlay');
        var mottoText = document.getElementById('motto-text');
        var commemText = document.getElementById('commem-text');
        var mapTitle = document.getElementById('map-title');
        
        var skipIntro = false;
        var introTimeout;
        var fadeTimeout;
        
        // 跳过开场动画的函数
        function skipToMainContent() {
            if (skipIntro) return;
            skipIntro = true;
            
            // 清除所有定时器
            if (introTimeout) clearTimeout(introTimeout);
            if (fadeTimeout) clearTimeout(fadeTimeout);
            
            // 立即隐藏遮罩
            if (introOverlay) {
                introOverlay.classList.add('fade-out');
                setTimeout(function() {
                    introOverlay.classList.add('hidden');
                }, 300);
            }
            
            // 获取竖线元素
            var verticalDivider = document.querySelector('.vertical-divider');
            
            // 立即显示所有文案元素
            if (mottoText) {
                mottoText.classList.add('animate-in', 'delay-1');
            }
            if (verticalDivider) {
                setTimeout(function() {
                    verticalDivider.classList.add('animate-in');
                }, 350);
            }
            if (mapTitle) {
                mapTitle.classList.add('animate-in', 'delay-3');
            }
            
            // 短暂延迟后开始历史动画
            setTimeout(function() {
                startAnimation(svgDoc, activeData);
            }, 800);
        }
        
        // 点击或按键跳过
        if (introOverlay) {
            introOverlay.style.cursor = 'pointer';
            introOverlay.addEventListener('click', skipToMainContent);
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ' || e.key === 'Escape') {
                    skipToMainContent();
                }
            }, { once: true });
        }
        
        // 第一步: 显示开场遮罩 2.5 秒
        introTimeout = setTimeout(function() {
            if (skipIntro) return;
            
            // 开始淡出遮罩
            if (introOverlay) {
                introOverlay.classList.add('fade-out');
            }
            
            // 淡出后显示文案
            fadeTimeout = setTimeout(function() {
                if (skipIntro) return;
                
                // 移除遮罩
                if (introOverlay) {
                    introOverlay.classList.add('hidden');
                }
                
                // 获取竖线元素
                var verticalDivider = document.querySelector('.vertical-divider');
                
                // 错落显示文案元素 - 按特定顺序
                // 1. 铭句（主视觉）先显示
                if (mottoText) {
                    mottoText.classList.add('animate-in', 'delay-1');
                }
                // 2. 竖线分隔符
                if (verticalDivider) {
                    setTimeout(function() {
                        verticalDivider.classList.add('animate-in');
                    }, 350);
                }
                // 3. 图注（大标题）
                if (mapTitle) {
                    mapTitle.classList.add('animate-in', 'delay-3');
                }
                
                // 文案显示后自动开始历史动画
                setTimeout(function() {
                    startAnimation(svgDoc, activeData);
                }, 1200);
                
            }, 1000); // 遮罩淡出动画时长
        }, 2500); // 遮罩显示时长
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
