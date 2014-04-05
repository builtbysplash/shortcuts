var ShortcutsElement = Garnish.Base.extend({

    title: null,
    uri: null,
    params: null,

    init: function(title, uri) {
        this.title = title;
        this.uri = this.getUriPart(uri);
        this.params = this.getUrlParameters(uri);
    },

    getFullUrl: function() {
        var url = Craft.getCpUrl(this.uri, this.params);
        // Fixes bug in Craft.getCpUrl
        return url.replace('admin//', 'admin/');
    },

    /*
     * Google getUrlParameters
     */
    getUrlParameters: function(uri) {
        var cutPos = uri.indexOf('?');
        var queryString = '';
        if (cutPos != -1)
        {
            queryString = uri.substring(cutPos);
            var qs = (function(a) {
                if (a == "") return {};
                var b = {};
                for (var i = 0; i < a.length; ++i)
                {
                    var p=a[i].split('=');
                    if (p.length != 2) continue;
                    b[p[0]] = decodeURIComponent(p[1].replace(/\+/g, " "));
                }
                return b;
            })(queryString.substring(1).split('&'));
            return qs;
        }
    },

    getUriPart: function(uri) {
        var cutPos = uri.indexOf('?cut');
        if (cutPos != -1)
        {
            return uri.substring(0, cutPos);
        }
        return uri;
    }

});

var ShortcutsDropdown = Garnish.Base.extend({

    title: null,
    elements: [],

    init: function(title, elements) {
        this.title = title;
        this.elements = elements;
    }

});

var CachedShortcutsBar = Garnish.Base.extend({

    $bar: null,
    html: '',

    init: function(html) {
        this.html = html;
    },

    render: function() {
        this.$bar = $('<div id="shortcuts-bar" />').insertAfter($('header#header'));
        this.$bar.html(this.html);

        var url = window.location.href.replace(Craft.baseCpUrl + '/', '');
        var elements = $('#shortcuts-bar ul li a').each(function() {
            if ($(this).attr('href').replace(Craft.baseCpUrl + '/', '') == url) {
                $(this).addClass('active');
            }
        });

        // Render quick add button
        var quickAdd = new QuickAddButton();
        quickAdd.activate();
    }

});

var QuickAddButton = Garnish.Base.extend({

    $parentElement: null,
    $button: null,

    newUrl: null,

    init: function(parentElement) {
        this.$parentElement = parentElement || null;
        this.newUrl = window.location.href.replace(Craft.baseCpUrl + '/', '');
        this.$button = $('#shortcuts-add');
        if (this.$button.length == 0) {
            this.$button = $('<li><div id="shortcuts-add" class="btn add icon"></div></li>');
        }
    },

    render: function() {
        this.$button.appendTo(this.$parentElement);
        this.activate();
    },

    activate: function() {
        var newUrl = this.newUrl;
        this.$button.click(function() {
            $nav = $('.sidebar nav');
            if ($nav) {
                $selected = $nav.find('li a.sel');
                if ($selected.attr('data-key')) {
                    cutKey = $selected.attr('data-key');
                    cutPos = newUrl.indexOf('?cut');
                    if (cutPos == -1)
                    {
                        cutPos = newUrl.indexOf('&cut');
                    }
                    if (cutPos != -1 ) {
                        newUrl = newUrl.substring(0, cutPos);
                    }
                }
                else
                {
                    cutKey = '';
                }
            }
            var params = {
                uri: newUrl,
                cut: cutKey
            };
            var url = Craft.getCpUrl('shortcuts/new', params);
            window.location.href = url.replace('admin//', 'admin/');
        });
    }

});

var ShortcutsBar = Garnish.Base.extend({

    $bar: null,
    $wrapper: null,
    $elements: null,
    $newEntryButton: null,

    elements: [],
    sections: [],

    init: function(elements, sections) {
        this.sections = JSON.parse(sections);
        var parsedElements = JSON.parse(elements);
        for (var title in parsedElements) {
           if (parsedElements.hasOwnProperty(title)) {
                if (typeof parsedElements[title] == "object") {
                    var subElements = [];
                    for (var subElementTitle in parsedElements[title]) {
                        if (parsedElements[title].hasOwnProperty(subElementTitle)) {
                            subElements.push(new ShortcutsElement(subElementTitle, parsedElements[title][subElementTitle]));
                        }
                    }
                    this.elements.push(new ShortcutsDropdown(title, subElements));
                }
                else {
                    this.elements.push(new ShortcutsElement(title, parsedElements[title]));
                }
           }
        }
    },

    renderEntryButton: function() {
        var $buttons = $('<div />').appendTo(this.$wrapper);
        if (this.sections.length > 1) {
            $('<div id="newEntryButton" class="btn submit menubtn add icon">New Entry</div>').appendTo($buttons);
            var $menu = $('<div class="menu" />').appendTo($buttons);
            var $ul = $('<ul />').appendTo($menu);
            for (var i = 0; i < this.sections.length; i++) {
                $('<li><a href="' + Craft.getUrl('entries/' + this.sections[i].handle + '/new') + '">' + this.sections[i].name + '</a></li>').appendTo($ul);
            }
        }
        else {
            $('<a id="newEntryButton" class="btn submit add icon" href="' + Craft.getUrl('entries/' + this.sections[0].handle + '/new') + '">New Entry</a>').appendTo($buttons);
        }
    },

    renderShortcuts: function() {
        this.$bar = $('<div id="shortcuts-bar" />').insertAfter($('header#header'));
        this.$wrapper = $('<div class="centered" />').appendTo(this.$bar);
        this.$elements = $('<ul />').appendTo(this.$wrapper);
        var currentUrl = window.location.href;

        for (var i = 0; i < this.elements.length; i++) {
            var el = this.elements[i];
            if (el instanceof ShortcutsElement) {
                if (currentUrl.indexOf(el.uri) == -1) {
                    $('<li><a href="' + el.getFullUrl() + '">' + el.title + '</a></li>').appendTo(this.$elements);
                } else {
                    $('<li><a class="active" href="' + el.getFullUrl() + '">' + el.title + '</a></li>').appendTo(this.$elements);
                }
            }
            else if (el instanceof ShortcutsDropdown) {
                var $dropDownWrapper =$('<div />').appendTo($('<li />').appendTo(this.$elements));
                var $link = $('<a class="menubtn">' + el.title + '</a>').appendTo($dropDownWrapper);
                var $menu = $('<div class="menu" />').appendTo($dropDownWrapper);
                var $ul = $('<ul />').appendTo($menu);
                for (var j = 0; j < el.elements.length; j++) {
                    if (currentUrl.indexOf(el.elements[j].uri) !== -1) {
                        $link.addClass('active');
                    }
                    $('<li><a href="' + el.elements[j].getFullUrl() + '">' + el.elements[j].title + '</a></li>').appendTo($ul);
                }
            }
        };
    },

    render: function() {
        if ($('#shortcuts-bar').length == 0) {
            // Render shortcuts
            this.renderShortcuts();

            // Render quick add button
            var quickAdd = new QuickAddButton(this.$elements);
            quickAdd.render();

            // Render add entry button
            if (this.sections.length > 0) {
                this.renderEntryButton();
            }

            // Cache the bar
            var html = $('#shortcuts-bar').html();
            if (html != '')
            {
                var data = {
                    html: html
                }
                Craft.postActionRequest('shortcuts/saveToCache', data, function(response) {
                    // Sucessfully saved to cache
                });
            }

        }
    }

});
