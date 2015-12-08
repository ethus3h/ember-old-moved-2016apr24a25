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

s2rGlobal.s2rOptions =
{
    detectKey : function(textbox, e)
    {
        e.stopPropagation();
        e.preventDefault();
        var key = s2rGlobal.s2rOptions.getKeyFromEvent(e);
        var modifiers = (e.metaKey?"META + ":"") + (e.ctrlKey?"CONTROL + ":"") + (e.altKey?"ALT + ":"") + (e.shiftKey?"SHIFT + ":"");
        if(key == null)
        {
            // do nothing
        }
        else
        {
            if (key == " ")
                key = "VK_SPACE";
            textbox.value = modifiers + key;
        }
    },

    getKeyFromEvent : function(e)
    {
        if(!s2rGlobal.s2rOptions.keys)
        {
            s2rGlobal.s2rOptions.keys = {};
            for(var property in e)
            {
                if(property.indexOf("DOM_VK_") == 0)
                    s2rGlobal.s2rOptions.keys[e[property]] = property.replace("DOM_","");
            }
        }

        if(e.charCode != 0)
        {
            return String.fromCharCode(e.charCode).toUpperCase();
        }

        return s2rGlobal.s2rOptions.keys[e.keyCode];
    },

    onApplyPreferences : function()
    {
        // Workaround. FF does not save this for some reason
        s2rGlobal.s2rServices.preferences().setCharPref(s2rGlobal.s2rPref.preferenceBookmarkShortcut,       document.getElementById("s2r_bookmark_shortcut").value);
        s2rGlobal.s2rServices.preferences().setCharPref(s2rGlobal.s2rPref.preferenceBookmarkEditShortcut,   document.getElementById("s2r_bookmark_edit_shortcut").value);
        s2rGlobal.s2rServices.preferences().setCharPref(s2rGlobal.s2rPref.preferenceSidebarShortcut,        document.getElementById("s2r_sidebar_shortcut").value);
        s2rGlobal.s2rServices.preferences().setCharPref(s2rGlobal.s2rPref.preferencePanelShortcut,          document.getElementById("s2r_panel_shortcut").value);
        s2rGlobal.s2rServices.preferences().setCharPref(s2rGlobal.s2rPref.preferenceOpenShortcut,           document.getElementById("s2r_open_shortcut").value);
    }

//     onLoad : function()
//     {
//     },
};

// window.addEventListener("load", s2rGlobal.s2rOptions.onLoad, false);
