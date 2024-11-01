<?php

class TemplateService
{
    private function getTemplatePath($name) {
        return dirname(dirname(__FILE__)).'/templates/'.$name.'.mustache';
    }

    public function hasTemplate($name) {
        return file_exists($this->getTemplatePath($name));
    }

    public function saveTemplate($name, $content) {
        $trimmed = trim($content);
        if(empty($trimmed)) {
            if ($this->hasTemplate($name)) {
                unlink($this->getTemplatePath($name));
            }
        } else {
            file_put_contents($this->getTemplatePath($name), $content);
        }
    }

    public function rawTemplate($name) {
        global $mustache;
        return $mustache->getLoader()->load($name);
    }

    public function loadTemplate($name) {
        global $mustache, $authService;

        if($this->hasTemplate('custom/'.$name) && $authService->can('wp_monero_miner_templates')) {
            return $mustache->loadTemplate('custom/'.$name);
        }

        return $mustache->loadTemplate($name);
    }

    public function render($name) {
        global $mustache;
        return $mustache->render($name);
    }
}

?>