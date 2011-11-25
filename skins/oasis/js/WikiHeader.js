var WikiHeader = {
	isDisplayed: false,

	settings: {
		mouseoverDelay: 300,
		mouseoutDelay: 350
	},

	init: function() {

		//Variables
		WikiHeader.nav = $("header.WikiHeader").children("nav");
		WikiHeader.navLI = WikiHeader.nav.children("ul").children("li");
		WikiHeader.subnav = WikiHeader.nav.find(".subnav");
		WikiHeader.mouseoverTimerRunning = false;

		WikiHeader.positionNav();

		//Events
		WikiHeader.navLI
			.children("a").focus(function(){
                                WikiHeader.hideNav();
				WikiHeader.showSubNav($(this).parent("li"));
			});

		if(!$().isTouchscreen()) {
			WikiHeader.navLI.hover(WikiHeader.mouseover, WikiHeader.mouseout);
		} else {
			WikiHeader.navLI.click(WikiHeader.click);
		}

		//Accessibility Events
		//Show when any inner anchors are in focus

                // IE9 focus handling fix - see BugId:5914.
                // Assume keyboard-based navigation (IE9 focus handling fix).
                var suppressOnFocus = false;

		WikiHeader.subnav.find("a")
                    // Switch to browser's default onfocus behaviour when mouse-based navigation is detected  (IE9 focus handling fix).
                    .bind('mousedown', function() { suppressOnFocus = true; })
                    // Switch back to keyboard-based navigation mode  (IE9 focus handling fix).
                    .bind('mouseup', function() { suppressOnFocus = false; })
                    // The onfocus behaviour intended only for keyboard-based navigation (IE9 focus handling fix).
                    .focus(function(event) {
                        if ( !suppressOnFocus ) {
                            WikiHeader.hideNav();
                            WikiHeader.showSubNav($(event.currentTarget).closest(".subnav").parent("li"));
                        }
                    });
		//Hide when focus out of first and last anchor
		WikiHeader.nav.children("ul").find("li:first-child a").focusout(WikiHeader.hideNav);
		WikiHeader.subnav.last().find("li:last-child a").focusout(WikiHeader.hideNav);

		//Mouse out of browser
		$(document).mouseout(function(e){
			if(WikiHeader.isDisplayed) {
				var from = e.relatedTarget || e.toElement;
				if(!from || from.nodeName == 'HTML'){
					WikiHeader.hideNav();
				}
			}
		});

		if ( (window.wgIsWikiNavMessage) && (wgAction == "edit") ) {
			$('#wpSave').hide();
		}
	},

	click: function(event) {
		var subnav = $(this).children('.subnav');
		if (subnav.length && !subnav.hasClass('opened')) {
			event.preventDefault();
			WikiHeader.hideNav();
			WikiHeader.showSubNav(event.currentTarget);
			WikiHeader.subnav.removeClass('opened');
			subnav.addClass('opened');
		};
	},

	mouseover: function(event) {

		//Hide all subnavs except for this one
		var otherSubnavs = WikiHeader.subnav.not($(this).find(".subnav"));
		if($('body').data('accessible')) {
			otherSubnavs.css("top", "-9999px");
		} else {
			otherSubnavs.hide();
		}

		//Cancel mouseoutTimer
		clearTimeout(WikiHeader.mouseoutTimer);

		if ($(event.relatedTarget).closest("#WikiHeader nav li").length == 0) {
			//Mouse is not coming from within the nav.

			//Delay before showing subnav.
			WikiHeader.mouseoverTimer = setTimeout(function() {
				WikiHeader.showSubNav(event.currentTarget);
				WikiHeader.mouseoverTimerRunning = false;
			}, WikiHeader.settings.mouseoverDelay);
			WikiHeader.mouseoverTimerRunning = true;

		} else {
			//Mouse IS coming from within the nav

			//Don't show subnavs when quickly moving mouse horizontally through wiki nav
			if (WikiHeader.mouseoverTimerRunning) {
				//Stop current timer
				clearTimeout(WikiHeader.mouseoverTimer);
				WikiHeader.mouseoverTimerRunning = false;

				//Start new timer
				WikiHeader.mouseoverTimer = setTimeout(function() {
					WikiHeader.showSubNav(event.currentTarget);
				}, WikiHeader.settings.mouseoverDelay);
				WikiHeader.mouseoverTimerRunning = true;

			} else {
				//Mouseover timer isn't running, so show subnavs immediately
				WikiHeader.showSubNav(this);
			}
		}

	},

	mouseout: function(event) {

		if ($(event.relatedTarget).closest("#WikiHeader nav li").length == 0) {
			//Mouse has exited the nav.

			//Stop mouseoverTimer
			clearTimeout(WikiHeader.mouseoverTimer);
			WikiHeader.mouseoverTimerRunning = false;

			//Start mouseoutTimer
			WikiHeader.mouseoutTimer = setTimeout(WikiHeader.hideNav, WikiHeader.settings.mouseoutDelay);

		} else {
			//Mouse is still within the nav

			//Hide nav immediately
			WikiHeader.hideNav();
		}

	},

	showSubNav: function(parent) {
		var subnav = $(parent).children('ul');

		if (subnav.exists()) {
			WikiHeader.isDisplayed = true;
			subnav.css("top", WikiHeader.navtop).show();
			$.hideAds();

			$.tracker.byStr('wikiheader/wikinav/open');
		}
	},

	hideNav: function() {
		WikiHeader.isDisplayed = false;
		//Hide subnav
		if($('body').data('accessible')) {
			WikiHeader.subnav.css("top", "-9999px");
		} else {
                    WikiHeader.subnav.css("display", "none");
		}
		$.showAds();
	},

	positionNav: function() {
		//This runs once. Sets the proper top position of the subnav. Can't be calculated earlier because custom font loading can adjust wiki nav height.
		WikiHeader.navtop = WikiHeader.nav.height();
	}
};

