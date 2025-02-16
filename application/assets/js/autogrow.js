/*
	Copyright 2018-2019 Cédric Levieux, Parti Pirate

	This file is part of Congressus.

    Congressus is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Congressus is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Congressus.  If not, see <http://www.gnu.org/licenses/>.
*/

/* global $ */

function autogrowEvent() {
/*    
	$(this).css({"height": "auto"});
	var height = this.scrollHeight;
	$(this).height(height);
*/	
	autogrowElement(this);
}

function autogrowElement(element) {
	$(element).css({"height": "auto"});
	var height = element.scrollHeight;
	$(element).height(height);
}

function addAutogrowListeners() {
	$("body").on("keyup", ".autogrow", autogrowEvent);
//	$("body").on("autogrow", ".autogrow", autogrowEvent);
	$("body .autogrow").keyup();
}

$(function() {
	addAutogrowListeners();
});