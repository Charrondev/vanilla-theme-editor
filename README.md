# vanilla-theme-editor
A proof of concept theme editor for Vanilla Forums. Dynamically recompile Sass in the browser so that your server doesn't have to.

## Compiling sass in the browser?! Why on earth would you do that?
- Sass makes it easy to create variable-driven themes. A developer can and should compile locally, but sometimes a developer may want to expose certain variables to the client(s) in a order to have a much more configurable theme.
- Compiling styles on the server can certaintly be faster and more efficient than using an Emscripten compiled version of libsass in the browsre, but php wrappers around libsass are woefully out of date and are unmaintained. [Sass.js](https://github.com/medialize/sass.js/) is a more stable and well maintained solution than any current PHP bindings.

## To Do

- [x] Set up javascript for building sass in the browser
- [ ] Complete the entrypoint parser
- [x] Create the filesystem -> javascript array mapper
- [ ] Create `themevariables.json` parser and generator
- [ ] Create the form logic
- [ ] Create database models for revisions
= [ ] Implement Caching

## How it works
- The plugin scans your addon for a `src/scss` directory, and recursively builds up a map of every scss file's name and contents. This is json_encoded and injecting into an inline script in the page and attatched to the window object. This item is also cached on the server, unless a configuration variable `isDevelopmentMode` is set to true.
- The plugin scans for a `themevariables.json`. This file can be created manually or generated with the [Vanilla CLI](https://github.com/vanilla/vanilla-cli).
- A form is created for the dashboard based on the json file, when edits are made to the form, the changes are saved to the site's config, and a temporary stylesheet is created with sass variables corresponding to the results of the form. The resulting stylesheet is appended **before** the map created earlier.
- The plugin's main javascript file includes and initializes `sass.js` and its web-worker. It then uses the file/content map on the window object to create a virtual file-system.
- The entry point(s) are pulled from the `entries.scss` section of your `addon.json`. If those cannot it be found it will default to all top level files in the directory that do not begin with `_`.
- Sass.js is passed the entry point and compiles the resulting stylesheets.
- The resulting files are stored as a revision in the database and cached. If any revision is active, the themes default stylesheets will be ignored, and the new compiled versions will be served instead. 

## Revisions
Revisions will be overwritten by newer ones unless they are given an explicit name. A common revision that is served to everybody may be used, as well as a seperate version served only to admins and people with a specific permission.
