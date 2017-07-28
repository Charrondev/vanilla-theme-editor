const Sass = require("sass.js/dist/sass.js");

$(() => {
    initializeSass();
});

function initializeSass () {
    // tell Sass.js where it can find the worker,
    // url is relative to document.URL - i.e. outside of whatever
    // Require or Browserify et al do for you
    Sass.setWorkerUrl("/plugins/sasscompiler/js/sass.worker.js");

    const sass = new Sass();

    sass.writeFile(window.sassMap);
    const entry = window.sassMap['custom.scss'];

    sass.compile(entry, result => {
        $('#SassOutput').text(result);
    });
}
