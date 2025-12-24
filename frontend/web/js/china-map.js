document.addEventListener('DOMContentLoaded', function () {
    console.log('[ChinaMap] 极简折线交互版 - 已加载');

    var mapObj = document.getElementById('china-map-object');
    if (mapObj) {
        mapObj.style.height = '900px';
    }

    var baseUrl = window._EVENT_INDEX_URL || '/event/index';
    
    // --- 新增：获取图片基础路径 ---
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
    
    console.log('[ChinaMap] 图片基础路径:', imageBasePath);

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

    // 省份ID到中文名称的映射 - 修复：补全缺失的省份
    var provinceIdMap = {
        'CNSN': '陕西省', 'CNSH': '上海市', 'CNCQ': '重庆市', 'CNZJ': '浙江省',
        'CNJX': '江西省', 'CNSC': '四川省', 'CNHB': '湖北省', 'CNHN': '湖南省',
        'CNGD': '广东省', 'CNFJ': '福建省', 'CNAH': '安徽省', 'CNJS': '江苏省',
        'CNSD': '山东省', 'CNHE': '河北省', 'CNHA': '河南省', 'CNSX': '山西省',
        'CNLN': '辽宁省', 'CNJL': '吉林省', 'CNHL': '黑龙江省', 'CNGS': '甘肃省',
        'CNQH': '青海省', 'CNYN': '云南省', 'CNGZ': '贵州省', 'CNGX': '广西壮族自治区',
        'CNXJ': '新疆维吾尔自治区', 'CNXZ': '西藏自治区', 'CNBJ': '北京市',
        'CNTJ': '天津市', 'CNNM': '内蒙古自治区', 'CNHI': '海南省', 'CNNX': '宁夏回族自治区',
        'CNTW': '台湾省', // 新增台湾
        'CNHK': '香港特别行政区', // 新增香港
        'CNMO': '澳门特别行政区'  // 新增澳门
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


    function drawFlag(svgDoc, x, y) {
        var g = document.createElementNS('http://www.w3.org/2000/svg', 'g');
        g.setAttribute('transform', `translate(${x}, ${y}) scale(1.8)`);
        
        var pole = document.createElementNS('http://www.w3.org/2000/svg', 'line');
        pole.setAttribute('x1', '0');
        pole.setAttribute('y1', '0');
        pole.setAttribute('x2', '0');
        pole.setAttribute('y2', '-26');
        pole.setAttribute('stroke', '#000000');
        pole.setAttribute('stroke-width', '1.33');
        pole.setAttribute('stroke-linecap', 'round');
        pole.style.pointerEvents = 'none';
        pole.style.filter = 'drop-shadow(1px 1px 2px rgba(0,0,0,0.4))';
        
        var flag = document.createElementNS('http://www.w3.org/2000/svg', 'path');
        flag.setAttribute('fill', '#FF3333');
        flag.setAttribute('stroke', '#CC0000');
        flag.setAttribute('stroke-width', '0.5');
        flag.style.pointerEvents = 'none';
        flag.style.filter = 'drop-shadow(2px 2px 4px rgba(0,0,0,0.5))';
        
        var animationStartTime = Date.now();
        var flagWidth = 16;
        var flagHeight = 11;
        
        function animate() {
            var elapsed = (Date.now() - animationStartTime) / 1000;
            var progress = elapsed % 2;
            var segments = 16;
            var d = 'M 0,-26 ';
            
            for (var i = 0; i <= segments; i++) {
                var t = i / segments;
                var xPos = t * flagWidth;
                var edgeFactor = Math.sin(t * Math.PI);
                var phase = progress * Math.PI * 2;
                var amplitude = 1.5;
                var frequency = 2;
                var yOffset = Math.sin(phase + t * Math.PI * frequency) * amplitude * edgeFactor;
                var yPos = -26 + yOffset;
                d += `L ${xPos},${yPos} `;
            }
            
            d += `L ${flagWidth},${-26 + flagHeight} `;
            
            for (var j = segments; j >= 0; j--) {
                var t = j / segments;
                var xPos = t * flagWidth;
                var edgeFactor = Math.sin(t * Math.PI);
                var phase = progress * Math.PI * 2;
                var amplitude = 1.5;
                var frequency = 2;
                var yOffset = Math.sin(phase + t * Math.PI * frequency) * amplitude * edgeFactor;
                var yPos = -26 + flagHeight + yOffset;
                d += `L ${xPos},${yPos} `;
            }
            
            d += 'Z';
            flag.setAttribute('d', d);
            requestAnimationFrame(animate);
        }
        animate();

        g.appendChild(pole);
        g.appendChild(flag);
        svgDoc.documentElement.appendChild(g);
    }

    // 创建省份名称显示标签
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

    // 获取省份标签点的坐标 - 增强：添加调试日志
    function getProvinceLabelPosition(svgDoc, provinceId) {
        var labelPoint = svgDoc.querySelector('#label_points circle[id="' + provinceId + '"]');
        if (labelPoint) {
            var pos = {
                x: parseFloat(labelPoint.getAttribute('cx')),
                y: parseFloat(labelPoint.getAttribute('cy'))
            };
            console.log('[ChinaMap] 找到标签点:', provinceId, pos);
            return pos;
        } else {
            console.warn('[ChinaMap] 未找到标签点:', provinceId);
        }
        return null;
    }

    // 显示省份名称
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

    // 隐藏省份名称
    function hideProvinceLabel() {
        if (provinceLabel) {
            provinceLabel.style.opacity = '0';
        }
    }

    // --- 修改：显示事件详情弹窗(修复图片路径) ---
    var currentSwiper = null;
    var currentEvents = [];

    function showEventModal(events, initialIndex) {
        if (typeof Swiper === 'undefined') {
            console.error('[ChinaMap] Swiper 库尚未加载');
            return;
        }

        var modal = document.getElementById('event-detail-modal');
        currentEvents = events;

        if (currentSwiper) {
            currentSwiper.destroy(true, true);
        }

        var swiperWrapper = document.getElementById('modal-swiper-wrapper');
        swiperWrapper.innerHTML = '';

        events.forEach(function(ev) {
            var slide = document.createElement('div');
            slide.className = 'swiper-slide';

            // 修复图片路径：使用正确的相对路径
            var imageName = 'songhu.webp';
            if (ev.title && ev.title.indexOf('128') > -1) {
                imageName = '128_songhu.webp';
            }
            
            // 构建完整路径
            var imageUrl = imageBasePath + '/images/' + imageName;
            console.log('[ChinaMap] 加载图片:', imageUrl);

            slide.innerHTML = `
                <img src="${imageUrl}" alt="${ev.title || ''}" onerror="console.error('[ChinaMap] 图片加载失败:', this.src)">
                <div class="overlay">
                    <h2>${ev.title || '未知事件'}</h2>
                </div>
            `;
            swiperWrapper.appendChild(slide);
        });

        // 初始化 Swiper
        setTimeout(function() {
            currentSwiper = new Swiper('#event-detail-modal .swiper', {
                effect: 'cards',
                grabCursor: true,
                initialSlide: initialIndex || 0,
                loop: events.length > 1,
                pagination: {
                    el: '#event-detail-modal .swiper-pagination',
                    clickable: true
                },
                on: {
                    slideChange: function() {
                        var realIndex = this.realIndex;
                        updateModalInfo(currentEvents[realIndex]);
                    }
                }
            });

            updateModalInfo(events[initialIndex || 0]);
            modal.classList.add('show');
        }, 100);
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
        if (dateStr && dateStr.length > 10) {
            dateStr = dateStr.substring(0, 10);
        }
        document.getElementById('modal-date').textContent = dateStr;
        document.getElementById('modal-location').textContent = event.location || '未知';
        document.getElementById('modal-summary').textContent = event.summary || '暂无摘要';
        // document.getElementById('modal-content').textContent = event.content || '暂无详情';
    }

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

        // 添加金色描边样式（保留地图整体阴影）
        addMapStyling(svgDoc);

        var paths = svgDoc.querySelectorAll('#features path');
        
        paths.forEach(function(path) {
            var originalFill = path.getAttribute('fill') || '';
            var provinceId = path.getAttribute('id');
            
            // 调试：打印省份ID
            if (provinceId) {
                console.log('[ChinaMap] 省份路径:', provinceId, provinceIdMap[provinceId] || '未映射');
            }
            
            // 确保初始状态正确
            path.style.transition = 'fill 0.3s ease, stroke 0.3s ease, opacity 0.3s ease, transform 0.3s ease, filter 0.3s ease';
            
            // 悬浮效果
            path.addEventListener('mouseenter', function () {
                this.style.fill = '#d9534f';
                this.style.stroke = '#FFA500';
                this.style.strokeWidth = '2';
                this.style.opacity = '0.9';
                this.style.transform = 'translateY(-3px)';
                this.style.filter = 'drop-shadow(0 5px 10px rgba(0,0,0,0.5))';
                
                // 显示省份名称
                if (provinceId) {
                    showProvinceLabel(svgDoc, provinceId);
                }
            });

            path.addEventListener('mouseleave', function () {
                this.style.fill = originalFill;
                this.style.stroke = '#FFD700';
                this.style.strokeWidth = '1.5';
                this.style.opacity = '1';
                this.style.transform = 'translateY(0)';
                this.style.filter = 'none';
                
                // 隐藏省份名称
                hideProvinceLabel();
            });
        });
        
        svgDoc.addEventListener('click', function(e) {
            if (e.target.tagName !== 'path' && e.target.tagName !== 'text') {
                var oldLayer = svgDoc.getElementById('interaction-layer');
                if (oldLayer) oldLayer.remove();
            }
        });

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

                    if (events && events.length > 0) {
                        path.style.cursor = 'pointer';

                        var center = provinceCenters[mapEngName] || provinceCenters[mapChineseName];
                        
                        if (!center) {
                            var bbox = path.getBBox();
                            center = {
                                x: bbox.x + bbox.width / 2,
                                y: bbox.y + bbox.height / 2
                            };
                            provinceCenters[mapChineseName] = center; 
                        }

                        drawFlag(svgDoc, center.x, center.y);

                        path.addEventListener('click', function (e) {
                            e.stopPropagation();
                            console.log('点击:', mapChineseName, '事件数:', events.length);
                            showEventModal(events, 0);
                        });
                    }
                });
            })
            .catch(err => console.error(err));
    }

    // 添加金色描边样式（保留地图整体阴影）
    function addMapStyling(svgDoc) {
        var svg = svgDoc.documentElement;
        
        var defs = svgDoc.querySelector('defs') || svgDoc.createElementNS('http://www.w3.org/2000/svg', 'defs');
        if (!svgDoc.querySelector('defs')) {
            svg.insertBefore(defs, svg.firstChild);
        }

        var style = svgDoc.createElementNS('http://www.w3.org/2000/svg', 'style');
        style.textContent = `
            /* 保留地图整体阴影立体感 */
            #features {
                filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.3));
            }

            /* 默认状态：金色描边 */
            #features path {
                stroke: #FFD700;
                stroke-width: 1.5;
                stroke-linejoin: round;
                stroke-linecap: round;
            }

            #label_points circle {
                fill: none;
                stroke: none;
            }

            #points circle {
                fill: #d9534f;
                stroke: #fff;
                stroke-width: 2;
            }
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