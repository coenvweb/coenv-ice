// fallback to local jQuery if CDN version is not available
var themeVars = themeVars || true;
!window.jQuery && document.write( unescape( '%3Cscript src="' + themeVars.themeurl + '/components/jquery/jquery.min.js"%3E%3C/script%3E' ) );