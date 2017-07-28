<?php
/**
 * @author Adam Charron <adam@charrondev.com>
 * @copyright 2009-2017 Vanilla Forums Inc.
 * @license Proprietary
 */

class ThemeEditorController extends DashboardController {

    /// Properties ///

    /**
     * @var Gdn_Form;
     */
    protected $form;

    // The theme directory that we are working with
    protected $themeDirectory;

    public function initialize() {
        parent::initialize();

        $this->themeDirectory = paths(PATH_THEMES, theme(), 'themeoptions.json');
        $this->form = new Gdn_Form('', 'bootstrap');
    }
}
