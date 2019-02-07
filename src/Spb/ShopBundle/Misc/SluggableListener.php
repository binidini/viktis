<?php

namespace Spb\ShopBundle\Misc;

class SluggableListener extends \Gedmo\Sluggable\SluggableListener {

    public function __construct() {
        $this->setTransliterator(array('\Spb\ShopBundle\Misc\Transliterator', 'transliterate'));
    }

    protected function getNamespace() {
        return parent::getNamespace();
    }

}
