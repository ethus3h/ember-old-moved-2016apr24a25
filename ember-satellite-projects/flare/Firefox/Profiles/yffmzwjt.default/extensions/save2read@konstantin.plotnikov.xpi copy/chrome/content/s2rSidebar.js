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

s2rGlobal.startup = function()
{
    s2rGlobal.s2rList.mainWindow = window.QueryInterface(Components.interfaces.nsIInterfaceRequestor)
                        .getInterface(Components.interfaces.nsIWebNavigation)
                        .QueryInterface(Components.interfaces.nsIDocShellTreeItem)
                        .rootTreeItem
                        .QueryInterface(Components.interfaces.nsIInterfaceRequestor)
                        .getInterface(Components.interfaces.nsIDOMWindow);

    s2rGlobal.s2rList.s2r_listNode = document.getElementById("save2readSidebarList");
    s2rGlobal.s2rList.sortNode = document.getElementById("save2read_sidebar_sort_by");
    s2rGlobal.s2rList.s2r_noBookmarksMessage = document.getElementById("sidebar-list-no-bookmarks-text");
    s2rGlobal.s2rList.statusbar = s2rGlobal.s2rList.mainWindow.document.getElementById("statusbar-display");

    s2rGlobal.s2rList.editDetailsPanel = {};
    s2rGlobal.s2rList.editDetailsPanel.panel        = document.getElementById("save2read_sidebarBookmarkDetails");
    s2rGlobal.s2rList.editDetailsPanel.header       = document.getElementById("save2read_sidebarDetailsHeader");
    s2rGlobal.s2rList.editDetailsPanel.tags         = document.getElementById("save2read_sidebarTagsField");
    s2rGlobal.s2rList.editDetailsPanel.description  = document.getElementById("save2read_sidebarDescriptionField");

    // Settings observers should be set first (so would be called last)
    s2rGlobal.s2rPref.addSortingObserver(s2rGlobal.s2rList);
    s2rGlobal.s2rPref.addFolderNameObserver(s2rGlobal.s2rList);
    s2rGlobal.s2rPref.addViewModeObserver(s2rGlobal.s2rList);
    s2rGlobal.s2rPref.addFullViewWithURLObserver(s2rGlobal.s2rList);
    s2rGlobal.s2rPref.addShowTooltipObserver(s2rGlobal.s2rList);

    s2rGlobal.s2rList.requestFillList(null, false);//fillList(s2rGlobal.s2rPref.getViewMode());

    s2rGlobal.s2rList.createBookmarksObserver();

    s2rGlobal.s2rList.sortNode.value = s2rGlobal.s2rPref.getSorting();
};

s2rGlobal.shutdown = function()
{
    s2rGlobal.s2rList.removeAllRequested();

    // Sidebar is unloading
    s2rGlobal.s2rList.deleteBookmarksObserver();

    s2rGlobal.s2rPref.removeSortingObserver(s2rGlobal.s2rList);
    s2rGlobal.s2rPref.removeFolderNameObserver(s2rGlobal.s2rList);
    s2rGlobal.s2rPref.removeViewModeObserver(s2rGlobal.s2rList);
    s2rGlobal.s2rPref.removeFullViewWithURLObserver(s2rGlobal.s2rList);
    s2rGlobal.s2rPref.removeShowTooltipObserver(s2rGlobal.s2rList);

    s2rGlobal.s2rList.s2r_listNode = null;
};

window.addEventListener("load", s2rGlobal.startup, false);
window.addEventListener("unload", s2rGlobal.shutdown, false);