$(function() {
	WikiHeader.init();
});

// v2
var WikiHeaderV2 = {
	lastSubnavClicked: -1,
	isDisplayed: false,
	activeL1: null,

	log: function(msg) {
		$().log(msg, 'WikiHeaderV2');
	},

	track: function(url) {
		$.tracker.byStr('wikiheader/wikinav/' + url);
	},

	init: function(isValidator) {
		//Variables
		WikiHeaderV2.nav = $('header.WikiHeaderRestyle').children('nav');
		WikiHeaderV2.navLI = WikiHeaderV2.nav.children('ul').children('li');
		WikiHeaderV2.subnav2 = WikiHeaderV2.nav.find('.subnav-2');
		WikiHeaderV2.subnav2LI = WikiHeaderV2.subnav2.children('li');
		WikiHeaderV2.subnav3 = WikiHeaderV2.nav.find('.subnav-3');

		WikiHeaderV2.positionNav();

		//Events
		WikiHeaderV2.navLI
			.click(WikiHeaderV2.mouseclickL1)
			.children('a').focus(function(){
				WikiHeaderV2.showSubNavL2($(this).parent('li'));
			});

		WikiHeaderV2.subnav2LI
			.click(WikiHeaderV2.mouseclickL2)
			.children('a').focus(function(){
				WikiHeaderV2.hideNavL3();
				WikiHeaderV2.showSubNavL3($(this).parent('li'));
			});

		//Apply a hover state if the device is not touch enabled
		if(!$().isTouchscreen()) {
			WikiHeaderV2.navLI.hover(WikiHeaderV2.mouseoverL1, WikiHeaderV2.mouseoutL1);
			WikiHeaderV2.subnav2LI.hover(WikiHeaderV2.mouseoverL2, WikiHeaderV2.mouseoutL2);
		}

		//Accessibility Events
		//Show when any inner anchors are in focus

		// IE9 focus handling fix - see BugId:5914.
		// Assume keyboard-based navigation (IE9 focus handling fix).
		var suppressOnFocus = false;

		WikiHeaderV2.subnav3.find('a')
			// Switch to browser's default onfocus behaviour when mouse-based navigation is detected  (IE9 focus handling fix).
			.bind('mousedown', function() {suppressOnFocus = true;})
			// Switch back to keyboard-based navigation mode  (IE9 focus handling fix).
			.bind('mouseup', function() {suppressOnFocus = false;})
			// The onfocus behaviour intended only for keyboard-based navigation (IE9 focus handling fix).
			.focus(function(event) {
				if ( !suppressOnFocus ) {
					WikiHeaderV2.hideNavL3();
					WikiHeaderV2.showSubNavL3($(event.currentTarget).closest('.subnav').parent('li'));
				}
			});
		//Hide when focus out of first and last anchor
		WikiHeaderV2.subnav3.find('li:first-child a').focusout(WikiHeaderV2.hideNavL3);
		WikiHeaderV2.subnav3.last().find('li:last-child a').focusout(WikiHeaderV2.hideNavL3);

		//Mouse out of browser
		$(document).mouseout(function(e){
			if(WikiHeaderV2.isDisplayed) {
				var from = e.relatedTarget || e.toElement;
				if(!from || from.nodeName == 'HTML'){
					WikiHeaderV2.hideNavL3();
				}
			}
		});

		// remove level 2 items not fitting into one row
		if (!isValidator) {
			var itemsRemoved = 0;

			WikiHeaderV2.subnav2.each(function(i) {
				var menu = $(this),
					items = menu.children('li').reverse();

					if (i > 0 ) {
						menu.css('visibility', 'hidden').show();
					}

					// loop through each menu item and remove it if doesn't fit into the first row
					items.each(function() {
						var item = $(this),
							pos = item.position();

						if (pos.top === 0) {
							// don't check next items
							return false;
						}
						else {
							item.remove();
							itemsRemoved++;
						}
					});

					if (i > 0 ) {
						menu.css('visibility', 'visible').hide();
					}
			});

			if (itemsRemoved > 0) {
				WikiHeaderV2.log('items removed: ' + itemsRemoved);
			}
		}
	},

	mouseclickL1: function(event) {
		if( !$(this).hasClass('marked') ){
			event.preventDefault();
			$('header.WikiHeaderRestyle nav ul li ul li').removeClass('marked2');
			$('header.WikiHeaderRestyle nav ul li').removeClass('marked');
			WikiHeaderV2.hideNavL3();
			$(this).addClass('marked');

			//Hide all subnavs except for this one
			var otherSubnavs = WikiHeaderV2.subnav2.not(  );
			if( $('body').data('accessible') ) {
				otherSubnavs.css('top', '-9999px');
			} else {
				otherSubnavs.hide();
			}
			WikiHeaderV2.activeL1 = this;
			WikiHeaderV2.showSubNavL2(this);
		}

		// click tracking
		var node = $(event.target);

		if (node.is('a')) {
			var canonicalName = node.attr('data-canonical');

			if (typeof canonicalName != 'undefined') {
				WikiHeaderV2.track('lvl2/' + canonicalName);

				// handle special cases
				switch(canonicalName) {
					case 'chat':
						$.openPopup(wgArticlePath.replace('$1', 'Special:Chat'), 'wikiachat', 'ChatRail', 600, 600);
						event.preventDefault();
						break;
				}
			}
			else {
				if (node.hasClass('subnav-2a')) {
					WikiHeaderV2.track('lvl2/click');
				}
				else if (node.hasClass('subnav-3a')) {
					WikiHeaderV2.track('lvl3/click');
				}
				else {
					WikiHeaderV2.track('lvl1/click');
				}
			}
		}
	},

	mouseoverL1: function(event) {
		// this menu is already opened - don't do anything
		if (WikiHeaderV2.activeL1 === this) {
			return;
		}

		//Hide all subnavs except for this one
		$('header.WikiHeaderRestyle nav ul li').removeClass('marked');
		WikiHeaderV2.hideNavL3();
		var index = $('header.WikiHeaderRestyle nav ul li').index(this);

		$(this).addClass('marked');
		event.preventDefault();

		//Hide all subnavs except for this one
		var otherSubnavs = WikiHeaderV2.subnav2.not(  );
		if( $('body').data('accessible') ) {
			otherSubnavs.css('top', '-9999px');
		} else {
			otherSubnavs.hide();
		}
		WikiHeaderV2.activeL1 = this;
		WikiHeaderV2.showSubNavL2(this);
	},

	mouseoutL1: function(event) {
		//nothing here
	},

	mouseclickL2: function(event) {
		//Hide all subnavs except for this one
		var otherSubnavs = WikiHeaderV2.subnav3.not($(this).find('.subnav'));

		if ( $(this).find('.subnav').exists() && !$(this).hasClass( 'marked2') ){
			WikiHeaderV2.hideNavL3();
			event.preventDefault();
			$(this).addClass('marked2');
			if($('body').data('accessible')) {
				otherSubnavs.css('top', '-9999px');
			} else {
				otherSubnavs.hide();
			}
			WikiHeaderV2.showSubNavL3( event.currentTarget );
		}
	},

	mouseoverL2: function() {
		//Hide all subnavs except for this one
		var otherSubnavs = WikiHeaderV2.subnav3.not($(this).find('.subnav'));

		if ( $(this).find('.subnav').exists() ){
			$(this).addClass('marked2');
			if($('body').data('accessible')) {
				otherSubnavs.css('top', '-9999px');
			} else {
				otherSubnavs.hide();
			}

			WikiHeaderV2.showSubNavL3(this);
		}
	},

	mouseoutL2: function() {
		WikiHeaderV2.hideNavL3();
	},

	showSubNavL2: function(parent) {
		var subnav = $(parent).children('ul');

		if (subnav.exists()) {
			subnav.show();

			WikiHeaderV2.track('lvl2/open');
		}
	},

	showSubNavL3: function(parent) {
		var subnav = $(parent).children('ul');

		if (subnav.exists()) {
			$(parent).addClass('marked2');

			WikiHeaderV2.isDisplayed = true;
			subnav.css('top', WikiHeaderV2.navtop).show();
			$.hideAds();

			WikiHeaderV2.track('lvl3/open');
		}
	},

	hideNavL3: function() {
		WikiHeaderV2.isDisplayed = false;
		WikiHeaderV2.lastSubnavClicked = -1;
		$('header.WikiHeaderRestyle nav ul li ul li').removeClass('marked2');

		//Hide subnav
		if($('body').data('accessible')) {
			WikiHeaderV2.subnav3.css('top', '-9999px');
		} else {
			WikiHeaderV2.subnav3.css('display', 'none');
		}
		$.showAds();
	},

	positionNav: function() {
		//This runs once. Sets the proper top position of the subnav. Can't be calculated earlier because custom font loading can adjust wiki nav height.
		WikiHeaderV2.navtop = WikiHeaderV2.nav.height() - 7;
	},

	firstMenuValidator: function() {
		var widthLevelFirst = 0,
			returnVal = true,
			maxWidth = $('#WikiaPage').width() - 420,
			menuNodes = $('.ArticlePreview #WikiHeader > nav > ul > li');

		menuNodes.reverse().each(function() {
			var item = $(this),
				pos = item.position();

			if (pos.top === 0) {
				// don't check next items
				return false;
			}
			else {
				returnVal = false;
				WikiHeaderV2.log('menu level #1 not valid');
			}
		});

		return returnVal;
	},

	secondMenuValidator: function() {
		var widthLevelSecond = 0,
			returnVal = true,
			maxWidth = $('#WikiaPage').width() - 280,
			menuNodes = $('.ArticlePreview #WikiHeader .subnav-2');

		$.each(menuNodes, function() {
			var menu = $(this);

			menu.show();
			$.each(menu.children('li'), function() {
				widthLevelSecond += $(this).width();
			});
			menu.hide();

			if (widthLevelSecond > maxWidth) {
				returnVal = false;
				WikiHeaderV2.log('menu level #2 not valid');
			}
			widthLevelSecond = 0;

		});
		// show the first submenu
		menuNodes.eq(0).show();
		return returnVal;
	}
};

