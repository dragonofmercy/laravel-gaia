const gui = {

    MAX_UID: 1000000,

    init: function(context){

        this.context = context || 'body';
        this.initTheme();

        // Init GUI Theme Switcher
        $('[data-gui-behavior="theme-switcher"]', this.context).GUIThemeSwitcher();

        // Init GUI tabs behavior
        $('[data-gui-behavior="tabs"]', this.context).GUITabs();

        // Init copy behavior
        $('[data-gui-behavior="copy"]', this.context).GUICopy();

        // Init copy behavior
        $('[data-gui-behavior="range"]', this.context).GUIControlRange();

        // Init copy behavior
        $('[data-gui-behavior="modal"]', this.context).GUIModal();

        // Init copy behavior
        $('[data-gui-behavior="fullscreen"]', this.context).GUIFullscreen();

        // Init card loading
        $('[data-gui-behavior="card-loading"]', this.context).on('click', function(){
            $(this).closest('.card').addClass('loading');
        });

        // Init modal dismiss
        $('#gui_modal [data-bs-dismiss="modal"]').on('click', function(e){
            e.preventDefault();
            $(this).closest('.modal').data('gui.modal').hide();
        });

        // Init GUI buttons
        $('[data-loading-text]', this.context).on('click', function(){
            $(this).GUIButton('loading');
        });

        // Init bootstrap tooltips toggle
        $('[data-bs-toggle="tooltip"]', this.context).each(function(){
            new bootstrap.Tooltip($(this)[0], {});
        });

        // Init bootstrap popover toggle
        $('[data-bs-toggle="popover"]', this.context).each(function(){
            new bootstrap.Popover($(this)[0], {});
        });

        // Fix missing readonly attribute
        $('select[readonly]').on('focus', function(){
            $(this).trigger('blur');
        });

        // Remove modal loading
        if($(this.context).is('.modal-content')){
            $(this.context).parents('.modal-dialog').removeClass('loading');
        }
    },

    isDarkMode: function(){
        return $('html').attr('data-theme') === 'dark';
    },

    initTheme: function(){
        if($('html').attr('data-theme')){
            return;
        }

        if(Cookies.get('dark-mode') !== undefined){
            if(Cookies.get('dark-mode') === 'true'){
                $('html').attr('data-theme', 'dark');
            }
        }
    },

    openModal: function(target, classname){
        let attributes = {};

        attributes['data-modal-target'] = target.substring(0, 1) === '#' ? target : '#gui_modal';
        attributes['data-modal-class'] = classname || '';

        if(target.substring(0, 1) !== '#'){
            attributes['data-modal-url'] = target;
        }

        $('<a />').attr(attributes).GUIModal().trigger('click');
    },

    openPopup: function(url, name, w, h, config){
        config = config || 'toolbar=no,resizable=yes,scrollbars=yes,menubar=no,location=no,directories=no,status=no';
        let dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
        let dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;
        let width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
        let height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;
        let left = ((width / 2) - (w / 2)) + dualScreenLeft;
        let top = ((height / 2) - (h / 2)) + dualScreenTop;
        let newWindow = window.open(url, name, config + ',width=' + w + ',height=' + h + ',top=' + top + ',left=' + left);
        if (window.focus) {
            newWindow.focus();
        }
    },

    getUID: function(prefix = ""){
        do {
            prefix += Math.floor(Math.random() * this.MAX_UID);
        } while(document.getElementById(prefix))
        return prefix;
    },

    getCssVariable: function(variable, $context){
        $context = $context || $(document.documentElement);
        return getComputedStyle($context[0]).getPropertyValue(variable);
    },

    datagridSearch: function(uid, url, clear){
        let data = $('#datagrid_search_' + uid + ' :input').serialize();
        if(clear){
            data = encodeURI($.map($('#datagrid_search_' + uid + ' :input').serializeArray(), function(val) {
                return val.name + '=';
            }).join("&"));
        } else {
            $('#datagrid_search_' + uid + ' [data-search]').GUIButton('loading');
        }
        $.ajax({
            type: 'POST',
            url: url,
            dataType: 'html',
            data: data,
            headers: {"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr('content')},
            success: function(v){
                $('#' + uid).html(v);
            }
        });
    },

    datagridSearchKeydown: function(uid, url, e){
        if(e.keyCode == 13){
            this.datagridSearch(uid, url);
        }
    },

    datagridSelection: function(el, uid, only_one, remote, strings, confirm_msg, new_window, modal){
        let $element = $(el);
        let $value = '';
        let $nb = 0;
        let trailing = '';

        $element.trigger('blur');

        $(".gui-selector-checkbox input", '#' + uid).each(function(){
            if($(this).prop('checked')){
                $value += $(this).val() + ",";
                $nb++;
            }
        });

        if ($nb < 1) {
            alert(strings['no_elements']);
        } else if(only_one && $nb > 1) {
            alert(strings['only_one']);
        } else {
            if(confirm_msg){
                if(!confirm(confirm_msg)){
                    return false;
                }
            }

            if($element.attr('href').slice(-1) !== '='){
                trailing = '/';
            }

            let url = $element.attr('href') + trailing + $value.substring(0, $value.length - 1);

            if(remote) {
                $.ajax({
                    url: url,
                    context: document.body,
                    type: 'POST',
                    headers: {"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr('content')},
                    success: function(data) {
                        $("#" + uid).html(data);
                    }
                });
            } else if(new_window) {
                window.open(url);
            } else if(modal) {
                gui.openModal(url, $element.attr('data-modal-class'));
            } else {
                if(window.location.href) {
                    window.location.href = url;
                } else {
                    window.location.replace(url);
                }
            }
        }

        return false;
    },

    ignitionErrorFrame: function(response, parent){
        if(parent.hasClass('modal-content')){
            let content = $('<div class="modal-body" />');
            let header = $('<div class="modal-header" />')
                .append($('<h2 class="modal-title" />').html("Error"))
                .append('<button type="button" class="btn-close" data-bs-dismiss="modal"></button>');

            parent.html('');
            parent.parent().removeClass(['modal-sm', 'modal-md', 'modal-lg', 'modal-xl', 'modal-xxl']).addClass('modal-xl');
            parent.append(header);
            parent.append(content);

            parent = content;
        }

        let errorFrame = $('<iframe />');
        errorFrame.css({ width: "100%", height: "70vh"});
        errorFrame.get(0).srcdoc = response;
        parent.html(errorFrame);
    },

    canvasScale: function(cv, scale){
        if (!(scale < 1) || !(scale > 0)) throw ('scale must be a positive number <1 ');
        let sqScale = scale * scale; // square scale = area of source pixel within target
        let sw = cv.width; // source image width
        let sh = cv.height; // source image height
        let tw = Math.ceil(sw * scale); // target image width
        let th = Math.ceil(sh * scale); // target image height
        let sx = 0, sIndex = 0; // source x,y, index within source array
        let tx = 0, ty = 0, yIndex = 0, tIndex = 0; // target x,y, x,y index within target array
        let tX = 0, tY = 0; // rounded tx, ty
        let w = 0, nw = 0, wx = 0, nwx = 0, wy = 0, nwy = 0; // weight / next weight x / y
        // weight is weight of current source point within target.
        // next weight is weight of current source point within next target's point.
        let crossX = false; // does scaled px cross its current px right border ?
        let crossY = false; // does scaled px cross its current px bottom border ?
        let sBuffer = cv.getContext('2d').
        getImageData(0, 0, sw, sh).data; // source buffer 8 bit rgba
        let tBuffer = new Float32Array(4 * sw * sh); // target buffer Float32 rgb
        let sR = 0, sG = 0,  sB = 0; // source's current point r,g,b
        // untested !
        let sA = 0;  //source alpha

        for (let sy = 0; sy < sh; sy++) {
            ty = sy * scale; // y src position within target
            tY = 0 | ty;     // rounded : target pixel's y
            yIndex = 4 * tY * tw;  // line index within target array
            crossY = (tY != (0 | ty + scale));
            if (crossY) { // if pixel is crossing botton target pixel
                wy = (tY + 1 - ty); // weight of point within target pixel
                nwy = (ty + scale - tY - 1); // ... within y+1 target pixel
            }
            for (sx = 0; sx < sw; sx++, sIndex += 4) {
                tx = sx * scale; // x src position within target
                tX = 0 |  tx;    // rounded : target pixel's x
                tIndex = yIndex + tX * 4; // target pixel index within target array
                crossX = (tX != (0 | tx + scale));
                if (crossX) { // if pixel is crossing target pixel's right
                    wx = (tX + 1 - tx); // weight of point within target pixel
                    nwx = (tx + scale - tX - 1); // ... within x+1 target pixel
                }
                sR = sBuffer[sIndex    ];   // retrieving r,g,b for curr src px.
                sG = sBuffer[sIndex + 1];
                sB = sBuffer[sIndex + 2];
                sA = sBuffer[sIndex + 3];

                if (!crossX && !crossY) { // pixel does not cross
                    // just add components weighted by squared scale.
                    tBuffer[tIndex    ] += sR * sqScale;
                    tBuffer[tIndex + 1] += sG * sqScale;
                    tBuffer[tIndex + 2] += sB * sqScale;
                    tBuffer[tIndex + 3] += sA * sqScale;
                } else if (crossX && !crossY) { // cross on X only
                    w = wx * scale;
                    // add weighted component for current px
                    tBuffer[tIndex    ] += sR * w;
                    tBuffer[tIndex + 1] += sG * w;
                    tBuffer[tIndex + 2] += sB * w;
                    tBuffer[tIndex + 3] += sA * w;
                    // add weighted component for next (tX+1) px
                    nw = nwx * scale
                    tBuffer[tIndex + 4] += sR * nw; // not 3
                    tBuffer[tIndex + 5] += sG * nw; // not 4
                    tBuffer[tIndex + 6] += sB * nw; // not 5
                    tBuffer[tIndex + 7] += sA * nw; // not 6
                } else if (crossY && !crossX) { // cross on Y only
                    w = wy * scale;
                    // add weighted component for current px
                    tBuffer[tIndex    ] += sR * w;
                    tBuffer[tIndex + 1] += sG * w;
                    tBuffer[tIndex + 2] += sB * w;
                    tBuffer[tIndex + 3] += sA * w;
                    // add weighted component for next (tY+1) px
                    nw = nwy * scale
                    tBuffer[tIndex + 4 * tw    ] += sR * nw; // *4, not 3
                    tBuffer[tIndex + 4 * tw + 1] += sG * nw; // *4, not 3
                    tBuffer[tIndex + 4 * tw + 2] += sB * nw; // *4, not 3
                    tBuffer[tIndex + 4 * tw + 3] += sA * nw; // *4, not 3
                } else { // crosses both x and y : four target points involved
                    // add weighted component for current px
                    w = wx * wy;
                    tBuffer[tIndex    ] += sR * w;
                    tBuffer[tIndex + 1] += sG * w;
                    tBuffer[tIndex + 2] += sB * w;
                    tBuffer[tIndex + 3] += sA * w;
                    // for tX + 1; tY px
                    nw = nwx * wy;
                    tBuffer[tIndex + 4] += sR * nw; // same for x
                    tBuffer[tIndex + 5] += sG * nw;
                    tBuffer[tIndex + 6] += sB * nw;
                    tBuffer[tIndex + 7] += sA * nw;
                    // for tX ; tY + 1 px
                    nw = wx * nwy;
                    tBuffer[tIndex + 4 * tw    ] += sR * nw; // same for mul
                    tBuffer[tIndex + 4 * tw + 1] += sG * nw;
                    tBuffer[tIndex + 4 * tw + 2] += sB * nw;
                    tBuffer[tIndex + 4 * tw + 3] += sA * nw;
                    // for tX + 1 ; tY +1 px
                    nw = nwx * nwy;
                    tBuffer[tIndex + 4 * tw + 4] += sR * nw; // same for both x and y
                    tBuffer[tIndex + 4 * tw + 5] += sG * nw;
                    tBuffer[tIndex + 4 * tw + 6] += sB * nw;
                    tBuffer[tIndex + 4 * tw + 7] += sA * nw;
                }
            } // end for sx
        } // end for sy

        // create result canvas
        let resCV = document.createElement('canvas');
        resCV.width = tw;
        resCV.height = th;
        let resCtx = resCV.getContext('2d');
        let imgRes = resCtx.getImageData(0, 0, tw, th);
        let tByteBuffer = imgRes.data;
        // convert float32 array into a UInt8Clamped Array
        let pxIndex = 0; //
        for (sIndex = 0, tIndex = 0; pxIndex < tw * th; sIndex += 4, tIndex += 4, pxIndex++) {
            tByteBuffer[tIndex] = Math.ceil(tBuffer[sIndex]);
            tByteBuffer[tIndex + 1] = Math.ceil(tBuffer[sIndex + 1]);
            tByteBuffer[tIndex + 2] = Math.ceil(tBuffer[sIndex + 2]);
            tByteBuffer[tIndex + 3] = Math.ceil(tBuffer[sIndex + 3]);
        }
        // writing result to canvas.
        resCtx.putImageData(imgRes, 0, 0);
        return resCV;
    }
}

gui.init();