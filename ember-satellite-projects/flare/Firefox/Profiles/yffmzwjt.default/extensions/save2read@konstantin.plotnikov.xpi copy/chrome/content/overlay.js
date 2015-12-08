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

s2rGlobal.s2rOverlay =
{
    onLoad : function()
    {
        // initialization code
        this.initialized = true;
        this.strings = document.getElementById("save2read-strings");

        var prefSrvc = s2rGlobal.s2rServices.preferences();
        var preference = "extensions.save2read.firstRun";
        var hit = false;
        if(!prefSrvc.prefHasUserValue(preference))
        {
            hit = true;
        }
        else if(prefSrvc.getCharPref(preference) != "no")
        {
            hit = true;
        }
        if(hit)
        {
            var navBar = document.getElementById("nav-bar");
            var currentset = navBar.currentSet;
            var button_names = [
                                "save2read-toolbar-button-sidebar",
//                                 "save2read-toolbar-button-panel"
                                ];
            for(var i in button_names)
            {
                if(currentset.indexOf(button_names[i]) == -1)
                {
                    currentset = currentset + "," + button_names[i];
                }
            }
            navBar.setAttribute("currentset",currentset);
            navBar.currentSet = currentset;
            document.persist("nav-bar","currentset");
            prefSrvc.setCharPref(preference, "no");
        }
    },

    onToolbarButtonCommand : function(e)
    {
        toggleSidebar('viewSidebar_save2read');
    },

    openItemCommand : function()
    {
        switch(s2rGlobal.s2rPref.getReadButton())
        {
        case "oldest":
            var bookmarksList = s2rGlobal.s2rList.getList("time_asc");
            var cont = bookmarksList.root;
            cont.containerOpen = true;
            var uri = cont.getChild(cont.childCount - 1).uri;
            break;
        case "newest":
            var bookmarksList = s2rGlobal.s2rList.getList("time_asc");
            var cont = bookmarksList.root;
            cont.containerOpen = true;
            var uri = cont.getChild(0).uri;
            break;
        case "first":
            var bookmarksList = s2rGlobal.s2rList.getList(s2rGlobal.s2rPref.getSorting());
            var cont = bookmarksList.root;
            cont.containerOpen = true;
            var uri = cont.getChild(0).uri;
            break;
        case "last":
            var bookmarksList = s2rGlobal.s2rList.getList(s2rGlobal.s2rPref.getSorting());
            var cont = bookmarksList.root;
            cont.containerOpen = true;
            var uri = cont.getChild(cont.childCount - 1).uri;
            break;
        case "random":
            var bookmarksList = s2rGlobal.s2rList.getList(s2rGlobal.s2rPref.getSorting());
            var cont = bookmarksList.root;
            cont.containerOpen = true;
            var uri = cont.getChild(Math.floor(Math.random()*cont.childCount)).uri;
            break;
        }

        cont.containerOpen = false;

        if(s2rGlobal.s2rPref.getOpenTabForLeftClick())
        {
            s2rGlobal.s2rList.mainWindow.gBrowser.selectedTab = s2rGlobal.s2rList.mainWindow.gBrowser.addTab(uri);
        }
        else
        {
            s2rGlobal.s2rList.mainWindow.gBrowser.contentDocument.location.href = uri;
        }

        if(s2rGlobal.s2rPref.getRemoveOnReadButton())
        {
            s2rGlobal.s2rGeneral.bookmarks_removeBookmark(uri, s2rGlobal.s2rPref.getFolderId());
        }
    },

    onUrlBarAddClick : function(event)
    {
        var contentDocument = window.getBrowser().contentDocument;
        var uri = String(contentDocument.location.href);
        var title = String(contentDocument.title);
        var itemId = s2rGlobal.s2rGeneral.bookmarks_create(title, uri, s2rGlobal.s2rPref.getFolderId());
        var autoTags = s2rGlobal.s2rPref.getAutoTags();
        if(autoTags != "")
        {
            s2rGlobal.s2rGeneral.tags_setTags(uri, autoTags, false);
        }
        if(event.button == 2) // checking if right click happened
        {
            s2rGlobal.s2rList.editDetailsPanel.panel.hidePopup();
            var title = s2rGlobal.s2rServices.bookmarks().getItemTitle(itemId);
            s2rGlobal.s2rList.editDetailsPanel.uri = uri;
            s2rGlobal.s2rList.editDetailsPanel.itemId = itemId;
            s2rGlobal.s2rList.editDetailsPanel.header.value = (title != "") ? title : uri;
            var nsiuri = s2rGlobal.s2rGeneral.makeURI(uri);
            s2rGlobal.s2rList.editDetailsPanel.tags.value = (nsiuri == null) ? "" : s2rGlobal.s2rServices.tags().getTagsForURI(nsiuri, {}).join(", ");
            s2rGlobal.s2rList.editDetailsPanel.description.value = s2rGlobal.s2rGeneral.bookmarks_getDescription(itemId);
            s2rGlobal.s2rList.editDetailsPanel.panel.openPopup(document.getElementById("urlbar-icons"), "after_start");
            s2rGlobal.s2rList.editDetailsPanel.causedOnBookmarkAdd = true;
        }
        else
        {
            if(s2rGlobal.s2rPref.getClosePageOnSave())
            {
                gBrowser.removeCurrentTab();
            }
        }
    },

    onUrlBarRemoveClick : function()
    {
        var uri = String(window.getBrowser().contentDocument.location.href);
        s2rGlobal.s2rGeneral.bookmarks_removeBookmark(uri, s2rGlobal.s2rPref.getFolderId());
    },

    onContextAddClick : function()
    {
        if(gContextMenu.onLink)
        {
            var uri = gContextMenu.linkURL;
            var title = gContextMenu.target.textContent.replace(/^\s*\n/g,"").split("\n")[0].replace(/^\s+|\s+$/g, "");
            if(title == "")
                title = gContextMenu.linkURL;
            var itemId = s2rGlobal.s2rGeneral.bookmarks_create(title, uri, s2rGlobal.s2rPref.getFolderId());
            var autoTags = s2rGlobal.s2rPref.getAutoTags();
            if(autoTags != "")
            {
                s2rGlobal.s2rGeneral.tags_setTags(uri, autoTags, false);
            }
            s2rGlobal.s2rGeneral.bookmarks_setDescription(itemId, gContextMenu.target.textContent.replace(/^\s*\n|^\s+|\s+$/gm,""));
        }
    },

    onContextRemoveClick : function()
    {
        if(gContextMenu.onLink)
        {
            var uri = gContextMenu.linkURL;
            s2rGlobal.s2rGeneral.bookmarks_removeBookmark(uri, s2rGlobal.s2rPref.getFolderId());
        }
    },

    toggleBookmark : function(event)
    {
        var loc = String(window.getBrowser().contentDocument.location.href);
        if(s2rGlobal.s2rGeneral.bookmarks_checkURI(loc, s2rGlobal.s2rPref.getFolderId()))
        {
            s2rGlobal.s2rOverlay.onUrlBarRemoveClick();
        }
        else
        {
            s2rGlobal.s2rOverlay.onUrlBarAddClick(event);
        }
    },

    bookmarkDetailsClosed : function()
    {
        s2rGlobal.s2rList.bookmarkDetailsClosed();
        if(s2rGlobal.s2rList.editDetailsPanel.causedOnBookmarkAdd && s2rGlobal.s2rPref.getClosePageOnSave())
        {
            gBrowser.removeCurrentTab();
        }
        s2rGlobal.s2rList.editDetailsPanel.causedOnBookmarkAdd = false;
    },

    showPanel : function()
    {
        var panel = document.getElementById("s2r_panel");
        var button = document.getElementById("save2read-toolbar-button-panel");
//         panel.position = "at_pointer";
        panel.openPopup(button, "after_start");
//         button.click();
    }
};

window.addEventListener("load", s2rGlobal.s2rOverlay.onLoad, false);