$(function() {
	WikiHeaderV2.init();

	// modify preview dialog
	if (window.wgIsWikiNavMessage) {
		// preload messages
		$.getMessages('Oasis-navigation-v2');

		// modify size of preview modal
		$(window).bind('EditPageRenderPreview', function(ev, options) {
			options.height = 300;
			options.width = ($('#WikiaPage').width() - 271) /* menu width */ + 32 /* padding */;
		});

		// setup menu in preview mode
		$(window).bind('EditPageAfterRenderPreview', function(ev, previewNode) {
			// don't style wiki nav like article content
			previewNode.removeClass('WikiaArticle');
			WikiHeaderV2.init(true);
			var firstMenuValid = WikiHeaderV2.firstMenuValidator(),
				secondMenuValid = WikiHeaderV2.secondMenuValidator(),
				menuParseError = !!previewNode.find('nav > ul').attr('data-parse-errors'),
				errorMessages = [];

			if (menuParseError) {
				errorMessages.push($.msg('oasis-navigation-v2-magic-word-validation'));
			}

			if (!firstMenuValid && !secondMenuValid) {
				errorMessages.push($.msg('oasis-navigation-v2-level12-validation'));
			}
			else if (!firstMenuValid) {
				errorMessages.push($.msg('oasis-navigation-v2-level1-validation'));
			}
			else if (!secondMenuValid) {
				errorMessages.push($.msg('oasis-navigation-v2-level2-validation'));
			}

			if (errorMessages.length > 0) {
				$('#publish').remove();
				alert(errorMessages.join("\n\n"));
			}
			previewNode.find('nav > ul a').click(function() {
				if ($(this).attr('href') == '#') return false;
			});
		});

		$('#wpPreview').parent().removeClass('secondary');
		$('#EditPageRail .module_page_controls .module_content').append(
			'<div class="preview-validator-desc">' + $.msg('oasis-navigation-v2-validation-caption') + '</div>'
		);
	}
});
