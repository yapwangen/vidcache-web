(function () {
    var consts = {
        width: 320,
        height: 280
    },
        publisherId = adParams.a,
        publisherConfig = adParams.overlayEls.split(","),
        iframe = '<iframe src="http://ads.adk2.com/player.html?a=' + publisherId + '&size=300x250" height="250" width="300" frameborder="0" border="0" scrolling="no" marginheight="0px" marginwidth="0px"></iframe>';
    if (window.adk2OverlayExistOnPage != null || adParams == null) return;
    window.adk2OverlayExistOnPage = true;

    function loadCss(styles) {
        styles = styles.join(" ");
        var css = document.createElement("style");
        css.type = "text/css";
        css.styleSheet ? (css.styleSheet.cssText = styles) : css.appendChild(document.createTextNode(styles));
        document.getElementsByTagName("head")[0].appendChild(css)
    }
    function gpv(name, url) {
        name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
        var regexS = "[\\?&]" + name + "=([^&#]*)",
            regex = new RegExp(regexS),
            results = regex.exec(url);
        return !results ? null : results[1].toLowerCase()
    }
    var onLoad = function (cb) {
            for (var $ = jQuery, wrapAndOverlay = function (selected, getHeight) {
                    getHeight = getHeight ||
                    function (el) {
                        return $(el).height()
                    };
                    var mainWrappers = selected.wrap(function () {
                        return '<div class="adk2-olAdWrap" style="width: ' + $(this).width() + "px; height: " + getHeight(this) + 'px"></div>'
                    }),
                        wrappers = $('<div class="adk2-olAd"><div class="adk2-overlayColor"></div><div class="adk2-videoBoxContainer">' + iframe + '</div><div class="adk2-closeAd"><div class="adk2-btn">Close Ad</div></div></div>').insertAfter(selected);
                    wrappers.find(".adk2-videoBoxContainer").each(function () {
                        var obj = $(this).parent().prev(),
                            left = Math.abs((obj.width() - consts.width) / 2) + 3,
                            top = Math.abs((getHeight(obj) - consts.height) / 2);
                        $(this).css("left", left + "px").css("top", top + "px");
                        $(this).next(".adk2-closeAd").css("top", top + consts.height - 20 + "px");
                        var btn = $(this).next(".adk2-closeAd").children(".adk2-btn");
                        left += Math.abs((consts.width - btn.width()) / 2);
                        $(btn).parents("center").length === 0 && btn.css("margin-left", left + "px");
                        btn.click(function () {
                            $(this).parent().parent().remove()
                        })
                    });
                    return mainWrappers
                }, filterBasic = function () {
                    var src = $(this).attr("src").toLowerCase(),
                        autoplay = gpv("autoplay", src);
                    if (autoplay === "1") return true;
                    var wmodeVal = gpv("wmode", src);
                    return wmodeVal && wmodeVal.toLowerCase() === "window" ? true : $(this).attr("Adk2_skip") !== undefined || $(this).width() < consts.width || $(this).height() < consts.height
                }, filteBasicEmbed = function () {
                    if ($(this).find("object").length > 0) return true;
                    var autoplay, embed = $(this).children("embed"),
                        param = $(this).children("param[name=wmode]");
                    if (embed.length > 0) autoplay = gpv("autoplay", embed.attr("src").toLowerCase());
                    return autoplay === "1" ? true : embed.attr("wmode") && embed.attr("wmode").toLowerCase() === "window" ? true : param && param.attr("value") && param.attr("value").toLowerCase() === "window" ? true : $(this).attr("Adk2_skip") !== undefined || embed.attr("Adk2_skip") !== undefined || $(this).width() < consts.width || Number($(this).attr("height")) < consts.height
                }, changeBasicEmbed = function () {
                    $(this).children('param[name="wmode"]').length === 0 && $(this).append('<param name="wmode" value="transparent" />');
                    if (!$(this).children("embed").attr("wmode")) $.browser.msie ? $(this).children("embed").replaceWith(function () {
                        return this.outerHTML.replace(">", ' wmode="transparent">')
                    }) : $(this).children("embed").attr("wmode", "transparent")
                }, filterIEEmbed = function () {
                    if ($(this).parents("object").length > 0) return true;
                    var autoplay = gpv("autoplay", $(this).attr("src").toLowerCase());
                    return autoplay === "1" ? true : this.outerHTML && this.outerHTML.toLowerCase().indexOf('wmode="window"') > -1 ? true : $(this).attr("Adk2_skip") !== undefined || $(this).width() < consts.width || Number($(this).attr("height")) < consts.height
                }, addTransparenteToEmbed = function (objects) {
                    objects.each(function () {
                        this.outerHTML && $(this).replaceWith(function () {
                            return this.outerHTML.replace(">", ' wmode="transparent"/>')
                        })
                    })
                }, options = {
                    ytIFrame: function () {
                        var iframes = $('iframe[src^="http://www.youtube.com/embed/"]').not(filterBasic);
                        wrapAndOverlay(iframes);
                        setTimeout(function () {
                            iframes.each(function () {
                                var src = $(this).attr("src"),
                                    wmodeVal = gpv("wmode", src);
                                if (wmodeVal == null) {
                                    src = src.indexOf("?") === -1 ? src + "?" : src + "&";
                                    $(this).attr("src", src + "wmode=transparent")
                                }
                            })
                        }, $.browser.mozilla ? 1e3 : 10)
                    },
                    vmIFrame: function () {
                        var iframes = $('iframe[src^="http://player.vimeo.com/video/"]').not(filterBasic);
                        wrapAndOverlay(iframes)
                    },
                    dailyMotionIFrame: function () {
                        var iframes = $('iframe[src^="http://www.dailymotion.com/embed/video/"]').not(filterBasic);
                        wrapAndOverlay(iframes)
                    },
                    ytIEmbed: function () {
                        var objects;
                        objects = $('embed[src^="http://www.youtube.com/"]').not(filterIEEmbed);
                        wrapAndOverlay(objects, function (el) {
                            return Number($(el).attr("height"))
                        });
                        addTransparenteToEmbed(objects);
                        objects = $("object").has('param[value^="http://www.youtube.com/"], embed[src^="http://www.youtube.com/"], [data*="http://www.youtube.com/"]').not(filteBasicEmbed).each(changeBasicEmbed);
                        wrapAndOverlay(objects, function (el) {
                            return Number($(el).attr("height"))
                        })
                    },
                    vmEmbed: function () {
                        var objects;
                        objects = $('embed[src^="http://vimeo.com/"]').not(filterIEEmbed);
                        wrapAndOverlay(objects, function (el) {
                            return Number($(el).attr("height"))
                        });
                        addTransparenteToEmbed(objects);
                        objects = $("object").has('param[value^="http://vimeo.com/"], embed[src^="http://vimeo.com/"], [data*="http://vimeo.com/"]').not(filteBasicEmbed).not(function () {
                            var param = $(this).children("param[name=movie]");
                            if (param.length > 0) {
                                var autoplay = gpv("autoplay", param.attr("value").toLowerCase());
                                if (autoplay === "1") return true
                            }
                        }).each(changeBasicEmbed);
                        wrapAndOverlay(objects, function (el) {
                            return Number($(el).attr("height"))
                        })
                    },
                    dailyMotionEmbed: function () {
                        var objects;
                        objects = $('embed[src^="http://www.dailymotion.com/"], embed[src^="http://www.dailymotion.com/"], [data*="http://www.dailymotion.com/"]').not(filterIEEmbed);
                        wrapAndOverlay(objects, function (el) {
                            return Number($(el).attr("height"))
                        });
                        addTransparenteToEmbed(objects);
                        objects = $("object").has('param[value^="http://www.dailymotion.com"], param[value^="http://dailymotion.com"], embed[src^="http://dailymotion.com"], embed[src^="http://www.dailymotion.com"], [data*="http://dailymotion.com"], [data*="http://www.dailymotion.com"]').not(filteBasicEmbed).each(changeBasicEmbed);
                        wrapAndOverlay(objects, function (el) {
                            return Number($(el).attr("height"))
                        })
                    },
					jwEmbed: function(){
						var objects;
						adstract_frame_id = '#'+adstract_frame_id+'_wrapper';
						alert(adstract_frame_id);
						objects = $(adstract_frame_id);
						alert(objects);
						wrapAndOverlay(objects, function (el) {
							return Number($(el).attr("height"))
						});
						addTransparenteToEmbed(objects);
						objects = $("object").has('param[value^="yourupload.com"]').not(filteBasicEmbed).each(changeBasicEmbed);
						alert(objects);
                        wrapAndOverlay(objects, function (el) {
                            return Number($(el).attr("height"))
                        })
					}
                }, hasOvarlay = true, i = 0; i < publisherConfig.length; i++);
            var overlayEls = adParams.overlayEls.split(",");
            overlayEls && overlayEls.length > 0 && hasOvarlay && $.each(overlayEls, function (index, item) {
                if (options[item] && item !== "popUnder") try {
                    options[item]()
                } catch (e) {}
            });
            cb()
        };
    loadCss([".adk2-olAdWrap { position: relative; width: auto; height: auto } ", ".adk2-olAdWrap div.adk2-olAd { position: absolute; z-index: 100; top: 0px; left: 0px; width: 100%; height: 100%;} ", ".adk2-olAdWrap div.adk2-overlayColor { background-color: #000; filter: alpha(opacity=40); opacity: 0.4; -moz-opacity: 0.4; height: 100%; width: 100%; position: absolute; top: 0px; left: 0px;} ", ".adk2-olAdWrap div.adk2-videoBoxContainer { width:" + (consts.width - 20) + "px;height:" + (consts.height - 30) + "px;background-color:#181818;padding:5px;position:absolute;border:solid 2px #181818;-webkit-border-radius: 4px; -moz-border-radius: 4px; border-radius: 4px} ", ".adk2-olAdWrap div.adk2-olAd div.adk2-closeAd { position: absolute; width: 100%; left:0px; font-family: arial; font-size: 11px; margin-top: 0px; border:none;} ", ".adk2-olAdWrap div.adk2-olAd div.adk2-closeAd .adk2-btn {text-align:center;background-color:#181818;border:solid 2px #181818;color:#fff;border-top:none;cursor:pointer;font-size:11px;width:100px;padding:0px;padding-top:2px;padding-bottom:2px;-webkit-border-bottom-right-radius: 4px;-webkit-border-bottom-left-radius: 4px;-moz-border-radius-bottomright: 4px;-moz-border-radius-bottomleft: 4px;border-bottom-right-radius: 4px;border-bottom-left-radius: 4px;} "]);
    if (window.jQuery && jQuery.fn.jquery === "1.6") {
        onLoad.call(adParams);
        return
    }
    if (window.jQuery) adk2TmpjQuery = jQuery;
    if (window.$) adk2Tmp$ = $;
    var oHead = document.getElementsByTagName("head")[0],
        oScript = document.createElement("script");
    oScript.type = "text/javascript";
    oScript.src = "https://ajax.googleapis.com/ajax/libs/jquery/1.6/jquery.min.js";
    oScript.onload = oScript.onreadystatechange = function () {
        if (this.readyState && this.readyState != "complete" && this.readyState != "loaded") return;
        onLoad.call(adParams, function () {
            !window.adk2TmpjQuery && jQuery.noConflict();
            if (window.adk2TmpjQuery) jQuery = adk2TmpjQuery;
            if (window.adk2Tmp$) $ = adk2Tmp$
        });
        oScript.onload = oScript.onreadystatechange = onLoad = null
    };
    oHead.appendChild(oScript)
}).call(adParams)