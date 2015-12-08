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

s2rGlobal.sr2_sortingType = {
    "time_asc"  : 0, // sort by creation time (ascending order)
    "time_desc" : 1, // sort by creation time (descending order)
    "name_asc"  : 2, // sort by name time (ascending order)
    "name_desc" : 3  // sort by name time (descending order)
};

s2rGlobal.s2r_showType = {
    "compact"  : 0,
    "full" : 1
};

s2rGlobal.s2rServices =
{
    preferences : function()
    {
        return Components.classes["@mozilla.org/preferences-service;1"]
                         .getService(Components.interfaces.nsIPrefBranch);
    },

    history : function()
    {
        return Components.classes["@mozilla.org/browser/nav-history-service;1"]
                         .getService(Components.interfaces.nsINavHistoryService);
    },

    bookmarks : function()
    {
        return Components.classes["@mozilla.org/browser/nav-bookmarks-service;1"]
                         .getService(Components.interfaces.nsINavBookmarksService);
    },

    prompt : function()
    {
        return Components.classes["@mozilla.org/embedcomp/prompt-service;1"]
                         .getService(Components.interfaces.nsIPromptService);
    },

    favicon : function()
    {
        return Components.classes["@mozilla.org/browser/favicon-service;1"]
                         .getService(Components.interfaces.nsIFaviconService);
    },

    IO : function()
    {
        return Components.classes["@mozilla.org/network/io-service;1"]
                         .getService(Components.interfaces.nsIIOService);
    },

    tags : function()
    {
        return Components.classes["@mozilla.org/browser/tagging-service;1"]
                         .getService(Components.interfaces.nsITaggingService);
    },

    annotation : function()
    {
        return Components.classes["@mozilla.org/browser/annotation-service;1"]
                         .getService(Components.interfaces.nsIAnnotationService);
    }
};

