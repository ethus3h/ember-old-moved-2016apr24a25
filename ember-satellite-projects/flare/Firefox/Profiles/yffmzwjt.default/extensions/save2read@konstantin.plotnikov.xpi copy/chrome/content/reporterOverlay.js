/* ***** BEGIN LICENSE BLOCK *****
 *
 * The Original Code is Mozilla Reporter (r.m.o).
 *
 * The Initial Developer of the Original Code is
 *      Robert Accettura <robert@accettura.com>.
 *
 * Portions created by the Initial Developer are Copyright (C) 2004
 * the Initial Developer. All Rights Reserved.
 *
 * Contributor(s):
 *   Gavin Sharp <gavin@gavinsharp.com>
 *   Konstantin Plotnikov <kostyapl@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * ***** END LICENSE BLOCK ***** */

if(!s2rGlobal) var s2rGlobal = {};

s2rGlobal.s2r_reporterListener = {

  QueryInterface: function(aIID) {
    if (aIID.equals(Components.interfaces.nsIWebProgressListener)   ||
        aIID.equals(Components.interfaces.nsIWebProgressListener2)  ||
        aIID.equals(Components.interfaces.nsISupportsWeakReference) ||
        aIID.equals(Components.interfaces.nsISupports))
      return this;
    throw Components.results.NS_NOINTERFACE;
  },

  onLocationChange: function(aProgress, aRequest, aURI) {
    s2rGlobal.s2rOverlay.onLocationChanged(aURI);
  },

  onStateChange: function() {  },
  onProgressChange: function() {  },
  onStatusChange: function() {  },
  onSecurityChange: function() {  },
  onProgressChange64: function() { },
  onRefreshAttempted: function() { return true; }
};

s2rGlobal.onBrowserLoad = function() {
  if ("undefined" != typeof(gBrowser))
    gBrowser.addProgressListener(s2rGlobal.s2r_reporterListener);
};

window.addEventListener("load", s2rGlobal.onBrowserLoad, false);
