<?php

class UpgradeController extends InstallerController
{


    public function onLoad()
    {
        parent::onLoad();

        $this->view->set('title', 'WHSuite Upgrader');
    }

    public function upgrade()
    {
        $migrate = $this->migrations->migrate();

        if (is_null($migrate) || $migrate != false) {
            // Migrations went without a hitch!
            
            // Update the installation lock file
            $this->filesystem->dumpFile(STORAGE_DIR . DS . 'whsuite.installed', INSTALL_VERSION);

            return $this->view->display('upgradeComplete.php');
        } else {
            return $this->view->display('upgradeFailed.php');
        }
    }

    
}