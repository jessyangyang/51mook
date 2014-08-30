define(function(require, exports, module) {
	require('highlight');
    exports.run = function() {
        hljs.configure({tabReplace: ' '});
        hljs.initHighlightingOnLoad();
    };
});