---------------------------------------------------------------------
 Carica Status Monitor
 
 Licence:  The MIT License
           http://www.opensource.org/licenses/mit-license.php 
 Copright: 2012 Thomas Weinert <thomas@weinert.info>
---------------------------------------------------------------------

This is a panic/status monitor. The main component is an Atom reader
written in Javascript using jQuery.

The project makes use of CSS 3. So don't blame me if it doesn't work
in your favorite browser.

--------------------------------------------------------------------

Browser Compatiblity:
- Firefox 13

--------------------------------------------------------------------

/src/index.html

A status monitor example. The feeds are configured using 
data-attributes inside the html. Several feeds are only test data.

--------------------------------------------------------------------

/src/twitter-wall.html

A twitter wall example. It uses only a single feed (a simple 
twitter search proxy) and displays the feed on all columns.

The search is defined by the url fragment/hash. 