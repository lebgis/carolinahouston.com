////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Helps with IE debugging.
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

jQuery.extend({getScript:function(e,t){var a=document.getElementsByTagName("head")[0],n=document.createElement("script"),d=!1;return n.src=e,n.onload=n.onreadystatechange=function(){d||this.readyState&&"loaded"!==this.readyState&&"complete"!==this.readyState||(d=!0,t&&t(),n.onload=n.onreadystatechange=null)},void a.appendChild(n)}});