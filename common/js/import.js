(function() {
    var jsfiles = ["jquery.js","yuga.js","jquery.easing.1.3.js","sexylightbox.v2.2.jquery.js"];  // ���[�h�����X�N���v�g�i���̃t�@�C������̑��΃p�X�w��j
    
    /****************************** DO NOT EDIT BELOW *****************************/
    function lastof(es)    { return es[es.length - 1]; }
    function dirname(path) { return path.substring(0, path.lastIndexOf('/')); }
    var prefix = dirname(lastof(document.getElementsByTagName('script')).src);
    for(var i = 0; i < jsfiles.length; i++) {
        document.write('<script type="text/javascript" src="' + prefix + '/' + jsfiles[i] + '"></script>');
    }
}).call(this);