s2rGlobal.s2rGeneral =
{
    makeURI : function(uri)
    {
        var ret = null;
        if(uri instanceof Components.interfaces.nsIURI)
        {
            ret = uri;
        }
        else
        {
            try
            {
                ret = s2rGlobal.s2rServices.IO().newURI(uri, null, null);
            }
            catch(e)
            {
                ret = null;
            }
        }
        return ret;
    },

    genNodeName : function(id)
    {
        return "s2r_Node_" + id;
    },

    bookmarks_create : function(name, uri, FolderId)
    {
        var bookmarksService = s2rGlobal.s2rServices.bookmarks();
        uri = s2rGlobal.s2rGeneral.makeURI(uri);
        if(uri != null)
            return bookmarksService.insertBookmark(FolderId, uri, bookmarksService.DEFAULT_INDEX, name);
        else
            return -1;
    },

    // checks if URI is bookmarked, contained in the list and returns the apropriate node
    bookmarks_findBookmarkID : function(uri, FolderId)
    {
        var nsiuri = s2rGlobal.s2rGeneral.makeURI(uri);

        if(nsiuri == null)
            return null;

        var bookmarksService = s2rGlobal.s2rServices.bookmarks();

        // This cause problems. For some bookmarked URLs this function still return false.
//         if(bookmarksService.isBookmarked(nsiuri) == false)
//         {
//             return null;
//         }

        var count = {};
        var nodeIDs = bookmarksService.getBookmarkIdsForURI(nsiuri, count);

        for(var i=0; i<count.value; i++)
        {
            if(bookmarksService.getFolderIdForItem(nodeIDs[i]) == FolderId)
                return nodeIDs[i];
        }
        return null;
    },

    bookmarks_checkURI : function(uri, FolderId)
    {
        var nsiuri = s2rGlobal.s2rGeneral.makeURI(uri);
        if(s2rGlobal.s2rGeneral.bookmarks_findBookmarkID(nsiuri, FolderId) == null)
            return false;
        return true;
    },

    bookmarks_removeBookmark : function(b_uri, FolderId)
    {
        var nsiuri = s2rGlobal.s2rGeneral.makeURI(b_uri);
        var id = s2rGlobal.s2rGeneral.bookmarks_findBookmarkID(nsiuri, FolderId);

        if(id == null)
        {
            return;
        }

        s2rGlobal.s2rServices.bookmarks().removeItem(id);
    },

    bookmarks_getDescription : function(itemId)
    {
        var descriptionID = "bookmarkProperties/description";
        var annotationSrvc = s2rGlobal.s2rServices.annotation();
        if(annotationSrvc.itemHasAnnotation(itemId, descriptionID))
            return annotationSrvc.getItemAnnotation(itemId, descriptionID);
        return "";
    },

    bookmarks_setDescription : function(itemId, description)
    {
        var descriptionID = "bookmarkProperties/description";
        var annotationSrvc = s2rGlobal.s2rServices.annotation();
        annotationSrvc.setItemAnnotation(itemId, descriptionID, description, 0, 0);
    },

    bookmarks_findFolder : function(foldername)
    {
        var historyService = s2rGlobal.s2rServices.history();
        var bookmarksService = s2rGlobal.s2rServices.bookmarks();
        var options = historyService.getNewQueryOptions();
        var query = historyService.getNewQuery();

        var menuFolder = bookmarksService.bookmarksMenuFolder; // Bookmarks menu folder

        query.setFolders([menuFolder], 1);

        var result = historyService.executeQuery(query, options);
        var rootNode = result.root;
        rootNode.containerOpen = true;

        var res = null;
        for (var i = 0; i < rootNode.childCount; i ++)
        {
            var node = rootNode.getChild(i);
            if(node.title == foldername && node.type == node.RESULT_TYPE_FOLDER)
            {
                res = node.itemId;
                break;
            }
        }

        // close a container after using it!
        rootNode.containerOpen = false;

        return res;
    },

    bookmarks_ensureFolderExist : function(foldername)
    {
        var bookmarksService = s2rGlobal.s2rServices.bookmarks();
        var menuFolder = bookmarksService.bookmarksMenuFolder; // Bookmarks menu folder

        var res = s2rGlobal.s2rGeneral.bookmarks_findFolder(foldername);

        // if not exist - we need to create it
        if(res == null)
        {
            res = bookmarksService.createFolder(menuFolder, foldername, bookmarksService.DEFAULT_INDEX);
        }
        return res;
    },

    bookmarksServiceObserver : function ()
    {
        this.itemAdded = null;
        this.itemRemoved = null;

        this.register = function()
        {
            s2rGlobal.s2rServices.bookmarks().addObserver(this, false);
        };

        this.unregister = function()
        {
            s2rGlobal.s2rServices.bookmarks().removeObserver(this);
        };

        this.onItemAdded = function(aItemId, aParentId, aIndex, aItemType)
        {
            if(this.itemAdded != null)
            {

                if(aParentId != s2rGlobal.s2rPref.getFolderId() || aItemType != s2rGlobal.s2rServices.bookmarks().TYPE_BOOKMARK)
                    return;

                this.itemAdded(aItemId);
            }
        };

        this.onItemRemoved = function(aItemId, aParentId, aIndex, aItemType)
        {
            if(aItemId == s2rGlobal.s2rPref.getFolderId())
            {
//                 if(aProperty == "title")
//                 {
//                     var newName = s2rGlobal.s2rServices.bookmarks().getItemTitle(aBookmarkId);
//                     if(s2rGlobal.s2rPref.getFolderName() != newName)
//                         s2rGlobal.s2rPref.setFolderName(newName);
//                 }
s2rGlobal.s2rPref.resetFolderIdCache();//setFolderName(s2rGlobal.s2rPref.getFolderName());
            }
            else
            {
                if(this.itemRemoved != null)
                {

                    if(aParentId != s2rGlobal.s2rPref.getFolderId() || aItemType != s2rGlobal.s2rServices.bookmarks().TYPE_BOOKMARK)
                        return;

                    this.itemRemoved(aItemId);
                }
            }
        };

        this.onItemChanged = function(aBookmarkId, aProperty, aIsAnnotationProperty, aNewValue, aLastModified, aItemType)
        {
            if(aProperty != "")
            {
                if(aBookmarkId == s2rGlobal.s2rPref.getFolderId())
                {
                    if(aProperty == "title")
                    {
                        var newName = s2rGlobal.s2rServices.bookmarks().getItemTitle(aBookmarkId);
                        if(s2rGlobal.s2rPref.getFolderName() != newName)
                            s2rGlobal.s2rPref.setFolderName(newName);
                    }
                }
                else
                {
                    if(this.itemAdded != null && this.itemRemoved != null)
                    {
                        var parentID = s2rGlobal.s2rServices.bookmarks().getFolderIdForItem(aBookmarkId);
                        if(parentID != s2rGlobal.s2rPref.getFolderId())
                            return;

                        this.itemRemoved(aBookmarkId);
                        this.itemAdded(aBookmarkId);
                    }
                }
            }
        };

        this.onItemMoved = function(aItemId, aOldParentId, aOldIndex, aNewParentId, aNewIndex, aItemType)
        {
            if(    aOldParentId == aNewParentId
                || !(aOldParentId == s2rGlobal.s2rPref.getFolderId() || aNewParentId == s2rGlobal.s2rPref.getFolderId())
                || aItemType != s2rServices.bookmarks().TYPE_BOOKMARK)
                return;

            if(aOldParentId == s2rGlobal.s2rPref.getFolderId())
            {
                if(this.itemRemoved != null)
                    this.itemRemoved(aItemId);
            }
            else
            {
                if(this.itemAdded != null)
                    this.itemAdded(aItemId);
            }
        };

        // Not used observer API:
        this.onBeginUpdateBatch = function(){};
        this.onEndUpdateBatch = function(){};
        this.onItemVisited = function(aBookmarkId, aVisitID, time){};

        this.QueryInterface = function(aIID)
        {
            if (aIID.equals(Components.interfaces.nsINavBookmarkObserver)   ||
//                 aIID.equals(Components.interfaces.nsISupportsWeakReference) ||
                aIID.equals(Components.interfaces.nsISupports))
            {
                return this;
            }
            throw Components.results.NS_NOINTERFACE;
        };
    },

    tags_setTags : function(uri, tagsString, doReplace)
    {
        uri = s2rGlobal.s2rGeneral.makeURI(uri);
        if(uri == null)
            return;
        var taggingSvc = s2rGlobal.s2rServices.tags();
        var tags = tagsString.split(",");
        for(var i = 0; i < tags.length; i++)
            tags[i] = tags[i].replace(/^\s+|\s+$/g, '');
        if(doReplace)
            taggingSvc.untagURI(uri, null); // remove old tags
        taggingSvc.tagURI(uri, tags);   // set new tags
    }

};

