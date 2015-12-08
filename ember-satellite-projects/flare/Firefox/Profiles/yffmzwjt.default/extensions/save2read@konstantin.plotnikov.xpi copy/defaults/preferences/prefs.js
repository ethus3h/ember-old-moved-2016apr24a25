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

pref("extensions.save2read.folderID",               "");
pref("extensions.save2read.viewMode",               "compact");
pref("extensions.save2read.folderName",             "Save-To-Read");
pref("extensions.save2read.sort_by",                "time_asc");
pref("extensions.save2read.sidebar_shortcut",       "ALT + [");
pref("extensions.save2read.bookmark_shortcut",      "ALT + W");
pref("extensions.save2read.bookmark_edit_shortcut", "ALT + Q");
pref("extensions.save2read.panel_shortcut",         "ALT + ]");
pref("extensions.save2read.open_shortcut",          "ALT + O");
pref("extensions.save2read.openTabForLeftClick",    false);
pref("extensions.save2read.closePanelOnClick",      false);
pref("extensions.save2read.useRightButton",         true);
pref("extensions.save2read.readButton",             "oldest");
pref("extensions.save2read.removeOnReadButton",     false);
pref("extensions.save2read.removeOnLeftClick",      false);
pref("extensions.save2read.fullViewWithURL",        false);
pref("extensions.save2read.showTooltip",            false);
pref("extensions.save2read.autoTags",               "");
pref("extensions.save2read.counterOnSidebarBtn",    true);
pref("extensions.save2read.counterOnPanelBtn",      false);
pref("extensions.save2read.counterOnOpenBtn",       false);
pref("extensions.save2read.closePageOnSave",        false);

// https://developer.mozilla.org/en/Localizing_extension_descriptions
pref("extensions.save2read@konstantin.plotnikov.description", "chrome://save2read/locale/overlay.properties");
