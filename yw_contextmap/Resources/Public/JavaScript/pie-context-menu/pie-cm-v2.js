(function (global, factory) {
    if (typeof define === 'function' && define.amd) {
        define([], factory);
    } else if (typeof module === 'object' && module.exports) {
        module.exports = factory();
    } else {
        global.PieContextMenu = factory();
    }
}(this, function () {
    "use strict";

    var SVG_NS = "http://www.w3.org/2000/svg";
    var that;
    var touchPoint;
    var evt;
    var touch;
    var selected;
    var button;

    var PieContextMenu = function (menuObject) {
        this.menuablesClass = menuObject.menuItemClass;
        this.numberOfButton = 0;

        this.menu;
        this.menu_id = menuObject.menuID;
        this.menuPosition;
        this.menu_model = menuObject.menu_model || 1;

        this.menu_svg;
        this.menu_svg_id = this.menu_id + "_svg";

        this.touchDuration = menuObject.touchDuration || 300;

        this.center;

        this.menuState = 0;
        this.active = "pie-context-menu--active";
        this.menuSize = menuObject.menuSize;

        this.menuActivateEvent = menuObject.menuActivateEvent || "contextmenu";

        this.radius;
        this.stroke_width;
        this.font_size;

        this.lastSelectedElement = null;
        this.buttons = [];

        this.init();
    };

    PieContextMenu.Button = function (index, text, icon) {
        this.index = index;
        this.text = text;
        this.icon = icon;
        this.isNew = true;
        this.element;
    };

    PieContextMenu.prototype.init = function () {
        this.menu = document.createElement("nav");
        this.menu_svg = document.createElementNS(SVG_NS, "svg");
        this.menu.appendChild(this.menu_svg);

        this.center = document.createElementNS(SVG_NS, "circle");
        this.menu_svg.appendChild(this.center);

        this.reset();
        this.create();

        this.menuActivateListener(this.menuActivateEvent);
        this.leftClickListener();
        this.keyupListener();
    };

    PieContextMenu.prototype.create = function () {
        var menu_node = document.getElementById(this.menu_id);
        if (menu_node === null)
            document.body.appendChild(this.menu);
    };

    PieContextMenu.prototype.destroy = function () {
        var menu_node = document.getElementById(this.menu_id);
        if (menu_node != null)
            document.body.removeChild(this.menu);
    };

    PieContextMenu.prototype.reset = function () {
        this.closeMenu();

        this.radius = this.menuSize/3;
        this.stroke_width = this.menuSize/5;
        this.font_size = Math.round(this.menuSize/12);

        this.menu.setAttribute("id", this.menu_id);
        this.menu.setAttribute("class", "pie-context-menu");
        this.menu.setAttribute("width", this.menuSize);
        this.menu.setAttribute("height", this.menuSize);

        this.menu_svg.setAttribute("id", this.menu_svg_id);
        this.menu_svg.setAttribute("width", this.menuSize);
        this.menu_svg.setAttribute("height", this.menuSize);

        this.draw();
    };

    PieContextMenu.prototype.menuActivateListener = function (event) {
        that = this;

        document.addEventListener(event, function (e) {
            selected = that.containsClass(e, that.menuablesClass);
            if (selected) {
                e.preventDefault();
                that.lastSelectedElement = selected;
                that.openMenu();
                that.positionMenu(e);
		// vimi - defines os overlay div id as global variable ymelementId, which is used to open recipe modal
		// by context menu using pcm.js in ym_en.js
		window.ymclickelementId = e.target.id;
            } else {
                that.closeMenu();
            }
        });

        /** Touch Code **/
        var timer;
        var touchButtonIndex;

        function duringLongTouch(e){
            e.preventDefault();
            if(e.target.classList.contains(that.menuablesClass)){
                that.openContextMenu(e);
            }
        }
        function touchstart(e) {
	    //e.preventDefault(); //required, that doubble tapdragzoom works in Chrome on iPad
            timer = setTimeout(() => duringLongTouch(e), that.touchDuration);
	    window.ymtouchelementId = e.target.id;
        }
        function touchend(e) {
            if (timer) clearTimeout(timer);
            if(that.menu_model === 2){
                touchPoint = that.getElementPoint(e);
                if(touchPoint.classList.length && (touchPoint.classList[0] === 'pcm_button') && !!touchPoint.parentNode.id){
                    document.querySelector('#'+touchPoint.parentNode.id).dispatchEvent(new Event('click'));
                }
                that.closeMenu();
            }
        }
        function menuTouchHoverEffect(touchButtons, tpi){
            touchButtonIndex = touchButtons.indexOf(tpi);
            touchButtons.forEach(function (tb, i){
                if(touchButtonIndex !== i){
                    PieContextMenu.menu_button_mouseout(document.getElementById(tb));
                }
            })
        }
        function touchMove(e){
            e.preventDefault();
            touchPoint = that.getElementPoint(e);
            var touchButtons = [];
            that.buttons.forEach(function (button){
                touchButtons.push(button.element.id);
            })
            if(touchPoint.classList.length && (touchPoint.classList[0] === 'pcm_button')){
                if(!!touchPoint.parentNode.id){
                    var tpi = touchPoint.parentNode.id;
                    PieContextMenu.menu_button_mouseover(document.getElementById(tpi));
                    menuTouchHoverEffect(touchButtons, tpi)
                }
            }else{
                that.buttons.forEach(function (button){
                    PieContextMenu.menu_button_mouseout(document.getElementById(button.element.id));
                })
            }
        }
	//document.addEventLi -> original
	// touchstart only recognized on viewer div id=os-rez13 -> no issues anymore with other site content like modals!
	// touchend & touchmove only recognized on menu div id=my_menu, since this is the active div where the touch takes place!
       //// document.getElementById("os-rez13").addEventListener("touchstart", (e) => touchstart(e));
        document.getElementById("os-rez13").addEventListener("touchstart", (e) => {
		touchstart(e);
                // get elementId of touched overlay and save as global variable ymelementId
		// used in pcm.js in ym_en.js to identify and show related recipe modal
               // window.ymtouchelementId = e.target.id;
		//if (ymelementId !== '') {
		//	alert(ymelementId);
		//};
                //touchstart(e);
                //alert(elementId);
                });
        document.getElementById("my_menu").addEventListener("touchend", (e) => touchend(e), false);
        document.getElementById("my_menu").addEventListener("touchmove", (e) => touchMove(e), {passive: false});
        /** Touch Code **/
    };
    PieContextMenu.prototype.openContextMenu = function (e){
        e.preventDefault();
        evt = (typeof e.originalEvent === 'undefined') ? e : e.originalEvent;
        touch = evt.touches[0] || evt.changedTouches[0];
        var menuAreaSelector;
        var eContextmenu;
        selected = that.containsClass(e, that.menuablesClass);
        if(selected){
            that.lastSelectedElement = selected;
        }
        touchPoint = document.elementFromPoint(touch.pageX,touch.pageY);
        if(touchPoint.classList.length && touchPoint.classList[0] === that.menuablesClass){
            menuAreaSelector = document.getElementsByClassName(that.menuablesClass)[0];
            eContextmenu = new MouseEvent("contextmenu", {
                bubbles: true,
                cancelable: false,
                view: window,
                button: 2,
                buttons: 0,
                clientX:  touch.pageX,
                clientY:  touch.pageY
            });
            menuAreaSelector.dispatchEvent(eContextmenu);
        }
    }
    PieContextMenu.prototype.getElementPoint = function (e){
        evt = (typeof e.originalEvent === 'undefined') ? e : e.originalEvent;
        touch = evt.touches[0] || evt.changedTouches[0];
        return document.elementFromPoint(touch.pageX,touch.pageY);
    }

    PieContextMenu.prototype.leftClickListener = function () {
        that = this;
        document.addEventListener("click", function (e) {
            touchPoint = document.elementFromPoint(e.clientX, e.clientY);
            button = e.which || e.button;
            if (button === 1) {
                if(!(touchPoint.classList.length && touchPoint.classList[0] === that.menuablesClass)){
                    that.closeMenu();
                }
            }
        });
    };

    PieContextMenu.prototype.keyupListener = function () {
        that = this;
        document.addEventListener("keydown", function (e) {
            var keyCode = e.keyCode;
            if (keyCode === 27) {
                that.closeMenu();
            }
        }, false);
    };

    PieContextMenu.prototype.closeMenu = function () {
        if (this.menuState !== 0) {
            this.menuState = 0;
            this.menu.classList.remove(this.active);
        }
    };

    PieContextMenu.prototype.openMenu = function () {
        if (this.menuState !== 1) {
            this.menuState = 1;
            this.menu.classList.add(this.active);
        }
    };

    PieContextMenu.prototype.positionMenu = function (e) {
        this.menuPosition = this.getPosition(e);

        var menuWidth = this.menu.offsetWidth ;
        var menuHeight = this.menu.offsetHeight ;

        // var windowWidth = window.innerWidth;
        // var windowHeight = window.innerHeight;

        var left = this.menuPosition.x - menuWidth/2;
        var top = this.menuPosition.y - menuHeight/2;

        if (this.menuPosition.x < menuWidth/2) {
            left = 0;
        }

        if (this.menuPosition.y < menuHeight/2) {
            top = 0;
        }

        this.menu.style.left = left + "px";
        this.menu.style.top = top + "px";
    };

    PieContextMenu.prototype.containsClass = function (e, className) {
        var el = e.srcElement || e.target;

        if (el.classList.contains(className)) {
            return el;
        } else {
            while (el = el.parentNode) {
                if (el.classList && el.classList.contains(className)) {
                    return el;
                }
            }
        }

        return false;
    };

    PieContextMenu.prototype.getPosition = function (e) {
        var posx = 0;
        var posy = 0;

        if (!e) var e = window.event;

        if (e.pageX || e.pageY) {
            posx = e.pageX;
            posy = e.pageY;
        } else if (e.clientX || e.clientY) {
            posx = e.clientX + document.body.scrollLeft +
                document.documentElement.scrollLeft;
            posy = e.clientY + document.body.scrollTop +
                document.documentElement.scrollTop;
        }

        return {
            x: posx,
            y: posy
        };
    };

    PieContextMenu.prototype.draw = function () {

        PieContextMenu.setCenterCircle(this.center, this.radius, this.menuSize);
        for (var i=0; i < this.numberOfButton; i++) {
            this.createMenuButton(this.buttons[i]);
        }
    };

    PieContextMenu.prototype.createMenuButton = function (button) {
        var index = button.index;
        var text = button.text;
        var icon = button.icon;

        var radius = this.radius;
        var stroke_width = this.stroke_width;
        var font_size = this.font_size;
        var nob = this.numberOfButton;

        var menu_button_G;
        var menu_button;
        var button_title;
        var button_icon;
        var g_id;
        if (this.buttons[index].isNew) {
            menu_button_G = document.createElementNS(SVG_NS, "g");
            menu_button = document.createElementNS(SVG_NS, "circle");
            button_title = document.createElementNS(SVG_NS, "text");
            button_icon = document.createElementNS(SVG_NS, "text");

            g_id = this.menu_id + "_button_" + (index+1);

            menu_button_G.appendChild(menu_button);
            menu_button_G.appendChild(button_title);
            menu_button_G.appendChild(button_icon);

            this.menu_svg.appendChild(menu_button_G);
            button.element = menu_button_G;

            this.buttons[index].isNew = false;
        } else {
            menu_button_G = this.buttons[index].element;
            menu_button = this.buttons[index].element.childNodes[0];
            button_title = this.buttons[index].element.childNodes[1];
            button_icon = this.buttons[index].element.childNodes[2];

            g_id = this.buttons[index].element.id;
        }

        PieContextMenu.setMenuButtonG(menu_button_G, g_id, this.menuSize);

        PieContextMenu.setMenuButton(menu_button, radius, stroke_width, index, nob);
        PieContextMenu.setMenuTitle(button_title, text, font_size);
        PieContextMenu.setMenuIcon(button_icon, icon, radius, index, nob, font_size);
    };

    PieContextMenu.prototype.resize = function (newSize) {
        this.menuSize = newSize;
        this.reset();
    };

    PieContextMenu.prototype.addButton = function (text, icon) {
        this.buttons[this.numberOfButton] =
            new PieContextMenu.Button(this.numberOfButton, text, icon);
        this.numberOfButton++;
        this.reset();
    };

    PieContextMenu.Button.prototype.changeText = function (newText) {
        this.text = newText;
        this.element.childNodes[1].textContent = this.text;
    };

    PieContextMenu.Button.prototype.changeIcon = function (newIcon) {
        this.icon = newIcon;
        //var cont = PieContextMenu.faviconClassToText(this.icon);
        this.element.childNodes[2].textContent = newIcon;
    };

    /* HELPER METHODS */
    PieContextMenu.setMenuButtonG = function (menu_button_G, id, size) {
        menu_button_G.setAttribute("id", id);
        menu_button_G.setAttribute("class", "pcm_group");
        menu_button_G.setAttribute("transform",
            "translate(" + size/2 + "," + size/2 + ")");
            menu_button_G.addEventListener("mouseover", function (e) {
                PieContextMenu.menu_button_mouseover(this);
            });
            menu_button_G.addEventListener("mouseout", function (e) {
                PieContextMenu.menu_button_mouseout(this);
            });
            // menu_button_G.setAttribute("onclick","deneme()");
            return menu_button_G;
    };

    PieContextMenu.setMenuButton = function (menu_button, radius, stroke_width, index, numberOfButton) {
        menu_button.setAttribute("class", "pcm_button");
        menu_button.setAttribute("r", radius);
        menu_button.setAttribute("stroke-width", stroke_width);

        var perimeter = Math.PI*2*radius;
        var size = (perimeter/numberOfButton)+(perimeter/500);
        var rot = -180+((360/numberOfButton)*index);

        menu_button.setAttribute("stroke-dasharray", size + " " + perimeter);
        menu_button.setAttribute("transform", "rotate(" + rot + ",0,0)");

        return menu_button;
    };

    PieContextMenu.setMenuTitle = function (button_title, text, font_size) {
        button_title.textContent = text;
        button_title.setAttribute("class", "pcm_title");
        button_title.setAttribute("y", "0.35em");
        button_title.setAttribute("font-size", font_size);
        button_title.setAttribute("display", "none");
        return button_title;
    };

    PieContextMenu.setMenuIcon = function (button_icon, icon, radius, index, numberOfButton, font_size) {
        //var cont = PieContextMenu.faviconClassToText(icon);
        var rot = -180+((360/numberOfButton)*index);

        var iconRot = -1*(rot+(180/numberOfButton));
        var dot = PieContextMenu.polarToCartesian(radius, iconRot);
        button_icon.textContent = icon;
        button_icon.setAttribute("class", "pcm_icon");
        button_icon.setAttribute("x", dot.x);
        button_icon.setAttribute("y", -dot.y);
        button_icon.setAttribute("dy", "0.35em");
        button_icon.setAttribute("font-size", font_size);
        return button_icon;
    };

    PieContextMenu.setCenterCircle = function (center, radius, size) {
        center.setAttribute("class", "pcm_center");
        center.setAttribute("r", radius);
        center.setAttribute("transform",
            "translate(" + size/2 + "," + size/2 + ")");
    };

    PieContextMenu.polarToCartesian = function (r, alpha) {
        var rad = alpha * (Math.PI/180);
        var dot = {
            x: r * Math.cos(rad),
            y: r * Math.sin(rad)
        };
        return dot;
    };

    PieContextMenu.faviconClassToText = function (favClass) {
        var temp_i = document.createElement("i");
        temp_i.className = favClass;
        document.body.appendChild(temp_i);
        var before = getComputedStyle(temp_i, ":before");
        var cont = before.content;
        cont = cont.substr(1);
        cont = cont.substr(0, cont.length-1);
        document.body.removeChild(temp_i);
        return cont;
    };
    /* HELPER METHOD END */

    /* EVENT FUNCTION */
    PieContextMenu.menu_button_mouseover = function (menu_button) {
        menu_button.childNodes[0].classList.add("pcm_button--hover");
        menu_button.childNodes[1].setAttribute("display", "inline");
        menu_button.childNodes[2].classList.add("pcm_icon--hover");
    };
    PieContextMenu.menu_button_mouseout = function (menu_button) {
        menu_button.childNodes[0].classList.remove("pcm_button--hover");
        menu_button.childNodes[1].setAttribute("display", "none");
        menu_button.childNodes[2].classList.remove("pcm_icon--hover");
    };

    /* EVENT FUNCTION END */

    return PieContextMenu;
}));