s2rGlobal.s2rPref =
{
    prefSrvc  : s2rGlobal.s2rServices.preferences(),
    prefSrvc2 : s2rGlobal.s2rServices.preferences().QueryInterface(Components.interfaces.nsIPrefBranch2),

    // Single-call preferences. Caching disabled

    preferenceSidebarShortcut : "extensions.save2read.sidebar_shortcut",
    getSidebarShortcut : function()
    {
        return s2rGlobal.s2rPref.prefSrvc.getCharPref(s2rGlobal.s2rPref.preferenceSidebarShortcut);
    },

    preferenceBookmarkShortcut : "extensions.save2read.bookmark_shortcut",
    getBookmarkShortcut : function()
    {
        return s2rGlobal.s2rPref.prefSrvc.getCharPref(s2rGlobal.s2rPref.preferenceBookmarkShortcut);
    },

    preferenceBookmarkEditShortcut : "extensions.save2read.bookmark_edit_shortcut",
    getBookmarkEditShortcut : function()
    {
        return s2rGlobal.s2rPref.prefSrvc.getCharPref(s2rGlobal.s2rPref.preferenceBookmarkEditShortcut);
    },

    preferencePanelShortcut : "extensions.save2read.panel_shortcut",
    getPanelShortcut : function()
    {
        return s2rGlobal.s2rPref.prefSrvc.getCharPref(s2rGlobal.s2rPref.preferencePanelShortcut);
    },

    preferenceOpenShortcut : "extensions.save2read.open_shortcut",
    getOpenShortcut : function()
    {
        return s2rGlobal.s2rPref.prefSrvc.getCharPref(s2rGlobal.s2rPref.preferenceOpenShortcut);
    },

    preferenceFolderName : "extensions.save2read.folderName",
    getFolderName : function()
    {
        var FolderName = s2rGlobal.s2rPref.prefSrvc.getCharPref(s2rGlobal.s2rPref.preferenceFolderName);
        if(FolderName == null || FolderName == "")
        {
            try
            {
                s2rGlobal.s2rPref.prefSrvc.clearUserPref(s2rGlobal.s2rPref.preferenceFolderName);
                FolderName = s2rGlobal.s2rPref.prefSrvc.getCharPref(s2rGlobal.s2rPref.preferenceFolderName);
            } catch (e)
            {
                // if this happen there are some VERY nasty error
                FolderName = "Save-To-Read";
            }
        }
        return FolderName;
    },
    setFolderName : function(newName)
    {
        s2rGlobal.s2rPref.prefSrvc.setCharPref(s2rGlobal.s2rPref.preferenceFolderName, newName);
    },
    addFolderNameObserver : function(obj)
    {
        s2rGlobal.s2rPref.prefSrvc2.addObserver(s2rGlobal.s2rPref.preferenceFolderName, obj, false);
    },
    removeFolderNameObserver : function(obj)
    {
        s2rGlobal.s2rPref.prefSrvc2.removeObserver(s2rGlobal.s2rPref.preferenceFolderName, obj);
    },

    preferenceReadButton : "extensions.save2read.readButton",
    getReadButton : function()
    {
        var ReadButton = s2rGlobal.s2rPref.prefSrvc.getCharPref(s2rGlobal.s2rPref.preferenceReadButton);
        if( ReadButton != "oldest" &&
            ReadButton != "newest" &&
            ReadButton != "first" &&
            ReadButton != "last" &&
            ReadButton != "random" )
        {
            try
            {
                s2rGlobal.s2rPref.prefSrvc.clearUserPref(s2rGlobal.s2rPref.preferenceReadButton);
                ReadButton = s2rGlobal.s2rPref.prefSrvc.getCharPref(s2rGlobal.s2rPref.preferenceReadButton);
            } catch (e)
            {
                // if this happen there are some VERY nasty error
                s2rGlobal.s2rPref.ReadButton = "";
            }
        }
        return ReadButton;
    },

    // Multi-call preferences. Caching enabled

    preferenceSorting : "extensions.save2read.sort_by",
    SortingCached : false,
    getSorting : function()
    {
        if(!s2rGlobal.s2rPref.SortingCached)
        {
            s2rGlobal.s2rPref.SortingCached = true;
            if(typeof(s2rGlobal.s2rPref.Sorting) == "undefined")
                s2rGlobal.s2rPref.prefSrvc2.addObserver(s2rGlobal.s2rPref.preferenceSorting, s2rGlobal.s2rPref, false);

            s2rGlobal.s2rPref.Sorting = s2rGlobal.s2rPref.prefSrvc.getCharPref(s2rGlobal.s2rPref.preferenceSorting);
            if(s2rGlobal.s2rPref.Sorting == null || (   s2rGlobal.s2rPref.Sorting != "time_asc" &&
                                                        s2rGlobal.s2rPref.Sorting != "time_desc" &&
                                                        s2rGlobal.s2rPref.Sorting != "name_asc" &&
                                                        s2rGlobal.s2rPref.Sorting != "name_desc" ))
            {
                try
                {
                    s2rGlobal.s2rPref.prefSrvc.clearUserPref(s2rGlobal.s2rPref.preferenceSorting);
                    s2rGlobal.s2rPref.SortingCached = false;
                    s2rGlobal.s2rPref.getSorting(); // this would not recurse because clearUserPref should throw an exception if called twice (setting is already at its default)
                } catch (e)
                {
                    // if this happen there are some VERY nasty error
                    s2rGlobal.s2rPref.SortingCached = true;
                    s2rGlobal.s2rPref.Sorting = "";
                }
            }
        }
        return s2rGlobal.s2rPref.Sorting;
    },
    addSortingObserver : function(obj)
    {
        s2rGlobal.s2rPref.prefSrvc2.addObserver(s2rGlobal.s2rPref.preferenceSorting, obj, false);
    },
    removeSortingObserver : function(obj)
    {
        s2rGlobal.s2rPref.prefSrvc2.removeObserver(s2rGlobal.s2rPref.preferenceSorting, obj);
    },

    preferenceViewMode : "extensions.save2read.viewMode",
    ViewModeCached : false,
    getViewMode : function()
    {
        if(!s2rGlobal.s2rPref.ViewModeCached)
        {
            s2rGlobal.s2rPref.ViewModeCached = true;
            if(typeof(s2rGlobal.s2rPref.ViewMode) == "undefined")
                s2rGlobal.s2rPref.prefSrvc2.addObserver(s2rGlobal.s2rPref.preferenceViewMode, s2rGlobal.s2rPref, false);

            s2rGlobal.s2rPref.ViewMode = s2rGlobal.s2rPref.prefSrvc.getCharPref(s2rGlobal.s2rPref.preferenceViewMode);
            if(s2rGlobal.s2rPref.ViewMode == null || (  s2rGlobal.s2rPref.ViewMode != "compact" &&
                                                        s2rGlobal.s2rPref.ViewMode != "full" ))
            {
                try
                {
                    s2rGlobal.s2rPref.prefSrvc.clearUserPref(s2rGlobal.s2rPref.preferenceViewMode);
                    s2rGlobal.s2rPref.ViewModeCached = false;
                    s2rGlobal.s2rPref.getViewMode(); // this would not recurse because clearUserPref should throw an exception if called twice (setting is already at its default)
                } catch (e)
                {
                    // if this happen there are some VERY nasty error
                    s2rGlobal.s2rPref.ViewModeCached = true;
                    s2rGlobal.s2rPref.ViewMode = "";
                }
            }
        }
        return s2rGlobal.s2rPref.ViewMode;
    },
    addViewModeObserver : function(obj)
    {
        s2rGlobal.s2rPref.prefSrvc2.addObserver(s2rGlobal.s2rPref.preferenceViewMode, obj, false);
    },
    removeViewModeObserver : function(obj)
    {
        s2rGlobal.s2rPref.prefSrvc2.removeObserver(s2rGlobal.s2rPref.preferenceViewMode, obj);
    },

    preferenceFolderId : "extensions.save2read.folderID",
    FolderIdCached : false,
    getFolderId : function()
    {
        //check if already loaded
        if(!s2rGlobal.s2rPref.FolderIdCached)
        {
            s2rGlobal.s2rPref.FolderIdCached = true;
            if(typeof(s2rGlobal.s2rPref.FolderId) == "undefined")
                s2rGlobal.s2rPref.prefSrvc2.addObserver(s2rGlobal.s2rPref.preferenceFolderName, s2rGlobal.s2rPref, false);

            var bookmarksService = s2rGlobal.s2rServices.bookmarks();

            s2rGlobal.s2rPref.FolderId = s2rGlobal.s2rPref.prefSrvc.getCharPref(s2rGlobal.s2rPref.preferenceFolderId);
            var folderName = s2rGlobal.s2rPref.getFolderName();
            var isNeedUpdate = false;
            if(s2rGlobal.s2rPref.FolderId == "")
            {
                isNeedUpdate = true;
            }
            else
            {
//                 if(s2rGlobal.s2rPref.FolderId == -1)
//                 {
//                     isNeedUpdate = true;
//                 }
//                 else 
                if(bookmarksService.getItemTitle(s2rGlobal.s2rPref.FolderId) != folderName)
                {
                    // We check if FolderId is for correct folder.
                    isNeedUpdate = true;
                }
            }
            if(isNeedUpdate)
            {
                s2rGlobal.s2rPref.FolderId = s2rGlobal.s2rGeneral.bookmarks_ensureFolderExist(folderName);
                s2rGlobal.s2rPref.prefSrvc.setCharPref(s2rGlobal.s2rPref.preferenceFolderId, s2rGlobal.s2rPref.FolderId);
            }
        }

        return s2rGlobal.s2rPref.FolderId;
    },
    resetFolderIdCache : function()
    {
        s2rGlobal.s2rPref.FolderIdCached = false;
    },

    preferenceOpenTabForLeftClick : "extensions.save2read.openTabForLeftClick",
    OpenTabForLeftClickCached : false,
    getOpenTabForLeftClick : function()
    {
        if(!s2rGlobal.s2rPref.OpenTabForLeftClickCached)
        {
            s2rGlobal.s2rPref.OpenTabForLeftClickCached = true;
            if(typeof(s2rGlobal.s2rPref.OpenTabForLeftClick) == "undefined")
                s2rGlobal.s2rPref.prefSrvc2.addObserver(s2rGlobal.s2rPref.preferenceOpenTabForLeftClick, s2rGlobal.s2rPref, false);
            s2rGlobal.s2rPref.OpenTabForLeftClick = s2rGlobal.s2rPref.prefSrvc.getBoolPref(s2rGlobal.s2rPref.preferenceOpenTabForLeftClick);
        }
        return s2rGlobal.s2rPref.OpenTabForLeftClick;
    },

    preferenceClosePanelOnClick : "extensions.save2read.closePanelOnClick",
    ClosePanelOnClickCached : false,
    getClosePanelOnClick : function()
    {
        if(!s2rGlobal.s2rPref.ClosePanelOnClickCached)
        {
            s2rGlobal.s2rPref.ClosePanelOnClickCached = true;
            if(typeof(s2rGlobal.s2rPref.ClosePanelOnClick) == "undefined")
                s2rGlobal.s2rPref.prefSrvc2.addObserver(s2rGlobal.s2rPref.preferenceClosePanelOnClick, s2rGlobal.s2rPref, false);
            s2rGlobal.s2rPref.ClosePanelOnClick = s2rGlobal.s2rPref.prefSrvc.getBoolPref(s2rGlobal.s2rPref.preferenceClosePanelOnClick);
        }
        return s2rGlobal.s2rPref.ClosePanelOnClick;
    },

    preferenceUseRightButton: "extensions.save2read.useRightButton",
    UseRightButtonCached : false,
    getUseRightButton: function()
    {
        if(!s2rGlobal.s2rPref.UseRightButtonCached)
        {
            s2rGlobal.s2rPref.UseRightButtonCached = true;
            if(typeof(s2rGlobal.s2rPref.UseRightButton) == "undefined")
                s2rGlobal.s2rPref.prefSrvc2.addObserver(s2rGlobal.s2rPref.preferenceUseRightButton, s2rGlobal.s2rPref, false);
            s2rGlobal.s2rPref.UseRightButton = s2rGlobal.s2rPref.prefSrvc.getBoolPref(s2rGlobal.s2rPref.preferenceUseRightButton);
        }
        return s2rGlobal.s2rPref.UseRightButton;
    },

    preferenceRemoveOnReadButton: "extensions.save2read.removeOnReadButton",
    RemoveOnReadButtonCached : false,
    getRemoveOnReadButton : function()
    {
        if(!s2rGlobal.s2rPref.RemoveOnReadButtonCached)
        {
            s2rGlobal.s2rPref.RemoveOnReadButtonCached = true;
            if(typeof(s2rGlobal.s2rPref.RemoveOnReadButton) == "undefined")
                s2rGlobal.s2rPref.prefSrvc2.addObserver(s2rGlobal.s2rPref.preferenceRemoveOnReadButton, s2rGlobal.s2rPref, false);
            s2rGlobal.s2rPref.RemoveOnReadButton = s2rGlobal.s2rPref.prefSrvc.getBoolPref(s2rGlobal.s2rPref.preferenceRemoveOnReadButton);
        }
        return s2rGlobal.s2rPref.RemoveOnReadButton;
    },

    preferenceRemoveOnLeftClick: "extensions.save2read.removeOnLeftClick",
    RemoveOnLeftClickCached : false,
    getRemoveOnLeftClick : function()
    {
        if(!s2rGlobal.s2rPref.RemoveOnLeftClickCached)
        {
            s2rGlobal.s2rPref.RemoveOnLeftClickCached = true;
            if(typeof(s2rGlobal.s2rPref.RemoveOnLeftClick) == "undefined")
                s2rGlobal.s2rPref.prefSrvc2.addObserver(s2rGlobal.s2rPref.preferenceRemoveOnLeftClick, s2rGlobal.s2rPref, false);
            s2rGlobal.s2rPref.RemoveOnLeftClick = s2rGlobal.s2rPref.prefSrvc.getBoolPref(s2rGlobal.s2rPref.preferenceRemoveOnLeftClick);
        }
        return s2rGlobal.s2rPref.RemoveOnLeftClick;
    },

    preferenceFullViewWithURL : "extensions.save2read.fullViewWithURL",
    FullViewWithURLCached : false,
    getFullViewWithURL : function()
    {
        if(!s2rGlobal.s2rPref.FullViewWithURLCached)
        {
            s2rGlobal.s2rPref.FullViewWithURLCached = true;
            if(typeof(s2rGlobal.s2rPref.FullViewWithURL) == "undefined")
                s2rGlobal.s2rPref.prefSrvc2.addObserver(s2rGlobal.s2rPref.preferenceFullViewWithURL, s2rGlobal.s2rPref, false);
            s2rGlobal.s2rPref.FullViewWithURL = s2rGlobal.s2rPref.prefSrvc.getBoolPref(s2rGlobal.s2rPref.preferenceFullViewWithURL);
        }
        return s2rGlobal.s2rPref.FullViewWithURL;
    },
    addFullViewWithURLObserver : function(obj)
    {
        s2rGlobal.s2rPref.prefSrvc2.addObserver(s2rGlobal.s2rPref.preferenceFullViewWithURL, obj, false);
    },
    removeFullViewWithURLObserver : function(obj)
    {
        s2rGlobal.s2rPref.prefSrvc2.removeObserver(s2rGlobal.s2rPref.preferenceFullViewWithURL, obj);
    },

    preferenceShowTooltip : "extensions.save2read.showTooltip",
    ShowTooltipCached : false,
    getShowTooltip : function()
    {
        if(!s2rGlobal.s2rPref.ShowTooltipCached)
        {
            s2rGlobal.s2rPref.ShowTooltipCached = true;
            if(typeof(s2rGlobal.s2rPref.ShowTooltip) == "undefined")
                s2rGlobal.s2rPref.prefSrvc2.addObserver(s2rGlobal.s2rPref.preferenceShowTooltip, s2rGlobal.s2rPref, false);
            s2rGlobal.s2rPref.ShowTooltip = s2rGlobal.s2rPref.prefSrvc.getBoolPref(s2rGlobal.s2rPref.preferenceShowTooltip);
        }
        return s2rGlobal.s2rPref.ShowTooltip;
    },
    addShowTooltipObserver : function(obj)
    {
        s2rGlobal.s2rPref.prefSrvc2.addObserver(s2rGlobal.s2rPref.preferenceShowTooltip, obj, false);
    },
    removeShowTooltipObserver : function(obj)
    {
        s2rGlobal.s2rPref.prefSrvc2.removeObserver(s2rGlobal.s2rPref.preferenceShowTooltip, obj);
    },

    preferenceAutoTags : "extensions.save2read.autoTags",
    AutoTagsCached : false,
    getAutoTags : function()
    {
        if(!s2rGlobal.s2rPref.AutoTagsCached)
        {
            s2rGlobal.s2rPref.AutoTagsCached = true;
            if(typeof(s2rGlobal.s2rPref.AutoTags) == "undefined")
                s2rGlobal.s2rPref.prefSrvc2.addObserver(s2rGlobal.s2rPref.preferenceAutoTags, s2rGlobal.s2rPref, false);
            s2rGlobal.s2rPref.AutoTags = s2rGlobal.s2rPref.prefSrvc.getCharPref(s2rGlobal.s2rPref.preferenceAutoTags);
        }
        return s2rGlobal.s2rPref.AutoTags;
    },

    preferenceCounterOnSidebarBtn : "extensions.save2read.counterOnSidebarBtn",
    getCounterOnSidebarBtn : function()
    {
        return s2rGlobal.s2rPref.prefSrvc.getBoolPref(s2rGlobal.s2rPref.preferenceCounterOnSidebarBtn);
    },
    addCounterOnSidebarBtnObserver : function(obj)
    {
        s2rGlobal.s2rPref.prefSrvc2.addObserver(s2rGlobal.s2rPref.preferenceCounterOnSidebarBtn, obj, false);
    },
    removeCounterOnSidebarBtnObserver : function(obj)
    {
        s2rGlobal.s2rPref.prefSrvc2.removeObserver(s2rGlobal.s2rPref.preferenceCounterOnSidebarBtn, obj);
    },

    preferenceCounterOnPanelBtn : "extensions.save2read.counterOnPanelBtn",
    getCounterOnPanelBtn : function()
    {
        return s2rGlobal.s2rPref.prefSrvc.getBoolPref(s2rGlobal.s2rPref.preferenceCounterOnPanelBtn);
    },
    addCounterOnPanelBtnObserver : function(obj)
    {
        s2rGlobal.s2rPref.prefSrvc2.addObserver(s2rGlobal.s2rPref.preferenceCounterOnPanelBtn, obj, false);
    },
    removeCounterOnPanelBtnObserver : function(obj)
    {
        s2rGlobal.s2rPref.prefSrvc2.removeObserver(s2rGlobal.s2rPref.preferenceCounterOnPanelBtn, obj);
    },

    preferenceCounterOnOpenBtn : "extensions.save2read.counterOnOpenBtn",
    getCounterOnOpenBtn : function()
    {
        return s2rGlobal.s2rPref.prefSrvc.getBoolPref(s2rGlobal.s2rPref.preferenceCounterOnOpenBtn);;
    },
    addCounterOnOpenBtnObserver : function(obj)
    {
        s2rGlobal.s2rPref.prefSrvc2.addObserver(s2rGlobal.s2rPref.preferenceCounterOnOpenBtn, obj, false);
    },
    removeCounterOnOpenBtnObserver : function(obj)
    {
        s2rGlobal.s2rPref.prefSrvc2.removeObserver(s2rGlobal.s2rPref.preferenceCounterOnOpenBtn, obj);
    },

    preferenceClosePageOnSave : "extensions.save2read.closePageOnSave",
    getClosePageOnSave : function()
    {
        return s2rGlobal.s2rPref.prefSrvc.getBoolPref(s2rGlobal.s2rPref.preferenceClosePageOnSave);;
    },
    addClosePageOnSaveObserver : function(obj)
    {
        s2rGlobal.s2rPref.prefSrvc2.addObserver(s2rGlobal.s2rPref.preferenceClosePageOnSave, obj, false);
    },
    removeClosePageOnSaveObserver : function(obj)
    {
        s2rGlobal.s2rPref.prefSrvc2.removeObserver(s2rGlobal.s2rPref.preferenceClosePageOnSave, obj);
    },

    observe : function(aSubject, aTopic, aData)
    {
        if ("nsPref:changed" == aTopic)
        {
            switch(aData)
            {
            case s2rGlobal.s2rPref.preferenceSorting:
                s2rGlobal.s2rPref.SortingCached = false;
                break;
            case s2rGlobal.s2rPref.preferenceViewMode:
                s2rGlobal.s2rPref.ViewModeCached = false;
                break;
            case s2rGlobal.s2rPref.preferenceFolderName:
                s2rGlobal.s2rPref.FolderIdCached = false;
                break;
            case s2rGlobal.s2rPref.preferenceOpenTabForLeftClick:
                s2rGlobal.s2rPref.OpenTabForLeftClickCached = false;
                break;
            case s2rGlobal.s2rPref.preferenceClosePanelOnClick:
                s2rGlobal.s2rPref.ClosePanelOnClickCached = false;
                break;
            case s2rGlobal.s2rPref.preferenceUseRightButton:
                s2rGlobal.s2rPref.UseRightButtonCached = false;
                break;
            case s2rGlobal.s2rPref.preferenceRemoveOnReadButton:
                s2rGlobal.s2rPref.RemoveOnReadButtonCached = false;
                break;
            case s2rGlobal.s2rPref.preferenceRemoveOnLeftClick:
                s2rGlobal.s2rPref.RemoveOnLeftClickCached = false;
                break;
            case s2rGlobal.s2rPref.preferenceFullViewWithURL:
                s2rGlobal.s2rPref.FullViewWithURLCached = false;
                break;
            case s2rGlobal.s2rPref.preferenceShowTooltip:
                s2rGlobal.s2rPref.ShowTooltipCached = false;
                break;
            case s2rGlobal.s2rPref.preferenceAutoTags:
                s2rGlobal.s2rPref.AutoTagsCached = false;
                break;
            default:
                break;
            }
        }
    },

    cleanObservers : function()
    {
        if(typeof(s2rGlobal.s2rPref.Sorting) != "undefined")
            s2rGlobal.s2rPref.prefSrvc2.removeObserver(s2rGlobal.s2rPref.preferenceSorting, s2rGlobal.s2rPref);

        if(typeof(s2rGlobal.s2rPref.ViewMode) != "undefined")
            s2rGlobal.s2rPref.prefSrvc2.removeObserver(s2rGlobal.s2rPref.preferenceViewMode, s2rGlobal.s2rPref);

        if(typeof(s2rGlobal.s2rPref.FolderId) != "undefined")
            s2rGlobal.s2rPref.prefSrvc2.removeObserver(s2rGlobal.s2rPref.preferenceFolderName, s2rGlobal.s2rPref);

        if(typeof(s2rGlobal.s2rPref.OpenTabForLeftClick) != "undefined")
            s2rGlobal.s2rPref.prefSrvc2.removeObserver(s2rGlobal.s2rPref.preferenceOpenTabForLeftClick, s2rGlobal.s2rPref);

        if(typeof(s2rGlobal.s2rPref.ClosePanelOnClick) != "undefined")
            s2rGlobal.s2rPref.prefSrvc2.removeObserver(s2rGlobal.s2rPref.preferenceClosePanelOnClick, s2rGlobal.s2rPref);

        if(typeof(s2rGlobal.s2rPref.UseRightButton) != "undefined")
            s2rGlobal.s2rPref.prefSrvc2.removeObserver(s2rGlobal.s2rPref.preferenceUseRightButton, s2rGlobal.s2rPref);

        if(typeof(s2rGlobal.s2rPref.RemoveOnReadButton) != "undefined")
            s2rGlobal.s2rPref.prefSrvc2.removeObserver(s2rGlobal.s2rPref.preferenceRemoveOnReadButton, s2rGlobal.s2rPref);

        if(typeof(s2rGlobal.s2rPref.RemoveOnLeftClick) != "undefined")
            s2rGlobal.s2rPref.prefSrvc2.removeObserver(s2rGlobal.s2rPref.preferenceRemoveOnLeftClick, s2rGlobal.s2rPref);

        if(typeof(s2rGlobal.s2rPref.FullViewWithURL) != "undefined")
            s2rGlobal.s2rPref.prefSrvc2.removeObserver(s2rGlobal.s2rPref.preferenceFullViewWithURL, s2rGlobal.s2rPref);

        if(typeof(s2rGlobal.s2rPref.ShowTooltip) != "undefined")
            s2rGlobal.s2rPref.prefSrvc2.removeObserver(s2rGlobal.s2rPref.preferenceShowTooltip, s2rGlobal.s2rPref);

        if(typeof(s2rGlobal.s2rPref.AutoTags) != "undefined")
            s2rGlobal.s2rPref.prefSrvc2.removeObserver(s2rGlobal.s2rPref.preferenceAutoTags, s2rGlobal.s2rPref);
    }
};

window.addEventListener("unload", s2rGlobal.s2rPref.cleanObservers, false);