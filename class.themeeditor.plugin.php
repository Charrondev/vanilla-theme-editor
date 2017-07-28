<?php if (!defined('APPLICATION')) { exit(); }

/**
 * SassCompiler Plugin
 *
 * This plugin allows an admin with a modern browser to edit theme variables
 * with a color-picker, and dynamically recompile in-browser.
 *
 * Changes:
 *  1.0     Initial release
 *
 * @author Adam Charron <adam@charrondev.com>
 * @copyright 2009-2017 Vanilla Forums, Inc
 * @package Internal
 */
class SassCompilerPlugin extends Gdn_Plugin {

    const PLUGIN_PATH = PATH_PLUGINS . 'sasscompiler';

    public function setup() {
        $this->structure();
    }

    /**
     * Create the settings endpoint for the plugin
     *
     * @param SettingsController $sender The settings controller
     *
     * @return void
     */
    public function settingsController_compiler_create(SettingsController $sender) {
        $sender->permission(['Garden.Community.Manage', 'Garden.Settings.Manage'], false);
        $sender->setHighlightRoute('settings/compiler');
        $sender->setData('Title', t('Sass Compiler'));
        $sender->addJsFile('bundle.js', 'plugins/sasscompiler');

        $sassMap = json_encode(json_encode($this->buildSassMap()));
        echo "<script>(function() {
            var jsonEncodedMap = $sassMap;
            window.sassMap = JSON.parse(jsonEncodedMap);
        })();
        </script>";

        if (!$this->isSupportByCurrentTheme()) {
            $sender->render($sender->fetchViewLocation('unsupported', false, 'plugins/sasscompiler'));
            return;
        }
    }

    public function base_getAppSettingsMenuItems_handler($sender) {
        /* @var SideMenuModule */
        $menu = $sender->EventArguments['SideMenu'];
        $menu->addLink('Appearance', t('Theme Compilation'), 'settings/compiler', 'Garden.Settings.Manage', ['After' => 'dashboard/settings/themes']);
    }

    /**
     * Build up a map
     *
     * @return void
     */
    public function buildSassMap() {
        $sourceDirectory = paths(PATH_THEMES, theme(), 'src/scss');
        $dirIterator = new RecursiveDirectoryIterator($sourceDirectory);
        $files = new RegexIterator(
            new RecursiveIteratorIterator($dirIterator),
            '/^.+\.scss$/',
            RegexIterator::GET_MATCH
        );

        $result = [];

        foreach ($files as $info) {
            $filePath = $info[0];
            $choppedFilePath = str_replace($sourceDirectory . '/', '', $filePath);
            $result[$choppedFilePath] = file_get_contents($filePath);
        }

        return $result;
    }

    private function transformArrayToJsObject($arr, $sequential_keys = false, $quotes = false, $beautiful_json = false) {
        $output = "{";
        $count = 0;
        foreach ($arr as $key => $value) {

            if ( isAssoc($arr) || (!isAssoc($arr) && $sequential_keys == true ) ) {
                $output .= ($quotes ? '"' : '') . $key . ($quotes ? '"' : '') . ' : ';
            }

            if (is_array($value)) {
                $output .= json_encode_advanced($value, $sequential_keys, $quotes, $beautiful_json);
            } else if (is_bool($value)) {
                $output .= ($value ? 'true' : 'false');
            } else if (is_numeric($value)) {
                $output .= $value;
            } else {
                $output .= ($quotes || $beautiful_json ? '"' : '') . $value . ($quotes || $beautiful_json ? '"' : '');
            }

            if (++$count < count($arr)) {
                $output .= ', ';
            }
        }

        $output .= "}";

        return $output;
    }

    public function getThemeOptions() {

    }

    private function getSassEntryPoint($theme) {
        return "custom.scss";
    }

    private function isSupportByCurrentTheme() {
        $path = paths(PATH_THEMES, theme(), 'themeoptions.json');
        return file_exists($path);
    }

    private function parseThemeOptionsIntoArray() {

    }
}
