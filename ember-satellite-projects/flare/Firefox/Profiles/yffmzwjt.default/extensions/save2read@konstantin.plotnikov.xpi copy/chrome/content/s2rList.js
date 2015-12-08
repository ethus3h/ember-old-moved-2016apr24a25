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

s2rGlobal.s2rList =
{
    mainWindow : null,
    filterString : "",
    s2r_listNode : null,
    removeList : {},

    createNode : function(showType, itemId, icon, uri, title, dateAdded)
    {
        uri = s2rGlobal.s2rGeneral.makeURI(uri);
        if(uri == null)
            return null;

        title = (title != "" ? title : uri.spec);

        var rowNode = document.createElement("row");
        var faviconNode = document.createElement("image");
        var nameNode = document.createElement("label");
        var timeNode = document.createElement("label");
        var statusNode = document.createElement("image");

        rowNode.setAttribute("id", s2rGlobal.s2rGeneral.genNodeName(itemId));
        rowNode.setAttribute("class", "s2r-row");
        if(s2rGlobal.s2rPref.getShowTooltip())
        {
            var tooltiptext = title + "\n\n" + uri.spec;
            rowNode.setAttribute("tooltiptext", tooltiptext);
        }

        if(icon == "" || icon == null)
        {
//             favicon = s2rGlobal.s2rServices.favicon().getFaviconLinkForIcon(uri).spec;
            var favicon = "moz-anno:favicon:" + uri.spec;
        }
        else
        {
            var favicon = icon;
        }
        faviconNode.setAttribute("src", favicon);
        faviconNode.setAttribute("class", "s2r-row-favicon");

        nameNode.setAttribute("class", "s2r-list-item");
        nameNode.setAttribute("value", title);
        nameNode.setAttribute("crop", "end");
        nameNode.onclick        = function(event) { s2rGlobal.s2rList.openURI(event, uri.spec, itemId); };
        nameNode.onmouseover    = function(event) { s2rGlobal.s2rList.statusbar.label = uri.spec; };
        nameNode.onmouseout     = function(event) { s2rGlobal.s2rList.statusbar.label = ""; };

        statusNode.setAttribute("class", "s2r-row-add-remove-button");
        if(itemId in s2rGlobal.s2rList.removeList)
        {
            statusNode.setAttribute("src", "chrome://save2read/skin/list-add.png");
            statusNode.onclick = function(event) { s2rGlobal.s2rList.cancelRemoveRequest(itemId); };
        }
        else
        {
            statusNode.setAttribute("src", "chrome://save2read/skin/list-remove.png");
            statusNode.onclick = function(event) { s2rGlobal.s2rList.requestRemoveBookmark(itemId); };
        }

        var date = new Date();
        var days = Math.round((date.getTime() / 1000 - dateAdded / 1000000) / 86400); //86400 - number of seconds in the twenty-four hours
        timeNode.setAttribute("value", (days==0)? "today" : days + " days");

        switch(s2rGlobal.s2r_showType[showType])
        {
            case s2rGlobal.s2r_showType["full"]:
                var vboxNode = document.createElement("vbox");
                var hostNode = document.createElement("label");

                if(s2rGlobal.s2rPref.getFullViewWithURL())
                {
                    var host_name = uri.spec;
                    var host_url = uri.spec;
                }
                else
                {
                    try
                    {
                        var host_name = uri.host + ((uri.port == -1) ? "" : ":" + uri.port);
                        var host_url = uri.scheme + "://" + host_name;
                    } catch (e)
                    {
                        var host_name = uri.spec;
                        var host_url = uri.spec;
                    }
                }

                hostNode.setAttribute("value", host_name);
                hostNode.setAttribute("class", "s2r-host");
                hostNode.setAttribute("crop", "end");
                hostNode.onclick        = function(event) { s2rGlobal.s2rList.openURI(event, host_url, -1); };
                hostNode.onmouseover    = function(event) { s2rGlobal.s2rList.statusbar.label = host_url; };
                hostNode.onmouseout     = function(event) { s2rGlobal.s2rList.statusbar.label = ""; };

                rowNode.appendChild(faviconNode);
                vboxNode.appendChild(nameNode);
                vboxNode.appendChild(hostNode);
                rowNode.appendChild(vboxNode);
                rowNode.appendChild(statusNode);
                rowNode.appendChild(timeNode);

                break;
            case s2rGlobal.s2r_showType["compact"]:
            default:
                rowNode.appendChild(faviconNode);
                rowNode.appendChild(nameNode);
                rowNode.appendChild(statusNode);
                rowNode.appendChild(timeNode);

                break;
        }
        return rowNode;
    },

    clearList : function(listNode)
    {
        while (listNode.firstChild)
        {
            listNode.removeChild(listNode.firstChild);
        }
    },

    fillList : function()
    {
        var show = s2rGlobal.s2rPref.getViewMode();

        if(s2rGlobal.s2rList.s2r_listNode == null)
        {
            return;
        }

        var bookmarksList = s2rGlobal.s2rList.getList(s2rGlobal.s2rPref.getSorting());
        var cont = bookmarksList.root;
        cont.containerOpen = true;

        s2rGlobal.s2rList.clearList(s2rGlobal.s2rList.s2r_listNode);

        s2rGlobal.s2rList.filterString = s2rGlobal.s2rList.filterString.toLowerCase();
        for (var i = 0; i < cont.childCount; i ++)
        {
            var node = cont.getChild(i);
            if(node.type != node.RESULT_TYPE_URI)
                continue;

            var node_nsIURI = s2rGlobal.s2rGeneral.makeURI(node.uri);
            if(node_nsIURI == null)
                continue;

            var filterHit = false;
            if(s2rGlobal.s2rList.filterString == "")
                filterHit = true;
            else
            {
                filterHit = node.title.toLowerCase().indexOf(s2rGlobal.s2rList.filterString) != -1 || node.uri.toLowerCase().indexOf(s2rGlobal.s2rList.filterString) != -1;

                if(!filterHit)
                {
                    var tags = s2rGlobal.s2rServices.tags().getTagsForURI(node_nsIURI, {});
                    for(var j = 0; j < tags.length && !filterHit; j++)
                    {
                        filterHit = tags[j].toLowerCase().indexOf(s2rGlobal.s2rList.filterString) != -1;
                    }

                    if(!filterHit)
                    {
                        filterHit = s2rGlobal.s2rGeneral.bookmarks_getDescription(node.itemId).toLowerCase().indexOf(s2rGlobal.s2rList.filterString) != -1;
                    }
                }
            }

            if(filterHit)
            {
                var newNode = s2rGlobal.s2rList.createNode(show, node.itemId, node.icon, node_nsIURI, node.title, node.dateAdded);
                if(newNode != null)
                    s2rGlobal.s2rList.s2r_listNode.appendChild(newNode);
            }
        }

        if(s2rGlobal.s2rList.s2r_listNode.firstChild)
        {
            s2rGlobal.s2rList.s2r_noBookmarksMessage.setAttribute("hidden", "true");
        }
        else
        {
            s2rGlobal.s2rList.s2r_noBookmarksMessage.setAttribute("hidden", "false");
        }

        cont.containerOpen = false;
    },

    getList : function(sort_by)
    {
        var historyService = s2rGlobal.s2rServices.history();
        var options = historyService.getNewQueryOptions();
        var query = historyService.getNewQuery();
        switch(s2rGlobal.sr2_sortingType[sort_by])
        {
            case s2rGlobal.sr2_sortingType["name_desc"]:
                options.sortingMode = options.SORT_BY_TITLE_DESCENDING;
                break;
            case s2rGlobal.sr2_sortingType["name_asc"]:
                options.sortingMode = options.SORT_BY_TITLE_ASCENDING;
                break;
            case s2rGlobal.sr2_sortingType["time_desc"]:
                options.sortingMode = options.SORT_BY_DATEADDED_ASCENDING;
                break;
            case s2rGlobal.sr2_sortingType["time_asc"]:
            default:
                options.sortingMode = options.SORT_BY_DATEADDED_DESCENDING;
                break;
        }

        query.setFolders([s2rGlobal.s2rPref.getFolderId()], 1);
        var result = historyService.executeQuery(query, options);

        return result;
    },

    requestRemoveBookmark : function(id)
    {
        var button = document.getElementById(s2rGlobal.s2rGeneral.genNodeName(id)).getElementsByClassName("s2r-row-add-remove-button")[0];
        button.setAttribute("src", "chrome://save2read/skin/list-add.png");
        button.setAttribute("onclick", "s2rGlobal.s2rList.cancelRemoveRequest(\"" + id + "\")");
        s2rGlobal.s2rList.removeList[id] = Components.classes["@mozilla.org/timer;1"]
                                                     .createInstance(Components.interfaces.nsITimer);

        s2rGlobal.s2rList.removeList[id].initWithCallback(s2rGlobal.s2rList.removeBookmarkTimeoutHandler, 3000, Components.interfaces.nsITimer.TYPE_ONE_SHOT);

    },

    removeBookmarkTimeoutHandler :
    {
        notify : function(timer)
        {
            for(var id in s2rGlobal.s2rList.removeList)
            {
                if(s2rGlobal.s2rList.removeList[id] == timer)
                {
                    delete s2rGlobal.s2rList.removeList[id];
                    s2rGlobal.s2rServices.bookmarks().removeItem(id);
                }
            }
        }
    },

    cancelRemoveRequest : function(id)
    {
        s2rGlobal.s2rList.removeList[id].cancel();
        var button = document.getElementById(s2rGlobal.s2rGeneral.genNodeName(id)).getElementsByClassName("s2r-row-add-remove-button")[0];
        button.setAttribute("onclick", "s2rGlobal.s2rList.requestRemoveBookmark(\"" + id + "\")");
        button.setAttribute("src", "chrome://save2read/skin/list-remove.png");
        delete s2rGlobal.s2rList.removeList[id];
    },

    removeAllRequested : function()
    {
        for(var id in s2rGlobal.s2rList.removeList)
        {
            s2rGlobal.s2rList.removeList[id].cancel();
            delete s2rGlobal.s2rList.removeList[id];
            s2rGlobal.s2rServices.bookmarks().removeItem(id);
        }
    },

    openURI : function(event, uri, itemId)
    {
        switch(event.button)
        {
            case 0: // Left click
                if(s2rGlobal.s2rPref.getOpenTabForLeftClick())
                {
                    s2rGlobal.s2rList.mainWindow.gBrowser.selectedTab = s2rGlobal.s2rList.mainWindow.gBrowser.addTab(uri);
                }
                else
                {
                    s2rGlobal.s2rList.mainWindow.gBrowser.contentDocument.location.href = uri;
                }
                if(s2rGlobal.s2rList.panelInitialized)
                {
                    if(s2rGlobal.s2rPref.getClosePanelOnClick())
                        s2rGlobal.s2rList.bookmarksPanel.hidePopup();
                }
                if(s2rGlobal.s2rPref.getRemoveOnLeftClick() && itemId != -1)
                {
                    s2rGlobal.s2rServices.bookmarks().removeItem(itemId);
                }
                break;
            case 1: // Middle click
//                 s2rGlobal.s2rList.mainWindow.openUILinkIn(uri, 'tab');
                s2rGlobal.s2rList.mainWindow.gBrowser.selectedTab = s2rGlobal.s2rList.mainWindow.gBrowser.addTab(uri);
                break;
            case 2: // Right click
                if(itemId != -1 && s2rGlobal.s2rPref.getUseRightButton())
                {
                    var notMarkedForRemoving = true;
                    for(var id in s2rGlobal.s2rList.removeList)
                    {
                        if(id == itemId)
                        {
                            notMarkedForRemoving = false;
                        }
                    }
                    s2rGlobal.s2rList.editDetailsPanel.panel.hidePopup();
                    if(notMarkedForRemoving)
                    {
                        var title = s2rGlobal.s2rServices.bookmarks().getItemTitle(itemId);
                        s2rGlobal.s2rList.editDetailsPanel.uri = uri;
                        s2rGlobal.s2rList.editDetailsPanel.itemId = itemId;
                        s2rGlobal.s2rList.editDetailsPanel.header.value = (title != "") ? title : uri;
                        var nsiuri = s2rGlobal.s2rGeneral.makeURI(uri);
                        s2rGlobal.s2rList.editDetailsPanel.tags.value = (nsiuri == null) ? "" : s2rGlobal.s2rServices.tags().getTagsForURI(nsiuri, {}).join(", ");
                        s2rGlobal.s2rList.editDetailsPanel.description.value = s2rGlobal.s2rGeneral.bookmarks_getDescription(itemId);
                        s2rGlobal.s2rList.editDetailsPanel.panel.openPopup(document.getElementById(s2rGlobal.s2rGeneral.genNodeName(itemId)));
                    }
                }
                break;
            dafault: // do nothing for this button
                break;
        }
    },

    addNode : function(nodeID)
    {
        var bookmarksSrvc = s2rGlobal.s2rServices.bookmarks();
        var node_nsIURI = bookmarksSrvc.getBookmarkURI(nodeID);

        var filterHit = false;
        if(s2rGlobal.s2rList.filterString == "")
            filterHit = true;
        else
        {
            filterHit = bookmarksSrvc.getItemTitle(nodeID).toLowerCase().indexOf(s2rGlobal.s2rList.filterString) != -1 || node_nsIURI.spec.toLowerCase().indexOf(s2rGlobal.s2rList.filterString) != -1;

            if(!filterHit)
            {
                var tags = s2rGlobal.s2rServices.tags().getTagsForURI(node_nsIURI, {});
                for(var j = 0; j < tags.length && !filterHit; j++)
                {
                    filterHit = tags[j].toLowerCase().indexOf(s2rGlobal.s2rList.filterString) != -1;
                }

                if(!filterHit)
                {
                    filterHit = s2rGlobal.s2rGeneral.bookmarks_getDescription(nodeID).toLowerCase().indexOf(s2rGlobal.s2rList.filterString) != -1;
                }
            }
        }

        if(filterHit)
        {
            var bookmarksList = s2rGlobal.s2rList.getList(s2rGlobal.s2rPref.getSorting());
            var cont = bookmarksList.root;
            cont.containerOpen = true;

            var index = -1;
            var index_next = -1;
            var node = null;
            for (var i = 0; i < cont.childCount; i ++)
            {
                node = cont.getChild(i);

                if(index == -1)
                {
                    if(node.itemId == nodeID)
                    {
                        index = i;
                    }
                }
                else
                {
                    // Search for the next element that fit filter string
                    var hit = false;
                    if(s2rGlobal.s2rList.filterString == "")
                        hit = true;
                    else
                    {
                        hit = node.title.toLowerCase().indexOf(s2rGlobal.s2rList.filterString) != -1 || node.uri.toLowerCase().indexOf(s2rGlobal.s2rList.filterString) != -1;

                        if(!hit)
                        {
                            var tags = s2rGlobal.s2rServices.tags().getTagsForURI(node_nsIURI, {});
                            for(var j = 0; j < tags.length && !hit; j++)
                            {
                                hit = tags[j].toLowerCase().indexOf(s2rGlobal.s2rList.filterString) != -1;
                            }

                            if(!hit)
                            {
                                hit = s2rGlobal.s2rGeneral.bookmarks_getDescription(node.itemId).toLowerCase().indexOf(s2rGlobal.s2rList.filterString) != -1;
                            }
                        }
                    }

                    if(hit)
                    {
                        index_next = i;
                        break;
                    }
                }

            }

            node = cont.getChild(index);
            var show = s2rGlobal.s2rPref.getViewMode();
            var newNode = s2rGlobal.s2rList.createNode(show, node.itemId, node.icon, node_nsIURI, node.title, node.dateAdded);
            if(newNode != null)
            {
                if((cont.childCount - 1) == index || index_next == -1)
                    s2rGlobal.s2rList.s2r_listNode.appendChild(newNode);
                else
                    s2rGlobal.s2rList.s2r_listNode.insertBefore(newNode, document.getElementById(s2rGlobal.s2rGeneral.genNodeName(cont.getChild(index_next).itemId)));
            }
            // Close container when done
            cont.containerOpen = false;
        }

        if(s2rGlobal.s2rList.s2r_listNode.firstChild)
        {
            s2rGlobal.s2rList.s2r_noBookmarksMessage.setAttribute("hidden", "true");
        }
        else
        {
            s2rGlobal.s2rList.s2r_noBookmarksMessage.setAttribute("hidden", "false");
        }
    },

    removeNode : function(nodeID)
    {
        s2rGlobal.s2rList.s2r_listNode.removeChild(document.getElementById(s2rGlobal.s2rGeneral.genNodeName(nodeID)));

        if(s2rGlobal.s2rList.s2r_listNode.firstChild)
        {
            s2rGlobal.s2rList.s2r_noBookmarksMessage.setAttribute("hidden", "true");
        }
        else
        {
            s2rGlobal.s2rList.s2r_noBookmarksMessage.setAttribute("hidden", "false");
        }

        if(s2rGlobal.s2rList.removeList[nodeID])
        {
            s2rGlobal.s2rList.removeList[nodeID].cancel();
            delete s2rGlobal.s2rList.removeList[nodeID];
        }
    },

    requestFillList : function(filter, force)
    {
        if(filter == null)
        {
            s2rGlobal.s2rList.filterString = "";
            s2rGlobal.s2rList.fillList();
        }
        else if(s2rGlobal.s2rList.filterString != filter || force)
        {
            s2rGlobal.s2rList.filterString = filter;
            s2rGlobal.s2rList.fillList();
        }
    },

    // Settings observer
    observe : function(aSubject, aTopic, aData)
    {
        if ("nsPref:changed" == aTopic)
        {
            switch(aData)
            {
            case s2rGlobal.s2rPref.preferenceSorting:
                s2rGlobal.s2rList.requestFillList(s2rGlobal.s2rList.filterString, true);
                s2rGlobal.s2rList.sortNode.value = s2rGlobal.s2rPref.getSorting();
                break;
            case s2rGlobal.s2rPref.preferenceFolderName:
//                s2rGlobal.s2rList.deleteBookmarksObserver();
//                s2rGlobal.s2rList.createBookmarksObserver();
                s2rGlobal.s2rList.requestFillList(s2rGlobal.s2rList.filterString, true);
                break;
            case s2rGlobal.s2rPref.preferenceViewMode:
            case s2rGlobal.s2rPref.preferenceShowTooltip:
            case s2rGlobal.s2rPref.preferenceFullViewWithURL:
                s2rGlobal.s2rList.requestFillList(s2rGlobal.s2rList.filterString, true);
                break;
            default:
                break;
            }
        }
    },

    createBookmarksObserver : function()
    {
        s2rGlobal.s2rList.bookmarksObserver = new s2rGlobal.s2rGeneral.bookmarksServiceObserver();
        s2rGlobal.s2rList.bookmarksObserver.register();
        s2rGlobal.s2rList.bookmarksObserver.itemAdded = s2rGlobal.s2rList.addNode;
        s2rGlobal.s2rList.bookmarksObserver.itemRemoved = s2rGlobal.s2rList.removeNode;
    },

    deleteBookmarksObserver : function()
    {
        s2rGlobal.s2rList.bookmarksObserver.unregister();
    },

    changeSorting : function(value)
    {
        s2rGlobal.s2rServices.preferences().setCharPref(s2rGlobal.s2rPref.preferenceSorting, value);
    },

    bookmarkDetailsClosed : function()
    {
        s2rGlobal.s2rGeneral.tags_setTags(s2rGlobal.s2rList.editDetailsPanel.uri, s2rGlobal.s2rList.editDetailsPanel.tags.value, true);
        s2rGlobal.s2rGeneral.bookmarks_setDescription(s2rGlobal.s2rList.editDetailsPanel.itemId, s2rGlobal.s2rList.editDetailsPanel.description.value);
    },

    hideDetailsPanel : function(event)
    {
        if(event.button != 2 && s2rGlobal.s2rList.editDetailsPanel.panel.state != "closed")
            s2rGlobal.s2rList.editDetailsPanel.panel.hidePopup();
    }
};
