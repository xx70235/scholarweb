/* global jQuery */

/**
 * Initialize a new tree for the media modal.
 */
window.rml.hooks.register("newModal", function(filter, $) {
    var menu = $(this).find(".media-menu"),
        container,
        containerID = $(this).parent().attr("id");
        
    if (!menu.data("rml")) {
        menu.append('<div class="separator"></div>');
        menu.data("rml", true);
    }else{
        menu.find(".aio-tree").remove();
    }
    $(this).addClass("rml-media-modal");
    $(this).find(".media-frame.hide-menu").removeClass("hide-menu"); // Never hide the medie menu frame
    
    // If it is the "Edit gallery" modal then create no tree
    var mediaButtonReverse = $(this).find(".media-button-reverse");
    if (mediaButtonReverse.is(":visible")) {
        return;
    }
    
    // Add tree container to left menu
    container = $(".rml-container.rml-dummy").clone().appendTo(menu);
    container.removeClass("rml-dummy").addClass("rml-no-dummy");
    
    // Add modal library relevant options
    var aioSettings = $.extend(true, {}, window.rml.defaultAioSettings, {
        container: {
            isListMode: false,
            isModalMode: [ menu, $(this) ],
            listMode: "grid",
            customSelectToChange: "#" + containerID + " .attachment-filters-rml",
            isResizable: false,
            isSticky: false,
            isResizable: false,
            isWordpressModal: true,
            theme: "wordpress wordpress-fixed"
        },
        movement: {
            selector: "#" + containerID + " ul.attachments > li"
        }
    });
    
    // Apply filters to the allInOneTree
    window.rml.hooks.call("aioSettings", aioSettings);
    window.rml.hooks.call("aioSettings/grid", aioSettings);
    
    // Apply filters to the allInOneTree modal mode
    window.rml.hooks.call("aioSettings/modal", aioSettings);
    window.rml.hooks.call("aioSettings/modal/grid", aioSettings);
    
    // Reset content loader and create the tree
    window.rml.library.treeContentAjaxLoader = null;
    window.rml.library.treeContentLoader = jQuery.Deferred();
    container.allInOneTree(aioSettings);
});

/**
 * Set an interval, which searches for new modal selects.
 * 
 * @hook newModal
 * @see window.rml.library::initializeToolbar
 */
window.rml.hooks.register("general", function($) {
    setInterval(function() {
        // Search new modals with attachments browser
        $(".media-modal .attachments-browser").each(function() {
            if ($(this).is(":visible") && !$(this).data("initialized")) {
                $(this).data("initialized", true);
                window.rml.library.initializeTreeContentLoader();
                window.rml.hooks.call("newModal", $(this), $(this).parents(".media-modal"));
            }
        });
        
        // Search for rml shortcut info containers
        $(".rml-shortcut-info:visible:not(.rml-shortcut-info-init)").each(function() {
            window.rml.library.showShortcutInfo($(this));
        });
    }, 500);
});

/**
 * When we are in a modal window and change the tabs, please reinit the active
 * folder to the current selected <select> value.
 */
window.rml.hooks.register("afterInit/modal", function() {
    var $ = jQuery, container = $(this),
        backbone = window.rml.library.getBackboneOfAIO(container);
    if (backbone.browser) {
        var select = backbone.view.$(".attachment-filters-rml-loaded");
        if (select.size() > 0) {
            var selected = select.val();
            if (selected !== "all") {
                container.allInOneTree("active", selected, true);
            }
        }
    }
});