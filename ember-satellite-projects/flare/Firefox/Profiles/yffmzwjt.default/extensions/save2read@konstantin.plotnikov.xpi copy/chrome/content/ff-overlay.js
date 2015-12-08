/*
    Add-on for firefox for quick bookmarking.
    Copyright (C) 2010  Konstantin Plotnikov

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

if(!s2rGlobal) var s2rGlobal = {};

s2rGlobal.s2rOverlay.bookmarksNumber = 0;

s2rGlobal.s2rOverlay.changeUrlButtons = function(show_any, show_remove)
{
    var addIcon = document.getElementById("s2r_urlBarAddIcon");
    var removeIcon = document.getElementById("s2r_urlBarRemoveIcon");
    if(show_any)
    {
        if(show_remove)
        {
            addIcon.setAttribute("hidden", "true");
            removeIcon.setAttribute("hidden", "false");
        }
        else
        {
            removeIcon.setAttribute("hidden", "true");
            addIcon.setAttribute("hidden", "false");
        }
    }
    else
    {
        addIcon.setAttribute("hidden", "true");
        removeIcon.setAttribute("hidden", "true");
    }
};

s2rGlobal.s2rOverlay.onLocationChanged = function(uri)
{
    if(uri instanceof Components.interfaces.nsIURI)
    {
        if(uri.spec == "about:blank" || uri.spec == "about:newtab")
        {
            s2rGlobal.s2rOverlay.changeUrlButtons(false, false);
            return;
        }
    }
    s2rGlobal.s2rOverlay.changeUrlButtons(true, s2rGlobal.s2rGeneral.bookmarks_checkURI(uri, s2rGlobal.s2rPref.getFolderId()));
};

s2rGlobal.s2rOverlay.onPageLoaded = function(event)
{
    if (event.originalTarget instanceof HTMLDocument)
    {
        var win = event.originalTarget.defaultView;
        if (win.frameElement)
        {
            return;
        }
    }

    var loc = String(window.getBrowser().contentDocument.location.href);
    if(loc != "about:blank" && loc != "about:newtab" && loc != "" && loc != null)
        s2rGlobal.s2rOverlay.changeUrlButtons(true, s2rGlobal.s2rGeneral.bookmarks_checkURI(loc, s2rGlobal.s2rPref.getFolderId()));
    else
        s2rGlobal.s2rOverlay.changeUrlButtons(false, false);
};

s2rGlobal.s2rOverlay.onBookmarksChange = function(nodeID)
{
    var loc = String(window.getBrowser().contentDocument.location.href);
    s2rGlobal.s2rOverlay.changeUrlButtons(true, s2rGlobal.s2rGeneral.bookmarks_checkURI(loc, s2rGlobal.s2rPref.getFolderId()));
};

s2rGlobal.s2rOverlay.createBookmarksObserver = function()
{
    s2rGlobal.s2rOverlay.bookmarksObserver = new s2rGlobal.s2rGeneral.bookmarksServiceObserver();
    s2rGlobal.s2rOverlay.bookmarksObserver.register();
    s2rGlobal.s2rOverlay.bookmarksObserver.itemAdded =
        function(nodeID)
        {
            s2rGlobal.s2rOverlay.onBookmarksChange(nodeID);
            s2rGlobal.s2rOverlay.bookmarksNumber++;
            s2rGlobal.s2rOverlay.setCounters();
        };
    s2rGlobal.s2rOverlay.bookmarksObserver.itemRemoved =
        function(nodeID)
        {
            s2rGlobal.s2rOverlay.onBookmarksChange(nodeID);
            s2rGlobal.s2rOverlay.bookmarksNumber--;
            s2rGlobal.s2rOverlay.setCounters();
        };
};

s2rGlobal.s2rOverlay.deleteBookmarksObserver = function()
{
    s2rGlobal.s2rOverlay.bookmarksObserver.unregister();
};

// Preferencies observer
s2rGlobal.s2rOverlay.observe = function(aSubject, aTopic, aData)
{
    if ("nsPref:changed" == aTopic)
    {
        switch(aData)
        {
        case s2rGlobal.s2rPref.preferenceFolderName:
            s2rGlobal.s2rPref.resetFolderIdCache();
//            s2rGlobal.s2rOverlay.deleteBookmarksObserver();
//            s2rGlobal.s2rOverlay.createBookmarksObserver();
            var loc = String(window.getBrowser().contentDocument.location.href);
            s2rGlobal.s2rOverlay.changeUrlButtons(true, s2rGlobal.s2rGeneral.bookmarks_checkURI(loc, s2rGlobal.s2rPref.getFolderId()));
            s2rGlobal.s2rOverlay.initBookmarksCounter();
            break;
        case s2rGlobal.s2rPref.preferenceCounterOnSidebarBtn:
            document.getElementById("save2read-toolbar-button-sidebar").setAttribute("hidecounter", !s2rGlobal.s2rPref.getCounterOnSidebarBtn());
            break;
        case s2rGlobal.s2rPref.preferenceCounterOnPanelBtn:
            document.getElementById("save2read-toolbar-button-panel").setAttribute("hidecounter", !s2rGlobal.s2rPref.getCounterOnPanelBtn());
            break;
        case s2rGlobal.s2rPref.preferenceCounterOnOpenBtn:
            document.getElementById("save2read-toolbar-button-open").setAttribute("hidecounter", !s2rGlobal.s2rPref.getCounterOnOpenBtn());
            break;
//         case s2rGlobal.s2rPref.preferenceSidebarShortcut:
//             s2rGlobal.s2rOverlay.setShortcut(document.getElementById("s2r_key_sidebar"), s2rGlobal.s2rPref.getSidebarShortcut());
//             break;
        default:
            break;
        }
    }
};

s2rGlobal.s2rOverlay.setShortcut = function(element, setting)
{
    var modifiers = setting.split(" + ");
    var key = modifiers.pop();
    modifiers = modifiers.join(" ").toLowerCase();
    element.setAttribute("modifiers", modifiers);
    if(key.indexOf("VK_") == 0)
    {
        element.setAttribute("keyCode", key);
        element.removeAttribute("key");
    }
    else
    {
        element.setAttribute("key", key);
        element.removeAttribute("keyCode");
    }
};

s2rGlobal.s2rOverlay.setCounters = function()
{
    var count = '' + s2rGlobal.s2rOverlay.bookmarksNumber;
    var list = ["save2read-toolbar-button-sidebar", "save2read-toolbar-button-panel", "save2read-toolbar-button-open"];
    for(element in list)
    {
        var btn = document.getElementById(list[element]);
        if(btn != null)
            btn.setAttribute("unread", count);
    }
};

s2rGlobal.s2rOverlay.initBookmarksCounter = function()
{
    var bookmarksList = s2rGlobal.s2rList.getList(s2rGlobal.s2rPref.getSorting());
    var cont = bookmarksList.root;
    cont.containerOpen = true;
    s2rGlobal.s2rOverlay.bookmarksNumber = cont.childCount;
    cont.containerOpen = false;

    s2rGlobal.s2rOverlay.setCounters();
};

s2rGlobal.s2rOverlay.onContextMenuShowing = function(event)
{
    if(gContextMenu.onLink)
    {
        var bookmarked = s2rGlobal.s2rGeneral.bookmarks_checkURI(gContextMenu.linkURL, s2rGlobal.s2rPref.getFolderId());
        document.getElementById("s2r_context_menu_item_add").hidden = bookmarked;
        document.getElementById("s2r_context_menu_item_remove").hidden = !bookmarked;
    }
    else
    {
        document.getElementById("s2r_context_menu_item_add").hidden = true;
        document.getElementById("s2r_context_menu_item_remove").hidden = true;
    }
};

s2rGlobal.s2rOverlay.onLoad = function()
{
    // Add a callback to be run every time a document loads.
    // note that this includes frames/iframes within the document
    gBrowser.addEventListener("load", s2rGlobal.s2rOverlay.onPageLoaded, true);

    // Create bookmarks observer
    s2rGlobal.s2rOverlay.createBookmarksObserver();

    // Create observer for folder name changing
    s2rGlobal.s2rPref.addFolderNameObserver(s2rGlobal.s2rOverlay);

    // Set counters visibility and observers
    s2rGlobal.s2rOverlay.initBookmarksCounter();
    var btn;
    btn = document.getElementById("save2read-toolbar-button-sidebar");
    if(btn != null)
        btn.setAttribute("hidecounter", !s2rGlobal.s2rPref.getCounterOnSidebarBtn());
    btn = document.getElementById("save2read-toolbar-button-panel");
    if(btn != null)
        btn.setAttribute("hidecounter", !s2rGlobal.s2rPref.getCounterOnPanelBtn());
    btn = document.getElementById("save2read-toolbar-button-open");
    if(btn != null)
        btn.setAttribute("hidecounter", !s2rGlobal.s2rPref.getCounterOnOpenBtn());
    s2rGlobal.s2rPref.addCounterOnSidebarBtnObserver(s2rGlobal.s2rOverlay);
    s2rGlobal.s2rPref.addCounterOnPanelBtnObserver(s2rGlobal.s2rOverlay);
    s2rGlobal.s2rPref.addCounterOnOpenBtnObserver(s2rGlobal.s2rOverlay);

    // Set hotkeys
    s2rGlobal.s2rOverlay.setShortcut(document.getElementById("s2r_key_bookmark"), s2rGlobal.s2rPref.getBookmarkShortcut());
    s2rGlobal.s2rOverlay.setShortcut(document.getElementById("s2r_key_sidebar"), s2rGlobal.s2rPref.getSidebarShortcut());
    s2rGlobal.s2rOverlay.setShortcut(document.getElementById("s2r_key_panel"), s2rGlobal.s2rPref.getPanelShortcut());
    s2rGlobal.s2rOverlay.setShortcut(document.getElementById("s2r_key_open"), s2rGlobal.s2rPref.getOpenShortcut());
//     s2rGlobal.s2rPref.addShortcutObserver(s2rGlobal.s2rOverlay);

    document.getElementById("contentAreaContextMenu")
            .addEventListener("popupshowing", s2rGlobal.s2rOverlay.onContextMenuShowing, false);
};

s2rGlobal.s2rOverlay.onUnload = function()
{
//    When no longer needed
    gBrowser.removeEventListener("load", s2rGlobal.s2rOverlay.onPageLoaded, true);


    s2rGlobal.s2rOverlay.deleteBookmarksObserver();
    s2rGlobal.s2rPref.removeFolderNameObserver(s2rGlobal.s2rOverlay);
    s2rGlobal.s2rPref.removeCounterOnSidebarBtnObserver(s2rGlobal.s2rOverlay);
    s2rGlobal.s2rPref.removeCounterOnPanelBtnObserver(s2rGlobal.s2rOverlay);
    s2rGlobal.s2rPref.removeCounterOnOpenBtnObserver(s2rGlobal.s2rOverlay);
//                             s2rGlobal.s2rPref.removeShortcutObserver(s2rGlobal.s2rOverlay);

    document.getElementById("contentAreaContextMenu")
            .removeEventListener("popupshowing", s2rGlobal.s2rOverlay.onContextMenuShowing, false);
};

// do not try to add a callback until the browser window has
// been initialised. We add a callback to the tabbed browser
// when the browser's window gets loaded.
window.addEventListener("load", s2rGlobal.s2rOverlay.onLoad, false);

window.addEventListener("unload", s2rGlobal.s2rOverlay.onUnload, false);

